<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION["user_id"])) {
    header("Location: menuscreen.php");
    exit();
}

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
            $staff = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($staff) {
                // Verify password
                if (password_verify($password, $staff["Staff_Password"])) {
                    // Password is correct, start session
                    $_SESSION["user_id"] = $staff["Staff_ID"];
                    $_SESSION["username"] = $staff["Staff_Username"];
                    $_SESSION["role"] = $staff["Staff_Role"];
                    
                    // Handle Remember Me
                    if(isset($_POST["remember"]) && $_POST["remember"] == "on") {
                        // Set cookies for 30 days
                        setcookie("user_login", $username, time() + (30 * 24 * 60 * 60), "/");
                        setcookie("user_password", $password, time() + (30 * 24 * 60 * 60), "/");
                    }
                    
                    // Redirect to menu screen
                    header("Location: menuscreen.php");
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
     <!-- Custom CSS -->
    <link rel="stylesheet" href="Css-admin/login.css" />
      <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
 
  </head>
  <body>
    <div class="login-wrapper">
      <h1 class="welcome-header">Welcome!</h1>
      <?php
      if (isset($error)) {
          echo '<div class="alert alert-danger">' . $error . '</div>';
      }
      ?>
      <form class="login-form" method="POST" action="">
        <div class="input-group">
          <input
            type="text"
            name="username"
            class="input-field"
            placeholder="Username or Email"
            value="<?php echo isset($_COOKIE['user_login']) ? $_COOKIE['user_login'] : ''; ?>"
          />
        </div>
        <div class="input-group">
          <input
            type="password"
            name="password"
            class="input-field"
            placeholder="Password"
            value="<?php echo isset($_COOKIE['user_password']) ? $_COOKIE['user_password'] : ''; ?>"
          />
        </div>
        <div class="remember-group">
          <input type="checkbox" id="remember" name="remember" class="remember-checkbox" />
          <label for="remember" class="remember-label">Remember me</label>
        </div>
        <button type="submit" name="login" class="login-button">Login</button>
      </form>
      <p class="signup-text">
        Don't have a Staff account? <a href="registrationadmin.php" class="signup-link">Sign up</a>
      </p>
    </div>
  </body>
</html>
