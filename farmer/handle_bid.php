<?php
require_once '../includes/auth_middleware.php';
require_once '../config/database.php';
require_once '../includes/mailer.php';
check_farmer_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid_id']) && isset($_POST['action'])) {
    $bid_id = intval($_POST['bid_id']);
    $action = $_POST['action'];
    $farmer_id = $_SESSION['user_id'];

    // Verify the bid belongs to this farmer
    $check_sql = "SELECT b.*, p.quantity as available_quantity FROM bids b 
                  JOIN products p ON b.product_id = p.id 
                  WHERE b.id = ? AND b.farmer_id = ? AND b.status = 'pending'";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $bid_id, $farmer_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if ($bid = mysqli_fetch_assoc($result)) {
        if ($action === 'accept') {
            // Start transaction
            mysqli_begin_transaction($conn);
            try {
                // Update bid status
                $update_bid = "UPDATE bids SET status = 'accepted' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_bid);
                mysqli_stmt_bind_param($stmt, "i", $bid_id);
                mysqli_stmt_execute($stmt);

                // Create order
                $create_order = "INSERT INTO orders (buyer_id, seller_id, total_amount, status) 
                               VALUES (?, ?, ?, 'confirmed')";
                $stmt = mysqli_prepare($conn, $create_order);
                $total = $bid['bid_amount'] * $bid['quantity'];
                mysqli_stmt_bind_param($stmt, "iid", $bid['retailer_id'], $farmer_id, $total);
                mysqli_stmt_execute($stmt);
                $order_id = mysqli_insert_id($conn);

                // Create order item
                $create_item = "INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) 
                              VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $create_item);
                mysqli_stmt_bind_param($stmt, "iidd", $order_id, $bid['product_id'], $bid['quantity'], $bid['bid_amount']);
                mysqli_stmt_execute($stmt);

                // Update product quantity
                $new_quantity = $bid['available_quantity'] - $bid['quantity'];
                if ($new_quantity < 0) {
                    throw new Exception("Insufficient quantity available");
                }
                $update_product = "UPDATE products SET quantity = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_product);
                mysqli_stmt_bind_param($stmt, "di", $new_quantity, $bid['product_id']);
                mysqli_stmt_execute($stmt);

                // Commit transaction
                mysqli_commit($conn);
                sendBidStatusEmail(
                    $details['email'],
                    'accepted',
                    $details['product_name'],
                    $bid['quantity'],
                    $bid['bid_amount']
                );

                header("Location: dashboard.php?success=bid_accepted");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                header("Location: dashboard.php?error=" . urlencode($e->getMessage()));
                exit();
            }
        } elseif ($action === 'reject') {
            $update_sql = "UPDATE bids SET status = 'rejected' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "i", $bid_id);
            if (mysqli_stmt_execute($stmt)) {
                sendBidStatusEmail(
                    $details['email'],
                    'rejected', 
                    $details['product_name'],
                    $bid['quantity'],
                    $bid['bid_amount']
                );
                header("Location: dashboard.php?success=bid_rejected");
                exit();
            }
        }
    }
}

header("Location: dashboard.php?error=invalid_request");
exit();
