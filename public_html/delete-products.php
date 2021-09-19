<?php
require '../src/app_classes.php';
require '../db_conn/db_conn.php';

use App\Product as P;

class ProductsToBeDeleted extends P {
    
    public function add_to_sql_arr() {
        $this->sql_arr[0] = '';
        foreach ($this->product as $prop => $value) {
            $this->sql_arr[0] .= $value . ', ';
        }
        
        $this->sql_arr[0] = trim($this->sql_arr[0], ', ');
    }
    
}
// Set quuery parameters
$products = new ProductsToBeDeleted($_POST);
$products->add_to_sql_arr();
$products->set_sql_str('DELETE FROM products WHERE SKU IN (:manySKUs_str)');

// Set connection
$myDB = (new ConnToDB())->connect();

// Initialize a new Deletion obj with the set connection
$PsToDelete = new Deletion($myDB);
// Delete Selected through deleteSelected method passing appropriate params: sql statement and sql arr to place manySKUs_str into it
$PsToDelete->deleteSelected($products->get_sql_str(), $products->get_sql_arr());
// Close connection to DB
$myDB = NULL;
// Send success message
echo 'success';