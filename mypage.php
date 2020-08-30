<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> マイページ');

  if(!isLogin()){
      debug('ログインチェック：未ログインユーザーなのでログインページに遷移します');
      header("Location:login.php");
  }else{
      debug('ログインチェック：ログインユーザーでした。');
  }

  // パスワード変更の処理
  if(!empty($_POST['changePass'])){
      
      debug('パスワード変更のPOST情報がありました');
      
      $pass_old = $_POST['pass-old'];
      $pass_new = $_POST['pass-new'];
      $pass_re = $_POST['pass-re'];
      
      // 未入力チェック
      validRequired($pass_old, 'pass_old');
      validRequired($pass_new, 'pass_new');
      validRequired($pass_re, 'pass_re');
      
      if(empty($err_msg)){
          
          // 現在のパスワードが合っているかチェック
          try{
              $dbh = dbConnect();
              $sql = 'SELECT password FROM users WHERE id = :id AND delete_flg = 0';
              $data = array(':id' => $_SESSION['login_user']);
              
              $stmt = queryPost($dbh, $sql, $data);
              $rst = $stmt->fetch(PDO::FETCH_ASSOC);
              debug('パスワードから取得したID：'.print_r($rst, true));
              
              if(!password_verify($pass_old, $rst['password'])){
                  
                  debug('現在のパスワードが違います');
                  $err_msg['pass_old'] = ERR_MSG14;
              }else{
                  
                  debug('現在のパスワードが一致しました。');
                  
                  // 新しいパスワードのバリデーションチェック
                  validMinLength($pass_new, 'pass_new');
                  validMaxLength($pass_new, 'pass_new');
                  validHalf($pass_new, 'pass_new');
                  validPass($pass_new, $pass_re, 'pass_new');
                  
                  if(empty($err_msg)){
                      
                      debug('パスワード変更のバリデーションOKです。');
                      
                      $dbh = dbConnect();
                      $sql = 'UPDATE users SET password = :pass WHERE id = :id';
                      $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':id' => $_SESSION['login_user']);
                      
                      $stmt = queryPost($dbh, $sql, $data);
                      
                      if($stmt){
                          debug('パスワード変更処理が完了しました。');
                          $_SESSION['login_time'] = time();
                          
                          $_SESSION['suc_msg'] = SUC_MSG05;
                          header("Location:index.php");
                      }else{
                          debug('パスワード変更処理に失敗しました。');
                          $err_msg['common_pass'] = ERR_MSG15;
                      }
                  }
              }
          }catch(Excwption $e){
              error_log('エラー発生：' .$e->getMessage());
          }
      }
  }

  // 会員退会の処理
  if(!empty($_POST['withdrawal'])){
      
      debug('会員退会のPOST送信がありました。');
      
      try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
          $data = array(':id' => $_SESSION['login_user']);
          
          $stmt = queryPost($dbh, $sql, $data);
          
          if($stmt){
              
              debug('退会処理完了しました。');
              session_unset();
              $_SESSION['suc_msg'] = SUC_MSG04;
              header("Location:index.php");
          }else{
              debug('退会処理に失敗しました。');
              $err_msg['common_user'] = ERR_MSG11;
          }
      }catch(Exception $e){
          error_log('エラー発生：'. $e->getMessage());
      }          
  }

?>


<?php
  $siteTitle = 'マイページ';
  require('head.php');
  require('header.php');
?>

<main class="sub-container">
        
        <h3 class="card-header">パスワード変更<span class="err-area"><?php if(!empty($err_msg['common_pass'])){ echo $err_msg['common_pass'];} ?></span></h3>
        
        <h4 class="sub-header">パスワード情報</h4>
        
    <form action="" method="post"> 
        
        <h5 class="title">現在のパスワード<span class="err-area"><?php if(!empty($err_msg['pass_old'])){ echo $err_msg['pass_old'];} ?></span></h5>
        <input type="password" class="long-text <?PHP if(!empty($err_msg['pass_old'])){ echo 'err';} ?>" name="pass-old" value="<?php if(!empty($pass_old)){ echo $pass_old; } ?>">
        
        <h5 class="title">新しいパスワード<span class="err-area"><?php if(!empty($err_msg['pass_new'])){ echo $err_msg['pass_new'];} ?></span></h5>
        <input type="password" class="long-text <?PHP if(!empty($err_msg['pass_new'])){ echo 'err';} ?>" name="pass-new" value="<?php if(!empty($pass_new)){ echo $pass_new; } ?>">
        <p class="validation">※半角英数字 4～20文字で入力してください。</p>
        
        <input type="password" class="long-text <?PHP if(!empty($err_msg['pass_new'])){ echo 'err';} ?>" name="pass-re" value="<?php if(!empty($pass_re)){ echo $pass_re; }?>">
        <p class="validation">※確認のためにもう一度パスワードを入力してください。</p>
        
        <div class="btn-area">
        
          <input type="submit" class="submit-btn" name="changePass" value="パスワード変更">
          
        </div>
        
    </form>

        <h3 class="card-header">会員退会<span class="err-area"><?php if(!empty($err_msg['common_user'])){ echo $err_msg['common_user'];} ?></span></h3>
        
        
        <h4 class="sub-header">会員情報</h4>
        
        <h5 class="title">ログインID</h5>
        <input type="text" class="long-text" value="<?php if(!empty($_SESSION['login_user'])){ echo $_SESSION['login_user']; } ?>">
        
    <form action="" method="post">
       <div class="btn-area">
        
         <input type="submit" class="submit-btn" name="withdrawal" value="会員退会">
         
        </div>
        
    </form>
    
</main>


<?php
  require('footer.php');
  require('script.php');
?>