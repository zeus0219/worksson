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
        <div class="col-md-6">
            <h2 class="p10 m0"><strong> <?php echo $dept_info->name; ?> </strong></h2>
            <p class="p10 m0"><?php echo substr($dept_info->description, 0, 200); ?> </p>
            <?php if(strlen($dept_info->description) > 200){ ?>
            <p class="p10 m0"><?php echo modal_anchor(get_uri("departments/department_view_modal"), 'See more...', array("class" => "text-white text-right", "title" => 'See more...', "data-post-depart_id" => $dept_info->id)); ?></p>
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
    });
</script>