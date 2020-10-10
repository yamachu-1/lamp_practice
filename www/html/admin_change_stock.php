<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// POSTでtoken情報を取得
$token = get_post('csrf_token');
// セッションとpostのtoken情報が同じか確認
if(is_valid_csrf_token($token) === TRUE){
//token生成
 $token = get_csrf_token();

  $db = get_db_connect();

  $user = get_login_user($db);

  if(is_admin($user) === false){
    redirect_to(LOGIN_URL);
  }
  //POSTでitem_idを取得したら代入。
  $item_id = get_post('item_id');
  //POSTでstockを取得したら代入。
  $stock = get_post('stock');

  //在庫数をDBに上書き
  if(update_item_stock($db, $item_id, $stock)){
    set_message('在庫数を変更しました。');
  } else {
    set_error('在庫数の変更に失敗しました。');
  }

  //管理ページへリダイレクト
  redirect_to(ADMIN_URL);
}