<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/11/07
 * Time: 13:28
 */

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

if (isset($_SESSION['login_user'])) {
    $login_user = $_SESSION['login_user'];
}

$pdo = connect();

$action = h($_POST['action']);
$search_query = '';

if ($action === 'searchBtn') {
    $search_query = h($_POST['search']);
    //$search_query = ;

    //$select = 'SELECT * FROM :table';

    $search_query = '%' . $search_query . '%';

    // 演奏会検索
    $stmt = $pdo->prepare('SELECT * FROM Concert WHERE title LIKE :query');
    $stmt->bindParam(':query', $search_query, PDO::PARAM_STR);
    $exists_concert = $stmt->execute();

    $results_concert = $stmt->fetchAll();

    $_stmt = $pdo->prepare('SELECT * FROM User WHERE group_name LIKE :query');
    $_stmt->bindParam(':query', $search_query, PDO::PARAM_STR);
    $exists_user = $_stmt->execute();

    $results_user = $_stmt->fetchAll();

    var_dump($exists_user);
    var_dump($results_user);
}

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>検索結果</title>
</head>
<body>

<h3>演奏会の検索結果</h3>
<? if ($exists_concert !== false): ?>
    <table>
        <tr>
            <th>日付</th>
            <th>タイトル</th>
        </tr>
        <? foreach ($results_concert as $concert): ?>
            <tr>
                <td><?php echo $concert['date'] ?></td>
                <td><a href="concert_detail.php?id=<? echo $concert['id'] ?>"><?php echo $concert['title'] ?></a></td>
            </tr>
        <? endforeach; ?>
    </table>
<? else: ?>
    見つかりませんでした
<? endif; ?>

<h3>団体の検索結果</h3>
<? if ($exists_user !== false): ?>
    <table>
        <tr>
            <th>拠点</th>
            <th>団体名</th>
        </tr>
        <? foreach ($results_user as $user): ?>
            <tr>
                <td><?php echo $user['base'] ?></td>
                <td><a href="profile.php?id=<? echo $user['id'] ?>"><?php echo $user['group_name'] ?></a></td>
            </tr>
        <? endforeach; ?>
    </table>
<? else: ?>
    見つかりませんでした
<? endif; ?>

</body>
</html>