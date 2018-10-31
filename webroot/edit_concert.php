<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/31
 * Time: 13:22
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$state_login = false;

$target = $_GET;

if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;

    $pdo = connect();
    $stmt = $pdo->prepare('SELECT * FROM Concert WHERE concert_id=?');
    $param[] = $target['id'];
    $stmt->execute($param);
    $element_original = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $title = filter_input(INPUT_POST, 'title');
    $date = filter_input(INPUT_POST, 'date');
    $place = filter_input(INPUT_POST, 'place');

    $sql = 'UPDATE Concert SET title=:title, date=:date, place=:place WHERE concert_id=:id';
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':place', $place);
    $stmt->bindParam(':id', $target['id']);

    $stmt->execute();

    header('Location:concert_detail.php?id=' . $target['id']);
}


?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>演奏会情報編集 | Festoso</title>
</head>
<body>
<h1>
    演奏会情報編集
</h1>

<?php if ($state_login === false): ?>
    <?php header('Location:login.php'); ?>
<?php else: ?>
    <form action="" method="post">
        <p>
            <label for="title">演奏会名</label><br>
            <input type="text" id="title" name="title" value="<?php echo $element_original['title'] ?>">
        </p>
        <p>
            <label for="date">日時</label><br>
            <input type="date" id="date" name="date" value="<?php echo $element_original['date'] ?>">
        </p>
        <p>
            <label for="place">場所</label><br>
            <input type="text" id="place" name="place" value="<?php echo $element_original['place'] ?>">
        </p>
        <input type="submit" value="登録">

    </form>
<?php endif; ?>

</body>
</html>
