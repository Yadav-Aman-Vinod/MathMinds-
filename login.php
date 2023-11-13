<?php
session_start();
require_once('dbconfig/config.php');
if (isset($_SESSION['email'])) {
  echo '<script>window.location.href = "login.php";</script>';
  exit;
}
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Login / SignUp</title>
</head>
<style>
  @import url(https://fonts.googleapis.com/css?family=Roboto:300);

.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background: #007bff;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form button:hover,.form button:active,.form button:focus {
  background: #0056b3;
}
.form .message {
  margin: 15px 0 0;
  color: #000000;
  font-size: 12px;
}
.form .message a {
  color: #4CAF50;
  text-decoration: none;
}
.form .register-form {
  display: none;
}
.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}
body {
background-color: #f2f2f2;
}
.eye-toggle {
  position: absolute;
  right: 12px; /* Adjust this value for the desired distance from the right edge */
  top: 57%;
  transform: translateY(-50%);
  cursor: pointer;
  width: 20px;
  height: 20px;
  background-color: transparent;
  background-image: url('eye-icon.png'); /* Add your eye icon image path */
  background-size: cover;
}
.eye-toggle2 {
  position: absolute;
  right: 12px; /* Adjust this value for the desired distance from the right edge */
  top: 55%;
  transform: translateY(-50%);
  cursor: pointer;
  width: 20px;
  height: 20px;
  background-color: transparent;
  background-image: url('eye-icon.png'); /* Add your eye icon image path */
  background-size: cover;
}
</style>
<body>
<div class="login-page">
  <div class="form">
    <form action="login.php" method="post" class="register-form">
      <h1>Math Game Signup</h1>
      <input name="name" type="text" placeholder="Name" required/>
      <input name="email" type="text" placeholder="Username" required/>
      <input name="class" type="number" placeholder="Class" required/>
      <input name="rollno" type="number" placeholder="RollNo" required/>
      <div class="eye-toggle2" onclick="togglePassword('password')"></div>
      <input name="password" type="password" id="password" placeholder="Password" required/>
      <p>Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character</p>
      <input name="cpassword" type="password" placeholder="Confirm Password" required/>
      <button name="register" id="create_btn">create</button>
      <p class="message">Already registered? <a href="#">Sign In</a></p>
    </form>


    <?php
        if(isset($_POST['register'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $class = $_POST['class'];
            $rollno = $_POST['rollno'];
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];

            $password_pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

            if (strlen($email) > 20) {
              echo '<script type="text/javascript">alert("Username should not be more than 20 characters.")</script>';
          } else {
            if ($password == $cpassword) {
                if (preg_match($password_pattern, $password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    $query = "select * from user where email='$email'";
                    $query_run = mysqli_query($con, $query);

                    if ($query_run) {
                        if (mysqli_num_rows($query_run) > 0) {
                            echo '<script type="text/javascript">alert("This Username Already exists.. Please try another Username!")</script>';
                        } else {
                            $query = "insert into user (name, email, class, rollno, password) values('$name','$email','$class','$rollno','$hashed_password')";
                            $query_run = mysqli_query($con, $query);

                            if ($query_run) {
                                echo '<script type="text/javascript">alert("User Registered.. Welcome")</script>';
                                $_SESSION['email'] = $email;
                                $_SESSION['password'] = $hashed_password;
                                echo '<script>window.location.href = "index.php";</script>';
                            } else {
                                echo '<p class="bg-danger msg-block">Registration Unsuccessful due to server error. Please try later</p>';
                            }
                        }
                    } else {
                        echo '<script type="text/javascript">alert("DB error")</script>';
                    }
                } else {
                    echo '<script type="text/javascript">alert("Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character")</script>';
                }
            } else {
                echo '<script type="text/javascript">alert("Password and Confirm Password do not match")</script>';
            }
        }
      }

        ?>




    <form action="login.php" method="post" class="login-form">
      <h1>Math Game Login</h1>
      <input name="email"type="text" placeholder="Username" required/>
      <div class="eye-toggle" onclick="togglePass('pass')"></div>
      <input name="password" type="password" id="pass" placeholder="Password" required/>
      <button name="login"id="login_btn">login</button>
      <p class="message">Not registered? <a href="#">Create an account</a></p>
    </form>


    <?php
        if(isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $query = "select * from user where email='$email'";
            $query_run = mysqli_query($con, $query);

            if ($query_run) {
                if (mysqli_num_rows($query_run) > 0) {
                    $row = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
                    $hashed_password = $row['password'];

                    if (password_verify($password, $hashed_password)) {
                      // Successful login
                      $_SESSION['email'] = $email;
                      $_SESSION['password'] = $password;
                      echo '<script>window.location.href = "index.php";</script>';
                  } else {
                      echo '<script type="text/javascript">alert("Password Verification Failed.")</script>';
                  }
                } else {
                    echo '<script type="text/javascript">alert("Invalid Credentials")</script>';
                }
            } else {
                echo '<script type="text/javascript">alert("Database Error")</script>';
            }
        }
        ?>


  </div>
</div>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  
<script>
$('.message a').click(function(){
    $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
    });
</script>
<script>
function togglePassword() {
    var passwordInput = document.getElementById('password');
    passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
}

function togglePass() {
    var passwordInput = document.getElementById('pass');
    passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
}

</script>

</body>
</html>
