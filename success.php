<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Order Success — Gerbag Industrial</title>
    <link rel="stylesheet" href="order.css">
    <style>
        body { background: #f5f5f5; }
        .success-wrap { max-width: 600px; margin: 80px auto; text-align: center; padding: 3rem; background: white; border-radius: 10px; }
        .icon { font-size: 50px; color: #000000; margin-bottom: 20px; }
        .success-wrap h1 { color: #333; font-size: 2rem; margin: 1rem 0; }
        .success-wrap p { color: #555; font-size: 1rem; line-height: 1.6; }
        .success-wrap strong { color: #000; }
        .nav-btn { padding: 12px 30px; margin-top: 20px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; font-weight: bold; }
        .nav-btn:hover { background: #0056b3; }
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