<?php
//共通に使う関数を記述
//XSS対応（ echoする場所で使用！それ以外はNG ）

//　IPA_情報処理推進機構、クロスサイトスクリプティング
function h($str){
return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

