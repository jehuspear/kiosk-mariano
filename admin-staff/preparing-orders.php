<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preparing Orders</title>
  
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="Css-admin/sidebar.css">
  <link rel="stylesheet" href="Css-admin/orderlist.css">
  <link rel="stylesheet" href="Css-admin/preparing_orders.css">
  <link rel="stylesheet" href="Css-admin/preparing_status.css">
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Toast Container -->
  <div class="toast-container"></div>
</head>
<body>
  <div class="wrapper">
    <?php 
    require_once 'includes/sidebar.php';
    renderSidebar('preparing');
    ?>

    <main class="main-content">
        <!-- Mobile Menu Toggle -->
        <div class="mobile-menu-toggle d-lg-none">
            <button class="btn btn-dark" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <h2 style="text-align: center;">List of orders</h2>
        <div class="order-list">
            <?php
            require_once 'database_admin.php';
            
            // Query to get orders that are "Preparing" with "Completed" payment status
            $sql = "SELECT 
                        o.Order_ID,
                        o.Order_TicketNumber,
                        o.Order_Status,
                        o.Order_EatingOption,
                        o.Order_TotalAmount,
                        GROUP_CONCAT(
                            CONCAT(
                                oi.OrderItem_Quantity, 
                                ' x ', 
                                m.MenuItem_Name, 
                                ' (', 
                                oi.OrderItem_CupSize,
                                ')'
                            ) 
                            SEPARATOR '<br>'
                        ) as items,
                        p.Payment_Method,
                        p.Payment_DateTime
                    FROM `order` o
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID
                    JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
                    JOIN payment p ON o.Payment_ID = p.Payment_ID
                    WHERE o.Order_Status = 'Preparing' 
                    AND p.Payment_Status = 'Completed'
                    GROUP BY o.Order_ID
                    ORDER BY p.Payment_DateTime ASC";
            
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="ticket-info">
                                <h3>Ticket #<?php echo htmlspecialchars($row['Order_TicketNumber']); ?></h3>
                                <span class="status-badge <?php echo strtolower($row['Order_Status']); ?>">
                                    <?php echo $row['Order_Status']; ?>
                                </span>
                            </div>
                            <div class="order-time">
                                <?php echo date('M d, Y h:i A', strtotime($row['Payment_DateTime'])); ?>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <div class="order-type">
                                <i class="fas <?php echo $row['Order_EatingOption'] === 'Dine-in' ? 'fa-utensils' : 'fa-shopping-bag'; ?>"></i>
                                <?php echo htmlspecialchars($row['Order_EatingOption']); ?>
                                <div class="payment-method">
                                <i class="fas <?php echo $row['Payment_Method'] === 'Cash' ? 'fa-money-bill' : 'fa-mobile-alt'; ?>"></i>
                                <?php echo htmlspecialchars($row['Payment_Method']); ?>
                            </div>
                            </div>
                            
                        </div>
                        
                        <div class="order-items">
                            <h4>Items:</h4>
                            <div class="items-list">
                                <?php echo $row['items']; ?>
                            </div>
                        </div>
                        
                        <div class="order-footer">
                            <div class="total-amount">
                                <strong>Total:</strong> ₱<?php echo number_format($row['Order_TotalAmount'], 2); ?>
                            </div>
                            <div class="action-buttons">
                                <button class="action-btn decline" data-order-id="<?php echo $row['Order_ID']; ?>" title="Cancel Order">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="action-btn approve" data-order-id="<?php echo $row['Order_ID']; ?>" title="Mark as Ready">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="no-orders">No orders in preparation at the moment.</p>';
            }
            ?>
        </div>
    </main>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="confirmationText">Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

            
            

<!-- Bootstrap JS and Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="Css-admin/bootstrap.bundle.min.js"></script>

<!-- Admin Modal -->
<link rel="stylesheet" href="Css-admin/admin-modal.css">
<script src="Javascript-admin/admin-modal.js"></script>

<!-- Custom Scripts -->
<script src="Javascript-admin/order_status_handler.js"></script>
<script src="Javascript-admin/mobile-menu.js"></script>
<script src="Javascript-admin/auto-refresh.js"></script>

<script>
// Initialize all components and handlers
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Initialize Bootstrap modals
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'), {
        backdrop: 'static',
        keyboard: false
    });
    window.confirmationModal = confirmationModal;

    // Initialize order handlers
    if (window.initializeOrderHandlers) {
        window.initializeOrderHandlers();
    }

    // Add hover effects to action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.transform = 'scale(1.1)');
        btn.addEventListener('mouseleave', () => btn.style.transform = 'scale(1)');
    });

    // Prevent modal from closing when clicking outside
    document.getElementById('confirmationModal').addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.stopPropagation();
        }
    });

    // Handle modal close button
    document.querySelector('#confirmationModal .btn-close').addEventListener('click', () => {
        confirmationModal.hide();
    });

    // Handle modal No button
    document.querySelector('#confirmationModal .btn-secondary').addEventListener('click', () => {
        confirmationModal.hide();
    });
});
</script>

<!-- Add refresh indicator and button styles -->
<style>
    .refresh-indicator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: rgba(255, 193, 7, 0.9);
        color: black;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        display: none;
        animation: fadeInOut 1s ease;
        z-index: 1000;
    }
    @keyframes fadeInOut {
        0% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 0; }
    }

    /* Enhanced Button Styles */
    .action-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        z-index: 1;
        user-select: none;
        -webkit-user-select: none;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border-radius: 50%;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .action-btn:hover::before {
        opacity: 1;
    }

    .action-btn.approve {
        background-color: #28a745;
        color: white;
    }

    .action-btn.approve::before {
        background-color: rgba(40, 167, 69, 0.2);
    }

    .action-btn.decline {
        background-color: #dc3545;
        color: white;
    }

    .action-btn.decline::before {
        background-color: rgba(220, 53, 69, 0.2);
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn:active {
        transform: scale(0.95);
    }

    .action-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .action-buttons {
        position: relative;
        z-index: 2;
        display: flex;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .action-btn {
            width: 36px;
            height: 36px;
        }

        .action-btn i {
            font-size: 1rem;
        }
    }
</style>
<div class="refresh-indicator">
    <i class="fas fa-sync-alt"></i> Refreshing...
</div>
</body>
</html>
