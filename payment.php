<?php
$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : 'N/A';
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Payment — Gerbag Industrial</title>
    <link rel="stylesheet" href="order.css">
    <style>
        .payment-box { max-width: 500px; margin: 50px auto; padding: 2.5rem; border: 1px solid #ddd; border-radius: 8px; text-align: center; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .pay-option { display: block; width: 100%; padding: 1rem; margin: 12px 0; border: 1px solid #007bff; border-radius: 5px; cursor: pointer; font-weight: bold; background: #f8f9ff; transition: 0.3s; color: #007bff; }
        .pay-option:hover { background: #007bff; color: #fff; }
    </style>
</head>
<body>
    <div class="site">
        <div class="payment-box">
            <h2>Complete Your Payment</h2>
            <p>Order Reference: <strong>#<?php echo $order_id; ?></strong></p>
            <p style="font-size: 1.8rem; color: #333;">Total: <strong>₱<?php echo number_format($amount, 2); ?></strong></p>
            <hr style="margin: 20px 0; opacity: 0.2;">
            <p>Choose a payment method to finish your order:</p>
            
            <button class="pay-option" onclick="location.href='process_payment.php?order_id=<?php echo $order_id; ?>&method=GCash'">Pay via GCash</button>

<button class="pay-option" onclick="location.href='process_payment.php?order_id=<?php echo $order_id; ?>&method=Maya'">Pay via Maya</button>

<button class="pay-option" onclick="location.href='process_payment.php?order_id=<?php echo $order_id; ?>&method=Bank Transfer'">Bank Transfer</button>
            
            <a href="index.php" style="display:block; margin-top:20px; font-size:0.9rem; color:#888;">Cancel and return to Home</a>
        </div>
    </div>
</body>
</html>