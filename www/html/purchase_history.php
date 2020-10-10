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

$db = get_db_connect();
$user = get_login_user($db);

// POSTでtoken情報を取得
$token = get_post('csrf_token');
// セッションとpostのtoken情報が同じか確認
if(is_valid_csrf_token($token) === TRUE){
//token生成
 $token = get_csrf_token();

//adminじゃない時は・・・
if(is_admin($user['type']) !== 1){
//関連するuser_idの購入履歴を一覧表示
  $purchases = output_history($db, $user['user_id']);
}else{
//adminは全てのuser_idの購入履歴を一覧表示
  $purchases = output_history_by_admin($db);
}

var_dump($user['user_id']);
//上のDBから取り出したデータのうち、price*amountの値を足していきトータルを算出
$total_price = sum_purchase($purchases);



//car_view.phpへ渡す。
include_once VIEW_PATH . 'purchase_history_view.php';

}
