# Readme.md

## ■ 本ツールで実現したいこと
Zabbix のマップ情報を API で取得して JSON 形式で表示する。

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
    "zbx_version" : "3.0.28"
}

```

## 実行方法
以下コマンドを実行する

```
php get_maps.php <コンフィグファイル>
```

## 実行例

```
[root@worker zabbix_get_map_info_by_api]# php get_maps.php config.json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "sysmapid": "1",
            "name": "Local network",
            "width": "680",
            "height": "200",
            "backgroundid": "0",
            "label_type": "0",
            "label_location": "0",
            "highlight": "1",
            "expandproblem": "1",
            "markelements": "1",
            "show_unack": "0",
            "grid_size": "50",
            "grid_show": "1",
            "grid_align": "1",
            "label_format": "0",
            "label_type_host": "2",
            "label_type_hostgroup": "2",
            "label_type_trigger": "2",
            "label_type_map": "2",
            "label_type_image": "2",
            "label_string_host": "",
            "label_string_hostgroup": "",
            "label_string_trigger": "",
            "label_string_map": "",
            "label_string_image": "",
            "iconmapid": "0",
            "expand_macros": "1",
            "severity_min": "0",
            "userid": "1",
            "private": "0",
            "show_suppressed": "0"
        }
    ],
    "id": 1
}
[root@worker zabbix_get_map_info_by_api]#
```
