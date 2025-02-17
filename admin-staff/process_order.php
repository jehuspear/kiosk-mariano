<?php
session_start();
require_once 'database_admin.php';

// Prevent any output before JSON response
ob_start();

// Ensure no errors are displayed in the output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to process orders'
    ]);
    exit;
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get raw POST data
$raw_data = file_get_contents('php://input');
if (!$raw_data) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No data received'
    ]);
    exit;
}

// Decode JSON data
$data = json_decode($raw_data, true);
if (!$data) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data received: ' . json_last_error_msg()
    ]);
    exit;
}

try {
    // Validate required data
    if (empty($data['items'])) {
        throw new Exception('No items in order');
    }

    if (!isset($data['totalAmount']) || !is_numeric($data['totalAmount'])) {
        throw new Exception('Invalid total amount');
    }

    if (!isset($data['finalAmount']) || !is_numeric($data['finalAmount'])) {
        throw new Exception('Invalid final amount');
    }

    if (empty($data['paymentMethod'])) {
        throw new Exception('Payment method is required');
    }

    if (empty($data['eatingOption'])) {
        throw new Exception('Eating option is required');
    }

    // Set timezone to Philippines
    date_default_timezone_set('Asia/Manila');

    // Get today's date range
    $today_start = date('Y-m-d 00:00:00');
    $today_end = date('Y-m-d 23:59:59');
    
    // Get the latest ticket number from today's orders
    $sql = "SELECT Order_TicketNumber FROM `order` 
            WHERE Order_DateTime BETWEEN ? AND ?
            ORDER BY Order_DateTime DESC, Order_ID DESC 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $today_start, $today_end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If there are no orders today, start from 1
    // Otherwise, increment the last ticket number
    if ($result->num_rows === 0) {
        $ticketNumber = 1;
    } else {
        $row = $result->fetch_assoc();
        $ticketNumber = $row['Order_TicketNumber'] + 1;
    }
    
    // Format ticket number to have leading zeros (e.g., 001, 012, 123)
    $formattedTicketNumber = str_pad($ticketNumber, 3, '0', STR_PAD_LEFT);
    
    // Begin transaction
    $conn->begin_transaction();

    $currentDateTime = date('Y-m-d H:i:s');
    $items = $data['items'];
    $totalAmount = $data['totalAmount'];
    // Ensure payment method is either 'Cash' or 'GCash'
    $paymentMethod = $data['paymentMethod'] === 'Cash' ? 'Cash' : 'GCash';
    $eatingOption = $data['eatingOption'];
    $discountType = $data['discountType'];
    $discountAmount = $data['discountAmount'];
    $finalAmount = $data['finalAmount'];
    $cashAmount = $data['cashAmount'] ?? null;
    $referenceNumber = $data['referenceNumber'] ?? null;
    $customDiscountName = $data['customDiscountName'] ?? null;
    $customDiscountPercent = $data['customDiscountPercent'] ?? null;
    $staffId = $_SESSION['user_id'];

    // Insert payment record
    $sql = "INSERT INTO payment (
            Payment_Method, 
            Payment_ReferenceNumber,
            Payment_DateTime, 
            Order_TotalAmount, 
            Payment_DiscountType,
            Payment_DiscountAmount,
            Payment_CashPaid,
            Payment_Change,
            Payment_TotalAmount, 
            Payment_Status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Completed')";
    
    $change = $paymentMethod === 'Cash' ? ($cashAmount - $finalAmount) : null;
    $subtotal = $totalAmount; // Original amount before discount
    $discountLabel = '';
    if ($discountType !== 'none') {
        if ($discountType === 'custom' && $customDiscountName) {
            $discountLabel = $customDiscountName . ' (' . $customDiscountPercent . '%)';
        } else if ($discountType === 'senior' || $discountType === 'pwd') {
            $discountLabel = strtoupper($discountType) . ' (20%)';
        }
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsdddd", 
            $paymentMethod, 
            $referenceNumber,
            $currentDateTime,
            $subtotal, // Store subtotal in Order_TotalAmount
            $discountLabel,
            $discountAmount,
            $cashAmount,
            $change,
            $finalAmount // Store final amount in Payment_TotalAmount
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment record: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Payment insert error: " . $e->getMessage());
        throw $e;
    }
    $paymentId = $conn->insert_id;
    
    // Insert order record
    $sql = "INSERT INTO `order` (
            Order_CustomerName,
            Order_EatingOption, 
            Order_TicketNumber, 
            Order_DateTime,
            Order_TotalAmount,
            Payment_ID, 
            Payment_Method, 
            Order_Status,
            Staff_ID
        ) VALUES (NULL, ?, ?, ?, ?, ?, ?, 'Preparing', ?)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisdisi", 
            ucfirst($eatingOption), // Capitalize first letter (Dine-in/Take-out)
            $ticketNumber,
            $currentDateTime,
            $subtotal, // Store subtotal in Order_TotalAmount
            $paymentId,
            $paymentMethod,
            $staffId
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order record: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Order insert error: " . $e->getMessage());
        throw $e;
    }
    $orderId = $conn->insert_id;
    
    // Check stock availability first
    $checkStock = $conn->prepare("SELECT 
            m.MenuItem_Name,
            ms.MenuItemSize_SizeName,
            ms.MenuItemSize_Stock,
            m.MenuItem_TotalStocks
        FROM menuitem_sizes ms
        JOIN menuitem m ON m.MenuItem_ID = ms.MenuItem_ID
        WHERE ms.MenuItemSize_ID = ?");
    
    foreach ($items as $item) {
        $checkStock->bind_param("i", $item['sizeId']);
        if (!$checkStock->execute()) {
            throw new Exception("Failed to check stock availability");
        }
        $stockResult = $checkStock->get_result()->fetch_assoc();
        
        if ($stockResult['MenuItemSize_Stock'] < $item['quantity']) {
            throw new Exception("Insufficient stock for {$stockResult['MenuItem_Name']} ({$stockResult['MenuItemSize_SizeName']})");
        }
    }

    // Insert order items and update stocks
    $insertOrderItem = $conn->prepare("INSERT INTO orderitem (Order_ID, MenuItem_ID, OrderItem_CupSize, OrderItem_Quantity, OrderItem_Price, MenuItemSize_ID) 
            VALUES (?, ?, ?, ?, ?, ?)");
    
    $updateMenuItemSize = $conn->prepare("UPDATE menuitem_sizes 
            SET MenuItemSize_Sold = MenuItemSize_Sold + ?,
                MenuItemSize_Stock = MenuItemSize_Stock - ?
            WHERE MenuItemSize_ID = ?");
            
    $updateMenuItem = $conn->prepare("UPDATE menuitem 
            SET MenuItem_TotalSold = MenuItem_TotalSold + ?,
                MenuItem_TotalStocks = MenuItem_TotalStocks - ?
            WHERE MenuItem_ID = ?");
    
    foreach ($items as $item) {
        // Insert order item
        $insertOrderItem->bind_param("iisidi", 
            $orderId, 
            $item['itemId'], 
            $item['sizeName'], 
            $item['quantity'], 
            $item['price'], 
            $item['sizeId']
        );
        if (!$insertOrderItem->execute()) {
            throw new Exception("Failed to insert order item");
        }
        
        // Update menuitem_sizes stock and sold quantities
        $updateMenuItemSize->bind_param("iii", 
            $item['quantity'], 
            $item['quantity'], 
            $item['sizeId']
        );
        if (!$updateMenuItemSize->execute()) {
            throw new Exception("Failed to update menu item size stock");
        }
        
        // Update menuitem stock and sold quantities
        $updateMenuItem->bind_param("iii", 
            $item['quantity'], 
            $item['quantity'], 
            $item['itemId']
        );
        if (!$updateMenuItem->execute()) {
            throw new Exception("Failed to update menu item stock");
        }
    }
    
    // Get staff name for receipt
    $sql = "SELECT Staff_FirstName FROM staff WHERE Staff_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $staffResult = $stmt->get_result();
    $staffName = $staffResult->fetch_assoc()['Staff_FirstName'];
    
    // Commit transaction
    $conn->commit();
    
    // Prepare response
    $response = [
        'success' => true,
        'orderDetails' => [
            'orderId' => $orderId,
            'ticketNumber' => $formattedTicketNumber,
            'Order_DateTime' => $currentDateTime,
            'staffFirstName' => $staffName,
            'eatingOption' => $eatingOption,
            'paymentMethod' => $paymentMethod,
            'items' => implode('<br>', array_map(function($item) {
                return $item['quantity'] . ' x ' . $item['itemName'] . ' (' . $item['sizeName'] . ')';
            }, $items)),
            'itemPrices' => implode('<br>', array_map(function($item) {
                return '₱' . number_format($item['price'] * $item['quantity'], 2);
            }, $items)),
            'totalAmount' => $totalAmount
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
