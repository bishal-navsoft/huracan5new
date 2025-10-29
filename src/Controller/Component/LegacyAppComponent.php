<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Http\Exception\NotFoundException;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class LegacyAppComponent extends Component
{
	use LocatorAwareTrait;
	public array $link_design = ['bi' => 'Button, Icon', 'bit' => 'Button, Icon, Text', 'bt' => 'Button, Text', 't' => 'Text'];
	public array $speeches = ['Mr' => 'Mr', 'Ms' => 'Ms'];
	public array $mobile_site_status = ['on' => 'online', 'off' => 'offline'];
	public array $allow_scrolling = ['on' => 'online', 'off' => 'offline'];
	public array $creditCardNames = ['visa' => 'Visa', 'mastercard' => 'Mastercard'];
	public array $months = [
	'1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June',
	'7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
	];

	/**
	* Convert a tilde-separated list into associative array where key=value
	*/
	public function assignArrayKeyValue(?string $str): array
	{
		if (empty($str)) {
			return [];
		}
		$returnArray = [];
		foreach (explode('~', $str) as $value) {
			$returnArray[$value] = $value;
		}
		return $returnArray;
	}

	/**
	* Convert pairs of key^value separated by ~ into associative array
	*/
	public function assignKeyValue(?string $str): array
	{
		if (empty($str)) {
			return [];
		}
		$returnArray = [];
		foreach (explode('~', $str) as $pair) {
			[$key, $val] = array_pad(explode('^', $pair), 2, null);
			$returnArray[$key] = $val;
		}
		return $returnArray;
	}
	
	/**
	* Get module settings (legacy behavior)
	* Sets variables on controller for view consumption
	*/
	public function getModuleSettings(): void
	{
		$ModuleSettings = $this->fetchTable('ModuleSettings'); // assumes table name plural
		$module = $ModuleSettings->find()->where(['id' => 1])->first();
		$controller = $this->getController();
		if (!$module) {
			// set empty defaults
			$controller->set('font_size', []);
			$controller->set('shadow_sizes', []);
			$controller->set('link_option', []);
			$controller->set('link_design', []);
			return;
		}
		$data = $module->toArray();
		$font_size = $this->assignArrayKeyValue($data['text_size'] ?? null);
		$controller->set('font_size', $font_size);
		$shadow_sizes = $this->assignArrayKeyValue($data['shadow_size'] ?? null);
		$controller->set('shadow_sizes', $shadow_sizes);
		$link_option = $this->assignArrayKeyValue($data['link_option'] ?? null);
		$controller->set('link_option', $link_option);
		$link_design = $this->assignKeyValue($data['link_design'] ?? null);
		$controller->set('link_design', $link_design);
	}

	/**
	* Legacy beforeFilter placeholder (keeps name for compatibility)
	*/
	public function beforeFilter(): void
	{
		//define('WEB_SITE_URL','http://'.$_SERVER['HTTP_HOST'].$this->webroot);
		//Configure::write('Config.language', $this->Session->read('Config.language'));
	}

	/* ------------------------- Session & Redirect helpers -------------------------- */

	protected function session()
	{
		return $this->getController()->getRequest()->getSession();
	}

	protected function controller()
	{
		return $this->getController();
	}

	// This function checks USER's type. Created by Naveen Gupta.
	/* ------------------------- User/Partner checks -------------------------- */

	public function checkUserType(): void
	{
		if ($this->session()->read('user_type') === 'P') {
			$this->controller()->redirect(['controller' => 'Partners', 'action' => 'home']);
		}
	}

	public function checkPartnerType(): void
	{
		if ($this->session()->read('user_type') === 'U') {
			$this->controller()->redirect(['controller' => 'Users', 'action' => 'home']);
		}
	}

	public function checkUserLogin(): bool
	{
		return (bool)$this->session()->read('user_id');
	}

	
	// This function redirects a user for log-in (if not logged in already) after storing reference URL. Created by Naveen Gupta.
	public function checkUserSession(): void
	{
		if (!$this->session()->check('user_id')) {
			$this->session()->setFlash('You need to be logged in to access this area.');
			$request = $this->controller()->getRequest();
			$url = $request->getQuery('url');
			if ($url) {
				$this->session()->write('referData', $url);
			}
			$this->controller()->redirect(['controller' => 'Users', 'action' => 'index']);
			// avoid further execution in legacy flow
			throw new NotFoundException();
		}
	}

	// This function checks whether administrator has logged in or not. Created by Naveen Gupta.
	public function checkAdminLogin(): bool
	{
		return (bool)$this->session()->read('admin_id');
	}
	
	// This function redirects administrator for log-in (if not logged in already) after storing reference URL. Created by Naveen Gupta.
	public function checkAdminSession(): void
	{
		if (!$this->session()->check('admin_id')) {
			$this->session()->setFlash('You need to be logged in to access this area.');
			$request = $this->controller()->getRequest();
			$url = $request->getQuery('url');
			if ($url) {
				$this->session()->write('referAdminData', $url);
			}
			$this->controller()->redirect(['controller' => 'AdminMasters', 'action' => 'index']);
			throw new NotFoundException();
		}
	}
	
	public function getRoleMenuPermission(): void
	{
		$RolePermissions = $this->fetchTable('RolePermissions');
		$AdminMenus = $this->fetchTable('AdminMenus');

		$roleId = $this->session()->read('adminData.AdminMaster.role_master_id');
		if (!$roleId) {
			return;
		}

		$rpmData = $RolePermissions->find()->where(['role_master_id' => $roleId, 'view' => '1'])->all()->toArray();

		$admin_menus_children = [];
		$admin_menus_parentId = [];
		foreach ($rpmData as $rpm) {
			$adminMenu = $AdminMenus->find()->where(['id' => $rpm->admin_menu_id])->first();
			if ($adminMenu) {
				$admin_menus_children[$adminMenu->parent_id][] = $adminMenu->toArray();
				$admin_menus_parentId[] = $adminMenu->parent_id;
			}
		}

		$admin_menus_parentId = array_values(array_unique($admin_menus_parentId));
		$admin_menus_parrentdata = [];
		foreach ($admin_menus_parentId as $pid) {
			$parent = $AdminMenus->find()->where(['id' => $pid])->first();
			if ($parent) {
				$admin_menus_parrentdata[] = $parent->toArray();
			}
		}

		$this->controller()->set('admin_menus_children', $admin_menus_children);
		$this->controller()->set('admin_menus_parrentdata', $admin_menus_parrentdata);
	}
	
	/*********REPORT LISN********************************/

	public function reportHsseLink(): void
	{
		$Reports = $this->fetchTable('Reports');
		$allHsseReport = $Reports->find()->where(['isdeleted' => 'N'])->orderDesc('id')->all()->toArray();
		$this->session()->write('allHsseReport', $allHsseReport);
	}					

	public function reportSqLink(): void
	{
		$SqReportMain = $this->fetchTable('SqReportMains');
		$allSqReport = $SqReportMain->find()->where(['isdeleted' => 'N'])->orderDesc('id')->all()->toArray();
		$this->session()->write('allSqReport', $allSqReport);
	}
	
	/**
	* Legacy hsse_client_tab rewritten for CakePHP 5
	*/
	public function hsseClientTab(): void
	{
		$request = $this->controller()->getRequest();
		$passParams = $request->getParam('pass') ?? [];

		if (empty($passParams[0])) {
			return;
		}

		$reportId = base64_decode($passParams[0]);
		$Reports = $this->fetchTable('Reports');

		$client = $Reports->find()->select(['client'])->where(['id' => $reportId])->first();

		if ($client) {
			$this->session()->write('clienttab', $client->client);
		}
	}

	/*function remedial_email($to,$fullname,$remedialno,$rportno,$remidial_summery,$remidial_closure_date)
	{
		$subject='';
		$from = 'Huracan Team';
		$subject='Your remedial action detail';
	    $message='<html><head><title></title></head>
	    				<body>
	    					<table  style="width:550px; border:1px solid #ddd; padding:18px 0 0; margin:20px auto 0; overflow:hidden; background:#fff;" >
								<tr><td style=" min-height:60px; border-bottom:1px solid #ddd; padding:0px 15px;">&nbsp;<a href="javascript:void(0);" style="float:left"><img src="'.$this->webroot.'app/webroot/images/huracan_logo.png" alt="" /></a>
								   <p style="float:right; color:#666; font-size:12px; font-family:Arial; text-align:right;">Visit us on <br /> http://www.huracan.com.au</p><br /><br /></td></tr>
									<tr><td style="padding:18px 15px 0;">&nbsp;<p style="font-size:12px !important;margin-bottom:30px;">
									<strong>Hello ,'.ucwords(strtolower($fullname)).'</strong></p><p style="font-size:12px !important;font-family:Arial">
									Your Remedial Action No:'.$remedialno.'</p><p style="font-size:12px !important;font-family:Arial">
									Your Report No:'.$rportno.'</p><p style="font-size:12px !important;font-family:Arial">Summary:'.$remidial_summery.'</p>
									<p style="font-size:12px !important;font-family:Arial">Closure Date:'.$remidial_closure_date.'</p>
									</td></tr><tr><td style="padding:18px 15px; background:#ddd; overflow:hidden;">&nbsp;<p style="float:left;color:#333; font-size:12px; font-family:Arial; text-align:left;">Thank You </p>
									<p style="float:right; color:#333; font-size:12px; font-family:Arial; text-align:left;margin-right:55px;"><span>Huracan Pty Ltd</span>
									<br/><br/><span>PO Box 1070</span><br/><span>ROMA</span><br/><span>QUEENSLAND 4455</span>
									<br/><span>Email: info@huracan.com.au</span></p></td></tr>
									<tr><td>&nbsp;</td></tr></table></body></html>';
									$this->PhpMailerEmail->send_mail($to,$subject,$message,$from,'','','');
									$responsemsg='ok';
               						return  $responsemsg; 
	}*/

	/**
	* Send remedial email (keeps legacy mail helper usage)
	* Note: this depends on a component/service named PhpMailerEmail being available in controller
	*/
	public function remedialEmail(string $to, string $fullname, $remedialno, $rportno, $remidial_summery, $remidial_closure_date)
	{
		$subject = 'Your remedial action detail';
		$from = 'Huracan Team';

		$webroot = $this->controller()->getRequest()->getAttribute('webroot') ?? '/';

		$message = '<html><head><title></title></head>' .
					'<body><table style="width:550px; border:1px solid #ddd; padding:18px 0 0; margin:20px auto 0; overflow:hidden; background:#fff;">' .
					'<tr><td style=" min-height:60px; border-bottom:1px solid #ddd; padding:0px 15px;">&nbsp;' .
					'<a href="javascript:void(0);" style="float:left"><img src="' . $webroot . 'app/webroot/images/huracan_logo.png" alt="" /></a>' .
					'<p style="float:right; color:#666; font-size:12px; font-family:Arial; text-align:right;">Visit us on <br /> http://www.huracan.com.au</p><br /><br /></td></tr>' .
					'<tr><td style="padding:18px 15px 0;">&nbsp;<p style="font-size:12px !important;margin-bottom:30px;">' .
					'<strong>Hello ,' . ucwords(strtolower($fullname)) . '</strong></p><p style="font-size:12px !important;font-family:Arial">' .
					'Your Remedial Action No:' . $remedialno . '</p><p style="font-size:12px !important;font-family:Arial">' .
					'Your Report No:' . $rportno . '</p><p style="font-size:12px !important;font-family:Arial">Summary:' . $remidial_summery . '</p>' .
					'<p style="font-size:12px !important;font-family:Arial">Closure Date:' . $remidial_closure_date . '</p>' .
					'</td></tr><tr><td style="padding:18px 15px; background:#ddd; overflow:hidden;">&nbsp;' .
					'<p style="float:left;color:#333; font-size:12px; font-family:Arial; text-align:left;">Thank You </p>' .
					'<p style="float:right; color:#333; font-size:12px; font-family:Arial; text-align:left;margin-right:55px;"><span>Huracan Pty Ltd</span>' .
					'<br/><br/><span>PO Box 1070</span><br/><span>ROMA</span><br/><span>QUEENSLAND 4455</span>' .
					'<br/><span>Email: info@huracan.com.au</span></p></td></tr>' .
					'<tr><td>&nbsp;</td></tr></table></body></html>';

		// Attempt to use controller's PhpMailerEmail component/service if available
		if (isset($this->controller()->PhpMailerEmail)) {
			$this->controller()->PhpMailerEmail->send_mail($to, $subject, $message, $from, '', '', '');
			return 'ok';
		}

		// Fallback: try PHP mail (not recommended) — leave as fallback
		// mail($to, $subject, $message, "From: $from\r\nContent-type: text/html; charset=iso-8859-1\r\n");
		return 'no-mail-service';
	}
	
	/*function grid_access()
	{
        switch($this->params['controller']){
			case'Reports':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'Sqreports':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'Jrns':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'Audits':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'Jobs':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'Lessons':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'Reports/report_hsse_list','parent_id !='=>0));	
			break;
		        case'RoleMasters':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'RoleMasters/list_roles','parent_id !='=>0));	
			break;
		        case'Certifications':
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' =>'RoleMasters/list_roles','parent_id !='=>0));	
			break;
		        default:
			$condn = array(
			'conditions' => array('RolePermission.role_master_id' => $this->Session->read('adminData.AdminMaster.role_master_id'), 'AdminMenu.url like' => $this->params['controller'].'/'.$this->params['action'],'parent_id !='=>0));
			break;
		}
		
		$this->RolePermission->bindModel(array('belongsTo' => array('AdminMenu')));
		$roleMenuData = $this->RolePermission->find('first', $condn);
		if($roleMenuData['RolePermission']['add'] == '1')
		{
			$this->set('is_add', 1);
		}
		else
		{
			$this->set('is_add', 0);
		}
		if($roleMenuData['RolePermission']['view'] == '1')
		{
			$this->set('is_view', 1);
		}
		else
		{
			$this->set('is_view', 0);
		}
		if($roleMenuData['RolePermission']['edit'] == '1')
		{
			$this->set('is_edit', 1);
		}
		else
		{
			$this->set('is_edit', 0);
		}
		if($roleMenuData['RolePermission']['block'] == '1')
		{
			$this->set('is_block', 1);
		}
		else
		{
			$this->set('is_block', 0);
		}
		if($roleMenuData['RolePermission']['delete'] == '1')
		{
			$this->set('is_delete', 1);
		}
		else
		{
			$this->set('is_delete', 0);
		}
	}*/
	/**
	* Legacy grid_access - simplified: determine permissions and set flags on controller
	*/
	public function gridAccess(): void
	{
		$controllerName = $this->controller()->getRequest()->getParam('controller');
		$actionName = $this->controller()->getRequest()->getParam('action');

		// Build default condition
		$urlLike = $controllerName . '/' . $actionName;
		$roleId = $this->session()->read('adminData.AdminMaster.role_master_id');
		$RolePermissions = $this->fetchTable('RolePermissions');
		$AdminMenus = $this->fetchTable('AdminMenus');

		// Map special controller cases (keeps legacy mapping)
		$specialMap = [
		'Reports' => 'Reports/report_hsse_list',
		'Sqreports' => 'Reports/report_hsse_list',
		'Jrns' => 'Reports/report_hsse_list',
		'Audits' => 'Reports/report_hsse_list',
		'Jobs' => 'Reports/report_hsse_list',
		'Lessons' => 'Reports/report_hsse_list',
		'RoleMasters' => 'RoleMasters/list_roles',
		'Certifications' => 'RoleMasters/list_roles',
		];

		$urlLike = $specialMap[$controllerName] ?? $urlLike;

		$roleMenuData = $RolePermissions->find()
		->contain(['AdminMenus'])
		->where(['RolePermissions.role_master_id' => $roleId, 'AdminMenus.url LIKE' => $urlLike, 'AdminMenus.parent_id !=' => 0])
		->first();

		$flags = [
		'is_add' => 0, 'is_view' => 0, 'is_edit' => 0, 'is_block' => 0, 'is_delete' => 0
		];

		if ($roleMenuData) {
			$rp = $roleMenuData;
			$flags['is_add'] = ($rp->add === '1') ? 1 : 0;
			$flags['is_view'] = ($rp->view === '1') ? 1 : 0;
			$flags['is_edit'] = ($rp->edit === '1') ? 1 : 0;
			$flags['is_block'] = ($rp->block === '1') ? 1 : 0;
			$flags['is_delete'] = ($rp->delete === '1') ? 1 : 0;
		}

		foreach ($flags as $k => $v) {
			$this->controller()->set($k, $v);
		}
	}
	
	/*function retrive_link_summary($madleVal,$uniqueType,$aTP)
	{
		for($u=0;$u<count($uniqueType);$u++){
		    if($uniqueType[$u]=='lesson'){
			
			 $condition= $aTP.".type ='lesson'";
		         $reportType=$madleVal->find('all', array('conditions' => $condition ));
			  
			   for($t=0;$t<count($reportType);$t++){
			       $reportdetail = $this->LessonMain->find('all', array('conditions' => array('LessonMain.id' =>$reportType[$t][$aTP]['link_report_id'])));
				if(count($reportdetail)>0){
				
				   $linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'Lessons/add_lsreport_view/'.base64_encode($reportdetail[0]['LessonMain']['id']).' target="_blank" >'.$reportdetail[0]['LessonMain']['report_no'].'</a>';
				   $linkHolder[$u]['summary'][$t][]=$reportdetail[0]['LessonMain']['summary'];
				}
			    
			   }
			 
			   
			}	
		      if($uniqueType[$u]=='job'){
			
			  $condition= $aTP.".type ='job'";
			  $reportType=$madleVal->find('all', array('conditions' => $condition));
			  
			   for($t=0;$t<count($reportType);$t++){
			    $reportdetail = $this->JobReportMain->find('all', array('conditions' => array('JobReportMain.id' =>$reportType[$t][$aTP]['link_report_id'])));
				if(count($reportdetail)>0){
				
				   $linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'Jobs/add_jobreport_view/'.base64_encode($reportdetail[0]['JobReportMain']['id']).' target="_blank" >'.$reportdetail[0]['JobReportMain']['report_no'].'</a>';
				   $linkHolder[$u]['summary'][$t][]=$reportdetail[0]['JobReportMain']['comment'];
				}
			    
			   }
			 
			   
			}
			 if($uniqueType[$u]=='audit'){
			
		          $condition= $aTP.".type ='audit'";
			  $reportType=$madleVal->find('all', array('conditions' => $condition));
			  
			   for($t=0;$t<count($reportType);$t++){
			  
				$reportdetail = $this->AuditReportMain->find('all', array('conditions' => array('AuditReportMain.id' =>$reportType[$t][$aTP]['link_report_id'])));
					if(count($reportdetail)>0){
						$linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'Audits/add_audit_view/'.base64_encode($reportdetail[0]['AuditReportMain']['id']).' target="_blank" >'.$reportdetail[0]['AuditReportMain']['report_no'].'</a>';
						$linkHolder[$u]['summary'][$t][]=$reportdetail[0]['AuditReportMain']['summary'];
					}
			   }
			 
			   
			} 
	
			 if($uniqueType[$u]=='sq'){
			
				$condition= $aTP.".type ='sq'";
			         $reportType=$madleVal->find('all', array('conditions' => $condition));
			       
				for($t=0;$t<count($reportType);$t++){
			       
					$reportdetail = $this->SqReportMain->find('all', array('conditions' => array('SqReportMain.id' =>$reportType[$t][$aTP]['link_report_id'])));
					if(count($reportdetail)>0){
						$linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'SqReports/add_report_view/'.base64_encode($reportdetail[0]['SqReportMain']['id']).' target="_blank" >'.$reportdetail[0]['SqReportMain']['report_no'].'</a>';
						$linkHolder[$u]['summary'][$t][]=$reportdetail[0]['SqReportMain']['summary'];
					}
				}
			 
			   
			}
			if($uniqueType[$u]=='jn'){
			
			   $condition= $aTP.".type ='jn'";
			   $reportType=$madleVal->find('all', array('conditions' => $condition));
			   for($t=0;$t<count($reportType);$t++){
			  
				$reportdetail = $this->JnReportMain->find('all', array('conditions' => array('JnReportMain.id' =>$reportType[$t][$aTP]['link_report_id'])));
				if(count($reportdetail)>0){
			       
				   $linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'Jrns/add_report_view/'.base64_encode($reportdetail[0]['JnReportMain']['id']).' target="_blank" >'.$reportdetail[0]['JnReportMain']['trip_number'].'</a>';
				   $linkHolder[$u]['summary'][$t][]=$reportdetail[0]['JnReportMain']['summary'];
			       
				}
		 
			   
			   }
			 
			   
			}
		     if($uniqueType[$u]=='hsse'){
			
			   $condition= $aTP.".type ='hsse'";
			   $reportType=$madleVal->find('all', array('conditions' => $condition));
	  
				for($t=0;$t<count($reportType);$t++){
			       
				 $reportdetail = $this->Report->find('all', array('conditions' => array('Report.id' =>$reportType[$t][$aTP]['link_report_id'])));
					if(count($reportdetail)){
					     $linkHolder[$u]['report_number'][$t][]='<a href='.$this->webroot.'Report/add_report_view/'.base64_encode($reportdetail[0]['Report']['id']).' target="_blank" >'.$reportdetail[0]['Report']['report_no'].'</a>';
					     $linkHolder[$u]['summary'][$t][]=$reportdetail[0]['Report']['summary'];
					  }
				
				}
			 
			   
			}
		      }
		      
	
		     for($h=0;$h<count($linkHolder);$h++){
			if(isset($linkHolder[$h])){
			
			  for($k=0;$k<count($linkHolder[$h]['report_number']);$k++){
				
				$linkDataHolder['rep_no'][]=$linkHolder[$h]['report_number'][$k][0];
			  
			  }
			  for($k=0;$k<count($linkHolder[$h]['summary']);$k++){
				
				$linkDataHolder['rep_summary'][]=$linkHolder[$h]['summary'][$k][0];
			  
			  }
			}
		     }
		return  $linkDataHolder; 
	}*/
	/**
	* retrive_link_summary: converted but simplified for readability
	* Note: depends on many legacy tables. This function attempts to keep original behaviour
	*/
	public function retriveLinkSummary($madleVal, array $uniqueType, string $aTP): array
	{
		$linkHolder = [];
		$linkDataHolder = [];

		// Helper to build link & summary
		$build = function ($type, $id) use ($aTP) {
		$map = [
			'lesson' => ['table' => 'LessonMains', 'url' => 'Lessons/add_lsreport_view', 'idField' => 'id', 'labelField' => 'report_no', 'summaryField' => 'summary'],
			'job' => ['table' => 'JobReportMains', 'url' => 'Jobs/add_jobreport_view', 'idField' => 'id', 'labelField' => 'report_no', 'summaryField' => 'comment'],
			'audit' => ['table' => 'AuditReportMains', 'url' => 'Audits/add_audit_view', 'idField' => 'id', 'labelField' => 'report_no', 'summaryField' => 'summary'],
			'sq' => ['table' => 'SqReportMains', 'url' => 'SqReports/add_report_view', 'idField' => 'id', 'labelField' => 'report_no', 'summaryField' => 'summary'],
			'jn' => ['table' => 'JnReportMains', 'url' => 'Jrns/add_report_view', 'idField' => 'id', 'labelField' => 'trip_number', 'summaryField' => 'summary'],
			'hsse' => ['table' => 'Reports', 'url' => 'Report/add_report_view', 'idField' => 'id', 'labelField' => 'report_no', 'summaryField' => 'summary'],
			];
			return $map[$type] ?? null;
		};

		foreach ($uniqueType as $uIndex => $type) {
			$condition = [$aTP . '.type' => $type];
			// $madleVal is expected to be a Table or Query object; attempt to iterate results
			$reportType = [];
			if (is_object($madleVal) && method_exists($madleVal, 'find')) {
				$reportType = $madleVal->find()->where($condition)->all()->toArray();
			} 
			elseif (is_array($madleVal)) {
			// fallback: filter array
				foreach ($madleVal as $row) {
					if (!empty($row[$aTP]['type']) && $row[$aTP]['type'] === $type) {
						$reportType[] = $row;
					}
				}
			}

			foreach ($reportType as $tIndex => $row) {
				$linkInfo = $build($type, $row[$aTP]['link_report_id'] ?? null);
				if (!$linkInfo) {
					continue;
				}
				$table = $this->fetchTable($linkInfo['table']);
				$linked = $table->find()->where([$linkInfo['table'] . '.' . $linkInfo['idField'] => $row[$aTP]['link_report_id']])->first();
				if ($linked) {
					$webroot = $this->controller()->getRequest()->getAttribute('webroot') ?? '/';
					$linkHtml = '<a href=' . $webroot . $linkInfo['url'] . '/' . base64_encode($linked->{$linkInfo['idField']}) . ' target="_blank" >' . $linked->{$linkInfo['labelField']} . '</a>';
					$linkHolder[$uIndex]['report_number'][$tIndex][] = $linkHtml;
					$linkHolder[$uIndex]['summary'][$tIndex][] = $linked->{$linkInfo['summaryField']};
				}
			}
		}

		// Flatten into linkDataHolder
		foreach ($linkHolder as $h => $group) {
			if (isset($group['report_number'])) {
				foreach ($group['report_number'] as $k => $arr) {
					$linkDataHolder['rep_no'][] = $arr[0] ?? null;
				}
			}
			if (isset($group['summary'])) {
				foreach ($group['summary'] as $k => $arr) {
					$linkDataHolder['rep_summary'][] = $arr[0] ?? null;
				}
			}
		}

		return $linkDataHolder;
	}
}