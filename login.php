<?php
session_start();
include 'db.php'; // include your database connection

$error = ''; // initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email=? AND password=?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Valid admin, redirect to admin.php
            $_SESSION['admin_email'] = $email;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid credentials";
        }

        $stmt->close();
    } else {
        $error = "Please enter email and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Nearest Seller Finder</title>
  <link rel="icon" href="assets/best-seller.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      scroll-behavior: smooth;
      background-color: #0f0f0f;
      background-image:
        radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.05), transparent 40%),
        radial-gradient(circle at 80% 30%, rgba(255, 255, 255, 0.04), transparent 40%),
        radial-gradient(circle at 50% 70%, rgba(255, 255, 255, 0.03), transparent 40%),
        linear-gradient(to right, #111 1px, transparent 1px),
        linear-gradient(to bottom, #111 1px, transparent 1px);
      background-size: cover, cover, cover, 40px 40px, 40px 40px;
    }
  </style>
</head>
<body class="text-white min-h-screen flex items-center justify-center">

  <!-- Login Form -->
  <main class="w-full max-w-md px-6">
    <h1 class="text-4xl font-bold text-center mb-6">Admin Login</h1>
    <p class="text-gray-300 text-center mb-10">Enter your credentials</p>

    <form action="" method="POST" class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20">
      
      <!-- Email -->
      <div class="mb-4 text-left">
        <label class="block text-sm mb-2">Username</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      </div>

      <!-- Password -->
      <div class="mb-6 text-left">
        <label class="block text-sm mb-2">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>

      <input type="hidden" name="role" value="admin">

      <!-- Submit -->
      <button type="submit" class="w-full py-3 bg-gray-200 text-black rounded-md hover:bg-gray-300 transition mb-4">
        Log in
      </button>

      <!-- Error message -->
      <?php if ($error): ?>
        <p class="text-red-500 text-sm text-center"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      
    </form>
  </main>

</body>
</html>
