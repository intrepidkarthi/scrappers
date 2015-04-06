<?php

/*
Tradus.com Data Scrapper
Written for bangaloreHack2013
*/

//Set Error reporting. Set to the maximum value
//error_reporting(3);

require('config.php');
include_once './db_functions.php';

//Source URL
$const_url = 'http://www.tradus.com/search/?query=';

//Setting max execution time
ini_set('max_execution_time', -1);

//Receive user input
$user_query = htmlspecialchars($_GET["query"]);


//Data Arrays
$productName = array();
$productLink = array();
$productImageUrl = array();
$productPrice = array();
$productDiscount = array();


//Get file content from URL
$get_url=$const_url.$user_query;
$source=file_get_contents($get_url);

//Load DOM parser
$dom_parse=new DOMDocument();
@$dom_parse->loadHTML($source);
$dom_xpath=new DOMXpath($dom_parse);

//Parse using class name
$results=$dom_xpath->query("//div[@class='product_image']");
$price=$dom_xpath->query("//div[@class='prod_price_3 search-product-block']");
//print_r($results);

if(!is_null($results))
{
	//Initialize counter
	$i = 0;
	//Looping through the results
    foreach($results as $val)
    {
    	$getDiv = $val->getElementsByTagName('a');
    	
    	//Storing data into array
    	$productName[$i] = $getDiv->item(0)->getElementsByTagName('img')->item(0)->getAttribute('title');
    	$productLink[$i] = 'http://www.tradus.com'.$getDiv->item(0)->getAttribute('href');
    	$productImageUrl[$i] = $getDiv->item(0)->getElementsByTagName('img')->item(0)->getAttribute('data-original');

        $i++;
    }
    
}

if(!is_null($price))
{
	//Initialize counter
	$j = 0;
	//Looping through the results
    foreach($price as $val2)
    {
    	$getDiv = $val2->getElementsByTagName('span');

    	//Storing data into array
    	$productPrice[$j] = $price->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
    	$$productDiscount[$j] = $price->item(0)->getElementsByTagName('span')->item(1)->nodeValue;

    	$j++;
    }
}


//Instantiate DB Object
$db = new DB_Functions();

//Insert the data
$length = count($productName);
for ($i = 0; $i < $length; $i++) {
	$db->storeData($productName[$i], $productLink[$i], $productImageUrl[$i], $productPrice[$i], $productDiscount[$i], $user_query, 1);
}






?>