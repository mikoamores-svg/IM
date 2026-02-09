<?php
include("connection.php");

if (isset($_GET['order_id']) && isset($_GET['method'])) {
    $order_id = (int)$_GET['order_id'];
    $method = $_GET['method'];
    
    // We use 'order_id' here because that is the common primary key name in your setup
    $query = $connection->prepare("SELECT total_amount FROM orders WHERE order_id = ?");
    $query->bind_param("i", $order_id);
    $query->execute();
    $result = $query->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        $amount = $order['total_amount'];

        $connection->begin_transaction();
        try {
            // Insert into the payments table
            $stmt = $connection->prepare("INSERT INTO payment (order_id, amount_paid, payment_method) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $order_id, $amount, $method);
            $stmt->execute();

            // Update the order status. 
            // NOTE: Make sure you ran the ALTER TABLE SQL I gave you earlier!
            $update = $connection->prepare("UPDATE orders SET payment_status = 'Paid' WHERE order_id = ?");
            $update->bind_param("i", $order_id);
            $update->execute();

            $delivery = $connection->prepare("INSERT INTO delivery_management (order_id, status) VALUES (?, 'In Production')");
            $delivery->bind_param("i", $order_id);
            $delivery->execute();

            $connection->commit();

            header("Location: success.php?order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            $connection->rollback();
            die("Payment Error: " . $e->getMessage());
        }
    } else {
        die("Error: Order #" . $order_id . " not found in database.");
    }
}
?>