<?php
require('simple_html_dom.php');

echo "dfsd";
$book_title=array();
$book_price=array();
$book_link=array();
$book_image=array();
$book_dis=array();
$book_aut=array();



$html = file_get_html('http://www.amazon.com/Best-Sellers-Kindle-Store/zgbs/digital-text/');

foreach($html->find('div[id=zg_critical], div[id=zg_nonCritical]') as $data){
    
    #for images of the book
    foreach($data->find('img') as $images){        
      $book_image[]=trim($images->src);
    }
    #for author
    foreach($data->find('.zg_byline') as $book_data){        
        foreach($book_data->find('a') as $val){        
          $book_aut[]=trim($val->innertext);
         
        }
    }

     #to get title of the book and it's link
    foreach($data->find('.zg_title') as $book_data){        
        foreach($book_data->find('a') as $val){        
          $book_title[]=trim($val->innertext);
          $book_link[]=trim($val->href);
        }
    }
    
    foreach($data->find('.zg_itemPriceBlock_compact') as $book_data){        
       
        foreach($book_data->find('.price') as $val){        
         $book_price[]=trim($val->innertext);         
        }
     }

}
$html ->clear();

 

foreach($book_link as $link_name){

//$link = file_get_html($link_name);

$htmls = scraperWiki::scrape($link_name);
$link = new simple_html_dom();
$link ->load($htmls);
foreach($link ->find('div[id=ps-content]') as $data){
    $book_disc='';
    #for images of the book
    if($data->find('p')){
        foreach($data->find('p') as $content){        
          $book_disc.=trim($content->innertext);       
        }
    }else{       
         
        foreach($data->find('div[id=postBodyPS]') as $content){        
          $book_disc.=trim($content->innertext);       
         }   
        $book_disc.=trim($content->innertext);      
    }
   
    $book_dis[]=$book_disc;
}

$link->clear();
}



$cnt=count($book_link);
echo "<table><tr><td>Book Title</td><td>Book Author</td><td>Book Price</td><td>Book Discription</td> <td>Book image Link</td></tr>";

for($ii=0;$ii<$cnt;$ii++){
    echo "<tr>";
    echo "<td>".$book_title[$ii]."</td>";
    echo "<td>".$book_aut[$ii]."</td>";
    echo "<td>".$book_price[$ii]."</td>";
    echo "<td>".$book_dis[$ii]."</td>";
    echo "<td>".$book_image[$ii]."</td>";
    echo "</tr>";

}

echo "</table>";

?>