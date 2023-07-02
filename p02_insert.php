<?php require'p00_header.php';?>

<?php

//1. POSTデータ取得
$stay_nm=$_POST['stay_nm'];
$stay_url=$_POST['stay_url'];
$access=$_POST['access'];
$recommend_memo=$_POST['recommend_memo'];
$stay_memo=$_POST['stay_memo'];

//２.  DB接続（p00_header.phpに記載）

//3. 画像ファイルのアップロード処理
$upload_dir = 'upload/'; // 画像のアップロード先ディレクトリ

// アップロードされたファイルの情報を取得
$uploaded_file01 = $_FILES['image01']['tmp_name'];
$filename01 = $_FILES['image01']['name'];
$file_path01 = $upload_dir . $filename01;
$uploaded_file02 = $_FILES['image02']['tmp_name'];
$filename02 = $_FILES['image02']['name'];
$file_path02 = $upload_dir . $filename02;

// ファイルを指定のディレクトリに移動
if (move_uploaded_file($uploaded_file01, $file_path01)) {
  // 移動成功した場合の処理
  echo 'ファイルの保存が成功しました。';
} else {
  // 移動失敗した場合の処理
  echo 'ファイルの保存が失敗しました。';
}
if (move_uploaded_file($uploaded_file02, $file_path02)) {
  // 移動成功した場合の処理
  echo 'ファイルの保存が成功しました。';
} else {
  // 移動失敗した場合の処理
  echo 'ファイルの保存が失敗しました。';
}

//４．データ登録SQL作成

//(1) SQL文を用意
$stmt = $pdo->prepare("INSERT INTO gs_bm_table(id, stay_nm, stay_url, access, recommend_memo, stay_memo, image01, image02, date)
 VALUES (NULL, :stay_nm, :stay_url, :access, :recommend_memo, :stay_memo, :image01, :image02, sysdate())");

//(2) バインド変数を用意
// Integer 数値の場合 PDO::PARAM_INT
// String文字列の場合 PDO::PARAM_STR

$stmt->bindValue(':stay_nm', $stay_nm, PDO::PARAM_STR);
$stmt->bindValue(':stay_url', $stay_url, PDO::PARAM_STR);
$stmt->bindValue(':access', $access, PDO::PARAM_STR);
$stmt->bindValue(':recommend_memo', $recommend_memo, PDO::PARAM_STR);
$stmt->bindValue(':stay_memo', $stay_memo, PDO::PARAM_STR);
$stmt->bindValue(':image01', $file_path01, PDO::PARAM_STR);
$stmt->bindValue(':image02', $file_path02, PDO::PARAM_STR);

//５. 実行
$status = $stmt->execute();

//６．データ登録処理後
if($status === false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit('ErrorMessage:'.$error[2]);
}else{

//７．登録が成功した場合の処理、p01_index.phpへリダイレクト
//header('Location:p01_index.php');
echo '<a href="p03_select.php">データ一覧</a>';

}
?>

<?php require'p99_footer.php';?>
