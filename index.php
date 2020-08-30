<?php

    require('function.php');
    debugLogStart();

    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> TOPページ');
    $siteTitle = 'TOP';
    require('head.php');
    require('header.php');

    // shopを取得
    $shops = getShops();
    debug('shopの中身'.print_r($shops, true));

?>
      <div class="suc-msg js-suc-msg"><?php if(!empty($_SESSION['suc_msg'])){ echo $_SESSION['suc_msg'];} ?></div>
      
      <?php sessionOnce('suc_msg'); ?>
      
     
      <main class="main-container">
          <div class="container">
              <img src="img/freedom_ogp.png" alt="" id="top-img">
          </div>
          <div class="select-part">
            <h3 class="card-header">店舗を選択</h3>
            
            <?php foreach($shops as $key => $val): ?>
            <table class="shop-table">
              <tbody>
                <tr>
                  <td class="shop-mark">
                    <img src="img/triangle.png" alt="">
                  </td>
                  <td class="shop-list">
                    <input type="radio" id="shop<?php echo $val['id']; ?>" name="shop" value="<?php echo $val['id']; ?>" class="js-select-shop" data-shopid="<?php echo $val['id']; ?>"><?php echo $val['shop_name']; ?>
                  </td>
                  <td class="shop-btn">
                    <button class="detail-btn">詳細</button>
                  </td>
                  <label for="shop<?php echo $val['id']; ?>" class="shop-area"></label>
                </tr>
              </tbody>
            </table>
            
            <?php endforeach; ?>
            
            <h3 class="card-header">メニューを選択</h3>
            
            <?php
              // shop1でmenuを取得
              $menus = getMenus(1);
            ?>
            
            <div id="ajaxreload">
            <?php foreach($menus as $key => $val): ?>
            
              <table class="shop-table js-view-menu">
              <tbody>
                <tr>
                  <td class="menu-mark">
                    <img src="img/triangle.png" alt="">
                  </td>
                  <td class="shop-list">
                    <input type="radio" id="menu<?php echo $val['id']; ?>" name="menu" value="<?php echo $val['id']; ?>" class="js-select-menu" data-menuid="<?php echo $val['id']; ?>"><?php echo $val['menu_name']; ?>
                  </td>
                  <td class="shop-btn">
                    <button class="detail-btn">詳細</button>
                  </td>
                  <label for="menu<?php echo $val['id']; ?>" class="shop-area"></label>
                </tr>
              </tbody>
            </table>
            
            <?php endforeach; ?>
            
            
            <h3 class="card-header">予約日時を選択</h3>
            
            <?php
                $weekStart = getWeekStart();    
            ?>
            
          <div id="ajaxReservationReload">
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
                <tr>
                    <th class="first">10:00〜</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                </tr>
                <tr>
                    <th class="first">12:00〜</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                </tr>
                <tr>
                    <th class="first">14:00〜</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                </tr>
                <tr>
                    <th class="first">16:00〜</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                </tr>
                <tr>
                    <th class="first">18:00〜</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                    <th>◯</th>
                </tr>
            </table>
            </div>
            </div>
          </div>
      </main>
      
<?php

    require('footer.php');
    require('script.php');

?>