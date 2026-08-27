<?php
/**
 * User Profile Page
 * Buzzly - Share Your World
 */

require_once 'includes/functions.php';
requireLogin();

$current_user_id = $_SESSION['user_id'];

// Get profile user ID
$profile_id = isset($_GET['id']) ? (int)$_GET['id'] : $current_user_id;
$user = getUserById($profile_id);

if (!$user) {
    header('Location: index.php');
    exit();
}

$posts = getUserPosts($profile_id);
$follower_count = getFollowerCount($profile_id);
$following_count = getFollowingCount($profile_id);
$is_following = $profile_id != $current_user_id ? isFollowing($current_user_id, $profile_id) : false;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = sanitize($_POST['fullname']);
    $bio = sanitize($_POST['bio']);
    
    // Handle profile picture upload
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $uploaded_file = uploadFile($_FILES['profile_pic']);
        if ($uploaded_file) {
            $profile_pic = basename($uploaded_file);
        }
    }
    
    $stmt = mysqli_prepare($conn, "UPDATE users SET fullname = ?, bio = ?, profile_pic = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $fullname, $bio, $profile_pic, $current_user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['fullname'] = $fullname;
        $_SESSION['profile_pic'] = $profile_pic;
        header("Location: profile.php?id=$profile_id");
        exit();
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                     alt="<?php echo htmlspecialchars($user['username']); ?>" 
                     class="profile-avatar">
            </div>
            
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="mb-0 me-3"><?php echo htmlspecialchars($user['username']); ?></h2>
                    
                    <?php if ($profile_id == $current_user_id): ?>
                        <button class="btn btn-outline-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </button>
                    <?php else: ?>
                        <button class="follow-btn btn <?php echo $is_following ? 'btn-outline-gradient' : 'btn-gradient'; ?> btn-sm" 
                                data-user-id="<?php echo $profile_id; ?>">
                            <?php echo $is_following ? 'Following' : 'Follow'; ?>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($posts); ?></div>
                        <div class="stat-label">posts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number follower-count"><?php echo $follower_count; ?></div>
                        <div class="stat-label">followers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $following_count; ?></div>
                        <div class="stat-label">following</div>
                    </div>
                </div>
                
                <?php if ($user['fullname']): ?>
                <div class="mt-3">
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h6>
                    <?php if ($user['bio']): ?>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Posts Grid -->
    <div class="row">
        <?php if (empty($posts)): ?>
        <div class="col-12">
            <div class="card text-center p-5">
                <i class="bi bi-camera display-1 text-muted mb-3"></i>
                <h3>No Posts Yet</h3>
                <?php if ($profile_id == $current_user_id): ?>
                    <p class="text-muted">Share your first moment with Buzzly!</p>
                    <a href="upload.php" class="btn-gradient mx-auto" style="max-width: 200px;">Upload a Post</a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
            <div class="col-md-4 mb-4">
                <div class="card post-card h-100">
                    <?php if ($post['media_type'] == 'image' && $post['media_url']): ?>
                    <img src="<?php echo htmlspecialchars($post['media_url']); ?>" 
                         class="card-img-top" style="height: 300px; object-fit: cover;">
                    <?php elseif ($post['media_type'] == 'video' && $post['media_url']): ?>
                    <video style="height: 300px; object-fit: cover;" class="card-img-top">
                        <source src="<?php echo htmlspecialchars($post['media_url']); ?>" type="video/mp4">
                    </video>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><?php echo timeAgo($post['created_at']); ?></small>
                            <div>
                                <span class="me-2"><i class="bi bi-heart-fill text-danger"></i> <?php echo $post['like_count']; ?></span>
                                <span><i class="bi bi-chat-fill"></i> <?php echo $post['comment_count']; ?></span>
                            </div>
                        </div>
                        
                        <?php if ($profile_id == $current_user_id): ?>
                        <div class="mt-3">
                            <a href="delete-post.php?id=<?php echo $post['id']; ?>" 
                               class="delete-post btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Profile Modal -->
<?php if ($profile_id == $current_user_id): ?>
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                             id="profile-preview" class="rounded-circle mb-3" width="120" height="120" 
                             style="object-fit: cover;">
                        <div>
                            <label for="profile_pic" class="btn btn-outline-gradient btn-sm">
                                <i class="bi bi-camera"></i> Change Photo
                            </label>
                            <input type="file" class="d-none" id="profile_pic" name="profile_pic" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" 
                               value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" 
                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_profile" class="btn-gradient">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>