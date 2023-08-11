<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost:3306', 'root', '', 'bookshop_sales');

// Fetch customers for filter dropdown
$customers = $conn->query("SELECT * FROM customers");

$errorMessage = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCustomer = $_POST['customer'];
    $minPrice = $_POST['min_price'];
    $maxPrice = $_POST['max_price'];

    if (!is_numeric($minPrice) && $minPrice !== '') {
        $errorMessage = 'Min Price should be a valid number.';
    } elseif (!is_numeric($maxPrice) && $maxPrice !== '') {
        $errorMessage = 'Max Price should be a valid number.';
    } elseif ($minPrice !== '' && $maxPrice !== '' && $minPrice > $maxPrice) {
        $errorMessage = 'Min Price cannot be greater than Max Price.';
    } else {
        // Build query based on filters
        $query = "SELECT customers.name, sales.product, sales.price
                  FROM sales
                  INNER JOIN customers ON sales.customer_id = customers.id
                  WHERE 1=1";

        if ($selectedCustomer !== 'all') {
            $query .= " AND customers.id = $selectedCustomer";
        }

        if ($minPrice !== '') {
            $query .= " AND sales.price >= $minPrice";
        }

        if ($maxPrice !== '') {
            $query .= " AND sales.price <= $maxPrice";
        }

        $result = $conn->query($query);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Shop Sales</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2>Book Shop Sales Dashboard</h2>
            <form method="post">
                <div class="form-group">
                    <label class="label" for="customer">Customer:</label>
                    <select class="input-field" name="customer">
                        <option value="all">All Customers</option>
                        <?php while ($row = $customers->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="label" for="min_price">Min Price:</label>
                    <input class="input-field" type="text" name="min_price">
                </div>
                <div class="form-group">
                    <label class="label" for="max_price">Max Price:</label>
                    <input class="input-field" type="text" name="max_price">
                </div>
                <button class="submit-button" type="submit">Filter</button>
            </form>
            <?php if ($errorMessage !== '') : ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
        </div>

        <?php if ($result !== null) : ?>
            <table class="sales-table">
                <tr>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Price</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['product']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="2">Total</td>
                    <td>
                        <?php
                        $total = 0;
                        $result->data_seek(0); // Reset result pointer
                        while ($row = $result->fetch_assoc()) {
                            $total += $row['price'];
                        }
                        echo $total;
                        ?>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>


