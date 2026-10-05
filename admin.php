<?php
$conn = new mysqli('localhost', 'root', '', 'campus_bites');

// Shop Status மாற்ற
if (isset($_POST['toggle_shop'])) {
    $conn->query("UPDATE canteen_status SET is_open = NOT is_open WHERE id = 1");
}

// Order Status மாற்ற (Pending -> Confirmed -> Ready)
if (isset($_POST['update_order_status'])) {
    $oid = $_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("ss", $new_status, $oid);
    $stmt->execute();
}

$shop_status = $conn->query("SELECT is_open FROM canteen_status WHERE id = 1")->fetch_assoc()['is_open'];
$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$menu = $conn->query("SELECT * FROM menu_items");
?>
<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title>Canteen Admin Panel</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; padding: 20px; }
        .section { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #eee; padding: 10px; text-align: left; }
        th { background: #fafafa; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-green { background: #28a745; color: #fff; }
        .btn-orange { background: #fd7e14; color: #fff; }
    </style>
</head>
<body>

<h1>Campus Bites - Admin Panel</h1>

<div class="section">
    <h3>Shop Status: <?php echo $shop_status ? "<span style='color:green'>OPEN</span>" : "<span style='color:red'>CLOSED</span>"; ?></h3>
    <form method="POST">
        <button type="submit" name="toggle_shop" class="btn <?php echo $shop_status ? 'btn-orange' : 'btn-green'; ?>">
            <?php echo $shop_status ? 'Close Canteen' : 'Open Canteen'; ?>
        </button>
    </form>
</div>

<div class="section">
    <h3>Student Orders</h3>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Name</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($row = $orders->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $row['student_name']; ?></td>
            <td>₹<?php echo $row['total_amount']; ?></td>
            <td><strong><?php echo $row['status']; ?></strong></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <input type="hidden" name="update_order_status" value="1">
                    <button type="submit" name="status" value="Confirmed" class="btn">Confirm</button>
                    <button type="submit" name="status" value="Ready" class="btn btn-green">Mark Ready</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>