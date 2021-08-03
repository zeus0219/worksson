<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('Departments'); ?></h1>
            <div class="title-button-group">
                <?php
                // if (get_setting("client_can_create_departments")) {
                        echo modal_anchor(get_uri("departments/department_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('add_department'), array("class" => "btn btn-default", "title" => app_lang('add_department'), "data-post-client_id" => $client_id));
                // }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="contact-table" class="display" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#contact-table").appTable({
            source: '<?php echo_uri("departments/department_list_data/" . $client_id) ?>',
            order: [[1, "asc"]],
            columns: [
                {title: '<?php echo  app_lang("DI") ?>', "class": "w50 text-center"},
                {title: "<?php echo app_lang("name") ?>"},
                {title: "<?php echo  app_lang("People") ?>", "class": "w10p"},
                {title: "<?php echo  app_lang("Status") ?>", "class": "w5p"},
                {title: "<?php echo  app_lang("Dialogue") ?>", "class": "w5p"},
                {title: "<?php echo  app_lang("Project") ?>", "class": "w5p"},
                {title: "<?php echo  app_lang("Contact_Info") ?>", "class": "w10p"},
                {title: "<?php echo  app_lang("Events") ?>", "class": "w5p"},
                {title: "<?php echo  app_lang("Budget") ?>", "class": "w5p"},
                {title: "<?php echo  app_lang("Manager") ?>", "class": "w15p"},
                {title: "<?php echo  app_lang("Action") ?>", "class": "w15p"},
                // {visible: false, searchable: false},
               
            ],
            printColumns: [0, 1, 2, 3, 4, 5,6,7,8,9],
            xlsColumns: [0, 1, 2, 3, 4, 5,6,7,8,9]

        });
    });
</script>