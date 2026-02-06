<?php
include "connections.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name    = $_POST["name"] ?? "";
    $email   = $_POST["email"] ?? "";
    $phone   = $_POST["phone"] ?? "";
    $message = $_POST["message"] ?? "";

    $sql = "INSERT INTO contact (name, email, phone, message)
            VALUES (?, ?, ?, ?)";

    $stmt = $connections->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $message);
    $stmt->execute();

    $stmt->close();
}
?>


<!DOCTYPE html>
<form method="POST" action="<?php htmlspecialchars("contacts.php"); ?>">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Contact — Gerbag Industrial</title>
		<link rel="stylesheet" href="main.css" />
		<link rel="stylesheet" href="contacts.css" />
	</head>
	<body>
		<div class="site">
			<header class="site-header">
				<div class="container header-inner">
					<div class="brand">
						<img src="backatlogo/image.png" alt="Gerbag logo" class="logo" />
						<h1>GERBAG INDUSTRIAL TECHNOLOGIES</h1>
					</div>
					<nav class="site-nav" aria-label="Main navigation">
						<button class="nav-btn" onclick="location.href='index.php'">Main</button>
						<button class="nav-btn" onclick="location.href='about.php'">About</button>
						<button class="nav-btn" onclick="location.href='services.php'">Services</button>
						<button class="nav-btn" onclick="location.href='products.php'">Products</button>
						<button class="nav-btn active" onclick="location.href='contacts.php'">Contact</button>
						<button class="nav-btn" onclick="location.href='order.php'">Order</button>
					</nav>
				</div>
			</header>

			<section class="hero hero-small">
				<div class="container">
					<h2>Contact Us</h2>
					<p class="lead">We're ready to support your projects sales, service, and spare parts.</p>
				</div>
			</section>

			<main class="container contacts-layout">
				<section class="contacts-form-wrap">
					<h3>Send a Message</h3>
					  <form id="contactForm" class="contacts-form" action="/api/contact" method="post" novalidate data-endpoint="/api/contact">
						<div class="grid">
							<label class="field">
								<span for="name">Name</span>
								<input type="text" name="name" required placeholder="Your full name" />
							</label>

							<label class="field">
								<span for="email">Email</span>
								<input type="email" name="email" required placeholder="you@example.com" />
							</label>

							<label class="field">
								<span for="phone">Phone</span>
								<input type="tel" name="phone" placeholder="Optional" />
							</label>

							<label class="field field-full">
								<span for="message">Message</span>
								<textarea name="message" rows="6" required placeholder="How can we help?"></textarea>
							</label>
						</div>

						<div class="form-actions">
							<button type="submit" class="primary-btn">Send Message</button>
							<button type="reset" class="nav-btn">Reset</button>
						</div>
					</form>
				</section>

				<aside class="contacts-info">
					<h3>Contact Details</h3>
					<p class="org"><strong>Gerbag Industrial Technologies Philippines</strong></p>
					<address>
						<div>Phone: <a href="tel:+6328382976">+(632) 838-2976</a></div>
						<div>Phone: <a href="tel:+6328385976">+(632) 838-5976</a></div>
						<div>Phone: <a href="tel:+6328384901">+(632) 838-4901</a></div>
						<div>Fax: +(632) 837-4073</div>
						<div>Email: <a href="mailto:info@gerbag.ph">info@gerbag.ph</a></div>
					</address>

						<div class="map-placeholder" aria-hidden="true">Map placeholder</div>
						<div id="contactMessage" class="form-message" role="status" aria-live="polite"></div>
					</aside>
					</main>

				</aside>
			</main>

			<footer class="site-footer">
				<div class="container footer-grid">
					<div>
						<strong>Gerbag Industrial Technologies Philippines</strong>
						<address>
							Phone: +(632) 838-2976 | +(632) 838-5976 | +(632) 838-4901
						</address>
					</div>

					<div class="footer-actions">
						<button class="nav-btn" onclick="location.href='index.php'">Main</button>
						<button class="nav-btn" onclick="location.href='about.php'">About</button>
						<button class="nav-btn" onclick="location.href='services.php'">Services</button>
						<button class="primary-btn" onclick="location.href='products.php'">Products</button>
					</div>
				</div>
				<div class="copyright">© 2010 Gerbag Industrial Technologies Philippines. All rights reserved.</div>
			</footer>
		</div>
	</body>
	</form>
</html>
