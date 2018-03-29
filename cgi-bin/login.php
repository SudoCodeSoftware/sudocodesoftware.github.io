<?php
function checkAT($pass_at = '') {
    
    //Import the facebook SDK stuff
    require_once __DIR__ . '/facebook-php-sdk-v4/src/Facebook/autoload.php';


    //Setup the specific facebook SDK information
    $fb = new Facebook\Facebook([
      'app_id' => '303510073316531',
      'app_secret' => 'b22ecbdd1c3be9adf74bfd79a612c896',
      'default_graph_version' => 'v2.5',
    ]);
    
    //Get the access token that's being posted
    
    if ($_POST["at"] == null) {
        //If $pass_at has no data in it, the function is being used for external authentication
        //So there is POST data
         $at = $_POST["ato"];
    }
    else {
        $at = $_POST["at"];
    }
    
    $at = str_replace(' ', '', $at);
    
    //Set some variables
    $output = '0';
    $error = '';
    $user = '';

    //Start checking the access token
    if ($at == '') {
        $output =  '2';
    } 
    
    elseif ($at != '') {
        //Work out if the access token is correct
        try {
            //If the access token is correct, store the response object and set the output to 1
            $response = $fb->get('/me?fields=id,name,email,gender,picture.width(300)', $at);
            $user = $response->getGraphUser();
            $output = '1';
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            //If Graph returns an error store the error and set the output to 2
            $error = 'Graph returned an error: ' . $e->getMessage();
            $output = '2'; 
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            //If validation fails or other local issues store the error and set the output to 2
            $error =  $error . 'Facebook SDK returned an error: ' . $e->getMessage();
            $output = '2';
        }
    }
    if ($output == '1') {
            //If authentication was succesful, store the username
            $output = $output . checkUserDB($user, $at)[0];
        }
    if ($_POST["ato"] == null) {
        //if pass_at is empty the function is being used externally
        //so we only want to return the success or failure
        $pic = $user->asArray('picture');
        $pic = $pic['picture']['url'];
        echo json_encode(array($output, $pic));
    }
    else {
        //else return succes/failure and the user object
        return array($output, $user, $error);
    }
}

function checkUserDB($user, $at) {
    $servername = "localhost";
    $username = "sudocode";
    $password = "sud0mcnuggets!";

    $user_id = $user->getProperty('id');
    $user_name = explode($user->getProperty('name'), ' ')[0];
    $user_email = $user->getProperty('email');
    $user_gender = $user->getProperty('gender');
    $user_cover = $user->getProperty('picture');
    
    $time = time();
    
    
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $mysql_init = 'USE sudocode_blossom';
    $conn->query($mysql_init);
    
    $user_check = "SELECT * FROM user_data WHERE fb_id = '". $user_id ."'";
    $result = $conn->query($user_check);
    
    if ($result->num_rows != 0){
        //user already exists
        $output = 'a';
        $pic = $user->asArray('picture');
        $pic = $pic['picture']['url'];
        //user doesn't already exist
        $add_user = "UPDATE user_data SET cover = '".$pic."' WHERE fb_id = '".$user_id."'";
        $conn->query($add_user);
    }
    else {
        $pic = $user->asArray('picture');
        $pic = $pic['picture']['url'];
        //user doesn't already exist
        $output = 'b';
        $add_user = "INSERT INTO user_data (fb_id, username, gender, cover, email)
VALUES ('". $user_id ."' , '".$user_name."', '".$user_gender."','".$pic."','".$user_email."')";
        $conn->query($add_user);
    }
    
    $activate_session = "UPDATE user_data SET session = '". $at .";". $time ."' where fb_id = '".$user_id."'";
    $conn->query($activate_session);
    
    
    $conn->close();
    return $output;
}
checkAT();
?>
