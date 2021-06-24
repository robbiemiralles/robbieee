<?php 
session_start();
  
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
    header("location: welcome.php");
    exit;
}
  
require_once "config.php";

$_SESSION["verify"] = false;
$_SESSION["code_access"] = false;
$username="";
  
 
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
  
    if(empty(trim($_POST["username"]))){
        echo "<script>alert('ENTER USERNAME');</script>";
    } else{
        $username = trim($_POST["username"]);
    }
     
    if(empty(trim($_POST["password"]))){
        echo "<script>alert('ENTER PASSWORD');</script>";
    } else{
        $password = trim($_POST["password"]);
    }
     
    if(empty($username_err) && empty($password_err)){ 
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){ 
            mysqli_stmt_bind_param($stmt, "s", $param_username);
             
            $param_username = $username;
             
            if(mysqli_stmt_execute($stmt)){ 
                mysqli_stmt_store_result($stmt);
                 
                if(mysqli_stmt_num_rows($stmt) == 1){ 
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){

                            $_SESSION["verify"] = true;
                            $_SESSION["code_access"] = true;
                            
                            $_SESSION["id"] = $id;

                            $stmt1 = $link->prepare("INSERT INTO activity_log (activity, username) VALUES (?, ?)");
                            $stmt1->bind_param("ss", $activity, $username);


                            $activity = "Logged in";
                            $username = $username;


                            $stmt1->execute();
                            $stmt1->close();
                            header("location: verification.php");
                            

                        } else{ 
                             
                            echo "<script>alert('PASSWORD ERROR');</script>";
                        }
                    }
                } else{ 
                    echo "<script>alert('USERNAME IS NOT EXIST');</script>";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}



?>
  
<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    

<head>
    <meta charset="UTF-8">
    <title>Login</title>

<style type="text/css">

.wrapper{ 
    width: 350px; 
    margin-top: 100px; 
    margin-left: 500px;
    background-color: white;
    border-style: double;
        }

body{
    font: 14px times; 
    padding-top: 40px;
    padding-bottom: 40px;
    background-position: absolute;
    background-repeat: no-repeat;
    background-size: cover;
    background-color: gray;
        }

</style>
   
   
</head>  
<body>    
    <div class="wrapper">
        <center><h1>Login</h1>
        <br>
      
        
        <form action="" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control">
                <br><br>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <br><br>
               
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">  
            </div>
            <br>
            <div>
            <br>
            <a href="forgot_password.php"><u>Forgot Password?</u><a></p>
            </div>
            <br><br>
            <p>Don't have an account? <a href="register.php">Register here.</a></p>
    </div></center>
        </form>


</body>
</html>