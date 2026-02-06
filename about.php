<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="about.css" />
    <title>About — Gerbag Industrial</title>
  </head>
  <body>
    <div class="about">
      <header class="site-header">
        <div class="container">
          <div class="brand">
            <img src="backatlogo/image.png" alt="Gerbag logo" />
            <h1>GERBAG INDUSTRIAL TECHNOLOGIES</h1>
          </div>
          <nav class="site-nav" aria-label="Main navigation">
            <button class="nav-btn" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn active">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contacts</button>
            <button class="nav-btn" onclick="location.href='order.php'">Order</button>
          </nav>
        </div>
      </header>

      <section class="hero">
        <div class="hero-bg" style="background-image: url('backatlogo/aboutback.png')" aria-hidden="true"></div>
        <div class="container hero-content">
          <h2>Expanding Possibilities. Innovating Solutions.</h2>
          <p class="lead">GERBAG INDUSTRIAL TECHNOLOGIES PHILIPPINES — reliable engineering, fabrication, and maintenance.</p>
        </div>
      </section>

      <main class="container main-grid" id="main">
        <article class="mission">
          <h3>About Gerbag</h3>
          <p>
            Gerbag Industrial Technologies Philippines is a 100% Filipino-owned company founded by Mr. Geronimo V.
            Bagacina Jr. We deliver high-level engineering and technological capabilities with a focus on reliable,
            locally made equipment and dependable refurbishment and maintenance services.
          </p>

          <h4>Our Mission</h4>
          <p>
            Our mission is to develop reliable locally made machines and components, provide dependable refurbishment
            and maintenance, and support domestic power plants and other industries with quality work at reasonable cost.
          </p>

          <div class="cta-row">
            <button class="primary-btn" onclick="location.href='contacts.php'">Contact Us</button>
            <button class="ghost-btn" onclick="location.href='services.php'">Our Services</button>
          </div>
        </article>

        <aside class="capabilities">
          <h4>Highlights of Our Capability</h4>
          <ul class="features">
            <li><img src="backatlogo/check.png" alt="ok" /> CNC machining center up to 10ft × 7ft</li>
            <li><img src="backatlogo/check.png" alt="ok" /> FARO CMM &amp; precision metrology</li>
            <li><img src="backatlogo/check.png" alt="ok" /> Licensed CAD/CAM software (Cimatron, Alibre)</li>
            <li><img src="backatlogo/check.png" alt="ok" /> Robotic welding &amp; NDT services</li>
            <li><img src="backatlogo/check.png" alt="ok" /> Rehabilitation for power-plant equipment</li>
          </ul>
        </aside>
      </main>

      <footer class="site-footer">
        <div class="container footer-grid">
          <div>
            <strong>Gerbag Industrial Technologies Philippines</strong>
            <address>
              Phone: +(632) 838-2976 | +(632) 838-5976 | +(632) 838-4901<br />Fax: +(632) 837-4073
            </address>
          </div>

          <div class="footer-actions">
            <button class="nav-btn" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="primary-btn" onclick="location.href='contacts.php'">Contact Us</button>
          </div>
        </div>
        <div class="copyright">© 2010 Gerbag Industrial Technologies Philippines. All rights reserved.</div>
      </footer>
    </div>
  </body>
</html>