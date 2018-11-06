<?php
session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$login_user = $_SESSION['login_user'];

$pdo = connect();

// 最近追加された演奏会5件を表示
$stmt_concert = $pdo->prepare(
    'SELECT * FROM Concert ORDER BY Concert.id DESC LIMIT 5'
);
$stmt_concert->execute();
$result_concert = $stmt_concert->fetchAll();

// 最近追加された演奏会5件を表示
$stmt = $pdo->prepare('SELECT title,title_hash FROM movie ORDER BY id DESC LIMIT 5');
$stmt->execute();
$result_movie = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>トップページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css"/>
    <!--    <script src="main.js"></script>-->
</head>
<body>

<div id="header">
    ヘッダーです
</div>

<h1>Festoso</h1>

<?php if (isset($login_user)) : ?>
    ようこそ、<?php echo $login_user['id']; ?>さん<br>
    <a href="profile.php?id=<? echo $login_user['id']; ?>">マイページ</a> <a href="submit_concert.php">演奏会登録</a>
    <a href="logout.php" onclick="return confirm('ログアウトします。よろしいですか？')">ログアウト</a>
<?php else : ?>
    <a href="login.php">ログイン</a>
<?php endif; ?>

<h2>最近追加された演奏会</h2>
<p>
    <?php foreach ($result_concert as $row): ?>
        <?php echo $row['title']; ?> :
        <a href="concert_detail.php?id=<?php echo $row['id']; ?>">
            concert_detail.php?id=<?php echo $row['id']; ?>
        </a>
        <br>
    <?php endforeach; ?>
</p>

<h2>最近追加された動画</h2>
<p>
    <?php foreach ($result_movie as $row): ?>
        <?php echo $row['title']; ?> : <a href="import_media.php?target=<?php echo $row['title_hash']; ?>">閲覧</a>
        <br>
    <?php endforeach; ?>
</p>

</body>
</html>