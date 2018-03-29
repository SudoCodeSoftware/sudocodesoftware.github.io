<?php

function getAnswers() {
    //This lets us call the access token authentication function
    include 'login.php';
    //Get the access token
    $at = $_POST["ato"];
    //check the access token and store it in $check (array)
    $check = checkAT($at);
    
    if ($check[0] == '1a'){
        //If the first element of the check array is the success(exisiting user) character
        //get the POST data to be updated
        $req_type = $_POST["type"];
        $user = $check[1];
        if ($req_type == "signup_personal"){
            $user_info = array($_POST["DOB"], $_POST["pref"]);
        }
        elseif ($req_type == "signup_uni") {
            $user_info = array($_POST["zid"], $_POST["email"], $_POST["degree"], $_POST["faculty"], $_POST["year"]);
        }
        $output = updateDB($user, $req_type, $user_info);  
    } 
    else {
        //in this case the authentication didn't work so return failure
        $output = '2';
    }
    echo json_encode($output);
}

function updateDB($user, $req_type, $user_info) {
    
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
    if ($req_type == "signup_personal") {
        $user_update = "UPDATE user_data SET gender_preference = '".$user_info[1]."', age = '".$user_info[0]."' WHERE fb_id = '".$user_id."'";
    }
    elseif ($req_type == "signup_uni"){
        $user_update = "UPDATE user_data SET email = '".$user_info[1]."', uni_id = '".$user_info[0]."', degree = '".$user_info[2]."', faculty = '".$user_info[3]."', year = '".$user_info[4]."'  WHERE fb_id = '".$user_id."'";
    }
    else {
        $user_update = "UPDATE user_data SET gender_preference = 'unknown', age = 'unknown' WHERE fb_id = '".$user_id."'";
    }
    
    $conn->query($user_update);
    $error = mysql_error($conn);
    $conn->close();
    
    if ($error == null) {
        return array('1', $user_info);
    }
    else {
        return array('2', $error, $user_info);
    }
}
    
getAnswers()

?>