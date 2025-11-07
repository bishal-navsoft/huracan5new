<!-- <script type="text/javascript" src="<?php echo $this->request->getAttribute('webroot') ?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

<script  language="javascript" type="text/javascript">
    function open_lightbox(id)
    {
	    $.fancybox.open({
            src: path + "Reports/remidial_email_view/" + id + "/" + remedial_no + "/" + report_id,
            type: 'iframe',
            opts: {
                animationEffect: "fade",
                buttons: ["close"],
                infobar: false,
                toolbar: false,
                modal: true,
                baseClass: "fancybox-custom-layout",
                afterShow: function (instance, current) {
                    console.info('done!');
                },
                iframe: {
                    preload: false
                }
            }
        });
    }
     function redirect_report(){
      var report_list_id=document.getElementById('report_list').value;
      document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_report_main/"+report_list_id;    
     }

    function change_dirction(tabval){
          switch(tabval){
            case'main':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_report_main/<?php echo $this->getRequest()->getParam('pass.0'); ?>";    
            break;
            case'clientdata':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_report_client/<?php echo $this->getRequest()->getParam('pass.0'); ?>";
            break;
            case'personnel':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_perssonel_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>"; 
            break;
            case'incident':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_incident_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>";  
            break;
            case'investigation':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_hsse_investigation/<?php echo $this->getRequest()->getParam('pass.0'); ?>";
            break;
            case'remidialaction':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_remidial_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>";  
            break;
	        case'remidialemail':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/hsse_remedila_email_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>";  
            break;
            case 'dataanalysis':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_investigation_data_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>";      
            break;   
            case'attachment':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_attachment_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>";
            break;
	        case'link':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/report_hsse_link_list/<?php echo $this->getRequest()->getParam('pass.0'); ?>/<?php echo base64_encode('all'); ?>";
            break;
            case'clientfeedback':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_hsse_feedback/<?php echo $this->getRequest()->getParam('pass.0'); ?>";  
            break;
            case'view':
            document.location="<?php echo $this->request->getAttribute('webroot'); ?>Reports/add_report_view/<?php echo $this->getRequest()->getParam('pass.0'); ?>";  
            break;
         }
    }
    
</script>
    
    <?php
        $session = $this->request->getSession();
        $reportCreate = $session->read('report_create');
        $adminData = $session->read('adminData');
        $adminId = $session->read('admin_id');
        $clientTab = $session->read('clienttab');
        $clientFeedback = $client_feedback ?? 0;
        $adminMasterId = $adminData['id'];
        $roleMasterId = $adminData['role_master']['id'];
        // echo '<pre>';
        // echo "reportCreate = "; var_dump($reportCreate);
        // echo "adminId = "; var_dump($adminId);
        // echo "adminData = "; var_dump($adminData);
        // echo "clientTab = "; var_dump($clientTab);
        // echo "adminMasterId = "; var_dump($adminMasterId);
        // echo "roleMasterId = "; var_dump($roleMasterId);
        // echo '</pre>';
    ?>
    <div class="tabspanel">
        <ul>
            <?php
                //$roleId = $session->read('admin_role_id');debug($roleId);
              if (($reportCreate == $adminData['id']) || ($adminData['role_master']['id'] == 1)){
            ?>
            <li><a href="javascript:void(0);" id="main" class="selectedtab" onclick="change_dirction('main');">Main</a></li>
            <?php if ($clientTab != 10): ?>
                <li><a href="javascript:void(0);" id="clientdata" onclick="change_dirction('clientdata');">Client Data</a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0);" id="personnel" onclick="change_dirction('personnel');">Personnel</a></li>
            <li><a href="javascript:void(0);" id="incident" onclick="change_dirction('incident');">Incident</a></li>
            <li><a href="javascript:void(0);" id="investigation" onclick="change_dirction('investigation');">Investigation</a></li>
            <li><a href="javascript:void(0);" id="remidialaction" onclick="change_dirction('remidialaction');">Remedial Action</a></li>
            <?php if ($roleMasterId == 1): ?>
                <li><a href="javascript:void(0);" id="remidialemail" onclick="change_dirction('remidialemail');">Remedial Action Email</a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0);" id="investigationdata" onclick="change_dirction('dataanalysis');">Incident Investigation</a></li>
            <li><a href="javascript:void(0);" id="attachment" onclick="change_dirction('attachment');">Attachment</a></li>
            <li><a href="javascript:void(0);" id="link" onclick="change_dirction('link');">Link</a></li>
            <?php if ($clientFeedback == 1): ?>
                <li><a href="javascript:void(0);" id="clientfeedback" onclick="change_dirction('clientfeedback');">Client Feedback</a></li>
            <?php endif; ?>
            <?php } ?>

            <li><a href="javascript:void(0);" id="view" onclick="change_dirction('view');">View</a></li>
            <li><a href="javascript:void(0);" id="print" onclick="open_lightbox('<?php echo $this->getRequest()->getParam('pass.0'); ?>');">Print Preview</a></li>

            <div class="clear"></div>
        </ul>
    </div>