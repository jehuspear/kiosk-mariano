<?php
session_start();
require_once 'database_customer.php';
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function sendPushNotification($ticketNumber, $message) {
    // Get subscription from session
    if (!isset($_SESSION['push_subscription']) || 
        $_SESSION['push_subscription']['ticket_number'] !== $ticketNumber) {
        return false;
    }

    $subscription = $_SESSION['push_subscription']['subscription'];
    
    $auth = array(
        'VAPID' => array(
            'subject' => 'https://sincocafe.com',
            'publicKey' => 'YOUR_PUBLIC_KEY', // You'll need to generate VAPID keys
            'privateKey' => 'YOUR_PRIVATE_KEY', // You'll need to generate VAPID keys
        ),
    );

    $webPush = new WebPush($auth);
    
    $report = $webPush->sendOneNotification(
        Subscription::create($subscription),
        $message
    );
    
    return $report->isSuccess();
}

// Handle push notification request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['ticketNumber']) && isset($data['status']) && $data['status'] === 'ReadyToClaim') {
        $message = json_encode([
            'title' => 'Order Ready!',
            'body' => "Your order (Ticket #{$data['ticketNumber']}) is ready for pickup!",
            'icon' => 'resources/images/logo.png',
            'badge' => 'resources/images/logo.png'
        ]);
        
        $success = sendPushNotification($data['ticketNumber'], $message);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    }
}
