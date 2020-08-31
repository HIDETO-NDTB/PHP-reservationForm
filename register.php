<?php
  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 会員登録ページ');

  // ログインユーザーかチェック
  if (isLogin()) {
      debug('ログインチェック：ログインユーザーなのでTOPに遷移します');
      header("Location:index.php");
  } else {
      debug('ログインチェック：未ログインユーザーでした。');
  }

  if (!empty($_POST)) {
      debug('会員登録のPOST送信がありました。');
      
      // 変数にPOSTを格納
      $loginid = $_POST['loginid'];
      $pass = $_POST['password'];
      $pass_re = $_POST['password-re'];
      $name = $_POST['name'];
      $slug = $_POST['slug'];
      $email = $_POST['email'];
      $tel = $_POST['tel'];
      
      // 未入力チェック
      validRequired($loginid, 'id');
      validRequired($pass, 'pass');
      validRequired($name, 'name');
      validRequired($slug, 'slug');
      validRequired($email, 'email');
      
      if (empty($err_msg)) {
          
          // ログインIDチェック
          validMinLength($loginid, 'id');
          validHalf($loginid, 'id');
          validIdDup($loginid, 'id');
          
          // パスワードチェック
          validMinLength($pass, 'pass');
          validMaxLength($pass, 'pass');
          validHalf($pass, 'pass');
          validPass($pass, $pass_re, 'pass');
          
          // 名前チェック
          validMaxLength($name, 'name');
          
          // フリガナチェック
          validKatakana($slug, 'slug');
          
          // emailチェック
          validEmail($email, 'email');
          validEmailDup($email, 'email');
      }
      
      if (empty($err_msg)) {
          debug('バリデーションOK');
          debug('会員登録：' .print_r($_POST, true));
          
          try {
              $dbh = dbConnect();
              $sql = 'INSERT INTO users (id, password, name, slug, email, tel, create_date) VALUES (:id, :pass, :name, :slug, :email, :tel, :create_date)';
              $data = array(':id'=>$loginid, ':pass'=>password_hash($pass, PASSWORD_DEFAULT), ':name'=>$name, ':slug'=>$slug, ':email'=>$email, ':tel'=>$tel, ':create_date'=>date('Y-m-d H:i:s'));
              
              $stmt = queryPost($dbh, $sql, $data);
              
              if ($stmt) {
                  debug('会員登録成功');
                  
                  // セッション情報を代入
                  $_SESSION['login_time'] = time();
                  $_SESSION['login_limit'] = 60*60;
                  $_SESSION['user_id'] = $loginid;
                  
                  // successメッセージを代入
                  $_SESSION['suc_msg'] = SUC_MSG01;
                  
                  header("Location:index.php");
              } else {
                  debug('クエリ失敗');
                  $err_msg['common'] = '会員登録に失敗しました。';
              }
          } catch (Exception $e) {
              echo 'エラー';
              error_log('エラー発生；'. $e->getMessage());
              debug('エラーSQL:'.$sql);
          }
      }
  }
?>
 
<?php
  $siteTitle = '会員登録';
  require('head.php');
  require('header.php');
?>


<main class="sub-container">
  <form action="" method="post">
      <h3 class="card-header">お客様登録<span class="err-area"><?php if (!empty($err_msg['common'])) {
    echo $err_msg['common'];
} ?></span></h3>
      
      <h4 class="sub-header">ログイン情報</h4>
      
      <h5 class="title"><span class="badge badge-req">必須</span>ログインID<span class="err-area"><?php if (!empty($err_msg['id'])) {
    echo $err_msg['id'];
} ?></span></h5>
      <input type="text" name="loginid" class="long-text <?php if (!empty($err_msg['id'])) {
    echo 'err';
} ?>" value="<?php if (!empty($loginid)) {
    echo $loginid;
} ?>">
      <p class="validation">※半角英数字 4文字以上で入力してください。</p>
      
      <h5 class="title"><span class="badge badge-req">必須</span>パスワード<span class="err-area"><?php if (!empty($err_msg['pass'])) {
    echo $err_msg['pass'];
} ?></span></h5>
      <input type="password" name="password" class="long-text <?php if (!empty($err_msg['pass'])) {
    echo 'err';
} ?>" value="<?php if (!empty($pass)) {
    echo $pass;
} ?>">
      <p class="validation">※半角英数字 4～20文字で入力してください。</p>
      <input type="password" name="password-re" class="long-text" value="<?php if (!empty($pass_re)) {
    echo $pass_re;
} ?>">
      <p class="validation">※確認のためにもう一度パスワードを入力してください。</p>
      
      <h4 class="sub-header">基本情報</h4>
      
      <h5 class="title"><span class="badge badge-req">必須</span>お名前<span class="err-area"><?php if (!empty($err_msg['name'])) {
    echo $err_msg['name'];
} ?></span></h5>
      <input type="text" name="name" class="long-text <?php if (!empty($err_msg['name'])) {
    echo 'err';
} ?>" value="<?php if (!empty($name)) {
    echo $name;
} ?>">
      
      <h5 class="title"><span class="badge badge-req">必須</span>フリガナ<span class="err-area"><?php if (!empty($err_msg['slug'])) {
    echo $err_msg['slug'];
} ?></span></h5>
      <input type="text" name="slug" class="long-text <?php if (!empty($err_msg['slug'])) {
    echo 'err';
} ?>" value="<?php if (!empty($slug)) {
    echo $slug;
} ?>">
      
      <h5 class="title"><span class="badge badge-req">必須</span>メールアドレス<span class="err-area"><?php if (!empty($err_msg['email'])) {
    echo $err_msg['email'];
} ?></span></h5>
      <input type="text" name="email" class="long-text <?php if (!empty($err_msg['email'])) {
    echo 'err';
} ?>" value="<?php if (!empty($email)) {
    echo $email;
} ?>">
      
      <h5 class="title"><span class="badge badge-noreq">任意</span>電話番号<span class="err-area"><?php if (!empty($err_msg['tel'])) {
    echo $err_msg['tel'];
} ?></span></h5>
      <input type="text" name="tel" class="long-text <?php if (!empty($err_msg['tel'])) {
    echo 'err';
} ?>" value="<?php if (!empty($tel)) {
    echo $tel;
} ?>">
      <p class="validation">※- （ハイフン）なしで記入　11文字以内</p>
      
      <div class="btn-area">
      
        <input type="submit" class="submit-btn" value="会員登録">
      
      </div>
  </form>



</main>

<?php

  require('footer.php');
  require('script.php');

?>