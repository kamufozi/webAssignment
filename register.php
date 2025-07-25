<?php
session_start(); // Start session for success message

// Include database configuration
require_once 'config.php'; // Make sure your config file is named config.php
if (!isset($pdo)) {
    die("PDO not initialized. Check config.php.");
}

// Initialize variables for error handling
$errors = [];
$success_message = '';

// Check if accessed directly (for testing)
if ($_SERVER["REQUEST_METHOD"] == "GET" && empty($_POST)) {
    // Show a message when accessed directly
    $direct_access = true;
} else {
    $direct_access = false;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $marital_status = $_POST['marital_status'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $religion = trim($_POST['religion'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Validation
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required";
    }

    if (empty($address)) {
        $errors[] = "Address is required";
    }

    if (empty($contact_number)) {
        $errors[] = "Contact number is required";
    } elseif (!is_numeric($contact_number)) {
        $errors[] = "Contact number must be numeric";
    }

    if (empty($occupation)) {
        $errors[] = "Occupation is required";
    }

    if (empty($marital_status)) {
        $errors[] = "Marital status is required";
    }

    if (empty($gender)) {
        $errors[] = "Gender is required";
    }

    if (empty($religion)) {
        $errors[] = "Religion is required";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $check_email_query = "SELECT id FROM users WHERE email = :email";
            $stmt = $pdo->prepare($check_email_query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already exists. Please use a different email.";
            }
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare SQL insert statement
            $insert_query = "INSERT INTO users (full_name, email, password, date_of_birth, address, contact_number, occupation, marital_status, gender, religion, bio, created_at) 
                           VALUES (:full_name, :email, :password, :date_of_birth, :address, :contact_number, :occupation, :marital_status, :gender, :religion, :bio, NOW())";
            
            $stmt = $pdo->prepare($insert_query);
            
            // Bind parameters
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':date_of_birth', $date_of_birth);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':occupation', $occupation);
            $stmt->bindParam(':marital_status', $marital_status);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':religion', $religion);
            $stmt->bindParam(':bio', $bio);
            
            // Execute the query
            if ($stmt->execute()) {
                // Set success message in session
                $_SESSION['registration_success'] = "Registration successful! Welcome to Bikkini Bottom News, " . htmlspecialchars($full_name) . "! Please login with your credentials.";
                
                // Redirect to login page
                header("Location: login.html");
                exit();
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
            
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status - Bikkini Bottom News</title>
    <link rel="icon" href="imgs/fish.svg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .fishz {
            border-radius: 50%;
            width: 30px;
            height: 30px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
        }

        .error ul {
            margin: 10px 0 0 20px;
        }

        .buttons {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .registration-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }

        .registration-info h3 {
            color: #1976d2;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.html" class="logo">
                <span><img src="imgs/fish.svg" class="fishz" alt=""></span> Bikkini Bottom News
            </a>
        </div>

        <?php if ($direct_access): ?>
            <div class="registration-info">
                <h3>🐠 Registration Processing Page</h3>
                <p>This page processes registration form submissions. Please use the registration form to create your account.</p>
                <p><strong>For testing:</strong> The database connection is working and the script is ready to process registrations!</p>
            </div>
            
            <div class="buttons">
                <a href="registration.html" class="btn btn-primary">Go to Registration Form</a>
                <a href="index.html" class="btn btn-secondary">Back to Home</a>
            </div>
            
        <?php elseif (!empty($errors)): ?>
            <div class="error">
                <h3>❌ Registration Failed</h3>
                <p>Please fix the following errors:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="buttons">
                <a href="registration.html" class="btn btn-primary">Try Again</a>
                <a href="index.html" class="btn btn-secondary">Back to Home</a>
            </div>
            
        <?php else: ?>
            <div class="registration-info">
                <h3>Registration Page</h3>
                <p>Please use the registration form to create your account.</p>
            </div>
            
            <div class="buttons">
                <a href="registration.html" class="btn btn-primary">Go to Registration</a>
                <a href="index.html" class="btn btn-secondary">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>