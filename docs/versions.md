# 対応バージョン

kind・Agones・OM2(Open Match 2)と、Go・Protobuf・gRPC の対応バージョンを、公式の情報から記録する。
公式で確認できないものは「未確認」と書く。

確認日: 2026-11-02(Go・Protobuf・gRPC は 2026-11-04)

## 一覧

| 対象 | 確認したバージョン | 対応する Kubernetes | 確認元(公式) |
|---|---|---|---|
| Agones | 1.61.0(最新リリース) | 1.34, 1.35, 1.36 | https://agones.dev/site/docs/installation/ / https://github.com/agones-dev/agones/releases/tag/v1.61.0 |
| kind | v0.33.0(最新リリース) | ノードイメージ: v1.37.0(既定), v1.36.4, v1.35.8, v1.34.11 | https://github.com/kubernetes-sigs/kind/releases/tag/v0.33.0 |
| Helm | 4.1.3 以上(Agones 1.61.0 の Helm 手順の前提) | 未確認 | https://github.com/agones-dev/agones/blob/v1.61.0/site/content/en/docs/Installation/Install%20Agones/helm.md(Prerequisites) / https://helm.sh/docs/intro/install/ |
| OM2(Open Match 2) | v2.0.2(最新タグ。GitHub Releases は無し) | 未確認 | https://github.com/googleforgames/open-match2/tree/v2.0.2 / https://github.com/googleforgames/open-match2/blob/v2.0.2/go.mod |
| Go | 1.27.3(最新の安定版) | - | https://go.dev/dl/ |
| protoc | 27.2(OM2 v2.0.2 の生成に使われた版) | - | https://github.com/protocolbuffers/protobuf/releases/tag/v27.2 / https://github.com/googleforgames/open-match2/blob/v2.0.2/proto/Dockerfile |
| protoc-gen-go | v1.34.2(OM2 v2.0.2 の生成に使われた版) | - | https://github.com/googleforgames/open-match2/blob/v2.0.2/pkg/pb/api.pb.go(先頭の versions) |
| protoc-gen-go-grpc | v1.4.0(OM2 v2.0.2 の生成に使われた版) | - | https://github.com/googleforgames/open-match2/blob/v2.0.2/pkg/pb/api_grpc.pb.go(先頭の versions) |
| gRPC-Go(`google.golang.org/grpc`) | v1.65.0(OM2 v2.0.2 の `go.mod` と同じ) | - | https://github.com/googleforgames/open-match2/blob/v2.0.2/go.mod |
| Protobuf Go(`google.golang.org/protobuf`) | v1.34.2(OM2 v2.0.2 の `go.mod` と同じ) | - | https://github.com/googleforgames/open-match2/blob/v2.0.2/go.mod |

## 補足

### Agones

- 公式の「Agones and Kubernetes Supported Versions」の表で、Agones 1.61 は Kubernetes 1.34, 1.35, 1.36 に対応する。
- 公式では、この 3 バージョンは GKE でテストしたもので、他のバージョンは未サポートとされている。
- 公式でサポートが明記されている環境は GKE・AKS・EKS・Minikube。kind 上での動作は未確認。

### Helm

- Agones 1.61.0 の Helm 手順は、前提を「Helm package manager 4.1.3+」としている。Agones は Helm チャート `agones/agones` の 1.61.0 を `--version 1.61.0` で固定して入れる(手順は [phase1/README.md](../phase1/README.md))。
- Devin の VM では Helm v4.3.1 で導入できることを確認した。

### kind

- v0.33.0 の既定のノードイメージは `kindest/node:v1.37.0`。これは Agones 1.61 の対応範囲(1.34〜1.36)の外にある。
- Agones の対応範囲に入るノードイメージは v1.36.4, v1.35.8, v1.34.11。公式は `@sha256` のダイジェスト指定で使うよう求めている。
- ダイジェストは、v0.33.0 のリリースノートに記載の値。

| ノードイメージ | ダイジェスト |
|---|---|
| `kindest/node:v1.37.0`(既定) | `sha256:a1ed56cfb0e7b93589bdf97c8cd566405a265939e3620fc4f5de89adff580ae5` |
| `kindest/node:v1.36.4` | `sha256:099e049362a1526b2db71494e1947aae99bd16290d7c895f2b7ea312e3cbfaed` |
| `kindest/node:v1.35.8` | `sha256:07b2536e30b803ed61d1677a79df6115f798ce64c80f9e22f6ed45afd09323c0` |
| `kindest/node:v1.34.11` | `sha256:44e222ee2132dab25ff87301682f89eb82c7880ea3a1bf543bfe9708fd08d67d` |

### OM2(Open Match 2)

- 公式 README では、public preview とされている。
- 対応する Kubernetes のバージョンは、公式で確認できないため未確認。
- タグ v2.0.2 のデプロイ例は Cloud Run のみ(https://github.com/googleforgames/open-match2/tree/v2.0.2/deploy)。GKE の例(`deploy/gke/`)は main ブランチにだけある。
- タグ v2.0.2 のデプロイ例(`deploy/service.yaml`)では `OM_REDIS_WRITE_HOST` / `OM_REDIS_READ_HOST` を設定している。`docs/DEVELOPMENT.md` も、デプロイの前提に Redis インスタンスを挙げている。Redis の要否と構成は、[phase2/README.md](../phase2/README.md#redis-の要否と構成om2-公式で確認した結果) を参照(公式の README には Redis の記載が無い)。
- `go.mod` の Go のバージョンは、タグ v2.0.2 では `go 1.22.5`(main ブランチでは `go 1.24.6`)。

### Go・Protobuf・gRPC(phase2)

- phase2 の CLI(OM2 を呼ぶ側)で使う。手順は [phase2/README.md](../phase2/README.md)。
- protoc・protoc-gen-go・protoc-gen-go-grpc は、OM2 v2.0.2 が公開している生成済みのコード(`pkg/pb/`)の先頭に書かれた版に合わせた。生成済みのコードの先頭の `protoc v5.27.2` は、OM2 の `proto/Dockerfile` の `PROTOC_VER=27.2` と合う(Devin の VM で protoc 27.2 を使って生成すると、先頭に `protoc v5.27.2` と書かれた)。
- gRPC-Go と Protobuf Go は、OM2 v2.0.2 の `go.mod` と同じ版にした。
- Go は、OM2 v2.0.2 の `go.mod` では `go 1.22.5` だが、phase2 では確認日の最新の安定版(1.27.3)を使う。Devin の VM では、Go 1.27.3 で上の版の gRPC-Go・Protobuf Go を使ってビルドとテストが通ることを確認した。

### phase2(OM2 を kind で動かす)で使うもの

確認日: 2026-11-04。手順は [phase2/README.md](../phase2/README.md)。Go のモジュールの版(open-match2 v2.0.2 など)は [phase2/go.mod](../phase2/go.mod) にある。

| 対象 | 使うもの | 確認元(公式) |
|---|---|---|
| om-core のイメージ | `us-docker.pkg.dev/open-match-public-images/open-match2/om-core@sha256:57003077dae638801826138332f5793364fe13995de78b4475c8951e17e37e54`(タグ `latest` のダイジェスト。タグは `latest` しか無い) | https://github.com/googleforgames/open-match2/blob/ac25040aa27e7746545db02a63fc22535842f83a/deploy/gke/om.yaml |
| Redis のイメージ | `redis:8.10.3@sha256:de1068af6f552240dfab67bcc9bfef99bfc60f776bb4580ef077ea4c8c97e848`(2026-11-04 のタグ `latest` と同じダイジェスト。イメージの `REDIS_VERSION` は 8.10.3。タグは同じ版のまま作り直されうるため、ダイジェストも付けた) | https://github.com/googleforgames/open-match2/blob/ac25040aa27e7746545db02a63fc22535842f83a/deploy/gke/redis.yaml |
| MMF のサンプル | open-match-ecosystem v2.0.1 の `v2/mmf/` | https://github.com/googleforgames/open-match-ecosystem/tree/v2.0.1/v2/mmf |
| pack | v0.40.10 | https://github.com/buildpacks/pack/releases/tag/v0.40.10 |
| MMF のイメージのビルダー | `gcr.io/buildpacks/builder:google-22`、Go は `GOOGLE_GO_VERSION=1.27.3` で固定 | https://cloud.google.com/docs/buildpacks/go |

- om-core のイメージが、タグ v2.0.2 のソースから作られたかは未確認。`go.mod` の open-match2 v2.0.2 の API とずれる可能性がある。Devin の VM では、v2.0.2 の生成コードの型で `CreateTicket`・`ActivateTickets`・`InvokeMatchmakingFunctions` を呼べた。
- OM2 が対応する Redis のバージョンは、公式で確認できないため未確認。

## 組み合わせ

- kind(v0.33.0)で Agones(1.61)を使う場合、ノードイメージは v1.34.11 / v1.35.8 / v1.36.4 のいずれかが、Agones の対応範囲に入る。ただし、kind 上での Agones の動作は公式では未確認。
- OM2 と Agones、OM2 と kind の対応関係は、公式で確認できないため未確認。
