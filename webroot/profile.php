<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$state_login = false;

if (isset($_SESSION)) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;
    $pdo = connect();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>プロフィールページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css"/>
    <script src="main.js"></script>
</head>
<body>
<?php if ($state_login === true) : ?>
    <a href="edit_profile.php?id=<?php echo $login_user['id'] ?>">プロフィール編集</a>
<?php endif; ?>
<h2>プロフィール</h2>
<h3>基本情報</h3>
<?php
$stmt = $pdo->prepare('SELECT * FROM User WHERE id=?');
$param[] = $login_user['id'];
$stmt->execute($param);
$results_concert = $stmt->fetch(PDO::FETCH_ASSOC);
?>

団体名: <?php echo $results_concert["group_name"]; ?>
<h3>演奏会一覧</h3>

<?php
// 演奏会の一覧を表示
$stmt = $pdo->prepare('SELECT * FROM Concert WHERE user_id=:user_id');
$stmt->bindParam(':user_id', $login_user['id']);
$stmt->execute();
$results_concert = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT title,title_hash  FROM movie WHERE user_id=:user_id');
$stmt->bindParam(':user_id', $login_user['id']);
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
                <td><?php echo $row['concert_title']; ?> </td>
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
    演奏会はまだ登録されていません

<?php endif; ?>
</body>
</html>