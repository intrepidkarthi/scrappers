<?php
/*
mydala.com Deals Scrapper
Written for bangaloreHack2013
*/



/**
 * Database config variables
 */
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "karthi");
define("DB_DATABASE", "banghack");

ini_set('max_execution_time', -1);

//Connect to DB
$connectdb = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Unable to connect to mysql");
$selectdb = mysql_select_db(DB_DATABASE,$connectdb) or die("unable to pick database");



$city_all_deals=array();
$city_multiple=array();
$get_url='http://www.mydala.com/mall/bangalore';

//Get HTML content from the URL
$source=file_get_contents($get_url);


//Parsing the DOM
$dom_parse=new DOMDocument();
@$dom_parse->loadHTML($source);
$dom_xpath=new DOMXpath($dom_parse);

$pattern='/mydala/';

$results=$dom_xpath->query("//div[@class='deal-showcase']/a[@class='deal-showcase-image-wrapper']");

if(!is_null($results))
{
    $cnt=0;
    foreach($results as $val)
    {
        $subject=$val->getAttribute('href');
        if(!preg_match($pattern, $subject))
        {
            $subject=getFrameurl($subject);
        }
        $city_all_deals[$cnt]=$subject;
        $cnt++;
    }
}
//print_r($city_all_deals);

$cnt=0;
foreach($city_all_deals as $val)
{
    if($val!="")
    {
        $paginate=1;
        $proces=1;
        do
        {
            $val_page=$val.'/page/'.$paginate;
            $source=file_get_contents($val_page);
            @$dom_parse->loadHTML($source);
            $dom_xpath=new DOMXpath($dom_parse);

            $results=$dom_xpath->query("//div[@class='deal-showcase ']/a[@class='deal-showcase-image-wrapper']");


            if(!is_null($results))
            {
                foreach($results as $vul)
                {
                    $city_multiple[$cnt]=$vul->getAttribute('href');
                    $cnt++;
                }
            }


            $end_pagination1=$dom_xpath->query("//div[@class='pagination fltRight suffix-8']"); // when there is no more than one page

            if($end_pagination1->length>0)
            {
                $end_pagination2=$dom_xpath->query("//div[@class='pagination fltRight suffix-8']/span[@class='disabled']"); // when there is more than one page
                $pattern2='/^next/';
                if($end_pagination2->length>0)
                {
                    $subject2=$end_pagination2->item(0)->nodeValue;
                    if(preg_match($pattern2, $subject2))
                    {
                        $proces=0;
                    }
                }
            }
            else
            {
                $proces=0;
            }

            $paginate++;
        }
        while($proces==1);

    }
}

// fetch content from individual links
$dealsrc='mydala';
$dealcity='bangalore';
foreach($city_multiple as $val)
{
    if($val!="")
    {
        $source=file_get_contents($val);
        @$dom_parse->loadHTML($source);
        $dom_xpath=new DOMXpath($dom_parse);
        $dealname=$dealdesc=$sp=$cp=$disc=$cat=$sub_cat=$image_url="";
        $results=$dom_xpath->query("//div[@class='big-two-right-column fltRight pull-10']/div/div");
        if($results->length>0)
        {
            $dealname=$results->item(0)->getElementsByTagName("h1")->item(0)->nodeValue;
            $dealdesc=$results->item(0)->getElementsByTagName("h2")->item(0)->nodeValue;
            if($results->length>1)
            {
                $sp=$results->item(1)->getElementsByTagName("div")->item(3)->lastChild->nodeValue;
                $cp=$results->item(2)->getElementsByTagName("div")->item(0)->lastChild->nodeValue;
                $disc=$results->item(2)->getElementsByTagName("div")->item(3)->lastChild->nodeValue;
                $save=$results->item(2)->getElementsByTagName("div")->item(6)->lastChild->nodeValue;
            }
        }
       
        $results2=$dom_xpath->query("//div[@class='page-nav clearLeft']//a");
        if($results2->length>0)
        {
            $cat=$results2->item(1)->nodeValue;
            $sub_cat=$results2->item(2)->nodeValue;
        }
       
        $results3=$dom_xpath->query("//div[@class='big-two-left-column fltLeft']/div/ul/li/img");
        if($results2->length>0)
        {
            $image_url=$results3->item(0)->getAttribute('src');
        }
         $query="insert into deals(deal_name,deal_desc,deal_url, image_url,category, city,costprice,saleprice,saveprice,discount) values('".$dealname."','".$dealdesc."','".$val."','".$image_url."','".$cat."','".$dealcity."','".$cp."','".$sp."','".$save."','".$disc."')";
         mysql_query($query,$connectdb);
    }
}


function getFrameurl($get_url)
{
$src=@file_get_contents($get_url);
$out="";
$dm_parse=new DOMDocument();
@$dm_parse->loadHTML($src);
$dm_xpath=new DOMXpath($dm_parse);
$res=$dm_xpath->query("//frame[@name='TopFrame']");
foreach($res as $val)
{
    $out=$val->getAttribute('src');
}

return $out;

}

?>