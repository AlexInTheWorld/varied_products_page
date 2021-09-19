<?php
require(dirname(__DIR__) . '/vendor/autoload.php');

class Config {

    public static function DB_USER() {
        return $_ENV['DB_USER'];
    }
    
    public static function DB_PASSWORD() {
        return $_ENV['DB_PASSWORD'];
    }

}

// MySQL Connection

class ConnToDB {
    
    private $pdo;

    public function connect() {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
        
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=id17591655_mydb', Config::DB_USER(), Config::DB_PASSWORD());
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();
        }
        
        return $this->pdo;

    }
}

class Insertion {
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo; 
    }
    // Insert the specified product field values into the specified table
    public function insert($sql_str, $sql_arr) {
        $stmt = $this->pdo->prepare($sql_str);
        $stmt->execute($sql_arr);
    }
}

class Query {
        /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo; 
    }
    // query for all products in the table
    public function getProducts($sql_str, $sql_arr) {
        $products = [];
        $stmt = $this->pdo->query($sql_str);
   
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $products[] = [
                'sku' => $row[$sql_arr['sku']],
                'name' => $row[$sql_arr['name']],
                'price' => $row[$sql_arr['price']],
                'specification' => $row[$sql_arr['specification']]
            ];
        }

        // Return a products arr containing the four fields pertaining to each product
        return $products;
    }
}

class Deletion {
            /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo; 
    }
    // Delete selected products
    public function deleteSelected($sql_str, $sql_arr) {
        $stmt = $this->pdo->prepare($sql_str);
        $stmt->bindValue(':manySKUs_str', $sql_arr[0]);
        $stmt->execute();
    
        return $stmt->rowCount();    
    }
}