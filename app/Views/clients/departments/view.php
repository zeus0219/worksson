<?php echo view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="bg-dark-success clearfix">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="p20 row">
                        <?php echo view("clients/departments/profile_image_section"); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <ul id="client-contact-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs b-b rounded-0" role="tablist">
        <li><a role="presentation" href="<?php echo_uri("departments/get_feed/".$dept_info->id); ?>" data-bs-target="#tab-general-info"> <?php echo 'Work Feed' //app_lang('general_info'); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("departments/get_todo/".$dept_info->id ); ?>" data-bs-target="#tab-company-info"> <?php echo 'Todo'; //app_lang('company'); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("departments/get_people/".$dept_info->id); ?>" data-bs-target="#tab-social-links"> <?php echo 'People'; //app_lang('social_links'); ?></a></li>
        <li><a role="presentation" href="<?php echo_uri("departments/get_project/".$dept_info->id); ?>" data-bs-target="#tab-account-settings"> <?php echo 'Projects'; //app_lang('account_settings'); ?></a></li>
       
        <li><a role="presentation" href="<?php echo_uri("departments/get_meeting/".$dept_info->id); ?>" data-bs-target="#tab-my-preferences"> <?php echo 'Events'; //app_lang('my_preferences'); ?></a></li>        
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="tab-general-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-company-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-social-links"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-account-settings"></div>
        <div role="tabpanel" class="tab-pane fade" id="tab-my-preferences"></div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".upload").change(function() {
            if (typeof FileReader == 'function' && !$(this).hasClass("hidden-input-file")) {
                showCropBox(this);
            } else {
                $("#profile-image-form").submit();
            }
        });
        $("#profile_image").change(function() {
            $("#profile-image-form").submit();
        });


        $("#profile-image-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function(data) {
                $.each(data, function(index, obj) {
                    if (obj.name === "profile_image") {
                        var profile_image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = profile_image;
                    }
                });
            },
            onSuccess: function(result) {
                if (typeof FileReader == 'function' && !result.reload_page) {
                    appAlert.success(result.message, {
                        duration: 10000
                    });
                } else {
                    location.reload();
                }
            }
        });

        setTimeout(function() {
            var tab = "<?php echo $tab; ?>";
            console.log(tab)
            if (tab === "general") {
                $("[data-bs-target='#tab-general-info']").trigger("click");
            } else if (tab === "company") {
                $("[data-bs-target='#tab-company-info']").trigger("click");
            } else if (tab === "account") {
                $("[data-bs-target='#tab-account-settings']").trigger("click");
            } else if (tab === "social") {
                $("[data-bs-target='#tab-social-links']").trigger("click");
            } else if (tab === "my_preferences") {
                $("[data-bs-target='#tab-my-preferences']").trigger("click");
            } else if (tab === "left_menu") {
                $("[data-bs-target='#tab-user-left-menu']").trigger("click");
            }
        }, 210);

    });
</script>