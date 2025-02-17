<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database_admin.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Verify database connection
if (!isset($conn)) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Debug log function
function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log .= " - " . print_r($data, true);
    }
    error_log($log);
}

try {
    debug_log("Starting menu items fetch");
    
    // Test query to verify database access
    if (!mysqli_query($conn, "SELECT 1")) {
        debug_log("Test query failed", mysqli_error($conn));
        throw new Exception("Database test query failed");
    }
    debug_log("Test query successful");

    // Prepare the query to get menu items with their sizes
    $query = "
        SELECT 
            m.MenuItem_ID,
            m.MenuItem_Name,
            m.MenuItem_Image,
            m.MenuItem_Description,
            m.MenuItem_Category,
            m.MenuItem_Availability,
            m.MenuItem_TotalSold,
            ms.MenuItemSize_ID,
            ms.MenuItemSize_SizeName,
            ms.MenuItemSize_IsHot,
            ms.MenuItemSize_Price,
            ms.MenuItemSize_Stock
        FROM menuitem m
        LEFT JOIN menuitem_sizes ms ON m.MenuItem_ID = ms.MenuItem_ID
        WHERE m.MenuItem_Availability = 'Available'
        AND ms.MenuItemSize_Stock > 0
        ORDER BY m.MenuItem_TotalSold DESC, m.MenuItem_Category, m.MenuItem_Name, ms.MenuItemSize_SizeName
    ";

    debug_log("Executing main query");
    $result = mysqli_query($conn, $query);
    if (!$result) {
        debug_log("Main query failed", mysqli_error($conn));
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
    debug_log("Main query successful");

    $results = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }

    // Group the results by menu item
    $menuItems = [];
    foreach ($results as $row) {
        $itemId = $row['MenuItem_ID'];
        
        // If this is a new menu item, initialize its entry
        if (!isset($menuItems[$itemId])) {
            $menuItems[$itemId] = [
                'MenuItem_ID' => $row['MenuItem_ID'],
                'MenuItem_Name' => $row['MenuItem_Name'],
                'MenuItem_Image' => $row['MenuItem_Image'],
                'MenuItem_Description' => $row['MenuItem_Description'],
                'MenuItem_Category' => $row['MenuItem_Category'],
                'MenuItem_Availability' => $row['MenuItem_Availability'],
                'MenuItem_TotalSold' => $row['MenuItem_TotalSold'],
                'sizes' => [],
                'bestSeller' => false
            ];
        }

        // Add the size information if it exists
        if ($row['MenuItemSize_ID']) {
            $menuItems[$itemId]['sizes'][] = [
                'MenuItemSize_ID' => $row['MenuItemSize_ID'],
                'MenuItemSize_SizeName' => $row['MenuItemSize_SizeName'],
                'MenuItemSize_IsHot' => $row['MenuItemSize_IsHot'],
                'MenuItemSize_Price' => $row['MenuItemSize_Price'],
                'MenuItemSize_Stock' => $row['MenuItemSize_Stock']
            ];
        }
    }

    // Sort sizes by price for each menu item
    foreach ($menuItems as &$item) {
        usort($item['sizes'], function($a, $b) {
            return $a['MenuItemSize_Price'] - $b['MenuItemSize_Price'];
        });
    }
    unset($item); // Break the reference

    // Sort by total sold and add best seller badges
    usort($menuItems, function($a, $b) {
        return $b['MenuItem_TotalSold'] - $a['MenuItem_TotalSold'];
    });

    // Mark top 3 best sellers
    for ($i = 0; $i < min(3, count($menuItems)); $i++) {
        $menuItems[$i]['bestSeller'] = $i + 1;
    }

    debug_log("Sorted menu item sizes by price");

    // Convert to indexed array
    $menuItems = array_values($menuItems);

    debug_log("Processing results", [
        'count' => count($menuItems),
        'first_item' => !empty($menuItems) ? [
            'name' => $menuItems[0]['MenuItem_Name'],
            'image' => $menuItems[0]['MenuItem_Image'],
            'sizes_count' => count($menuItems[0]['sizes'])
        ] : null
    ]);
    
    // Return success response with menu items
    $response = [
        'success' => true,
        'menuItems' => $menuItems,
        'count' => count($menuItems)
    ];
    debug_log("Sending response", $response);
    echo json_encode($response);

} catch (Exception $e) {
    // Return error response with detailed message
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'error' => mysqli_error($conn)
    ]);
    exit;
}
?>
