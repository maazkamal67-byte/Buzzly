<?php
/**
 * Home Feed Page
 * Buzzly - Share Your World
 */

require_once 'includes/functions.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$posts = getUserFeed($user_id);
$suggestions = getSuggestedUsers($user_id);

require_once 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Main Feed Column -->
        <div class="col-lg-8">
            <div id="posts-container">
                <?php if (empty($posts)): ?>
                <div class="card text-center p-5">
                    <i class="bi bi-camera display-1 text-muted mb-3"></i>
                    <h3>No Posts Yet</h3>
                    <p class="text-muted">Start following people to see their posts here!</p>
                    <a href="upload.php" class="btn-gradient mx-auto" style="max-width: 200px;">Create Your First Post</a>
                </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="card post-card fade-in">
                        <!-- Post Header -->
                        <div class="card-header d-flex align-items-center">
                            <img src="uploads/<?php echo htmlspecialchars($post['profile_pic']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['username']); ?>" 
                                 class="rounded-circle me-3" width="45" height="45">
                            <div>
                                <h6 class="mb-0">
                                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($post['fullname'] ?: $post['username']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted"><?php echo timeAgo($post['created_at']); ?></small>
                            </div>
                        </div>
                        
                        <!-- Post Content -->
                        <div class="card-body">
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            
                            <?php if ($post['media_type'] == 'image' && $post['media_url']): ?>
                            <img src="<?php echo htmlspecialchars($post['media_url']); ?>" 
                                 class="post-image img-fluid rounded-3 mt-3" 
                                 alt="Post image">
                            <?php elseif ($post['media_type'] == 'video' && $post['media_url']): ?>
                            <video controls class="post-video img-fluid rounded-3 mt-3">
                                <source src="<?php echo htmlspecialchars($post['media_url']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Post Stats -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <i class="bi bi-heart-fill text-danger"></i>
                                    <span class="ms-1 like-count"><?php echo $post['like_count']; ?></span>
                                    <span class="ms-3"><?php echo $post['comment_count']; ?> comments</span>
                                </div>
                            </div>
                            
                            <!-- Post Actions -->
                            <div class="d-flex justify-content-between border-top pt-3">
                                <button class="like-btn <?php echo $post['user_liked'] ? 'active' : ''; ?>" 
                                        data-post-id="<?php echo $post['id']; ?>">
                                    <i class="bi <?php echo $post['user_liked'] ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                    <span class="ms-1">Like</span>
                                </button>
                                
                                <button class="btn btn-link text-dark text-decoration-none" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#comments-<?php echo $post['id']; ?>">
                                    <i class="bi bi-chat"></i> Comment
                                </button>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="collapse mt-3" id="comments-<?php echo $post['id']; ?>">
                                <div class="comment-section">
                                    <?php 
                                    $comments = getPostComments($post['id']);
                                    foreach ($comments as $comment): 
                                    ?>
                                    <div class="comment-item">
                                        <img src="uploads/<?php echo htmlspecialchars($comment['profile_pic']); ?>" 
                                             class="comment-avatar">
                                        <div class="comment-content">
                                            <div class="comment-author">
                                                <?php echo htmlspecialchars($comment['username']); ?>
                                            </div>
                                            <div class="comment-text">
                                                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                            </div>
                                            <div class="comment-time">
                                                <?php echo timeAgo($comment['created_at']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Add Comment Form -->
                                <form class="comment-form mt-3 d-flex" data-post-id="<?php echo $post['id']; ?>">
                                    <input type="text" class="form-control comment-input" 
                                           placeholder="Add a comment..." required>
                                    <button type="submit" class="btn btn-link text-primary ms-2">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="suggestions-card">
                <div class="d-flex align-items-center mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                         alt="<?php echo htmlspecialchars($user['username']); ?>" 
                         class="rounded-circle me-3" width="55" height="55">
                    <div>
                        <h6 class="mb-0"><?php echo htmlspecialchars($user['fullname'] ?: $user['username']); ?></h6>
                        <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3">Suggestions For You</h6>
                
                <?php foreach ($suggestions as $suggestion): ?>
                <div class="suggestion-item">
                    <img src="uploads/<?php echo htmlspecialchars($suggestion['profile_pic']); ?>" 
                         class="suggestion-avatar">
                    <div class="flex-grow-1">
                        <h6 class="mb-0"><?php echo htmlspecialchars($suggestion['fullname'] ?: $suggestion['username']); ?></h6>
                        <small class="text-muted">@<?php echo htmlspecialchars($suggestion['username']); ?></small>
                    </div>
                    <button class="follow-btn btn btn-sm btn-gradient" 
                            data-user-id="<?php echo $suggestion['id']; ?>">
                        Follow
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>