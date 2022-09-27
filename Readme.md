# Readme.md

## ■ 本ツールで実現したいこと
Zabbix のマップ情報を API で取得して JSON 形式でログに残す。

## ■ 実行環境
- php が動作する環境
    - 動作確認 php バージョン：7.2.34

## ■ 使い方
### config.json の準備
config.json.org を config.json にリネームして以下情報を定義する。

```json
{
    "zbx_host" : <Zabbixのホスト名 or IP>,
    "zbx_login_user" : <Zabbix へのログインユーザー名>,
    "zbx_login_password" : <上記 zbx_login_user のログインパスワード>,
    "rpc_path" : <api_jsonrpc.php へのパス>,
    "zbx_version" : <対象 Zabbix のバージョン>
}
```


例：

```json
{
    "zbx_host" : "zabbix-svr",
    "zbx_login_user" : "Admin",
    "zbx_login_password" : "zabbix",
    "rpc_path" : "http://zabbix-svr/zabbix/api_jsonrpc.php",
    "zbx_version" : 3.0.28
}

```

## 実行方法
以下コマンドを実行する

```
php get_maps.php <コンフィグファイル>
```
