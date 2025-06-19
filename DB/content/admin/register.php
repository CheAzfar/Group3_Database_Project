<?php
session_start();
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data and sanitize
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $role = 'staff';


    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Using BCRYPT for better security

    // Insert into the database (assuming there's a `users` table)
    $stmt = $conn->prepare("INSERT INTO users (UserID, UserPwd, UserType) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $role);
    $stmt->execute();

    // Check if the insertion was successful
    if ($stmt->affected_rows > 0) {
        echo "<div class='alert alert-success'>User registered successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background color */
            color: #495057; /* Dark text color */
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #f4a261;
            color: white;
        }
        .btn-custom:hover {
            background-color: #e76f51;
            color: white;
        }
        h1 {
            color: #f4a261;
        }
        label {
            font-weight: bold;
        }
        #password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-weak {
            color: red;
        }
        .strength-medium {
            color: orange;
        }
        .strength-strong {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Staff Registration</h1>
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                        <i id="password-icon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <div id="password-strength"></div>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-custom w-100">Register</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            const strengthIndicator = document.getElementById('password-strength');
            strengthIndicator.textContent = 'Strength: ' + strength.label;
            strengthIndicator.className = 'strength-' + strength.strength;
        });

        // Function to check password strength
        function checkPasswordStrength(password) {
            let strength = { label: 'Weak', strength: 'weak' };
            if (password.length >= 8 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password)) {
                strength = { label: 'Strong', strength: 'strong' };
            } else if (password.length >= 6) {
                strength = { label: 'Medium', strength: 'medium' };
            }
            return strength;
        }
    </script>
</body>
</html>
