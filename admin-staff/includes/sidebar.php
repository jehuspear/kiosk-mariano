<?php
function renderSidebar($currentPage = '') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = 'Please log in to access this page.';
        header('Location: login.php');
        exit;
    }

    // Get user's role from session
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    
    // Include error message display
    require_once __DIR__ . '/error_message.php';
    
    // Add CSS and JS for notifications
    static $resources_included = false;
    if (!$resources_included) {
        echo '<link rel="stylesheet" href="Css-admin/sidebar-notifications.css">';
        echo '<link rel="stylesheet" href="Css-admin/role-display.css">';
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
            <img src="Images/logo/logo2.png" alt="SINCO CAFE" class="logo-img">
            <!-- <h2>SINCO CAFE</h2> -->
            <p class="welcome-text">
                Hello, <?php echo isset($_SESSION['firstname']) ? htmlspecialchars($_SESSION['firstname']) : 'User'; ?>
                <br>
                <small class="role-<?php echo strtolower(htmlspecialchars($userRole)); ?>">
                    <?php echo ucfirst(htmlspecialchars($userRole)); ?>
                </small>
            </p>
        </div>
        
        <div class="sidebar-content">
            <?php if ($userRole === 'Admin'): ?>
                <div class="sidebar-section">
                    <h5 class="sidebar-heading">Management</h5>
                    <ul class="nav">
                        <li class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                            <a href="home.php"><i class="fa-solid fa-home"></i> <span>Home Dashboard</span></a>
                        </li>
                        <li class="<?php echo $currentPage === 'staff-management' ? 'active' : ''; ?>">
                            <a href="staff-management.php"><i class="fa-solid fa-users"></i> <span>Staff</span></a>
                        </li>
                        <li class="<?php echo $currentPage === 'menuscreen' ? 'active' : ''; ?>">
                            <a href="menuscreen.php"><i class="fa-solid fa-book-open"></i> <span>Menu</span></a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Order Management - Accessible to both Admin and Staff -->
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

            <?php if ($userRole === 'Admin'): ?>
                <!-- Analytics - Admin Only -->
                <div class="sidebar-section">
                    <h5 class="sidebar-heading">Analytics</h5>
                    <ul class="nav">
                        <li class="<?php echo $currentPage === 'feedback' ? 'active' : ''; ?>">
                            <a href="feedback.php"><i class="fa-solid fa-comments"></i> <span>Feedback</span></a>
                        </li>
                        <li class="<?php echo $currentPage === 'history' ? 'active' : ''; ?>">
                            <a href="history.php"><i class="fa-solid fa-history"></i> <span>History</span></a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

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
