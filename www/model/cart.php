<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//itemsとcartsをitem_idで統合。入力されたuser_idのものを出力。
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
     carts.user_id = ?
  ";

  return fetch_all_query($db, $sql, [$user_id]);
}

//carts,itemsテーブルをitem_idが同一の物でマージしたのちに、user_idとcart_idのものを取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
      
  ";

  return fetch_query($db, $sql, [$user_id, $item_id]);

}

function add_cart($db, $user_id, $item_id ) {
  //user_idのデータから特定のカートをDBから呼び出し
  $cart = get_user_cart($db, $user_id, $item_id);

  //呼び出せなかった場合
  if($cart === false){
    //DBに新たにカートを追加
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//cartに商品を入れる
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
       )
      VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, [$item_id, $user_id, $amount]);
}

//cart_idの商品数を変更
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql,[$amount,$cart_id]);
}

//cart_idのものを削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, [$cart_id]);
}

function purchase_carts($db, $carts){
  //cartsの入力内容から、引数なし、在庫数不足などのエラーがないか確認。
  if(validate_cart_purchase($carts) === false){
    return false;
  }

  //先に購入履歴に入力
  add_purchase_history($db,$carts[0]['user_id'],$date);
  $purchase_id = read_purchase_id($db)[0]['purchase_id'];
  //配列から一つずつ繰り返し
  foreach($carts as $cart){
    //item_idのものに(stock-注文数)した物を上書き
    if(update_item_stock_and_purchase_details(
        $db, 
        // $cart['user_id'],
        $purchase_id,
        $cart['item_id'], 
        $cart['stock'],
        $cart['amount'],
        $cart['price']
      ) === false ){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  delete_user_carts($db, $carts[0]['user_id']);
}

 //user_idのもののカート内容を削除。
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, [$user_id]);
}

//cartにある一つ一つの商品のpriceとamountを掛け算した結果を足す。
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  //カートがないとエラーを吐き出す。
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  //cartsから一つずつ引数
  foreach($carts as $cart){
    //$cartの引数が開いたか？
    if(is_open($cart) === false){
      //ひらけられないと以下のエラー
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //stockよりamountの方が多い時
    if($cart['stock'] - $cart['amount'] < 0){
      //エラーをはき、現在のstock数を購入可能数として表示
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

