<?php
include 'config.php';
if(isset($_POST['register'])){
	$username = $_POST['username'];
	$email = $_POST['email'];
	$role = $_POST['role'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepared Statement to check existence
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

	if($result->num_rows > 0){
		$error = "Username or Email already exists!";
	} else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
		$stmt->execute();
		$success = "Registration successful! You can now <a href='login.php'>login</a>.";
	}
}
include 'header.php';
?>
<div class="flex flex-col items-center py-12">
	<h1 class="text-2xl font-bold mb-6">Create Account</h1>
	<form method="POST" class="w-full max-w-xs bg-white p-6 rounded-lg shadow">
		<?php if(isset($error)) echo '<p class="text-red-600 mb-2">'.$error.'</p>'; ?>
		<?php if(isset($success)) echo '<p class="text-green-600 mb-2">'.$success.'</p>'; ?>
		<input type="text" name="username" placeholder="Username" required class="w-full px-3 py-2 mb-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
		<input type="email" name="email" placeholder="Email" required class="w-full px-3 py-2 mb-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
		<input type="password" name="password" placeholder="Password" required class="w-full px-3 py-2 mb-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
		<select name="role" required class="w-full px-3 py-2 mb-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
			<option value="customer" selected>Customer</option>
			<option value="seller">Seller</option>
		</select>
		<button name="register" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Register</button>
		<p class="mt-3 text-center text-sm">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
	</form>
</div>
<?php include 'footer.php'; ?>