<?php

require_once 'Attributes.php';
require_once 'Units.php';

class Product {

    public static $general_props = ['sku', 'name', 'price', 'productType'];
    public $prod_input;
    
    function __construct($input) {
        $this->prod_input = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    }
    
    public static function get_p_arr() {
        return Attributes::add_to_sql_arr($this->prod_input, self::$general_props);
    }
    
}

class Subtype extends Product {
    
    use Units;

    public static $subtype_props = ['Furniture' => ['height', 'length', 'width'], 'DVD' => ['size'], 'Book' => ['weight']];

    function __construct($query) {
        parent::__construct($query);
        $this->spec = ['DVD' => function($input_arr) {
                                    return 'Size: ' . $input_arr['size'] . ' MB';
                                 },
                        'Book' => function($input_arr) {
                                    return 'Weight: ' . $input_arr['weight'] . ' KG';
                                 },
                        'Furniture' => function($input_arr) {
                                    return 'Dimensions: ' . $input_arr['height'] . ' x ' . $input_arr['width'] . ' x ' . $input_arr['length'];
                                 },
                       ];
    }
    
    public static function get_p_arr() {
        $response_arr = parent::get_p_arr();
        $valid_props = isset($response_arr['productType']) ? self::$subtype_props[$response_arr['productType']] : [];
        
        return Attributes::add_to_sql_arr($this->prod_input, $valid_props, $response_arr);
    }
    
    public function set_units($arr) {
        $response_arr = [];
        
        for ($i = 0; $i < count($arr); $i++) {
            $specification = ($this->spec[$arr[$i]['productType']])($arr[$i]);
            $response_arr[] = array(
                'sku'               => $arr[$i]['sku'],
                'name'              => $arr[$i]['name'],
                'price'             => $arr[$i]['price'] . ' $',
                'specification'     => $specification
            );  
        }
        
        return $response_arr;
    }
    
    public function Value($val) {
        return trim($val);
    }
    
    public function noHTMLnoQuotes($string) {
        return htmlentities($this->Value($string), ENT_NOQUOTES);
    }
    
    public function noHTMLquotes($string) {
        return htmlentities($this->Value($string), ENT_QUOTES);
    }
    
    public function sql($connection,$val) {
        if(is_numeric($val)) {
            return $val;
        }
        return "'" . mysqli_real_escape_string($connection, htmlspecialchars($this->Value($val))) . "'";
    }
    
    public function sqlWithArray($connection, $array) {
        $res = array();
        
        foreach($array as $field=>$val) {
            $res[$field] = "'" . mysqli_real_escape_string($connection, htmlspecialchars($this->Value($val))) . "'";
        }
        return $res;
    }
    
}