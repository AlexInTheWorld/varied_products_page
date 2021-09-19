<?php
require '../src/app_classes.php';
require '../db_conn/db_conn.php';

use App\Product as P;

class ProductQuery extends P {
    
    public function add_to_sql_arr() {
        $this->sql_arr = array('sku' => 'SKU', 'name' => 'name', 'price' => 'price', 'specification' => 'sp_attr_value');
    }
    
}
// Set quuery parameters
$products = new ProductQuery([]);
$products->add_to_sql_arr();
$products->set_sql_str('SELECT SKU, name, price, sp_attr_value FROM products');

// Set connection
$conn = (new ConnToDB())->connect();

// Initialize a new Query obj with the set connection
$allProducts = new Query($conn);
// Pass query params from products to query obj, and save result in as an array
$products_arr = $allProducts->getProducts($products->get_sql_str(), $products->get_sql_arr());
// Close connection
$conn = NULL;

// Send data as a json obj
echo json_encode($products_arr); 