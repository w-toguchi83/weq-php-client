# Weq Client

[Weq(JSON API)サーバ](https://github.com/w-toguchi83/weq) と通信するクライアントのPHP実装。

## 導入方法

`composer.json` の `repositories` に追加します。

```
// 例
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/w-toguchi83/weq-php-client.git"
    }
]
```

`composer.json` の `require` に追加します。

```
// 例
"resuire" : [
    "weq-lib/weq-client": "^0.0.0"
}
```

`composer` コマンドからインストールします。

```
$ composer install weq-lib/weq-client
```


## 機能

`Weqサーバ` が提供するAPIを手軽に呼び出して結果を受け取ることができます。   
API通信失敗時に設定によって、内部的にリトライする機能があります。


### インスタンス生成方法

```
<?php

use WeqClient\Config;
use WeqClient\Client;

// 情報を設定した `Config` インスタンスを生成します。
$host   = 'sample.weq.com';
$config = Config::cerate($host);

// セキュア通信設定、タイムアウト(秒)設定、通信失敗時のリトライ回数、リトライ時の待機時間(ミリ秒)を設定できます。
$config->setSecure(false)
       ->setTimeout(10)
       ->setRetryCount(5)
       ->setWaitTime(100);

// Weaサーバと通信するクライアントを生成します。
$weqClient = Client::create($config);
```


### 疎通確認機能

`Weqサーバ` との疎通確認をする機能です。

```
if ($weqClient->ping() === false) {  // return bool
    // Weqサーバと疎通ができない!!
}
```

### 問い合わせ結果取得機能

#### 全件結果取得

クエリの結果を全件取得できる機能です。

```
$resource = 'salesdb';
$query = 'SELECT * FROM sales WHERE sales_date >= :sales_begin AND sales_date <= :sales_end';
$binds = [
    'sales_begin' => '2019-07-01 10:00:00',
    'sales_end'   => '2019-07-01 12:00:00',
];

// `fetch` メソッドの戻り値は `Iterator` です。内部的にはメモリ節約のために `yield` で返されています。
foreach ($weqClient->fetch($resource, $query, $binds) as $row) {
    // データに対する何らかの処理
    // ...
}
```

#### 範囲結果取得

クエリの結果を範囲指定で取得できる機能です。   
指定できるリミット値の条件は `1000` です。

```
$resource = 'salesdb';
$query  = 'SELECT * FROM sales WHERE sales_date >= :sales_begin AND sales_date <= :sales_end';
$offset = 0;
$limit  = 100;
$binds  = [
    'sales_begin' => '2019-07-01 10:00:00',
    'sales_end'   => '2019-07-01 12:00:00',
];

// 配列が返ります。
$rows = $weqClient->fetchRange($resource, $query, $offset, $limit, $binds);  // return array
```


#### 件数取得

クエリの結果件数を取得できる機能です。

```
$resource = 'salesdb';
$query = 'SELECT * FROM sales WHERE sales_date >= :sales_begin AND sales_date <= :sales_end';
$binds = [
    'sales_begin' => '2019-07-01 10:00:00',
    'sales_end'   => '2019-07-01 12:00:00',
];

$count = $weqClient->count($resource, $query, $binds);  // return int

```


## 動作確認

`docker`, `docker-compose` が必要です。

`kick.sh` を用意しているので、このシェル経由で単体テストの動作を確認できます。

```
# `build` は初回のみ実行(またはコンテナイメージを作り直したいとき)
$ sh kick.sh test_build
$sh kick.sh test
Creating network "weq-php-client_default" with the default driver
Creating weq-php-client_weqdb1_1 ... done
Creating weq-php-client_weqdb2_1 ... done
weq-php-client_weq_run_cb80f23a33ab
wait for stating database
Starting weq-php-client_weqdb2_1 ... done
Starting weq-php-client_weqdb1_1 ... done
Creating weq-php-client_weq_1    ... done
PHPUnit 7.5.14 by Sebastian Bergmann and contributors.

.............                                                     13 / 13 (100%)

Time: 40.92 seconds, Memory: 4.00 MB

OK (13 tests, 26 assertions)
Stopping weq-php-client_weq_1                ... done
Stopping weq-php-client_weq_run_cb80f23a33ab ... done
Stopping weq-php-client_weqdb2_1             ... done
Stopping weq-php-client_weqdb1_1             ... done
Removing weq-php-client_phpunit_run_c30c14b8c41e ... done
Removing weq-php-client_weq_1                    ... done
Removing weq-php-client_weq_run_cb80f23a33ab     ... done
Removing weq-php-client_weqdb2_1                 ... done
Removing weq-php-client_weqdb1_1                 ... done
Removing network weq-php-client_default
```


また `PHPStan` の実行結果も `kick.sh` から確認できます。

```
$ sh kick.sh phpstan 7
 6/6 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 [OK] No errors

```
