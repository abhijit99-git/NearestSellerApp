<?php

include 'db.php';
// Receive form data from index.php
$customer_name = $_POST['customer_name'] ?? '';
$product = $_POST['product'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$customer_lat = $_POST['lat'] ?? ''; // Renamed for clarity
$customer_lng = $_POST['lng'] ?? ''; // Renamed for clarity



// Haversine formula to calculate distance in km
function haversine_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c;
}

// Simulate multiple seller locations (in a real app, this would come from a database)
// Fetch all sellers from DB
$sellers = [];
$sql = "
    SELECT s.name, s.location_lat, s.location_lng, i.stock_qty
    FROM sellers s
    JOIN inventory i ON s.id = i.seller_id
    JOIN products p ON i.product_id = p.id
    WHERE p.name = '$product' AND i.stock_qty >= $quantity
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$sellers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sellers[] = [
            'name' => $row['name'],
            'lat' => (float)$row['location_lat'],
            'lng' => (float)$row['location_lng'],
            'stock_qty' => (int)$row['stock_qty']
        ];
    }
}

$conn->close();

$nearest_seller_name = 'N/A';
$min_distance_km = PHP_FLOAT_MAX; // Initialize with a very large number
$distance_km_display = 'N/A';

if ($customer_lat && $customer_lng) {
    foreach ($sellers as $seller) {
        $current_distance = haversine_distance($customer_lat, $customer_lng, $seller['lat'], $seller['lng']);

        if ($current_distance < $min_distance_km) {
            $min_distance_km = $current_distance;
            $nearest_seller_name = $seller['name'];
        }
    }
    $distance_km_display = round($min_distance_km, 2) . ' km';
} else {
    $distance_km_display = 'Location not provided';
}

// In a real application, you would now "record the order" with $customer_name, $product, $quantity, $nearest_seller_name, $min_distance_km
// For this example, we'll just display the information.
if ($nearest_seller_name === 'N/A') {
    header("Location: NoStockAvailable.php");
    exit(); // stop execution
} else {
    // Start database operations
    include 'db.php';

    // 1. Get seller ID by name
    $stmt = $conn->prepare("SELECT id FROM sellers WHERE name = ?");
    $stmt->bind_param("s", $nearest_seller_name);
    $stmt->execute();
    $seller_result = $stmt->get_result();
    $seller_row = $seller_result->fetch_assoc();
    $seller_id = $seller_row['id'];
    $stmt->close();

    // 2. Get product ID by name
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $stmt->bind_param("s", $product);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product_row = $product_result->fetch_assoc();
    $product_id = $product_row['id'];
    $stmt->close();

    // 3. Check if customer exists
    $stmt = $conn->prepare("SELECT id FROM customers WHERE name = ?");
    $stmt->bind_param("s", $customer_name);
    $stmt->execute();
    $customer_result = $stmt->get_result();
    if ($customer_result->num_rows > 0) {
        $customer_row = $customer_result->fetch_assoc();
        $customer_id = $customer_row['id'];
    } else {
        // Insert new customer
        $stmt_insert = $conn->prepare("INSERT INTO customers (name, location_lat, location_lng) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sdd", $customer_name, $customer_lat, $customer_lng);
        $stmt_insert->execute();
        $customer_id = $stmt_insert->insert_id;
        $stmt_insert->close();
    }
    $stmt->close();

    // 4. Insert order into orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, product_id, seller_id, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $customer_id, $product_id, $seller_id, $quantity);
    $stmt->execute();
    $stmt->close();

    // 5. Reduce stock in inventory
    $stmt = $conn->prepare("UPDATE inventory SET stock_qty = stock_qty - ? WHERE seller_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $seller_id, $product_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="icon" href="assets/best-seller.png" type="image/png">
<title>Order Confirmation - Nearest Seller Finder</title>
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
    color: #fff;
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
    <!-- Logo -->
    <div class="text-2xl font-bold">
      <span class="inline-block w-6 h-6 bg-white/70 rounded-md"></span>
    </div>
    <!-- Centered Menu -->
    <nav class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex gap-6">
      <a href="index.php" class="px-4 py-2 rounded-full text-white text-sm active-link">Home</a>
      <a href="index.php" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">About</a>
      <a href="index.php" class="px-4 py-2 rounded-full hover:bg-white/10 text-white text-sm">Contact</a>
    </nav>
    <!-- Login -->
    <div class="flex items-center gap-4">
      <a href="login.php" class="text-sm">Log in</a>
    </div>
  </div>
</header>

<br><br><br><br>

<!-- Order Confirmation Card -->
<main class="container mx-auto px-4 py-8 max-w-2xl flex-grow">
  <div class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20 text-center">
    <div class="flex justify-center mb-6">
      <div class="bg-green-100/20 p-4 rounded-full">
        <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
        </svg>
      </div>
    </div>
    <h2 class="text-3xl font-bold mb-2">Order Confirmed!</h2>
    <p class="text-gray-300 leading-relaxed mb-8">Thank you for your purchase. Your order has been placed successfully.</p>

    <div class="border-t border-white/20 pt-6 text-left">
      <h3 class="text-xl font-semibold mb-6">Order Details</h3>
      <div class="space-y-4">
        <div class="flex justify-between">
          <p class="text-gray-300">Customer Name</p>
          <p class="text-white"><?php echo htmlspecialchars($customer_name) ?></p>
        </div>
        <div class="flex justify-between">
          <p class="text-gray-300">Product Name</p>
          <p class="text-white"><?php echo htmlspecialchars($product) ?></p>
        </div>
        <div class="flex justify-between">
          <p class="text-gray-300">Quantity</p>
          <p class="text-white"><?php echo htmlspecialchars($quantity) ?></p>
        </div>
        <div class="flex justify-between">
          <p class="text-gray-300">Assigned Seller</p>
          <p class="text-white"><?php echo htmlspecialchars($nearest_seller_name) ?></p>
        </div>
        <div class="flex justify-between">
          <p class="text-gray-300">Distance to Seller</p>
          <p class="text-white"><?php echo htmlspecialchars($distance_km_display) ?></p>
        </div>
      </div>
    </div>

    <div class="mt-10">
      <button onclick="location.href='index.php'" class="w-full bg-gray-200 text-black py-3 px-4 rounded-md hover:bg-gray-300 transition font-semibold">
        Place Another Order
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
