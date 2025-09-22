<?php
session_start();
require_once '../src/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $response['message'] = "Email and password are required.";
        echo json_encode($response);
        exit();
    }
    
    $db = getDB();
    
    // Get user with role information
    $stmt = $db->prepare("
        SELECT u.UserID, u.FullName, u.Email, u.Password, u.RoleID, r.RoleName 
        FROM User u 
        JOIN Role r ON u.RoleID = r.RoleID 
        WHERE u.Email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || $password !== $user['Password']) {
        $response['message'] = "Invalid email or password.";
        echo json_encode($response);
        exit();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['UserID'];
    $_SESSION['user_name'] = $user['FullName'];
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_role'] = $user['RoleName'];
    $_SESSION['logged_in'] = true;
    
    $response['success'] = true;
    $response['message'] = "Login successful!";
    
    // Redirect based on role
    if ($user['RoleName'] === 'ADMIN') {
        $response['redirect'] = '../admin/admin.php';
    } else {
        $response['redirect'] = '../public/landing.php';
    }
    
} catch (Exception $e) {
    $response['message'] = "Login failed. Please try again.";
    error_log("Login error: " . $e->getMessage());
}

echo json_encode($response);