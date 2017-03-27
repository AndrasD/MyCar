<?php

class DbHandler {
    
    private $conn;
    
    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
    }

    /**
    * Fetching single record
    */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();
    }

    /**
    * Fetching all record
    */
    public function getAllRecord($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        return $r;
    }


    /**
    * Creating new record
    */
    public function insertIntoTable($obj, $column_names, $table_name) {
        
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
            if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        
        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
        } else {
            return NULL;
        }
    }

    /**
    * Update a record
    */
    public function updateIntoTable($obj, $column_names, $table_name, $column_where) {
        
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        $$where = $c[$column_where];

        $query = "UPDATE ".$table_name." SET ";
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
            if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $query = $query."'".$desired_key."' = '".$$desired_key."',";
        }
        $query = trim($query,',')." WHERE '".$column_where."' = ".$$where;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        
        if ($r) {
            return $$where;
        } else {
            return $query;
        }
    }

    public function getSession(){
        if (!isset($_SESSION)) {
            session_start();
        }
        $sess = array();
        if(isset($_SESSION['id']))
        {
            $sess["id"] = $_SESSION['id'];
            $sess["name"] = $_SESSION['name'];
            $sess["email"] = $_SESSION['email'];
            $sess["admin"] = $_SESSION['admin'];
        }
        else
        {
            $sess["id"] = '';
            $sess["name"] = 'Guest';
            $sess["email"] = '';
            $sess["admin"] = '';
        }
        return $sess;
    }

    public function destroySession(){
        if (!isset($_SESSION)) {
            session_start();
        }
        if(isSet($_SESSION['id']))
        {
            unset($_SESSION['id']);
            unset($_SESSION['name']);
            unset($_SESSION['email']);
            unset($_SESSION['admin']);
            $info='info';
            if(isSet($_COOKIE[$info]))
            {
                setcookie ($info, '', time() - $cookie_time);
            }
            $msg="Logged Out Successfully...";
        }
        else
        {
            $msg = "Not logged in...";
        }
        return $msg;
    }
    
}

?>