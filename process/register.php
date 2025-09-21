<?php
session_start();
require_once '../src/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/createaccount.html');
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['emailAddress'] ?? '');
    $mobile = trim($_POST['mobileNumber'] ?? '');
    $address = trim($_POST['deliveryAddress'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($fullName) || strlen($fullName) < 2) {
        $errors[] = "Full name is required and must be at least 2 characters.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    
    if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors[] = "Valid 10-digit mobile number is required.";
    }
    
    if (empty($address) || strlen($address) < 10) {
        $errors[] = "Delivery address is required and must be at least 10 characters.";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(' ', $errors);
        echo json_encode($response);
        exit();
    }
    
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT UserID FROM User WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $response['message'] = "Email address is already registered.";
        echo json_encode($response);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Get default customer role ID
    $stmt = $db->prepare("SELECT RoleID FROM Role WHERE RoleName = 'CUSTOMER'");
    $stmt->execute();
    $role = $stmt->fetch();
    $roleId = $role ? $role['RoleID'] : 2; // Default to 2 if not found
    
    // Insert user
    $stmt = $db->prepare("
        INSERT INTO User (FullName, Email, Mobile, Password, RoleID) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$fullName, $email, $mobile, $hashedPassword, $roleId]);
    
    $userId = $db->lastInsertId();
    
    // For now, we'll use a default city. In a real app, you'd have city selection
    // Let's use Colombo (CityID = 1) as default
    $stmt = $db->prepare("
        INSERT INTO UserAddress (UserID, AddressLine, CityID) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $address, 1]);
    
    // Create cart for the user
    $stmt = $db->prepare("INSERT INTO Cart (UserID) VALUES (?)");
    $stmt->execute([$userId]);
    
    $response['success'] = true;
    $response['message'] = "Registration successful! You can now log in.";
    
} catch (Exception $e) {
    $response['message'] = "Registration failed. Please try again.";
    error_log("Registration error: " . $e->getMessage());
}

echo json_encode($response);
?>