<?php

class Attributes {
    // Extract desired key=>value pairs from a provided source array (namely $POST) into a response array
    public static function add_to_sql_arr($source_arr, $sql_props, $response_arr=[]) {
        for ($i = 0; $i < count($sql_props); $i++) {
            if (isset($source_arr[$sql_props[$i]])) {
                $response_arr[$sql_props[$i]] = $source_arr[$sql_props[$i]];
            } else {
                $response_arr = [];
                break;
            }
        }
        return $response_arr;
    }
    
}