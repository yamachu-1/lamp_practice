<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴画面</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">

<!-- cart_viewから引用。ここから修正 -->

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>ユーザー</th>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>小計</th>
            <th>購入明細表示</th>
          </tr>
        </thead>
        <tbody>
    <?php foreach($purchases as $purchase) { ?>
          <tr>
            <td><?php print(h($purchase['user_id'])); ?></td>
            <td><?php print(h($purchase['purchase_id'])); ?></td>
            <td><?php print(h($purchase['purchase_date'])); ?></td>
            <td><?php print(h(number_format($purchase['total']))); ?>円</td>
            <td>
              <form method="post" action="purchase_detail.php">
                <input type="submit" value="購入明細" class="btn btn-secondary">
                <input type="hidden" name="purchase_id" value="<?php print($purchase['purchase_id']);?>">
                <input type="hidden" name="csrf_token" value="<?php print($token); ?>">
              </form>
            </td>
         
    <?php } ?> 
  </div>
</body>
</html>