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

  //関連するuser_idの購入履歴を一覧表示
  $purchases = output_history($db,$user['user_id']);
  $purchase_id = (int)(get_post('purchase_id'));
  $purchase_details = output_detail($db,$purchase_id);
  // $total_price = sum_purchase($purchases);
  //adminじゃない時は・・・
  if((is_admin($user) === TRUE) || ($user['user_id'] === $purchases['user_id'])) {
    // purchase_detail_view.phpへ渡す。
  include_once VIEW_PATH . 'purchase_detail_view.php';

  } else {
    set_error('ユーザー表示エラー');

  }


} else {
   set_error('不正なリクエストです。');
}

