<?php
namespace App;
    
abstract class Product {
    
    protected $product;
    public $sql_str;
    public $sql_arr;
    
    public function __construct($product) {
        $this->product = $product;
        $this->sql_arr = [];
        $this->sql_str = '';
    }

    public function set_sql_str($string) {
        $this->sql_str = $string;
    }
    
    public function get_sql_str() {
        return $this->sql_str;
    }
    
    public function fill_props_fromP($init_table) {
        foreach ($this->product as $Pprop => $Pvalue) {
            $init_table[$Pprop] = htmlspecialchars($Pvalue);
        }
        return $init_table;
    }
    
    abstract public function add_to_sql_arr();
    
    public function get_sql_arr() {
        return $this->sql_arr;
    }    
};