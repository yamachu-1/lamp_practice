<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

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

  //cart_idがpostされたら代入
  $cart_id = get_post('cart_id');

  //cart_idの物を抽出し、行を削除
  if(delete_cart($db, $cart_id)){
    set_message('カートを削除しました。');
  } else {
    set_error('カートの削除に失敗しました。');
  }

  redirect_to(CART_URL);
}