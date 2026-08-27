<?php
/**
 * Delete Post
 * Buzzly - Share Your World
 */

require_once 'includes/functions.php';
requireLogin();

if (isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Verify post belongs to user
    $stmt = mysqli_prepare($conn, "SELECT id FROM posts WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Delete post (comments and likes will be deleted automatically due to CASCADE)
        $stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);
    }
}

// Redirect back
header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'profile.php');
exit();
?>