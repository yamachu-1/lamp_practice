<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}
//nameが送信されていたら代入
$name = get_post('name');
//passwordが送信されていたら代入
$password = get_post('password');
//
$password_confirmation = get_post('password_confirmation');

$db = get_db_connect();

try{
  //user名やpasswordが適切か確認し、問題なければDBへusernameとpasswordを挿入。
  $result = regist_user($db, $name, $password, $password_confirmation);
  //取得できなければエラー
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

//登録完了の旨表示
set_message('ユーザー登録が完了しました。');
//sessionへログイン情報をセットしログイン
login_as($db, $name, $password);
redirect_to(HOME_URL);