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

//itemsとcartsをitem_idで統合。入力されたuser_idのものを出力。
$carts = get_user_carts($db, $user['user_id']);
//上のDBから取り出したデータのうち、price*amountの値を足していきトータルを算出
$total_price = sum_carts($carts);

$token = get_csrf_token();

//car_view.phpへ渡す。
include_once VIEW_PATH . 'cart_view.php';
