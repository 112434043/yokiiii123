<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_bites";

$conn = new mysqli($servername, $username, $password, $dbname);

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;

if ($order_id > 0 && !$conn->connect_error) {
    $result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - Campus Bites</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #faf5ee; color: #333; min-height: 100vh; display: flex; flex-direction: column; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 16px 40px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .logo-area h2 { color: #d84315; font-size: 24px; }
        .logo-area span { font-size: 12px; color: #777; }
        nav a { text-decoration: none; color: #444; font-weight: 500; margin-left: 20px; }
        .track-box { max-width: 520px; margin: 50px auto; background: #fff; padding: 35px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); text-align: center; }
        .order-id-badge { background: #ffeedd; color: #d84315; font-size: 20px; font-weight: 700; padding: 8px 20px; border-radius: 20px; display: inline-block; margin-bottom: 16px; }
        .status-badge { background: #e8f5e9; color: #2e7d32; padding: 6px 16px; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block; margin: 12px 0 20px; }
        .details-table { width: 100%; text-align: left; margin-top: 15px; border-collapse: collapse; }
        .details-table td { padding: 10px 0; border-bottom: 1px dashed #eee; font-size: 14px; }
        .btn-menu { display: inline-block; margin-top: 25px; background: #d84315; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>

<header>
    <div class="logo-area">
        <h2>Campus Bites</h2>
        <span>College Canteen</span>
    </div>
    <nav>
        <a href="home.html">Home</a>
        <a href="menu.html">Menu</a>
        <a href="cart.html">Cart</a>
        <a href="track.php" style="color: #d84315; font-weight: 600;">Track Order</a>
    </nav>
</header>

<div class="track-box">
    <?php if ($order): ?>
        <div class="order-id-badge">Order #<?php echo $order['id']; ?></div>
        <h2>Hello, <?php echo htmlspecialchars($order['student_name']); ?>!</h2>
        <div><span class="status-badge">Status: <?php echo htmlspecialchars($order['order_status']); ?></span></div>
        
        <table class="details-table">
            <tr><td><strong>Student ID:</strong></td><td><?php echo htmlspecialchars($order['student_id']); ?></td></tr>
            <tr><td><strong>Pickup Time:</strong></td><td><?php echo htmlspecialchars($order['pickup_time']); ?></td></tr>
            <tr><td><strong>Department:</strong></td><td><?php echo htmlspecialchars($order['department']); ?></td></tr>
            <tr><td><strong>Phone:</strong></td><td><?php echo htmlspecialchars($order['phone']); ?></td></tr>
            <tr><td><strong>Total Amount:</strong></td><td>₹<?php echo htmlspecialchars($order['total_amount']); ?></td></tr>
        </table>
    <?php else: ?>
        <h2>No Active Order Found</h2>
        <p style="color: #777; margin-top: 10px;">Please place an order from the cart page first.</p>
    <?php endif; ?>
    
    <a href="menu.html" class="btn-menu">&larr; Back to Menu</a>
</div>

</body>
</html>