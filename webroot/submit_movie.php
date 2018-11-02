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
require "$root/mission6/database.php";

$state_login = false;

if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;
}

$pdo = connect();

try {
    if (isset($_FILES['movie']['error']) && is_int($_FILES['movie']['error'])) {
        switch ($_FILES['movie']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('ファイルが選択されていません', 400);
            case UPLOAD_ERR_INI_SIZE:
                throw new RuntimeException('ファイルサイズが大きすぎます', 400);
            default:
                throw new RuntimeException('その他のエラーが発生しました', 500);
        }

        // 画像・動画をバイナリデータにする
        $row_data = file_get_contents($_FILES['upfile']['tmp_name']);

        // DBに格納するファイルネーム設定
        // サーバ側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける

        $date = getdate();
        $fname = $_FILES['upfile']['tmp_name'] . $date['year'] . $date['mon'] . $date['mday'] . $date['hours'] . $date['minutes'] . $date['seconds'];
        $fname_hash = hash("sha256", $fname);

        $stmt = $pdo->prepare(
            'INSERT INTO movie(id, title, title_hash, row_data, user_id) VALUES (null, ?, ?, ?, ?)'
        );
        $params[] = $fname;
        $params[] = $fname_hash;
        $params[] = $row_data;
        $params[] = $login_user['id'];
        $stmt->execute($params);
    }
    header('Location:profile.php?id=' . $login_user['id']);
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
    <title>動画アップロード</title>
</head>
<body>
<?php if ($state_login !== false): ?>
    <form action="submit_movie.php" enctype="multipart/form-data" method="post">
        <input type="file" name="movie">
        <input type="submit" value="アップロード">
    </form>
<?php else: ?>
    <?php header('Location:login.php'); ?>
<?php endif; ?>
</body>
</html>
