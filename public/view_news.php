<?php
require_once __DIR__ . '/../includes/auth.php';
checkStudent();

require_once __DIR__ . '/../classes/News.php';
$news = new News();
$allNews = $news->getAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>News &amp; Announcements</h2>
        <p>Stay updated with the latest university announcements.</p>
    </div>
    <a href="student_dashboard.php" class="btn btn-secondary">&#8592; Back to Dashboard</a>
</div>

<div class="news-list">
    <?php if (empty($allNews)): ?>
        <div class="glass-panel" style="padding:2rem; text-align:center;">
            <p class="empty-msg">No announcements available yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($allNews as $n): ?>
        <div class="news-card glass-panel">
            <div class="news-meta">
                <span class="news-date"><?php echo date('d M Y', strtotime($n['published_at'])); ?></span>
                <span class="news-author">by <?php echo htmlspecialchars($n['author_name']); ?></span>
            </div>
            <h3><?php echo htmlspecialchars($n['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($n['content'])); ?></p>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>