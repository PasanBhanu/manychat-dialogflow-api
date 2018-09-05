<?php
    require_once("database.php");
    if (!isset($_SESSION['id'])){
        header('Location: index.php');
        die();
    }else{
        $userId = $_SESSION['id'];
    }
    $msgApiKey = $msgDialogflow = $userManyChatApiKey = $userJSONKey = "";
    // Load Data
    $conn = mysqli_connect($database_server, $database_username, $database_password, $database_name);
    $sql = "SELECT * FROM tblusers WHERE UserId='$userId'";
    $result = mysqli_query($conn, $sql);
    if ($result){
        $row = mysqli_fetch_assoc($result);
        $userManyChatApiKey = $row['UserManyChatApiKey'];
        $userJSONKey = $row['UserJSONKey'];
    }
    mysqli_close($conn);
    // Save Data
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['API_KEY'])){
            $conn = mysqli_connect($database_server, $database_username, $database_password, $database_name);
            if (isset($_POST['userManyChatApiKey'])){ $userManyChatApiKey = mysqli_real_escape_string($conn, textencode($_POST['userManyChatApiKey'])); }
            if (!empty($_POST['userManyChatApiKey'])){
                $sql = "UPDATE tblusers SET UserManyChatApiKey= '$userManyChatApiKey' WHERE UserId='$userId'";
                mysqli_query($conn, $sql);
                $msgApiKey = "Your API Key Updated!";
            }else{
                $msgApiKey = "Please enter api key.";
            }
            mysqli_close($conn);
        }
		if (isset($_POST['DIALOGFLOW'])){
            $conn = mysqli_connect($database_server, $database_username, $database_password, $database_name);
            $target_file = "keys/" . $userId . ".json";
            $userJSONKey = $userId . ".json";
            if (move_uploaded_file($_FILES["userJsonKey"]["tmp_name"], $target_file)) {
                $sql = "UPDATE tblusers SET UserJSONKey='$userJSONKey' WHERE UserId='$userId'";
                mysqli_query($conn, $sql);
                $msgDialogflow = "Your Dialogflow Key Updated!";
            } else {
                $msgDialogflow = "Sorry, there was an error uploading your file.";
            }
            mysqli_close($conn);
        }
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Chat Connect - Settings</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!-- Custom styles for this template -->
        <link href="assests/css/home.css" rel="stylesheet">
    </head>

  <body>

    <nav class="navbar navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Chat Connect</a>
        <a href="logout.php" class="btn btn-danger my-2 my-sm-0">Logout</a>
    </nav>

    <main role="main" class="container">
        <br><br>
        <div class="container">
            <h2>Instructions</h2>
            <br>
            <p>Please follow this <a href="instructions.pdf" target="_blank">instructions</a> to connect your Dialogflow bot with Manychat.<br>Your Manychat Endpoint is <strong>https://s1.softinklab.com/chat/api.php?id=<?php echo $userId; ?></strong></p>
            <?php
                if ($userJSONKey != "" and $userManyChatApiKey != ""){
                    echo "<p><strong>Status : </strong><font color='green'>Your API is ready to connect!</font></p>";
                }else{
                    echo "<p><strong>Status : </strong><font color='red'>Please add Manychat API Key and Dialogflow Key to initiate connection.</font></p>";
                }
            ?>
        </div>
        <br><br>
        <div class="container">
            <h2>Manychat API Details</h2>
            <br>
            <form method="post">
                <div class="form-group row">
                    <label for="userManyChatApiKey" class="col-sm-2 col-form-label">API Key</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="userManyChatApiKey" name="userManyChatApiKey" value="<?php echo $userManyChatApiKey; ?>">
                    </div>
                </div>
                <input type="submit" name="API_KEY" class="btn btn-primary mb-2" value="Save"/>
            </form>
            <p class="mb-3 text-muted"><font color="red"><?php echo $msgApiKey ?></font></p>
        </div>
        <br><br>
        <div class="container">
            <h2>Dialogflow API Connect</h2>
            <br>
            <?php
                if ($userJSONKey == ""){
                    echo "<p><strong>No key selected. Please add your service account key.</strong></p>";
                }else{
                    echo "<p><strong>Your current key is " . $userJSONKey . "</strong></p>";
                }
            ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="userJsonKey">Select Service Account Credentials JSON File</label>
                    <input type="file" class="form-control-file" id="userJsonKey" name="userJsonKey">
                </div>
                <input type="submit" name="DIALOGFLOW" class="btn btn-primary mb-2" value="Upload"  accept=".json"/>
            </form>
            <p class="mb-3 text-muted"><font color="red"><?php echo $msgDialogflow ?></font></p>
        </div>
    </main>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>
