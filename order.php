<?php
$host = 'localhost';
$db = 'RestaurantDB';
$user = 'root';  // replace with your database username
$pass = '';  // replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pizzaQty = isset($_POST['pizza']) ? intval($_POST['pizza']) : 0;
        $rotiQty = isset($_POST['roti']) ? intval($_POST['roti']) : 0;
        $riceQty = isset($_POST['rice']) ? intval($_POST['rice']) : 0;
        $totalPaid = isset($_POST['total_paid']) ? floatval($_POST['total_paid']) : 0;

        $pizzaPrice = 1500;
        $rotiPrice = 1500;
        $ricePrice = 500;

        $pizzaTotal = $pizzaQty * $pizzaPrice;
        $rotiTotal = $rotiQty * $rotiPrice;
        $riceTotal = $riceQty * $ricePrice;

        $totalPayable = $pizzaTotal + $rotiTotal + $riceTotal;
        $balance = $totalPaid - $totalPayable;

        $efNo = rand(1, 100);
        $paymentDate = date('Y-m-d H:i:s');

        // Insert order into Orders table
        $orderStmt = $pdo->prepare("INSERT INTO Orders (ef_no, payment_date, total_payable, total_paid, balance) 
                                    VALUES (:ef_no, :payment_date, :total_payable, :total_paid, :balance)");
        $orderStmt->execute([
            ':ef_no' => $efNo,
            ':payment_date' => $paymentDate,
            ':total_payable' => $totalPayable,
            ':total_paid' => $totalPaid,
            ':balance' => $balance
        ]);

        $orderId = $pdo->lastInsertId();

        // Insert order items into OrderItems table
        $orderItems = [
            ['name' => 'Pizza', 'quantity' => $pizzaQty, 'price' => $pizzaTotal],
            ['name' => 'Roti', 'quantity' => $rotiQty, 'price' => $rotiTotal],
            ['name' => 'Rice', 'quantity' => $riceQty, 'price' => $riceTotal]
        ];

        foreach ($orderItems as $item) {
            if ($item['quantity'] > 0) {
                $itemStmt = $pdo->prepare("INSERT INTO OrderItems (order_id, item_name, quantity, price) 
                                           VALUES (:order_id, :item_name, :quantity, :price)");
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':item_name' => $item['name'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }
        }

        // Respond with JSON
        echo json_encode([
            'ef_no' => $efNo,
            'payment_date' => $paymentDate,
            'total_payable' => $totalPayable,
            'total_paid' => $totalPaid,
            'balance' => $balance
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
