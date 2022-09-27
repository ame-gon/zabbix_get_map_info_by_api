<?php

/*
*/

// 実行時引数の確認
if ( count($argv) != 2 ) {
    exit_script("php get_maps.php <コンフィグファイル>");
    exit_script("");
    exit_script("Example:");
    exit_script("php get_maps.php config.json");
}

// 入力が必要なパラメータの宣言
$zbx_host = '';
$zbx_login_user = '';
$zbx_login_password = '';

// jsonファイルから設定情報の読み込み
$config_file = $argv[1];
$config_json = file_get_contents($config_file); //指定したファイルの要素をすべて取得する
$config_data = json_decode($config_json, true); //json形式のデータを連想配列の形式にする

// 設定用データの整形
$config_data = array_merge($config_data, array('zbx_url'=>$config_data['rpc_path']));

// Zabbix のバージョンで API が仕様変更していることもあり、
// Zabbbix のバージョンごとの制御にも使用できるんじゃないかな、と思ってつけてます。
$ZABBIX_API_VERSION = $config_data['zbx_version'];

// Zabbix API バージョンの確認
$response = get_zabbix_api_version($config_data['zbx_url']);
if ( is_null($response) ) {
    exit_script("Zabbix API バージョンの取得に失敗しました。Zabbix API への接続設定を確認してください。");
}
$config_data = array_merge($config_data, array('zbx_api_version'=>$response['result']));

// Zabbix ログイン
$response = zbx_login($config_data['zbx_url'], $config_data['zbx_login_user'], $config_data['zbx_login_password']);
if ( is_null($response) ) {
    exit_script("ZabbixへのAPI接続に失敗しました。Zabbix ログイン情報を確認してください。");
}
check_response_error($response);

// Token の取得
$auth = $response['result'];
$id = $response['id'];

// Zabbix API バージョンの確認
if ( strcmp($config_data['zbx_api_version'], $ZABBIX_API_VERSION) == 0 ) {
    $response = get_zabbix_maps($config_data['zbx_url'], $auth);
} else {
    $response = null;
}
if ( is_null($response) ) {
    write_log("Zabbix Version => " . $config_data['zbx_api_version'] );
    write_log("Zabbix Version in config => " . $ZABBIX_API_VERSION );
    exit_script("Zabbix から情報取得に失敗しました。Zabbix への接続設定を確認してください。");
}
check_response_error($response);

// 結果表示
$json = json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
write_log($json);


////////////////////////////////////////////////////////////////////////////////
// 以下、関数の定義
////////////////////////////////////////////////////////////////////////////////

// Zabbix から API バージョンを取得する
function get_zabbix_api_version($zbx_url) {
    // リクエストデータの作成
    $request = array(
        'jsonrpc'   => '2.0',
        'method'    => 'apiinfo.version',
        'id'        => 1,
        'auth'      => null,
        'params'    => array(),
    );

    return get_zbx_contents_response($zbx_url, $request);
}

// Zabbix にログインしてその結果を取得する
function zbx_login($zbx_url, $zbx_login_user, $zbx_login_password) {
    // リクエストデータの作成
    $request = array(
        'jsonrpc'   => '2.0',
        'method'    => 'user.login',
        'params'    => array(
            'user'      => $zbx_login_user,
            'password'  => $zbx_login_password,
        ),
        'id'        => 1,
        'auth'      => null,
    );
    
    return get_zbx_contents_response($zbx_url, $request);
}

// Zabbix からマップ関連の情報を取得する
function get_zabbix_maps($zbx_url, $auth, $id = 1) {
    $request = array(
        'jsonrpc'   => '2.0',
        'method'    => 'map.get',
        'params'    => array(
            'output'      => "extend",
            'selectOperations'      => "extend",
            'selectRecoveryOperations'      => "extend",
            'selectFilter'      => "extend",
            'selectAcknowledgeOperations' => "extend",
        ),
        'auth'      => $auth,
        'id'        => $id,
    );
    
    return get_zbx_contents_response($zbx_url, $request);
}

// HTTP で POST して結果を得る
function get_zbx_contents_response($zbx_url, $request) {
    // リクエストデータを JSON 形式に変換
    $request_json = json_encode($request);

    // HTTPストリームコンテキストの作成
    $opts['http'] = array(
        'method'    => 'POST',
        'header'    => 'Content-Type: application/json-rpc',
        'content'   => $request_json,
    );
    $context = stream_context_create($opts);

    // リクエストの実行
    $response_json = file_get_contents($zbx_url, false, $context);
    
    // レスポンスの表示
    $response = json_decode($response_json, true);
    return $response;
}

// メッセージ表示用関数
function write_log($msg) {
    echo $msg, "\n";
}

// エラー情報を含んでいるか確認。エラー情報があればその配列に含まれる data 情報を表示して終了。
function check_response_error($response) {
    if ( array_key_exists('error', $response)) {
        exit_script("Error: " . $response['error']['data']);
    }
}

// メッセージがあれば表示して終了
function exit_script($msg) {
    write_log($msg);
    exit(1);
}
