<?php

 require '../../src/app_classes.php'; // Handle product input
 require '../../db_conn/db_conn.php'; // Manage DB logic


use App\Product as P;

class uProduct extends P {

    public $convTable;
    // Set a conversion table to upload the values as specified by DB columns. DB contains one table with the following fields: SKU, name, price, sp_attr_value
    public function setConversionTable() {
        
        $initTable = $this->fill_props_fromP(array('sku' => '', 'name' => '', 'price' => '', 'size' => '', 'weight' => '', 'height' => '', 'length' => '', 'width' => ''));
        
        $this->convTable = [
            
            'sku' =>
                ['value' => $initTable['sku'], 'dbCol' => 'SKU', 'dbValue' => $initTable['sku']],
            
            'name' => 
                ['value' => $initTable['name'], 'dbCol' => 'name', 'dbValue' => $initTable['name']],
            
            'price' => 
                ['value' => $initTable['price'], 'dbCol' => 'price', 'dbValue' => $initTable['price'] . ' $'],
            
            'size' => 
                ['value' => $initTable['size'], 'dbCol' => 'sp_attr_value', 'dbValue' => 'Size: ' . $initTable['size'] . ' MB'],
            
            'weight' =>
                ['value' => $initTable['weight'], 'dbCol' => 'sp_attr_value', 'dbValue' => 'Weight: ' . $initTable['weight'] . ' KG'],
            
            'dimensions' => 
                ['value' => $initTable['height'] . $initTable['width'] . $initTable['length'], 'dbCol' => 'sp_attr_value', 'dbValue' => 'Dimensions: ' . $initTable['height'] . ' x ' . $initTable['width'] . ' x ' . $initTable['length']]
            
                            ];
    }
    
    public function getConversionTable() {
        return $this->convTable;
    }
    // Prepare an sql array to be executed for DB insertion
    public function add_to_sql_arr() {
        $current_convTable = $this->getConversionTable();
        
        if ($current_convTable) {
            foreach ($this->convTable as $prop_name => $prop_params) {
                if ($prop_params['value']){
                    $this->sql_arr[':' . $prop_params['dbCol']] = $prop_params['dbValue'];
                }
            }
        } else {
            $this->setConversionTable();
            $this->add_to_sql_arr();
        }
    }
    
}

// Instantiate user inputted product
$newProduct = new uProduct($_POST);
// Set props and values to be inserted in DB
$newProduct->setConversionTable();
$newProduct->add_to_sql_arr();
// Set sql statement
$newProduct->set_sql_str('INSERT INTO products (SKU, name, price, sp_attr_value) ' . 'VALUES(:SKU, :name, :price, :sp_attr_value)');

// Prepare and insert into DB:
// Set connection
$newInsertion = new Insertion((new ConnToDB)->connect());
// Insert
$newInsertion->insert($newProduct->get_sql_str(), $newProduct->get_sql_arr());

echo 'success';