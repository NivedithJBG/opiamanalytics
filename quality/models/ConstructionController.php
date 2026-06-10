<?php

class ConstructionController extends Controller
{

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index','view'),
						'users'=>array('*'),
				),
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
						'actions'=>array('create','update','Checkname','Resourcesearch','Addresourcetemp','Deleteresourcetemp','Search'),
						'users'=>array('@'),
				),
				array('allow', // allow admin user to perform 'admin' and 'delete' actions
						'actions'=>array('admin','delete'),
						'users'=>array('admin'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.Name,b.COR_Qunatity,b.COR_Price AS resprice FROM resources AS a
                INNER JOIN construction_resources AS b ON  a.Resource_Id=b.COR_Resource_Id
                WHERE b.COR_CO_Id='".$id."' ORDER BY a.Resource_Id";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$this->render('view',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionSearch()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT Construction_Id,CO_Name,CO_Unit,CO_Price,CO_Quantity,CO_Quantity FROM construction WHERE CO_Status='0' ";

		if($_POST['investmentname']!='')
			$sql.="AND CO_Name LIKE '%".$_POST['investmentname']."%'";
		$sql.="ORDER BY Construction_Id ASC, CO_Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$datarows.="<tr>
                                <td >".$data['Construction_Id']."</td>
                                <td><span id='overheadname".$data['Construction_Id']."'>".$data['CO_Name']."</span>
                                <input class='form-control editmajorconsumablesname listinvestments' type='text' id='editinvestments".$data['Construction_Id']."' value='".$data['CO_Name']."' style='display:none'>
                                <span class='error'></span></td>
                                <td><span id='overheadunit".$data['Construction_Id']."'>".$data['CO_Unit']."</span>
                                <input class='form-control editinvestmentunit listinvestment' type='text' id='editinvestmentunit".$data['Construction_Id']."' value='".$data['CO_Unit']."'  style='display:none'>
                                <span class='error'></span></td>
                                <td ><span id='investmentamount".$data['Construction_Id']."'>".$data['CO_Price']."</span>
                                <input class='form-control editinvestmentamount listinvestment' type='hidden' id='editinvestmentamount".$data['Construction_Id']."' value='".$data['CO_Price']."'></td>
                                <td class='small75'>
                                    <a href='".Yii::app()->request->baseUrl."/Construction/update?id=".$data['Construction_Id']."'>
                                    <button type='button' title='Edit investment' class='btn btn-primary editinvestment' value='".$data['Construction_Id']."' id='editinvestmentbutton".$data['Construction_Id']."'> <span >Estimate</span></button></a>
                                </td>
                                <td class='small75'>
                                    <button type='button' class='btn btn-primary deleteconstruction' value='".$data['Construction_Id']."' id='deleteconstruction".$data['Construction_Id']."' title='Delete investment'> <span class='glyphicon glyphicon-trash'></span></button>
                                </td>
                            </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="7" style="text-align: center;">No Constructions Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT Name FROM resources  WHERE status='0' ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$resources=$dataReader->readAll();
		$dataProvider='';
		foreach($resources AS $key=>$res):
			if($key==0):
				$dataProvider="'".$res['Name']."'";
			else:
				$dataProvider.=",'".$res['Name']."'";
			endif;
		endforeach;
		if(isset($_POST['Construction_Name'])):
			$model= new Construction();
			$model->CO_Name=$_POST['Construction_Name'];
			$model->CO_Unit=$_POST['Construction_Unit'];
			if($model->save(false)):
				$this->redirect(array('/Construction/update?id='.$model->Construction_Id));
			endif;
		endif;
		$this->render('create',array(
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionCheckname()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT COUNT(*) AS total FROM investments WHERE Name='".$_POST['name']."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$data=$dataReader->read();
		if($data['total']>0):
			echo 'Yes';
		else:
			echo 'No';
		endif;

	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{

		$model=Construction::model()->findByPk($_GET['id']);

		$connection = CActiveRecord::getDbConnection();
		$sqltemvals="SELECT a.Name AS resource,a.Unit,a.Resource_Id,b.* FROM resources AS a
                INNER JOIN construction_resources AS b ON  a.Resource_Id=b.COR_Resource_Id
                WHERE b.COR_CO_Id='".$_GET['id']."' ORDER BY a.Resource_Id";
		$command=$connection->createCommand($sqltemvals);
		$dataReader=$command->query();
		$resourcesadded=$dataReader->readAll();
		$sql="SELECT Name FROM resources  WHERE status='0' ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$resources=$dataReader->readAll();
		foreach($resources AS $key=>$res):
			if($key==0):
				$dataProvider="'".$res['Name']."'";
			else:
				$dataProvider.=",'".$res['Name']."'";
			endif;
		endforeach;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if($_POST['Construction_Name'])
		{
			$model->CO_Name=$_POST['Construction_Name'];
			$model->CO_Unit=$_POST['Construction_Unit'];
			$model->CO_Price=$_POST['Construction_Rate'];
			if($model->save(false)):
				for($i=0;$i<count($_POST['resourceid']);$i++)
				{
					$amount=($_POST['resourceqty'][$i] * $_POST['resourcerate'][$i]);
					$sql="UPDATE construction_resources SET COR_Qunatity='".$_POST['resourceqty'][$i]."',COR_Price='$amount',COR_Actual_Price='".$_POST['resourcerate'][$i]."' WHERE COR_Resource_Id='".$_POST['resourceid'][$i]."' ";
					$command=$connection->createCommand($sql);
					$dataReader=$command->query();
				}
				$this->redirect(array('view','id'=>$model->Construction_Id));

			endif;
		}

		$this->render('update',array(
				'model'=>$model,
				'resourcesadded'=>$resourcesadded,
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionResourcesearch()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT Resource_Id,Name,Unit,Price,ResourceType_Id,Resource_group_Id FROM resources WHERE status='0' AND Resource_group_Id='5'";

		/*if($_POST['resourcetype']!='none')
			$sql.="AND ResourceType_Id='".$_POST['resourcetype']."'";*/
		if($_POST['resourcename']!='')
			$sql.="AND Name LIKE '%".$_POST['resourcename']."%'";
		$sql.=" ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';

		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$datarows.="<tr id='resourcerow".$data['Resource_Id']."' class='resourcetype".$data['ResourceType_Id']."'>
                            <td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td>".$data['Unit']."</td>
                            <td>".$data['Price']." <input type='hidden' id='resourceprice".$data['Resource_Id']."' value='".$data['Price']."'></td>
                            <td><input type='text' id='specificrate".$data['Resource_Id']."' value='".$data['Price']."' class='form-control' placeholder='Specific rate'></td>
                            <td><input type='text' id='resourcequantity".$data['Resource_Id']."' class='form-control' placeholder='Quantity'><span class='error'></span></td>
                            <td class='small75'><button type='button' class='btn btn-primary addresource' value='".$data['Resource_Id']."' id='addresourcebutton".$data['Resource_Id']."' title='Add Item'> <span class='glyphicon glyphicon-upload'></span></button></td>
                        </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="5" style="text-align: center;">No Resources Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}
	public function actionAddresourcetemp()
	{
		if(isset($_POST['quantity'])):


			$connection = CActiveRecord::getDbConnection();
			$rate=$_POST['quantity']*$_POST['srate'];

			$sql="INSERT INTO construction_resources (COR_CO_Id,COR_Resource_Id,COR_Qunatity,COR_Price,COR_Actual_Price) VALUES('".$_POST['Consumables_Id']."','".$_POST['resid']."','".$_POST['quantity']."','".($rate)."','".$_POST['srate']."')";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();


			$sql1="UPDATE construction SET CO_Price=CO_Price+".$rate."  WHERE Construction_Id='".$_POST['Consumables_Id']."'";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();

			$sqltemvals="SELECT a.*,b.* FROM resources AS a
                    INNER JOIN construction_resources AS b ON  a.Resource_Id=b.COR_Resource_Id
                    WHERE b.COR_CO_Id='".$_POST['Consumables_Id']."' ORDER BY a.Resource_Id";
			$command=$connection->createCommand($sqltemvals);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();

			$datarows='';
			$price=0;
			foreach($dataProvider AS $key=>$data):
				$price=$price+$data['COR_Price'];
				$datarows.="<tr id='tempresrow".$data['COR_Id']."'>
				<td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td style='width: 20%'>".$data['Unit']."</td>
                            <td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['COR_Id']."'>
                            <input type='text' data-id='".$data['COR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['COR_Id']."' value='".$data['COR_Qunatity']."' >
                            <span class='error'></span></td>
                            <td style='width: 20%'><input type='text' data-id='".$data['COR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['COR_Id']."' value='".$data['COR_Actual_Price']."' ></td>
                            <td class='resource-amount' id='amount".$data['COR_Id']."'>".$data['COR_Price']."</td>
                            <td><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['COR_Id']."' value='".$data['COR_Id']."'>Remove</button></td>
                            </tr>";
			endforeach;
			$Investments=Construction::model()->findByPk($_POST['Consumables_Id']);
			if($Investments->save(false)):
				$arr = array('result' => $datarows,'price'=>$price,'error'=>'No');
				echo json_encode($arr);
			endif;
		endif;
	}
	public function actionDeleteresourcetemp()
	{
		if(isset($_POST['resid'])):
			$connection = CActiveRecord::getDbConnection();
			$sql="SELECT COR_CO_Id,COR_Price AS Price FROM construction_resources WHERE COR_Id='".$_POST['resid']."'";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$overheads=$dataReader->read();
			$sql="DELETE FROM construction_resources WHERE COR_Id='".$_POST['resid']."'";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$sql1="UPDATE construction SET CO_Price=CO_Price-".$overheads['Price']." WHERE Construction_Id='".$overheads['COR_CO_Id']."'";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();

			$sqltemvals="SELECT a.*,b.* FROM resources AS a
                    INNER JOIN construction_resources AS b ON  a.Resource_Id=b.COR_Resource_Id
                    WHERE b.COR_CO_Id='".$overheads['COR_CO_Id']."' ORDER BY a.Resource_Id";
			$command=$connection->createCommand($sqltemvals);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();
			//print_r($dataProvider);

			$datarows='';
			$price=0;
			foreach($dataProvider AS $key=>$data):
				$price=$price+$data['COR_Price'];
				$datarows.="<tr id='tempresrow".$data['COR_Id']."'>
				<td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td style='width: 20%'>".$data['Unit']."</td>
                            <td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['COR_Id']."'>
                            <input type='text' data-id='".$data['COR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['COR_Id']."' value='".$data['COR_Qunatity']."' >
                            <span class='error'></span></td>
                            <td style='width: 20%'><input type='text' data-id='".$data['COR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['COR_Id']."' value='".$data['COR_Actual_Price']."' ></td>
                            <td class='resource-amount' id='amount".$data['COR_Id']."'>".$data['COR_Price']."</td>
                            <td><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['COR_Id']."' value='".$data['COR_Id']."'>Remove</button></td>
                            </tr>";
			endforeach;
			$Overhead=Construction::model()->findByPk($overheads['COR_CO_Id']);
			/*echo $datarows;
			exit;*/

			//$amount=$price/($product->quantity);
			//$product->amount=$amount;
			if($Overhead->save(false)):
				$arr = array('result' => $datarows,'price'=>$price,'error'=>'No');
				echo json_encode($arr);
			endif;
		endif;
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		$connection = CActiveRecord::getDbConnection();
		$sql="DELETE FROM construction_resources WHERE Construction_Id='".$id."'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$arr = array('error'=>'No');
		echo json_encode($arr);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('MajorConsumables');
		$this->render('index',array(
				'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MajorConsumables('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MajorConsumables']))
			$model->attributes=$_GET['MajorConsumables'];

		$this->render('admin',array(
				'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Overheads the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Construction::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Overheads $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='overheads-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}