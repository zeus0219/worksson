<?php

namespace App\Models;

class Departments_user_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'department_user';
        parent::__construct($this->table);
    }
    function delete_department_users($project_id) {
        
        $db      = \Config\Database::connect();
        $department_user_table = $db->table('department_user');
        $department_user_table->where('department_id', $project_id);
        $department_user_table->delete();
       
       
        return true;
       

       
    }
    

}
