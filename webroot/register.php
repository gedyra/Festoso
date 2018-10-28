<?php
/**
 * Created by PhpStorm.
 * User: Naohiro
 * Date: 2018/10/13
 * Time: 16:59
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$err = array();
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $user_name = filter_input(INPUT_POST, 'user_name');
    $password = filter_input(INPUT_POST, 'password');
    $password_conf = filter_input(INPUT_POST, 'password_conf');
    $group_name = filter_input(INPUT_POST, 'group_name');

    if ($user_name === '') {
        $err['user_name'] = 'ユーザ名は入力必須です。';
    }
    if ($password === '') {
        $err['password'] = 'パスワードは入力必須です。';
    }
    if ($password !== $password_conf) {
        $err['password_conf'] = 'パスワードが一致しません。';
    }
    if ($password === '') {
        $err['group_name'] = '団体名は必須入力です。';
    }

    if (count($err) === 0) {
        $pdo = connect();
        $stmt = $pdo->prepare('INSERT INTO `User` (`id`, `user_name`, `password`, `group_name`) VALUES (null, ?, ?, ?)');

        $params = array();
        $params[] = $user_name;
        //$params[] = password_hash($password, PASSWORD_DEFAULT);
        //$params[] = crypt($password, '$2a'.)
        $params[] = $password;
        $params[] = $group_name;

        // IDに重複がなければ登録
        // 重複があればエラーを吐く
        try {
            $success = $stmt->execute($params);
        } catch (PDOException $e) {
            //echo $e->getMessage();
            $error_code = $stmt->errorCode();

            if ($error_code == 23000) {
                $err['duplicate'] = 'すでに使われているIDまたは合唱団の名前です';
            }
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
    <style type="text/css">
        .error {
            color: red;
        }
    </style>
</head>
<body>
<?php if (count($err) > 0) : ?>
    <?php foreach ($err as $e) : ?>
        <p class="error"><?php echo h($e); ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($success) && $success) : ?>
    <p>登録に成功しました。</p>
    <p><a href="login.php">こちらからログインしてください。</a></p>
<?php else: ?>
    <form action="" method="post">
        <p>
            <label for="user_name">ユーザID
                <input type="text" id="user_id" name="user_name">
            </label>
        </p>
        <p>
            <label for="group_name">団体名</label>
            <input type="text" id="group_name" name="group_name">
        </p>
        <p>
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
        </p>
        <p>
            <label for="password_conf">パスワード(確認)</label>
            <input type="password" id="password_conf" name="password_conf">
        </p>
        <p>
            <button type="submit">新規ユーザ登録</button>
        </p>
    </form>
<?php endif; ?>
</body>
</html>