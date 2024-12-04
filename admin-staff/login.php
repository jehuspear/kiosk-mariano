<?php
session_start();
include 'database_admin.php';

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Check if username exists
        $sql = "SELECT * FROM staff WHERE Staff_Username = ? OR Staff_Email = ?";
        $stmt = mysqli_stmt_init($conn);
        
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($user = mysqli_fetch_assoc($result)) {
                // Verify password
                if (password_verify($password, $user["Staff_Password"])) {
                    // Password is correct, start session
                    $_SESSION["user_id"] = $user["Staff_ID"];
                    $_SESSION["username"] = $user["Staff_Username"];
                    $_SESSION["role"] = $user["Staff_Role"];
                    
                    // Redirect to home page
                    header("Location: home-admin.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
        } else {
            $error = "Something went wrong";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="Css-admin/login.css" />
  </head>
  <body>
    <div class="login-wrapper">
      <h1 class="welcome-header">Welcome!</h1>
      <?php
      if (isset($error)) {
          echo '<div class="error-message">' . $error . '</div>';
      }
      ?>
      <form class="login-form" method="POST" action="">
        <div class="input-group">
          <input
            type="text"
            name="username"
            class="input-field"
            placeholder="Username or Email"
          />
        </div>
        <div class="input-group">
          <input
            type="password"
            name="password"
            class="input-field"
            placeholder="Password"
          />
        </div>
        <div class="remember-group">
          <input type="checkbox" id="remember" name="remember" class="remember-checkbox" />
          <label for="remember" class="remember-label">Remember me</label>
        </div>
        <button type="submit" name="login" class="login-button">Login</button>
      </form>
      <p class="signup-text">
        Don't have an account? <a href="registrationadmin.php" class="signup-link">Signup</a>
      </p>
    </div>
  </body>
</html>
