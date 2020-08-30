<?php

    require('function.php');
    require('head.php');

    debug('Ajax2中身：'.print_r($_POST, true));
    $shop_id = ($_POST['selectShop']) ? $_POST['selectShop'] : '';
    $menu_id = ($_POST['selectMenu']) ? $_POST['selectMenu'] : '';

    // 予約日だけの状態で予約状況を取得
    $rsv_date = getReservations($shop_id, $menu_id);
              
?>
           
<?php
    // 今週月曜日を1/1の日付形式で取得
    $weekStart = getWeekStart();
    // 今週月曜日を2020-01-01の日付形式で取得
    $dataWeekStart = getDataWeekStart();
?>

<form action="" method="post" class="">
            
    <table class="table table-time">
        <tr>
            <th class="first"></th>
            <th><?php echo $weekStart.getDateOfWeek($weekStart); ?></th>
            <th><?php echo getCalendar($weekStart, 1).getDateOfWeek(getCalendar($weekStart, 1)); ?></th>
            <th><?php echo getCalendar($weekStart, 2).getDateOfWeek(getCalendar($weekStart, 2)); ?></th>
            <th><?php echo getCalendar($weekStart, 3).getDateOfWeek(getCalendar($weekStart, 3)); ?></th>
            <th><?php echo getCalendar($weekStart, 4).getDateOfWeek(getCalendar($weekStart, 4)); ?></th>
            <th><?php echo getCalendar($weekStart, 5).getDateOfWeek(getCalendar($weekStart, 5)); ?></th>
            <th><?php echo getCalendar($weekStart, 6).getDateOfWeek(getCalendar($weekStart, 6)); ?></th>
        </tr>
            
        <?php
              for($i=10; $i<=18; $i=$i+2):
        ?>
        <tr>
            <th class="first"><?php echo $i.':00〜'; ?></th>
            <th><div class="<?php echo isReservation($dataWeekStart, $i, $rsv_date)  ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv($dataWeekStart, $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation($dataWeekStart, $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,1), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,1), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,1), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,2), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,2), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,2), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,3), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,3), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,3), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,4), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,4), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,4), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,5), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,5), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,5), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
            <th><div class="<?php echo isReservation(getDataWeek($dataWeekStart,6), $i, $rsv_date) ? 'unlink-area' : 'link-area'; ?>"><a href="<?php echo jumpRsv(getDataWeek($dataWeekStart,6), $i, $_POST['selectShop'], $_POST{'selectMenu'}); ?>"><?php echo isReservation(getDataWeek($dataWeekStart,6), $i, $rsv_date) ? '×' : '◯'; ?></a></div></th>
        </tr>
        <?php
              endfor;
        ?>
    </table>
    <div class="discription">
        <p class="discription"><span class="ok">○</span>受付中</p>
        <p class="discription"><span class="ng">×</span>受付終了</p>
        <?php
          if(!isLogin()){
        ?>
        <a href="login.php" class="discription">ご予約は会員様限定です。ログインの上、ご予約下さい。</a>
        <?php
          }
        ?>
    </div>
</form>
    
<?php
    require('script.php');
?>