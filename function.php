<?php

// ====================================
// ログ
// ====================================

// ログを取るか
ini_set('log_errors', 'on');
// ログの出力ファイルを指定
ini_set('error_log', 'php.log');

// ====================================
// debug
// ====================================

$debug_flg = true;
function debug($str)
{
    global $debug_flg;
    if (!empty($debug_flg)) {
        error_log('デバッグ：' .$str);
    }
}

// ====================================
// セッション準備
// ====================================

// セッションファイルの置き場変更
session_save_path("/var/tmp/");
// ガーべージコレクションが削除するセッション有効期限を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('seiion.cookie_lifetime', 60*60*24*30);
// セッションを使う
session_start();
// 現在のセッションIDを新たに生成したものと置き換える
session_regenerate_id();

// ====================================
// debug
// ====================================

function debugLogStart()
{
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示開始');
    debug('セッションID：'.session_id());
    debug('セッションの中身：'.print_r($_SESSION, true));
    debug('現在日時：'.time());
    
    if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
        debug('ログイン期限：'.($_SESSION['login_date'] + $_SESSION[login_limit]));
    }
}

// ====================================
// メッセージ
// ====================================

// エラーメッセージを定数に格納
define('ERR_MSG01', '入力必須です。');
define('ERR_MSG02', '文字以上ご入力下さい。');
define('ERR_MSG03', '半角英数字でご入力下さい。');
define('ERR_MSG04', '文字以内でご入力下さい。');
define('ERR_MSG05', 'パスワードと確認パスワードが異なります。');
define('ERR_MSG06', '全角カタカナでご入力下さい。');
define('ERR_MSG07', 'メールアドレスの形式でご入力下さい。');
define('ERR_MSG08', '既にそのログインIDで登録があります。');
define('ERR_MSG09', '既にそのemailで登録があります。');
define('ERR_MSG10', 'ログインIDかパスワードが違います。');
define('ERR_MSG11', '会員退会できませんでした。管理者にお問い合わせ下さい。');
define('ERR_MSG12', 'パスワード再発行キーが違います。');
define('ERR_MSG13', 'パスワード再発行キーの有効期限が切れています。');
define('ERR_MSG14', '現在のパスワードが違います。');
define('ERR_MSG15', 'パスワード変更ができませんでした。管理者にお問い合わせ下さい。');
define('ERR_MSG16', 'ご予約できませんでした。管理者にお問い合わせ下さい。');



// successメッセージを定数に格納
define('SUC_MSG01', '会員登録ありがとうございます。');
define('SUC_MSG02', 'メールを送信しました。');
define('SUC_MSG03', 'パスワードが変更されました。メールをご確認下さい。');
define('SUC_MSG04', '退会処理を行いました。');
define('SUC_MSG05', 'パスワードが変更されました。');
define('SUC_MSG06', 'ご予約ありがとうございます！');


// エラーメッセージ格納用の配列
$err_msg = array();

// ====================================
// バリデーション関数
// ====================================

// 未入力チェック
function validRequired($str, $key)
{
    if (empty($str)) {
        global $err_msg;
        $err_msg[$key] = ERR_MSG01;
    }
}
// 最小文字数チェック
function validMinLength($str, $key, $len=4)
{
    if (mb_strlen($str) < $len) {
        global $err_msg;
        $err_msg[$key] = $len.ERR_MSG02;
    }
}
// 半角英数字チェック
function validHalf($str, $key)
{
    if (!preg_match('/^[a-zA-Z0-9]+$/', $str)) {
        global $err_msg;
        $err_msg[$key] = ERR_MSG03;
    }
}
// 最大文字数チェック
function validMaxLength($str, $key, $len=20)
{
    if (mb_strlen($str) > $len) {
        global $err_msg;
        $err_msg[$key] = $len.ERR_MSG04;
    }
}
// パスワード同値チェック
function validPass($pass, $pass_re, $key)
{
    if ($pass !== $pass_re) {
        global $err_msg;
        $err_msg[$key] = ERR_MSG05;
    }
}
// 全角カタカナチェック
function validKatakana($str, $key)
{
    if (!preg_match('/^[　 ァ-ヶｦ-ﾟー]+$/u', $str)) {
        global $err_msg;
        $err_msg[$key] = ERR_MSG06;
    }
}
// email形式チェック
function validEmail($str, $key)
{
    if (!preg_match('/^\S+@\S+\.\S+$/', $str)) {
        global $err_msg;
        $err_msg[$key] = ERR_MSG07;
    }
}
// ログインID重複チェック
function validIdDup($str, $key)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT id FROM users WHERE id = :id AND delete_flg = 0';
        $data = array(':id' => $str);
        
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rst) {
            global $err_msg;
            $err_msg[$key] = ERR_MSG08;
        }
    } catch (Exception $e) {
        error_log('エラー発生'. $e->getMessage());
    }
}
// email重複チェック
function validEmailDup($str, $key)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $str);
        
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rst) {
            global $err_msg;
            $err_msg[$key] = ERR_MSG09;
        }
    } catch (Exception $e) {
        error_log('エラー発生'. $e->getMessage());
    }
}

// ====================================
// DB接続
// ====================================

function dbConnect()
{
    // DBへの接続準備
    $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
    $db['heroku_8e6b658c950f637'] = ltrim($db['path'], '/');
    $dsn = 'mysql:dbname=heroku_8e6b658c950f637;host:=us-cdbr-east-02.cleardb.com;charset=utf8';
    $user = 'be25fc14654a4c';
    $password='16ea7f8a';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う（一度に結果セットを全て取得しサーバー負荷を軽減）
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // PDOオブジェクト生成（DBへ接続）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

function queryPost($dbh, $sql, $data)
{
    // クエリ作成
    $stmt = $dbh->prepare($sql);
    // プレースホルダに値をセットしSQL文を実行
    if (!$stmt->execute($data)) {
        debug('クエリに失敗しました。');
        debug('失敗したクエリ' .$sql);
        echo 'エラー発生';
        return 0;
    }
    return $stmt;
}

// ====================================
// メール送信
// ====================================

function sendMail($from, $to, $subject, $comment)
{
    if (!empty($to) && !empty($subject) && !empty($comment)) {
        // 文字化けしないように設定
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        
        // メールを送信（送信結果はtrueかfalseで返ってくる）
        $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
        // 送信結果を判定
        if ($result) {
            debug('メールを送信しました。');
        } else {
            debug('メールの送信に失敗しました。');
        }
    }
}

// ====================================
// 日付関連
// ====================================

// 今日の3日後を取得(1/1)
function getWeekStart()
{
    $rst = date('n/j', strtotime("+3 day", strtotime("today")));
    return $rst;
}
// カレンダー表示用(1/1)
function getCalendar($day, $plus)
{
    $rst = date('n/j', strtotime("$day + $plus day"));
    return $rst;
}
// 今日の3日後を取得(2020-01-01)
function getDataWeekStart()
{
    $rst = date('Y-m-d', strtotime("+3 day", strtotime("today")));
    return $rst;
}
// データ取得用(2020-01-01)
function getDataWeek($day, $plus)
{
    $rst = date('Y-m-d', strtotime("$day + $plus day"));
    return $rst;
}
// 日付から曜日を取得（木）
function getDateOfWeek($date)
{
    $rst = date('w', strtotime($date));
    switch ($rst) {
        case 0:
            $answer = '(日)';
            break;
        case 1:
            $answer = '(月)';
            break;
        case 2:
            $answer = '(火)';
            break;
        case 3:
            $answer = '(水)';
            break;
        case 4:
            $answer = '(木)';
            break;
        case 5:
            $answer = '(金)';
            break;
        case 6:
            $answer = '(土)';
            break;
    }
    return $answer;
}
// 2020年1月1日10時の形式にフォーマット
function getViewDate($day, $time)
{
    $rst = date('Y年n月j日H時〜', strtotime("+$time hour", strtotime("$day")));
    return $rst;
}
// 2020-01-01 10:00:00の形式にフォーマット
function getDataDate($day, $time)
{
    $rst = date('Y-m-d H:i:s', strtotime("+$time hour", strtotime("$day")));
    return $rst;
}

// ====================================
// 独自function
// ====================================

// ログインユーザーかどうか
function isLogin()
{
    if (empty($_SESSION['login_time'])) {
        debug('ログインしていません。');
        return false;
    } elseif ($_SESSION['login_time']+$_SESSION['login_limit'] < time()) {
        debug('セッション有効期限切れです。');
        session_destroy();
        return false;
    } else {
        debug('ログインしています。');
        debug('ユーザー情報：'.print_r($_SESSION, true));
        return true;
        $_SESSION['login_time'] = time();
    }
}

// セッションを1回だけ取得
function sessionOnce($key)
{
    if (!empty($_SESSION[$key])) {
        $_SESSION[$key] = '';
    }
}

// キーの発行
function makeRandKey($length = 8)
{
    $str = '';
    $char = ('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    for ($i=0; $i<$length; $i++) {
        $str .= $char[mt_rand(0, 61)];
    }
    return $str;
}

// 全てのshopを取得
function getShops()
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT id, shop_name FROM shops';
        $data = array();
        
        $stmt = queryPost($dbh, $sql, $data);
        $results = $stmt->fetchAll();
        
        if ($results) {
            return $results;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
    }
}

// 該当shopのmenuを取得
function getMenus($id)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT m.id, menu_name FROM menus AS m RIGHT JOIN menu_shop AS ms ON m.id = ms.menu_id WHERE ms.shop_id = :s_id';
        $data = array(':s_id' => $id);
        
        $stmt = queryPost($dbh, $sql, $data);
        $results = $stmt->fetchAll();
        
        if ($results) {
            return $results;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
    }
}

// shop,menuから予約を取得
function getReservations($s_id, $m_id)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT id, user_id, reservation_date FROM reservations WHERE shop_id = :s_id AND menu_id = :m_id';
        $data = array(':s_id' => $s_id, ':m_id' => $m_id);
        
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetchAll();
        
        if ($rst) {
            debug('予約：'.print_r($rst, true));
            // 予約日時のみを配列に格納
            $rst_array = array_column($rst, 'reservation_date');
        } else {
            debug('shop,menuから予約が見つかりませんでした。');
            // あり得ない日時を代入
            $rst_array = array('1111-11-11 00:00:00');
        }
        debug('予約日：'.print_r($rst_array, true));
        return $rst_array;
    } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
    }
}

// 予約が入っているかチェック
function isReservation($date, $time, $array)
{
    $rst = in_array($date.' '.$time.':00:00', $array);
    if ($rst) {
        return true;
    } else {
        return false;
    }
}

// reserve.phpに飛ぶ
function jumpRsv($day, $time, $s_id, $m_id)
{
    return 'reserve.php?date='.$day.'&time='.$time.'&s_id='.$s_id.'&m_id='.$m_id;
}

// shop_idから店名を取得
function getShopOne($s_id)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT shop_name FROM shops WHERE id = :s_id';
        $data = array(':s_id' => $s_id);
        
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rst) {
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}
// menu_idからメニュー名を取得
function getMenuOne($m_id)
{
    try {
        $dbh = dbConnect();
        $sql = 'SELECT menu_name FROM menus WHERE id = :m_id';
        $data = array(':m_id' => $m_id);
        
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rst) {
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}
