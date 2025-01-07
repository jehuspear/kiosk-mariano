<?php
session_start();

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if user is not admin
if($_SESSION["role"] !== "Admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Staff Management</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
        <link rel="stylesheet" href="Css-admin/home.css">
        <link rel="stylesheet" href="Css-admin/sidebar.css">
    </head>
    <body>
        <div class="wrapper">
            <?php 
            require_once 'includes/sidebar.php';
            renderSidebar('staff');
            ?>
            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Title Bar -->
                <div class="page-title-bar">
                    <div class="d-flex align-items-center" style="text-align: center;">
                        <div class="mobile-menu-toggle d-lg-none">
                            <button class="btn btn-dark" id="sidebarToggle">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                        <h2 >Staff Management</h2>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="fas fa-plus"></i> Add New Staff
                    </button>
                </div>

                <div class="container">
                    
                    <!-- Search Bar -->
                    <div class="row mb-4">
                        <div class="col">
                            <input type="text" id="searchStaff" class="form-control" placeholder="Search staff...">
                        </div>
                    </div>

                    <!-- Staff Table -->
                    <div class="table-responsive" style="text-align: center;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                <!-- Staff data will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addStaffModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addStaffForm">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="firstName" placeholder="First Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="middleName" placeholder="Middle Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="lastName" placeholder="Last Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="contactNumber" placeholder="Contact Number" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="address" placeholder="Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="date" class="form-control" name="birthDate" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="role" required>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveStaffBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Staff Modal -->
        <div class="modal fade" id="editStaffModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editStaffForm">
                            <input type="hidden" name="staffId">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="firstName" placeholder="First Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="middleName" placeholder="Middle Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="lastName" placeholder="Last Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="contactNumber" placeholder="Contact Number" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="address" placeholder="Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="date" class="form-control" name="birthDate" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="role" required>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateStaffBtn">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteStaffModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this staff member?</p>
                        <input type="hidden" id="deleteStaffId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- JavaScript -->
        <script src="Javascript-admin/staff-management.js"></script>
        <script src="Javascript-admin/mobile-menu.js"></script>
    </body>
</html>
