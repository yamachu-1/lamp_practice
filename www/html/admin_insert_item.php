<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//POSTがあったらnameを取得
$name = get_post('name');
//POSTがあったらpriceを取得
$price = get_post('price');
//POSTがあったらstatusを取得
$status = get_post('status');
//POSTがあったらstockを取得
$stock = get_post('stock');
//POSTがあったらimageを取得
$image = get_file('image');

//登録アイテム関数でDBに登録
if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}


redirect_to(ADMIN_URL);