<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$state_login = false;

if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;
}

$target = $_GET;
$pdo = connect();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>プロフィールページ | FESTOSO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css"/>
    <script src="main.js"></script>
</head>
<body>
<h2>プロフィール</h2>
<h3>基本情報</h3>
<?php
$stmt = $pdo->prepare('SELECT * FROM User WHERE id=?');
$param[] = $target['id'];
$stmt->execute($param);
$results_user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

団体名: <?php echo $results_user['group_name']; ?>
<br>
拠点: <?php if ($results_user['base'] === ''): ?> まだ登録されていません <?php else: ?><?php echo $results_user['base']; ?><?php endif; ?>
<br>
SNS等リンク: <?php if ($results_user['homepage'] === ''): ?> まだ登録されていません <?php else: ?><?php echo $results_user['homepage']; ?><?php endif; ?>
<br>
<?php if ($state_login === true and $login_user['id'] === $target['id']) : ?>
    <a href="edit_profile.php">プロフィール編集</a>
<?php endif; ?>

<h3>演奏会一覧</h3>

<?php
// 演奏会の一覧を表示
$stmt = $pdo->prepare('SELECT * FROM Concert WHERE user_id=:user_id');
$stmt->bindParam(':user_id', $target['id']);
$stmt->execute();
$results_concert = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT title,title_hash  FROM movie WHERE user_id=:user_id');
$stmt->bindParam(':user_id', $target['id']);
$stmt->execute();
$results_movie = $stmt->fetchAll();
?>

<?php if (count($results_concert) > 0): ?>
    <p>
    <table border="1">
        <tr>
            <th>タイトル</th>
            <th>詳細ページ</th>
        </tr>
        <?php foreach ($results_concert as $row): ?>
            <tr>
                <td><?php echo $row['title']; ?> </td>
                <td>
                    <a href="concert_detail.php?id=<?php echo $row['concert_id']; ?>">concert_detail.php?id=<?php echo $row['concert_id']; ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </p>
<?php else: ?>
    演奏会はまだ登録されていません

<?php endif; ?>

<h3>投稿した動画</h3>
<?php if (count($results_movie) > 0): ?>
    <p>
    <table border="1">
        <tr>
            <th>タイトル</th>
            <th>閲覧リンク</th>
        </tr>
        <?php foreach ($results_movie as $row): ?>
            <tr>
                <td><?php echo $row['title']; ?> </td>
                <td>
                    <a href="import_media.php?target=<?php echo $row['title_hash']; ?>">閲覧</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </p>
<?php else: ?>
    動画はまだ登録されていません
<?php endif; ?>
</body>
</html>