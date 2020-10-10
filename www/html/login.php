<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

//セッションをスタート
session_start();

//ログインしていたらHOMEへリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}

include_once VIEW_PATH . 'login_view.php';