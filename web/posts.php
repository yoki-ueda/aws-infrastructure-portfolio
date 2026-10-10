<?php

// RDS MySQLへ接続し、postsテーブルのデータを取得・表示するPHPページ

$config = require '/var/www/config/db.php';

$db = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['database'],
    $config['port']
);

$result = $db->query("SELECT * FROM posts");
?>

<h1>Posts</h1>

<?php while ($row = $result->fetch_assoc()): ?>
    <h2><?= htmlspecialchars($row['title']) ?></h2>
    <p><?= htmlspecialchars($row['detail']) ?></p>
    <p><?= htmlspecialchars($row['image']) ?></p>
<?php endwhile; ?>
