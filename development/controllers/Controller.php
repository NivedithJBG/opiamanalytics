<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function getNotificationCount()
	{
		$condition='(b.user_id='.Yii::app()->user->id.' OR a.createdby='.Yii::app()->user->id.') ';
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.* FROM task AS a INNER JOIN task_users AS b ON a.task_id=b.task_id WHERE $condition GROUP BY a.task_id ORDER BY a.task_id DESC ";
		//echo $sql;exit;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$usertasks=$dataReader->readAll();
		$total=0;
		$taskcount=0;
		foreach($usertasks AS $usertask):
			$count=TaskNotifications::model()->count(array('condition'=>'task_id='.$usertask['task_id'].' AND ( (user_id!=:user AND type="Comment" AND status IN (0,1)) OR (user_id!=:user AND type="Status" AND status IN (0,1)) OR (assigned_user=:user AND type="TaskAssign" AND status IN (0,1)) ) ','params'=>array(':user'=>Yii::app()->user->id)));
			$taskcount=$taskcount + $count;
		endforeach;
		$notfcount=Notifications::model()->count(array('condition'=>'user_id=1 AND status IN (0,1)'));
		$userprofile=UserProfile::model()->find(array('condition'=>'user_id='.Yii::app()->user->id.' '));
		if($userprofile['function']!=''):
			$fwdnotfcount=Notifications::model()->count(array('condition'=>'assigned_dep IN ('.$userprofile['function'].') AND status IN (0,1)','order'=>'notf_id DESC'));
		endif;
		if(Yii::app()->user->isAdmin()):
			$total=$taskcount + $notfcount + $fwdnotfcount;
		else:
			$total=$taskcount + $fwdnotfcount;
		endif;
		return $total;
	}

	public function getNotifications()
	{
		$condition='(b.user_id='.Yii::app()->user->id.' OR a.createdby='.Yii::app()->user->id.') ';
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.* FROM task AS a INNER JOIN task_users AS b ON a.task_id=b.task_id WHERE $condition GROUP BY a.task_id ORDER BY a.task_id DESC ";
		//echo $sql;exit;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$usertasks=$dataReader->readAll();
		$usernotf='';
		if(count($usertasks)>0):
			foreach($usertasks AS $usertask):
				$notifications=TaskNotifications::model()->findAll(array('condition'=>'task_id='.$usertask['task_id'].' AND ( (user_id!=:user AND type="Comment" AND status IN (0,1)) OR (user_id!=:user AND type="Status" AND status IN (0,1)) OR (assigned_user=:user AND type="TaskAssign" AND status IN (0,1)) )','params'=>array(':user'=>Yii::app()->user->id),'limit'=>10,'order'=>'status ASC,notf_id DESC'));
				$html='';
				$lastid='';
				if(count($notifications)>0):
					foreach($notifications AS $item):
						$html.='<div class="list-group" style="margin-bottom: 0px">';
						switch($item->type){
							case "TaskAssign":
								$user=User::model()->findByPk($item->user_id);
								$html.='<a href="'.Yii::app()->createUrl('Task/View/'.$item->task_id.'').'" style="color: #555;text-align: left;" class="list-group-item '.($item->status!='2'?'unread':'').' readnotification"  data-id="'.$item->notf_id.'">'.$user->username.' has assigned a new task</a>';
								break;
							case "Comment":
								$user=User::model()->findByPk($item->user_id);
								$task=Task::model()->findByPk($item->task_id);
								$html.='<a href="'.Yii::app()->createUrl('Task/View/'.$item->task_id.'').'" style="color: #555;text-align: left;" class="list-group-item '.($item->status!='2'?'unread':'').' readnotification"  data-id="'.$item->notf_id.'">'.$user->username.' commented on '.$task->name.'</a>';
								break;
							case "Status":
								$user=User::model()->findByPk($item->user_id);
								$task=Task::model()->findByPk($item->task_id);
								$html.='<a href="'.Yii::app()->createUrl('Task/View/'.$item->task_id.'').'" style="color: #555;text-align: left;" class="list-group-item '.($item->status!='2'?'unread':'').' readnotification"  data-id="'.$item->notf_id.'">'.$user->username.' has completed '.$task->name.'</a>';
								break;
						}
						$lastid=$item->notf_id;
						$html.='</div>';
					endforeach;
					$html.='<input type="hidden" value="'.$lastid.'" id="lastitem">';
					/*if($notificationscount>20):
                        $html.='<li class="loadmore"><a href="javascript:void(0)" class="text-center">View more</a></li>';
                    endif;*/
				endif;
				$usernotf.=$html;
			endforeach;
		//else:
			//$usernotf='<div class="list-group"><a href="#">No notifications found.</a></div>';
		endif;
		$notifications=Notifications::model()->findAll(array('condition'=>'user_id=1 AND status IN (0,1)','order'=>'notf_id DESC'));
		$notfhtml='';
		foreach($notifications AS $notification):
			$notfhtml.='<div class="list-group" style="margin-bottom: 0px">';
			switch($notification->type){
				case "Fund Request":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('FinanceRequests/groupapprove/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new fund request for approval</a>';
					break;
				case "Fund Receipt":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('FinanceRequests/receiptapprove/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new fund receipt for approval</a>';
					break;
				case "Bill":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('FinanceRequests/billsapprove/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new bill for approval</a>';
					break;
				case "Document":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('DoccumentManager/editdocument/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new document for approval</a>';
					break;
				case "Upload Document":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('DoccumentManager/EditUploaddoc/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new document for approval</a>';
					break;
				case "Work Order":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('DoccumentManager/workorderapprove/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new Work Order for approval</a>';
					break;
				case "Purchase Order":
					$user=User::model()->findByPk($notification->assigned_user);
					$notfhtml.='<a href="'.Yii::app()->createUrl('DoccumentManager/PurchaseOrderApprove/'.$notification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($notification->status!='2'?'unread':'').' readnotification"  data-id="'.$notification->notf_id.'">'.$user->username.' has created a new Purchase Order for approval</a>';
					break;
			}
			$notfhtml.='</div>';
		endforeach;
		if(Yii::app()->user->isAdmin()):
			$usernotf.=$notfhtml;
		endif;
		$userprofile=UserProfile::model()->find(array('condition'=>'user_id='.Yii::app()->user->id.' '));
		//echo $userprofile['function'];exit;
		$depts=explode(',',$userprofile['function']);
		if($userprofile['function']!=''):
			$docnotifications=Notifications::model()->findAll(array('condition'=>'assigned_dep IN ('.$userprofile['function'].') AND status IN (0,1)','order'=>'notf_id DESC'));
			//echo count($docnotifications);exit;
			$docnotfhtml='';
			foreach($docnotifications AS $docnotification):
				$docnotfhtml.='<div class="list-group" style="margin-bottom: 0px">';
				switch($docnotification->type){
					case "created":
						$user=User::model()->findByPk($docnotification->assigned_user);
						$docnotfhtml.='<a href="'.Yii::app()->createUrl('DoccumentManager/Viewdocument/'.$docnotification->itemid.'').'" style="color: #555;text-align: left;" class="list-group-item '.($docnotification->status!='2'?'unread':'').' readnotification"  data-id="'.$docnotification->notf_id.'">'.$user->username.' has forwaded a document with the remarks "'.$docnotification->remarks.'" </a>';
						break;
					case "uploaded":
						$user=User::model()->findByPk($docnotification->assigned_user);
						$document=Drawings::model()->findByPk($docnotification->itemid);
						$extension=$this->getExtendion($document->filename);
						if($extension=='pdf'):
							$docnotfhtml.='<a href="'. Yii::app()->request->baseUrl .'/'.$document['filename'].'" style="color: #555;text-align: left;" class="list-group-item '.($docnotification->status!='2'?'unread':'').' readnotification"  data-id="'.$docnotification->notf_id.'">'.$user->username.' has forwaded a document with the remarks "'.$docnotification->remarks.'" </a>';
						elseif($extension=='doc'):
							$docnotfhtml.='<a href="https://docs.google.com/gview?url=http://www.geotech.net.in/app/'.$document['filename'].'" style="color: #555;text-align: left;" class="list-group-item '.($docnotification->status!='2'?'unread':'').' readnotification"  data-id="'.$docnotification->notf_id.'">'.$user->username.' has forwaded a document with the remarks "'.$docnotification->remarks.'" </a>';
						else:
							$docnotfhtml.='<a href="'.Yii::app()->request->baseUrl .'/'.$document['filename'].'" style="color: #555;text-align: left;" class="list-group-item '.($docnotification->status!='2'?'unread':'').' readnotification"  data-id="'.$docnotification->notf_id.'">'.$user->username.' has forwaded a document with the remarks "'.$docnotification->remarks.'" </a>';
						endif;
						break;

				}
				$docnotfhtml.='</div>';
			endforeach;
		endif;
		$usernotf.=$docnotfhtml;
		$notificationscount=TaskNotifications::model()->count(array('condition'=>'assigned_user=:user','params'=>array(':user'=>Yii::app()->user->id)));
		return $usernotf;
	}

	public function getCartCount()
	{
		$cartitems=Cart::model()->findAll(array('condition'=>'status=0'));
		return count($cartitems);
	}

	public function getExtendion($filename)
	{
		$lastDotPos = strrpos($filename, '.');

		if ( !$lastDotPos ){
			return false;
		}
		else{
			$exten=substr($filename, $lastDotPos+1);
			$type='';
			switch($exten){
				case 'jpg':
					$type='image';
					break;
				case 'jpeg':
					$type='image';
					break;
				case 'png':
					$type='image';
					break;
				case 'gif':
					$type='image';
					break;
				case 'pdf':
					$type='pdf';
					break;
				default:
					$type='doc';
					break;
			}
			return $type;
		}
	}
}