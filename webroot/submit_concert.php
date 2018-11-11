<?php
/**
 * Created by PhpStorm.
 * User: Naohiro
 * Date: 2018/10/16
 * Time: 16:07
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$login_user = $_SESSION['login_user'];

function upload()
{
    global $root;
    $tmp_name = $_FILES['image']['tmp_name'];
    $name = basename($_FILES['image']['name']);
    $name = mb_convert_encoding($name, "SJIS", "AUTO");
    move_uploaded_file($tmp_name,
        "$root/mission6/images/$name");
}

date_default_timezone_set('Asia/Tokyo');
$today = date("Y-m-d");

$err = array();
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $concert_title = filter_input(INPUT_POST, 'concert_title');
    $concert_cast = filter_input(INPUT_POST, 'concert_cast');
    $concert_date = filter_input(INPUT_POST, 'concert_date');
    $concert_place = filter_input(INPUT_POST, 'concert_place');

    if ($concert_title === '') {
        $err['concert_title'] = '演奏会タイトルは入力必須です。';
    }

    if (count($err) === 0) {
        $pdo = connect();
        $stmt = $pdo->prepare('INSERT INTO `Concert` (`id`, `user_id`, `title`, `cast`, `place`, `date`)'
            . ' VALUES (null, ?, ?, ?, ?, ?)');

        $params = array();
        $params[] = $login_user['id'];
        $params[] = $concert_title;

        $params[] = $concert_cast;
        $params[] = $concert_place;
        $params[] = $concert_date;

        $success = $stmt->execute($params);

        $concert_id = $pdo->lastInsertId('concert_id');


        $stmt->bindParam(':user_id', $login_user['id']);
        $stmt->bindParam(':concert_id', $concert_id);
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>演奏会登録</title>
</head>
<body>
<h2>演奏会登録</h2>
<?php if (isset($success) && $success) : ?>
    <?php header('Location:concert_detail.php?id=' . $concert_id); ?>
<?php else: ?>
    <form action="" method="post" enctype="multipart/form-data">
        <p>
            <label for="title">演奏会のタイトル</label>
            <input type="text" id="title" name="concert_title">
        </p>
        <p>
            <label for="cast">出演者</label>
            <input type="text" id="cast" name="concert_cast">
        </p>
        <p>
            <label for="date">日付</label>
            <input type="date" id="date" name="concert_date" value="<?php echo $today; ?>">
        </p>
        <p>
            <label for="place">場所</label>
            <input type="text" id="place" name="concert_place">
        </p>
        <p>
            <label for="poster">画像</label><br>
            <input type="file" id="poster" name="image" accept="image/*">
        </p>
        <p>
            <button type="submit">新規登録</button>
        </p>
    </form>

<?php endif; ?>
</body>
</html>
