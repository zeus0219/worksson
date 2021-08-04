<?php

namespace App\Models;

class Departments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'departments';
        parent::__construct($this->table);
    }
    function get_details($options = array()) {
       
        $where = "";
        $users_table = $this->db->prefixTable('users');
        $departments_table = $this->db->prefixTable('departments');
        $departmentuser_table = $this->db->prefixTable('department_user');
        
        
        
       
        $id = get_array_value($options, "id");
        $name = get_array_value($options, "name");
        $description = get_array_value($options, "description");
        $image = get_array_value($options, "image");
        $client_id = get_array_value($options, "client_id");
        $status = get_array_value($options, "status");
        $where_in = get_array_value($options, "where_in");
        if ($client_id) {
            $where .= " AND $departments_table.client_id=$client_id";
        }
        if($where_in) {
            foreach($where_in as $k=>$v){
                if(is_array($v)) {
                    $v = implode(',' ,$v);
                }
                if(!$v)
                {
                    $v = "''";
                }
                $where .= " AND $departments_table.$k in ($v)";
            }
        }
        //prepare full query string
        $sql = "SELECT *,$departments_table.id as dept_id,$departments_table.status as dstatus,$departments_table.image as dimage,$departments_table.client_id as dclient_id
        FROM $departments_table
        INNER JOIN $users_table ON $users_table.id=$departments_table.client_id
        
        WHERE $departments_table.deleted=0 $where
        ORDER BY $departments_table.id";
        
        return $this->db->query($sql);
    }
    public function get_one_records($department_id)
    {
        $html='';
        $sql = "SELECT *
        FROM department_user
        LEFT JOIN users ON users.id=department_user.user_id
        WHERE department_user.deleted=0 and users.deleted=0 and users.status ='active' and department_user.department_id=$department_id
        ORDER BY users.id";
        
        $users_info= $this->db->query($sql)->getResult();
       
        if (isset($users_info) && !empty($users_info)) {
            $html = '<div class="mt5" style="display: flex;" >';
            $counter=0;
            foreach ($users_info as $row) {
                $user_image_url = get_avatar($row->image);
                if($counter <=3){
                    $html .= '<span title="' . $row->first_name . " " . $row->last_name . '"><span class="avatar avatar-xs mr10"><img src="' . $user_image_url . '" alt="' . $row->first_name . " " . $row->last_name . '"></span></span>';

                }

           $counter++; }
            if ($counter > 3) {
                $html .= "<span class='bg-success badge'>" . $counter . "</span>";
            }
           
            $html  .='</div>';
        }
        return $html;


    }
    function delete_department_and_sub_items($project_id) {
        
        $db      = \Config\Database::connect();
        $departments_table = $db->table('departments');

       
        
        $departments_table->where('id', $project_id);
        $departments_table->delete();

        $department_user_table = $db->table('department_user');

        $department_user_table->where('department_id', $project_id);
        $department_user_table->delete();
       
        // $departments_table = $this->db->prefixTable('departments');
        // $department_user_table = $this->db->prefixTable('department_user');
        // $milestones_table = $this->db->prefixTable('milestones');
        // $project_files_table = $this->db->prefixTable('project_files');
        // $project_comments_table = $this->db->prefixTable('project_comments');
        // $activity_logs_table = $this->db->prefixTable('activity_logs');
        // $notifications_table = $this->db->prefixTable('notifications');

        //get project files info to delete the files from directory 

        // $project_files_sql = "SELECT * FROM $departments_table WHERE $departments_table.deleted=0 AND $departments_table.id=$project_id; ";
        // $project_files = $this->db->query($project_files_sql)->getResult();

        //get project comments info to delete the files from directory 
        // $project_comments_sql = "SELECT * FROM $department_user_table WHERE $department_user_table.deleted=0 AND $department_user_table.department_id=$project_id; ";
        // $project_comments = $this->db->query($project_comments_sql)->getResult();

        //delete the project and sub items
        // $delete_department_sql = "UPDATE $departments_table SET $departments_table.deleted=1 WHERE $departments_table.id=$project_id; ";
        // $this->db->query($delete_project_sql);

        // $delete_departmentuser_sql = "UPDATE $department_user_table SET $department_user_table.deleted=1 WHERE $department_user_table.department_id=$project_id; ";
        // $this->db->query($delete_tasks_sql);
        
        // $delete_milestones_sql = "UPDATE $milestones_table SET $milestones_table.deleted=1 WHERE $milestones_table.project_id=$project_id; ";
        // $this->db->query($delete_milestones_sql);

        // $delete_files_sql = "UPDATE $project_files_table SET $project_files_table.deleted=1 WHERE $project_files_table.project_id=$project_id; ";
        // $this->db->query($delete_files_sql);

        // $delete_comments_sql = "UPDATE $project_comments_table SET $project_comments_table.deleted=1 WHERE $project_comments_table.project_id=$project_id; ";
        // $this->db->query($delete_comments_sql);

        // $delete_activity_logs_sql = "UPDATE $activity_logs_table SET $activity_logs_table.deleted=1 WHERE $activity_logs_table.log_for='project' AND $activity_logs_table.log_for_id=$project_id; ";
        // $this->db->query($delete_activity_logs_sql);

        // $delete_notifications_sql = "UPDATE $notifications_table SET $notifications_table.deleted=1 WHERE $notifications_table.project_id=$project_id; ";
        // $this->db->query($delete_notifications_sql);


        //delete the comment files from directory
        // $comment_file_path = get_setting("deparment_files_path");
        // foreach ($project_comments as $comment_info) {
        //     if ($comment_info->files && $comment_info->files != "a:0:{}") {
        //         $files = unserialize($comment_info->files);
        //         foreach ($files as $file) {
        //             delete_app_files($comment_file_path, array($file));
        //         }
        //     }
        // }



        //delete the project files from directory
        // $file_path = get_setting("deparment_files_path");
        // print_r($file_path);
        // foreach ($project_files as $file) {
        //     delete_app_files($file_path, array(make_array_of_file($file)));
        // }
        return true;
        // echo '12';
        // die;

       
    }
    public function get_count_project($dept_id)
    {

        $total = 0;
        $db      = \Config\Database::connect();
        $department_user_table = $db->table('projects');
        $department_user_table->where('department_id', $dept_id);
        $total =  $department_user_table->countAllResults();

        return $total;
    }


    public function get_slug_dept_id($dept)
    {

      
        $db      = \Config\Database::connect();
        $department_user_table = $db->table('departments');
        $department_user_table->select('id'); 
        $department_user_table->where('slug', $dept);
        $total =  $department_user_table->get();
        $id= 0 ;
        foreach ($total->getResult() as $row)
        {
            $id = $row->id;
            
        }

        return $id;
    }


    public function get_count_event($dept_id)
    {

        $db      = \Config\Database::connect();
        $department_user_table = $db->table('events');
        $department_user_table->where('department_id', $dept_id);
        $total =  $department_user_table->countAllResults();

        return $total;
    }
    

}
