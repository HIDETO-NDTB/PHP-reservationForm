<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
      <script>
          
          $(function(){
              
              // ===================================
              // successメッセージ表示
              // ===================================
              var $sucMsg = $('.js-suc-msg'),
                  msg = $sucMsg.text();
              if(msg.replace(/\s+/g, "").length){
                  $sucMsg.slideToggle('slow');
                  setTimeout(function(){ $sucMsg.slideToggle('slow');}, 5000);
              }
              
              // ===================================
              // indexのメニュー表示とshopマーク変更
              // ===================================
              var $selectShop = $('.js-select-shop');
              // ajax通信が蓄積されログが溜まるのを防ぐ為jqxhrを使用
              var jqxhr;
              $selectShop.on('change', function(){
                  var shopId = $(this).data('shopid');
                  var $shopMark = $($(this).parent('.shop-list').siblings('.shop-mark'));
                  if (jqxhr){
                      return;
                  }
                  jqxhr = $.ajax({
                      type: "POST",
                      url: "ajax-menu.php",
                      data: {selectShop : shopId}
                  }).done(function(data){
                      // console.log('Ajax成功です。:');
                      $('#ajaxreload').html(data);
                      
                      // 一旦全てのマークを▶︎に戻す
                      $('.shop-mark').children('img').attr('src','img/triangle.png');
                      // チェックされたものだけ ✔︎ マークにする
                      $shopMark.children('img').attr('src','img/check-icon.png');
                      
                  }).fail(function(msg){
                      // console.log('Ajax失敗。');
                  });
              });          
              
              // ===================================
              // indexの予約テーブル表示とmenuマーク変更
              // ===================================
              var $selectMenu = $('.js-select-menu');
              
              // 選択されているラジオボタンの値を取得
              shopId = $('input:radio[name="shop"]:checked').val();
              
              var jqxhr;
              $selectMenu.on('change', function(){
                  var menuId = $(this).data('menuid');
                  var $menuMark = $($(this).parent('.shop-list').siblings('.menu-mark'));
                  if (jqxhr){
                      return;
                  }
                  jqxhr = $.ajax({
                      type: "POST",
                      url: "ajax-reservation.php",
                      data: {selectShop : shopId, selectMenu : menuId}
                  }).done(function(data){
                      // console.log('Ajax2成功です。');
                      $('#ajaxReservationReload').html(data);
                      // 一旦全てのマークを▶︎に戻す
                      $('.menu-mark').children('img').attr('src', 'img/triangle.png');
                      // チェックされたものだけ ✔︎ マークにする
                      $menuMark.children('img').attr('src', 'img/check-icon.png');
                  }).fail(function(msg){
                      // console.log('Ajax2失敗。');
                  }); 
              });
              
          });
          
      </script>
    </body>
</html>   