<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'data' => null];

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $response['success'] = true;
    $response['data'] = [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

echo json_encode($response);