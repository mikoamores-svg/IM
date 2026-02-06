<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order — Gerbag Industrial</title>
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="order.css" />
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
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="nav-btn" onclick="location.href='products.php'">Products</button>
            <button class="nav-btn" onclick="location.href='contacts.php'">Contacts</button>
            
          </nav>
        </div>
      </header>

      <section class="hero hero-small">
        <div class="container">
          <h2>Place an Order</h2>
          <p class="lead">Fill customer details and select products below.</p>
        </div>
      </section>

      <main class="container order-layout">
        <section class="order-form-wrap">
          <h3>Customer Details</h3>
          <form id="orderForm" action="/api/orders" method="post" class="order-form" novalidate>
            <div class="customer-grid">
              <label class="field">
                <span>First name</span>
                <input name="customer[firstName]" id="customerFirst" required placeholder="First name" />
              </label>

              <label class="field">
                <span>Last name</span>
                <input name="customer[lastName]" id="customerLast" required placeholder="Last name" />
              </label>

              <label class="field">
                <span>Middle name</span>
                <input name="customer[middleName]" id="customerMiddle" placeholder="Middle name or N/A" />
              </label>

              <label class="field">
                <span>Company</span>
                <input name="customer[company]" id="customerCompany" placeholder="Company (optional)" />
              </label>

              <label class="field">
                <span>Email</span>
                <input type="email" name="customer[email]" id="customerEmail" required placeholder="you@example.com" />
              </label>

              <label class="field">
                <span>Phone</span>
                <input name="customer[phone]" id="customerPhone" placeholder="Contact number" />
              </label>
            </div>

            <h3 class="items-title">Order Items</h3>
            <p class="muted">Choose product and quantity. Click Add item to include more rows.</p>

            <table class="items-table" aria-label="Order items">
              <thead>
                <tr>
                  <th>Product</th>
                  <th style="width:280px">Quantity</th>
                  <th style="width:270px">Unit price (PHP)</th>
                  <th style="width:110px">Remove</th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                <!-- initial row inserted by JS -->
              </tbody>
            </table>

            <div class="table-actions">
              <button type="button" id="addItemBtn" class="nav-btn">Add Item</button>
            </div>

            <section class="estimator">
              <h4>Estimate</h4>
              <div class="estimator-controls">
                <label>
                  Delivery zone
                  <select id="deliveryZone">
                    <option value="MM">Metro Manila</option>
                    <option value="LUZ">Luzon (outside Metro Manila)</option>
                    <option value="VIS">Visayas</option>
                    <option value="MIN">Mindanao</option>
                  </select>
                </label>

                <label style="margin-left:1rem;">
                  <input type="checkbox" id="rushToggle" /> Rush (20% surcharge)
                </label>
              </div>

              <div class="estimate-values">
                <div>Subtotal: <span id="subtotal">₱0.00</span></div>
                <div>Delivery fee: <span id="deliveryFee">₱0.00</span></div>
                <div>Rush fee: <span id="rushFee">₱0.00</span></div>
                <div style="margin-top:0.25rem"><strong>Total estimate: <span id="totalEstimate">₱0.00</span></strong></div>
                <div id="estimateNote" class="muted" style="margin-top:0.5rem">If any item has no unit price, enter an estimated price in the Unit price field.</div>
              </div>
            </section>

            <div class="form-actions">
              <button type="submit" class="primary-btn">Submit Order</button>
              <button type="reset" class="nav-btn">Reset</button>
            </div>

            <div id="orderMessage" class="form-message" role="status" aria-live="polite"></div>
          </form>
        </section>
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
            <button class="nav-btn active" onclick="location.href='index.php'">Main</button>
            <button class="nav-btn" onclick="location.href='about.php'">About</button>
            <button class="nav-btn" onclick="location.href='services.php'">Services</button>
            <button class="primary-btn" onclick="location.href='products.php'">Products</button>
          </div>
        </div>
        <div class="copyright">© 2010 Gerbag Industrial Technologies Philippines. All rights reserved.</div>
      </footer>

    </div>

    <script>
      // Product list aggregated from site pages. Update IDs if backend provides SKU mapping.
      const PRODUCTS = [
        {id: 'SUPERSIZE_MC', name: 'SUPERSIZE MACHINING CENTER (2X3Meters)'},
        {id: 'ROBOTIC_WELDER', name: 'MULTI AXIS ROBOTIC WELDING MACHINE'},
        {id: 'LEADWELL_V40', name: 'MACHINING CENTER LEADWELL V-40'},
        {id: 'WIRE_EDM_FA10', name: 'WIRECUT-EDM MACHINE MITSUBISHI FA10'},

        {id: 'FLANGE_REFACING', name: 'FLANGE REFACING TOOL'},
        {id: 'TURNING_TOOL', name: 'TURNING TOOL'},
        {id: 'KEY_WAY_MILL', name: 'KEY WAY MILLING TOOL'},
        {id: 'BORING_SYSTEM', name: 'BORING SYSTEM'},
        {id: 'PORTABLE_MILL', name: 'PORTABLE MILLING SYSTEM'},

        {id: 'DYE_PEN_KITS', name: 'DYE PENETRANT INSPECTION KITS'},
        {id: 'MAG_PARTICLE', name: 'MAGNETIC PARTICLE INSPECTION'},
        {id: 'PORTABLE_HARDNESS', name: 'PORTABLE HARDNESS TESTER'},
        {id: 'ULTRASONIC_ISONIC2005', name: 'UNIVERSAL ULTRASONIC FLAW DETECTOR - ISONIC2005'},
        {id: 'ULTRASONIC_USM35', name: 'UNIVERSAL ULTRASONIC FLAW DETECTOR - KRAUTKRAMER USM35x'},
        {id: 'VIDEO_BORESCOPE', name: 'ARTICULATED VIDEO BORESCOPE'},
        {id: 'SURFACE_ROUGHNESS', name: 'SURFACE ROUGHNESS TESTER'},
        {id: 'THICKNESS_TESTER', name: 'THICKNESS TESTER'},

        {id: 'CONTROL_UNIT_QS', name: 'BUILT-IN CONTROL UNIT FOR QS'},
        {id: 'TRUE_COLOR_PROC', name: 'TRUE COLOR IMAGE PROCESSING FUNCTION'},
        {id: 'FIBER_OPTIC_LIGHT', name: 'FIBER OPTIC RING LIGHT'},
        {id: 'FARO_GAGE', name: 'FARO GAGE'},

        {id: 'CUTLESS_BEARINGS', name: 'CUTLESS BEARINGS'},
        {id: 'BABBITT_BEARINGS', name: 'THE BABBITT BEARINGS'},
        {id: 'MOLDED_RUBBER', name: 'MOLDED RUBBER PRODUCTS'},
        {id: 'RUBBER_EXP_JOINTS', name: 'RUBBER EXPANSION JOINTS'},
        {id: 'FLEXIBLE_CONNECTORS', name: 'FLEXIBLE RUBBER CONNECTORS'},
        {id: 'RUBBER_HOSES', name: 'INDUSTRIAL RUBBER HOSES'},
        {id: 'VACUUM_WAND', name: 'VACUUM WAND'}
      ];

      // Optional price list (in PHP). Fill with real base prices when available.
      const PRICES = {
        // sample entries (edit or replace with backend data)
        'LEADWELL_V40': 3500000,
        'WIRE_EDM_FA10': 2500000,
        'PORTABLE_MILL': 150000,
        'SUPERSIZE_MC': 12000000
      };

      // Delivery zone fees (PHP). Adjust to your shipping model.
      const DELIVERY_ZONES = {
        MM: { name: 'Metro Manila', fee: 200 },
        LUZ: { name: 'Luzon (outside Metro Manila)', fee: 500 },
        VIS: { name: 'Visayas', fee: 800 },
        MIN: { name: 'Mindanao', fee: 1000 }
      };

      const RUSH_MULTIPLIER = 0.20; // 20% surcharge when rush is selected

      const fmt = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

      const itemsBody = document.getElementById('itemsBody');
      const addItemBtn = document.getElementById('addItemBtn');
      const orderForm = document.getElementById('orderForm');
      const orderMessage = document.getElementById('orderMessage');

      function createItemRow(selectedId) {
        const tr = document.createElement('tr');

        const productTd = document.createElement('td');
        const select = document.createElement('select');
        select.name = 'items[][productId]';
        select.required = true;
        PRODUCTS.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name;
          if (p.id === selectedId) opt.selected = true;
          select.appendChild(opt);
        });
        productTd.appendChild(select);

        const qtyTd = document.createElement('td');
        const qty = document.createElement('input');
        qty.type = 'number'; qty.min = '1'; qty.value = '1'; qty.name = 'items[][quantity]'; qty.required = true; qty.className = 'qty';
        qtyTd.appendChild(qty);

        const priceTd = document.createElement('td');
        const price = document.createElement('input');
        price.type = 'number'; price.min = '0'; price.step = '0.01'; price.name = 'items[][unitPrice]'; price.className = 'unit-price';
        // pre-fill from PRICES if available for selected id
        const initId = selectedId || PRODUCTS[0].id;
        if (PRICES[initId]) price.value = PRICES[initId];
        priceTd.appendChild(price);

        const remTd = document.createElement('td');
        const remBtn = document.createElement('button');
        remBtn.type = 'button'; remBtn.className = 'nav-btn'; remBtn.textContent = 'Remove';
        remBtn.addEventListener('click', () => tr.remove());
        remTd.appendChild(remBtn);

        tr.appendChild(productTd);
        tr.appendChild(qtyTd);
        tr.appendChild(priceTd);
        tr.appendChild(remTd);

        // update price when product selection changes
        select.addEventListener('change', () => {
          const pid = select.value;
          if (PRICES[pid]) price.value = PRICES[pid];
          updateTotals();
        });

        // recalc when qty or price changes
        qty.addEventListener('input', updateTotals);
        price.addEventListener('input', updateTotals);

        // recalc when removing
        remBtn.addEventListener('click', updateTotals);

        return tr;
      }


      // initial row
      itemsBody.appendChild(createItemRow());

      addItemBtn.addEventListener('click', () => {
        itemsBody.appendChild(createItemRow());
        updateTotals();
      });

      // estimator elements
      const deliveryZone = document.getElementById('deliveryZone');
      const rushToggle = document.getElementById('rushToggle');
      const subtotalEl = document.getElementById('subtotal');
      const deliveryFeeEl = document.getElementById('deliveryFee');
      const rushFeeEl = document.getElementById('rushFee');
      const totalEstimateEl = document.getElementById('totalEstimate');
      const estimateNote = document.getElementById('estimateNote');

      function updateTotals() {
        let subtotal = 0;
        let missingPrice = false;
        document.querySelectorAll('#itemsBody tr').forEach(row => {
          const sel = row.querySelector('select');
          const q = row.querySelector('.qty');
          const p = row.querySelector('.unit-price');
          const qtyVal = parseInt(q?.value, 10) || 0;
          const unit = Number(p?.value);
          if (!p || isNaN(unit) || unit <= 0) {
            missingPrice = true;
          }
          const line = qtyVal * (isNaN(unit) ? 0 : unit);
          subtotal += line;
        });

        const zone = deliveryZone.value;
        const deliveryFee = (DELIVERY_ZONES[zone] && DELIVERY_ZONES[zone].fee) ? DELIVERY_ZONES[zone].fee : 0;
        const rushFee = rushToggle.checked ? Math.round((subtotal + deliveryFee) * RUSH_MULTIPLIER) : 0;
        const total = subtotal + deliveryFee + rushFee;

        subtotalEl.textContent = fmt.format(subtotal);
        deliveryFeeEl.textContent = fmt.format(deliveryFee);
        rushFeeEl.textContent = fmt.format(rushFee);
        totalEstimateEl.textContent = fmt.format(total);

        if (missingPrice) {
          estimateNote.textContent = 'Some items have no unit price — enter estimated prices to improve the total.';
        } else {
          estimateNote.textContent = 'Estimate is based on entered unit prices. Final price may vary.';
        }
      }

      // wire estimator controls
      deliveryZone.addEventListener('change', updateTotals);
      rushToggle.addEventListener('change', updateTotals);

      // wire dynamic inputs existing on page load
      document.addEventListener('input', (e) => {
        if (e.target && (e.target.classList.contains('qty') || e.target.classList.contains('unit-price'))) updateTotals();
      });

      // initial totals
      updateTotals();

      orderForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        orderMessage.textContent = '';
        orderMessage.className = 'form-message';

        const customer = {
          firstName: document.getElementById('customerFirst').value || '',
          lastName: document.getElementById('customerLast').value || '',
          middleName: (document.getElementById('customerMiddle').value || '').trim() || 'N/A',
          company: document.getElementById('customerCompany').value || '',
          email: document.getElementById('customerEmail').value || '',
          phone: document.getElementById('customerPhone').value || ''
        };

        const items = [];
        document.querySelectorAll('#itemsBody tr').forEach(row => {
          const sel = row.querySelector('select');
          const q = row.querySelector('.qty');
          const p = row.querySelector('.unit-price');
          if (sel && q) items.push({ productId: sel.value, quantity: parseInt(q.value, 10) || 1, unitPrice: Number(p?.value) || 0 });
        });

        if (items.length === 0) { orderMessage.textContent = 'Add at least one item.'; orderMessage.classList.add('error'); return; }

        const payload = { customer, items };

        try {
          const res = await fetch(orderForm.action, {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
          });
          if (!res.ok) throw new Error('Server returned ' + res.status);
          const data = await res.json().catch(() => ({}));
          orderMessage.textContent = data.message || 'Order submitted successfully.';
          orderMessage.classList.add('success');
          orderForm.reset();
          itemsBody.innerHTML = '';
          itemsBody.appendChild(createItemRow());
          // reset estimator UI
          deliveryZone.value = 'MM';
          rushToggle.checked = false;
          updateTotals();
        } catch (err) {
          orderMessage.textContent = 'Submission failed: ' + err.message;
          orderMessage.classList.add('error');
        }
      });
    </script>
  </body>
</html>
