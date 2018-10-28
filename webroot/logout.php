<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/27
 * Time: 0:04
 */

session_start();

if (isset($_SESSION)) {
    $_SESSION = array();

    if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }

    session_destroy();
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
ログアウトしました。
<a href="index.php">トップページはこちら</a>
</body>
</html>
