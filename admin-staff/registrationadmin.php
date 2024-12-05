<!-- PHP Code Here -->
<?php

include 'database_admin.php'; //Connection to the phpMyAdmin SQL database

session_start();

// Check if user is already logged in
if(isset($_SESSION["user_id"])) {
    header("Location: menuscreen.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Screen</title>

  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="Css-admin/registrationadmin.css">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="image-container">
        <img src="Images/aaaaaa.jpg" alt="Coffee Machine">
      </div>
      
      <div class="form">
        <!-- PHP Code for Sign Up here -->
<?php
        if(isset($_POST["submit"])){
        $LastName = $_POST["LastName"];
        $FirstName = $_POST["FirstName"];
        $MiddleName = $_POST["MiddleName"];
        $DOB = date("Y-m-d", strtotime($_POST["DateofBirth"]));

        // Address
        $City = $_POST["City"];
        $Barangay = $_POST["Barangay"];
        $Street = $_POST["Street"];
        $Address = $Street . ", " . $Barangay . ", " . $City;

        $Email = $_POST["Email"];
        $Contact = $_POST["ContactNo"];
        $Username = $_POST["Username"];
        $password = $_POST["password"];
        $RepeatPassword = $_POST["repeat_password"];
        $Role = "Staff"; // Default role
        $errors = array();

	    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
 
        // validate if all fields are empty
        if (empty($LastName) OR empty($FirstName) OR empty($MiddleName) OR empty($DOB) OR empty($City) OR empty($Barangay) OR empty($Street)  OR empty($Email) OR empty($Contact) OR empty($Username) OR empty($password) OR empty($RepeatPassword)) {
            array_push($errors,"All fields are required");
        }
        // validate if the email is not validated
        if (!filter_var($Email,FILTER_VALIDATE_EMAIL)){
            array_push($errors,"Email is not valid");
        }
        // password should not be less than 8
        if (strlen($password)<8) {
            array_push($errors,"Password must be atleast 8 characters long");
        }
         // check if password is the same
         if($password!= $RepeatPassword){
            array_push($errors,"Password does not match");          
        }

        // Calculate age based on Date of Birth
        $dobTimestamp = strtotime($DOB);
        $currentTimestamp = time();
        $age = floor(($currentTimestamp - $dobTimestamp) / (365.25 * 24 * 60 * 60));

    
        require_once "database_admin.php";
        // Checking if the email is already taken
        $sql1 = "SELECT * FROM staff WHERE Staff_Email = '$Email'";
       
        $result1 = mysqli_query($conn, $sql1);
        $rowCount1 = mysqli_num_rows($result1);
        
        if ($rowCount1 > 0){
            array_push($errors, "Email Already Taken!");
        }
        // Checking if the Username is already taken
        $sql2 = "SELECT * FROM staff WHERE Staff_Username = '$Username'";
        $result2 = mysqli_query($conn, $sql2);
        $rowCount2 = mysqli_num_rows($result2);
        
        if ($rowCount2 > 0){
            array_push($errors, "Username Already Taken!");
        }
        if (count($errors)> 0){
                foreach($errors as $error) {
                    echo"<div class='alert alert-danger'>$error</div>";
                }
             } else {
              // Insert into the Database
                require_once "database_admin.php";
                $sql ="INSERT INTO staff (Staff_Username, Staff_Password, Staff_Firstname, Staff_MiddleName, Staff_Lastname, Staff_ContactNumber, Staff_Email, Staff_Address, Staff_BirthDate, Staff_Role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $preparestmt = mysqli_stmt_prepare($stmt, $sql);
            if ($preparestmt) {
                mysqli_stmt_bind_param($stmt, "ssssssssss", $Username, $passwordHash, $FirstName, $MiddleName, $LastName, $Contact, $Email, $Address, $DOB, $Role);
                mysqli_stmt_execute($stmt);
                echo "<div class = 'alert alert-success'> Staff Information is Registered Successfully! </div>";
            } else {
                die("Something went wrong!");
            }
             }
            }
             ?>
        <h1 style="font-weight: 500;">Staff Registration</h1>
        <form action="registrationadmin.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="LastName" placeholder="Last Name: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="FirstName" placeholder="First Name: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="MiddleName" placeholder="Middle Name: " required>
            </div>
            <label style="text-align: center;">Enter Date of Birth: </label>
            <div class="form-group">
                <input type="date" class="form-control" name="DateofBirth" placeholder="Date of Birth: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="City" placeholder="City: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="Barangay" placeholder="Barangay: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="Street" placeholder="Street: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="Email" placeholder="Email: " required>
            </div>
            <div class="form-group">
                <input type="int" class="form-control" name="ContactNo" placeholder="Contact No: " required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="Username" placeholder="Username: " required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password: " required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password: " required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary key" name="submit" placeholder="Submit ">
            </div>
            <div><p> Already Have an Account? <a href="login.php"> Login Here</a></div>
        </form>
    
      </div>
    </div>
  </div>
</body>
</html>
