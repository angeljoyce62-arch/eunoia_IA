<?php
include 'config.php';
if(isset($_POST['register'])){
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
	if(mysqli_num_rows($check) > 0){
		$error = "Username or Email already exists!";
	} else {
		mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')");
		$success = "Registration successful! You can now <a href='login.php'>login</a>.";
	}
}
?>

<head>
	<title>Register</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="ui_improvements.css">
</head>
<body>
<div class="logo-container" style="justify-content:center;">
	<img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
	<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Register</span>
</div>
<form method="POST" style="width:300px;margin:auto;">
	<?php if(isset($error)) echo '<p style="color:red;">'.$error.'</p>'; ?>
	<?php if(isset($success)) echo '<p style="color:green;">'.$success.'</p>'; ?>
	<input type="text" name="username" placeholder="Username" required><br><br>
	<input type="email" name="email" placeholder="Email" required><br><br>
	<input type="password" name="password" placeholder="Password" required><br><br>
	<button name="register">Register</button>
	<p>Already have an account? <a href="login.php">Login</a></p>
</form>
</body>