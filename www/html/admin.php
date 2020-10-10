<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する読み込み
require_once MODEL_PATH . 'item.php';

//ログインチェック用のセッションを開始する。
session_start();
//ユーザーIDがセッション内にあるか確認
if(is_logined() === false){
//なければLOGIN_URLへリダイレクト
  redirect_to(LOGIN_URL);
}


//PDOを取得
$db = get_db_connect();

//PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

//ユーザータイプが管理者タイプか否か確認
if(is_admin($user) === false){
//管理者じゃなければログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//全てのアイテムデータを取得
$items = get_all_items($db);
//管理者view画面へ。
include_once VIEW_PATH . '/admin_view.php';
