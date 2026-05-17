<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){

$email = $_POST['email'];
$password = md5($_POST['password']);

$query = mysqli_query($conn,

"SELECT * FROM admins
WHERE email='$email'
AND password='$password'");

if(mysqli_num_rows($query)>0){

$_SESSION['admin']=true;

header("Location:admin.php");

}else{

echo "Invalid Admin Login";

}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Login</title>

<link rel="stylesheet"
href="style.css">

</head>

<body>

<form method="POST"
class="login-form">

<h1>Admin Login</h1>

<input type="email"
name="email"
placeholder="Admin Email"
required>

<br><br>

<input type="password"
name="password"
placeholder="Password"
required>

<br><br>

<button name="login">

Admin Login

</button>

<br><br>

<a href="facebook-admin-login.php">

Connect with Facebook

</a>

</form>

</body>
</html>