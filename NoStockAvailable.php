<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>No Stock Available</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body {
    background-color: #0f0f0f;
    background-image:
      radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.05), transparent 40%),
      radial-gradient(circle at 80% 30%, rgba(255, 255, 255, 0.04), transparent 40%),
      radial-gradient(circle at 50% 70%, rgba(255, 255, 255, 0.03), transparent 40%),
      linear-gradient(to right, #111 1px, transparent 1px),
      linear-gradient(to bottom, #111 1px, transparent 1px);
    background-size: cover, cover, cover, 40px 40px, 40px 40px;
    color: #fff;
    scroll-behavior: smooth;
  }
  .active-link {
    background-color: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
  }
</style>
</head>
<body class="text-white min-h-screen flex flex-col">

<!-- Navbar -->
<header class="fixed top-4 left-1/2 -translate-x-1/2 z-30 w-11/12 max-w-6xl rounded-2xl backdrop-blur-md bg-white/10 shadow-lg border border-white/20">
  <div class="flex items-center justify-between px-6 py-3 relative">
    <div class="text-2xl font-bold">
      <span class="inline-block w-6 h-6 bg-white/70 rounded-md"></span>
    </div>
    <nav class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex gap-6">
      <a href="index.php" class="px-4 py-2 rounded-full text-white text-sm active-link">Home</a>
      <a href="index.php" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">About</a>
      <a href="index.php" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">Contact</a>
    </nav>
    <div class="flex items-center gap-4">
      <a href="login.php" class="text-sm">Log in</a>
    </div>
  </div>
</header>

<br><br><br><br>

<!-- No Stock Card -->
<main class="container mx-auto px-4 py-8 max-w-2xl flex-grow">
  <div class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20 text-center">
    <div class="flex justify-center mb-6">
      <div class="bg-red-100/20 p-4 rounded-full">
        <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
        </svg>
      </div>
    </div>
    <h2 class="text-3xl font-bold mb-2">Oops!</h2>
    <p class="text-gray-300 leading-relaxed mb-8">Sorry, the selected product is currently out of stock with all sellers.</p>

    <div class="mt-10">
      <button onclick="location.href='index.php'" class="w-full bg-gray-200 text-black py-3 px-4 rounded-md hover:bg-gray-300 transition font-semibold">
        Back to Home
      </button>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="bg-white/10 mt-12 py-6 border-t border-white/20 text-center text-gray-300 text-sm">
  <p>© 2025 Nearest Seller by Abhijit. All rights reserved.</p>
</footer>

<script>
  // Navbar active state on click
  const navLinks = document.querySelectorAll("nav a");
  navLinks.forEach(link => {
    link.addEventListener("click", () => {
      navLinks.forEach(l => l.classList.remove("active-link"));
      link.classList.add("active-link");
    });
  });
</script>

</body>
</html>
