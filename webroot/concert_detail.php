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

// 編集権限があるかどうかを判定
function isHost($state_login, $login_user, $concert_info)
{
    if (!isset($login_user['id'])) {
        return false;
    }

    if ($state_login === true and $login_user['id'] === $concert_info['user_id']) {
        return true;
    } else {
        return false;
    }
}

// DBから演奏会の画像を取得する関数
function fetch_images(pdo $pdo, $concert_info)
{
    $sql = 'SELECT * FROM Picture WHERE concert_id=:concert_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':concert_id', $concert_info['id'], PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}


// ログインしているかどうかを判定
$state_login = false;
$login_user = array();
if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
    $state_login = true;
}

// GETメソッドのクエリから、表示したい演奏会のIDを取得
if (isset($_GET['id'])) {
    $object_concert_id = h($_GET['id']);

    $pdo = connect();

    // 指定されたidの演奏会が存在するかどうかを調べる
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS num_of_concert FROM Concert WHERE Concert.id=:id'
    );
    $stmt->bindParam(':id', $object_concert_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 演奏会が存在した場合
    if ($result['num_of_concert'] > 0) {
        // 指定された演奏会idの演奏会情報情報を取得
        $stmt = $pdo->prepare(
            'SELECT * FROM Concert WHERE Concert.id=?'
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

        $images = fetch_images($pdo, $concert_info);
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
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <title>演奏会詳細</title>

    <style type="text/css">
        img {
            width: 320px;
            height: auto;
        }
    </style>
</head>
<header>
    <div class="container">
        <div class="header-left">
            <form action="search.php" method="post">
                <label for="search">団体名・演奏会を検索</label>
                <input type="text" name="search" id="search">
                <button type="submit" name="action" value="searchBtn">検索</button>
            </form>
        </div>
        <div class="header-right">
            <?php if (!isset($login_user)): ?>
                <a class="register" href="register.php">新規登録</a>
                <a class="login" href="login.php">ログイン</a>
            <?php else: ?>
                <a class="login" href="logout.php"
                   onclick="return confirm('ログアウトします。よろしいですか？')">ログアウト</a>
                <a class="register" href="profile.php?id=<? echo $login_user['id'] ?>">マイページ</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<body>
<?php if ($result["num_of_concert"] > 0): ?>
    <h2>演奏会詳細</h2>

    <? foreach ($images as $image): ?>
        <a href="<? echo $image['path'] ?>">
            <img src="<? echo $image['path'] ?>" alt="<? echo $image['title'] ?>">
        </a>
    <? endforeach; ?>

    <? if (isHost($state_login, $login_user, $concert_info)): ?>
        <a href="submit_image.php?concert_id=<? echo $concert_info['id'] ?>">画像追加</a>
    <? endif; ?>

    <table>
        <tr>
            <td>演奏会名</td>
            <td><?php echo $concert_info['title'] ?></td>
        </tr>
        <tr>
            <td>主催者</td>
            <td>
                <a href="profile.php?id=<? echo $concert_info['user_id'] ?>">
                    <? echo $group_info['group_name'] ?>
                </a>
            </td>
        </tr>
        <tr>
            <td>日時</td>
            <td><?php echo $concert_info['date'] ?></td>
        </tr>
        <tr>
            <td>場所</td>
            <td><?php echo $concert_info['place'] ?></td>
        </tr>
        <tr>
            <td>プログラム</td>
            <td><?php echo $concert_info['program'] ?></td>
        </tr>
        <tr>
            <td>自由記述欄</td>
            <td><?php echo $concert_info['freetext'] ?></td>
        </tr>
    </table>

    <?php if (isset($login_user)) : ?>
        <?php if (isHost($state_login, $login_user, $concert_info)): ?>
            <a href="edit_concert.php?id=<? echo $object_concert_id ?>">編集</a>
        <? endif; ?>
    <?php endif; ?>

<?php else: ?>
    指定された演奏会は存在しません。<br>
    <a href="index.php">トップページに戻る</a>
<?php endif; ?>
</body>
</html>
