<?php require'p00_header.php';?>

<?php
//funcs.phpを読み込む
require_once('p99_funcs.php');

//1.  DB接続（p00_header.phpに記載）

//２．データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM gs_bm_table");
$status = $stmt->execute();

//３．データ表示
$view="";
if ($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("ErrorQuery:".$error[2]);
}else{
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
  while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
    //geocording.jp APIから緯度・経度を取得する
    $url = "https://www.geocoding.jp/api/?q=" . h($result['stay_nm']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $xml = simplexml_load_string($res);
    $lat = $xml->coordinate->lat;
    $lon = $xml->coordinate->lng;

    $view .= '<div id ="post-data">'; //.=にすることで上書きでなく追加される
    $view .= '一押し：<br>';
    $view .= '<img src="' . h($result['image01']) . '" alt="一押し写真"><br>';
    $view .= '宿泊先：<br>';
    $view .= '<img src="' . h($result['image02']) . '" alt="宿泊先写真"><br>';
    $view .= '<table>';
    $view .= '<tr>';
    $view .= '<td class="left">'.'更新日付'.'</td>';
    $view .= '<td>'.h($result['date']).'</td>';
    $view .= '</tr>';
    $view .= '<tr>';
    $view .= '<td class="left">'.'宿泊先'.'</td>';
    $view .= '<td>'.h($result['stay_nm']).'</td>';
    $view .= '</tr>';
    $view .= '<tr>';
    $view .= '<td class="left">'.'宿泊先URL'.'</td>';
    $view .= '<td>'.'<a href="'.h($result['stay_url']).'">'.h($result['stay_url']).'</a></td>';
    $view .= '</tr>';
    $view .= '<tr>';
    $view .= '<td class="left">'.'宿泊先への公共交通機関'.'</td>';
    $view .= '<td>'.h($result['access']).'</td>'; 
    $view .= '</tr>';
    $view .= '<tr>';
    $view .= '<td class="left">'.'一押しメモ'.'</td>';
    $view .= '<td>'.h($result['recommend_memo']).'</td>';
    $view .= '</tr>'; 
    $view .= '<tr>';
    $view .= '<td class="left">'.'宿泊先メモ'.'</td>';
    $view .= '<td>'.h($result['stay_memo']).'</td>';
    $view .= '</tr>'; 
    $view .= '</table>';
    $view .= '<p class="lat-lon">'.'緯度:' . $lat . '／経度:' . $lon . '</p>';
    $view .= '<button id="search" type=”button” onclick="clickAlert()">'.'検索'.'</button>';
    $view .= '<button>'.'<a href="p04_detail.php?id=' . h($result['id']).'">'.'更新'.'</a>'.'</button>';
    $view .= '<button>'.'<a href="p06_delete.php?id=' . h($result['id']).'">'.'削除'.'</a>'.'</button>';
    $view .= '</div>';
  }
}
?>

<script
src="https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key="
async
defer
></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<script>
function GetMap() {
  const map = new Microsoft.Maps.Map("#myMap", {
    center: new Microsoft.Maps.Location(35.669099, 139.703436),
    mapTypeId: Microsoft.Maps.MapTypeId.load,
    zoom: 8,
  });

// 現在位置を取得
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    function (position) {
      const currentPosition = new Microsoft.Maps.Location(
        position.coords.latitude,
        position.coords.longitude
        );

// 左側_Load the directions module.
Microsoft.Maps.loadModule(
  "Microsoft.Maps.Directions",
  function () {
    // Create an instance of the directions manager.
    const directionsManager =
    new Microsoft.Maps.Directions.DirectionsManager(map);

    // 出発地を現在位置に設定
    const startWaypoint =
    new Microsoft.Maps.Directions.Waypoint({
      location: currentPosition,
    });
    directionsManager.addWaypoint(startWaypoint, 0);
    
    let lat = <?= $lat ?> // 最終レコードの $lat を参照していることはわかった
    let lon = <?= $lon ?> // 最終レコードの $lon を参照していることはわかった
    console.log(lat,'初期表示内容でDBの最終レコードlat、phpファイルの115行目');
    console.log(lon,'初期表示内容でDBの最終レコードlon、phpファイルの116行目');
    
    const endWaypoint = 
    new Microsoft.Maps.Directions.Waypoint({
      location: new Microsoft.Maps.Location(lat, lon) });
    directionsManager.addWaypoint(endWaypoint, 1);
    directionsManager.setRenderOptions({ itineraryContainer: "#directionsItinerary" });
    directionsManager.showInputPanel("directionsPanel")
  })

// マップを現在位置にセンタリング
map.setView({ center: currentPosition });

},
function (error) {
  console.log("現在位置の取得に失敗しました。");
}
);
} else {
  console.log("お使いのブラウザはGeolocation APIをサポートしていません。"
  );
}
}

function clickAlert() {
  alert("ボタンがクリックされました！"); // これは動く
  lat = $(this).closest("#post-data").find(".lat-lon").data("lat");
  lon = $(this).closest("#post-data").find(".lat-lon").data("lon");
  console.log(lat,'phpファイルの144行目'); // undefinedになる
  //GetMap(lat, lon);
}

/*　上のclickAlertのコードとの違いがわからなくて、動かない部分
$("#search").on("click", function () {
  const lat = $(this).closest("#post-data").find(".lat-lon").data("lat");
  const lon = $(this).closest("#post-data").find(".lat-lon").data("lon");
  console.log(lat);
  console.log('ボタンがクリックされました！');
  GetMap(lat, lon);
});
*/
</script>

<!-- Main[Start] -->
<a class="navbar-brand" href="p01_index.php">投稿する</a>
<div id="post-all"><?= $view ?></div>
<div id="map" class="contents">
  <h2 class="title">DESTINATION</h2>
  <div class="map"></div>
  <div class="destination">
    <div id="myMap"></div>
    <div class="directionsContainer">
      <div id="directionsPanel"></div>
      <div id="directionsItinerary"></div>
    </div>
  </div>
<!-- Main[End] -->

<?php require'p99_footer.php';?>