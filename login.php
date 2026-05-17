echo "Invalid Login";
<?php
session_start();
include 'config.php';
if(isset($_POST['login'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$username'");
	if($row = mysqli_fetch_assoc($result)){
		if(password_verify($password, $row['password'])){
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['username'] = $row['username'];
			header('Location: index.php');
			exit();
		} else {
			$error = "Invalid password!";
		}
	} else {
		$error = "User not found!";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Login</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="ui_improvements.css">
</head>

<body>

<div class="logo-container" style="justify-content:center;">
	<img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
	<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Login</span>
</div>
<form method="POST" style="width:300px;margin:auto;">
	<?php if(isset($error)) echo '<p style="color:red;">'.$error.'</p>'; ?>
	<input type="text" name="username" placeholder="Username or Email" required><br><br>
	<input type="password" name="password" placeholder="Password" required><br><br>
	<button name="login">Login</button>
	<p>Don't have an account? <a href="register.php">Register</a></p>
</form>

</body>
</html>