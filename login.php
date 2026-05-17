<?php
include 'config.php';
if(isset($_POST['login'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

    // Prepared Statement for Security
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            if($row['role'] == 'admin') header('Location: admin.php');
            else if($row['role'] == 'seller') header('Location: seller_dashboard.php');
            else header('Location: index.php');
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
include 'header.php';
?>
<div class="flex items-center justify-center py-12">
	<div class="bg-white p-8 rounded-lg shadow-md w-96">
		<div class="flex flex-col items-center mb-6">
			<h1 class="text-2xl font-bold text-gray-800">Login</h1>
		</div>
		
		<form method="POST" class="space-y-4">
			<?php if(isset($error)) echo '<p class="text-red-500 text-sm">'.$error.'</p>'; ?>
			<div>
				<input type="text" name="username" placeholder="Username or Email" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
			</div>
			<div>
				<input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
			</div>
			<button name="login" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Login</button>
			<p class="text-sm text-center text-gray-600">Don't have an account? <a href="register.php" class="text-blue-500">Register</a></p>
		</form>
	</div>
</div>
<?php include 'footer.php'; ?>