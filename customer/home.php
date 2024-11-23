<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mariano Cafe - Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
            min-height: 100vh;
        }
        .home-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
        }
        .logo-container {
            text-align: center;
            margin-top: 15vh;
        }
        .logo {
            max-width: 250px;
            height: auto;
            margin-bottom: 2rem;
        }
        .tagline {
            font-size: 1.2rem;
            letter-spacing: 1px;
            color: #fff;
            font-weight: 300;
        }
        .tap-to-start {
            text-align: center;
            margin-bottom: 15vh;
        }
        .start-btn {
            background: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 1rem 3rem;
            font-size: 1.25rem;
            border-radius: 30px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
        }
        .start-btn:hover {
            background: #fff;
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255,255,255,0.3);
            text-decoration: none;
        }
        @media (max-width: 576px) {
            .logo {
                max-width: 180px;
                margin-bottom: 1.5rem;
            }
            .tagline {
                font-size: 1rem;
                padding: 0 1rem;
            }
            .start-btn {
                font-size: 1.1rem;
                padding: 0.75rem 2.5rem;
            }
            .home-page {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="home-page container-fluid">
        <div class="logo-container">
            <img src="resources/images/logo.png" alt="Mariano Cafe Logo" class="logo img-fluid">
            <!-- <p class="tagline">Where Good Coffee Starts</p> -->
        </div>
        <div class="tap-to-start">
            <a href="menu.php" class="start-btn">Tap To Start</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
