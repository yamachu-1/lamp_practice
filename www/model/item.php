<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

// item_idからitemの情報をゲット
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";

  return fetch_query($db, $sql, [$item_id]);
}
//is_openがtrueのときは1のみ取得。それ以外は全て取得　？？？
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

//上の式に対して、is_openはfalseなので、全てのitemsを出力
function get_all_items($db){
  return get_items($db);
}
//上の式にたいして、is_openがtrueになったので、statusが1のもののみ出力
function get_open_items($db){
  return get_items($db, true);
}

function regist_item($db, $name, $price, $stock, $status, $image){
//ファイル名称を設定
  $filename = get_upload_filename($image);
//さっきの項目で空欄や文字制限から外れていた場合・・・
  if(validate_item($name, $price, $stock, $filename, $status) === false){
//FALSEと返却
    return false;
  }
//insertでBDに挿入。
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

//transactionでitem情報の格納＋画像情報の格納が完了したらOK
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

//名前、価格、在庫、ファイル名、公開ステータスをDBに保存
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
      VALUES(?,?,?,?,?)
  ";
  return execute_query($db, $sql, [$name, $price, $stock, $filename, $status_value]);
}
//item_idから検索して、statusを更新
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$status, $item_id]);
}
//item_idからstockを更新
function update_item_stock($db, $item_id, $stock){

  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$stock, $item_id]);
}

//item_idからstockを更新
function purchased_item_stock($db, $item_id, $stock, $amount){
  $new_stock = $stock - $amount;
  
    $sql = "
      UPDATE
        items
      SET
        stock = ?
      WHERE
        item_id = ?
      LIMIT 1
    ";
    
    return execute_query($db, $sql, [$new_stock, $item_id]);
  }

//購入履歴をDBに登録
function add_purchase_history($db, $user_id, $date){

  $date = DATE;
  $sql = "
  INSERT INTO 
	purchase(
    user_id,
    purchase_date
    )
    VALUES (? , ?)
    ";
    return execute_query($db,$sql, [$user_id, $date]);
}

function read_purchase_id($db){
  $sql = "  
  SELECT 
    purchase_id
  FROM
    purchase
  ORDER BY purchase_id DESC
    LIMIT 1
  ";
  return fetch_all_query($db, $sql);
}

function add_purchase_details($db, $purchase_id, $item_id,$amount,$price){

  $sql = "
    INSERT INTO
        purchase_details(
          purchase_id,
          item_id,
          amount,
          price
          )
        VALUES(?,?,?,?)
    ";
    return execute_query($db,$sql, [$purchase_id, $item_id, $amount, $price]);
}
//DBへの登録＋stockの更新ができるか確認！
function update_item_stock_and_purchase_details($db, $purchase_id, $item_id, $stock, $amount, $price){
  $db->beginTransaction();
  if(purchased_item_stock($db, $item_id, $stock, $amount)
   && add_purchase_details($db, $purchase_id, $item_id, $amount, $price)){
    $db->commit();
    return true;
  }
    $db->rollback();
    return set_error('購入に失敗しました。');
    return false;
}

function output_history($db, $user_id){
  $sql = "
  SELECT 
  purchase.user_id,
  purchase.purchase_id,
  purchase_date,
  SUM(price * amount) as total
 FROM
   purchase
  INNER JOIN
   purchase_details
 ON
   purchase_details.purchase_id = purchase.purchase_id
 WHERE
  user_id = ?
 GROUP BY
  purchase.purchase_id
 ORDER BY
  purchase_id DESC
  ";

return fetch_all_query($db, $sql, [$user_id]);
}

function output_detail($db, $purchase_id){
  $sql = "
  SELECT 
    purchase.user_id,
    purchase.purchase_id,
    purchase_date, 
    items.name, 
    purchase_details.price, 
    purchase_details.amount, 
    purchase_details.price * purchase_details.amount as subtotal 
  FROM 
    purchase 
  INNER JOIN 
    purchase_details 
  ON 
    purchase_details.purchase_id = purchase.purchase_id 
  INNER JOIN 
    items 
  ON 
    purchase_details.item_id = items.item_id 
  WHERE 
    purchase.purchase_id = ?
  ";

return fetch_all_query($db, $sql, [$purchase_id]);
}

function output_history_by_admin($db){
  $sql = "
  SELECT 
  purchase.purchase_id,
  purchase_date,
  SUM(price * amount) as total
 FROM
   purchase
 
  INNER JOIN
   purchase_details
 ON
   purchase_details.purchase_id = purchase.purchase_id
 GROUP BY
  purchase.purchase_id
 
  ";
return fetch_all_query($db, $sql);
}
//cartにある一つ一つの商品のpriceとamountを掛け算した結果を足す。
function sum_purchase($purchases){
  $total_price = 0;
  $purchase = 0;
  foreach($purchases as $purchase){
    $total_price += $purchase['price'] * $purchase['amount'];
  }
  return $total_price;
}

function get_ranking($db){
  $sql = "
  SELECT 
   SUM(amount) as total,
   items.name,
   items.price,
   items.image
  FROM
    purchase_details
  INNER JOIN
    items
  ON
    purchase_details.item_id = items.item_id
  GROUP BY
    purchase_details.item_id
  ORDER BY
    total DESC 
  LIMIT 3
  ";
  return fetch_all_query($db,$sql);
}

function destroy_item($db, $item_id){
//item情報をDBから取得 
  $item = get_item($db, $item_id);
//DBから取れなかったらFALSE
  if($item === false){
    return false;
  }
//トランザクション起動
  $db->beginTransaction();
//itemとimageのdeleteが完了した時・・・
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
//コミットさせる
      $db->commit();
//TRUEと返却
      return true;
  }
  $db->rollback();
  return false;
}

//item情報を削除
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$item_id]);
}


//itemstatusが１か否か
function is_open($item){
  return $item['status'] === 1;
}

//一通りの適性確認
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

//名前が一定の文字数か
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}
//価格は0以上の整数か？
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//在庫数は0以上の整数か？
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//ファイル名があるか？
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}
//公開ステータスが入力されているか？
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}