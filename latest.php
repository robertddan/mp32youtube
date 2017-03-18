<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

require("inc/config.php");
require("inc/database.php");

$database = new MySQLDatabase();



$sql = "SELECT ID, songname, video_id
        FROM youtube
        ORDER BY rand()
        LIMIT 5 ";

$youtube = $database->query($sql);
$count = 0;
$max = 5;

print '<table id="latestVIdeo" width="455" height="115" border="0" ><tr >';
while($record = $database->fetch_array($youtube)) {
    
$vidaidi = $record['video_id'];  
$vidname = $record['songname']; 

if(strlen($vidname) > 19){
    $vidname = substr($vidname, 0, 19) ."..."; 
} 
    
$count++;
        print <<<END

            <td valign="top">
                
                    <a href="http://www.youtube.com/watch?v=$vidaidi" target="_blank">
                    <div style="margin-right:5px; margin-bottom:5px; height:74px; width:113px;" ><img style="border: none; " src="http://img.youtube.com/vi/$vidaidi/1.jpg" width="113" height="74"></div> <span style="font-size: 9px;">$vidname</span></a>  
                
            </td> 

END;

if($count >= $max){
  //reset counter
   $count = 0;
  //end and restart
   echo '</tr>';
 }

}
print '</table>'
?>
<script>

    $img = $('#latestVIdeo img');
    $img.each(function(){
        $(this).hide().load(function(){
            $(this).fadeIn(900);
        });
    });
    

</script>