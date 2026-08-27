<?php
/**
 * User Logout
 * Buzzly - Share Your World
 */

require_once 'includes/functions.php';

// Destroy all sessions
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>