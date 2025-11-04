<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Mailer\Mailer;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\FrozenTime;

class EmailsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadModel('Emails');
        $this->loadModel('Reports');
        $this->loadModel('JhaMain');
        $this->loadModel('LessonMain');
        $this->loadModel('CertificationEmail');
        $this->loadModel('LdjsEmail');
        $this->loadModel('DocumentMain');
        $this->loadModel('SuggestionMain');
        $this->loadModel('SuggestionRemidial');
        $this->loadModel('AuditReportMain');
        $this->loadModel('AuditRemidial');
        $this->loadModel('JobRemidial');
        $this->loadModel('JobReportMain');
        $this->loadModel('RemidialEmailList');
        $this->loadModel('SqRemidial');
        $this->loadModel('SqReportMain');
        $this->loadModel('Incident');
        $this->loadModel('BusinessType');
        $this->loadModel('Fieldlocation');
        $this->loadModel('Client');
        $this->loadModel('Country');
        $this->loadModel('AdminMasters');
        $this->loadModel('HsseClient');
        $this->loadModel('HsseIncident');
        $this->loadModel('HssePersonnel');
        $this->loadModel('RolePermission');
        //$this->loadModel('RoleMasters');
        $this->loadModel('AdminMenu');
        $this->loadModel('IncidentCategory');
        $this->loadModel('Losses');
        $this->loadModel('IncidentSubCategory');
        $this->loadModel('Residual');
        $this->loadModel('HsseAttachment');
        $this->loadModel('HsseRemidial');
        $this->loadModel('Priority');
        $this->loadModel('ImmediateCauses');
        $this->loadModel('ImmediateSubCause');
        $this->loadModel('HsseInvestigation');
        $this->loadModel('RootCause');
        $this->loadModel('HsseInvestigationData');
        $this->loadModel('Potential');
        $this->loadModel('HsseClientfeedback');
        $this->viewBuilder()->setLayout('ajax');
    }
	function auto_generate_email(){
		  Configure::write('debug', '2');
		  $this->layout="ajax";
		  $currentDate=date("Y-m-d");
		  
		  $rel = $this->RemidialEmailList->find('all', array('conditions' => array('RemidialEmailList.status' =>'N','RemidialEmailList.email_date' =>$currentDate)));

                  if(count($rel)>0){
		       $reporttype='';
			for($r=0;$r<count($rel);$r++){
				 $userDetail= $this->AdminMaster->find('all', array('conditions' => array('AdminMaster.admin_email' =>$rel[$r]['RemidialEmailList']['email'])));
				 $fullname=$userDetail[0]['AdminMaster']['first_name'].' '.$userDetail[0]['AdminMaster']['last_name'];
				 if($rel[$r]['RemidialEmailList']['report_type']=='hsse'){
					$reportNo= $this->Report->find('all', array('conditions' => array('Report.id' =>$rel[$r]['RemidialEmailList']['report_id'])));
				  
					$rportno=$reportNo[0]['Report']['report_no'];
					$remDetail= $this->HsseRemidial->find('all', array('conditions' => array('HsseRemidial.report_no' =>$rel[$r]['RemidialEmailList']['report_id'],'HsseRemidial.remedial_no' =>$rel[$r]['RemidialEmailList']['remedial_no'])));
					$remidial_summery=$remDetail[0]['HsseRemidial']['remidial_summery'];
					if($remDetail[0]['HsseRemidial']['remidial_closure_date']!='0000-00-00'){
					       $rem_cls_date=explode("-",$remDetail[0]['HsseRemidial']['remidial_closure_date']);
					       $remidial_closure_date=date("d/M/y", mktime(0, 0, 0, $rem_cls_date[1], $rem_cls_date[2], $rem_cls_date[0]));
					 }else{
					       $remidial_closure_date='';	
					 }
					$reporttype='<font style="color: #75309F;font-weight:bold" >HSSE Incident Report</font>'; 
				 }
				 if($rel[$r]['RemidialEmailList']['report_type']=='sq'){
					$reportNo= $this->SqReportMain->find('all', array('conditions' => array('SqReportMain.id' =>$rel[$r]['RemidialEmailList']['report_id'])));
					$rportno=$reportNo[0]['SqReportMain']['report_no'];
					$remDetail= $this->SqRemidial->find('all', array('conditions' => array('SqRemidial.report_no' =>$rel[$r]['RemidialEmailList']['report_id'],'SqRemidial.remedial_no' =>$rel[$r]['RemidialEmailList']['remedial_no'])));
					$remidial_summery=$remDetail[0]['SqRemidial']['remidial_summery'];
					if($remDetail[0]['SqRemidial']['remidial_closure_date']!='0000-00-00'){
					       $rem_cls_date=explode("-",$remDetail[0]['HsseRemidial']['remidial_closure_date']);
					       $remidial_closure_date=date("d/M/y", mktime(0, 0, 0, $rem_cls_date[1], $rem_cls_date[2], $rem_cls_date[0]));
					 }else{
					       $remidial_closure_date='';	
					 }
					$reporttype='<font style="color: #75309F;font-weight:bold" >Service Quality Incident Report</font>'; 
				 }
				 if($rel[$r]['RemidialEmailList']['report_type']=='audit'){
					$reportNo= $this->AuditReportMain->find('all', array('conditions' => array('AuditReportMain.id' =>$rel[$r]['RemidialEmailList']['report_id'])));
					$rportno=$reportNo[0]['AuditReportMain']['report_no'];
					$remDetail= $this->AuditRemidial->find('all', array('conditions' => array('AuditRemidial.report_no' =>$rel[$r]['RemidialEmailList']['report_id'],'AuditRemidial.remedial_no' =>$rel[$r]['RemidialEmailList']['remedial_no'])));
					$remidial_summery=$remDetail[0]['AuditRemidial']['remidial_summery'];
					if($remDetail[0]['AuditRemidial']['remidial_closure_date']!='0000-00-00'){
					       $rem_cls_date=explode("-",$remDetail[0]['AuditRemidial']['remidial_closure_date']);
					       $remidial_closure_date=date("d/M/y", mktime(0, 0, 0, $rem_cls_date[1], $rem_cls_date[2], $rem_cls_date[0]));
					 }else{
					       $remidial_closure_date='';	
					 }
					 $reporttype='<font style="color: #75309F;font-weight:bold" >Audit / Inspection Report</font>'; 
				 }
				 if($rel[$r]['RemidialEmailList']['report_type']=='job'){
					$reportNo= $this->JobReportMain->find('all', array('conditions' => array('JobReportMain.id' =>$rel[$r]['RemidialEmailList']['report_id'])));
					$rportno=$reportNo[0]['JobReportMain']['report_no'];
					$remDetail= $this->JobRemidial->find('all', array('conditions' => array('JobRemidial.report_no' =>$rel[$r]['RemidialEmailList']['report_id'],'JobRemidial.remedial_no' =>$rel[$r]['RemidialEmailList']['remedial_no'])));
					$remidial_summery=$remDetail[0]['JobRemidial']['remidial_summery'];
					if($remDetail[0]['JobRemidial']['remidial_closure_date']!='0000-00-00'){
					       $rem_cls_date=explode("-",$remDetail[0]['JobRemidial']['remidial_closure_date']);
					       $remidial_closure_date=date("d/M/y", mktime(0, 0, 0, $rem_cls_date[1], $rem_cls_date[2], $rem_cls_date[0]));
					 }else{
					       $remidial_closure_date='';	
					 }
					 $reporttype='<font style="color: #75309F;font-weight:bold" >Job Report</font>'; 
				 }
				  if($rel[$r]['RemidialEmailList']['report_type']=='suggestion'){
					$reportNo= $this->SuggestionMain->find('all', array('conditions' => array('SuggestionMain.id' =>$rel[$r]['RemidialEmailList']['report_id'])));
					$rportno=$reportNo[0]['SuggestionMain']['report_no'];
					$remDetail= $this->SuggestionRemidial->find('all', array('conditions' => array('SuggestionRemidial.report_no' =>$rel[$r]['RemidialEmailList']['report_id'],'SuggestionRemidial.remedial_no' =>$rel[$r]['RemidialEmailList']['remedial_no'])));
					$remidial_summery=$remDetail[0]['SuggestionRemidial']['remidial_summery'];
					if($remDetail[0]['SuggestionRemidial']['remidial_closure_date']!='0000-00-00'){
					       $rem_cls_date=explode("-",$remDetail[0]['SuggestionRemidial']['remidial_closure_date']);
					       $remidial_closure_date=date("d/M/y", mktime(0, 0, 0, $rem_cls_date[1], $rem_cls_date[2], $rem_cls_date[0]));
					 }else{
					       $remidial_closure_date='';	
					 }
					 $reporttype='<font style="color: #75309F;font-weight:bold" >Suggestion Report</font>'; 
				 }
				 $remdet=$this->remedial_email($rel[$r]['RemidialEmailList']['email'],$fullname,$rel[$r]['RemidialEmailList']['remedial_no'],$rportno,$remidial_summery,$remidial_closure_date,$reporttype);
				 if($remdet=='ok'){
				          $this->request->data['RemidialEmailList']['id'] = $rel[$r]['RemidialEmailList']['id'];
					  $this->request->data['RemidialEmailList']['status'] = 'Y';
				          $this->RemidialEmailList->save($this->request->data,false);
					
				 }
			}
		  }else{
		   echo 'No  remedial email send';
		  }
		 
	       $ce = $this->CertificationEmail->find('all', array('conditions' => array('CertificationEmail.status' =>'N','CertificationEmail.email_date' =>$currentDate)));
	       if(count($ce)>0){
		   for($c=0;$c<count($ce);$c++){
			
			 $subject='Your certification detail';
		         $from = 'jhollingworth@huracan.com.au';
			 $to = $ce[$c]['AdminMaster']['admin_email'];
			 //$to = 'debi15prasad@gmail.com';
		         $subject='Your Certification detail';
			 
			 $ex=explode("-",$ce[$c]['CertificationMain']['expire_date']);
			 $certificate_expire_date=date("d/M/y", mktime(0, 0, 0, $ex[1], $ex[2], $ex[0]));
			 $cdate=explode("-",$ce[$c]['CertificationMain']['cert_date']);
			 $certificate_cert_date=date("d/M/y", mktime(0, 0, 0, $cdate[1], $cdate[2], $cdate[0]));
			 $triner=$ce[$c]['CertificationMain']['triner'];
			 $validate=$ce[$c]['CertificationMain']['valid_date'];
			 $type=$ce[$c]['CertificationList']['type'];
			 $fullname=$ce[$c]['AdminMaster']['first_name'].' '.$ce[$c]['AdminMaster']['last_name'];
		         $certemail=$this->cert_email($fullname,$certificate_expire_date,$certificate_cert_date,$validate,$type,$fullname,$ce[$c]['CertificationMain']['id'],$from,$to,$subject);      
		      
                       
		   }
		
	       }else{
	         echo 'No certification email send'; 
	       }
	       
	       $le = $this->LdjsEmail->find('all', array('conditions' => array('LdjsEmail.status' =>'N','LdjsEmail.email_date' =>$currentDate,'LdjsEmail.type' =>'lesson'),'recursive'=>2));

	      if(count($le)>0){
		   for($c=0;$c<count($le);$c++){
			
			  $reportdetail = $this->LessonMain->find('all', array('conditions' => array('LessonMain.id' =>$le[$c]['LdjsEmail']['leid'])));            
			 $subject='Your Job Hazard Analysis Detail';
		         $from = 'jhollingworth@huracan.com.au';
			 $to = $le[$c]['AdminMaster']['admin_email'];
			 //$to = 'debi15prasad@gmail.com';
			 
			 $vx=explode("-",$reportdetail[0]['LessonMain']['validation_date']);
			 $v_date=date("d/M/y", mktime(0, 0, 0, $vx[1], $vx[2],$vx[0]));
			 
			 $rvd=explode("-",$reportdetail[0]['LessonMain']['revalidate_date']);
			 $rv_date=date("d/M/y", mktime(0, 0, 0, $rvd[1], $rvd[2],$rvd[0]));
			 
			 $fullname=$le[$c]['AdminMaster']['first_name'].' '.$le[$c]['AdminMaster']['last_name'];
					 
			$lessonemail=$this->ldjs_email($fullname, $reportdetail[0]['LessonMain']['report_no'],$v_date,$rv_date,$reportdetail[0]['LessonMain']['summary'],$reportdetail[0]['LessonMain']['details'],'LdjsEmail',$le[$c]['LdjsEmail']['id'],$subject,$from,$to,'Lesson');
                       
		   }
		
	       }else{
	         echo 'No best practice lesson email send'; 
	       } 
	       
	       
	       $ja = $this->LdjsEmail->find('all', array('conditions' => array('LdjsEmail.status' =>'N','LdjsEmail.email_date' =>$currentDate,'LdjsEmail.type' =>'jha'),'recursive'=>2));

	       
                  if(count($ja)>0){
		   for($c=0;$c<count($ja);$c++){
			
			 $reportdetail = $this->JhaMain->find('all', array('conditions' => array('JhaMain.id' =>$ja[$c]['LdjsEmail']['leid'])));            
			 $subject='Your Job Hazard Analysis Detail';
		         $from = 'jhollingworth@huracan.com.au';
			 $to = $ja[$c]['AdminMaster']['admin_email'];
			// $to = 'debi15prasad@gmail.com';
			 
			 $vx=explode("-",$reportdetail[0]['JhaMain']['validation_date']);
			 $v_date=date("d/M/y", mktime(0, 0, 0, $vx[1], $vx[2],$vx[0]));
			 
			 $rvd=explode("-",$reportdetail[0]['JhaMain']['revalidate_date']);
			 $rv_date=date("d/M/y", mktime(0, 0, 0, $rvd[1], $rvd[2],$rvd[0]));
			 
			 $fullname=$ja[$c]['AdminMaster']['first_name'].' '.$ja[$c]['AdminMaster']['last_name'];
					 
			$jhaemail=$this->ldjs_email($fullname, $reportdetail[0]['JhaMain']['report_no'],$v_date,$rv_date,$reportdetail[0]['JhaMain']['summary'],$reportdetail[0]['JhaMain']['details'],'LdjsEmail',$ja[$c]['LdjsEmail']['id'],$subject,$from,$to,'Job Hazard Analysis');
	                     
		   }
		
	       }else{
	         echo 'No job hazard analysis  email send'; 
	       } 
	      
	       $ja = $this->LdjsEmail->find('all', array('conditions' => array('LdjsEmail.status' =>'N','LdjsEmail.email_date' =>$currentDate,'LdjsEmail.type' =>'document'),'recursive'=>2));

	       
                  if(count($ja)>0){
		   for($c=0;$c<count($ja);$c++){
			
			 $reportdetail = $this->DocumentMain->find('all', array('conditions' => array('DocumentMain.id' =>$ja[$c]['LdjsEmail']['leid'])));            
			 $subject='Your Job Hazard Analysis Detail';
		         $from = 'jhollingworth@huracan.com.au';
			 $to = $ja[$c]['AdminMaster']['admin_email'];
			 //$to = 'debi15prasad@gmail.com';
			 
			 $vx=explode("-",$reportdetail[0]['DocumentMain']['validation_date']);
			 $v_date=date("d/M/y", mktime(0, 0, 0, $vx[1], $vx[2],$vx[0]));
			 
			 $rvd=explode("-",$reportdetail[0]['DocumentMain']['revalidate_date']);
			 $rv_date=date("d/M/y", mktime(0, 0, 0, $rvd[1], $rvd[2],$rvd[0]));
			 
			 $fullname=$ja[$c]['AdminMaster']['first_name'].' '.$ja[$c]['AdminMaster']['last_name'];
					 
			$document=$this->ldjs_email($fullname, $reportdetail[0]['DocumentMain']['report_no'],$v_date,$rv_date,$reportdetail[0]['DocumentMain']['summary'],$reportdetail[0]['DocumentMain']['details'],'LdjsEmail',$ja[$c]['LdjsEmail']['id'],$subject,$from,$to,'Document');
	                     
		   }
		
	       }else{
	         echo 'No document  email send'; 
	       } 
	      
	        
	       
	       
	 exit;	  
		
	}
}	