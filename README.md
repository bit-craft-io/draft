## 概要
- 実務で得た経験を基に、設計思想を整理・発展させたリポジトリです。<br />
  再利用性と責務分離をLaravelとドメイン駆動設計(DDD)で実現してます。
- This repository is a part of my professional portfolio, <br>
  demonstrating my learning process and technical skills in software development.

## ディレクトリ構成
```
app/                           # アプリケーション
--------------------------------------------------------------------------------
├── Core/                      # システム基盤
│ ├── DataSources/             # インフラ層の共通処理
│ ├── Domains/                 # ドメイン層の共通処理
│ ├── Exceptions/              # 例外処理
│ │ ├── Enum/                  # 例外コード
│ │ └── (Except)/              # 各例外クラス
│ ├── Http/                    # HTTPレイヤーの共通処理
│ │ ├── Middlewares/           # ミドルウェア
│ │ └── ...                    # 共通処理（Applications, Controllers, Requests, Responses）
│ └── Libraries/               # 共通ユーティリティ
│ │ ├── Stateful/              # 静的クラス（ステートフル）
│ │ ├── Stateless/             # 静的クラス（ステートレス）
│ │ └── Traits/                # 静的クラスを扱うトレイト（use して静的メソッドを実行）
--------------------------------------------------------------------------------
├── DataSources/               # インフラ層（データソース）
├── Domains/                   # ドメイン層
│ └── (Rep, Ent, Vo, Vp, Svc)/ # 各ドメインクラス
├── Http                       # API
│ ├── Applications/            # ユースケース
│ ├── Controllers/             # プレゼンテーション層
│ ├── Requests/                # バリデーション定義
│ └── Responses/               # APIレスポンス
├── Mcp                        # （DDDとは関連なし）AI通信用窓口
├── Models                     # インフラ層（モデル）
│ ├── Enum/                    # インフラ層（タイプ）
│ └── (Model)/                 # 各モデルクラス
├── Providers                  # サービスプロバイダ
--------------------------------------------------------------------------------
docs/                          # ドキュメント
├── ddd/                       # 本リポジトリのDDD概要
├── apidog/                    # （DDDとは関連なし）ApiDog
│ ├── import                   # （DDDとは関連なし）インポートデータ
│ ├── .env                     # （DDDとは関連なし）仕様書を出力するプログラムの環境変数
│ ├── build.sh                 # （DDDとは関連なし）仕様書を出力するプログラム 
│ └── layout.hbs               # （DDDとは関連なし）出力時のレイアウト
├── xdebug/                    # （DDDとは関連なし）Xdebug
│ ├── src                      # （DDDとは関連なし）Xdebug設定の資料
│ └── dist                     # （DDDとは関連なし）Xdebug設定の資料（PDF）
sandbox/                       # （DDDとは関連なし）検証・使い捨てコード置き場
├── grpc/                      # （DDDとは関連なし）gRPC疎通確認用
Makefile                       # （DDDとは関連なし）プロジェクト共通コマンド
```

## 設計思想
- コードは「責務」で整理
- ドメイン層はビジネスルールを自然言語化して構築（ユビキタス言語）
- システム基盤は抽象構造で設計
- 他人が見て「なるべく迷わない」命名を心がける

## 特徴・ポイント
1. **DataSources を経由して ORM(Model) を操作**
    - インフラ層の責務を DataSources に集約
    - ドメイン層は DB や ORM の存在を意識せず、ビジネスロジックに専念できる
2. **Entity の集合を Iterator で表現**
    - Collection クラスを使わず、Iterator による軽量な疑似 Collection として管理
    - メモリ効率を意識した設計
3. **ValueObject の集合を Iterator で表現**
    - Collection クラスを使わず、Iterator による軽量な疑似 Collection として管理
    - メモリ効率を意識した設計
4. **ValueProxy は Mutable な ValueObject**
    - ValueObject が Immutable に対し、内部状態を更新可能にしたクラス
5. **Transaction の commit や rollback は Middleware で制御**
   - 都度、try-catch を記述せず Middleware で処理
6. **API の Response は Middleware で制御**
   - 共通もしくは個別のレスポンスの型で返す<br />
     ※トランザクションの処理後はスレーブ参照にする為<br />
     　config.database.connections.{db}.sticky = true

## 使用技術
- Laravel(PHP 8.5)
- Docker(MySQL, Redis)
- AWS(EC2, RDS, Valkey)

## その他
- コミットログの内容は割愛してます
