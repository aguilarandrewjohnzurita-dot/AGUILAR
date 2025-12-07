<?php
// ----------------------------------------------
// PHP SECTION
// ----------------------------------------------
$showModal = false;
$receiptHTML = "";
$totalPrice = 0;
$change = 0;

// Product List
$products = [
    "Roasted Chicken" => 285,
    "Hot & Spicy Chicken" => 300,
    "Lechon Manok" => 310,
    "Paa / Wings" => 120,
    "Chicken Adobo" => 100,
    "Golden Chicken" => 499,
    "Ice cream Cola" => 39,
    "Ice Coffee" => 39,
    "Royal" => 12,
    "Coke" => 12
];

// Keep form values
$fullname = $_POST["fullname"] ?? "";
$number = $_POST["number"] ?? "";
$address = $_POST["address"] ?? "";
$date = $_POST["date"] ?? "";
$time = $_POST["time"] ?? "";
$cash = floatval($_POST["cash"] ?? 0);
$freeSoup = $_POST["freeSoup"] ?? "";

// AUTO-CALC PHP-ONLY
$orderList = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($products as $name => $price) {
        $key = str_replace(" ", "_", $name);
        $qty = intval($_POST[$key] ?? 0);

        if ($qty > 0) {
            $subtotal = $qty * $price;
            $orderList[] = [
                "name" => $name,
                "qty" => $qty,
                "subtotal" => $subtotal
            ];
            $totalPrice += $subtotal;
        }
    }

    $change = $cash - $totalPrice;

    // Build Receipt
    ob_start();
    ?>
    <h2>ORDER RECEIPT</h2>
    <p><b>Name:</b> <?= $fullname ?></p>
    <p><b>Contact Number:</b> <?= $number ?></p>
    <p><b>Address:</b> <?= $address ?></p>
    <p><b>Date:</b> <?= $date ?></p>
    <p><b>Time:</b> <?= $time ?></p>
    <br>

    <?php foreach ($orderList as $item): ?>
        <p><?= $item["name"] ?> (x<?= $item["qty"] ?>) — ₱<?= number_format($item["subtotal"], 2) ?></p>
    <?php endforeach; ?>

    <br>
    <p><b>Total:</b> ₱<?= number_format($totalPrice, 2) ?></p>
    <p><b>Cash:</b> ₱<?= number_format($cash, 2) ?></p>
    <p><b>Change:</b> ₱<?= number_format($change, 2) ?></p>
    <p><b>Free Soup:</b> <?= $freeSoup ?></p>
    <?php

    $receiptHTML = ob_get_clean();
    $showModal = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chooks-to-Drew Order Form (PHP ONLY)</title>

<style>
    body { font-family: Arial; background: #f6f2d4; padding: 20px; }
    .container {
        background: white; padding: 25px; max-width: 650px; margin: auto;
        border-radius: 10px; box-shadow: 0 0 10px #0003;
    }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, textarea, select {
        width: 100%; padding: 10px; margin-top: 5px;
        border-radius: 5px; border: 1px solid #ccc;
    }
    .products { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .product-box { border: 2px solid #ccc; padding: 10px; border-radius: 10px; }
    button {
        width: 100%; padding: 12px; margin-top: 20px;
        background: #d32f2f; color: white;
        border: none; border-radius: 8px; font-size: 18px; cursor: pointer;
    }
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; justify-content: center; 
        align-items: center; visibility: hidden; opacity: 0; transition: 0.3s;
    }
    .modal-overlay.show { visibility: visible; opacity: 1; }
    .modal {
        background: white; padding: 25px; width: 350px; border-radius: 10px;
        text-align: center; box-shadow: 0 0 15px #000;
    }
</style>
</head>

<body>

<div class="container">
    <h1>Chooks-to-Drew Order Form (PHP Only)</h1>

    <form method="POST">

        <label>Full Name:</label>
        <input type="text" name="fullname" value="<?= $fullname ?>" required>

        <label>Contact Number:</label>
        <input type="text" name="number" value="<?= $number ?>" required>

        <label>Complete Address:</label>
        <textarea name="address" required><?= $address ?></textarea>

        <label>Preferred Delivery Date:</label>
        <input type="date" name="date" value="<?= $date ?>" required>

        <label>Preferred Delivery Time:</label>
        <input type="time" name="time" value="<?= $time ?>" required>

        <label>Select Products & Quantity:</label>

        <div class="products">
            <?php foreach ($products as $name => $price): ?>
                <?php $key = str_replace(" ", "_", $name); ?>
                <div class="product-box">
                    <h3><?= $name ?></h3>
                    <p>₱<?= $price ?></p>
                    <input type="number" name="<?= $key ?>" min="0"
                           value="<?= $_POST[$key] ?? 0 ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <label>Total Price (PHP):</label>
        <input type="text" readonly value="₱ <?= number_format($totalPrice,2) ?>">

        <label>Cash from Customer (₱):</label>
        <input type="number" name="cash" value="<?= $cash ?>">

        <label>Change (PHP):</label>
        <input type="text" readonly value="₱ <?= number_format($change,2) ?>">

        <label>Free Soup:</label>
        <select name="freeSoup" required>
            <option value="">-- Select --</option>
            <option value="Tinola" <?= $freeSoup=="Tinola"?"selected":"" ?>>Tinola</option>
            <option value="Sinigang" <?= $freeSoup=="Sinigang"?"selected":"" ?>>Sinigang</option>
            <option value="Bulalo" <?= $freeSoup=="Bulalo"?"selected":"" ?>>Bulalo</option>
        </select>

        <button type="submit">Place Order</button>
    </form>
</div>

<!-- Modal -->
<div class="modal-overlay <?= $showModal ? "show" : "" ?>">
    <div class="modal">
        <?= $receiptHTML ?>
        <form method="POST">
            <button class="close-btn">Close</button>
        </form>
    </div>
</div>

</body>
</html>
