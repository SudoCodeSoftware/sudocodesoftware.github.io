<?php
function getLocation(){
    //This lets us call the access token authentication function
    include login.php;
    //Get the access token
    $at = $_POST["at"];
    //check the access token and store it in $check (array)
    $check = checkAT($at);
    if ($check[0] == '1a'){
        //If the first element of the check array is the success(exisiting user) character
        //get the POST data to be updated
        $data = $_POST["data"];
        updateDB($check[1], $data);
        echo '1';
    } 
    else {
        //in this case the authentication didn't work so return failure
        echo '2';
    }
}

function updateDB($user, $data){
    $servername = "localhost";
    $username = "sudocode";
    $password = "sud0mcnuggets!";

    $user_id = $user->getProperty('id');
    
    
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $mysql_init = 'USE sudocode_blossom';
    $conn->query($mysql_init);
    
    $user_update = "UPDATE user_data SET location_e = '".$data[0]."', location_n = '".$data[1]."' WHERE fb_id = '".$user_id."'";

    $conn->query($user_update);
    
    $conn->close();
}
?>