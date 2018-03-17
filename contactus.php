<?php
$name = $_GET['name'];
$email = $_GET['email'];
$message = $_GET['message'];
mail($email, $name, $message);                        
?>