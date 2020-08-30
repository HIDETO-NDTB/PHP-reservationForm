<?php

  require('function.php');
  debugLogStart();

  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ログアウトページ');

  // セッションを削除
  session_destroy();
  debug('ログアウト終了しました。');

  // index.phpに遷移
  header("Location:index.php");


?>