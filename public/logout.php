<?php
require_once __DIR__ . '/../lib/auth.php';
auth_logout();
header('Location: ' . APP_BASE_URL . 'public/login.php');
exit;
?>