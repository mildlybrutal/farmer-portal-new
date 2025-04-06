<?php
require_once '../includes/auth_middleware.php';
require_once '../config/database.php';
check_retailer_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $farmer_id = intval($_POST['farmer_id']);
    $retailer_id = $_SESSION['user_id'];
    $quantity = floatval($_POST['quantity']);
    $bid_amount = floatval($_POST['bid_amount']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Verify product exists and has sufficient quantity
    $check_sql = "SELECT quantity FROM products WHERE id = ? AND farmer_id = ? AND status = 'available'";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $product_id, $farmer_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if ($product = mysqli_fetch_assoc($result)) {
        if ($quantity <= $product['quantity']) {
            // Place the bid
            $sql = "INSERT INTO bids (retailer_id, farmer_id, product_id, quantity, bid_amount, message) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiidds", $retailer_id, $farmer_id, $product_id, $quantity, $bid_amount, $message);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: dashboard.php?success=bid_placed");
                exit();
            } else {
                header("Location: dashboard.php?error=bid_failed");
                exit();
            }
        } else {
            header("Location: dashboard.php?error=insufficient_quantity");
            exit();
        }
    }
}

header("Location: dashboard.php?error=invalid_product");
exit();
