<?php
require_once 'database_customer.php';

function getItemTemperature($itemId) {
    global $conn;
    
    $sql = "SELECT DISTINCT 
                CASE 
                    WHEN MenuItemSize_Temperature = 1 THEN 'Hot'
                    WHEN MenuItemSize_Temperature = 0 THEN 'Cold'
                END as temperature
            FROM menuitem_sizes 
            WHERE MenuItem_ID = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $temperatures = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $temperatures[] = $row['temperature'];
    }
    
    mysqli_stmt_close($stmt);
    
    // Generate HTML for temperature display
    if (!empty($temperatures)) {
        $html = '<div class="temperature-info">';
        $html .= '<p class="temp-label">Available in:</p>';
        foreach ($temperatures as $temp) {
            $icon = $temp === 'Hot' ? 'fa-fire' : 'fa-snowflake';
            $html .= "<span class='temp-badge {$temp}'>";
            $html .= "<i class='fas {$icon}'></i> {$temp}";
            $html .= "</span>";
        }
        $html .= '</div>';
        return $html;
    }
    
    return '';
}

// If this file is called directly via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['itemId'])) {
    header('Content-Type: text/html');
    $itemId = intval($_GET['itemId']);
    echo getItemTemperature($itemId);
}
?>
