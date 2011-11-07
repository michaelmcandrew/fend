<?php
$server = "localhost";
$login = "root";
$password = "root";
$database = "fend_drupal";

///////////////////////////////////////////////////////////////////////////////////////////////////

$link = mysql_connect($server, $login, $password) or die("Connect error : " . mysql_error());
echo "Connect OK\n";

$db_selected = mysql_select_db($database, $link);
if (!$db_selected)
   die ('Select DB error : ' . mysql_error());

$query = "SELECT * FROM field_data_body WHERE body_value like '%[img_assist%'";
$result = mysql_query($query);

if (!$result) {
    $message  = 'Query error : ' . mysql_error() . "\n";
    $message .= 'Query : ' . $query;
    die($message);
}

$count=0;
while ($row = mysql_fetch_assoc($result)) 
{
    $tmp = $row['body_value'];
    $count++;
    echo "\n###############################################################################\n";
    echo "Entity ID: ".$row['entity_id'];
    //echo $tmp;    
    //echo "\n###############################################################################\n";
    
    $start = strpos($tmp, "[img_assist");
    
    while ($start = strpos($tmp, "[img_assist") !== FALSE)
    {
        $end = strpos($tmp, "]", $start);
        $img = substr($tmp, $start+12, $end-$start-12);
        
        echo "Img: $img \n";
        
        list($nid, $title, $desc, $link_img, $align, $width, $height) = explode("|", $img);
        
        $nid = substr($nid, strpos($nid, "=")+1);
        $title = substr($title, strpos($title, "=")+1);
        $desc = substr($desc, strpos($desc, "=")+1);
        $link_img = substr($link_img, strpos($link_img, "=")+1);
        $align = substr($align, strpos($align, "=")+1);
        $width = substr($width, strpos($width, "=")+1);
        $height = substr($height, strpos($height, "=")+1);
        
        echo "NID: $nid \n";
        
        $query_image = "SELECT * FROM image WHERE nid=".$nid;
        $result_image = mysql_query($query_image);
        
        $row_image = mysql_fetch_assoc($result_image);
        $fid = $row_image['fid'];
        
        echo "FID: $fid \n";
        
        $query_file = "SELECT * FROM files WHERE fid=".$fid;
        $result_file = mysql_query($query_file);
        
        $row_file = mysql_fetch_assoc($result_file);
        $img_path = $row_file['filepath'];
        
        if ($img_path[0] != '/')  
            $img_path = '/' . $img_path;
        
        echo "Src: $img_path \n";
        
        $buffer = substr($tmp, 0, $start);
        
        $buffer .= "<img alt=\"$desc\" src=\"$img_path\" style=\"width: ".$width."px; height: ".$height."px;\">";
        
        $buffer .= substr($tmp, $end+1);
        
        //echo "Buffer: $buffer \n";
        
        $tmp = $buffer;
        
        mysql_free_result($result_image);
        //break; // Test
    } // End : while ($start = strpos($tmp, "[img_assist"))    
    
    $update_query = "UPDATE field_data_body SET body_value = '".addslashes($tmp)."' WHERE entity_id = ".$row['entity_id'];
    $res = mysql_query($update_query);
    
    if (!$res) {
        $message  = 'Query error : ' . mysql_error() . "\n";
        $message .= 'Query : ' . $update_query;
        die($message);
    }
    
    //break; // Test
}// End : while ($row = mysql_fetch_assoc($result)) 

mysql_free_result($result);

mysql_close($link);
echo "\nEnd ($count entities modified)\n\n";
?>