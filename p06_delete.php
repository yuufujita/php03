<?php require'p00_header.php';?>

<?php
//funcs.phpを読み込む
require_once('p99_funcs.php');

$id = $_GET['id'];

//３．データ登録SQL作成
$stmt = $pdo->prepare('DELETE FROM gs_bm_table WHERE id = :id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$status = $stmt->execute(); //実行

if ($status === false) {
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    header('Location: p03_select.php');
    exit();
}