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
        <form class="login-form" method="POST" action="loginADMIN.php">
            <div class="input-group">
                <input
                    type="text"
                    id="username"
                    class="input-field"
                    placeholder="Username"
                    name="username"
                    required
                />
            </div>
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    class="input-field"
                    placeholder="Password"
                    name="password"
                    required
                />
            </div>
            <div class="remember-group">
                <input type="checkbox" id="remember" class="remember-checkbox" />
                <label for="remember" class="remember-label">Remember me</label>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        <!-- Display error message -->
        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <p class="signup-text">
            Don’t have an account? <a href="registrationadmin.php" class="signup-link">Signup</a>
        </p>
    </div>
</body>
</html>
