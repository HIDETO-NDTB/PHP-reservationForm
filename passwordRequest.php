<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> パス再発行ページ');

  if(isLogin()){
      debug('ログインチェック：ログインユーザーなのでTOPに遷移します');
      header("Location:index.php");
  }else{
      debug('ログインチェック：未ログインユーザーでした。');
  }

  if(!empty($_POST)){
      
      debug('パス再発行のPOST情報がありました。');
      
      $token = $_POST['token'];
      $email = $_SESSION['auth_email'];
      
      validRequired($token, 'token');
      
      if(empty($err_msg)){
          
          // パスワード再発行用キーが一致するかチェック
          if($_SESSION['token'] !== $token){
              
              debug('パスワード再発行キーが一致しません。');
              $err_msg['common'] = ERR_MSG12;
          }else if($_SESSION['auth_limit'] < time()){
              
              debug('パスワード再発行キー有効期限切れです。');
              $err_msg['common'] = ERR_MSG13;
          }else{
              
              debug('パスワード再発行キーが一致しました。');
              $pass_new = makeRandKey();
              
              try{
                  $dbh = dbConnect();
                  $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg=0';
                  $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':email' => $email);
                  
                  $stmt = queryPost($dbh, $sql, $data);
                  
                  if($stmt){
                      debug('パスワード再発行完了しました。');
                      
                      // ユーザーにメールで新パスワードを通知
                      $from = 'service@gmail.com';
                      $to = $email;
                      $subject = 'パスワード再発行のお知らせ | 予約フォーム';
                      $comment = <<<EOT
                  平素は当予約フォームをご愛顧頂き、誠にありがとうございます。
                  パスワード再発行手続きのご依頼を頂きましたので、メールにてご案内申し上げます。
                  下記に新しいパスワードを記載致します。
                  新しいパスワード：$pass_new
                  
                  下記URLよりログインの上、マイページよりパスワード変更をお願い致します。
                  
                  url : http://localhost/PHP_Reservation_Form/login.php
                  
                  今後とも宜しくお願い申し上げます。
                  
                  予約フォーム株式会社 お客様サポートセンター
                  
                  EOT;
                      
                      sendMail($from, $to, $subject, $comment);
                      
                      // セッションを削除
                      session_unset();
                      
                      // successメッセー
                      $_SESSION['suc_msg'] = SUC_MSG03;
                      header("Location:login.php");
                      
                  }else{
                      debug('パスワード再発行のクエリに失敗しました。');
                  }
                  
              }catch(Exception $e){
                  error_log('エラー発生：' .$e->getMessage());
              }
          }
      }
  }

  
?>


<?php
  $siteTitle = 'パスワード再発行';
  require('head.php');
  require('header.php');
?>

<main class="sub-container">
    <form action="" method="post">
        <h3 class="card-header">パスワード再設定<span class="err-area"><?php if(!empty($err_msg['common'])){ echo $err_msg['common'];} ?></span></h3>
        
        <p class="reminder">パスワードをお忘れの方は、こちらからパスワードの再発行ができます。</p>
        <p class="reminder">メールで送信されたパスワード再発行用キーを入力し[送信する]ボタンを押して下さい。</p>
        <p class="reminder">新しいパスワードをメールにてお知らせ致します。</p>
        
        <h4 class="sub-header">パスワード情報</h4>
        
        <h5 class="title">パスワード再発行用キー<span class="err-area"><?php if(!empty($err_msg['token'])){ echo $err_msg['token'];} ?></span></h5>
        <input type="text" name="token" class="long-text <?php if(!empty($err_msg['token'])){ echo 'err';} ?>" value="<?php if(!empty($token)){ echo $token;} ?>">
        
        <div class="btn-area">
        
          <input type="submit" class="submit-btn" value="送信する">
          
        </div>
        
    </form>
</main>


<?php
  require('footer.php');
  require('script.php');
?>