<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細画面</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

  <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>ユーザー</th>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
    <?php $i = 0;
      foreach($purchases as $purchase) {
        if($i >=1){ break; }?>
          
          <tr>
            <td><?php print(h($purchase['user_id'])); ?></td>
            <td><?php print(h($purchase['purchase_id'])); ?></td>
            <td><?php print(h($purchase['purchase_date'])); ?></td>
            <td><?php print(h(number_format($purchase['total']))); ?>円</td>
        <?php $i++;
      } ?> 
  </div>
  
  <div class="container">
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>購入時の商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
    <?php foreach($purchase_details as $purchase_detail) { ?>
          <tr>
            <td><?php print(h($purchase_detail['name'])); ?></td>
            <td><?php print(h($purchase_detail['price'])); ?></td>
            <td><?php print(h($purchase_detail['amount'])); ?></td>
            <td><?php print(h($purchase_detail['subtotal'])); ?>円</td>         
    <?php } ?> 
  </div>
</body>
</html>