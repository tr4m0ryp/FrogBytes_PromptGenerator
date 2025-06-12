<?php
// Logout handler
// logout.php

require_once __DIR__ . '/includes/User.php';

$user = new User();
$user->logout();

// Redirect to login page
header('Location: login.php');
exit;
?>
