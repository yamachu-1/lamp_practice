<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//user_idからuser情報を獲得
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, [$user_id]);
}
//ユーザー名からuser情報を獲得
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql ,[$name]);
}

function login_as($db, $name, $password){
  //入力されたnameからidやname,password,typeをDBから取得
  $user = get_user_by_name($db, $name);
  //userが取得できない or パスワードが一致しない時・・・
  if($user === false || $user['password'] !== $password){
    //FALSEを返す
    return false;
  }
  //そうでない場合user_idの$valueであるsessionをセット
  set_session('user_id', $user['user_id']);
  //DBから取得した$userを返却
  return $user;
}

//sessionにあるuseridからuser情報を獲得
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

//user情報を新規にインサート
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

//userTYPEがadminか確認
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}
//user_nameとpasswordが適切か確認
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  //適切だったらそれを返却
  return $is_valid_user_name && $is_valid_password ;
}

function is_valid_user_name($name) {
  //一旦trueとする。
  $is_valid = true;
  //最大、最小文字長さを超えていないか？
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    //超えていたらエラーを吐く。
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  //英数字のみ書かれているか？
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

//passwordが適性か確認
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  //長さは適切？
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  //アルファベットが入力されているか？
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  //passwordは確認用のpasswordと同一？
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

//user情報を追加する
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (? , ?)
  ";

  return execute_query($db, $sql, [$name, $password]);
}

