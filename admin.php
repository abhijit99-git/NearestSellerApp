<?php
include "db.php";

// Handle adding seller
if(isset($_POST['add_seller'])){
    $name = $_POST['seller_name'];
    $lat = $_POST['seller_lat'];
    $lng = $_POST['seller_lng'];
    $stmt = $conn->prepare("INSERT INTO sellers (name, location_lat, location_lng) VALUES (?,?,?)");
    $stmt->bind_param("sdd",$name,$lat,$lng);
    $stmt->execute();
    $stmt->close();
}

// Handle adding product
if(isset($_POST['add_product'])){
    $name = $_POST['product_name'];
    $desc = $_POST['product_desc'];
    $stmt = $conn->prepare("INSERT INTO products (name, description) VALUES (?,?)");
    $stmt->bind_param("ss",$name,$desc);
    $stmt->execute();
    $stmt->close();
}

// Handle updating inventory
if(isset($_POST['update_stock'])){
    $seller_id = $_POST['seller_id'];
    $product_id = $_POST['product_id'];
    $qty = $_POST['stock_qty'];
    $stmt = $conn->prepare("INSERT INTO inventory (seller_id,product_id,stock_qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE stock_qty=?");
    $stmt->bind_param("iiii",$seller_id,$product_id,$qty,$qty);
    $stmt->execute();
    $stmt->close();
}

$page = $_GET['page'] ?? 'sellers';
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<link rel="icon" href="assets/best-seller.png" type="image/png">
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style>
:root {
    --primary-color: #10b981;
    --secondary-color: #1f2937;
    --background-color: #111827;
    --text-primary: #d1d1d1ff;
    --text-secondary: #9ca3af;
    --accent-color: #22c55e;
    --border-color: #374151;
    --input-bg: #374151;
    --input-text: #e5e7eb;
    --input-placeholder: #9ca3af;
}
body { 
    font-family:'Inter',sans-serif; 
    background-color: var(--background-color); 
    color: var(--text-primary);
    margin:0;
}
.sidebar-link { 
    display:flex; 
    align-items:center; 
    gap:0.75rem; 
    padding:0.75rem 1rem; 
    border-radius:0.5rem; 
    color: var(--text-secondary); 
    text-decoration:none;
    font-weight: 500;
    transition: background-color 0.3s, color 0.3s;
}
.sidebar-link:hover { 
    background-color: var(--secondary-color); 
    color: var(--primary-color);
}
.sidebar-link.active { 
    background-color: var(--secondary-color); 
    color: var(--primary-color); 
    font-weight: 700;
}
input, select, textarea { 
    background-color: var(--input-bg); 
    color: var(--input-text); 
    border: 1.5px solid var(--border-color); 
    border-radius:0.5rem; 
    padding:0.6rem 0.75rem; 
    font-size: 1rem;
    transition: border-color 0.3s;
    outline-offset: 2px;
}
input::placeholder, textarea::placeholder {
    color: var(--input-placeholder);
}
input:focus, select:focus, textarea:focus {
    border-color: var(--primary-color);
    outline: none;
}
table { 
    color: var(--text-primary); 
    width:100%; 
    border-collapse: separate;
    border-spacing: 0 8px;
    font-size: 0.95rem;
}
thead th { 
    color: var(--text-secondary); 
    padding:0.75rem 1rem; 
    text-align:left;
    font-weight: 600;
    border-bottom: 2px solid var(--border-color);
}
tbody td { 
    padding:0.75rem 1rem;
    background-color: var(--secondary-color);
    border-radius: 0.5rem;
}
tbody tr:hover td { 
    background-color: #256d4a; 
    cursor: default;
}
button { 
    cursor:pointer; 
    font-weight: 600;
    transition: background-color 0.3s;
}
button:hover {
    background-color: var(--accent-color);
}
.modal-bg { 
    background-color: rgba(0,0,0,0.6); 
    backdrop-filter: blur(4px);
}
.flex.min-h-screen {
    min-height: 100vh;
}
.bg-secondary-color {
    background-color: var(--secondary-color);
}
</style>
</head>
<body>

<div class="flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 bg-secondary-color shadow-md flex-shrink-0">
    <div class="p-6"><h1 class="text-2xl font-bold text-[var(--text-primary)]">Admin Panel</h1></div>
    <nav class="mt-6 px-4 space-y-2">
        <?php
        $menus = ['customers'=>'Customers','sellers'=>'Sellers','products'=>'Products','inventory'=>'Inventory','orders'=>'Orders'];
        foreach($menus as $key=>$label){
            $active = ($page==$key)?'active':'';
            echo '<a class="sidebar-link '.$active.'" href="?page='.$key.'">'.$label.'</a>';
        }

        echo '<a class="sidebar-link '.$active.'" href="index.php">Log out</a>';
        ?>
    </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold"><?= $menus[$page] ?></h2>

        <?php if($page=='sellers'): ?>
            <button onclick="document.getElementById('addSellerModal').classList.remove('hidden')" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Add Seller</button>
        <?php elseif($page=='products'): ?>
            <button onclick="document.getElementById('addProductModal').classList.remove('hidden')" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Add Product</button>
        <?php elseif($page=='inventory'): ?>
            <button onclick="document.getElementById('updateStockModal').classList.remove('hidden')" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Add / Update Inventory</button>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-6">
        <input type="hidden" name="page" value="<?= $page ?>">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name..." class="w-full p-3 rounded" style="background-color: var(--input-bg); color: var(--input-text); border: 1.5px solid var(--border-color); font-size:1rem;" />
    </form>

    <!-- Table -->
    <div class="bg-[var(--secondary-color)] rounded-xl overflow-hidden shadow-lg">
        <table class="w-full">
            <thead>
                <tr>
                    <?php
                    switch($page){
                        case 'customers': $cols=['ID','Name','Lat','Lng']; break;
                        case 'sellers': $cols=['ID','Name','Lat','Lng']; break;
                        case 'products': $cols=['ID','Name','Description']; break;
                        case 'inventory': $cols=['Seller','Product','Stock']; break;
                        case 'orders': $cols=['Order ID','Customer','Product','Seller','Qty','Timestamp']; break;
                        default: $cols = [];
                    }
                    foreach($cols as $c) echo "<th>$c</th>";
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $where = $search ? "WHERE name LIKE '%". $conn->real_escape_string($search) ."%'": '';
                switch($page){
                    case 'customers':
                        $res=$conn->query("SELECT * FROM customers $where");
                        while($r=$res->fetch_assoc()) echo "<tr><td>{$r['id']}</td><td>{$r['name']}</td><td>{$r['location_lat']}</td><td>{$r['location_lng']}</td></tr>";
                    break;
                    case 'sellers':
                        $res=$conn->query("SELECT * FROM sellers $where");
                        while($r=$res->fetch_assoc()) echo "<tr><td>{$r['id']}</td><td>{$r['name']}</td><td>{$r['location_lat']}</td><td>{$r['location_lng']}</td></tr>";
                    break;
                    case 'products':
                        $res=$conn->query("SELECT * FROM products $where");
                        while($r=$res->fetch_assoc()) echo "<tr><td>{$r['id']}</td><td>{$r['name']}</td><td>{$r['description']}</td></tr>";
                    break;
                    case 'inventory':
                        $res=$conn->query("SELECT i.seller_id,s.name AS seller,p.name AS product,i.stock_qty FROM inventory i JOIN sellers s ON i.seller_id=s.id JOIN products p ON i.product_id=p.id");
                        while($r=$res->fetch_assoc()) echo "<tr><td>{$r['seller']}</td><td>{$r['product']}</td><td>{$r['stock_qty']}</td></tr>";
                    break;
                    case 'orders':
                        $res = $conn->query("
                            SELECT 
                                o.order_id, 
                                c.name AS customer, 
                                p.name AS product, 
                                s.name AS seller, 
                                o.quantity, 
                                o.timestamp 
                            FROM orders o 
                            JOIN customers c ON o.customer_id = c.id 
                            JOIN products p ON o.product_id = p.id 
                            JOIN sellers s ON o.seller_id = s.id
                        ");
                        if($res){
                            while($r=$res->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$r['order_id']}</td>
                                    <td>{$r['customer']}</td>
                                    <td>{$r['product']}</td>
                                    <td>{$r['seller']}</td>
                                    <td>{$r['quantity']}</td>
                                    <td>{$r['timestamp']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No orders found or query error</td></tr>";
                        }
                    break;
                }
                ?>
            </tbody>
        </table>
    </div>
</main>
</div>

<!-- Modals -->
<!-- Add Seller -->
<div id="addSellerModal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
    <div class="bg-[var(--secondary-color)] p-6 rounded w-96 shadow-lg">
        <h2 class="text-xl font-bold mb-6">Add Seller</h2>
        <form method="POST" class="space-y-4">
            <input type="text" name="seller_name" placeholder="Name" required>
            <input type="number" step="0.0001" name="seller_lat" placeholder="Latitude" required>
            <input type="number" step="0.0001" name="seller_lng" placeholder="Longitude" required>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addSellerModal').classList.add('hidden')" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700 transition">Cancel</button>
                <button type="submit" name="add_seller" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Product -->
<div id="addProductModal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
    <div class="bg-[var(--secondary-color)] p-6 rounded w-96 shadow-lg">
        <h2 class="text-xl font-bold mb-6">Add Product</h2>
        <form method="POST" class="space-y-4">
            <input type="text" name="product_name" placeholder="Name" required>
            <textarea name="product_desc" placeholder="Description" rows="4" required class="resize-none"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addProductModal').classList.add('hidden')" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700 transition">Cancel</button>
                <button type="submit" name="add_product" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Stock -->
<div id="updateStockModal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
    <div class="bg-[var(--secondary-color)] p-6 rounded w-96 shadow-lg">
        <h2 class="text-xl font-bold mb-6">Update Stock</h2>
        <form method="POST" class="space-y-4">
            <select name="seller_id" required>
                <option value="">Select Seller</option>
                <?php $res=$conn->query("SELECT * FROM sellers"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id']}'>{$r['name']}</option>"; ?>
            </select>
            <select name="product_id" required>
                <option value="">Select Product</option>
                <?php $res=$conn->query("SELECT * FROM products"); while($r=$res->fetch_assoc()) echo "<option value='{$r['id']}'>{$r['name']}</option>"; ?>
            </select>
            <input type="number" name="stock_qty" placeholder="Stock Quantity" min="0" required>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('updateStockModal').classList.add('hidden')" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700 transition">Cancel</button>
                <button type="submit" name="update_stock" class="px-4 py-2 bg-[var(--primary-color)] rounded hover:bg-green-600 transition">Update</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
