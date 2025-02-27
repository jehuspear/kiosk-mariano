<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database_admin.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's role
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Verify database connection
if (!isset($conn)) {
    die("Database connection failed");
}

// Set default values for filtering and pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get search parameters
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$paymentMethod = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

// Build the query
$query = "SELECT o.Order_ID, o.Order_CustomerName, o.Order_TicketNumber, 
                 o.Order_DateTime, o.Order_TotalAmount, o.Payment_Method, 
                 p.Payment_DiscountType, p.Payment_TotalAmount, p.Payment_Status
          FROM `order` o
          LEFT JOIN payment p ON o.Payment_ID = p.Payment_ID
          WHERE p.Payment_Status = 'Completed'";

$countQuery = "SELECT COUNT(*) as total FROM `order` o 
               LEFT JOIN payment p ON o.Payment_ID = p.Payment_ID
               WHERE p.Payment_Status = 'Completed'";

// Add search filters if provided
if (!empty($searchTerm)) {
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $query .= " AND (o.Order_CustomerName LIKE '%$searchTerm%' OR o.Order_TicketNumber LIKE '%$searchTerm%')";
    $countQuery .= " AND (o.Order_CustomerName LIKE '%$searchTerm%' OR o.Order_TicketNumber LIKE '%$searchTerm%')";
}

if (!empty($dateFrom)) {
    $dateFrom = mysqli_real_escape_string($conn, $dateFrom);
    $query .= " AND DATE(o.Order_DateTime) >= '$dateFrom'";
    $countQuery .= " AND DATE(o.Order_DateTime) >= '$dateFrom'";
}

if (!empty($dateTo)) {
    $dateTo = mysqli_real_escape_string($conn, $dateTo);
    $query .= " AND DATE(o.Order_DateTime) <= '$dateTo'";
    $countQuery .= " AND DATE(o.Order_DateTime) <= '$dateTo'";
}

if (!empty($paymentMethod)) {
    $paymentMethod = mysqli_real_escape_string($conn, $paymentMethod);
    $query .= " AND o.Payment_Method = '$paymentMethod'";
    $countQuery .= " AND o.Payment_Method = '$paymentMethod'";
}

// Add sorting and pagination
$query .= " ORDER BY o.Order_DateTime DESC LIMIT $offset, $limit";

// Execute queries
$result = mysqli_query($conn, $query);
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts - White House Cafe</title>

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="resources/favicon/favicon_io/site.webmanifest"> 
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Css-admin/sidebar.css">
    <link rel="stylesheet" href="Css-admin/history.css">
    <style>
        .receipt-actions {
            display: flex;
            gap: 5px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-view {
            background-color: #17a2b8;
            color: white;
        }
        .btn-print {
            background-color: #6c757d;
            color: white;
        }
        .btn-view:hover, .btn-print:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <?php 
        include 'includes/sidebar.php';
        renderSidebar('receipts'); // Pass 'receipts' as the current page
    ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2><i class="fas fa-receipt"></i> Receipts</h2>
                    <p class="text-muted">View and print receipts from completed orders</p>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Customer name or ticket #" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?php echo htmlspecialchars($dateFrom); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?php echo htmlspecialchars($dateTo); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">All Methods</option>
                            <option value="Cash" <?php echo $paymentMethod === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="GCash" <?php echo $paymentMethod === 'GCash' ? 'selected' : ''; ?>>GCash</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="receipts.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Receipts Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Ticket #</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Payment Method</th>
                            <th>Discount</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['Order_ID']; ?></td>
                                    <td><?php echo $row['Order_TicketNumber']; ?></td>
                                    <td><?php echo $row['Order_CustomerName'] ? htmlspecialchars($row['Order_CustomerName']) : 'Anonymous'; ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($row['Order_DateTime'])); ?></td>
                                    <td><?php echo $row['Payment_Method']; ?></td>
                                    <td><?php echo $row['Payment_DiscountType'] ? htmlspecialchars($row['Payment_DiscountType']) : 'None'; ?></td>
                                    <td>₱<?php echo number_format($row['Payment_TotalAmount'], 2); ?></td>
                                    <td class="receipt-actions">
                                        <button class="btn btn-sm btn-view" onclick="viewReceipt(<?php echo $row['Order_ID']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No receipts found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchTerm); ?>&date_from=<?php echo urlencode($dateFrom); ?>&date_to=<?php echo urlencode($dateTo); ?>&payment_method=<?php echo urlencode($paymentMethod); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>&date_from=<?php echo urlencode($dateFrom); ?>&date_to=<?php echo urlencode($dateTo); ?>&payment_method=<?php echo urlencode($paymentMethod); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchTerm); ?>&date_from=<?php echo urlencode($dateFrom); ?>&date_to=<?php echo urlencode($dateTo); ?>&payment_method=<?php echo urlencode($paymentMethod); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="receiptModalLabel">
                        <i class="fas fa-receipt me-2"></i>Order Receipt
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="receiptFrame" style="width: 100%; height: 600px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printReceiptFromModal()">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize receipt modal
        const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        let currentReceiptId = null;
        
        // Function to view receipt in modal
        function viewReceipt(orderId) {
            currentReceiptId = orderId;
            const receiptFrame = document.getElementById('receiptFrame');
            receiptFrame.src = `view_receipt.php?order_id=${orderId}`;
            receiptModal.show();
        }
        
        // Function to print receipt from modal
        function printReceiptFromModal() {
            if (!currentReceiptId) return;
            
            const receiptFrame = document.getElementById('receiptFrame');
            receiptFrame.contentWindow.print();
        }
        
        // Date range validation
        document.addEventListener('DOMContentLoaded', function() {
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            
            dateFrom.addEventListener('change', function() {
                if (dateTo.value && dateFrom.value > dateTo.value) {
                    dateTo.value = dateFrom.value;
                }
            });
            
            dateTo.addEventListener('change', function() {
                if (dateFrom.value && dateTo.value < dateFrom.value) {
                    dateFrom.value = dateTo.value;
                }
            });
        });
    </script>
</body>
</html>
