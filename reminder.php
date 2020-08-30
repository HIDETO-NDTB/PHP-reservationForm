<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> リマインダーページ');

  if(isLogin()){
      debug('ログインチェック：ログインユーザーなのでTOPに遷移します');
      header("Location:index.php");
  }else{
      debug('ログインチェック：未ログインユーザーでした。');
  }

  if(!empty($_POST)){
      
      debug('リマインダーのPOST送信がありました。');
      
      $email = $_POST['email'];
      
      validRequired($email, 'email');
      
      if(empty($err_msg)){
          
          try{
              $dbh = dbConnect();
              $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg = 0';
              $data = array(':email' => $email);
              
              $stmt = queryPost($dbh, $sql, $data);
              $rst = $stmt->fetch(PDO::FETCH_ASSOC);
              debug('リマインダーのID:'.print_r($rst, true));
              
              if(!$rst){
                  debug('登録されていないemailです。');
                  $err_msg['common'] = 'そのメールアドレスでの登録はありません。';
                     
              }else{
                  // パスワード変更用のキーを発行
                  $auth_key = makeRandKey();
                  
                  // セッションに情報を格納
                  $_SESSION['auth_email'] = $email;
                  $_SESSION['token'] = $auth_key;
                  $_SESSION['auth_limit'] = time() + 60*30;
                  debug('パスワード再発行情報：'.print_r($_SESSION, true));
                  
                  $from = 'service@gmail.com';
                  $to = $email;
                  $subject = 'パスワード再発行のお手続き | 予約フォーム';
                  $comment = <<<EOT
                  平素は当予約フォームをご愛顧頂き、誠にありがとうございます。
                  パスワード再発行手続きのご依頼を頂きましたので、メールにてご案内申し上げます。
                  下記パスワード再発行用キーをコピーの上で、下記URLへアクセス下さいます様、お願い致します。
                  パスワード再発行用キー：$auth_key
                  
                  尚、パスワード再発行用キーの有効期限30分となっておりますので、お早めのお手続きをお願い致します。
                  
                  url : http://localhost/PHP_Reservation_Form/passwordRequest.php
                  
                  今後とも宜しくお願い申し上げます。
                  
                  予約フォーム株式会社 お客様サポートセンター
                  
                  EOT;
                  
                  sendMail($from, $to, $subject, $comment);
                  
                  $_SESSION['suc_msg'] = SUC_MSG02;
                  header("Location:index.php");
                  
              }
          }catch(Eception $e){
              error_log('エラー発生：' .$e->getMessage());
          }
      }
  }

?>


<?php
  $siteTitle = 'リマインダー';
  require('head.php');
  require('header.php');
?>

<main class="sub-container">
    <form action="" method="post">
        <h3 class="card-header">パスワード再設定<span class="err-area"><?php if(!empty($err_msg['common'])){ echo $err_msg['common'];} ?></span></h3>
        
        <p class="reminder">パスワードをお忘れの方は、こちらからパスワードの再設定ができます。</p>
        <p class="reminder">ご登録頂いているメールアドレスを入力し[送信する]ボタンを押して下さい。</p>
        <p class="reminder">パスワード変更用のURLをメールにてお知らせ致します。</p>
        
        <h4 class="sub-header">パスワード情報</h4>
        
        <h5 class="title">メールアドレス<span class="err-area"><?php if(!empty($err_msg['email'])){ echo $err_msg['email'];} ?></span></h5>
        <input type="text" name="email" class="long-text <?php if(!empty($err_msg['email'])){ echo 'err';} ?>" value="<?php if(!empty($email)){ echo $email;} ?>">
        
        <div class="btn-area">
        
          <input type="submit" class="submit-btn" value="送信する">
          
        </div>
        
    </form>
</main>


<?php
  require('footer.php');
  require('script.php');
?>