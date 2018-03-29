<?php

function getConversationID($user_1, $user_2, $conn) {
    if ($user_2 != '') {
        $chat_query = "SELECT * FROM active_conversations WHERE user_1 ='".$user_2."' and user_2 = '".$user_1."'";
        $response = $conn->query($chat_query);
        $response = $response->fetch_assoc();
        $conversation_id = $response["conversation_id"];
    }
    if ($response == '') {
        $chat_query = "SELECT * FROM active_conversations WHERE user_1 ='".$user_1."' and user_2 = '".$user_2."'";
        $response = $conn->query($chat_query);
        $response = $response->fetch_assoc();
        $conversation_id = $response["conversation_id"];
    } 
    if ($user_2 == '') {
        $conversation_id = array();
        $chat_query = "SELECT * FROM active_conversations WHERE user_1 ='".$user_1."' OR user_2 = '".$user_1."'";
        $response = $conn->query($chat_query);
        while ($row = $response->fetch_assoc()) {
            array_push($conversation_id, $row['conversation_id']);
        } 
    }
    return $conversation_id;
}
        

function getNamefromID($userToID, $user_id, $conn, $final) {
    if ($userToID == true) {
        $chat_query = "SELECT * FROM user_data WHERE fb_id ='".$user_id."'";
        $response = $conn->query($chat_query);
        $response = $response->fetch_assoc();  
    } else {
        $chat_query = "SELECT * FROM user_data WHERE username ='".$user_id."'";
        $response = $conn->query($chat_query);
        $response = $response->fetch_assoc();
        $response = $response["fb_id"];
    }
    
    return $response;
}


function sortByType($req_type, $contact_id){
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
        //$conn = $check[1];
        $mysql_init = 'USE sudocode_blossom';
        $conn->query($mysql_init);
        $user_id = $check[1]->getProperty('id');
        if ($req_type == '') {
            $req_type = $_POST["req_type"];
            $contact_id =$_POST["contact_id"];
        }
        if ($req_type == 'create'){
            //code to be executed if req=create
            $sqlquery = "SELECT * FROM active_conversations WHERE user_1 = '".$user_id."' AND user_2 = '".$contact_id."' OR user_1 = '".$contact_id."' AND user_2 = '".$user_id."'";
            $usersChats = $conn->query($sqlquery);
            $userChats = $usersChats->fetch_assoc();
            if (count($userChats) == 0) {
                if ($user_id != $contact_id){
                    //Create new row for conversation
                    $chat_query = "INSERT INTO active_conversations (user_1, user_2) VALUES ('".$user_id."', '".$contact_id."')";
                    $conn->query($chat_query);

                    //Get the ID of the row
                    $conversation_id = @mysql_insert_id();

                    //Add the conversation ID to the actioners row
                    $chat_query = "UPDATE user_data SET active_conversations = CONCAT(active_conversations, '".$conversation_id."') WHERE fb_id ='".$user_id."'";
                    $conn->query($chat_query);

                    //Add the conversation ID to the recievers account
                    $chat_query = "UPDATE user_data SET active_conversations = CONCAT(active_conversations, '".$conversation_id."') WHERE fb_id ='".$contact_id."'";
                    $conn->query($chat_query);
                    $output = '1';
                }
            }
        }
            if ($req_type ==  'delete'){
                //code to be executed if req=delete
                
                //Get the conversation id
                $conversation_id = getConversationID($user_id, $contact_id, $conn);
                
                //Delete conversation from table
                $chat_query = "DELETE * FROM active_conversations WHERE user_1 ='".$user_id."' and user_2 = '".$contact_id."'";
                $conn->query($chat_query);
                
                //Remove conversation id from active user
                $chat_query = "SELECT * FROM user_data WHERE id ='".user_id."'";
                $response = $conn->query($chat_query);
                $response = $response->fetch_assoc();
                $response = explode(chr(31), $response["active_conversations"]);
                $return = '';
                foreach (count($response) as $curr_id) {
                    if ($curr_id != $conversation_id) {
                        $return = $return . $curr_id .chr(31);
                    }
                }
                $chat_query = "UPDATE user_data SET active_conversations = '".$return."' where fb_id = '".$user_id."'";
                $conn->query($chat_query);
                
                //Remove conversation id from passive user
                $chat_query = "SELECT * FROM user_data WHERE id ='".$contact_id."'";
                $response = $conn->query($chat_query);
                $response = $response->fetch_assoc();
                $response = explode(chr(31), $response["active_conversations"]);
                $return = '';
                foreach (count($response) as $curr_id) {
                    if ($curr_id != $conversation_id) {
                        $return = $return . $curr_id .chr(31);
                    }
                }
                $chat_query = "UPDATE user_data SET active_conversations = '".$return."' where fb_id = '".$contact_id."'";
            }
                
            else if ($req_type ==  'send'){
                //code to be executed if req=send
                $contact_id = $_POST["contact_id"];
                //get conversation id
                //add the message
                $user_name = getNamefromID(true, $user_id, $conn);
                $user_name = $user_name['username'];
                $conversation_id = getConversationID($user_id, $contact_id, $conn);
                if (trim($_POST["message"]) != "" ) {
                    $message = addslashes($_POST["message"]);
                    $chat_query = "UPDATE active_conversations SET messages = CONCAT(messages, '".$user_id.chr(31).$message.chr(31)."') WHERE conversation_id = '".$conversation_id."'";
                    $conn->query($chat_query);
                    $chat_query = "UPDATE active_conversations SET cache = CONCAT(cache, '".$user_id.chr(31).$message.chr(31)."') WHERE conversation_id = '".$conversation_id."'";
                    $output = array('1', mysql_error($link), $_POST["message"]);
                } else {
                    $chat_query = "";
                    $output = array('0', mysql_error($link), $_POST["message"]);
                }
            }
            else if ($req_type ==  'check'){
                //code to be execute if req=check
                //get conversation id
                $marker = $_POST["marker"];
                $contact_id = $_POST["contact_id"];
                $conversation_id = getConversationID($user_id, $contact_id, $conn);
                //get messages
                $chat_query = "SELECT messages FROM active_conversations WHERE conversation_id = '".$conversation_id."'";
                $response = $conn->query($chat_query);
                $messagesRetrieved = explode(chr(31), $response->fetch_assoc()["messages"]);
                $totalLength = count($messagesRetrieved);
                $messagesSent = array();
                if ($marker == -1){
                    $marker = 0;
                } else {
                    $marker = $totalLength - $marker;
                }
                
                for($i = $marker; $i < $marker+20; ++$i) {
                    array_push($messagesSent, $messagesRetrieved[$i]);
                }
                
                $returnMarker = $totalLength - $marker + 20;
                
                if ($returnMarker >= $totalLength ) {
                    $returnMarker = -1;
                }
                $output = array($messagesSent, $returnMarker);
                
            }
        
            else if ($req_type == 'check_cache') {
                $contact_id = $_POST["contact_id"];
                $conversation_id = getConversationID($user_id, $contact_id, $conn);
                $chat_query = "SELECT cache FROM active_conversations WHERE conversation_id = '".$conversation_id."'";
                $response = $conn->query($chat_query);
                $output = explode(chr(31), $response->fetch_assoc()["cache"]);
                $chat_query = "UPDATE active_conversations SET cache = '' WHERE conversation_id = '".$conversation_id."'";

            }
            else if ($req_type == 'active_conv') {
                $conversation_id = getConversationID($user_id, '', $conn);
                $names = array();
                foreach (range(1, count($conversation_id)) as $number) {
                    $chat_query = "SELECT * FROM active_conversations WHERE conversation_id = '".$conversation_id[$number-1]."'";
                    $response = $conn->query($chat_query);
                    $response = $response->fetch_assoc();
                    if ($response["user_1"] != $user_id) {
                        $user_data = getNamefromID(true, $response["user_1"], $conn);
                        $other_name = getNamefromID(true, $response["user_2"], $conn);
                        $lastMessageIndex = count(explode(chr(31),$response['messages']));
                        $lastMessage = trim(explode(chr(31),$response['messages'])[$lastMessageIndex - 3]);
                        if ($lastMessage == $response["user_1"]) {
                            $name = $user_data["username"];
                        } else if ($lastMessage == $response["user_2"] ) {
                            $name = "You";
                        }
                        if ($user_data["username"] != null) {
                            array_push($names, array($user_data["username"], $user_data["fb_id"], $user_data["uni"], $user_data["cover"], array($name, (explode(chr(31),$response['messages'])[$lastMessageIndex - 2]))));
                        }
                    }
                    else {
                        $user_data = getNamefromID(true, $response["user_2"], $conn);
                        $other_name = getNamefromID(true, $response["user_1"], $conn, true);
                        $lastMessageIndex = count(explode(chr(31),$response['messages']));
                        $lastMessage = trim(explode(chr(31),$response['messages'])[$lastMessageIndex - 3]);
                        if ($lastMessage == $response["user_1"]) {
                            $name = "You";
                        } else if ($lastMessage == $response["user_2"] ) {
                            $name = $user_data["username"];
                        }
                        if ($user_data["username"] != null) {
                            array_push($names, array($user_data["username"], $user_data["fb_id"], $user_data["uni"], $user_data["cover"], array($name, (explode(chr(31),$response['messages'])[$lastMessageIndex - 2]))));
                        }
                    }
                }
                $output = $names;
            }
        $conn->query($chat_query);
        $conn->close();
                
        echo json_encode($output);
} 
    else {
        //in this case the authentication didn't work so return failure
        echo json_encode("2");
    }
    
}


sortByType()
?>
