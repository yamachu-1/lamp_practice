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

  //POSTでitem_idを取得
  $item_id = get_post('item_id');

  //アイテムを削除が完了したか？
  if(destroy_item($db, $item_id) === true){
    set_message('商品を削除しました。');
  } else {
    set_error('商品削除に失敗しました。');
  }



  redirect_to(ADMIN_URL);

}