<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータに関する読み込み
require_once MODEL_PATH . 'user.php';
//アイテムデータに関する読み込み
require_once MODEL_PATH . 'item.php';
//カートデータに関する読み込み
require_once MODEL_PATH . 'cart.php';

//ログインチェックセッションを開始
session_start();
//ユーザーIDがセッションにあるか確認
if(is_logined() === false){
//なければLOGIN_URLへリダイレクト
  redirect_to(LOGIN_URL);
}
// POSTでtoken情報を取得
$token = get_post('csrf_token');
// セッションとpostのtoken情報が同じか確認
if(is_valid_csrf_token($token) === TRUE){
//token生成
 $token = get_csrf_token();


  //PDOを取得
  $db = get_db_connect();
  //PDOを利用してログインユーザーのデータを取得
  $user = get_login_user($db);
  //itemsとcartsをitem_idで統合。入力されたuser_idのものを出力する。
  $carts = get_user_carts($db, $user['user_id']);

  //エラーチェック後、「ストックーカート内容物」を更新し、カート内を削除する
  if(purchase_carts($db, $carts)  === false){
    set_error('商品が購入できませんでした。');
    redirect_to(CART_URL);
  } 
  //foreachで一つずつcart内のものを呼び出し、amount*priceで計算したものを足してtotal＿priceに代入
  $total_price = sum_carts($carts);

  include_once '../view/finish_view.php';
}