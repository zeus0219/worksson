<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<?php echo form_open_multipart(get_uri("departments/save_department"), array("id" => "department-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix post-dropzone" id="new-ticket-dropzone">
    <div class="container-fluid">
        <br />


        <div class="form-group">
            <div class="row">
                <label for="name" class=" col-md-3"><strong><?php echo app_lang('description'); ?></strong></label>
                <div class=" col-md-9">
                    <p><?= $depart_info->description;  ?></p>
                </div>
            </div>
        </div>
        
        <br />
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>
<?php echo form_close(); ?>
