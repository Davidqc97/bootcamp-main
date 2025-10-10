<?php
require_once '/var/www/shared/auth.php';

// Redirigir al login
header('Location: http://localhost:8082/login.php');
exit;
?>