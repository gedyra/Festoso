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
    $stmt = $pdo->prepare('SELECT * FROM User WHERE id=?');
    $param[] = $login_user['id'];
    $stmt->execute($param);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    $group_name_origin = $results['group_name'];
    $base_origin = $results['base'];
    $homepage_origin = $results['homepage'];
}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $group_name = filter_input(INPUT_POST, 'group_name');
    $base = filter_input(INPUT_POST, "base");
    $homepage = filter_input(INPUT_POST, 'homepage');

    $sql = 'UPDATE User SET group_name=?, base=?, homepage=? WHERE User.id=?';
    $stmt = $pdo->prepare($sql);

    $params[] = $group_name;
    $params[] = $base;
    $params[] = $homepage;
    $params[] = $login_user['id'];

    $stmt->execute($params);

    header('Location:profile.php?id=' . $login_user['id']);
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
            <label for="group_name">団体名</label><br>
            <input type="text" id="group_name" name="group_name" value="<?php echo $group_name_origin ?>">
        </p>
        <p>
            <label for="base">団体拠点</label><br>
            <input type="text" id="base" name="base" value="<?php echo $base_origin ?>">
        </p>
        <p>
            <label for="homepage">SNS等アドレス</label><br>
            <input type="text" id="homepage" name="homepage" value="<?php echo $homepage_origin ?>">
        </p>
        <input type="submit" value="登録">

    </form>

<?php endif; ?>

</body>
</html>
