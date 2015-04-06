<?php

class DB_Functions {

    private $db;

  
    // constructor
    function __construct() {
        include_once './db_connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new product details
     * returns product details
     */
    public function storeData($pname, $plink, $pimagelink, $pprice, $pdiscount, $ptag, $merchant) {
        // insert user into database
        $result = mysql_query("INSERT INTO products(product_name, product_link, product_imageurl, product_price, product_discount, product_tags, merchant_id) VALUES('$pname', '$plink', '$pimagelink', '$pprice', '$pdiscount', '$ptag', '$merchant')");
        // check for successful store
        if ($result) {
            // get user details
            $id = mysql_insert_id(); // last inserted id
            $result = mysql_query("SELECT * FROM products WHERE id = $id") or die(mysql_error());
            // return user details
            if (mysql_num_rows($result) > 0) {
                return mysql_fetch_array($result);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get product by tag or keyword
     */
    public function getProductsByTag($tag) {
        $result = mysql_query("SELECT * FROM products WHERE product_tags LIKE '$tag' LIMIT 20");
        return $result;
    }

    /**
     * Getting all products of a keyword
     */
    public function getAllProducts($tag) {
         $result = mysql_query("SELECT * FROM products WHERE product_tags LIKE '$tag' LIMIT 20");
        return $result;
    }

    /**
     * Check product is existed or not
     */
    public function isProductsExisted($tag) {
        $result = mysql_query("SELECT product_id from products WHERE product_tags LIKE '$tag'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed
            return true;
        } else {
            // user not existed
            return false;
        }
    }

}

?>