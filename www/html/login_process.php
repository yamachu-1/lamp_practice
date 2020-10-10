<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}
// POSTでtoken情報を取得
$token = get_post('csrf_token');
// セッションとpostのtoken情報が同じか確認
if(is_valid_csrf_token($token) === TRUE){
  //token生成
  $token = get_csrf_token();

  //nameが送信されていたら代入
  $name = get_post('name');
  //passwordが送信されていたら代入
  $password = get_post('password');
  //dbへ接続
  $db = get_db_connect();

  //うまくusername,passwordが入力されている場合、ログイン情報を取得
  $user = login_as($db, $name, $password);
  //DBから取得できなかった場合・・・
  if( $user === false){
    //通知の上ログイン画面にリダイレクト
    set_error('ログインに失敗しました。');
    redirect_to(LOGIN_URL);
  }
  //session[__message]にログインした旨保存する
  set_message('ログインしました。');
  //usertypeがadminだった場合は管理画面へリダイレクト
  if ($user['type'] === USER_TYPE_ADMIN){
    redirect_to(ADMIN_URL);
  }
  //それ以外はHOMEへリダイレクト
  redirect_to(HOME_URL);
}