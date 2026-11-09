# CLAUDE.md

このファイルは、このリポジトリで作業する AI への指針を定める。

## 境界

「境界」と「共通の方針」の2節は、Claude Code と Devin の両方に適用する。それ以外の節と `.claude/` 配下は、Claude Code 専用とする。

`devin/` は、Devin に設定しているルール・スキル・スクリプトの元データ(正)を置く場所とする。Devin への反映は手作業で行い、記録は `devin/README.md` に残す。Claude Code 用の写しは `.claude/skills/issue-to-pr/` に別に持ち(重複を許す)、元との対応は `devin/README.md` の表に書く。`.claude/` の中では、`devin/` を参照しない。

## 共通の方針

- 使用言語は Go とする。
- 基本の段階(ロードマップの P0〜P3)では公式の型(公式の手順・サンプル)を優先し、独自設計をしない。P4 以降は、基本の結果を見て、issue ごとに決める。
- 公式で確認できない仕様は断定せず、「未確認」と書いて報告する。
- `terraform apply` と `terraform destroy` は人が実行する。AI は `terraform plan` までとする。
- 対応バージョンは [docs/versions.md](docs/versions.md) に記録する。
- Go のコードを追加したときは、ビルド・lint・テストのコマンド(単一テストの実行方法を含む)と、複数ファイルにまたがる構成を、このファイルに追記する。
- 認証情報(API キー、パスワード、秘密鍵、`.env` の実値)を、リポジトリ、issue、PR、ログ、プロンプトに入れない。必要なときは、人が別の手段で渡す。入れてしまった場合は、作業を止めて報告する。
- 本番環境を変える操作(データの削除、インフラの破壊的な変更を含む)は、人の確認なしに実行しない。
- 次の操作は、実行前に作業を止めて、人に確認する(PR のコメントか Slack で、対象のサービス・呼び出す回数の見込み・理由を示す)。
  - 課金が発生する API の呼び出し
  - 1 回の作業で、同じ API を 20 回以上呼ぶ操作
  - サービスの利用規約が禁じる、または制限する操作(スクレイピング、レート制限の回避など)
  - なお、パッケージの取得、`git clone`、`docker pull`、GitHub の通常の操作は、確認なしで行ってよい。

## Go のコマンドと構成

「共通の方針」の Go のコードの追記先。Claude Code と Devin の両方に適用する。

- Go のモジュールは `phase2/` と `phase3/`(それぞれ `github.com/bit-craft-io/aidd-practice-v1/phase2`・`github.com/bit-craft-io/aidd-practice-v1/phase3`)。コマンドは、対象のモジュールのディレクトリ(`phase2/` または `phase3/`)で実行する。ビルド・lint・テストのコマンドは、どちらのモジュールでも同じ(下記)。
- 生成: `./gen.sh`(OM2 の公式 proto を `proto/` に取得し、`pb/` を生成する。`proto/` はリポジトリに入れない。protoc とプラグインの版、proto の sha256 が違うときは生成せずに止まる)
- ビルド: `go build ./...`
- lint: `go vet ./... && test -z "$(gofmt -l .)"`(未整形のファイルがあると終了コードが 0 以外になる)
- テスト: `go test ./...`
- 単一テスト: `go test ./match -run '^TestInvoke$'`、`go test ./cmd/create-ticket -run '^TestCreateTicket$'`
- 構成:
  - `phase2/mmf/`: マッチメイキング関数(MMF)。`main.go` が `server.StartServer` で gRPC サーバーを起動し、`functions/soloduel` の `Run` を登録する。`server.GetChunkedRequest` が、om-core から分割して届くプロファイルを組み立てる。
  - `phase2/match/`: 確認用のプログラム。om-core の HTTP の窓口に、OM2 の生成コード(`github.com/googleforgames/open-match2/v2/pkg/pb`)の型を JSON にして送る。
  - `phase2/pb/`: `gen.sh` で OM2 の公式 proto から生成したコード。手で編集しない。
  - `phase2/cmd/create-ticket/`: `phase2/pb` の `OpenMatchServiceClient` で、om-core の gRPC の `CreateTicket` を呼ぶ CLI。
  - `phase2/pb` と `github.com/googleforgames/open-match2/v2/pkg/pb` は、同じ proto から生成した別のパッケージ。1 つのプログラムで両方を import しない(同じ proto を 2 回登録することになる)。
  - 手順は [phase2/README.md](phase2/README.md)。
- phase3(モジュール `phase3/`。コマンドは `phase3/` で実行する):
  - 生成(`gen.sh`)は無い。OM2 の API は公式モジュールの生成コード `github.com/googleforgames/open-match2/v2/pkg/pb` を直接使う(phase2 の `match`・`mmf` と同じ)。
  - 単一テスト: `go test ./cmd/match-client -run '^TestSameAddressForBothClients$'`
  - `phase3/cmd/match-client/`: 動作確認用のクライアント代役 CLI(`match-client`)。`main.go` が全体の流れ、`om.go` が om-core の HTTP 窓口の呼び出し、`agones.go` が `kubectl` 経由の `GameServerAllocation`。OM2 と Agones の呼び出しはインターフェースにして、テストで差し替える。
  - Agones の Go モジュール(`agones.dev/agones`)は import しない(gRPC・Protobuf の版が OM2 の pin より上がるため)。GameServerAllocation は `kubectl create -f` で投入する。
  - 手順は [phase3/README.md](phase3/README.md)。

## Claude Code 専用

- Git / GitHub の運用は `.claude/rules/git.md` に従う。
- Git / GitHub の操作は `/git` スキル(`.claude/skills/git/`)で行う。
