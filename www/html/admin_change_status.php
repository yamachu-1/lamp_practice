<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する読み込み
require_once MODEL_PATH . 'item.php';;

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


  //POSTで'item_id'があれば代入。
  $item_id = get_post('item_id');
  //POSTで'changes_to'があれば代入
  $changes_to = get_post('changes_to');

  //open状態の時・・・
  if($changes_to === 'open'){
  //DBにopenであることを上書き
    update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  //メッセージ出力
    set_message('ステータスを変更しました。');

  //一方でclose状態の時・・・
  }else if($changes_to === 'close'){
  //DBにcloseであることを上書き
    update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  //メッセージ送信
    set_message('ステータスを変更しました。');
  }else {
  //それ以外の時はエラー吐き出し。
    set_error('不正なリクエストです。');
  }

  //管理者画面へ遷移
  redirect_to(ADMIN_URL);
}