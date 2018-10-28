<?php
/**
 * Created by IntelliJ IDEA.
 * User: Naohiro
 * Date: 2018/10/23
 * Time: 14:49
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/mission6/database.php";

if (isset($_GET["target"]) && $_GET["target"] !== "") {
    $target = $_GET["target"];
} else {
    header("Location: submit_movie.php");
}
$MIMETypes = array(
    'mp4' => 'video/mp4'
);

try {
    $pdo = connect();
    $sql = 'SELECT * FROM movie WHERE title_hash=?';
    $stmt = $pdo->prepare($sql);
    $param[] = $target;
    $stmt->execute($param);

    $raw = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: ' . $MIMETypes['mp4']);
    echo($raw["row_data"]);
} catch (PDOException $e) {
    exit($e->getMessage());
}