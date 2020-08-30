
 
 <header class="header">
  <div class="header-container">
    <a href="index.php"><img src="img/home-icon.png" alt="" class="home-img"></a>
    <div class="header-center">
      <p class="execuse">こちらは予約フォームのダミーであり、実際のサービスを提供するものではありません。</p>
    </div>
    <div class="header-right">
     <?php if(!isLogin()): ?>
       <a href="login.php"><button class="auth-btn">ログイン</button></a>
       <a href="register.php"><button class="auth-btn">会員登録</button></a>
     <?php else: ?>
       <a href="logout.php"><button class="auth-btn">ログアウト</button></a>
       <a href="mypage.php"><button class="auth-btn">マイページ</button></a>
     <?php endif; ?>
    </div>
  </div>
</header>