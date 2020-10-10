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

  //item_idがポストされていたら受け取り。
  $item_id = get_post('item_id');
  //カートを呼び出しor新規作成し、amountに１をたす
  if(add_cart($db,$user['user_id'], $item_id)){
    set_message('カートに商品を追加しました。');
  } else {
    set_error('カートの更新に失敗しました。');
  }
}
  redirect_to(HOME_URL);

