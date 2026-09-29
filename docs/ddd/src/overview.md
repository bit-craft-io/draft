## 概要

- **GitHub:** https://github.com/bit-craft-io/nukui-ddd

上記のGitHubは<br />
実務で得た経験を基に、設計思想を整理・発展させたリポジトリです。<br />
再利用性と責務分離をLaravelとドメイン駆動設計(DDD)で実現してます。<br />

その実装内容と設計プロセスを資料化したものです。

---

### 設計思想

- **責務分離**：誰が何を知るべきかを構造で強制する
- **DDD**：ビジネスルールをコードで自然言語化する
- **ユビキタス言語**：設計者と開発者が同じ言葉で話せる状態を作る
---

### レイヤー構成


---

### 主な設計ポイント

- DataSources 経由で ORM を操作し、ドメイン層が DB を意識しない構造
- Entity / ValueObject の集合を Iterator で表現（メモリ効率重視）
- Transaction と Response の制御を Middleware に集約
- ValueProxy で Mutable な状態管理を明示的に分離
---

<div style="page-break-before: always;"></div>

### シーケンス図

<div style="border: 2px solid #333; padding: 15px; width: 100%; box-sizing: border-box; text-align: center;">

```plantuml
@startuml

hide footbox

skinparam sequenceArrowThickness 1.5
skinparam roundcorner 6
skinparam sequenceParticipantBorderThickness 1
skinparam defaultFontSize 14

skinparam BoxPadding 20
skinparam sequenceMessageAlign center

skinparam sequenceLifeLineBorderThickness 1
skinparam sequenceLifeLineBorderColor #black

header " "
footer " "

participant "       Client       \n       (HTTP)       " as Client #E6F1FB
participant " Request / Response \n"                     as Req #FFF5F5
participant "    Middleware      \n"                     as MW #EEEDFE
participant "    Controller      \n"                     as Ctrl #E1F5EE
participant "    Application     \n     (Use case)     " as App #FAECE7
participant "     Repository     \n"                     as Rep #FAECE7
participant "       Entity       \n"                     as Ent #FAECE7
participant "    DataSources     \n     (ORM/Model)    " as DS #F1EFE8

' == HTTPリクエスト受付 ==

Client -> Req : POST /api/...
activate Client
activate Req

Req -> Req : バリデーション実行
Req -> MW : validated request
activate MW
deactivate Req

opt
MW -> MW : Transaction Begin
end

' == コントローラー呼び出し ==

MW -> Ctrl : validated request
activate Ctrl

Ctrl -> App : execute(dto)
activate App

' == リポジトリ生成 ==

group ドメインロジックA

    App -> Rep ** : new Rep()
    activate Rep

    ' == データ取得 → エンティティ生成 ==

    App -> Rep : makeDraft() / find(id)

    Rep -> DS : DataSource::make()->get()
    activate DS
    DS -> DS : Model (ORM) クエリ実行
    DS --> Rep : Model
    deactivate DS

    Rep -> Ent ** : Domain::ent()->init(model)
    activate Ent
    Rep --> App : EntXxx

    ' == ドメインロジック ==

    App -> Ent : ビジネスロジック実行
    App -> Ent : ValueObject / ValueProxy 操作
    Ent --> App : result
    deactivate Ent

    ' == 永続化 ==

    App -> Rep : save(entity)

    Rep -> DS : persist(entity)
    activate DS
    DS --> Rep : saved
    deactivate DS

    Rep --> App : saved
    deactivate Rep

    Rep -[hidden]-> Rep : dummy_space
end


group ドメインロジックB
    note right of App
    別の処理
    end note
    App -[hidden]-> DS
    DS -[hidden]-> DS : Model (ORM) dummy____
    DS -[hidden]-> App
end

group ユースケースA
    note right of App
    Entityを介さずModelを操作
    end note

    App -> Rep
    activate Rep
    Rep -> DS : DataSource::make()->get()
    activate DS
    DS -> DS : Model (ORM) クエリ実行
    DS --> Rep : Model
    deactivate DS
    Rep --> App
    deactivate Rep

    Rep -[hidden]-> Rep : dummy_space
end

' == レスポンス生成 ==

App --> Ctrl : result
deactivate App

Ctrl --> MW : response data
deactivate Ctrl

note right of MW
  Transaction Commit
  (スレーブ参照へ切替: sticky = true)
end note

MW -> Req : JSON生成
activate Req
Req --> Client : JSON 200 OK
deactivate Req

== エラー発生時 ==

alt 例外発生
    note right of MW
    Bootstrapの定義でエラーをキャッチ
    end note
    MW -> MW : 例外キャッチ\n(Core/Exceptions)
    MW -> MW : Transaction Rollback
    MW -> Req : エラーJSON生成
    activate Req
    Req --> Client : エラーレスポンス\n(Exceptions/Enum のコード付き)
    deactivate Req
    
    Rep -[hidden]-> Rep : dummy_space
end

deactivate MW
deactivate Client

@enduml
```
</div>

---

### 使用技術

- Laravel（PHP 8.4）
- Docker（MySQL, Redis）
- AWS（EC2, RDS, Valkey）
