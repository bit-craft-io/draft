# 対応バージョン

PHP と Laravel の対応バージョンと、それに合わせるミドルウェア・パッケージの版を記録する。
確認できないものは「未確認」と書く。

確認日: 2026-11-09

## 方針

- PHP は **8.5.12 以上**を必須とする。
  - 根拠: GHSA-w8hw-w9fx-j79w(CVE-2026-109618)、GHSA-6wmf-4rv5-p3rp(CVE-2026-109619)
  - 上記アドバイザリの原文は、この記録の作成時に参照できなかった(未確認)。要件は依頼者の指定による。
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

| 対象 | 現在の設定 | Laravel 13 の公式要件 | 判定 |
|---|---|---|---|
| MySQL | 8.0(master / slave) | MySQL 5.7 以上 | 要件を満たす。MySQL 8.0 のサポート期限は未確認 |
| Redis | 7.0 | Redis サーバーの最低版の記載なし | 未確認 |
| nginx | タグ指定なし(`latest`) | 記載なし | 版が固定されていない |
| Node(redoc 用) | 20-alpine | 記載なし | Laravel とは無関係(`docker/container/redoc/Dockerfile`) |

出典:
- https://laravel.com/docs/13.x/database (サポートする DB と最低版)
- https://laravel.com/docs/13.x/redis (クライアントの記載のみで、サーバー版の記載なし)

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
| `composer.json` | `php` ^8.5.12、`laravel/framework` ^13.0、`laravel/tinker` ^3.0、`laravel/mcp` ^1.1、`phpunit/phpunit` ^12.0 | 同左 | **反映済み**(動作は未確認) |
| `composer.lock` | Laravel 13.40.0 ほか、上の版で再生成(`composer update --no-install`) | 同左 | **反映済み**(インストール・テストは未実施) |
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
