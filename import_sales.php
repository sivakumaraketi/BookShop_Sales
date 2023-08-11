<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Assuming your JSON data is stored in a file named "sales.json"
$jsonData = file_get_contents('sales.json');
$salesData = json_decode($jsonData, true);

$conn = new mysqli('localhost:3306', 'root', '', 'bookshop_sales');

if ($jsonData === false) {
    die('Error reading sales.json: ' . error_get_last()['message']);
}

$salesData = json_decode($jsonData, true);
if ($salesData === null) {
    die('Error decoding JSON data: ' . json_last_error_msg());
}

$successMessage = '';
$failureMessage = '';

foreach ($salesData as $sale) {
    $customerName = $sale['customer'];
    $productName = $sale['product'];
    $price = $sale['price'];

    // Check if customer already exists, if not, insert
    $customerId = null;
    $result = $conn->query("SELECT id FROM customers WHERE name = '$customerName'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customerId = $row['id'];
    } else {
        $conn->query("INSERT INTO customers (name) VALUES ('$customerName')");
        $customerId = $conn->insert_id;
    }

    // Insert sale data
    if ($conn->query("INSERT INTO sales (customer_id, product, price) VALUES ($customerId, '$productName', $price)")) {
        $successMessage = 'Sales data imported successfully.';
    } else {
        $failureMessage = 'Failed to import sales data.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bookshop Sales Import</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="message <?php echo isset($successMessage) ? 'success' : 'failure'; ?>">
        <?php echo isset($successMessage) ? $successMessage : $failureMessage; ?>
    </div>
</body>
</html>
