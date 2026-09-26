<?php

namespace app\components;

/* Single source of truth for "can this user do X" — replaces the 4+
   divergent UserTabs::find() query shapes that used to live scattered
   across main.php, reporting.php, projectsmain/index.php,
   financerequests/index.php, and FinancerequestsController.php. Both
   nav-visibility (menu hiding) and real server-side enforcement call
   into this class so there is exactly one resolution rule, not several.

   Resolution order, most specific wins, first match applies:
     1. user.role_id == 0            -> superuser, always allow
     2. user+project-specific override -> its effect wins outright
     3. user global override (project_id IS NULL) -> wins if no project-specific row
     4. role-level default (role_permissions) -> wins if no user override
     5. nothing found -> deny

   enforced=0 on a permission_action row means "fail open" (always allow)
   regardless of the above — this is what makes rolling enforcement out
   controller-by-controller safe: wiring a controller onto it changes
   nothing observable until its rows are deliberately flipped to
   enforced=1, and flipping back is an instant, code-deploy-free revert. */
class PermissionChecker
{
    private static $_actionCache = [];      // "$controllerId/$actionIdStr/$microKey" => action row or false
    private static $_roleDefaultCache = [];  // role_id => [action_id => effect]
    private static $_overrideCache = [];     // user_id => [ "$action_id:$project_id" => effect ]
    private static $_superuserCache = [];    // user_id => bool

    private static function isSuperuser($userId)
    {
        if (isset(self::$_superuserCache[$userId])) {
            return self::$_superuserCache[$userId];
        }
        $roleId = \Yii::$app->db->createCommand(
            "SELECT role_id FROM user WHERE id = :id", [':id' => $userId]
        )->queryScalar();
        return self::$_superuserCache[$userId] = ((int)$roleId === 0);
    }

    private static function lookupAction($controllerId, $actionIdStr, $microKey = '')
    {
        $key = $controllerId . '/' . $actionIdStr . '/' . $microKey;
        if (array_key_exists($key, self::$_actionCache)) {
            return self::$_actionCache[$key];
        }
        $row = \Yii::$app->db->createCommand(
            "SELECT * FROM permission_action WHERE controller_id=:c AND action_id_str=:a AND micro_key=:m AND Delete_status=0",
            [':c' => $controllerId, ':a' => $actionIdStr, ':m' => $microKey]
        )->queryOne();
        return self::$_actionCache[$key] = ($row ?: false);
    }

    private static function roleDefaults($roleId)
    {
        if (isset(self::$_roleDefaultCache[$roleId])) {
            return self::$_roleDefaultCache[$roleId];
        }
        $rows = \Yii::$app->db->createCommand(
            "SELECT action_id, effect FROM role_permissions WHERE role_id = :rid", [':rid' => $roleId]
        )->queryAll();
        $map = [];
        foreach ($rows as $r) $map[(int)$r['action_id']] = $r['effect'];
        return self::$_roleDefaultCache[$roleId] = $map;
    }

    private static function userOverrides($userId)
    {
        if (isset(self::$_overrideCache[$userId])) {
            return self::$_overrideCache[$userId];
        }
        $rows = \Yii::$app->db->createCommand(
            "SELECT action_id, project_id, effect FROM user_permission_overrides WHERE user_id = :uid", [':uid' => $userId]
        )->queryAll();
        $map = [];
        foreach ($rows as $r) {
            $pid = $r['project_id'] === null ? '' : (int)$r['project_id'];
            $map[$r['action_id'] . ':' . $pid] = $r['effect'];
        }
        return self::$_overrideCache[$userId] = $map;
    }

    /* Core check by action_id (once the permission_action row is already
       known) — the shared logic canRoute()/can() both build on. */
    private static function resolve($userId, $actionRow, $projectId = null)
    {
        if (self::isSuperuser($userId)) return true;
        if ((int)$actionRow['enforced'] === 0) return true; // fail-open until deliberately promoted

        $actionId = (int)$actionRow['action_id'];
        $overrides = self::userOverrides($userId);

        if ($projectId !== null && array_key_exists($actionId . ':' . (int)$projectId, $overrides)) {
            return $overrides[$actionId . ':' . (int)$projectId] === 'allow';
        }
        if (array_key_exists($actionId . ':', $overrides)) {
            return $overrides[$actionId . ':'] === 'allow';
        }

        $roleId = \Yii::$app->db->createCommand("SELECT role_id FROM user WHERE id=:id", [':id' => $userId])->queryScalar();
        $roleDefaults = self::roleDefaults($roleId);
        if (isset($roleDefaults[$actionId])) {
            return $roleDefaults[$actionId] === 'allow';
        }

        return false; // no row anywhere -> deny
    }

    /* Route-based check for server-side enforcement (BaseController). If no
       permission_action row is registered for this route at all, fails
       open — enforcement only ever applies to routes deliberately added to
       the registry, so wiring a controller in is safe by construction. */
    public static function canRoute($userId, $controllerId, $actionIdStr, $projectId = null, $microKey = '')
    {
        $actionRow = self::lookupAction($controllerId, $actionIdStr, $microKey);
        if (!$actionRow) return true;
        return self::resolve($userId, $actionRow, $projectId);
    }

    /* Direct action_id check, e.g. for view-layer conditionals like the
       Finance Approve/Reject/Hold buttons. */
    public static function canAction($userId, $actionId, $projectId = null)
    {
        $row = \Yii::$app->db->createCommand(
            "SELECT * FROM permission_action WHERE action_id=:id AND Delete_status=0", [':id' => $actionId]
        )->queryOne();
        if (!$row) return true;
        return self::resolve($userId, $row, $projectId);
    }

    /* Bulk-resolves every tab's visibility for a user in one pass, for nav
       rendering — avoids N+1 queries across main.php's gating sites and
       the 47-tab loops in reporting.php/projectsmain/index.php/
       financerequests/index.php. Returns [tab_id => bool]. A tab is
       considered visible if ANY of its registered actions resolves allow. */
    public static function resolveTabsForUser($userId, $projectId = null)
    {
        if (self::isSuperuser($userId)) {
            $tabIds = \Yii::$app->db->createCommand("SELECT tab_id FROM department_tab")->queryColumn();
            return array_fill_keys($tabIds, true);
        }

        $actions = \Yii::$app->db->createCommand(
            "SELECT action_id, tab_id, enforced FROM permission_action WHERE Delete_status=0"
        )->queryAll();

        $result = [];
        foreach ($actions as $a) {
            $tabId = (int)$a['tab_id'];
            if (!empty($result[$tabId])) continue; // already known visible
            $allowed = ((int)$a['enforced'] === 0) || self::resolve($userId, $a, $projectId);
            if ($allowed) $result[$tabId] = true;
        }
        return $result;
    }

    /* Convenience: is at least one action under this tab allowed. */
    public static function canAnyForTab($userId, $tabId, $projectId = null)
    {
        $tabs = self::resolveTabsForUser($userId, $projectId);
        return !empty($tabs[$tabId]);
    }
}
