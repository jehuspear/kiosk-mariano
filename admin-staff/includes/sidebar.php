<?php
function renderSidebar($currentPage = '') {
    // Get user's role from session
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    
    // Add CSS and JS for notifications
    static $resources_included = false;
    if (!$resources_included) {
        echo '<link rel="stylesheet" href="Css-admin/sidebar-notifications.css">';
        echo '<script src="Javascript-admin/sidebar-notifications.js" defer></script>';
        
        // Add notification sound if not on pending orders page
        if ($currentPage !== 'pending') {
            echo '<audio id="notificationSound" style="display: none;">
                    <source src="../customer/resources/sounds/notification.mp3" type="audio/mpeg">
                  </audio>';
        }
        $resources_included = true;
    }
    ?>
    <div class="sidebar">
        <div class="logo">
            <img src="Images/logo/logo.png" alt="SINCO CAFE" class="logo-img">
            <h2>SINCO CAFE</h2>
            <p class="welcome-text">Hello, <?php echo isset($_SESSION['firstname']) ? $_SESSION['firstname'] : 'User'; ?></p>
        </div>
        
        <div class="sidebar-content">
            <?php if ($userRole === 'Admin'): ?>
            <div class="sidebar-section">
                <h5 class="sidebar-heading">Management</h5>
                <ul class="nav">
                    <li class="<?php echo $currentPage === 'staff' ? 'active' : ''; ?>">
                        <a href="home.php"><i class="fa-solid fa-users"></i> <span>Staff</span></a>
                    </li>
                    <li class="<?php echo $currentPage === 'menu' ? 'active' : ''; ?>">
                        <a href="menuscreen.php"><i class="fa-solid fa-book-open"></i> <span>Menu</span></a>
                    </li>
                </ul>
            </div>
            <?php else: ?>
            <div class="sidebar-section">
                <h5 class="sidebar-heading">Management</h5>
                <ul class="nav">
                    <li class="<?php echo $currentPage === 'menu' ? 'active' : ''; ?>">
                        <a href="menuscreen.php"><i class="fa-solid fa-book-open"></i> <span>Menu</span></a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

            <div class="sidebar-section">
                <h5 class="sidebar-heading">Orders</h5>
                <ul class="nav">
                    <li class="<?php echo $currentPage === 'pending' ? 'active' : ''; ?>">
                        <a href="pending-orders.php">
                            <i class="fa-solid fa-hourglass-start"></i>
                            <span>Pending</span>
                            <!-- Pending badge will be added here by JavaScript -->
                        </a>
                    </li>
                    <li class="<?php echo $currentPage === 'preparing' ? 'active' : ''; ?>">
                        <a href="preparing-orders.php">
                            <i class="fa-solid fa-mug-hot"></i>
                            <span>Preparing</span>
                            <!-- Preparing badge will be added here by JavaScript -->
                        </a>
                    </li>
                    <li class="<?php echo $currentPage === 'completed' ? 'active' : ''; ?>">
                        <a href="completed-orders.php"><i class="fa-solid fa-check-circle"></i> <span>Completed</span></a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h5 class="sidebar-heading">Analytics</h5>
                <ul class="nav">
                    <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="<?php echo $currentPage === 'feedback' ? 'active' : ''; ?>">
                        <a href="feedback.php"><i class="fa-solid fa-comments"></i> <span>Feedback</span></a>
                    </li>
                    <li class="<?php echo $currentPage === 'history' ? 'active' : ''; ?>">
                        <a href="history.php"><i class="fa-solid fa-history"></i> <span>History</span></a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section mt-auto">
                <ul class="nav">
                    <li><a href="logout.php" class="logout-link"><i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php
    // Add notification sound if not on pending or preparing orders pages
    if ($currentPage !== 'pending' && $currentPage !== 'preparing'): ?>
    <audio id="notificationSound" style="display: none;">
        <source src="../customer/resources/sounds/notification.mp3" type="audio/mpeg">
    </audio>
    <?php endif; ?>
    <?php
}
?>
