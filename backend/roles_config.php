<?php
// Simple role configuration for SIGCA
// Add admin emails here - these users will have admin privileges
$admin_emails = [
    'admin@sigca.com', // Default admin email
    // Add more admin emails as needed, like:
    // 'supervisor@empresa.com',
    // 'admin2@sigca.com',
];

// Add gerente emails here - these users will have gerente privileges
$gerente_emails = [
    'gerente@sigca.com', // Example: manager email
    // Add more gerente emails as needed
];

// Add admin user IDs here - these user IDs will have admin privileges
$admin_user_ids = [
    1, // Default admin user ID (usually the first user)
    // Add more admin user IDs as needed, like:
    // 5, // Another admin user
    // 10, // Yet another admin
];

// Add gerente user IDs here - these user IDs will have gerente privileges
$gerente_user_ids = [
    2, // Example gerente user ID
    // Add more gerente user IDs as needed
];

function is_admin_by_email($email) {
    global $admin_emails;
    return in_array(strtolower($email), array_map('strtolower', $admin_emails));
}

function is_admin_by_id($user_id) {
    global $admin_user_ids;
    return in_array($user_id, $admin_user_ids);
}

function is_gerente_by_email($email) {
    global $gerente_emails;
    return in_array(strtolower($email), array_map('strtolower', $gerente_emails));
}

function is_gerente_by_id($user_id) {
    global $gerente_user_ids;
    return in_array($user_id, $gerente_user_ids);
}

function get_user_role($email, $user_id = null) {
    if (is_admin_by_email($email) || ($user_id && is_admin_by_id($user_id))) {
        return 'admin';
    }
    if (is_gerente_by_email($email) || ($user_id && is_gerente_by_id($user_id))) {
        return 'gerente';
    }
    return 'user';
}
?>