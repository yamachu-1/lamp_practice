<?php

//var_dumpで内容確認+終了
function dd($var){
  var_dump($var);
  exit();
}
//header関数で特定のURLへ移動
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

//データが送信されたらGET
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}
//データが送信されたらPOST
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

//ファイルが送信されたら入れる
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

//SESSIONを受けたとったら代入
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

//sessionをセット
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

//session errorsに載せる
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

//get_sessionのエラーをset_$_SESSION内に配列として格納？
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

//SESSIONにエラーがありSESSION errorsが0以外の時
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

//SESSIONにmessage内容を格納
function set_message($message){

  $_SESSION['__messages'][] = $message;
}

//SESSION[__messages]にmessage内容を配列として入れる
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//user_idがセッションにある時
function is_logined(){
  return get_session('user_id') !== '';
}
//送信されたfileが適切か調べる。
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  //送信したファイルの形式を確認
  $mimetype = exif_imagetype($file['tmp_name']);
  //JPEGかPNG
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  //ランダムな文字列＋ファイル形式
  return get_random_string() . '.' . $ext;
}

//48文字のランダムな文字列を出力
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

//特定の場所に画像を保存
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

//画像を削除
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


//入力文字列の長さが最小以上かつ最大以下であることを確認
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

//文字列がアルファベットか確認
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

//正の整数か確認
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

//$format内の中に$stringの文字列が存在するかチェック
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

//fileが適切な名前でない場合、エラーを吐く
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  //送信した画像の形式がGIFかPNGか確認。違っていた場合エラー。あっていた場合TRUE。
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

//htmlspecialcharsを略した。
function h($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

// トークンの生成
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
}