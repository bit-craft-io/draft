# 対応バージョン

PHP と Laravel の対応バージョンと、それに合わせるミドルウェア・パッケージの版を記録する。
確認できないものは「未確認」と書く。

確認日: 2026-11-09(PHP・ミドルウェアのサポート期限は 2026-11-10 に確認)

## 方針

- PHP は **8.5.12 以上**を必須とする。
  - 根拠: GHSA-w8hw-w9fx-j79w(CVE-2026-109618)、GHSA-6wmf-4rv5-p3rp(CVE-2026-109619)
  - php.net の ChangeLog(https://www.php.net/ChangeLog-8.php)で、PHP 8.5.12(2026-10-22)の「Core」の修正として、GHSA-w8hw-w9fx-j79w と GHSA-6wmf-4rv5-p3rp(Avoid state corruption via bailout)が記載されていることを確認した。
  - 同じ2件は、8.4.27、8.3.36、8.2.35 の ChangeLog にも記載されている(8.5 系以外にも、修正済みの版がある)。
  - NVD(https://services.nvd.nist.gov/rest/json/cves/2.0)で、次の対応を確認した(2026-11-10 に取得)。
    - GHSA-w8hw-w9fx-j79w は CVE-2026-109618(CVSS 9.8 CRITICAL、公開 2026-10-21)。メモリ不足(OOM)時の `longjmp()` で、リクエストのメモリが未初期化のまま残り、リモートコード実行につながる。
    - GHSA-6wmf-4rv5-p3rp は CVE-2026-109619(CVSS 8.1 HIGH、公開 2026-10-21)。グローバルな状態が、解放済みのリクエストのメモリを指し続ける。
    - 影響範囲(NVD の構成情報): 8.2 系は 8.2.35 未満、8.3 系は 8.3.36 未満、8.4 系は 8.4.27 未満、8.5 系は 8.5.12 未満。
  - PHP 8.5 系を使う方針は、依頼者の指定による。
- Laravel は **13 系**とする。
- 「解決結果」は、`composer update --dry-run`(PHP 8.5.12 を指定)で依存を解決した結果であり、動作を確認した版ではない。
  `composer.lock` を更新してテストが通ったら、確認済みに書き換える。

## 本体

| 対象 | 版 | 確認元 |
|---|---|---|
| PHP | 8.5.12 以上(必須) | 上記アドバイザリ(依頼者の指定) |
| Laravel(`laravel/framework`) | ^13.0(解決結果: 13.40.0) | composer の依存解決 |

Laravel 13 自体の PHP 要件は 8.3 以上。8.5.12 以上という要件は、それより厳しい上位の制約として扱う。

出典: https://laravel.com/docs/13.x/deployment#server-requirements

## Composer パッケージ(本番)

| 対象 | 現在の制約 | 変更後の制約 | 解決結果 | 備考 |
|---|---|---|---|---|
| php | ^8.4 | ^8.5.12 | - | |
| laravel/framework | ^12.0 | ^13.0 | 13.40.0 | |
| laravel/tinker | ^2.10.1 | ^3.0 | 3.0.2 | 公式のアップグレードガイドで指定。2.x は 13 と共存できない(依存解決で確認) |
| laravel/mcp | ^0.7.1 | ^1.1 | 1.1.2 | 公式のアップグレードガイドに記載なし。0.7.x の 13 対応は未確認。0.x から 1.x のメジャー更新 |
| laravel/sanctum | ^4.0 | ^4.0(変更なし) | 4.3.3 | |
| google/protobuf | ^4.33 | ^4.33(変更なし) | 4.33.6 | |
| hidehalo/nanoid-php | ^2.0 | ^2.0(変更なし) | 2.0 | |

## Composer パッケージ(開発)

| 対象 | 現在の制約 | 変更後の制約 | 解決結果 | 備考 |
|---|---|---|---|---|
| phpunit/phpunit | ^11.5 | ^12.0 | 12.5.43 | 公式のアップグレードガイドで指定 |
| phpstan/phpstan | ^2.2 | ^2.2(変更なし) | 2.3.5 | |
| friendsofphp/php-cs-fixer | ^3.95 | ^3.95(変更なし) | 3.95.29 | |
| laravel/pail | ^1.2.2 | ^1.2.2(変更なし) | 1.2.7 | |
| laravel/sail | ^1.41 | ^1.41(変更なし) | 1.69.0 | |
| nunomaduro/collision | ^8.6 | ^8.6(変更なし) | 8.9.5 | |
| mockery/mockery | ^1.6 | ^1.6(変更なし) | 1.6.15 | |
| fakerphp/faker | ^1.23 | ^1.23(変更なし) | 1.24.1 | |

出典: https://laravel.com/docs/13.x/upgrade#updating-dependencies (上記の `laravel/framework`・`laravel/tinker`・`phpunit/phpunit` の指定)

## ミドルウェア(Docker)

| 対象 | 設定 | Laravel 13 の公式要件 | 判定 |
|---|---|---|---|
| MySQL | **8.4**(master / slave)。旧 8.0 | MySQL 5.7 以上 | Laravel の要件を満たす。8.4(LTS)の EOL は 2032-04-30。旧 8.0 は、EOL 2026-04-30 で終了済みだったため更新した |
| Redis | **7.2**。旧 7.0 | Redis サーバーの最低版の記載なし | 7.2 の EOL は 2029-12-01。旧 7.0 は、EOL 2024-07-29 で終了済みだったため更新した |
| nginx | タグ指定なし(`latest`) | 記載なし | 版が固定されていない |
| Node(redoc 用) | **24-alpine**。旧 20-alpine | 記載なし(Laravel とは無関係) | 24 の EOL は 2028-04-30。旧 20 は、EOL 2026-04-30 で終了済みだったため更新した |

出典:
- https://endoflife.date/api/mysql.json、https://endoflife.date/api/redis.json、https://endoflife.date/api/nodejs.json (サポート期限。2026-11-10 に取得)
- https://laravel.com/docs/13.x/database (サポートする DB と最低版)
- https://laravel.com/docs/13.x/redis (クライアントの記載のみで、サーバー版の記載なし)

### 実際のコンテナで確認した内容(2026-11-10)

| 対象 | 確認 | 結果 |
|---|---|---|
| MySQL 8.4.13 + **旧設定のまま** | master を起動 | **失敗**: `unknown variable 'default_authentication_plugin=mysql_native_password'`(8.4 で削除されたオプション) |
| MySQL 8.4.13 + `mysql_native_password = ON` | master と slave を起動 | master は起動。slave の初期化が**失敗**: `CHANGE MASTER TO` が構文エラー(8.4 で削除された構文) |
| MySQL 8.4.13 + **見直した設定** | master と slave を起動し、レプリケーションを確認 | **成功**: `Replica_IO_Running: Yes`、`Replica_SQL_Running: Yes`、エラーなし。master に書いた行が slave に複製された |
| MySQL 8.4.13(`caching_sha2_password`) | PHP の `pdo_mysql`(PHP 8.3.6)から接続 | **成功** |
| Redis 7.2.17 | 起動、`PING`、`SET`・`GET` | 成功 |
| redoc(Node 24) | `@redocly/cli` の対応する Node(npm の `engines`: `>=22.12.0 \|\| >=20.19.0 <21.0.0`) | 24 は条件を満たす。**コンテナ内でのビルド・実行は未確認**(このセッションのコンテナは、外部に接続できない) |

### MySQL 8.4 で変えた点

- `default_authentication_plugin`(`master.cnf`、`slave.cnf`)を削除した。8.4 で削除されたオプション。
- `IDENTIFIED WITH mysql_native_password` を外し、既定の `caching_sha2_password` にした(`init_master.sql`の `guest`、`replica`)。8.4 では `mysql_native_password` が、既定で無効。
- `CHANGE MASTER TO` / `START SLAVE` を、`CHANGE REPLICATION SOURCE TO` / `START REPLICA` に変えた(`init_slave.sql`)。`caching_sha2_password` のレプリケーション用ユーザーが、暗号化なしの接続で認証できるよう、`GET_SOURCE_PUBLIC_KEY=1` を付けた。
- `guest` など、`mysql_native_password` を前提とする外部クライアント(古い DB クライアント)は、接続できない可能性がある(未確認)。
- 既存のボリューム(8.0 のデータ)を、8.4 でそのまま起動できるかは、未確認。作り直す(`docker compose down -v`)ことを勧める。

### Redis クライアント

- 公式は PhpRedis(PECL の `redis` 拡張)を既定とし、導入できないときは `predis/predis` を使う。
- このプロジェクトは `pecl install redis` で PhpRedis を入れている。PECL の版は固定されていない。

## PHP 拡張(Docker で導入)

| 拡張 | 導入方法 | 版 | PHP 8.5.12 対応 |
|---|---|---|---|
| zip、pdo_mysql、intl、bcmath | docker-php-ext-install | PHP 本体に同梱 | PHP の版に従う |
| apcu、redis、xhprof、xdebug | pecl install | 指定なし | 未確認(PECL の情報を取得できなかった) |

## 現状との差分

この記録は、移行後の版を示す。リポジトリの実体は、次のとおり。

| 対象 | 現在 | 移行後 | 状態 |
|---|---|---|---|
| `composer.json` | `php` ^8.5.12、`laravel/framework` ^13.0、`laravel/tinker` ^3.0、`laravel/mcp` ^1.1、`phpunit/phpunit` ^12.0 | 同左 | **反映済み** |
| `composer.lock` | Laravel 13.40.0 ほか、上の版で再生成 | 同左 | **反映済み** |
| Laravel 13 での起動 | PHP 8.3.6 + Symfony 7.4 の scratch 環境で、`artisan route:list`(25 ルート。MCP・Sanctum を含む)が成功 | 同左 | 確認済み(PHP 8.5.12 は未確認)。既存の `ExampleTest` は、`/` が401で失敗する(例外描画の設計によるもので、Laravel 13 に起因しない) |
| `docker/container/mysql/*/Dockerfile`、設定 | `mysql:8.4`(設定を見直し済み) | 同左 | **反映済み**(実際のコンテナで確認) |
| `docker/docker-compose.yml` の Redis | `redis:7.2` | 同左 | **反映済み**(起動を確認) |
| `docker/container/redoc/Dockerfile` | `node:24-alpine` | 同左 | **反映済み**(ビルドは未確認) |
| `docker/container/php/Dockerfile` | `php:8.4-fpm` | `php:8.5.12-fpm` | 未更新 |
| `docker/container/job/Dockerfile` | `php:8.4` | `php:8.5.12` | 未更新 |
| `README.md` の使用技術 | Laravel(PHP 8.4) | PHP 8.5 | 未更新 |

- `composer.lock` は、PHP 8.3 の環境で、`--ignore-platform-req=php` を付けて再生成した。`platform` には `php: ^8.5.12` が記録されている。
- `composer update` は、依存全体を更新した。Laravel 13 に合わせた変更以外にも、推移的な依存のメジャー更新が含まれる(例: `guzzlehttp/guzzle` 7.10.0 → 8.3.1、`guzzlehttp/promises` 2.3.0 → 3.1.0、`brick/math` 0.14.1 → 1.0.0)。影響は未確認。

## Laravel 12 から 13 への移行で影響を確認する箇所

公式のアップグレードガイド(https://laravel.com/docs/13.x/upgrade)のうち、このプロジェクトに関係しそうなもの。実際の影響は未確認。

| 項目 | 影響度(公式) | このプロジェクトの該当箇所 |
|---|---|---|
| CSRF ミドルウェアの改名(`VerifyCsrfToken` / `ValidateCsrfToken` を `PreventRequestForgery` へ) | 高 | `config/sanctum.php` が `ValidateCsrfToken` を参照している。旧名は非推奨の別名として残る |
| キャッシュ・Redis のキー接頭辞(ハイフン区切りへ) | 低 | `CACHE_PREFIX` / `REDIS_PREFIX` / `SESSION_COOKIE` を明示しない場合、接頭辞が変わる |
| キャッシュの `serializable_classes` 設定 | 中 | キャッシュにオブジェクトを入れている場合、許可するクラスの明示が必要。`StfStaCache` の使い方は未確認 |
| MySQL の `upsert` の検証 | 中 | `uniqueBy` が空だと例外になる |
| MySQL の `DELETE` ... `JOIN` / `ORDER BY` / `LIMIT` | 低 | 使用箇所は未確認 |
