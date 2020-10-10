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

  //cart_idがpostされてたら代入
  $cart_id = get_post('cart_id');
  //amountがpostされていたら代入
  $amount = get_post('amount');

  //DBへ$cart_idのところへ$amountを書き込みする。
  if(update_cart_amount($db, $cart_id, $amount)){
    set_message('購入数を更新しました。');
  } else {
    set_error('購入数の更新に失敗しました。');
  }

  redirect_to(CART_URL);
}