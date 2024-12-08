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
                <form method="POST" action="registerUSER.php">
                    <div class="input-row">
                        <input type="text" name="firstname" placeholder="Firstname" required>
                        <input type="text" name="lastname" placeholder="Lastname" required>
                    </div>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password_1" placeholder="Password" required>
                    <input type="password" name="password_2" placeholder="Confirm Password" required>
                    <button type="submit" name="reg_user">Complete Registration</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
