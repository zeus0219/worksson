<div class="box" id="profile-image-section">
    <div class="box-content w200 text-center profile-image">
        <?php
        $url = "departments";

        //set url
        // if ($user_info->user_type === "client") {
        //     $url = "clients";
        // } else if ($user_info->user_type === "lead") {
        //     $url = "leads";
        // }
        echo form_open(get_uri($url . "/save_department_image/" . $user_info->id . '/' . $dept_info->id), array("id" => "profile-image-form", "class" => "general-form", "role" => "form"));
        ?>

        <div class="file-upload btn mt0 p0 profile-image-upload" data-bs-toggle="tooltip" title="<?php echo app_lang("upload_and_crop"); ?>" data-placement="right">
            <span class="btn color-white"><i data-feather="camera" class="icon-16"></i></span>
            <input id="profile_image_file" class="upload" name="profile_image_file" type="file" data-height="200" data-width="200" data-preview-container="#profile-image-preview" data-input-field="#profile_image" />
        </div>
        <div class="file-upload btn p0 profile-image-upload profile-image-direct-upload" data-bs-toggle="tooltip" title="<?php echo app_lang("upload"); ?> (200x200 px)" data-placement="right">
            <?php
            echo form_upload(array(
                "id" => "profile_image_file_upload",
                "name" => "profile_image_file",
                "class" => "no-outline hidden-input-file upload"
            ));
            ?>
            <label for="profile_image_file_upload" class="clickable">
                <span class="btn color-white ml2"><i data-feather="upload" class="icon-16"></i></span>
            </label>
        </div>
        <input type="hidden" id="profile_image" name="profile_image" value="" />

        <span class="avatar avatar-lg"><img id="profile-image-preview" src="<?php echo get_avatar_department($dept_info->image); ?>" alt="..."></span>

        <?php echo form_close(); ?>
    </div>


    <div class="box-content pl15">
        <div class="col-md-12">
            <h2 class="p10 m0"><strong> <?php echo $dept_info->name; ?> </strong></h2>
            <p class="p10 m0"><?php echo substr($dept_info->description, 0, 200); ?> </p>
            <?php if(isset($department) && $department):?>
                <div class="custom-btn-group">
                    <?php echo form_open(get_uri("todo/save"), array("id" => "todo-inline-form", "class" => "fade-btn hide tab-company-info", "role" => "form")); ?>
                    <input type="hidden" name="department" value="<?php echo $department->id?>">
                    <div class="todo-input-box" style="margin:0px">
                        <div class="input-group">
                            <?php
                            echo form_input(array(
                                "id" => "todo-title",
                                "name" => "title",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => app_lang('add_a_todo'),
                                "autocomplete" => "off",
                                "autofocus" => true
                            ));
                            ?>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-secondary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                            </span>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    <?php
                        if (!get_setting("disable_user_invitation_option_by_clients")) {
                            echo modal_anchor(get_uri("clients/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default fade-btn hide tab-social-links", "title" => app_lang('send_invitation'), "data-post-client_id" => $login_user->client_id));
                        }
                        if ($department->client_id == $login_user->id) {
                            echo modal_anchor(get_uri("projects/modal_form/".$department->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-default fade-btn hide tab-account-settings", "data-post-client_id" => $login_user->client_id, "title" => app_lang('add_project')));
                        }
        
                        echo modal_anchor(get_uri("events/modal_form/".$department->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_event'), array("class" => "btn btn-default fade-btn hide tab-my-preferences add-btn", "title" => app_lang('add_event'), "data-post-client_id" => $login_user->id));
                    ?>            
                </div>
            <?php endif;?>
            <?php if(strlen($dept_info->description) > 200){ ?>
            <p class="p10 m0">
                <?php echo modal_anchor(get_uri("departments/department_view_modal"), 'See more...', array("class" => "text-white text-right", "title" => 'See more...', "data-post-depart_id" => $dept_info->id)); ?>
            </p>
            <?php }?>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        //modify design for mobile devices
        if (isMobile()) {
            $("#profile-image-section").children("div").each(function() {
                $(this).addClass("p0");
                $(this).removeClass("box-content");
            });
        }

        $('[data-bs-toggle="tooltip"]').tooltip();
        $("#client-contact-tabs").find('li>a').click(function(){
            var id = $(this).data('bs-target');
            $('.fade-btn.active').addClass('hide').removeClass('active');
            $('.'+id.substr(1)).removeClass('hide').addClass('active');
        })
    });
</script>