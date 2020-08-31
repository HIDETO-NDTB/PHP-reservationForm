<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ログインページ');

  if (isLogin()) {
      debug('ログインチェック：ログインユーザーなのでTOPに遷移します');
      header("Location:index.php");
  } else {
      debug('ログインチェック：未ログインユーザーでした。');
  }

  if (!empty($_POST)) {
      debug('ログインのPOST送信がありました。');
      
      $loginid = $_POST['loginid'];
      $pass = $_POST['password'];
      
      // 未入力チェック
      validRequired($loginid, 'id');
      validRequired($pass, 'pass');
      
      if (empty($err_msg)) {
          
          // ログインIDのバリデーション
          validMinLength($loginid, 'id');
          validHalf($loginid, 'id');
          
          // パスワードのバリデーション
          validMinLength($pass, 'pass');
          validMaxLength($pass, 'pass');
          validHalf($pass, 'pass');
          
          if (empty($err_msg)) {
              try {
                  $dbh = dbConnect();
                  $sql = 'SELECT password FROM users WHERE id = :id AND delete_flg = 0';
                  $data = array(':id'=>$loginid);
                  
                  $stmt = queryPost($dbh, $sql, $data);
                  $rst = $stmt->fetch(PDO::FETCH_ASSOC);
                  
                  debug('DBのパスワード：'.print_r($rst, true));
                  
                  if (empty($rst)) {
                      debug('ログインIDが見つかりません。');
                      $err_msg['common'] = ERR_MSG10;
                  } elseif (password_verify($pass, $rst['password'])) {
                      debug('IDとパスワードが一致しました。');
                      $_SESSION['login_time'] = time();
                      $_SESSION['login_limit'] = 60*60;
                      $_SESSION['login_user'] = $loginid;
                      header("Location:index.php");
                  } else {
                      debug('パスワードが一致しません。');
                      $err_msg['common'] = ERR_MSG10;
                  }
              } catch (Exception $e) {
                  error_log('エラー発生：'. $e->getMessage());
              }
          }
      }
  }

?>


<?php
  $siteTitle = 'ログイン';
  require('head.php');
  require('header.php');
?>

<div class="suc-msg js-suc-msg"><?php echo sessionOnce('suc_msg'); ?></div>

<main class="sub-container">
    <form action="" method="post">
        <h3 class="card-header">ログイン<span class="err-area"><?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?></span></h3>
        
        <p class="validation">登録されたログインIDとパスワードを入力して [ログイン]ボタンを押してください。</p>
        
        <h4 class="sub-header">ログイン情報</h4>
        
        <h5 class="title">ログインID<span class="err-area"><?php if (!empty($err_msg['id'])) {
    echo $err_msg['id'];
} ?></span></h5>
        <input type="text" name="loginid" class="long-text <?php if (!empty($err_msg['id'])) {
    echo 'err';
} ?>" value="<?php if (!empty($loginid)) {
    echo $loginid;
} ?>">
        
        <h5 class="title">パスワード<span class="err-area"><?php if (!empty($err_msg['pass'])) {
    echo $err_msg['pass'];
} ?></span></h5>
        <input type="password" name="password" class="long-text <?php if (!empty($err_msg['pass'])) {
    echo 'err';
} ?>" value="<?php if (!empty($pass)) {
    echo $pass;
} ?>">
        <p class="validation">※パスワードの再設定は<a href="reminder.php">こちら</a></p>
        
        <div class="btn-area">
        
          <input type="submit" class="submit-btn" value="ログイン">
          
        </div>
        
    </form>
</main>


<?php
  require('footer.php');
  require('script.php');
?>