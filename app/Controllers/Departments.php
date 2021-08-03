<?php

namespace App\Controllers;

class Departments extends Security_Controller {

    protected $Departments_model;
    protected $Departments_user_model;

    
    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
        $this->Departments_model = model('App\Models\Departments_model');
        $this->Departments_user_model = model('App\Models\Departments_user_model');
    }

    /* load clients list view */

    function index($tab = "") {
        $this->access_only_allowed_members();

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['groups_dropdown'] = json_encode($this->_get_groups_dropdown_select2_data(true));
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown(true);

        $view_data['tab'] = $tab;

        return $this->template->rander("clients/index", $view_data);
    }
    // use function 
    function all_departments()
    {
        if (!get_setting("client_can_edit_departments")  || !get_setting("client_can_create_departments")  || !get_setting("client_can_view_departments")) {
            app_redirect("forbidden");
        }
        $view_data['client_id'] = $this->login_user->id;
        return $this->template->rander("clients/departments/lists", $view_data);
    }
    /* list of contacts, prepared for datatable  */

    function department_list_data($client_id = 0)
    {
        if (!get_setting("client_can_view_departments")) {
            app_redirect("forbidden");
        }
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array("user_type" => "client", "client_id" => $client_id, "custom_fields" => $custom_fields, "show_own_clients_only_user_id" => $this->show_own_clients_only_user_id());
        $list_data = $this->Departments_model->get_details($options)->getResult();
        $result = array();
        $hide_primary_contact_label = false;
        if (!$client_id) {
            $hide_primary_contact_label = true;
        }
        foreach ($list_data as $data) {
            $result[] = $this->_make_department_row($data, $custom_fields, $hide_primary_contact_label);
        }
        echo json_encode(array("data" => $result));
    }
    private function _make_department_row($data, $custom_fields, $hide_primary_contact_label = false)
    {
        $html = '';

        $image_url = get_avatar_department($data->dimage);
        $image_url2 = get_avatar($data->image);
        $department_image = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $c_name = "";
        if (get_setting("client_can_view_departments")) {
            $c_name = get_uri('departments/view/'  . $data->slug);
        } else {
            $c_name = "#";
        }
        $departmentProfile = $c_name;
        $department_name = "<a href='$departmentProfile'>$data->name</a>";
        $full_name = "<span class='avatar avatar-xs'><img src='$image_url2' alt='" . $data->first_name . "' style='margin-right: 7px;'>" . $data->first_name . " </span>";
        $private = "";
        if ($data->dstatus == 1) {
            $private = "<span class='bg-success badge'>Open</span>";
        } else {
            $private = "<span class='bg-danger badge'>Close</span>";
        }
        // $users_info = $this->Departments_model->get_one_records($data->id)->getResult();
        $users_info = $this->Departments_model->get_one_records($data->dept_id);

        $countProject = $this->Departments_model->get_count_project($data->dept_id);
        $countEvent = $this->Departments_model->get_count_event($data->dept_id);
        // if(isset($users_info) && !empty($users_info)){
        // foreach($users_info as $row){
        // $user_image_url = get_avatar($row->image);

        // $html = '<div class="mt5"><span title="Jeremie Mouithsone"><span class="avatar avatar-xs mr10"><img src="'. $user_image_url. '" alt="' . $row->first_name . " " . $row->last_name . '"></span></span></div>';
        // }
        // }


        $contact_info = $data->phone;
        $optoins = "";
        // if (get_setting("client_can_edit_departments")) {
        $optoins .= modal_anchor(get_uri("departments/department_modal_edit"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => 'Edit department', "data-post-id" => $data->dept_id));
        // }

        // if (get_setting("client_can_edit_departments")) {
        $optoins .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_project'), "class" => "delete", "data-id" => $data->dept_id, "data-action-url" => get_uri("departments/delete"), "data-action" => "delete-confirmation"));
        // }

        //$action= '<a href="#" class="edit" title="Edit task" data-post-id="1" data-act="ajax-modal" data-title="Edit task" data-action-url="http://localhost/app-project/projects/task_modal_form"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit icon-16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
        // $primary_contact = "";
        // if ($data->is_primary_contact == "1" && !$hide_primary_contact_label) {
        // $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        // }

        // $removal_request_pending = "";
        // if ($this->login_user->user_type == "staff" && $data->requested_account_removal) {
        // $removal_request_pending = "<span class='bg-danger badge'>" . app_lang("removal_request_pending") . "</span>";
        // }

        // $contact_link = anchor(get_uri("clients/contact_profile/" . $data->id), $full_name . $primary_contact) . $removal_request_pending;
        // if ($this->login_user->user_type === "client") {
        // $contact_link = $full_name; //don't show clickable link to client
        // }

        // $client_info = $this->Clients_model->get_one($data->client_id);

        $row_data = array(
            $department_image,
            // $contact_link,
            // anchor(get_uri("clients/view/" . $data->client_id), $client_info->company_name),
            $department_name,
            $users_info,
            $private,
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle icon"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>',
            $countProject,
            modal_anchor(get_uri("team_members/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default", "title" => app_lang('send_invitation'))),
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar icon"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> <span class="bg-success badge">' . $countEvent . '</span>',
            $data->budget,
            $full_name,
            $optoins,

        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }



        return $row_data;
    }
        /* open department_modal modal */

    function department_modal() {

        if (!get_setting("client_can_create_departments")) {
            app_redirect("forbidden");
        }
        $this->validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));
        $depart_id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');
        $view_data["client_info"] = $this->Users_model->get_all()->getResult();
        return $this->template->view('clients/departments/department_modal', $view_data);
    }
    function department_modal_edit() {

        if (!get_setting("client_can_edit_departments")) {
            app_redirect("forbidden");
        }
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));
        $depart_id = $this->request->getPost('id');
        $view_data["depart_info"] = $this->Departments_model->get_one($depart_id);
        $where = array('department_id'=>$view_data["depart_info"]->id);
        $view_data["users_info"] = $this->Departments_user_model->get_all_where($where)->getResult();
        $view_data['user_id'] = array_column($view_data["users_info"], 'user_id');
        $view_data["client_info"] = $this->Users_model->get_all()->getResult();
        return $this->template->view('clients/departments/department_modal_edit', $view_data);
    }

    //save_department
    function save_department() {

        if (!get_setting("client_can_create_departments")) {
            app_redirect("forbidden");
        }

        $client_id = $this->login_user->id; //user id 
        if($this->request->getPost('client_id')) {
            $client_id = $this->request->getPost('client_id');
        }
        $name = trim($this->request->getPost('name'));
        $slug = (str_replace(' ', '-', strtolower($name)));
        $description = trim($this->request->getPost('description'));
        $people = $this->request->getPost('people');
        $status = trim($this->request->getPost('status'));

        $this->validate_submitted_data(array(
            "name" => "required|trim",
            "description" => "required|trim",
            
        ));
        $target_path = get_setting("deparment_files_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "department");
        $verification_data = array(
            "name" => $name,
            "description"=>$description,
            'status'=> $status,
            'client_id'=>$client_id,
            'image'=> $files_data,
            'slug'=>$slug 
        );
        $save_id = $this->Departments_model->ci_save($verification_data);
        if ($save_id) {
            if(isset($people) && !empty($people)){
                foreach($people as $key=>$value){
                    $people_data = array(
                        "department_id" => $save_id,
                        "user_id" => $people[$key],

                    );
                    $this->Departments_user_model->ci_save($people_data);
                }
            }
            app_redirect('departments/all_departments');
           // echo json_encode(array('success' => true, 'message' => app_lang("invitation_sent")));
        } else {
            app_redirect('departments/all_departments');
           // echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }
    function edit_department($department_id) {
        
        if (!get_setting("client_can_edit_departments")) {
            app_redirect("forbidden");
        }
        $client_id = $this->login_user->id;
        if($this->request->getPost('client_id')) {
            $client_id = $this->request->getPost('client_id');
        }
        $name = trim($this->request->getPost('name'));
        $description = trim($this->request->getPost('description'));
        $people = $this->request->getPost('people');
        $status = trim($this->request->getPost('status'));
        $budget = trim($this->request->getPost('budget'));
        // echo '<pre>';
        // print_r($_POST);
        // die;
        $this->validate_submitted_data(array(
            "name" => "required|trim",
            "description" => "required|trim",
            
        ));
        $target_path = get_setting("deparment_files_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "department");
        if(unserialize($files_data) && unserialize($files_data)[0]['file_name'] !=''){
            $verification_data = array(
                "name" => $name,
                "description"=>$description,
                'status'=> $status,
                'budget'=> $budget,
                'client_id'=>$client_id,
                'image'=> $files_data
            );
        }else{
            $verification_data = array(
                "name" => $name,
                "description"=>$description,
                'status'=> $status,
                'budget'=> $budget,
                'client_id'=>$client_id,
                //'image'=> $files_data
               
            );
        }
        $save_id = $this->Departments_model->ci_save($verification_data, $department_id);
        
        if ($save_id) {
            $this->Departments_user_model->delete_department_users($department_id);

            if(isset($people) && !empty($people)){
                foreach($people as $key=>$value){
                    $people_data = array(
                        "department_id" => $department_id,
                        "user_id" => $people[$key],
                    );
                    $this->Departments_user_model->ci_save($people_data);
                }
            }
            app_redirect('departments/all_departments');
           // echo json_encode(array('success' => true, 'message' => app_lang("invitation_sent")));
        } else {
            app_redirect('departments/all_departments');
           // echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    function view($dept, $tab = "")
    {
        if (!get_setting("client_can_view_departments")) {
            app_redirect("forbidden");
        }
        // $client_id = 1;
        $client_id = $this->login_user->id;
        $dept_id = $this->Departments_model->get_slug_dept_id($dept);
        $view_data['user_info'] = $this->Users_model->get_one($client_id);
        $view_data['dept_info'] = $this->Departments_model->get_one($dept_id);
        $view_data['tab'] = $tab;
        $view_data['show_cotact_info'] = true;
        $view_data['show_social_links'] = true;
        return $this->template->rander("clients/departments/view", $view_data);
    }

    /* only visible to client  */

    /* return a row of contact list table */
    //  end use function 
    //for team members, check only read_only permission here, since other permission will be checked accordingly
    private function can_edit_clients() {
        if ($this->login_user->user_type == "staff" && get_array_value($this->login_user->permissions, "client") === "read_only") {
            return false;
        }

        return true;
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_view_files")) {
                app_redirect("forbidden");
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_add_files")) {
                app_redirect("forbidden");
            }
        }
    }

    private function can_access_this_client($client_id = 0) {
        $client_info = $this->Clients_model->get_one($client_id);

        if ($this->login_user->user_type === "staff" && $client_info->id && get_array_value($this->login_user->permissions, "client") === "own" && !($client_info->created_by == $this->login_user->id || $client_info->owner_id == $this->login_user->id)) {
            app_redirect("forbidden");
        }
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('id');
        $this->can_access_this_client($client_id);
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loading from the client's details view
        $view_data["ticket_id"] = $this->request->getPost('ticket_id'); //needed only when loading from the ticket's details view and created by unknown client
        $view_data['model_info'] = $this->Clients_model->get_one($client_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();


        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('clients/modal_form', $view_data);
    }

    function delete() {
       
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        
        // $this->can_access_this_client($id);
        if ($this->Departments_model->delete_department_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

   
    
    
    

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id)->getResult();
        return $this->template->view('clients/star/clients_list', $view_data);
    }

    /* load projects tab  */

    function projects($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = $client_id;
        return $this->template->view("clients/projects/index", $view_data);
    }

    /* load payments tab  */

    function payments($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            return $this->template->view("clients/payments/index", $view_data);
        }
    }

    /* load tickets tab  */

    function tickets($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {

            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tickets", $this->login_user->is_admin, $this->login_user->user_type);

            $view_data['show_project_reference'] = get_setting('project_reference_in_tickets');

            return $this->template->view("clients/tickets/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $view_data["can_edit_invoices"] = $this->can_edit_invoices();

            return $this->template->view("clients/invoices/index", $view_data);
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("estimates", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("clients/estimates/estimates", $view_data);
        }
    }

    /* load orders tab  */

    function orders($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("clients/orders/orders", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            return $this->template->view("clients/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            return $this->template->view("clients/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("client");
            $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event") . " " . strtolower(app_lang("label"))));
            return $this->template->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id, $view_type = "") {
        $this->can_view_files();
        $this->can_access_this_client($client_id);

        if ($this->login_user->user_type == "client") {
            $client_id = $this->login_user->client_id;
        }

        $view_data['client_id'] = $client_id;
        $view_data['page_view'] = false;

        if ($view_type == "page_view") {
            $view_data['page_view'] = true;
            return $this->template->rander("clients/files/index", $view_data);
        } else {
            return $this->template->view("clients/files/index", $view_data);
        }
    }

    /* file upload modal */

    function file_modal_form() {
        $this->can_add_files();

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;
        $this->can_access_this_client($client_id);

        $view_data['client_id'] = $client_id;
        return $this->template->view('clients/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {
        $this->can_add_files();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($client_id);

        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "client_id" => $client_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->ci_save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->can_view_files();
        $this->can_access_this_client($client_id);

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        if ($data->uploaded_by_user_type == "staff") {
            $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);
        } else {
            $uploaded_by = get_client_contact_profile_link($data->uploaded_by, $uploaded_by);
        }

        $description = "<div class='float-start'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("clients/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("clients/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        if ($this->login_user->user_type == "staff") {
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete_file"), "data-action" => "delete-confirmation"));
        }


        return array($data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {
            $this->can_view_files();

            if (!$file_info->client_id) {
                app_redirect("forbidden");
            }

            $this->can_access_this_client($file_info->client_id);

            $view_data['can_comment_on_files'] = false;
            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("client", $file_info->client_id));

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            return $this->template->view("clients/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {
        $this->can_view_files();

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            app_redirect("forbidden");
        }

        $this->can_access_this_client($file_info->client_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
      upload_file_to_temp();
        
        
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id || ($this->login_user->user_type == "client" && $info->uploaded_by !== $this->login_user->id)) {
            app_redirect("forbidden");
        }

        $this->can_access_this_client($info->client_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

   

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $this->can_access_this_client($view_data['user_info']->client_id);
        return $this->template->view("users/account_settings", $view_data);
    }

    //show my preference settings of a team member
    function my_preferences() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);

        //language dropdown
        $view_data['language_dropdown'] = array();
        if (!get_setting("disable_language_selector_for_clients")) {
            $view_data['language_dropdown'] = get_language_list();
        }

        $view_data["hidden_topbar_menus_dropdown"] = $this->get_hidden_topbar_menus_dropdown();

        return $this->template->view("clients/contacts/my_preferences", $view_data);
    }

    function save_my_preferences() {
        //setting preferences
        $settings = array("notification_sound_volume", "disable_push_notification", "disable_keyboard_shortcuts");

        if (!get_setting("disable_language_selector_for_clients")) {
            array_push($settings, "personal_language");
        }

        if (!get_setting("disable_topbar_menu_customization")) {
            array_push($settings, "hidden_topbar_menus");
        }

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting("user_" . $this->login_user->id . "_" . $setting, $value, "user");
        }

        //there was 2 settings in users table.
        //so, update the users table also


        $user_data = array(
            "enable_web_notification" => $this->request->getPost("enable_web_notification"),
            "enable_email_notification" => $this->request->getPost("enable_email_notification"),
        );

        $user_data = clean_data($user_data);

        $this->Users_model->ci_save($user_data, $this->login_user->id);

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_personal_language($language) {
        if (!get_setting("disable_language_selector_for_clients") && ($language || $language === "0")) {

            $language = clean_data($language);

            $this->Settings_model->save_setting("user_" . $this->login_user->id . "_personal_language", strtolower($language), "user");
        }
    }

    /* load contacts tab  */

    function contacts($client_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data["client_id"] = $client_id;
            $view_data["view_type"] = "";
        } else {
            $view_data["client_id"] = "";
            $view_data["view_type"] = "list_view";
        }
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['can_edit_clients'] = $this->can_edit_clients();

        return $this->template->view("clients/contacts/index", $view_data);
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($view_data['model_info']->client_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('clients/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->can_access_this_client($view_data['model_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_clients'] = $this->can_edit_clients();
            return $this->template->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members_or_client_contact($client_id);
            $this->can_access_this_client($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_clients'] = $this->can_edit_clients();

            $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

            return $this->template->view('clients/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $contact_info = $this->Users_model->get_one($contact_id);
            $this->can_access_this_client($contact_info->client_id);

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $view_data['can_edit_clients'] = $this->can_edit_clients();
            return $this->template->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->request->getPost('contact_id');
        $client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($client_id);
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $user_data = array(
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "phone" => $this->request->getPost('phone'),
            "skype" => $this->request->getPost('skype'),
            "job_title" => $this->request->getPost('job_title'),
            "gender" => is_null($this->request->getPost('gender')) ? "" : $this->request->getPost('gender'),
            "note" => $this->request->getPost('note')
        );

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric"
        ));


        if (!$contact_id) {
            //inserting new contact. client_id is required

            $this->validate_submitted_data(array(
                "email" => "required|valid_email",
            ));

            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["email"] = trim($this->request->getPost('email'));
            $user_data["password"] = password_hash($this->request->getPost("login_password"), PASSWORD_DEFAULT);
            $user_data["created_at"] = get_current_utc_time();

            //validate duplicate email address
            if ($this->Users_model->is_email_exists($user_data["email"])) {
                echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
                exit();
            }
        }

        //by default, the first contact of a client is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->request->getPost('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Users_model->ci_save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("client_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->ci_save($user_data, $primary_contact);
            }

            //send login details to user only for first time. when creating  a new contact
            if (!$contact_id && $this->request->getPost('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info");

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('login_password');
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                send_app_mail($this->request->getPost('email'), $email_template->subject, $message);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $contact_info = $this->Users_model->get_one($contact_id);
        $this->can_access_this_client($contact_info->client_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->request->getPost('facebook'),
            "twitter" => $this->request->getPost('twitter'),
            "linkedin" => $this->request->getPost('linkedin'),
            "googleplus" => $this->request->getPost('googleplus'),
            "digg" => $this->request->getPost('digg'),
            "youtube" => $this->request->getPost('youtube'),
            "pinterest" => $this->request->getPost('pinterest'),
            "instagram" => $this->request->getPost('instagram'),
            "github" => $this->request->getPost('github'),
            "tumblr" => $this->request->getPost('tumblr'),
            "vine" => $this->request->getPost('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->ci_save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
    }

    //save account settings of a client contact (user)
    function save_account_settings($user_id) {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }
        $this->access_only_allowed_members_or_contact_personally($user_id);

        $contact_info = $this->Users_model->get_one($user_id);
        $this->can_access_this_client($contact_info->client_id);

        $this->validate_submitted_data(array(
            "email" => "required|valid_email"
        ));

        if ($this->Users_model->is_email_exists($this->request->getPost('email'), $user_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $this->request->getPost('email')
        );

        //don't reset password if user doesn't entered any password
        if ($this->request->getPost('password')) {
            $account_data['password'] = password_hash($this->request->getPost("password"), PASSWORD_DEFAULT);
        }

        //only admin can disable other users login permission
        if ($this->login_user->is_admin) {
            $account_data['disable_login'] = $this->request->getPost('disable_login');
        }


        if ($this->Users_model->ci_save($account_data, $user_id)) {

            //resend new password to client contact
            if ($this->request->getPost('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info");

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $this->request->getPost('first_name');
                $parser_data["USER_LAST_NAME"] = $this->request->getPost('last_name');
                $parser_data["USER_LOGIN_EMAIL"] = $account_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('password');
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                send_app_mail($this->request->getPost('email'), $email_template->subject, $message);
            }

            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_department_image($user_id = 0,$department_id) {
        // if (!$this->can_edit_clients()) {
        //     app_redirect("forbidden");
        // }
     
        

        //$this->access_only_allowed_members_or_contact_personally($user_id);
        $user_info = $this->Users_model->get_one($user_id);
        $depart_info = $this->Departments_user_model->get_one($department_id);
       // $this->can_access_this_client($user_info->client_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->request->getPost("profile_image"));
        $profile_image=serialize($profile_image);
   

        if ($profile_image) {
           
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("deparment_files_path"), "", $profile_image));

          
            //delete old file
            delete_app_files(get_setting("deparment_files_path"), array(@unserialize($depart_info->image)));
            
            $image_data = array("image" => $profile_image);
            $this->Departments_model->ci_save($image_data, $department_id);
            echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "deparment_files_path");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => app_lang('profile_image_error_message')));
                    exit();
                }

                $profile_image = serialize(move_temp_file("avatar.png", get_setting("deparment_files_path"), "", $image_file_name));

                //delete old file
                delete_app_files(get_setting("deparment_files_path"), array(@unserialize($depart_info->image)));

                $image_data = array("image" => $profile_image);
                $this->Departments_model->ci_save($image_data, $department_id);
                echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->request->getPost('id');

        $contact_info = $this->Users_model->get_one($id);
        $this->can_access_this_client($contact_info->client_id);

        if ($this->request->getPost('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->getRow();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */
function department_view_modal() {

        if ( !get_setting("client_can_view_departments") &&  !$this->login_user->user_type == "client") {
            app_redirect("forbidden");
        }

        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "depart_id" => "required|numeric"
        ));

        $depart_id = $this->request->getPost('depart_id');
        $view_data["depart_info"] = $this->Departments_model->get_one($depart_id);
        return $this->template->view('clients/departments/department_view_modal', $view_data);
    }
    private function _make_contact_row($data, $custom_fields, $hide_primary_contact_label = false) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1" && !$hide_primary_contact_label) {
            $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        }

        $removal_request_pending = "";
        if ($this->login_user->user_type == "staff" && $data->requested_account_removal) {
            $removal_request_pending = "<span class='bg-danger badge'>" . app_lang("removal_request_pending") . "</span>";
        }

        $contact_link = anchor(get_uri("clients/contact_profile/" . $data->id), $full_name . $primary_contact) . $removal_request_pending;
        if ($this->login_user->user_type === "client") {
            $contact_link = $full_name; //don't show clickable link to client
        }

        $client_info = $this->Clients_model->get_one($data->client_id);

        $row_data = array(
            $user_avatar,
            $contact_link,
            anchor(get_uri("clients/view/" . $data->client_id), $client_info->company_name),
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("clients/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }
    





    /* show keyboard shortcut modal form */

    function keyboard_shortcut_modal_form() {
        return $this->template->view('team_members/keyboard_shortcut_modal_form');
    }

    function upload_excel_file() {
        upload_file_to_temp(true);
    }

    function import_clients_modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        return $this->template->view("clients/import_clients_modal_form");
    }

    private function _prepare_client_data($data_row, $allowed_headers) {
        //prepare client data
        $client_data = array();
        $client_contact_data = array("user_type" => "client", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($data_row as $row_data_key => $row_data_value) { //row values
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if (strpos($header_key_value, 'cf') !== false) { //custom field
                $explode_header_key_value = explode("-", $header_key_value);
                $custom_field_id = get_array_value($explode_header_key_value, 1);
                $custom_field_values_array[$custom_field_id] = $row_data_value;
            } else if ($header_key_value == "client_groups") { //we've to make client groups data differently
                $client_data["group_ids"] = $this->_get_client_group_ids($row_data_value);
            } else if ($header_key_value == "contact_first_name") {
                $client_contact_data["first_name"] = $row_data_value;
            } else if ($header_key_value == "contact_last_name") {
                $client_contact_data["last_name"] = $row_data_value;
            } else if ($header_key_value == "contact_email") {
                $client_contact_data["email"] = $row_data_value;
            } else {
                $client_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "client_data" => $client_data,
            "client_contact_data" => $client_contact_data,
            "custom_field_values_array" => $custom_field_values_array
        );
    }

    private function _get_existing_custom_field_id($title = "") {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "clients"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers) {
        foreach ($headers_row as $key => $header) {
            if (!((count($headers) - 1) < $key)) { //skip default headers
                continue;
            }

            //so, it's a custom field
            //check if there is any custom field existing with the title
            //add id like cf-3
            $existing_id = $this->_get_existing_custom_field_id($header);
            if ($existing_id) {
                array_push($headers, "cf-$existing_id");
            }
        }

        return $headers;
    }

    function save_client_from_excel_file() {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        if (!$this->validate_import_clients_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new \SpreadsheetReader($temp_file_path . $file_name);
        $allowed_headers = $this->_get_allowed_headers();
        $now = get_current_utc_time();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, modify this for custom fields and continue for the next loop
                $allowed_headers = $this->_prepare_headers_for_submit($value, $allowed_headers);
                continue;
            }

            $client_data_array = $this->_prepare_client_data($value, $allowed_headers);
            $client_data = get_array_value($client_data_array, "client_data");
            $client_contact_data = get_array_value($client_data_array, "client_contact_data");
            $custom_field_values_array = get_array_value($client_data_array, "custom_field_values_array");

            //couldn't prepare valid data
            if (!($client_data && count($client_data))) {
                continue;
            }

            //found information about client, add some additional info
            $client_data["created_date"] = $now;
            $client_data["created_by"] = $this->login_user->id;
            $client_contact_data["created_at"] = $now;

            //save client data
            $client_save_id = $this->Clients_model->ci_save($client_data);
            if (!$client_save_id) {
                continue;
            }

            //save custom fields
            $this->_save_custom_fields_of_client($client_save_id, $custom_field_values_array);

            //add client id to contact data
            $client_contact_data["client_id"] = $client_save_id;
            $this->Users_model->ci_save($client_contact_data);
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _save_custom_fields_of_client($client_id, $custom_field_values_array) {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "clients",
                "related_to_id" => $client_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->ci_save($field_value_data);
        }
    }

    private function _get_client_group_ids($client_groups_data) {
        $explode_client_groups = explode(", ", $client_groups_data);
        if (!($explode_client_groups && count($explode_client_groups))) {
            return false;
        }

        $groups_ids = "";

        foreach ($explode_client_groups as $group) {
            $group_id = "";
            $existing_group = $this->Client_groups_model->get_one_where(array("title" => $group, "deleted" => 0));
            if ($existing_group->id) {
                //client group exists, add the group id
                $group_id = $existing_group->id;
            } else {
                //client group doesn't exists, create a new one and add group id
                $group_data = array("title" => $group);
                $group_id = $this->Client_groups_model->ci_save($group_data);
            }

            //add the group id to group ids
            if ($groups_ids) {
                $groups_ids .= ",";
            }
            $groups_ids .= $group_id;
        }

        if ($groups_ids) {
            return $groups_ids;
        }
    }

    private function _get_allowed_headers() {
        return array(
            "company_name",
            "contact_first_name",
            "contact_last_name",
            "contact_email",
            "address",
            "city",
            "state",
            "zip",
            "country",
            "phone",
            "website",
            "vat_number",
            "client_groups",
            "currency",
            "currency_symbol"
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            $key_value = str_replace(' ', '_', strtolower(trim($header, " ")));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //the rest headers will be treated as custom fields
                //pushed header at last of this loop
            } else if (((count($allowed_headers) - 1) < $key) && $key_value) {
                //custom fields headers
                //check if there is any existing custom field with this title
                if (!$this->_get_existing_custom_field_id(trim($header, " "))) {
                    $header_array["has_error"] = true;
                    $header_array["custom_field"] = true;
                }
            } else { //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }

    function validate_import_clients_file() {
        $this->access_only_allowed_members();

        $file_name = $this->request->getPost("file_name");
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if ($file_ext == "xlsx") {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_a_excel_file') . " (.xlsx)"));
        }
    }

    function validate_import_clients_file_data($check_on_submit = false) {
        $this->access_only_allowed_members();

        $table_data = "";
        $error_message = "";
        $headers = array();
        $got_error_header = false; //we've to check the valid headers first, and a single header at a time
        $got_error_table_data = false;

        $file_name = $this->request->getPost("file_name");

        require_once(APPPATH . "ThirdParty/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new \SpreadsheetReader($temp_file_path . $file_name);

        $table_data .= '<table class="table table-responsive table-bordered table-hover" style="width: 100%; color: #444;">';

        $table_data_header_array = array();
        $table_data_body_array = array();

        foreach ($excel_file as $row_key => $value) {
            if ($row_key == 0) { //validate headers
                $headers = $this->_store_headers_position($value);

                foreach ($headers as $row_data) {
                    $has_error_class = false;
                    if (get_array_value($row_data, "has_error") && !$got_error_header) {
                        $has_error_class = true;
                        $got_error_header = true;

                        if (get_array_value($row_data, "custom_field")) {
                            $error_message = app_lang("no_such_custom_field_found");
                        } else {
                            $error_message = sprintf(app_lang("import_client_error_header"), app_lang(get_array_value($row_data, "key_value")));
                        }
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                if (!array_filter($value)) {
                    continue;
                }

                $error_message_on_this_row = "<ol class='pl15'>";
                $has_contact_first_name = get_array_value($value, 1) ? true : false;

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data, $has_contact_first_name);
                        if ($row_data_validation) {
                            $has_error_class = true;
                            $error_message_on_this_row .= "<li>" . $row_data_validation . "</li>";
                            $got_error_table_data = true;
                        }
                    }

                    if ($row_data === "0" || $row_data === 0 || $row_data || $has_error_class) {
                        $table_data_body_array[$row_key][] = array("has_error_class" => $has_error_class, "value" => $row_data);
                    }
                }

                $error_message_on_this_row .= "</ol>";

                //error messages for this row
                if ($got_error_table_data) {
                    $table_data_body_array[$row_key][] = array("has_error_text" => true, "value" => $error_message_on_this_row);
                }
            }
        }

        //return false if any error found on submitting file
        if ($check_on_submit) {
            return ($got_error_header || $got_error_table_data) ? false : true;
        }

        //add error header if there is any error in table body
        if ($got_error_table_data) {
            array_push($table_data_header_array, array("has_error_text" => true, "value" => app_lang("error")));
        }

        //add headers to table
        $table_data .= "<tr>";
        foreach ($table_data_header_array as $table_data_header) {
            $error_class = get_array_value($table_data_header, "has_error_class") ? "error" : "";
            $error_text = get_array_value($table_data_header, "has_error_text") ? "text-danger" : "";
            $value = get_array_value($table_data_header, "value");
            $table_data .= "<th class='$error_class $error_text'>" . $value . "</th>";
        }
        $table_data .= "<tr>";

        //add body data to table
        foreach ($table_data_body_array as $table_data_body_row) {
            $table_data .= "<tr>";

            foreach ($table_data_body_row as $table_data_body_row_data) {
                $error_class = get_array_value($table_data_body_row_data, "has_error_class") ? "error" : "";
                $error_text = get_array_value($table_data_body_row_data, "has_error_text") ? "text-danger" : "";
                $value = get_array_value($table_data_body_row_data, "value");
                $table_data .= "<td class='$error_class $error_text'>" . $value . "</td>";
            }

            $table_data .= "<tr>";
        }

        //add error message for header
        if ($error_message) {
            $total_columns = count($table_data_header_array);
            $table_data .= "<tr><td class='text-danger' colspan='$total_columns'><i data-feather='alert-triangle' class='icon-16'></i> " . $error_message . "</td></tr>";
        }

        $table_data .= "</table>";

        echo json_encode(array("success" => true, 'table_data' => $table_data, 'got_error' => ($got_error_header || $got_error_table_data) ? true : false));
    }

    private function _row_data_validation_and_get_error_message($key, $data, $has_contact_first_name) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //company name field is required
        if ($header_value == "company_name" && !$data) {
            return app_lang("import_client_error_company_name_field_required");
        }

        //if there is contact first name then the contact last name and email is required
        //the email should be unique then
        if ($has_contact_first_name) {
            if ($header_value == "contact_last_name" && !$data) {
                return app_lang("import_client_error_contact_name");
            }

            if ($header_value == "contact_email") {
                if ($data) {
                    if ($this->Users_model->is_email_exists($data)) {
                        return app_lang("duplicate_email");
                    }
                } else {
                    return app_lang("import_client_error_contact_email");
                }
            }
        }
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-clients-sample.xlsx"))));
    }

    function gdpr() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);
        return $this->template->view("clients/contacts/gdpr", $view_data);
    }

    function export_my_data() {
        if (get_setting("enable_gdpr") && get_setting("allow_clients_to_export_their_data")) {
            $user_info = $this->Users_model->get_one($this->login_user->id);

            $txt_file_name = $user_info->first_name . " " . $user_info->last_name . ".txt";

            $data = $this->_make_export_data($user_info);

            $handle = fopen($txt_file_name, "w");
            fwrite($handle, $data);
            fclose($handle);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($txt_file_name));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($txt_file_name));
            readfile($txt_file_name);

            //delete local file
            if (file_exists($txt_file_name)) {
                unlink($txt_file_name);
            }

            exit;
        }
    }

    private function _make_export_data($user_info) {
        $required_general_info_array = array("first_name", "last_name", "email", "job_title", "phone", "gender", "skype", "created_at");

        $data = strtoupper(app_lang("general_info")) . "\n";

        //add general info
        foreach ($required_general_info_array as $field) {
            if ($user_info->$field) {
                if ($field == "created_at") {
                    $data .= app_lang("created") . ": " . format_to_datetime($user_info->$field) . "\n";
                } else if ($field == "gender") {
                    $data .= app_lang($field) . ": " . ucfirst($user_info->$field) . "\n";
                } else if ($field == "skype") {
                    $data .= "Skype: " . ucfirst($user_info->$field) . "\n";
                } else {
                    $data .= app_lang($field) . ": " . $user_info->$field . "\n";
                }
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("client_info")) . "\n";

        //add company info
        $client_info = $this->Clients_model->get_one($user_info->client_id);
        $required_client_info_array = array("company_name", "address", "city", "state", "zip", "country", "phone", "website", "vat_number");
        foreach ($required_client_info_array as $field) {
            if ($client_info->$field) {
                $data .= app_lang($field) . ": " . $client_info->$field . "\n";
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("social_links")) . "\n";

        //add social links
        $social_links = $this->Social_links_model->get_one($user_info->id);

        unset($social_links->id);
        unset($social_links->user_id);
        unset($social_links->deleted);

        foreach ($social_links as $key => $value) {
            if ($value) {
                if ($key == "googleplus") {
                    $data .= "Google plus: " . $value . "\n";
                } else {
                    $data .= ucfirst($key) . ": " . $value . "\n";
                }
            }
        }

        return $data;
    }

   

    /* check valid file for ticket */

    function validate_department_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    function get_feed($dpt_id) {
        $view_data['team_members'] = "";
        $view_data['department_id'] = $dpt_id;
        $this->init_permission_checker("message_permission");
        if (get_array_value($this->login_user->permissions, "message_permission") !== "no") {
            
            $dpt_users = $this->Departments_user_model->get_all_where(array('department_id'=>$dpt_id))->getResult();
            $users = array();
            foreach($dpt_users as $row) {
                $users[] = $row->user_id;
            }
            $view_data['team_members'] = $this->Messages_model->get_users_for_messaging(
                array(
                    'login_user_id'=>$this->login_user->id,
                    'specific_members'=>$users

                )
            )->getResult();
        }
        return $this->template->view("timeline/index", $view_data);
    }

    function get_todo($dpt_id) {
        $view_data["client_info"] = $this->Departments_model->get_all()->getResult();
        $view_data['department_id'] = $dpt_id;
        return $this->template->view("todo/index", $view_data);
    }

    function get_people($dpt_id) {
        $view_data['client_id'] = $this->login_user->client_id;
        $view_data['department'] = get_department($dpt_id);
        return $this->template->view("clients/contacts/users", $view_data);
    }

    function get_project($dpt_id, $status = '') {
        $view_data['project_labels_dropdown'] = json_encode($this->make_labels_dropdown("project", "", true));

        $view_data["can_create_projects"] = $this->can_create_projects();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data["status"] = $status;

        $view_data['client_id'] = $this->login_user->client_id;
        $view_data['page_type'] = "full";
        $view_data["can_create_projects"] = get_department($dpt_id)->client_id == $this->login_user->id;
        $view_data['department_id'] = $dpt_id;
        return $this->template->view("clients/projects/index", $view_data);
    }

    function get_meeting($dpt_id, $encrypted_event_id = "") {
        $view_data['encrypted_event_id'] = $encrypted_event_id;
        $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown();
        $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event") . " " . strtolower(app_lang("label"))));
        $view_data['department_id'] = $dpt_id;
        return $this->template->view("events/index", $view_data);
    }

}

/* End of file clients.php */
/* Location: ./app/controllers/clients.php */