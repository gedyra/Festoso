<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/11/04
 * Time: 23:07
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$pdo = connect();

$sql = 'SELECT * FROM Picture WHERE concert_id=:concert_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':concert_id', 1);
$stmt->execute();

$images = $stmt->fetchAll();

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>画像表示</title>

    <style type="text/css">
        img {
            width: 320px;
            height auto;
        }
    </style>
</head>
<body>
<?php foreach ($images as $image): ?>
    <a href="<? echo $image['path']; ?>"><img src="<? echo $image['path']; ?>" alt="演奏会関連画像"></a>
<?php endforeach; ?>
</body>
</html>
