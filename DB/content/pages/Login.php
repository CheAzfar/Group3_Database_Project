<?php
// Start session and check for errors passed from login_Form.php
session_start();
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the error after displaying
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puan Zai Highway - Admin Login</title>

    <!-- Montserrat Font Import -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
    </style>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            background-size: cover;
            background-position: center;
            font-family: 'Montserrat', sans-serif;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 90%;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4),
                0 10px 25px rgba(0, 0, 0, 0.3),
                inset 0 -5px 15px rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
        }

        .container:hover {
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5),
                0 15px 35px rgba(0, 0, 0, 0.4),
                inset 0 -8px 20px rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
        }

        .login-section {
            background-color: white;
            padding: 40px;
            width: 50%;
        }

        .welcome-back {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .portal-title {
            color: #E76F51;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 2px solid #d35400;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .login-Form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: 'Montserrat', sans-serif;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .remember-me input {
            margin-right: 10px;
        }

        .login-btn {
            background-color: #E76F51;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .login-btn:hover {
            background-color: #e67e22;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .icon {
            text-align: center;
            margin: 20px 0;
        }

        .icon i {
            font-size: 40px;
            color: #E76F51;
            filter: drop-shadow(2px 2px 3px rgba(0, 0, 0, 0.2));
        }

        .banner-section {
            width: 50%;
            background: url('../../assets/images/login_banner.png') center/cover no-repeat;
            position: relative;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .error-message {
            color: #d9534f;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            }

            .container:hover {
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            }

            .login-section,
            .banner-section {
                width: 100%;
                min-height: 300px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="login-section">
            <h1 class="welcome-back">Welcome Back!</h1>
            <h2 class="portal-title">Admin Portal – Authorized Personnel Only</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form class="login-Form" action="login_Form.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="UserID" name="UserID" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="UserPwd" name="UserPwd" required>
                </div><br>

                <button type="submit" class="login-btn">Login</button>
            </form>

            <div class="icon">
                <i class="fas fa-utensils"></i>
            </div>
        </div>

        <div class="banner-section">
            <div class="banner-overlay"></div>
        </div>
    </div>
</body>

</html>