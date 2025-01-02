<?php
require_once 'database_customer.php';

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = isset($_POST['itemId']) ? intval($_POST['itemId']) : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;

    if ($itemId && $size) {
        // Query to get price and stock for the specific size
        $sql = "SELECT MenuItemSize_Price as price, MenuItemSize_Stock as stock,
                       MenuItemSize_IsHot as temperature
                FROM menuitem_sizes 
                WHERE MenuItem_ID = ? AND MenuItemSize_SizeName = ?
                ORDER BY MenuItemSize_Price ASC
                LIMIT 1";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to prepare statement: ' . $conn->error
            ]);
            exit;
        }
        
        $stmt->bind_param("is", $itemId, $size);
        
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to execute statement: ' . $stmt->error
            ]);
            $stmt->close();
            exit;
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            // Map temperature values
            $temperatureDisplay = 'NORMAL';
            switch ($row['temperature']) {
                case 'Hot':
                    $temperatureDisplay = 'HOT';
                    break;
                case 'Iced':
                    $temperatureDisplay = 'ICED';
                    break;
                case 'Normal':
                    $temperatureDisplay = 'NORMAL';
                    break;
            }

            echo json_encode([
                'success' => true,
                'price' => $row['price'],
                'stock' => $row['stock'],
                'temperature' => $temperatureDisplay
            ]);
        } else {
            // If size not found, get the first available size
            $fallbackSql = "SELECT MenuItemSize_SizeName as size, 
                                  MenuItemSize_Price as price, 
                                  MenuItemSize_Stock as stock,
                                  MenuItemSize_IsHot as temperature
                           FROM menuitem_sizes 
                           WHERE MenuItem_ID = ?
                           ORDER BY MenuItemSize_Price ASC
                           LIMIT 1";
            
            $fallbackStmt = $conn->prepare($fallbackSql);
            if (!$fallbackStmt) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to prepare fallback statement: ' . $conn->error
                ]);
                exit;
            }
            
            $fallbackStmt->bind_param("i", $itemId);
            
            if (!$fallbackStmt->execute()) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to execute fallback statement: ' . $fallbackStmt->error
                ]);
                $fallbackStmt->close();
                exit;
            }
            
            $fallbackResult = $fallbackStmt->get_result();
            $fallbackRow = $fallbackResult->fetch_assoc();
            
            if ($fallbackRow) {
                // Map temperature values
                $temperatureDisplay = 'NORMAL';
                switch ($fallbackRow['temperature']) {
                    case 'Hot':
                        $temperatureDisplay = 'HOT';
                        break;
                    case 'Iced':
                        $temperatureDisplay = 'ICED';
                        break;
                    case 'Normal':
                        $temperatureDisplay = 'NORMAL';
                        break;
                }

                echo json_encode([
                    'success' => true,
                    'price' => $fallbackRow['price'],
                    'stock' => $fallbackRow['stock'],
                    'temperature' => $temperatureDisplay,
                    'size' => $fallbackRow['size']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No sizes available for this item'
                ]);
            }
            $fallbackStmt->close();
        }
        
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Item ID and size are required'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
