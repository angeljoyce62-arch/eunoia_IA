<?php
include 'config.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid Admin Credentials";
        }
    } else {
        $error = "Admin account not found";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link href="css/output.css" rel="stylesheet">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Platform Control Center</h1>
        
        <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-sm text-center'>$error</p>"; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Admin Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Security Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button name="login" class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold hover:bg-slate-800 transition">
                Authenticate Admin
            </button>
        </form>
        
        <div class="mt-6 pt-6 border-t text-center">
            <a href="login.php" class="text-sm text-blue-600 hover:underline">Standard User Login</a>
        </div>
    </div>
</body>
</html>