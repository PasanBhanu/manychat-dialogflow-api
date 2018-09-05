<?php
    require_once("database.php");
    $errorData = "";
    $inputEmail = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $conn = mysqli_connect($database_server, $database_username, $database_password, $database_name);
        if (isset($_POST['inputEmail'])){ $inputEmail = mysqli_real_escape_string($conn, textencode($_POST['inputEmail'])); }
        if (!empty($_POST['inputEmail']) and !empty($_POST['inputPassword'])){
            $inputPassword = base64_encode($_POST['inputPassword']);
            $sql = "SELECT * FROM tblusers WHERE UserEmail='$inputEmail'";
            $result = mysqli_query($conn, $sql);
            if ($result){
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    if($inputPassword == $row['UserPassword']){
                        $userId = $row['UserId'];
                        $_SESSION['id'] = $userId;
                        header('Location: settings.php');
                    }else{
                        $errorData = "Username or Password does not match!";
                    }
                }else{
                    $errorData = "Username or Password does not match!";
                }
            }else{
                $errorData = "Username or Password does not match!";
            }
		}else{
			$errorData = "Please fill all required data.";
		}
		mysqli_close($conn);
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Chat Connect - Login</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="assests/css/signin.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <form class="form-signin" method="POST">
      <img class="mb-4" src="assests/images/logo.png" alt="" height="150">
      <h1 class="h3 mb-3 font-weight-normal">Sign In</h1>
      <label for="inputEmail" class="sr-only">Email Address</label>
      <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Email Address" value="<?php echo $inputEmail; ?>" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="inputPassword" class="form-control bottom-signin-field" placeholder="Password" required>
      <input class="btn btn-lg btn-primary btn-block" type="submit" value="Login"/>
      <p class="mt-5 mb-3 text-muted"><font color="red"><?php echo $errorData ?></font></p>
      <p class="mt-5 mb-3 text-muted"><a href="register.php">Create Account</a></p>
    </form>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>