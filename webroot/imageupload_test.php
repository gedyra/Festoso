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


function upload()
{
//    switch ($_FILES['image']['error']) {
//        case UPLOAD_ERR_OK:
//            break;
//        case UPLOAD_ERR_NO_FILE:
//            throw new RuntimeException('ファイルが選択されていません', 400);
//        case UPLOAD_ERR_INI_SIZE:
//            throw new RuntimeException('ファイルサイズが大きすぎます', 400);
//        default:
//            throw new RuntimeException('その他のエラーが発生しました', 500);
//    }

    global $root;
    $tmp_name = $_FILES['image']['tmp_name'];
    $bname = basename($_FILES['image']['name']);
    $name = mb_convert_encoding($bname, "UTF-8", "AUTO");
    $path = "../images/$name";
    move_uploaded_file($tmp_name, $path);

    return array('name' => $name, 'path' => $path);
}

if (isset($_SESSION['login_user'])) {
    if (isset($_FILES['image'])) {
        $image_info = upload();

        $login_user = $_SESSION['login_user'];

        $pdo = connect();

        $sql = 'INSERT INTO Picture(id, title, path, user_id, concert_id)'
            . ' VALUES (null, :picture_title, :picture_path, :user_id, :concert_id)';
        $stmt = $pdo->prepare($sql);

        echo $image_info['name'];
        $stmt->bindParam(':picture_title', $image_info['name'], PDO::PARAM_STR);
        $stmt->bindParam(':picture_path', $image_info['path'], PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $login_user['id'], PDO::PARAM_INT);
        $stmt->bindValue(':concert_id', 1, PDO::PARAM_INT);

        $stmt->execute();

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
    <title>画像アップロードテスト</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" id="image" name="image" accept="image/*">
    <button type="submit">アップロード</button>
</form>
</body>
</html>
