<?php
//This is the directory where images will be saved 
$target = "res/usr-img/";  
//$target = $target . basename( $_FILES['photo']['name']);
//This gets all the other information from the form
$name=$_COOKIE["username"]; 
$pic_name = $name . ".png";
$target = $target . $pic_name;
//$name = 'Nathyglobal';
$pic=($_FILES['photo']['name']);   
// Connects to your Database  
mysql_connect("localhost", "sudocom", "gate+Grace*civic") or die(mysql_error());  
mysql_select_db("sudocom") or die(mysql_error());   
//Writes the information to the database  
mysql_query("UPDATE User_Base SET DP='$pic_name' WHERE Username='$name'");   
//Writes the photo to the server  
if(move_uploaded_file($_FILES['photo']['tmp_name'], $target))  {   
    //Tells you if its all ok  
    echo "File Uploaded";  }  
else {   
    //Gives and error if its not  
    echo "Sorry, there was a problem uploading your file.";  }
header( 'Location: http://forums.sudo-code.com/profile.html' ) ;
?>