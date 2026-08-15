<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use amnah\yii2\user\models\User;
use app\components\AppIconRegistry;

class UserpermissionsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        // This whole module is admin/superuser-only — enforced here, not
        // just hidden via the icon's role_id==0 gate in main.php, since
        // that gate only controls whether the icon is SHOWN, not whether
        // the underlying actions can be called directly by URL.
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if (!$user || (int)$user->role_id !== 0) {
            throw new \yii\web\ForbiddenHttpException('User Management is restricted to administrators.');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex($layout = 'true')
    {
        if ($layout === 'false') {
            $this->layout = false;
        }
        return $this->render('index');
    }

    // ─── Dropdown data: users, projects, icons ──────────────────────────────

    public function actionInitdata()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $db = Yii::$app->db;

        $users = $db->createCommand("
            SELECT u.id, u.username, u.role_id, COALESCE(ur.role_name, 'Admin') AS role_name
            FROM user u
            LEFT JOIN user_role ur ON ur.role_id = u.role_id AND u.role_id != 0
            WHERE u.status = 1
            ORDER BY u.username ASC
        ")->queryAll();

        $roles = $db->createCommand("SELECT role_id, role_name FROM user_role WHERE status=1 ORDER BY role_name ASC")->queryAll();

        $icons = AppIconRegistry::all();

        return ['error' => 'No', 'users' => $users, 'roles' => $roles, 'icons' => $icons];
    }

    // ─── This user's projects + which icons are granted for a given project ─

    public function actionUsercontext()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('user_id');
        if (!$userId) return ['error' => 'Yes', 'errortext' => 'No user specified.'];

        $db = Yii::$app->db;

        $projects = $db->createCommand("
            SELECT pr.Project_Id, pr.Name
            FROM user_projects up
            JOIN projects pr ON pr.Project_Id = up.projectid
            WHERE up.userid = :uid
            ORDER BY pr.Name ASC
        ", [':uid' => $userId])->queryAll();

        return ['error' => 'No', 'projects' => $projects];
    }

    public function actionIconaccess()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId    = (int)Yii::$app->request->post('user_id');
        $projectId = Yii::$app->request->post('project_id'); // '' = All Projects
        if (!$userId) return ['error' => 'Yes', 'errortext' => 'No user specified.'];

        $projectId = ($projectId === '' || $projectId === null) ? null : (int)$projectId;

        $params = [':uid' => $userId];
        $sql = "SELECT icon_key FROM user_icon_access WHERE user_id = :uid AND ";
        if ($projectId === null) {
            $sql .= "project_id IS NULL";
        } else {
            $sql .= "project_id = :pid";
            $params[':pid'] = $projectId;
        }
        $granted = Yii::$app->db->createCommand($sql, $params)->queryColumn();

        return ['error' => 'No', 'granted_icon_keys' => $granted];
    }

    // ─── Save the checked icons for one user + project (or All Projects) ───

    public function actionSaveiconaccess()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId    = (int)Yii::$app->request->post('user_id');
        $projectId = Yii::$app->request->post('project_id');
        $iconKeys  = Yii::$app->request->post('icon_keys', []);
        $grantedBy = Yii::$app->user->id;

        if (!$userId) return ['error' => 'Yes', 'errortext' => 'No user specified.'];
        $projectId = ($projectId === '' || $projectId === null) ? null : (int)$projectId;

        // Only persist keys AppIconRegistry actually recognizes right now —
        // guards against a stale/tampered key from an old page load that no
        // longer maps to a real icon.
        $validKeys = array_column(AppIconRegistry::all(), 'icon_key');
        $iconKeys = is_array($iconKeys) ? array_values(array_intersect($iconKeys, $validKeys)) : [];

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $params = [':uid' => $userId];
            $sql = "DELETE FROM user_icon_access WHERE user_id = :uid AND ";
            if ($projectId === null) {
                $sql .= "project_id IS NULL";
            } else {
                $sql .= "project_id = :pid";
                $params[':pid'] = $projectId;
            }
            $db->createCommand($sql, $params)->execute();

            foreach ($iconKeys as $iconKey) {
                $db->createCommand()->insert('user_icon_access', [
                    'user_id'    => $userId,
                    'icon_key'   => $iconKey,
                    'project_id' => $projectId,
                    'granted_by' => $grantedBy,
                ])->execute();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['error' => 'Yes', 'errortext' => $e->getMessage()];
        }

        return ['error' => 'No'];
    }

    // ─── Add User (username, password, designation/role) ───────────────────

    public function actionAdduser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $username = trim(Yii::$app->request->post('username', ''));
        $password = Yii::$app->request->post('password', '');
        $roleId   = (int)Yii::$app->request->post('role_id');
        $email    = trim(Yii::$app->request->post('email', ''));

        if ($username === '' || $password === '' || !$roleId) {
            return ['error' => 'Yes', 'errortext' => 'Username, password and designation are required.'];
        }

        $exists = Yii::$app->db->createCommand("SELECT id FROM user WHERE username=:u", [':u' => $username])->queryScalar();
        if ($exists) {
            return ['error' => 'Yes', 'errortext' => 'That username is already taken.'];
        }

        $user = new User();
        $user->username     = $username;
        $user->email        = $email ?: null;
        $user->password     = password_hash($password, PASSWORD_BCRYPT);
        $user->activkey     = '';
        $user->account_type = 'normal';
        $user->createtime   = time();
        $user->lastvisit    = time();
        $user->superuser    = 0;
        $user->status       = 1;
        $user->role_id      = $roleId;

        if (!$user->save(false)) {
            return ['error' => 'Yes', 'errortext' => 'Failed to create user.'];
        }

        return ['error' => 'No', 'user_id' => $user->id];
    }
}
