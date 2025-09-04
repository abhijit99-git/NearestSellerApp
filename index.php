
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="assets/best-seller.png" type="image/png">

  <title>Nearest Seller Finder</title>
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
    .active-link {
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
    }
  </style>
</head>
<body class="text-white min-h-screen flex flex-col">

<script>
    window.addEventListener('DOMContentLoaded', () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        document.getElementById('lat').value = position.coords.latitude;
        document.getElementById('lng').value = position.coords.longitude;
        console.log('Location set on page load');
      },
      (error) => {
        console.warn('Geolocation error on page load:', error);
      }
    );
  }
});

</script>

  <!-- Navbar -->
  <header class="fixed top-4 left-1/2 -translate-x-1/2 z-30 w-11/12 max-w-6xl rounded-2xl backdrop-blur-md bg-white/10 shadow-lg border border-white/20">
    <div class="flex items-center justify-between px-6 py-3 relative">
      <!-- Logo -->
      <div class="text-2xl font-bold">
        <span class="inline-block w-6 h-6 bg-white/70 rounded-md"></span>
      </div>
      <!-- Centered Menu -->
      <nav class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex gap-6">
        <a href="#home" class="px-4 py-2 rounded-full text-white text-sm active-link">Home</a>
        <a href="#about" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">About</a>
        <a href="#contact" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">Contact</a>
      </nav>
      <!-- Login -->
      <div class="flex items-center gap-4">
        <a href="login.php" class="text-sm">Log in</a>
      </div>
    </div>
  </header>

  <br><br>
  <!-- Hero Section -->
  <main id="home" class="flex flex-col justify-center items-center text-center flex-1 px-6">
    <h1 class="text-5xl font-extrabold leading-tight md:text-6xl mt-40">
      Find Nearest Seller <br> in a Quick Way
    </h1>
    <p class="mt-6 text-lg text-gray-300 max-w-xl">
      Discover the closest sellers in your area with our easy-to-use tool. Simply enter your location and start exploring.
    </p>
    <button onclick="document.getElementById('order').scrollIntoView({behavior:'smooth'})" 
      class="mt-8 px-6 py-3 bg-gray-200 text-black rounded-md hover:bg-gray-300 transition">
      Place Order
    </button>
  </main>

  <!-- Customer Order Form -->
  <section id="order" class="min-h-screen flex flex-col justify-center items-center px-6 text-center">
    <h2 class="text-4xl font-bold mb-6">Place Your Order</h2>
    <form id="orderForm" action="confirm.php" method="POST" class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20 max-w-md w-full">
      <div class="mb-4 text-left">
        <label class="block text-sm mb-2">Customer Name</label>
        <input type="text" name="customer_name" required 
          class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>
      <div class="mb-4 text-left">
    <label class="block text-sm mb-2">Product</label>
    <select name="product" required 
      class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      <option value="" disabled selected>Select a product</option>
      <?php
      include "db.php";
      $res = $conn->query("SELECT id, name FROM products ORDER BY name ASC");
      while($row = $res->fetch_assoc()){
          echo '<option value="'.$row['name'].'" class="bg-gray-800">'.$row['name'].'</option>';
      }
      ?>
    </select>
</div>
      <div class="mb-6 text-left">
        <label class="block text-sm mb-2">Quantity</label>
        <input type="number" name="quantity" min="1" required 
          class="w-full px-4 py-2 rounded-md bg-white/20 text-white border border-white/30 focus:outline-none focus:ring-2 focus:ring-gray-400">
      </div>
      <button type="submit" class="w-full py-3 bg-gray-200 text-black rounded-md hover:bg-gray-300 transition">
        Submit Order
      </button>
      <input type="hidden" name="lat" id="lat">
<input type="hidden" name="lng" id="lng">

    </form>
  </section>

  <script>
const form = document.getElementById('orderForm');
if (form) {
  form.addEventListener('submit', function(e) {
    if (navigator.geolocation) {
      e.preventDefault();
      navigator.geolocation.getCurrentPosition(
        (position) => {
          document.getElementById('lat').value = position.coords.latitude;
          document.getElementById('lng').value = position.coords.longitude;
          form.submit();
        },
        (error) => {
          console.warn('Geolocation error:', error);
          form.submit();
        }
      );
    } else {
      console.warn('Geolocation not supported');
    }
  });
} else {
  console.error('Form with id "orderForm" not found');
}

</script>

  <!-- About Section -->
  <section id="about" class="py-16 flex flex-col items-center text-center">
    <h2 class="text-4xl font-bold mb-8">About Us</h2>
    <p class="text-gray-300 mb-8 max-w-xl">We help customers find the nearest sellers quickly and easily. Connect with us on GitHub and LinkedIn.</p>
    <div class="flex gap-6">
      <a href="https://github.com/abhijit99-git" target="_blank" class="bg-white/10 p-4 rounded-full hover:bg-white/20 transition">
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 .5C5.37.5 0 5.87 0 12.5c0 5.3 3.438 9.8 8.205 11.387.6.113.82-.263.82-.583 0-.288-.01-1.05-.015-2.06-3.338.727-4.042-1.61-4.042-1.61-.546-1.387-1.334-1.756-1.334-1.756-1.09-.744.083-.728.083-.728 1.205.084 1.84 1.238 1.84 1.238 1.07 1.834 2.807 1.304 3.492.997.108-.775.418-1.305.762-1.605-2.665-.303-5.467-1.332-5.467-5.933 0-1.31.468-2.382 1.235-3.222-.124-.303-.535-1.523.117-3.176 0 0 1.008-.322 3.3 1.23a11.42 11.42 0 013.003-.403c1.02.005 2.045.137 3.003.403 2.29-1.552 3.295-1.23 3.295-1.23.655 1.653.244 2.873.12 3.176.77.84 1.233 1.912 1.233 3.222 0 4.61-2.807 5.625-5.48 5.922.43.37.815 1.1.815 2.222 0 1.606-.015 2.902-.015 3.293 0 .322.216.698.825.58C20.565 22.298 24 17.798 24 12.5 24 5.87 18.63.5 12 .5z"/>
        </svg>
      </a>
      <a href="https://www.linkedin.com/in/abhijit-tikone-684942241/" target="_blank" class="bg-white/10 p-4 rounded-full hover:bg-white/20 transition">
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M4.98 3.5C3.34 3.5 2 4.84 2 6.48s1.34 2.98 2.98 2.98 2.98-1.34 2.98-2.98S6.62 3.5 4.98 3.5zM2.4 9h5.16v12H2.4V9zM9 9h4.92v1.68h.07c.685-1.296 2.358-2.66 4.855-2.66 5.19 0 6.147 3.422 6.147 7.87V21h-5.16v-5.37c0-1.278-.025-2.926-1.78-2.926-1.783 0-2.056 1.392-2.056 2.83V21H9V9z"/>
        </svg>
      </a>
    </div>
  </section>

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
