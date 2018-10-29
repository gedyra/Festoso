<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/29
 * Time: 18:37
 */

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
    $stmt = $pdo->prepare('SELECT FROM User WHERE id=?');
    $param[] = $login_user['id'];
    $stmt->execute($param);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    $group_name_origin = $results['group_name'];
    $base_origin = $results['base_origin'];
    $homepage_origin = $results['homepage'];
}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $group_name = filter_input(INPUT_POST, 'user_name');
    $base = filter_input(INPUT_POST, "base");
    $homepage = filter_input(INPUT_POST, 'homepage');

    $flags = array(
        $group_name => false,
        $base => false,
        $homepage => false
    );


}


?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Festoso</title>
</head>
<body>

<?php if ($state_login === false): ?>
    リダイレクトします
    <?php header('Location:login.php'); ?>

<?php else: ?>
    <form action="" method="post">
        <p>
            <label for="group_name">団体名</label>
            <input type="text" id="group_name" name="group_name">
        </p>
        <p>
            <label for="base">団体拠点</label>
            <input type="text" id="base" name="base">
        </p>
        <p>
            <label for="homepage">ホームページ</label>
            <input type="text" id="homepage" name="homepage">
        </p>

    </form>

<?php endif; ?>

</body>
</html>
