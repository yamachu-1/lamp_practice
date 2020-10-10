<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

session_start();
//$_SESSION変数に空の配列を代入
$_SESSION = array();
//session内にある配列を$paramに代入
$params = session_get_cookie_params();
//cookieを過去にして削除
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
//session内のデータを消去する
session_destroy();

redirect_to(LOGIN_URL);

