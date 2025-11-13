<?php
echo $this->Html->css('imageupload');
$webroot = $this->request->getAttribute('webroot');
$attachment_id   = $attachment_id   ?? 0;
$heading         = $heading         ?? 'Add Attachment';
$description     = $description     ?? '';
$imagepath       = $imagepath       ?? '';
$attachmentstyle = $attachmentstyle ?? 'style="display:none;"';
$report_number   = $report_number   ?? '';
$report_id       = $report_id       ?? 0;
$imagename       = $imagename       ?? '';
$button          = $button          ?? 'Add';
?>

<script type="text/javascript">
var csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
var rootpath = '<?= $webroot; ?>';

    /*function uploadFile() 
    {
        $('#displayFile').show();
        $('#loaderRes').html('<br /><img src="' + rootpath + 'img/ajax-loader.gif">');
        const uploadPath = rootpath + 'Reports/uploadimage/upload';
        $("#add_report_attachment_form")
            .attr("action", uploadPath)
            .attr("target", "imgFrame")
            .submit();
    }*/

    function uploadFile() {
        const fileInput = document.querySelector('#BrowserHidden');
        if (!fileInput || !fileInput.files.length) {
            $('#image_upload_res').html('<span style="color:red;">Please select a file</span>');
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file_upload', file);
        console.log(file);
        $('#loaderRes').html('<br /><img src="' + rootpath + 'img/ajax-loader.gif">');

        $.ajax({
            url: rootpath + 'Reports/uploadimage/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-Token': csrfToken },
            success: function(res) {
                $('#loaderRes').html('');

                if (res.status === 'success') {
                    $('#image_upload_res').html('<span style="color:green;">' + res.message + '</span>');
                    $('#hiddenFile').val(res.filename);
                    $('#displayFile')
                        .show()
                        .html(`<span id="${res.filename}">
                                <a href="${res.file_url}" target="_blank">${res.filename}</a>
                            </span>`);
                    $('#del').show();
                } else {
                    $('#image_upload_res').html('<span style="color:red;">' + res.message + '</span>');
                }
            },
            error: function(xhr, status, error) {
                $('#loaderRes').html('');
                $('#image_upload_res').html('<span style="color:red;">Upload failed: ' + error + '</span>');
            }
        });
    }

    function uploadComplete(fileName) {
        if (fileName && fileName !== 'fail') {
            $('#hiddenFile').val(fileName);
            $('#image_upload_res').html('<span style="color:green;">File uploaded successfully</span>');
        } else {
            $('#image_upload_res').html('<span style="color:red;">File upload failed</span>');
        }
    }

    function confirmFun() 
    {
        const imagename = $('#hiddenFile').val();
        if (!imagename) return false;

        if (confirm("Do you want to delete this file?")) {
            const deletePath = rootpath + 'Reports/uploadimage/delete/' + encodeURIComponent(imagename);
            $('#loaderRes').html('<br /><img src="' + rootpath + 'img/ajax-loader.gif">');
            $("#add_report_attachment_form")
                .attr("action", deletePath)
                .attr("target", "imgFrame")
                .submit();
            $('#hiddenFile').val('');
            $('#FileField').val('');
            $('#displayFile').hide();
            $('#del').hide();
        }
        return false;
    }

    function add_hsse_attach() 
    {
        const attachment_description = $.trim($('#attachment_description').val());
        const file_name = $.trim($('#hiddenFile').val());
        const report_id = '<?= $report_id; ?>';

        if (attachment_description === '') {
            $('#description_error').html('Enter description');
            $('#attachment_description').focus();
            return false;
        } else {
            $('#description_error').html('');
        }

        if (file_name === '') {
            $('#image_upload_res').html('<span class="textcmpul">Please upload file</span>');
            return false;
        }
        const attachment_id = $('#attachment_id').val();
        console.log('Attachment ID:', attachment_id);
        const formData = {
            'id': attachment_id,
            'attachment_description': attachment_description,
            'hiddenFile': file_name,
            'report_id': report_id
        };

        $("#loader").html('<img src="' + rootpath + 'img/loader.gif" />');

        $.ajax({
            type: "POST",
            url: rootpath + "Reports/hsseattachmentprocess/",
            data: formData,
            headers: { "X-CSRF-Token": csrfToken },
            success: function(res) {
                if (res === 'fail') {
                    $("#loader").html('<span style="color:red;">Please try again</span>');
                } else if (res === 'add' || res === 'update') {
                    const msg = res === 'add' ? 'Attachment added successfully' : 'Attachment updated successfully';
                    $("#loader").html('<span style="color:green;">' + msg + '</span>');
                    setTimeout(function() {
                        document.location = rootpath + "Reports/report_hsse_attachment_list/<?= base64_encode($report_id); ?>";
                    }, 800);
                } else {
                    $("#loader").html('<span style="color:red;">Unexpected response: ' + res + '</span>');
                }
            },
            error: function(xhr, status, error) {
                $("#loader").html('<span style="color:red;">Request failed: ' + error + '</span>');
            }
        });
        return false;
    }
</script>

<div class="wrapall">
    <aside><?= $this->Element('left_menu'); ?></aside>

    <section>
        <?= $this->Element('hssetab'); ?>

        <script>
        $(document).ready(function() {
            $("#main, #clientdata, #personnel, #incident, #investigation, #investigationdata, #remidialaction, #clientfeedback, #view")
                .removeClass("selectedtab");
            $("#attachment").addClass("selectedtab");
        });
        </script>

        <?= $this->Form->create(
			null,
			[
				'type' => 'file',
				'id' => 'add_report_attachment_form',
				'name' => 'add_report_attachment_form',
				'class' => 'adminform',
				'url' => ['controller' => 'Reports', 'action' => 'add_hsse_attachment', $report_id]
			]
		); //debug($attachment_id);?>
        <?= $this->Form->hidden('attachment_id', ['id' => 'attachment_id', 'value' => $attachment_id]); ?>

        <h2><?= h($heading); ?>&nbsp;&nbsp;<?= h($report_number); ?>
            <span class="textcmpul">* Required fields</span>
        </h2><br/>

        <label>Description</label>
        <?= $this->Form->control('attachment_description', [
            'type' => 'text',
            'id' => 'attachment_description',
            'label' => false,
            'value' => $description,
            'class' => 'textclass',
            'div' => false
        ]); ?>
        <span class="textcmpul" id="description_error"></span>

        <div class="clearflds"></div>

        <label><?= __("Upload File"); ?></label>
        <div class="upload-here">
            <input type="file" name="data[Reports][file_upload]" id="BrowserHidden" onchange="$('#FileField').val(this.value);" />
            <div id="BrowserVisible">
                <input type="text" id="FileField" />
            </div>
            <input type="button" name="upload" id="uploadBtn" class="buttonsave" onclick="return uploadFile();" value="Upload" />
        </div>

        <div class="clearflds"></div>

        <label>&nbsp;</label>
        <div class="upload_filewrap">
            <span id="file_upload">
                <span id="displayFile"><?= $imagepath; ?></span>
                <div class="picnav">
                    <a href="javascript:void(0);" id="del" <?= $attachmentstyle; ?> onclick="return confirmFun();">Delete</a>
                </div>
                <div class="clearupload"></div>
            </span>
            <div class="upload_success" id="image_upload_res"></div>
        </div>

        <?= $this->Form->hidden('hiddenFile', ['id' => 'hiddenFile', 'value' => $imagename]); ?>

        <iframe id="imgFrame" name="imgFrame" src="" style="width:100px;height:100px;border:1px solid #D5E278; display:none"></iframe>

        <div class="buttonpanel">
            <span id="loader" style="font-size:13px;"></span>
            <input type="button" id="saveBtn" class="buttonsave" onclick="add_hsse_attach();" value="<?= h($button); ?>" />
        </div>

        <?= $this->Form->end(); ?>
    </section>
</div>
