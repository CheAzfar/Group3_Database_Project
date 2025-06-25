<?php
session_start();
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Handle Delete
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ? AND UserType = 'staff'");
    $stmt->bind_param("s", $deleteId);
    if ($stmt->execute()) {
        $success = "Staff user deleted successfully.";
    } else {
        $error = "Failed to delete staff user.";
    }
    $stmt->close();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $userID = trim($_POST['update_id']);
    $newUsername = trim($_POST['username']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $error = '';

    if (!empty($newPassword)) {
        if ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } elseif (!preg_match('/^[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/\d/', $newPassword) || strlen($newPassword) < 8) {
            $error = "Password must start with a capital letter, include lowercase, a number, and be at least 8 characters.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET UserID = ?, UserPwd = ? WHERE UserID = ? AND UserType = 'staff'");
            $stmt->bind_param("sss", $newUsername, $hashedPassword, $userID);
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET UserID = ? WHERE UserID = ? AND UserType = 'staff'");
        $stmt->bind_param("ss", $newUsername, $userID);
    }

    if (empty($error)) {
        if ($stmt->execute()) {
            $success = "Staff user updated successfully.";
        } else {
            $error = "Failed to update staff user.";
        }
        $stmt->close();
    }
}


// Handle search
$search = $_GET['search'] ?? '';
$query = "SELECT UserID FROM users WHERE UserType = 'staff'";
if (!empty($search)) {
    $query .= " AND UserID LIKE ?";
    $stmt = $conn->prepare($query);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
    $stmt->execute();
    $staffList = $stmt->get_result();
} else {
    $staffList = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #f4a261;
            color: white;
        }
        .btn-custom:hover {
            background-color: #e76f51;
        }
        h2 {
            color: #f4a261;
        }
        form.inline-form {
            display: flex;
            gap: 10px;
        }
        .form-control-inline {
            width: auto;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Manage Staff Accounts</h2>

    <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <!-- Search Form -->
    <form class="inline-form mb-3" method="GET" action="">
        <input type="text" name="search" class="form-control form-control-inline" placeholder="Search by UserID" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-custom">Search</button>
        <a href="manage_staff.php" class="btn btn-secondary">Reset</a>
    </form>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Current UserID</th>
            <th>New UserID</th>
            <th>New Password</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($staffList->num_rows > 0): ?>
            <?php while ($row = $staffList->fetch_assoc()) { ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($row['UserID']) ?></td>
                        <td><input type="text" name="username" value="<?= htmlspecialchars($row['UserID']) ?>" class="form-control" required></td>
                        <td>
                            <input type="password" name="new_password" class="form-control password-field" placeholder="New password">
                            <div class="text-muted small password-strength"></div>
                            <input type="password" name="confirm_password" class="form-control mt-1 confirm-password-field" placeholder="Confirm password">
                            <div class="text-danger small password-error"></div>
                        </td>

                        <td class="d-flex gap-2">
                            <input type="hidden" name="update_id" value="<?= $row['UserID'] ?>">
                            <button type="submit" class="btn btn-custom btn-sm">Update</button>
                            <a href="?delete=<?= urlencode($row['UserID']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($row['UserID']) ?>?')">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No staff users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
    <script>
        document.querySelectorAll('form').forEach(form => {
            const passwordField = form.querySelector('.password-field');
            const confirmPasswordField = form.querySelector('.confirm-password-field');
            const errorBox = form.querySelector('.password-error');
            const strengthBox = form.querySelector('.password-strength');
            const submitBtn = form.querySelector('button[type="submit"]');

            if (passwordField && confirmPasswordField) {
                form.addEventListener('submit', function (e) {
                    const password = passwordField.value.trim();
                    const confirmPassword = confirmPasswordField.value.trim();

                    if (password !== '' || confirmPassword !== '') {
                        const startsWithCapital = /^[A-Z]/.test(password);
                        const hasLowercase = /[a-z]/.test(password);
                        const hasNumber = /\d/.test(password);
                        const minLength = password.length >= 8;

                        if (!startsWithCapital || !hasLowercase || !hasNumber || !minLength) {
                            errorBox.textContent = "Password must start with a capital letter, include lowercase, a number, and be at least 8 characters.";
                            e.preventDefault();
                            return;
                        }

                        if (password !== confirmPassword) {
                            errorBox.textContent = "Passwords do not match.";
                            e.preventDefault();
                            return;
                        }
                    }

                    errorBox.textContent = ""; // Clear errors
                });

                // Live password strength check
                passwordField.addEventListener('input', function () {
                    const pwd = passwordField.value;
                    const strength = getStrength(pwd);
                    strengthBox.textContent = `Strength: ${strength.label}`;
                    strengthBox.style.color = strength.color;
                });
            }

            function getStrength(password) {
                let score = 0;
                if (password.length >= 8) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[a-z]/.test(password)) score++;
                if (/\d/.test(password)) score++;
                if (/[\W]/.test(password)) score++;

                if (score >= 4) return { label: "Strong", color: "green" };
                if (score === 3) return { label: "Medium", color: "orange" };
                if (score > 0) return { label: "Weak", color: "red" };
                return { label: "", color: "inherit" };
            }
        });
    </script>
</html>
