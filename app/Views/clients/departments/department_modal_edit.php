<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<?php echo form_open_multipart(get_uri("departments/edit_department/".$depart_info->id), array("id" => "department-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body">
    <div class="container-fluid clearfix post-dropzone" id="new-ticket-dropzone1">
        <br />


        <div class="form-group">
            <div class="row">
                <label for="name" class=" col-md-3"><strong><?php echo app_lang('name'); ?></strong></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "name",
                        "name" => "name",
                        "required" => true,
                        "value" => $depart_info->name,
                        "class" => "form-control",
                        "placeholder" => app_lang('name'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="client_id" class=" col-md-3"><strong><?php echo app_lang('Manager'); ?></strong></label>
                <div class=" col-md-9">
                    <?php
                    $clients_dropdown = array();
                    if (isset($client_info) && !empty($client_info)) {
                        foreach ($client_info as $row) {
                            $clients_dropdown[$row->id] = $row->first_name . ' ' . $row->last_name;
                        }
                    }
                    echo form_dropdown("client_id", $clients_dropdown, $depart_info->client_id, "class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><strong><?php echo app_lang('description'); ?></strong></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "required" => true,
                        "name" => "description",
                        "value" => $depart_info->description,
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "style" => "height:150px;",
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">

                <label for="description" class=" col-md-3"><strong><?php echo 'Profile picture'; //app_lang('description'); 
                                                                    ?></strong><br>
                    <small>Enter a name or email</small></label>

                <div class=" col-md-9">
                    <?php echo view("includes/dropzone_preview"); ?>
                    <button class="btn btn-default upload-file-button float-start me-auto btn-sm round  <?php
                                                                                                        if (!$depart_info->id) {
                                                                                                            echo "hide";
                                                                                                        }
                                                                                                        ?>" type="button" style="color:#7988a2"><i data-feather='camera' class='icon-16'></i> <?php echo  app_lang("upload_file"); ?></button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">

                <label for="description" class=" col-md-3"><strong><?php echo 'Budget';?></strong></label>

                <div class=" col-md-9">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?php echo ($currency ? $currency : "$"); ?></span>
                        </div>
                        <?php
                        echo form_input(array(
                            "id" => "budget",
                            "name" => "budget",
                            "value" => $depart_info->budget,
                            "class" => "form-control",
                            "placeholder" => app_lang('Budget'),
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">

                <label for="ticket_type_id" class=" col-md-3"><strong><?php echo 'Add People' //app_lang('ticket_type'); 
                                                                        ?></strong></label>
                <div class="col-md-9">
                    <select id="ticket_type_id" name="people[]" class="select2 " required multiple>
                        <?php if (isset($client_info) && !empty($client_info)) {
                            foreach ($client_info as $row) {
                                if(in_array($row->id, $user_id)){
                                    echo '<option selected value="' . $row->id . '">' . $row->first_name . ' ' . $row->last_name . '</option>';

                                }else{
                                    echo '<option value="' . $row->id . '">' . $row->first_name . ' ' . $row->last_name . '</option>';

                                }
                            }
                        } ?>


                    </select>
                    <?php
                    //echo form_dropdown("ticket_type_id", $ticket_types_dropdown, $model_info->ticket_type_id, "class='select2'");
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><strong><?php echo  'Make Private' //app_lang('description'); 
                                                                    ?></strong><br>
                    <small>When a department is set to private, it can only
                        be viewed or joined by invitation</small>
                </label>

                <div class=" col-md-9">
                    <input type="checkbox" name="status" data-on="Public" data-off="Private" value="<?php if($depart_info->status==0){ echo '1';}else{ echo '1';} ?>" <?php if($depart_info->status==1){ echo 'checked';} ?> data-toggle="toggle">
                    <?php
                    // echo form_checkbox(array(
                    //     "id" => "description",
                    //     "name" => "description",
                    //     "value" => $model_info->description,
                    //     "class" => "form-control",
                    //     "placeholder" => app_lang('description'),
                    //     "style" => "height:150px;",
                    //     "data-rich-text-editor" => true
                    // ));
                    ?>
                </div>
            </div>
        </div>
        <br />
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('send'); ?></button>
</div>
<?php echo form_close(); ?>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $("#department-form .select2").select2();
        $("#department-form").validate({});

        var uploadUrl = "<?php echo get_uri("departments/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("departments/validate_department_file"); ?>";
        var editMode = "<?php echo $depart_info->id; ?>";

        var dropzone = attachDropzoneWithForm("#new-ticket-dropzone1", uploadUrl, validationUrl);

        // $("#department-form").appForm({
        //     onSuccess: function(result) {
        //         if (editMode) {

        //             appAlert.success(result.message, {
        //                 duration: 10000
        //             });

        //             //don't reload whole page when it's the list view
        //             if ($("#ticket-table").length) {
        //                 $("#ticket-table").appTable({
        //                     newData: result.data,
        //                     dataId: result.id
        //                 });
        //             } else {
        //                 location.reload();
        //             }
        //         } else {
        //             $("#ticket-table").appTable({
        //                 newData: result.data,
        //                 dataId: result.id
        //             });
        //         }

        //     }
        // });


    });
</script>