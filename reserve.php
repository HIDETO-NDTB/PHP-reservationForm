<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 予約ページ');

  if (!isLogin()) {
      debug('ログインチェック：未ログインユーザーなのでログインページに遷移します');
      header("Location:login.php");
  } else {
      debug('ログインチェック：ログインユーザーでした。');
  }

  debug('GET情報:'.print_r($_GET, true));

  // GET情報から必要事項を取得
  $shop = getShopOne($_GET['s_id']);
  $menu = getMenuOne($_GET['m_id']);
  $rsv_date = getDataDate($_GET['date'], $_GET['time']);

  debug('予約時間：'.$rsv_date);

  if (!empty($_POST['reservation'])) {
      debug('予約のPOST送信がありました。');
      
      try {
          $dbh = dbConnect();
          $sql = 'INSERT INTO reservations (shop_id, menu_id, user_id, reservation_date) VALUES(:s_id, :m_id, :u_id, :date)';
          $data = array(':s_id' => $_GET['s_id'], ':m_id' => $_GET['m_id'], ':u_id' => $_SESSION['login_user'], ':date' => $rsv_date);
          
          $stmt = queryPost($dbh, $sql, $data);
          
          if ($stmt) {
              $_SESSION['suc_msg'] = SUC_MSG06;
              header("Location:index.php");
          } else {
              $err_msg['common'] = ERR_MSG16;
          }
      } catch (Evception $e) {
          error_log('エラー発生：'.$e->getMessage());
      }
  }

?>


<?php
  $siteTitle = '予約ページ';
  require('head.php');
  require('header.php');
?>

<main class="sub-container">
    <form action="" method="post">
        
        <h3 class="card-header">予約確認<span class="err-area"><?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?></span></h3>
        
        <h4 class="sub-header">予約情報</h4>
         
        
        <h5 class="title">店舗名称<span class="err-area"></span></h5>
        <h6 class="confirm"><?php echo $shop['shop_name']; ?></h6>
        
        <h5 class="title">メニュー名称<span class="err-area"></span></h5>
        <h6 class="confirm"><?php echo $menu['menu_name']; ?></h6>
        
        <h5 class="title">予約日時<span class="err-area"></span></h5>
        <h6 class="confirm"><?php echo getViewDate($_GET['date'], $_GET['time']); ?></h6>
        
        <h4 class="sub-header">会員情報</h4>
        
        <h5 class="title">ログインID</h5>
        <h6 class="confirm"><?php echo $_SESSION['login_user']; ?></h6>
        
        <div class="btn-area">
        
        <input type="button" class="submit-btn back-btn" value="戻る" onclick="history.back()">
        <input type="submit" class="submit-btn" value="予約" name="reservation">
        
        </div>
        
    </form>
    
</main>


<?php
  require('footer.php');
  require('script.php');
?>