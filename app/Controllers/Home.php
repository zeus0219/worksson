<?php

namespace App\Controllers;

class Home extends App_Controller {

    private $signin_validation_errors;

    function __construct() {
        parent::__construct();
    }

    function index() {
        if ($this->Users_model->login_user_id()) {
            app_redirect('dashboard/view');
        } else {
            $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }

            return $this->template->view('home/index', $view_data);
        }
    }

}
