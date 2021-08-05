<?php

namespace App\Models;

class Posts_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'posts';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $posts_table = $this->db->prefixTable('posts');
        $users_table = $this->db->prefixTable('users');
        $where = "";

        $limit = get_array_value($options, "limit");
        $limit = $limit ? $limit : "20";
        $offset = get_array_value($options, "offset");
        $offset = $offset ? $offset : "0";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $posts_table.id=$id";
        }

        $where_in = get_array_value($options, "where_in");
        if($where_in) {
            foreach($where_in as $k=>$v){
                if(is_array($v)) {
                    $v = implode(',' ,$v);
                }
                if(!$v)
                {
                    $v = "''";
                }
                $where .= " AND $posts_table.$k in ($v)";
            }
        }

        //show the main posts in descending mode
        //but show the replies in ascedning mode
        $sort = " DESC";
        $post_id = get_array_value($options, "post_id");
        if ($post_id) {
            $where .= " AND $posts_table.post_id=$post_id";
            $sort = "ASC";
        } else {
            $where .= " AND $posts_table.post_id=0";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $posts_table.created_by=$user_id";
        }

        $user = new \stdClass();
        $user_model = model('App\Models\Users_model');
        $login_user_id = $user_model->login_user_id();
        if ($login_user_id) {
            //initialize login users required information
            $user = $user_model->get_access_info($login_user_id);

            //initialize login users access permissions
            if ($user->permissions) {
                $permissions = unserialize($user->permissions);
                $user->permissions = is_array($permissions) ? $permissions : array();
            } else {
                $user->permissions = array();
            }
        }
        if($user->user_type != 'staff') {
            $user_id = $user->id;
            $dpt_user_table = $this->db->prefixTable('department_user');
            $where .= " and $posts_table.created_by in (select user_id from $dpt_user_table where department_id in (select department_id from $dpt_user_table where user_id=$user_id))";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS $posts_table.*, $posts_table.id AS parent_post_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS created_by_user, $users_table.image as created_by_avatar,
            (SELECT COUNT($posts_table.id) as total_replies FROM $posts_table WHERE $posts_table.post_id=parent_post_id AND $posts_table.deleted=0) AS total_replies
        FROM $posts_table
        LEFT JOIN $users_table ON $users_table.id= $posts_table.created_by
        WHERE $posts_table.deleted=0 $where
        ORDER BY $posts_table.created_at $sort
        LIMIT $offset, $limit";
        $data = new \stdClass();
        $data->result = $this->db->query($sql)->getResult();
        $data->found_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow()->found_rows;
        return $data;
    }

    function count_new_posts() {
        $now = get_current_utc_time("Y-m-d");
        $posts_table = $this->db->prefixTable('posts');
        $sql = "SELECT COUNT($posts_table.id) AS total
        FROM $posts_table
        WHERE $posts_table.deleted=0 AND $posts_table.post_id=0 AND DATE($posts_table.created_at)='$now'";
        return $this->db->query($sql)->getRow()->total;
    }

}
