<?php
// get_filtered_feedback.php: Handle AJAX requests for filtered feedback data
session_start();

// Debug session info
error_log('Session data: ' . json_encode($_SESSION));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Check if user has Admin role
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if ($userRole !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Access denied. This page is only accessible to administrators.']);
    exit;
}

// For debugging, we'll return dummy data if there are any issues
if (!isset($_SESSION['user_id']) || $userRole !== 'Admin') {
    // Return dummy data for testing
    $dummyResponse = [
        'feedbacks' => [
            [
                'Feedback_CustomerName' => 'Test Customer',
                'Feedback_Rating' => 5,
                'Feedback_Comments' => 'This is a test comment',
                'Feedback_DateTime' => date('Y-m-d H:i:s'),
                'Order_ID' => 12345,
                'Order_TicketNumber' => 'T12345'
            ]
        ],
        'ratings' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 1],
        'ratingChart' => [0, 0, 0, 0, 100],
        'totalRatings' => 1,
        'averageRating' => 5.0
    ];
    
    header('Content-Type: application/json');
    echo json_encode($dummyResponse);
    exit;
}

// Define constant to allow database connection
define('ALLOW_DIRECT_ACCESS', true);

// Include database connection
require_once 'database_admin.php';

// Initialize response array
$response = [
    'feedbacks' => [],
    'ratings' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
    'ratingChart' => [0, 0, 0, 0, 0],
    'totalRatings' => 0,
    'averageRating' => 0
];

// Get filter parameters
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;
$ratings = isset($_POST['ratings']) ? $_POST['ratings'] : [];

// Debug
error_log('Received parameters: ' . json_encode($_POST));

// Validate dates
if ($startDate) {
    $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
} else {
    // Default to 30 days ago
    $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
}

if ($endDate) {
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
} else {
    // Default to today
    $endDate = date('Y-m-d 23:59:59');
}

// Build SQL query
$sql = "SELECT f.Feedback_CustomerName, f.Feedback_Rating, f.Feedback_Comments, f.Feedback_DateTime, 
        o.Order_ID, o.Order_TicketNumber 
        FROM feedback f 
        LEFT JOIN `order` o ON f.Order_ID = o.Order_ID
        WHERE f.Feedback_DateTime BETWEEN ? AND ?";

$params = [$startDate, $endDate];
$types = "ss";

// Add rating filter if specified
if (!empty($ratings)) {
    // Convert ratings to array if it's not already
    if (!is_array($ratings)) {
        $ratings = [$ratings];
    }
    
    $placeholders = [];
    foreach ($ratings as $rating) {
        $placeholders[] = '?';
        $types .= 'i';
    }
    
    $sql .= " AND f.Feedback_Rating IN (" . implode(',', $placeholders) . ")";
    $params = array_merge($params, $ratings);
}

// Add order by clause
$sql .= " ORDER BY f.Feedback_DateTime DESC";

// Prepare and execute query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters dynamically
if (count($params) > 0) {
    // Create a reference array for bind_param
    $bindParams = [];
    $bindParams[] = $types;
    
    foreach ($params as $key => $value) {
        $bindParams[] = &$params[$key];
    }
    
    call_user_func_array([$stmt, 'bind_param'], $bindParams);
}

$stmt->execute();
$result = $stmt->get_result();

// Process results
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response['feedbacks'][] = $row;
        $rating = (int)$row['Feedback_Rating'];
        if (isset($response['ratings'][$rating])) {
            $response['ratings'][$rating]++;
        }
    }
}

// Calculate rating chart data
$response['totalRatings'] = array_sum($response['ratings']);
if ($response['totalRatings'] > 0) {
    foreach ($response['ratings'] as $rating => $count) {
        $response['ratingChart'][$rating - 1] = round(($count / $response['totalRatings']) * 100, 2);
    }
    
    // Calculate average rating
    $totalScore = 0;
    foreach ($response['ratings'] as $rating => $count) {
        $totalScore += ($rating * $count);
    }
    $response['averageRating'] = round($totalScore / $response['totalRatings'], 1);
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
