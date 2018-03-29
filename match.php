<?php
function indexByType(){
    
    $req_type = $_POST["req_type"];
    
    if ($req_type == "ticked") {
        setState();
    } else {
        getMatches();
    }
    
}

function setState(){
    include 'login.php';
    //check the access token and store it in $check (array - 0=output, 1=user)
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
        
        $mysql_init = 'USE sudocode_blossom';
        $conn->query($mysql_init);
        $user_id = $check[1]->getProperty('id');
        $dbquery = "SELECT * FROM user_data where fb_id = '".$user_id."'";
        $response = $conn->query($dbquery);
        $user_response = $response->fetch_assoc();
    }
    $matchId = $_POST["matchId"];
    
    $dbquery = "SELECT * FROM user_data WHERE fb_id = ".$matchId."";
    $matchUser = $conn->query($dbquery);
    $matchUser = $matchUser->fetch_assoc();
    
    
    $dbquery = "UPDATE user_data SET ticked = CONCAT(ticked, '".$user_response["user_id"].chr(31)."') WHERE fb_id = ".$matchId."";
    $conn->query($dbquery);
    
    
    
    $dbquery = "SELECT * FROM user_data WHERE fb_id = ".$user_id."";
    $response = $conn->query($dbquery);
    $response = $response->fetch_assoc();
    
    $ticked = explode(chr(31), $response["ticked"]);
    
    $output = array('0', $response);
    if (in_array($matchUser['user_id'], $ticked)) {
        ob_start();
        include 'chat.php';
        $surpress = sortByType('create', $matchUser["fb_id"]);
        $output = array('1', array("name"=>$matchUser["username"], "cover"=>$matchUser["cover"]));
        ob_end_clean();
    }
    echo json_encode($output);
    $conn->close();
}


function getMatches() {
    include_once 'login.php';
    //check the access token and store it in $check (array - 0=output, 1=user)
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
        
        $mysql_init = 'USE sudocode_blossom';
        $conn->query($mysql_init);
        $user_id = $check[1]->getProperty('id');
        $dbquery = "SELECT * FROM user_data where fb_id = '".$user_id."'";
        $response = $conn->query($dbquery);
        $user_response = $response->fetch_assoc();
    
    $dbquery = 'SELECT * FROM user_data';
    $response = $conn->query($dbquery);
    $matches = array();
    $threshold = 0;
    $level = 0;
    $limit = 3;
    while($row = $response->fetch_assoc()) {
        if (!in_array($user_response["user_id"], explode(chr(31), $row["checked"]))){
        
            //matching algorithm
            if ($user_response["gender_preference"] == $row["gender"]) {
                if ($user_response["gender"] == $row["gender_preference"]) {
                    if ($user_response["uni"] == $row["uni"]){
                        $level += 10;
                    }
                    $user_fac = explode(chr(31),$user_response["faculty"]);
                    $match_fac = explode(chr(31), $row["faculty"]);
                    if ($user_fac[0] == $match_fac[0]){
                        $level += 10;
                    }
                    if (count($user_fac) > 1 and count($match_fac) > 1) {
                        if ($user_fac[1] == $match_fac[1]){
                        $level += 10;
                        }
                    }
                    if ($user_response["degree"] == $row["degree"]) {
                        $level += 10;
                    }

                    str_replace('"', "", $user_response["age"]);
                    str_replace("'", "", $user_response["age"]);

                    str_replace('"', "", $row["age"]);
                    str_replace("'", "", $row["age"]);
                    $date = new DateTime($user_response["age"]);
                    $now = new DateTime();
                    $interval = $now->diff($date);
                    $user_age =  $interval->y;


                    $date = new DateTime($row["age"]);
                    $now = new DateTime();
                    $interval = $now->diff($date);
                    $match_age =  $interval->y;

                    $level -= abs(($user_age - $match_age));

                    }

                }

        if ($level >= $threshold && $user_response["user_id"] != $row["user_id"] && count($matches) <= $limit) {
            $dbquery = "UPDATE user_data SET checked = CONCAT(checked, '".$user_response["user_id"].chr(31)."') WHERE user_id = ".$row["user_id"]."";
            //$conn->query($dbquery);
            array_push($matches, array("fb_id"=>$row["fb_id"], "name"=>$row["username"], "photo"=>$row["cover"], "uni"=>$row["uni"], "degree"=>$row["degree"], "age"=>$match_age));
            }
        }
    }
    if (count($matches) < $limit){
        array_push($matches, "0");
        }
}

     else {
        $output = '2';
    }
echo json_encode($matches);
$conn->close();
} 
indexByType()
?>