<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/23
 * Time: 14:49
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$target = filter_input(INPUT_GET, 'target');
if ($target === "") {
    header('HTTP1.0/404 404 Not Found');
}

$MIMETypes = array(
    'mp4' => 'video/mp4'
);

try {
    $pdo = connect();
    $sql = 'SELECT * FROM Movie INNER JOIN User ON Movie.user_id = User.id WHERE path=?';
    $stmt = $pdo->prepare($sql);
    $param[] = $target;
    $stmt->execute($param);

    $raw = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>動画</title>
</head>
<body>

<h3><? echo $raw['music_title'] ?></h3>
<video src="<? echo $target ?>" type="video/mp4" controls height="240" width="320"></video>

<table>
    <tr>
        <td>作曲者情報</td>
        <td>
            <? if ($raw['composer'] !== '') {
                echo $raw['composer'];
            } else {
                echo 'まだ登録されていません';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>演奏者情報</td>
        <td>
            <a href="profile.php?id=<? echo $raw['user_id'] ?>">
                <? echo $raw['group_name'] ?>
            </a>
        </td>
    </tr>
</table>
</body>
</html>
