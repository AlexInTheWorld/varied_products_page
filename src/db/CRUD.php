<?php

require_once(__DIR__ . '/MySQL.php');

class Crud extends MySQL {
  
    public function __construct($query) {
        parent::__construct($query);
    }
  
    public function insert() {
        $execute = $this->makeInsertion($this->TABLE, Subtype::get_p_arr());
        if ($this->error) { return ['error'=> $this->error()]; };
        return $execute;
    }
  
    public function select() {
        $select = $this->makeSelection($this->TABLE);
        if ($this->error) { return ['error'=> $this->error()]; };
        return $select;
    }
    
    public function delete_selected() {
        $execute = $this->makeDeletion($this->TABLE, $this->prod_input, " OR ", "sku");
        if ($this->error) { return ['error'=> $this->error()]; };
        return $execute;
    }
}

?>