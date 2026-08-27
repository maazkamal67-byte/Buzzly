<?php
/**
 * Post Upload Page
 * Buzzly - Share Your World
 */

require_once 'includes/functions.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = sanitize($_POST['content']);
    
    if (empty($content)) {
        $error = 'Please write something for your post.';
    } else {
        $media_type = 'none';
        $media_url = null;
        
        // Handle file upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
            $uploaded_file = uploadFile($_FILES['media']);
            if ($uploaded_file) {
                $media_url = $uploaded_file;
                
                // Determine media type
                $file_type = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
                if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $media_type = 'image';
                } elseif (in_array($file_type, ['mp4', 'mov'])) {
                    $media_type = 'video';
                }
            } else {
                $error = 'Failed to upload file. Please check file size and type.';
            }
        }
        
        if (empty($error)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO posts (user_id, content, media_type, media_url) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $content, $media_type, $media_url);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Your post has been shared successfully!';
                $content = ''; // Clear form
            } else {
                $error = 'Failed to create post. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="auth-form">
                <div class="text-center mb-4">
                    <span class="brand-text">Share Your World</span>
                    <p class="text-muted mt-2">Create a new post and share with your followers</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="content" class="form-label">What's on your mind?</label>
                        <textarea class="form-control" id="content" name="content" rows="4" 
                                  placeholder="Write something..." required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="media" class="form-label">Add Photo or Video</label>
                        <div class="upload-area border rounded-3 p-4 text-center" 
                             style="border: 2px dashed #dbdbdb; cursor: pointer;"
                             onclick="document.getElementById('media').click();">
                            <i class="bi bi-cloud-upload display-4 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">Click to upload or drag and drop</p>
                            <small class="text-muted">JPG, PNG, GIF, MP4 (Max 50MB)</small>
                        </div>
                        <input type="file" class="d-none" id="media" name="media" accept="image/*,video/*">
                        <div class="mt-3" id="preview-container" style="display: none;">
                            <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded-3" style="max-height: 300px;">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-gradient w-100">
                        <i class="bi bi-cloud-upload me-2"></i>Share Post
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>