<?php
require_once 'config.php';
setcookie('auth_token','', time()-3600, '/');
header("Location: $AUTH_URL/login.php");
exit;
