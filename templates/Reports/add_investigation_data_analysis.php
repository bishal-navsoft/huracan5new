<script language="javascript" type="text/javascript">
 	function isNumberKey(evt)
    {
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
			return true;
    }	     
</script>
<div class="wrapall">
	<aside>
		<?php echo $this->Element('left_menu'); ?>
	</aside>
	<section>
 	<?php
        echo $this->Element('hssetab');
	?>
    <script language="javascript" type="text/javascript">
		var csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
    	$(document).ready(function() {
			$("#main").removeClass("selectedtab");
			$("#clientdata").removeClass("selectedtab");
			$("#personnel").removeClass("selectedtab");
			$("#incident").removeClass("selectedtab");
			$("#investigation").removeClass("selectedtab");
           	$("#investigationdata").addClass("selectedtab");
			$("#remidialaction").removeClass("selectedtab");
			$("#attachment").removeClass("selectedtab");
			$("#clientfeedback").removeClass("selectedtab");
			$("#view").removeClass("selectedtab");
		});
		
    	function incident_detail(){
			document.getElementById('error_msg').innerHTML='';
			var incident_val = jQuery.trim(document.getElementById('incident_val').value);
			if(incident_val!=0){
				var path='<?php echo $this->webroot; ?>';
				$.ajax({
					type: "POST",
					url: path+"Reports/displayincidentdetail/",
					data: { incidentid: incident_val },
					headers: {
						'X-CSRF-Token': csrfToken
					},
					beforeSend: function() {
						// Optional: show loader before sending
						$('#loss_content').html('<img src="<?php echo $this->webroot; ?>img/ajaxloader.gif" />');
					},
					success: function(res)
					{
		                var splitvalue=res.split("~");
						document.getElementById('loss_content').innerHTML=splitvalue[0];
						document.getElementById('incident_content').innerHTML=splitvalue[1];
						document.getElementById('investigation_no_content').innerHTML=splitvalue[2];
						document.getElementById('investigation_no').value=splitvalue[2];
						document.getElementById('incident_no').value=splitvalue[3];
						/* if(splitvalue[2] )
						{
							for(var i = 0;i < document.getElementById("immediate_cause").length;i++)
							{
								if(document.getElementById("immediate_cause").options[i].value == splitvalue[2] ){
									document.getElementById("immediate_cause").selectedIndex = i;
								}
							}
						}
				  		if(splitvalue[3]){
							if(splitvalue[3]!='no'){
								document.getElementById('immediate_cause_content').innerHTML='<img src="<?php echo $this->webroot; ?>img/ajaxloader.gif" />';   
								document.getElementById('immediate_cause_content').innerHTML=splitvalue[3];
							}
				   			}else{
					   			document.getElementById('immediate_cause_content').innerHTML='';
				         	}
							if(splitvalue[4]){	
									document.getElementById('comments').value=splitvalue[4];
							}else{
								document.getElementById('comments').value='';
							}
							if(splitvalue[5]){
									$('#remedial_holder').append(splitvalue[5]);
							}else{
									document.getElementById('remedial_holder').innerHTML='';
							}
							if(splitvalue[6]){
								document.getElementById('root_cause_container').innerHTML='<img src="<?php echo $this->webroot; ?>img/loader.gif" />';
										document.getElementById('root_cause_container').innerHTML='';
									$('#root_cause_container').append(splitvalue[6]);
							}else{
								document.getElementById('root_cause_container').innerHTML='<img src="<?php echo $this->webroot; ?>img/loader.gif" />';
									document.getElementById('root_cause_container').innerHTML='';	
									$('#root_cause_container').append(splitvalue[2]);
							}
						}	
						*/
					},
					error: function(xhr, status, error) {
						console.error("AJAX Error:", error);
						alert("Something went wrong. Please try again.");
					}
				});
				return false;
			}else{
				document.location="<?php echo $this->webroot; ?>Reports/add_investigation_data_analysis/<?php echo base64_encode($report_id); ?>";    
			}
     	}    

		function immediate_cause_detail() 
		{
			var immediate_cause = $.trim($('#immediate_cause').val());

			if (immediate_cause == 0) {
				$('#immediate_cause_content').html(' ');
			} else {
				$('#immediate_cause_content').html('<img src="<?php echo $this->webroot; ?>img/ajaxloader.gif" />');

				var rootpath = '<?php echo $this->Url->build('/', ['fullBase' => true]); ?>';

				$.ajax({
					type: "POST",
					url: rootpath + "Reports/retrivecause/",
					data: { data: 'a', immediate_cause: immediate_cause },
					headers: {
						'X-CSRF-Token': csrfToken
					},
					success: function(res) {
						$('#immediate_cause_content').html(res);
					},
					error: function(xhr, status, error) {
						console.error("AJAX Error:", error);
						alert("Something went wrong while retrieving immediate cause details.");
					}
				});

				return false;
			}
		}
    
        var trid='<?php echo $tr_id; ?>';
		var tr_id=new Array();
		var idContent=new Array();
		var idC='<?php echo $idContent; ?>';
		var spTRID=trid.split(',');
		var spIDC=idC.split(',');
        if(spTRID.length>0){
	      	for(var i=0; i<spTRID.length; i++){
	    		tr_id.push(spTRID[i]);
				idContent.push(spIDC[i]);
	       }
	  	}

    	function remove_child(rid)
		{
			var child_element=document.getElementById(rid);
			child_element.parentNode.removeChild(child_element);
			var index = tr_id.indexOf(rid);
			tr_id.splice(index, 1);
			//alert(rid.substring(2,rid.length));
			//alert(idContent[0]);
			//alert(idContent[1]);
			var vindex = idContent.indexOf(rid.substring(2,rid.length));
			idContent.splice(vindex, 1);
			var idString=idContent.toString();
			// alert(idString);
			document.getElementById('id_holder').value=idString;  
    	}

		function add_remedial(){
			document.getElementById('error_msg').innerHTML='';  
			var remedial_info = jQuery.trim(document.getElementById('remedial_data').value);
			if(remedial_info!=0){
				var rem_info=remedial_info.split("~");
	  			var passIDD='tr'+rem_info[0];
				if(tr_id.length>0){
	      			for(var i=0;i<tr_id.length;i++){
						if(tr_id[i]==passIDD){
	       					document.getElementById('error_msg').innerHTML='REM-'+rem_info[0]+' already added in investigation team';
			       			return false;
						}
		    		}
	   			}
				tr_id.push(passIDD);
				idContent.push(rem_info[0]);
				var idString=idContent.toString();
				if(idString[0]==','){
					idString.substring(1,idString.length);
				}
				document.getElementById('id_holder').value=idString;
				$('#remedial_holder').append('<tr id=tr'+rem_info[0]+'><td width="12%" align="left" valign="middle"  >REM-'+rem_info[0]+'</td><td width="78%" align="left" valign="middle"  >'+rem_info[1]+'</td><td width="10%" align="left" valign="middle"  ><a href="javascript:void(0);" onclick="remove_child(\''+passIDD+'\');">Remove</a></td></tr>');
			}
		}

     	function  add_root_cause(rtc,tdid)
		{
            var root_cause_id = jQuery.trim(document.getElementById(rtc).value);
		    if(root_cause_id==0)
			{
		 		var idArr = [];
			    var trd= document.getElementById("root_cause_container").getElementsByTagName("tr");
                var deleteId=new Array();
		        for(var i=0;i<trd.length;i++)
				{
				    var idval=trd[i].id.split("-");
					if(tdid<idval[1]){
						deleteId.push(trd[i].id);	   
					}
				}
				for(var j=0 ;j<deleteId.length;j++)
				{
				  	var child_element=document.getElementById(deleteId[j]);
                    child_element.parentNode.removeChild(child_element);
				}
			}else if(root_cause_id!=0)
			{
				var trd= document.getElementById("root_cause_container").getElementsByTagName("tr");
                var deleteId=new Array();
		        for(var i=0;i<trd.length;i++)
				{
		          	var idval=trd[i].id.split("-");
				   	if(tdid<idval[1]){
						deleteId.push(trd[i].id);	   
					}
				}
				for(var j=0 ;j<deleteId.length;j++)
				{
					var child_element=document.getElementById(deleteId[j]);
                    child_element.parentNode.removeChild(child_element);
				}
			 	var rootpath='<?php echo $this->webroot; ?>';
			   	document.getElementById("lH").innerHTML='<img src="<?php echo $this->webroot; ?>img/ajaxloader.gif" />';
			    $.ajax({
					type: "POST",
					url: rootpath + "Reports/retriverootcause/",
					data: { id: root_cause_id },
					headers: {
						'X-CSRF-Token': csrfToken
					},
					beforeSend: function() {
						$("#lH").html('<img src="<?php echo $this->webroot; ?>img/ajaxloader.gif" />');
					},
					success: function(res) {
						$("#lH").html(''); // clear loading icon
						$("#root_cause_container").append(res);
					},
					error: function(xhr, status, error) {
						console.error("AJAX Error:", error);
						alert("Something went wrong while retrieving root cause details.");
					}
				});

			    return false;
		    }
	  	}

     	function add_investigation_data_analysis()
		{
	  		document.getElementById('error_msg').innerHTML='';
			var select_val= document.getElementById("root_cause_container").getElementsByTagName("select");
			var idContainer=new Array();
			for(k=0;k<select_val.length;k++){
				var ids=select_val[k].id;
				var selectid=document.getElementById(ids).value;
				idContainer.push(selectid);
			}
	  		var rootCauseCont=idContainer.toString();
			var select_cause_val= document.getElementById("cause_content").getElementsByTagName("select");
			var  causeContainer=new Array();
			for(j=0;j<select_cause_val.length;j++){
				var idsCause=select_cause_val[j].id;
				var selectCauseId=document.getElementById(idsCause).value;
				causeContainer.push(selectCauseId);
			}
	   		var causeCont=causeContainer.toString();
			var incident_val = jQuery.trim(document.getElementById('incident_val').value);
			var investigation_id = jQuery.trim(document.getElementById('investigation_id').value);
			var id_holder = jQuery.trim(document.getElementById('id_holder').value);
			var comments = jQuery.trim(document.getElementById('comments').value);
			var investigation_no = jQuery.trim(document.getElementById('investigation_no').value);
			var incident_no = jQuery.trim(document.getElementById('incident_no').value);
			var report_id = <?php echo $report_id; ?>;
			if(incident_val==0)
			{
				document.getElementById('incident_id_error').innerHTML='Please select incident id';
				document.getElementById('incident_val').focus();
	       	}else{ 
	  			var dataStr = $("#add_report_investigation_form").serialize();
     			document.getElementById('loader').innerHTML='<img src="<?php echo $this->webroot; ?>img/loader.gif" />';	
	  			var rootpath='<?php echo $this->webroot; ?>';
			    $.ajax({
					type: "POST",
					url: rootpath + "Reports/save_date_analysis/",
					data: {
						data: dataStr,
						incident_val: incident_val,
						rootCauseCont: rootCauseCont,
						causeCont: causeCont,
						remidial_holder: id_holder,
						comments: comments,
						report_id: report_id,
						investigation_id: investigation_id,
						investigation_no: investigation_no,
						incident_no: incident_no
					},
					headers: {
						'X-CSRF-Token': csrfToken
					},
					beforeSend: function() {
						$("#loader").html('<img src="<?php echo $this->webroot; ?>img/loader.gif" /> Saving...');
					},
					success: function(res) {
						var splres = res.split("~");
						if (splres[0] === 'add') {
							$("#loader").html('<font color="green">Incident Investigation Data Added Successfully</font>');
						} else if (splres[0] === 'update') {
							$("#loader").html('<font color="green">Incident Investigation Data Updated Successfully</font>');
						} else {
							$("#loader").html('<font color="red">Unexpected response from server.</font>');
						}

						// redirect to add_investigation_data_analysis page
						if (splres.length >= 4) {
							document.location = rootpath + "Reports/add_investigation_data_analysis/" + splres[1] + "/" + splres[2] + "/" + splres[3];
						}
					},
					error: function(xhr, status, error) {
						console.error("AJAX Error:", error);
						$("#loader").html('<font color="red">Error saving investigation data. Please try again.</font>');
					}
				});

	   			return false;
	 		}
     	}
  	</script>   
    <?php
     
    echo $this->Form->create(null, [
        'url' => ['controller' => 'Reports', 'action' => 'addReportInvestigationData'],
        'name' => 'add_report_investigation_data_form',
        'id' => 'add_report_investigation_data_form',
        'method' => 'post',
        'class' => 'adminform'
    ]);
	$edit_investigation_id = $edit_investigation_id ?? '';
    echo $this->Form->control('investigation_id', [
        'type' => 'hidden',
        'id' => 'investigation_id',
        'name' => 'investigation_id',
        'value' => $edit_investigation_id,
        'label' => false
    ]);

    echo $this->Form->control('investigation_no', [
        'type' => 'hidden',
        'id' => 'investigation_no',
        'name' => 'investigation_no',
        'value' => $investigation_no,
        'label' => false
    ]);

    echo $this->Form->control('incident_no', [
        'type' => 'hidden',
        'id' => 'incident_no',
        'name' => 'incident_no',
        'value' => $incident_no,
        'label' => false
    ]);
	?>

 <h2><?php echo $heading; ?>&nbsp;&nbsp;(<?php echo $report_number; ?>)<span class="textcmpul">Field marked with * are compulsory  </span></h2>
 <br/>
     
       <div class="sub_contentwrap fixreport_table" >
	  <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" >
	       <tr>
                 <td colspan="3" align="center" valign="middle" class="titles">Incident</td>
      
              <tr>
	  </table>
     </div>
    <div class="sub_contentwrap">
	  <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" >

		       <td align="left" width="22%" valign="middle" class="label"><?PHP echo __("Incident Id:");?><span style="color:red;">*</span></td>
		       <td>
			    <select name="incident_val" id="incident_val" onchange="incident_detail();" <?php echo $disabled; ?> >
				<option value="0">Select One</option>
				   	<?php //debug($incidentdetail);
					for ($inc = 0; $inc < count($incidentdetail); $inc++) {
						if ($incidentdetail[$inc]->incident_loss != 0) { ?>
							<option value="<?php echo $incidentdetail[$inc]->id; ?>"
								<?php if ($incidentdetail[$inc]->id == $edit_incident_id) echo "selected"; ?>>
								Incident-<?php echo $incidentdetail[$inc]->incident_no; ?>
							</option>
					<?php } } ?>
	     
	     
			   </select><span class="textcmpul" id="incident_id_error"></span>
		       </td>
		    </tr>
	            <tr>
		       <td align="left" width="22%" valign="middle" class="label"><?PHP echo __("Investigation No:");?></td><td id="investigation_no_content"><?php echo $investigation_no; ?></span></td>
		     
		    </tr>
		    <tr>
			 <td align="left" width="22%" valign="middle" class="label">Loss:</td>
			 <td id="loss_content"><?php echo $ltype; ?></td>
		    </tr>
		    <tr>
			 <td align="left" width="22%" valign="middle" class="label">Incident:</td>
			 <td id="incident_content"><?php echo $incident_summary; ?></td>
		    </tr>
			 
		   
	  </table>
    </div>
     <div class="sub_contentwrap fixreport_table" >
	  <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" >
	       <tr>
                 <td colspan="3" align="center" valign="middle" class="titles">Immediate Cause</td>
      
              <tr>
	  </table>
     </div>
    <div class="sub_contentwrap">
		<table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" id="cause_content" >
		    <tr>
		       <td align="left" width="22%" valign="middle" class="label"><?PHP echo __("Immediate Cause:");?></td>
		       <td  valign="middle" >
			    <select name="immediate_cause" id="immediate_cause" onchange="immediate_cause_detail();">
                    <option value="0">Select One</option>
				   	<?php foreach ($immediateCauseDetail as $cause): ?>
						<option value="<?= $cause->id ?>" <?= $cause->id == $immediate_cause ? 'selected' : '' ?>>
							<?= h($cause->type) ?>
						</option>
					<?php endforeach; ?>
          		</select>
			    <span id="immediate_cause_content">
			    <?php if (!empty($immediate_cause) && $immediate_cause != 0): ?>
					<select id="immeditaet_sub_cus" name="immeditaet_sub_cus">
						<option value="0">Select One</option>
						<?php foreach ($immediateSubCauseList as $subCause): ?>
							<option value="<?= h($subCause->id) ?>"
								<?= ($subCause->id == $immediate_sub_cause) ? 'selected' : '' ?>>
								<?= h($subCause->type) ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			    </span>
		       </td>
		    </tr>
		    <!-- <tr><td align="left" width="22%" valign="middle" class="label"><?PHP echo __("Comments:");?></td><td><?PHP echo $this->Form->input('comments', array('type'=>'text', 'id'=>'comments','name'=>'comments','value'=>$comments,'label' => false,'div' => false)); ?></td></tr> -->
			<tr>
				<td align="left" width="22%" valign="middle" class="label">
					<?php echo __("Comments:"); ?>
				</td>
				<td>
					<?php
					echo $this->Form->input('comments', [
						'type'  => 'text',
						'id'    => 'comments',
						'name'  => 'comments',
						'value' => $comments ?? '', // ✅ Prevents undefined variable
						'label' => false,
						'div'   => false
					]);
					?>
				</td>
			</tr>
	  </table>
    </div>

    
    <div class="sub_contentwrap fixreport_table" >
	  <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" >
	       <tr>
                 <td colspan="3" align="center" valign="middle" class="titles">Remedial Action Item</td>
      
              <tr>
	  </table>
     </div>
    
    <div class="looptable_panel">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
      <tr>
        <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dyntable">
          <tr>
            <td width="22%" align="left" valign="middle">
	       		<select id="remedial_data" name="remedial_data"   >
                 <option value="0">Select One</option>	  
				<?php foreach ($remidialData as $item): ?>
					<option value="<?= $item->id . '~' . h($item->remidial_summery) ?>">
						<?= 'REM-' . $item->id ?>
					</option>
				<?php endforeach; ?>
              	</select>
	    	</td>
	    	<td id="error_msg" width="68%" class="textcmpul" style="color: red;"></td>
	   		<td width="10%" align="right" valign="middle" >
	       	<?php if($is_add==1){?>
	       		<input type="button" name="button" value="Add" class="buttonsave" onclick="add_remedial();" />
	       	<?php }else{?>
		    	&nbsp;
	      	<?php  }?>
	       <input type="hidden" id="id_holder" id="id_holder" value="<?php echo $id_holder; ?>" />
	   </td>
            
          </tr>
        </table></td>
      </tr>
    </table>
  </div>
    <div class="sub_contentwrap fixreport_table" >
     <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" id="remedial_holder">
	  <?php if (!empty($remidialList) && count($remidialList) > 0): ?>
			<?php foreach ($remidialList as $rem): ?>
				<tr id="tr<?= h($rem->id) ?>">
					<td width="12%" align="left" valign="middle">
						<?= 'REM-' . h($rem->remedial_no) ?>
					</td>
					<td width="78%" align="left" valign="middle">
						<?= h($rem->remidial_action) ?>
					</td>
					<td width="10%" align="left" valign="middle">
						<a href="javascript:void(0);" onclick="remove_child('tr<?= h($rem->id) ?>');">Remove</a>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	 </table>	  
    </div>
        <div class="sub_contentwrap fixreport_table" >
	  <table width="100%" border="0" cellspacing="1" cellpadding="0" class="reporttable" >
	       <tr>
                 <td colspan="3" align="center" valign="middle" class="titles">Root Cause &nbsp;<span id="lH"></span></td>
      
              <tr>
	  </table>
     </div>
         <div class="looptable_panel">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
      <tr>
        <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dyntable" id="root_cause_container">
          <tr id="tr-0">
            <td width="22%" align="left" valign="middle">

	       <select id="root_cause" name="root_cause"  onchange="add_root_cause('root_cause',0)">
                <option value="0">Select One</option>	  
		    	<?php foreach ($rootParrentCauseData as $rootCause): ?>
				<option value="<?= $rootCause->id ?>" <?= ($rootParrentCausevalue == $rootCause->id) ? 'selected' : '' ?>>
					<?= h($rootCause->type) ?>
				</option>
				<?php endforeach; ?>
            </select>
	    </td>
	    <td id="error_msg" width="68%" class="textcmpul" style="color: red;"></td>
	   <td width="10%" align="right" valign="middle" >&nbsp;</td>
            <input type="hidden" id="root_cause_holder" id="root_cause_holder" value="" /> 
          </tr>
	  <?php
	    //  echo '<pre>';
	    // print_r($parrentRoot);
	    // echo '<br>';
	    //  print_r($explode_root_cause);
	    //  echo '<br>';
	    // print_r($childRoot);

	    
	     
	       if (!empty($parrentRoot) && is_array($parrentRoot)) : 
     for ($p = 0; $p < count($parrentRoot) - 1; $p++) : ?>
        <?php 
            $parentId = $parrentRoot[$p];
            $childData = $childRoot[$parentId][0] ?? [];
        ?>
        <tr id="tr-<?php echo $parentId; ?>">
            <td width="22%" align="left" valign="middle">
                <select 
                    id="root_cause-<?php echo $parentId; ?>" 
                    name="root_cause-<?php echo $parentId; ?>"
                    onchange="add_root_cause('root_cause-<?php echo $parentId; ?>', <?php echo $parentId; ?>)"
                >
                    <option value="0">Select One</option>
                    <?php if (!empty($childData) && is_array($childData)) : ?>
                        <?php foreach ($childData as $c => $child) : ?>
                            <option 
                                value="<?php echo $child['RootCause']['id']; ?>" 
                                <?php echo ($child['RootCause']['id'] == ($explode_root_cause[$p + 1] ?? null)) ? 'selected' : ''; ?>
                            >
                                <?php echo $child['RootCause']['type']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td width="68%"></td>
            <td width="10%" align="right" valign="middle">&nbsp;</td>
        </tr>
    <?php endfor; ?>
<?php endif; ?>

	      
	  
	 </table></td>
      </tr>
    </table>
  </div> 	
<div class="clearflds"></div>

<?php echo $this->Form->end(); ?>
<div class="buttonpanel">
<span id="loader" class="textcmpul"  style="float:left;font-size: 13px;"></span>
<?php if($button=='Submit'){
     if($is_add==1){?>
     <input type="button" name="save" id="save" class="buttonsave" onclick="add_investigation_data_analysis();" value="<?php echo $button; ?>" />
     <?php }  ?>

<?php }else{
     if($is_edit==1){?>
     
        <input type="button" name="save" id="save" class="buttonsave" onclick="add_investigation_data_analysis();" value="<?php echo $button; ?>" />

<?php } } ?>
    

</div>
</section>    