<?php
    header("Content-Type: application/json; charset=UTF-8");
    require_once ("vendor/autoload.php");
    require_once ("database.php");
    use Google\Cloud\Dialogflow\V2\SessionsClient;
    use Google\Cloud\Dialogflow\V2\TextInput;
    use Google\Cloud\Dialogflow\V2\QueryInput;

    if (!isset($_POST)){
        die();
    }
    
    $request = json_decode(file_get_contents('php://input'), true);
    
    // Get user data
    $ownerId = $_GET['id'];
    $userManyChatApiKey = $userJSONKey = "";
    $conn = mysqli_connect($database_server, $database_username, $database_password, $database_name);
    $sql = "SELECT * FROM tblusers WHERE UserId='$ownerId'";
    $result = mysqli_query($conn, $sql);
    if ($result){
        $row = mysqli_fetch_assoc($result);
        $userManyChatApiKey = $row['UserManyChatApiKey'];
        $userJSONKey = "keys/" . $row['UserJSONKey'];
    }
    mysqli_close($conn);

    // Get project id
    $jsonData = file_get_contents($userJSONKey);
    $jsonArray = json_decode($jsonData, true);
    $projectId = $jsonArray['project_id'];
    
    // Decode manychat request
    $userId = $request["id"];
    $userResponse = $request["last_input_text"];
    
    // Send data to dialogflow
    $botReply = dialogflowSmallTalk($userJSONKey, $projectId, $userResponse, $userId);

    // Send data to manychat
    sendResponseToManychat(makeManyChatTextResponse($botReply, $userId), $userManyChatApiKey);

    function makeManyChatTextResponse($text, $userId){
        $response = array(
            "subscriber_id" => $userId,
            "data" => array(
                "version" => "v2",
                "content" => array(
                    "messages" => array(
                        array(
                            "type" => "text",
                            "text" => $text
                        )
                    )
                )
            )
        );
        return $response;
    }

    function sendResponseToManychat($data, $apiKey){
        $manyChatApiKey = "Bearer " . $apiKey;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.manychat.com/fb/sending/sendContent",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($data, true),
          CURLOPT_HTTPHEADER => array(
            "Authorization: " . $manyChatApiKey,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "Error :" . $err;
        } else {
            echo $response;
        }
    }

    function dialogflowSmallTalk($apiKey, $projectId, $text, $sessionId, $languageCode = 'en-US'){
        $credentials = array('credentials' => $apiKey);
        $sessionsClient = new SessionsClient($credentials);
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
     
        // create text input
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);
     
        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
     
        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        $queryText = $queryResult->getQueryText();
        $intent = $queryResult->getIntent();
        $confidence = $queryResult->getIntentDetectionConfidence();
        $fulfilmentText = $queryResult->getFulfillmentText();
     
        $sessionsClient->close();

        return $fulfilmentText;
    }
?>