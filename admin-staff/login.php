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
      <form class="login-form">
        <div class="input-group">
          <input
            type="text"
            id="username"
            class="input-field"
            placeholder="User name or Email"
          />
        </div>
        <div class="input-group">
          <input
            type="password"
            id="password"
            class="input-field"
            placeholder="Password"
          />
        </div>
        <div class="remember-group">
          <input type="checkbox" id="remember" class="remember-checkbox" />
          <label for="remember" class="remember-label">Remember me</label>
        </div>
        <button type="submit" class="login-button">Login</button>
      </form>
      <p class="signup-text">
        Don’t have an account? <a href="registrationadmin.html" class="signup-link">Signup</a>
      </p>
    </div>
  </body>
</html>
