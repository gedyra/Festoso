<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/23
 * Time: 14:11
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "../database.php";

$state_login = false;

if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;
}

$pdo = connect();

function upload_movie()
{
    $success = false;
    $error = array();
    if (isset($_FILES['movie']['error']) && is_int($_FILES['movie']['error'])) {
        switch ($_FILES['movie']['error']) {
            case UPLOAD_ERR_OK:
                $tmp_name = $_FILES['movie']['tmp_name'];
                $basename = basename($_FILES['movie']['name']);
                $name = mb_convert_encoding($basename, 'UTF-8', 'AUTO');
                $path = '../movies/' . hash('sha256', $name);
                move_uploaded_file($tmp_name, $path);

                return array('success' => true, 'name' => $name, 'path' => $path, 'error' => $error);
                break;
            case UPLOAD_ERR_NO_FILE:
                $err['nofile'] = 'ファイルが選択されていません';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $err['toobig'] = 'ファイルサイズが大きすぎます';
                break;
            default:
                $err['other'] = 'その他のエラーが発生しました';
                break;
        }
        return array('success' => false, 'errors' => $err);
    }
}

$login_user = array();
if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];

    if (isset($_FILES['movie'])) {

        $movie_info = upload_movie();

        if ($movie_info['success'] === false) {
            foreach ($movie_info['errors'] as $e) {
                echo $e . '<br>';
            }
        } else {

            $music_title = filter_input(INPUT_POST, 'music_title');
            $composer = filter_input(INPUT_POST, 'composer');
            // DBに格納するファイルネーム設定
            // サーバ側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける
            var_dump($music_title);

            $stmt = $pdo->prepare(
                'INSERT INTO Movie(id, title, music_title, composer, path, user_id)'
                . 'VALUES (null, :title, :music_title, :composer, :path, :user_id)'
            );
            $stmt->bindParam(':title', $movie_info['name']);
            $stmt->bindParam(':music_title', $music_title);
            $stmt->bindParam(':composer', $composer);
            $stmt->bindParam(':path', $movie_info['path']);
            $stmt->bindParam(':user_id', $login_user['id']);
            $stmt->execute();

            //header('Location:profile.php?id=' . $login_user['id'] . '&movie=true');
        }
    }
} else {
    header('Location:login.php');
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>動画アップロード</title>
</head>
<body>
<?php if ($state_login === true): ?>
    <!--    --><?php //foreach ($err as $e): ?>
    <!--        <p class="error">--><?php //echo h($e)?><!--</p>-->
    <!--    --><?php //endforeach; ?>
    <h2>動画アップロード</h2>
    <form action="submit_movie.php" enctype="multipart/form-data" method="post">
        <p>
            <label for="music_title">曲名</label>
            <input type="text" id="music_title" name="music_title">
        </p>
        <p>
            <label for="composer">作曲者名</label>
            <input type="text" id="composer" name="composer">
        </p>
        <p>
            <input type="file" name="movie" accept="video/*">
            <input type="submit" value="アップロード">
        </p>
    </form>
<?php else: ?>
    <?php header('Location:login.php'); ?>
<?php endif; ?>
</body>
</html>
