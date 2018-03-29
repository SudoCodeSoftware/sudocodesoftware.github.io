<?php

function sortByType(){
    if ($_POST["req_type"] == 'getProfile'){
        getProfile();
    } 
}


function getProfile() {
    include_once 'login.php';
    
    $check = checkAT();
    
    
    if ($check[0] == '1a'){
        $servername = "localhost";
        $username = "sudocode";
        $password = "sud0mcnuggets!";
        // Create connection
        $conn = new mysqli($servername, $username, $password);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        //$conn = $check[1];
        $mysql_init = 'USE sudocode_blossom';
        $conn->query($mysql_init);
        $user_id = $check[1]->getProperty('id');
        
        if ($_POST["user_id"] != ""){
            $user_id = $_POST["user_id"];
        }
        
        $output = retrieveProfile($conn, $user_id);
        
       
        echo json_encode($output);
        
    } else {
        echo json_encode("2");
    }
}


function retrieveProfile($conn, $user_id) {
    $sqlquery = "SELECT * FROM user_data WHERE fb_id = '".$user_id."'";
    $response = $conn->query($sqlquery);
    $response = $response->fetch_assoc();
    return $response;
}

sortByType()

?>
