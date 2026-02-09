<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Order Success — Gerbag Industrial</title>
    <link rel="stylesheet" href="order.css">
    <style>
        .success-wrap { max-width: 600px; margin: 80px auto; text-align: center; padding: 3rem; background: white; border-radius: 10px; }
        .icon { font-size: 50px; color: #28a745; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="success-wrap">
        <div class="icon">✔</div>
        <h1>Order Placed Successfully!</h1>
        <p>Your order <strong>#<?php echo htmlspecialchars($_GET['order_id'] ?? 'Unknown'); ?></strong> has been received.</p>
        <p>We are now preparing your industrial parts. A confirmation has been sent to your email.</p>
        <br>
        <button class="nav-btn active" onclick="location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>