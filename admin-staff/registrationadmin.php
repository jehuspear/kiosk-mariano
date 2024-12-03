<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Screen</title>
  <link rel="stylesheet" href="Css-admin/registrationadmin.css">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="image-container">
        <img src="Images/aaaaaa.jpg" alt="Coffee Machine">
      </div>
      <div class="form">
        <h1>Get Started</h1>
        <form>
          <div class="input-row">
            <input type="text" placeholder="Firstname" required>
            <input type="text" placeholder="Lastname" required>
          </div>
          <input type="text" placeholder="Username" required>
          <input type="password" placeholder="Password" required>
          <button type="submit">Complete Registration</button>
        </form>
        <p class="signup-text">
          Already have an account? <a href="login.php" class="signup-link">Login</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
