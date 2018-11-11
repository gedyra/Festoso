<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$err = array();
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $user_name = filter_input(INPUT_POST, 'user_name');
    $password = filter_input(INPUT_POST, 'password');

    if (empty($user_name)) {
        $err['user_name'] = 'ユーザ名は入力必須です';
    }
    if ($password === '' or empty($user_name)) {
        $err['password'] = 'パスワードは入力必須です';
    }

    if (count($err) === 0) {
        $pdo = connect();

        $stmt = $pdo->prepare('SELECT * FROM User WHERE user_name = ?');
        $params[] = $user_name;

        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {

            // ハッシュの準備
            $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
            $salt = base64_encode($salt);
            $salt = str_replace('+', '.', $salt);

            // 入力されたパスワードをハッシュ化
            $password_hash = crypt($password, '$2y$10$' . $salt . '$');

            // DBから取得したパスワードのハッシュ値と、
            $password_true = $row['password'];
            if ($password_true === $password_hash) {
                session_regenerate_id(true);
                $_SESSION['login_user'] = $row;
                $login_user = $_SESSION['login_user'];
                header("Location:profile.php?id=" . $login_user['id']);
                return;
            }
        }
        $err['login'] = 'ログインに失敗しました';
    }
}
?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <!--    <meta charset="utf-8" />-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ログイン</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="main.css"/>
        <script src="main.js"></script>
    </head>
    <body>
    <h2>ログイン</h2>
    <form action="" method="post">
        <?php if (isset($err['login'])) : ?>
            <p class="error"><?php echo h($err['login']); ?></p>
        <?php endif; ?>
        <p>
            <label for="user_name">ユーザ名</label>
            <input type="text" name="user_name" id="user_name">
            <?php if (isset($err['user_name'])) : ?>
        <p class="error"><?php echo h($err['user_name']); ?></p>
    <?php endif; ?>
        </p>
        <p>
            <label for="">パスワード</label>
            <input type="password" name="password" id="password">
            <?php if (isset($err['password'])) : ?>
        <p class="error"><?php echo h($err['password']); ?></p>
    <?php endif; ?>
        </p>
        <p>
            <button type="submit" name="login">ログイン</button>
            <br>
        </p>
    </form>
    <a href="register.php">新規登録はこちら</a>
    </body>
    </html>

<?php

?>