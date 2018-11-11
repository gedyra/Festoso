<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/11/02
 * Time: 12:35
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

// ログインユーザが演奏会主催者かどうかを判定
function isHost($login_user, $concert_id)
{

    if (!isset($login_user['id'])) {
        return false;
    }

    $pdo = connect();
    $sql = 'SELECT COUNT(*) as cnt FROM Concert WHERE Concert.id = :concert_id AND Concert.user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam('concert_id', $concert_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $login_user['id'], PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['cnt'] > 0) {
        return true;
    } else {
        return false;
    }
}

// 画像アップロードのための関数
function upload()
{
    $success = true;
    $error = array();
    switch ($_FILES['image']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
            $error[] = 'ファイルサイズが大きすぎます';
            $success = false;
            break;
        case UPLOAD_ERR_NO_FILE:
            $error[] = 'ファイルが選択されていません';
            $success = false;
            break;
        default:
            $error[] = 'その他のエラーが発生しました';
            $success = false;
            break;
    }

    $tmp_name = $_FILES['image']['tmp_name'];
    $bname = basename($_FILES['image']['name']);
    $name = mb_convert_encoding($bname, "UTF-8", "AUTO");
    $path = "../images/$name";
    move_uploaded_file($tmp_name, $path);

    return array('success' => $success, 'name' => $name, 'path' => $path, 'error' => $error);
}


$login_user = array();
if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    if (isset($_FILES['image'])) {
        $image_info = upload();

        // 画像のアップロードに成功したら、DBに登録
        if ($image_info['success'] === true) {
            $login_user = $_SESSION['login_user'];

            $pdo = connect();

            $sql = 'INSERT INTO Picture(id, title, path, user_id, concert_id)'
                . ' VALUES (null, :picture_title, :picture_path, :user_id, :concert_id)';
            $stmt = $pdo->prepare($sql);

            echo $image_info['name'];
            $stmt->bindParam(':picture_title', $image_info['name'], PDO::PARAM_STR);
            $stmt->bindParam(':picture_path', $image_info['path'], PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $login_user['id'], PDO::PARAM_INT);
            $stmt->bindParam(':concert_id', $_GET['id'], PDO::PARAM_INT);

            $stmt->execute();
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
    <title>画像アップロード</title>
    <style type="text/css">
        .error {
            color: red;
        }
    </style>
</head>


<body>
<? if (isset($_GET['concert_id']) and isHost($login_user, $_GET['concert_id'])): ?>
    <h1>画像アップロード</h1>
    <? if (isset($image_info)): ?>
        <? foreach ($image_info['error'] as $err): ?>
            <p class="error"><? echo h($err) ?></p>
        <? endforeach; ?>
    <? endif; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" id="image" name="image" accept="image/*">
        <button type="submit">アップロード</button>
    </form>
<? elseif (!isset($login_user['id'])): ?>
    <? header('Location:login.php'); ?>
<? else: ?>
    403 forbidden
<? endif; ?>
</body>
</html>
