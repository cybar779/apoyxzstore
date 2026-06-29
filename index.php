<?php
// index.php - RASCAL STORE PREMIUM CLONE
// Sistem tanpa MySQL, pakai JSON sebagai database

// --- SETUP DATA JSON (Otomatis bikin file kalau belum ada) ---
$p_file = 'products.json';
$o_file = 'orders.json';
$c_file = 'cheats.json';

if (!file_exists($p_file)) {
    file_put_contents($p_file, json_encode([
        ['id'=>1, 'name'=>'DRIP CLIENT APKMOD', 'cat'=>'Android', 'price'=>18500, 'img'=>'https://via.placeholder.com/400x300/6d28d9/ffffff?text=DRIP+APKMOD'],
        ['id'=>2, 'name'=>'DRIP CLIENT PROXY', 'cat'=>'Android', 'price'=>18500, 'img'=>'https://via.placeholder.com/400x300/6d28d9/ffffff?text=DRIP+PROXY'],
        ['id'=>3, 'name'=>'PATO TEAM APKMOD', 'cat'=>'Android', 'price'=>25000, 'img'=>'https://via.placeholder.com/400x300/f97316/ffffff?text=PATO+TEAM'],
        ['id'=>4, 'name'=>'HG CHEATS APKMOD', 'cat'=>'Android', 'price'=>15000, 'img'=>'https://via.placeholder.com/400x300/d946ef/ffffff?text=HG+CHEATS'],
        ['id'=>5, 'name'=>'BR MODS PC', 'cat'=>'PC', 'price'=>45000, 'img'=>'https://via.placeholder.com/400x300/0ea5e9/ffffff?text=BR+MODS+PC'],
        ['id'=>6, 'name'=>'MIGUL FREE FIRE PRO', 'cat'=>'iOS', 'price'=>50000, 'img'=>'https://via.placeholder.com/400x300/10b981/ffffff?text=MIGUL+IOS'],
    ]));
}
if (!file_exists($o_file)) file_put_contents($o_file, json_encode([]));
if (!file_exists($c_file)) {
    file_put_contents($c_file, json_encode([
        ['name'=>'DRIP CLIENT APKMOD', 'cat'=>'Android', 'status'=>'online'],
        ['name'=>'DRIP CLIENT PROXY', 'cat'=>'Android', 'status'=>'online'],
        ['name'=>'DRIP 8BALL POLL', 'cat'=>'Android', 'status'=>'maintenance'],
        ['name'=>'PATO TEAM REGEDIT', 'cat'=>'Android', 'status'=>'maintenance'],
        ['name'=>'BR MODS PC', 'cat'=>'PC', 'status'=>'online'],
        ['name'=>'MIGUL FREE FIRE PRO', 'cat'=>'iOS', 'status'=>'online'],
    ]));
}

// --- FUNGSI BACA/TULIS JSON ---
function jread($f) { return json_decode(file_get_contents($f), true); }
function jwrite($f, $d) { file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT)); }

// --- ROUTING HALAMAN ---
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = null;

// Ambil produk detail buat checkout
$products = jread($p_file);
$product = null;
if ($page == 'checkout' && $id > 0) {
    foreach ($products as $p) { if ($p['id'] == $id) { $product = $p; break; } }
}

// Proses Checkout (Simpan Order)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'checkout') {
    $orders = jread($o_file);
    $newOrder = [
        'id' => 'ORD-'.date('Ymd').rand(100,999),
        'date' => date('Y-m-d H:i:s'),
        'name' => $_POST['nama'],
        'wa' => $_POST['wa'],
        'product_id' => $_POST['product_id'],
        'package' => $_POST['package'],
        'qty' => $_POST['qty'],
        'total' => $_POST['total'],
        'status' => 'PENDING'
    ];
    $orders[] = $newOrder;
    jwrite($o_file, $orders);
    header("Location: ?page=payment&id=".$newOrder['id']);
    exit;
}

// Ambil data order untuk halaman pembayaran
if ($page == 'payment' && isset($_GET['id'])) {
    $orders = jread($o_file);
    foreach ($orders as $o) { if ($o['id'] == $_GET['id']) { $order = $o; break; } }
}

// --- HTML & CSS ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RASCAL STORE</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:0; }
        body { background: #0f0a1e; color: #fff; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 15px; }
        
        /* HEADER & SIDEBAR */
        header { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #1e1832; }
        .logo { font-size: 20px; font-weight: 800; color: #d946ef; text-transform: uppercase; letter-spacing: 1px; }
        .header-right { display: flex; gap: 15px; align-items: center; font-size: 14px; color: #9ca3af; }
        .menu-btn { font-size: 24px; cursor: pointer; color: #fff; }

        /* SIDEBAR OVERLAY */
        .sidebar { position: fixed; right: -100%; top: 0; width: 80%; max-width: 320px; height: 100%; background: #151127; z-index: 9999; transition: 0.4s ease; box-shadow: -5px 0 15px rgba(0,0,0,0.7); padding: 20px; border-left: 1px solid #2a2540; }
        .sidebar.open { right: 0; }
        .sidebar-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2a2540; padding-bottom: 15px; }
        .sidebar h3 { color: #d946ef; font-size: 18px; }
        .close-btn { font-size: 24px; cursor: pointer; color: #9ca3af; }
        .sidebar-item { background: #1a1528; padding: 12px 15px; border-radius: 10px; margin: 10px 0; border: 1px solid #2a2540; display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .sidebar-item:hover { border-color: #d946ef; }

        /* BANNER */
        .banner { background: radial-gradient(circle at 30% 30%, #2d1f4e, #0f0a1e); border-radius: 20px; padding: 20px; margin: 15px 0; border: 1px solid #2a2540; position: relative; }
        .banner h1 { font-size: 24px; color: #fff; text-shadow: 0 0 10px #d946ef; display: flex; align-items: center; gap: 10px; }
        .banner .sub { display: flex; gap: 8px; flex-wrap: wrap; margin: 10px 0; }
        .banner .sub span { background: #0f0a1e; padding: 4px 12px; border-radius: 15px; font-size: 12px; border: 1px solid #333; }
        .badge-time { background: #1e1832; padding: 5px 15px; border-radius: 20px; font-size: 12px; border: 1px solid #d946ef; }

        /* FILTER */
        .filters { display: flex; gap: 10px; margin: 15px 0; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; border-radius: 20px; background: #1a1528; border: 1px solid #2a2540; color: #fff; cursor: pointer; font-size: 13px; }
        .filter-btn.active { background: #d946ef; border-color: #d946ef; color: #fff; }

        /* PRODUCT GRID */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        @media(min-width: 768px) { .grid { grid-template-columns: repeat(3, 1fr); } }
        .card { background: #1a1528; border-radius: 12px; border: 1px solid #2a2540; overflow: hidden; transition: 0.3s; display: flex; flex-direction: column; }
        .card:hover { border-color: #d946ef; transform: translateY(-2px); }
        .card-img { width: 100%; aspect-ratio: 1/1; object-fit: cover; background: #151127; }
        .card-body { padding: 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .card-cat { font-size: 10px; color: #d946ef; background: #271a3a; padding: 2px 8px; border-radius: 10px; display: inline-block; margin-bottom: 5px; }
        .card-title { font-size: 14px; font-weight: 600; margin-bottom: 5px; line-height: 1.2; }
        .card-sub { font-size: 11px; color: #9ca3af; display: flex; align-items: center; gap: 5px; margin-bottom: 8px; }
        .card-btn { background: #d946ef; text-align: center; padding: 8px 0; border-radius: 8px; font-size: 13px; font-weight: bold; color: #fff; display: block; margin-top: auto; }

        /* CHECKOUT PAGE */
        .back-link { display: inline-block; color: #9ca3af; margin: 10px 0; font-size: 14px; }
        .checkout-title { text-align: center; font-size: 22px; color: #d946ef; margin: 15px 0; }
        .step-box { background: #1a1528; border-radius: 12px; padding: 15px; margin: 10px 0; border: 1px solid #2a2540; }
        .step-num { background: #d946ef; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; margin-right: 8px; }
        input, select { width: 100%; padding: 12px; background: #0f0a1e; border: 1px solid #2a2540; border-radius: 8px; color: #fff; margin: 5px 0; outline: none; }
        input:focus { border-color: #d946ef; }

        .pkg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .pkg-item { background: #0f0a1e; padding: 12px; border-radius: 10px; border: 1px solid #2a2540; text-align: center; cursor: pointer; }
        .pkg-item.active { border-color: #d946ef; background: #1e1832; }
        .price-now { color: #10b981; font-weight: bold; font-size: 16px; }
        .price-old { text-decoration: line-through; color: #6b7280; font-size: 12px; }
        
        .qty-ctrl { display: flex; align-items: center; justify-content: center; gap: 15px; background: #0f0a1e; border-radius: 10px; padding: 10px; margin-top: 15px; border:1px solid #2a2540; }
        .qty-btn { background: #2a2540; border: none; color: #fff; width: 36px; height: 36px; border-radius: 8px; font-size: 20px; cursor: pointer; }
        .qty-input { width: 40px; text-align: center; background: transparent; border: none; color: #fff; font-size: 18px; }

        .sticky-footer { position: sticky; bottom: 0; background: #0f0a1e; padding: 12px 15px; border-top: 1px solid #2a2540; display: flex; justify-content: space-between; align-items: center; }
        .sticky-total .lbl { font-size: 12px; color: #9ca3af; }
        .sticky-total .val { font-size: 22px; font-weight: bold; color: #d946ef; }
        .pay-now { background: #d946ef; border: none; color: #fff; padding: 10px 25px; border-radius: 10px; font-weight: bold; font-size: 16px; cursor: pointer; }

        /* PAYMENT OVERLAY */
        .payment-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); display: flex; justify-content: center; align-items: center; z-index: 9999; backdrop-filter: blur(5px); }
        .payment-modal { background: #151127; max-width: 420px; width: 95%; border-radius: 20px; padding: 20px; border: 1px solid #2a2540; max-height: 90vh; overflow-y: auto; }
        .modal-header { text-align: center; background: linear-gradient(135deg, #d946ef, #a855f7); margin: -20px -20px 15px -20px; padding: 20px; border-radius: 20px 20px 0 0; position: relative; }
        .modal-header h3 { color: #fff; margin: 0; font-size: 18px; }
        .modal-header p { color: #e0d4f5; font-size: 13px; margin: 0; }
        .modal-close { position: absolute; top: 15px; right: 15px; color: #fff; font-size: 20px; cursor: pointer; background: rgba(0,0,0,0.3); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        
        .timer { text-align: center; font-size: 28px; font-weight: bold; color: #ef4444; margin: 5px 0 15px 0; }
        .qr-area { background: #fff; padding: 15px; border-radius: 15px; text-align: center; margin-bottom: 15px; position: relative; }
        .qr-area img { width: 160px; height: 160px; }
        .qr-badge { background: #ef4444; color: #fff; font-size: 10px; font-weight: bold; padding: 3px 10px; border-radius: 10px; position: absolute; top: -10px; left: 50%; transform: translateX(-50%); }
        .qr-dl { background: #d946ef; border: none; color: #fff; padding: 8px 20px; border-radius: 20px; font-size: 13px; cursor: pointer; margin: 10px 0; }
        .pay-logo { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .pay-logo span { background: #fff; padding: 4px 10px; border-radius: 5px; font-size: 10px; font-weight: bold; color: #333; }

        .detail-box { background: #0f0a1e; border-radius: 10px; padding: 12px; margin: 10px 0; border: 1px solid #2a2540; }
        .detail-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; border-bottom: 1px solid #1e1832; }
        .detail-row:last-child { border-bottom: none; font-weight: bold; font-size: 16px; padding-top: 8px; }
        
        .checking-box { border: 1px dashed #d946ef; border-radius: 10px; padding: 12px; text-align: center; color: #10b981; background: #0f0a1e; margin: 10px 0; }
        .license-key { color: #10b981; font-weight: bold; letter-spacing: 2px; }
        
        .status-pending { background: #f59e0b; color: #000; padding: 3px 12px; border-radius: 10px; font-size: 12px; font-weight: bold; }
        .batal-btn { display: block; background: #ef4444; color: #fff; text-align: center; padding: 12px; border-radius: 10px; font-weight: bold; margin-top: 10px; }

        /* STATUS PAGE */
        .status-section { margin: 15px 0; }
        .status-section h4 { color: #d946ef; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
        .status-item { display: flex; justify-content: space-between; background: #1a1528; padding: 12px 15px; border-radius: 10px; margin: 8px 0; border: 1px solid #2a2540; }
        .st-online { background: #10b981; color: #fff; padding: 2px 12px; border-radius: 12px; font-size: 11px; }
        .st-maint { background: #f59e0b; color: #000; padding: 2px 12px; border-radius: 12px; font-size: 11px; }
    </style>
</head>
<body>

<!-- SIDEBAR MENU -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>MENU</h3>
        <div class="close-btn" onclick="toggleSidebar()">✕</div>
    </div>
    <div class="sidebar-item">🌙 Mode Gelap (Toggle)</div>
    <div class="sidebar-item">👤 Login / Registrasi</div>
    <div class="sidebar-item">🔍 Lacak Pesanan</div>
    <div class="sidebar-item">⬇️ Download All Cheat</div>
    <a href="?page=status" class="sidebar-item">📊 Status Cheat</a>
    <div class="sidebar-item">💬 Saluran WhatsApp</div>
    <div class="sidebar-item">🎵 TikTok</div>
</div>

<div class="container">
    <!-- HEADER -->
    <header>
        <div class="logo">RASCAL STORE</div>
        <div class="header-right">
            <span id="jam"></span>
            <span>👤</span>
            <span class="menu-btn" onclick="toggleSidebar()">☰</span>
        </div>
    </header>

    <!-- ROUTING PHP -->
    <?php if ($page == 'home'): ?>
        <!-- BANNER -->
        <div class="banner">
            <h1>⚡ PROSES CEPAT SECEPAT KILAT!</h1>
            <div class="sub">
                <span>🚀 PROSES CEPAT</span>
                <span>⚡ SECEPAT KILAT</span>
                <span class="badge-time">⏱ 1-3 MENIT</span>
            </div>
            <div style="margin-top:10px; display:flex; gap:5px; flex-wrap:wrap; font-size:11px; color:#9ca3af;">
                <span>QRIS</span> <span>OVO</span> <span>GoPay</span> <span>LinkAja</span> <span>BNI</span> <span>BCA</span> <span>DANA</span>
            </div>
            <div style="margin-top:15px; font-size:12px; color:#a855f7; border-top:1px solid #2a2540; padding-top:10px;">
                RASCAL STORE TRUSTED ALL RESERVED SINCE 2020 ALL TRANSAKSI NO REFUND ⚡
            </div>
        </div>

        <!-- FILTER -->
        <div class="filters">
            <button class="filter-btn active" onclick="filter('all')">ALL</button>
            <button class="filter-btn" onclick="filter('Android')">📱 ANDROID</button>
            <button class="filter-btn" onclick="filter('iOS')">🍎 IOS</button>
            <button class="filter-btn" onclick="filter('PC')">💻 PC</button>
        </div>

        <!-- PRODUK -->
        <h2 style="font-size:16px; margin:15px 0; color:#fff;">🔥 PRODUK POPULER</h2>
        <div class="grid" id="produkGrid">
            <?php foreach($products as $p): ?>
            <div class="card" data-cat="<?= $p['cat'] ?>">
                <img src="<?= $p['img'] ?>" class="card-img" alt="<?= $p['name'] ?>">
                <div class="card-body">
                    <div>
                        <span class="card-cat"><?= $p['cat'] ?></span>
                        <div class="card-title"><?= $p['name'] ?></div>
                        <div class="card-sub">🔒 <?= $p['cat'] ?> NO ROOT</div>
                    </div>
                    <a href="?page=checkout&id=<?= $p['id'] ?>" class="card-btn">🛒 BUY</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($page == 'checkout' && $product): ?>
        <a href="?page=home" class="back-link">← Kembali</a>
        <div class="checkout-title"><?= $product['name'] ?></div>
        
        <!-- FITUR UTAMA DUMMY -->
        <div class="step-box" style="border-left:3px solid #d946ef;">
            <div style="font-size:13px; font-weight:bold; margin-bottom:5px;">FITUR UTAMA :</div>
            <div style="font-size:12px; color:#9ca3af;">
                ✓ Drip client <br>
                ✓ All fitur safe <br>
                ✓ Aimbot <br>
                ✓ Aim rage <br>
                ✓ Speed hack <br>
                ✓ Fly <br>
            </div>
        </div>

        <form method="POST" id="formCheckout">
            <input type="hidden" name="action" value="checkout">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

            <div class="step-box">
                <div class="step-num">1</div> <strong>DATA PEMBELI</strong>
                <input type="text" name="nama" placeholder="Masukan Nama Asli (Anda)" required>
                <div style="display:flex; gap:10px; align-items:center;">
                    <select style="width:40%;"><option>🇮🇩 +62</option></select>
                    <input type="text" name="wa" placeholder="Masukkan no WhatsApp" required style="width:60%;">
                </div>
            </div>

            <div class="step-box">
                <div class="step-num">2</div> <strong>PILIH ITEM & JUMLAH</strong>
                <div class="pkg-grid">
                    <label class="pkg-item active" onclick="pilihPaket(this, 18500)">
                        <input type="radio" name="package" value="1 Day" hidden checked>
                        <strong>1 Day</strong><br>
                        <span class="price-old">Rp20.000</span> <br>
                        <span class="price-now">Rp18.500</span>
                    </label>
                    <label class="pkg-item" onclick="pilihPaket(this, 35000)">
                        <input type="radio" name="package" value="3 Day" hidden>
                        <strong>3 Day</strong><br>
                        <span class="price-old">Rp55.000</span> <br>
                        <span class="price-now">Rp35.000</span>
                    </label>
                    <label class="pkg-item" onclick="pilihPaket(this, 65000)">
                        <input type="radio" name="package" value="7 Day" hidden>
                        <strong>7 Day</strong><br>
                        <span class="price-old">Rp100.000</span> <br>
                        <span class="price-now">Rp65.000</span>
                    </label>
                    <label class="pkg-item" onclick="pilihPaket(this, 165000)">
                        <input type="radio" name="package" value="15 Day" hidden>
                        <strong>15 Day</strong><br>
                        <span class="price-old">Rp200.000</span> <br>
                        <span class="price-now">Rp165.000</span>
                    </label>
                    <label class="pkg-item" onclick="pilihPaket(this, 220000)">
                        <input type="radio" name="package" value="30 Day" hidden>
                        <strong>30 Day</strong><br>
                        <span class="price-old">Rp300.000</span> <br>
                        <span class="price-now">Rp220.000</span>
                    </label>
                </div>
                
                <div class="qty-ctrl">
                    <button type="button" class="qty-btn" onclick="ubahQty(-1)">−</button>
                    <input type="number" name="qty" id="qtyInput" class="qty-input" value="1" min="1" readonly>
                    <button type="button" class="qty-btn" onclick="ubahQty(1)">+</button>
                </div>
            </div>

            <div class="step-box" style="border:1px solid #d946ef;">
                <div class="step-num">3</div> <strong>METODE PEMBAYARAN</strong>
                <div style="background:#0f0a1e; padding:12px; border-radius:8px; margin-top:10px;">
                    <span style="font-weight:bold; font-size:15px;">📱 QRIS (All Payment)</span><br>
                    <span style="font-size:12px; color:#9ca3af;">DANA, OVO, GoPay, ShopeePay, Mobile Banking</span>
                </div>
            </div>

            <div class="sticky-footer">
                <div class="sticky-total">
                    <div class="lbl">Total Tagihan</div>
                    <div class="val" id="totalDisplay">Rp 18.500</div>
                </div>
                <button type="button" class="pay-now" onclick="document.getElementById('formCheckout').submit();">Pay Now</button>
            </div>
            <input type="hidden" name="total" id="totalInput" value="18500">
        </form>

    <?php elseif ($page == 'payment' && $order): ?>
        <!-- MODAL PEMBAYARAN -->
        <div class="payment-overlay">
            <div class="payment-modal">
                <div class="modal-header">
                    <div class="modal-close" onclick="window.location.href='?page=home'">✕</div>
                    <div style="background:#fff; width:50px; height:50px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:24px; color:#d946ef;">⏱</div>
                    <h3>MENUNGGU PEMBAYARAN</h3>
                    <p>Scan QR Code di bawah untuk membayar</p>
                </div>
                
                <div style="text-align:center; color:#9ca3af; font-size:12px; margin-bottom:5px;">
                    BATAS WAKTU PEMBAYARAN
                </div>
                <div class="timer" id="timerCountdown">4:57</div>

                <div class="qr-area">
                    <div class="qr-badge">SCAN TO PAY</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PAYMENT-<?= $order['id'] ?>" alt="QRIS">
                    <button class="qr-dl">⬇ Download QRIS</button>
                </div>
                <div class="pay-logo">
                    <span>DANA</span> <span>OVO</span> <span>gopay</span> <span>Pay</span> <span>🏦</span>
                </div>

                <div class="detail-box">
                    <div style="display:flex; gap:10px; margin-bottom:8px;">
                        <img src="<?= $products[0]['img'] ?>" style="width:40px; height:40px; border-radius:5px;">
                        <div>
                            <strong style="font-size:14px;"><?= $order['product_id'] == 1 ? 'DRIP CLIENT APKMOD' : 'PRODUK LAIN' ?></strong><br>
                            <span style="background:#d946ef; font-size:10px; padding:2px 8px; border-radius:5px;"><?= $order['package'] ?></span>
                        </div>
                    </div>
                    <div class="detail-row"><span>Harga Satuan</span> <span>Rp <?= number_format($order['total']/$order['qty'], 0, ',', '.') ?></span></div>
                    <div class="detail-row"><span>Jumlah</span> <span>x<?= $order['qty'] ?></span></div>
                    <div class="detail-row"><span>Total Bayar</span> <span style="color:#10b981;">Rp <?= number_format($order['total'], 0, ',', '.') ?></span></div>
                </div>

                <div style="font-size:12px; padding:8px 0; color:#9ca3af;">
                    <strong>Cara Pembayaran:</strong><br>
                    1. Buka aplikasi E-Wallet seperti DANA, OVO, GoPay, ShopeePay.<br>
                    2. Pilih menu Scan QRIS atau Bayar.<br>
                    3. Arahkan kamera ke QR Code di atas.<br>
                    4. Konfirmasi nominal pembayaran lalu selesaikan transaksi.
                </div>

                <div class="checking-box">
                    <span>⏳ Checking Payment</span>
                </div>

                <div class="detail-box" style="background:#0f0a1e; border:1px solid #1e1832;">
                    <div class="detail-row"><span>ID Transaksi:</span> <span style="color:#d946ef;"><?= $order['id'] ?></span></div>
                    <div class="detail-row"><span>Tanggal:</span> <span><?= $order['date'] ?></span></div>
                    <div class="detail-row"><span>Status:</span> <span class="status-pending">PENDING</span></div>
                </div>

                <a href="?page=home" class="batal-btn">Batal & Tutup</a>
            </div>
        </div>

    <?php elseif ($page == 'status'): ?>
        <h2 style="text-align:center; color:#d946ef; margin:15px 0;">📊 Status Cheat</h2>
        <p style="text-align:center; font-size:13px; color:#9ca3af;">Informasi status terbaru semua cheat yang tersedia</p>
        <a href="?page=home" class="back-link">← Kembali</a>

        <?php 
        $cheats = jread($c_file);
        $cats = ['Android', 'PC', 'iOS'];
        foreach($cats as $cat): 
            $filtered = array_filter($cheats, fn($c) => $c['cat'] === $cat);
            if(empty($filtered)) continue;
        ?>
        <div class="status-section">
            <h4>🤖 <?= strtoupper($cat) ?></h4>
            <?php foreach($filtered as $c): ?>
            <div class="status-item">
                <div>
                    <strong style="font-size:14px;"><?= $c['name'] ?></strong><br>
                    <span style="font-size:10px; color:#9ca3af;"><?= $c['cat'] ?></span>
                </div>
                <div style="display:flex; align-items:center;">
                    <?php if($c['status'] == 'online'): ?>
                        <span class="st-online">🟢 ONLINE</span>
                    <?php else: ?>
                        <span class="st-maint">🟠 MAINTENANCE</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <a href="?page=home" style="display:block; background:#1a1528; padding:12px; text-align:center; border-radius:10px; margin-top:20px; border:1px solid #2a2540;">← Kembali ke Beranda</a>

    <?php else: ?>
        <div style="text-align:center; margin:50px 0;">
            <h2 style="color:#9ca3af;">404 - Produk tidak ditemukan</h2>
            <a href="?page=home" style="color:#d946ef; display:block; margin-top:10px;">Kembali ke beranda</a>
        </div>
    <?php endif; ?>
</div>

<script>
    // JAM WIB
    function updateClock() {
        const now = new Date();
        document.getElementById('jam').innerText = now.toLocaleTimeString('id-ID', { hour12: false, hour:'2-digit', minute:'2-digit' }) + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // SIDEBAR TOGGLE
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }

    // FILTER PRODUK
    function filter(cat) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
        document.querySelectorAll('.card').forEach(c => {
            c.style.display = (cat === 'all' || c.dataset.cat === cat) ? 'flex' : 'none';
        });
    }

    // CHECKOUT LOGIC
    let currentPrice = 18500;
    function pilihPaket(el, price) {
        document.querySelectorAll('.pkg-item').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        el.querySelector('input[type="radio"]').checked = true;
        currentPrice = price;
        updateTotal();
    }
    
    function ubahQty(val) {
        let q = parseInt(document.getElementById('qtyInput').value) + val;
        if(q < 1) q = 1;
        document.getElementById('qtyInput').value = q;
        updateTotal();
    }

    function updateTotal() {
        let qty = parseInt(document.getElementById('qtyInput').value);
        let total = currentPrice * qty;
        document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('totalInput').value = total;
    }

    // TIMER COUNTDOWN (MODAL PEMBAYARAN)
    if(document.getElementById('timerCountdown')) {
        let time = 297; // 4:57
        const timer = document.getElementById('timerCountdown');
        setInterval(() => {
            if(time <= 0) { timer.innerText = "0:00"; return; }
            time--;
            let m = Math.floor(time / 60);
            let s = time % 60;
            timer.innerText = m + ':' + (s < 10 ? '0' : '') + s;
        }, 1000);
    }
</script>
</body>
</html>