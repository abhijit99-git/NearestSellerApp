<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="assets/best-seller.png" type="image/png">
  <title>Sign Up - Nearest Seller Finder</title>
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

  <!-- Signup Form -->
  <main class="w-full max-w-md px-6">
    <h1 class="text-4xl font-bold text-center mb-6">Create Account</h1>
    <p class="text-gray-300 text-center mb-10">Sign up to get started</p>

    <form action="signup_auth.php" method="POST" class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20">
      


      <!-- Email -->
      <div class="mb-4 text-left">
        <label class="block text-sm mb-2">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>

      <!-- Password -->
      <div class="mb-4 text-left">
        <label class="block text-sm mb-2">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>

      <!-- Confirm Password -->
      <div class="mb-6 text-left">
        <label class="block text-sm mb-2">Confirm Password</label>
        <input type="password" name="confirm_password" required class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>

      <!-- Submit -->
      <button type="submit" class="w-full py-3 bg-gray-200 text-black rounded-md hover:bg-gray-300 transition mb-4">
        Sign Up
      </button>

      <!-- Login Link -->
      <div class="text-center">
        <p class="text-sm text-gray-300">Already have an account? 
          <a href="login.php" class="text-white font-medium">Log In</a>
        </p>
      </div>
    </form>
  </main>

</body>
</html>
