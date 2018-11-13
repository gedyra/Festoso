<?php
session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

$login_user = $_SESSION['login_user'];

$pdo = connect();

// 最近追加された演奏会5件を表示
$stmt_concert = $pdo->prepare(
    'SELECT * FROM Concert INNER JOIN User ON Concert.user_id = User.id ORDER BY Concert.id DESC LIMIT 10'
);
$stmt_concert->execute();
$result_concert = $stmt_concert->fetchAll();

// 最近追加された演奏会5件を表示
$stmt = $pdo->prepare('SELECT * FROM Movie ORDER BY id DESC LIMIT 10');
$stmt->execute();
$result_movie = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>トップページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--    <script src="main.js"></script>-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">


</head>


<header>
    <!--    <div class="container">-->
    <!--        <div class="header-left">-->
    <!--            <form action="search.php" method="post">-->
    <!--                <label for="search">団体名・演奏会を検索</label>-->
    <!--                <input type="text" name="search" id="search">-->
    <!--                <button type="submit" name="action" value="searchBtn">検索</button>-->
    <!--            </form>-->
    <!--        </div>-->
    <!--        <div class="header-right">-->
    <!--            --><?php //if (!isset($login_user)): ?>
    <!--                <a class="register" href="register.php">新規登録</a>-->
    <!--                <a class="login" href="login.php">ログイン</a>-->
    <!--            --><?php //else: ?>
    <!--                <a class="login" href="logout.php"-->
    <!--                   onclick="return confirm('ログアウトします。よろしいですか？')">ログアウト</a>-->
    <!--                <a class="register" href="profile.php?id=--><? // echo $login_user['id'] ?><!--">マイページ</a>-->
    <!--            --><?php //endif; ?>
    <!--        </div>-->
    <!--    </div>-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Festoso</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07"
                    aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample07">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
                    </li>
                    <?php if (!isset($login_user)): ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="login.php">ログイン</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="register.php">新規登録</a>
                        </li>

                    <?php else: ?>

                    <? endif; ?>
                    <!--                    <li class="nav-item dropdown">-->
                    <!--                        <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown07" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>-->
                    <!--                        <div class="dropdown-menu" aria-labelledby="dropdown07">-->
                    <!--                            <a class="dropdown-item" href="#">Action</a>-->
                    <!--                            <a class="dropdown-item" href="#">Another action</a>-->
                    <!--                            <a class="dropdown-item" href="#">Something else here</a>-->
                    <!--                        </div>-->
                    <!--                    </li>-->
                </ul>
                <form class="form-inline my-2 my-md-0" method="post" action="search.php">
                    <input class="form-control" type="text" placeholder="演奏会・団体を検索" aria-label="Search">
                </form>
            </div>
        </div>
    </nav>
</header>
<body>
<main role="main">
    <div class="col-sm-8 mx-auto">
        <p>
            <?php if (isset($login_user)) : ?>
                ようこそ、<?php echo $login_user['id']; ?>さん<br>
            <?php endif; ?>
        </p>

        <h2>最近追加された演奏会</h2>
        <div class="container">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>開催日</th>
                    <th>タイトル</th>
                    <th>団体</th>
                </tr>

                </thead>
                <?php foreach ($result_concert as $row): ?>
                    <tr>
                        <td><?php echo $row['date'] ?></td>
                        <td>
                            <a href="concert_detail.php?id=<?php echo $row['id']; ?>">
                                <?php echo $row['title'] ?></a>
                        </td>
                        <td>
                            <a href="profile.php?id=<?php echo $row['user_id'] ?>"><? echo $row['group_name'] ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>最近追加された動画</h2>
        <div class="container">
            <table class="table table-responsive">
                <?php foreach ($result_movie as $row): ?>
                    <tr>
                        <td>
                            <?php echo $row['title']; ?>
                        </td>
                        <td>
                            <a href="import_media.php?target=<?php echo $row['path']; ?>">閲覧</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>
</html>