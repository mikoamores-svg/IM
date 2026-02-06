<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta
      name="description"
      content="Gerbag Industrial Technologies Philippines - Expanding Possibilities. Innovating Solutions. Industrial equipment and machinery supplier."
    />
    <title>Products - Gerbag Industrial Technologies Philippines</title>

    <link rel="stylesheet" href="products.css" />
  </head>
  <body>
    <div class="products">
      <div class="site-container">
        <header class="site-header">
          <img src="backatlogo/image.png" alt="Gerbag logo" />
          <div>
            <div class="site-title">GERBAG INDUSTRIAL TECHNOLOGIES PHILIPPINES</div>
            <div class="site-tag">Expanding Possibilities. Innovating Solutions</div>
          </div>
          <nav class="top-nav" aria-label="Main navigation">
            <button class="nav-btn" type="button" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" type="button" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" type="button" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" type="button" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" type="button" onclick="location.href='contacts.php'">Contacts</button>
            <button class="nav-btn" type="button" onclick="location.href='order.php'">Order</button>
          </nav>
        </header>

        <div class="layout">
          <aside class="sidebar" aria-label="Categories">
            <h2>Categories</h2>
            <div class="category-list">
              <button class="category-btn active" type="button" onclick="location.href='products.php'">FOREFRONT MAJOR MACHINERIES/EQUIPMENTS</button>
              <button class="category-btn" type="button" onclick="location.href='category1.php'">ON-SITE MACHINING EQUIPMENTS</button>
              <button class="category-btn" type="button" onclick="location.href='category2.php'">NON-DESTRUCTIVE TESTING EQUIPMENTS &amp; OTHER SPECIALIZED TOOLS</button>
              <button class="category-btn" type="button" onclick="location.href='category3.php'">TECHNOLOGICALLY ADVANCE PRECISION MEASURING EQUIPMENT</button>
              <button class="category-btn" type="button" onclick="location.href='category4.php'">SPECIAL PRODUCTS</button>
            </div>
          </aside>

          <main class="main-content">
            <h2>EQUIPMENTS</h2>

            <div class="equipments-grid">
              <article class="card">
                <img src="equipments/equipment1.png" alt="Supersize Machining Center" />
                <h3>SUPERSIZE MACHINING CENTER (2X3Meters)</h3>
                <p>Large double-column vertical machining center for oversized parts and molds.</p>
              </article>

              <article class="card">
                <img src="equipments/equipment2.png" alt="Multi Axis Robotic Welding Machine" />
                <h3>MULTI AXIS ROBOTIC WELDING MACHINE</h3>
                <p>Automated multi-axis robotic welding cells for precision welding.</p>
              </article>

              <article class="card">
                <img src="equipments/equipment3.png" alt="Leadwell V-40 Machining Center" />
                <h3>MACHINING CENTER LEADWELL V-40</h3>
                <p>Leadwell V-40 vertical machining center for precision milling operations.</p>
              </article>

              <article class="card">
                <img src="equipments/equipment4.png" alt="Mitsubishi FA10 Wire EDM" />
                <h3>WIRECUT-EDM MACHINE MITSUBISHI FA10</h3>
                <p>High-precision Wire EDM for complex mold-making and tooling.</p>
              </article>
            </div>

            <!-- gallery removed per request -->
          </main>
        </div>

        <footer class="site-footer">
          <div class="links">
            <button class="nav-btn" type="button" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" type="button" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" type="button" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" type="button" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" type="button" onclick="location.href='order.php'">Order</button>
          </div>
          <div>
            <a class="cta" href="contacts.php">CONTACT US</a>
          </div>
        </footer>
      </div>
    </div>
  </body>
</html>