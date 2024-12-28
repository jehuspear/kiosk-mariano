<?php
session_start();

// Check if user is not logged in
// if(!isset($_SESSION["user_id"])) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orderscreen</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/order.css">
        <link rel="stylesheet" href="Css-admin/cancel_order.css">
        <link rel="stylesheet" href="Css-admin/confirm_order.css">
        <link rel="stylesheet" href="Css-admin/search_order.css">
    </head>
    <body>
        <div class="wrapper">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="logo">
                    <h2>SINCO CAFE</h2>
                </div>
                <ul class="nav">
                    <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i> <span>Logout</span></a></li>
                    <li><a href="menuscreen.php"><i class="fa-solid fa-book"></i> Menu</a></li>
                    <li><a href="decodingscreen.php"><i class="fa-solid fa-ticket"></i> E-ticket</a></li>
                    <li><a href="pending-orders.php"><i class="fa-solid fa-mug-hot"></i> Pending</a></li>
                    <li><a href="preparing-orders.php"><i class="fa-solid fa-sort"></i> Order Lists</a></li>
                    <li><a href="completed-orders.php"><i class="fa-solid fa-check-to-slot"></i> Completed</a></li>
                    <li><a href="reports.php"><i class="fa-solid fa-newspaper"></i> Dashboard</a></li>
                    <li><a href="feedback.php"><i class="fa-regular fa-comment"></i> Feedback</a></li>
                    <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
                </ul>
            </div>
            <!-- Main Content -->
            <div class="main-content">
                <div class="text-wrapper-10">Pending Orders</div>
                <!-- Search Bar -->
                <div class="search-container mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchTicket" class="form-control" placeholder="Search Order Ticket Number...">
                    </div>
                </div>
                <div class="navbar">
                    
                    <div class="navbar-item">Order Ticket No</div>
                    
                    <div class="navbar-item">Eating Option</div>
                    <div class="navbar-item">Product ID</div>
                    <div class="navbar-item">Order Item</div>
                    <div class="navbar-item">Payment</div>
                    <div class="navbar-item">Date</div>
                    <div class="navbar-item">Discount</div>
                    <div class="navbar-item">Total</div>
                    <div class="navbar-item">Status</div>
                </div>

  
            <div class="order-details-container">
            <?php
            require_once 'database_admin.php';
            
            $sql = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.OrderItem_Quantity, ' x ', m.MenuItem_Name, ' ', oi.OrderItem_CupSize) SEPARATOR '<br>') as items
                    FROM `order` o
                    JOIN orderitem oi ON o.Order_ID = oi.Order_ID
                    JOIN menuitem m ON oi.MenuItem_ID = m.MenuItem_ID
                    WHERE o.Order_Status = 'Pending'
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_DateTime DESC";
                    
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="order">
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_TicketNumber']); ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_EatingOption']); ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Order_ID']); ?></div>
                        <div class="order-item"><?php echo $row['items']; ?></div>
                        <div class="order-item"><?php echo htmlspecialchars($row['Payment_Method']); ?></div>
                        <div class="order-item"><?php echo date('m/d/y', strtotime($row['Order_DateTime'])); ?></div>
                        <div class="order-buttons">
                            <button class="button add-button" style="margin-left:40px">Add</button>
                        </div>
                        <div class="order-item">₱<?php echo number_format($row['Order_TotalAmount'], 2); ?></div>
                        <div class="order-buttons">
                            <button class="button done-button" style="margin-left:40px" data-order-id="<?php echo $row['Order_ID']; ?>">Confirm</button>
                            <button class="button-cancel" style="margin-left:40px" data-order-id="<?php echo $row['Order_ID']; ?>">Cancel</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            ?>
            </div>

            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="confirmationModalLabel">Confirmation</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <h4>Is the order paid?</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="noButton" data-bs-dismiss="modal">No</button>
                        <button type="button" class="btn btn-success" id="yesButton">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Cancel Confirmation -->
        <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelConfirmationModalLabel">Cancel Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this order?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelButton">Yes, Cancel Order</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Discount -->
        <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discountModalLabel">Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="discount-buttons">
                    <button type="button" class="btn btn-primary me-2" data-discount="10">10%</button>
                    <button type="button" class="btn btn-primary me-2" data-discount="15">15%</button>
                    <button type="button" class="btn btn-primary me-2" data-discount="20">20%</button>
                    <button type="button" class="btn btn-secondary" id="customDiscountButton">Custom</button>
                </div>
                <div id="customDiscountInput" class="mt-3" style="display: none;">
                    <label for="customDiscountValue" class="form-label">Enter Custom Discount (%)</label>
                    <input type="number" id="customDiscountValue" class="form-control" placeholder="e.g., 25">
                    <button type="button" class="btn btn-success mt-2" id="applyCustomDiscount">Apply</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- JavaScript -->
        <script src="Javascript-admin/confirm_order.js"></script>
        <script src="Javascript-admin/cancel_order.js"></script>
        <script src="Javascript-admin/search_order.js"></script>
    </body>
</html>
