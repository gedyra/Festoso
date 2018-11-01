<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/18
 * Time: 18:51
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$login_user = $_SESSION['login_user'];

// GETメソッドのクエリから、表示したい演奏会のIDを取得
if (isset($_GET['id'])) {
    $object_concert_id = h($_GET['id']);

    $pdo = connect();

    // 指定されたidの演奏会が存在するかどうかを調べる
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS num_of_concert FROM Concert WHERE concert_id=:id'
    );
    $stmt->bindParam(':id', $object_concert_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 演奏会が存在した場合
    if ($result['num_of_concert'] > 0) {
        // 指定された演奏会idの演奏会情報情報を取得
        $stmt = $pdo->prepare(
            'SELECT * FROM Concert WHERE concert_id=?'
        );
        $params[] = $object_concert_id;
        $stmt->execute($params);

        $concert_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // 演奏会を主催するユーザの団体名を取得
        $stmt = $pdo->prepare(
            'SELECT group_name FROM User WHERE id=?'
        );
        $user_id[] = $concert_info['user_id'];
        $stmt->execute($user_id);

        $group_info = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>演奏会詳細</title>
</head>
<body>
<?php if ($result["num_of_concert"] > 0): ?>
    <h2>演奏会詳細</h2>
    演奏会名 <?php echo $concert_info['title'] ?> <br>
    主催者 <?php echo $group_info['group_name'] ?> <br>
    日時 <?php echo $concert_info['date'] ?> <br>
    場所 <?php echo $concert_info['place'] ?> <br>

    <!--    ログインしているユーザが演奏会主催者ならば、編集リンクを表示-->
    <?php if ($login_user['id'] === $concert_info['user_id']): ?>
        <a href="edit_concert.php?id=<? echo $object_concert_id ?>">編集</a>
    <? endif; ?>
<?php else: ?>
    指定された演奏会は存在しません。<br>
    <a href="index.php">トップページに戻る</a>
<?php endif; ?>

</body>
</html>
