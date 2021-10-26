<?php

require_once(dirname(__DIR__).'/product/create_product.php');

class MySQL extends Subtype {

    public $connection;
    public $error = array();
    
    public $TABLE = "Some table";

    protected $host, $user, $password, $database;

    public function __construct($query) {
        parent::__construct($query);

            $this->host         = "Your host";
            $this->user         = "Your user ID";
            $this->password     = "Your password";
            $this->database     = "Your database";

            $this->MySQLConnect();

    }

    public function __destruct() {
        $this->host         = NULL;
        $this->user         = NULL;
        $this->password     = NULL;
        $this->database     = NULL;
        // Close connection if it is open
        if ($this->connection) {
        mysqli_close($this->connection);
        }
    }

    public function MySQLConnect(){
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        if(!$this->connection){
            $e = 'Server error: Failed to connect to DB';
            $this->setError($e);

            return false;
        }
        return $this->connection;
    }

    protected function setError($error) {
        array_push($this->error, $error);
    }
    
    public function error() {
        return $this->error[count($this->error)-1];
    }

    protected function Query($query){
        
        if($this->CheckConnection() === false){
            return false;
        }
        $execute            = mysqli_query($this->connection, $query);
        if(!$execute) {
            $e              = 'MySQL query error: ' . mysqli_error($this->connection);
            $this->setError($e);
        }
        return $execute;
    }

    protected function CheckConnection(){
        if(!$this->connection) {
            $e = 'DB connection failed';
            $this->setError($e);
            
            return false;
        }
        return true;
    }

    public function Execute($query){
        if($this->CheckConnection() === false){
            return false;
        }
        $return  = array();
        $execute = $this->Query($query);
        if($execute === false) {
            $e = 'MySQL query error: ' . mysqli_error($this->connection);
            $this->setError($e);
            
            return false;
        }
        if(!is_bool($execute)) {
            while($row = mysqli_fetch_array($execute)) {
                $return[] = $row;
            }
        }
        return $return;
    }
    
    public function makeSelection($table, $condition = "", $clause = " AND ") {
        $query = "SELECT * FROM " . $table;
        if(!empty($condition)){
            $query .= $this->where($condition, $clause);
        }
        $result = $this->Execute($query);
        // Return the resulting array with applicable units appended to the values if query is successful
        return $result ? $this->set_units($result) : $result;
    }

    public function makeInsertion($table, array $rows) {
        if ($this->isValid($rows)) {
            $rows       = $this->sqlWithArray($this->connection, $rows);
            $keys       = "(".implode(array_keys($rows)," ,").")";
            $values     = " VALUES (".implode(array_values($rows),", ").")";
            $query      = "INSERT INTO $table $keys $values";
            return $this->Execute($query);
        } else {
            $e = 'Invalid input';
            $this->setError($e);
            return;
        }
        
    }

    public function makeDeletion($table, $condition, $clause = " OR ", $overwritten_field = "") {
        if ($this->isValid($condition)) {
            $query = "DELETE FROM " . $table . $this->where($condition, $clause, $overwritten_field);
            return $this->Execute($query);
        } else {
            $e = 'Invalid input';
            $this->setError($e);
            return;
        }
    }

    protected function where($condition, $clause, $overwritten_field) {
        $query     = " WHERE ";
        if(is_array($condition)) {
            
            $size  = count($condition);
            $index = 1;
            foreach ($condition as $field => $val) {
                $field  = $overwritten_field or $field;
                $query .= "{$field}=" . $this->sql($this->connection, $val);
                if ($index < $size) {
                    $query .= "{$clause}";
                }
                $index++;
            }

        } else if(is_string($condition)) {
            $query .= $condition;
        } else {
            $query = "";
        }
        return $query;
    }

    public function isValid($input) {
        if(is_array($input) && count($input) > 0) {
            return true;
        }
        return false;
    }
}

?>
