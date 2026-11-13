# devin/

Devin に設定しているルール・スキル・スクリプトの、元データ(正)を置く。

## 構成

- `rules/rule-*.md`: Devin の Customize → ルール。1ファイルが1ルール。先頭の「説明」「トリガー」は、Devin の設定欄に対応する。「内容」が本文
- `skills/issue-to-pr/SKILL.md`: Devin の Customize → スキル
- `scripts/run-summary.sh`: Devin の blueprint から `/usr/local/bin/run-summary` に入れるスクリプト(`rule-token-output` が使う)

## 管理のしかた

- 変更は、このディレクトリを先に直し、PR でレビューしてから、Devin の設定へ反映する
- 反映は手作業(自動では同期されない)。反映したら、下の表に、日付を書く
- Devin に設定するものは、ここに集める。`.claude/` は、Devin を参照しない(疎結合)。Claude Code が使う分は、`.claude/skills/issue-to-pr/` に、写しを別に持つ(重複を許す)
- Devin のモデルは、スキルやルールでは決めない。Devin の設定(Settings → Devin → セッションエージェント → デフォルトエージェント = Fusion)で決める
- 左の元を直したら、下の対応表の右の写しも直す

## Claude Code 用の写しとの対応

| 元(この `devin/`) | Claude Code 用の写し |
|---|---|
| `skills/issue-to-pr/SKILL.md`、`rules/rule-issue-start.md`、`rules/rule-branch-create.md` | `.claude/skills/issue-to-pr/SKILL.md` |
| `rules/rule-pr-create.md` | `.claude/skills/issue-to-pr/pr-body.md` |
| `rules/rule-token-output.md` | `.claude/skills/issue-to-pr/output.md` |
| `scripts/run-summary.sh` | `.claude/skills/issue-to-pr/scripts/run-summary.sh`(同一) |
| `rules/rule-base.md`、`rules/rule-terraform.md` | 写さない(`CLAUDE.md` の「共通の方針」に同じ内容がある) |
| `rules/rule-pr-close.md`、`rules/rule-token-session.md` | 写さない(Devin 専用) |

## PR で Devin を呼ぶ

Devin が作った PR(追跡中の PR)では、コメントの先頭に `@devin-ai-integration`、`/devin `、`Devin` のいずれかを書くと、Devin が応答する。

| 書き方 | 結果 |
|---|---|
| `@devin-ai-integration` | 応答する |
| `/devin ` | 応答する |
| `Devin` | 応答する |

注意:
- `@Devin`、`@devin`、`@devin-ai` は、GitHub の別の個人ユーザーに通知が行くので、PR に書かない。
- PR と関係のない質問は、Devin が答えない。Slack などの新しいセッションで聞く。
- Devin が追跡している PR のコメントは、メンションなしでも受信され、トークンを消費しうる。指摘は 1 回のレビューにまとめる。止めるときは、PR の「Disable automatic comment, CI, and merge conflict monitoring」にチェックを入れるか、PR が閉じたあとにセッションをアーカイブする。

## 反映の記録

| ファイル | 反映した日 |
|---|---|
| 全ファイル(初回の取り込み) | 2026-11-06(Devin の設定から写した) |
| `skills/issue-to-pr/SKILL.md`(手順 1・2: モデルの確認) | 2026-11-09 |
| `skills/issue-to-pr/SKILL.md`(手順 1・2: モデルの確認を外した) | 2026-11-09 |
| README の「PR で Devin を呼ぶ」(`@Devin` を、GitHub の別の個人ユーザーに当たらない書き方に改めた) | 2026-11-13 |

## Devin の設定画面

- ルール: Customize → ルール(組織: bit-craft-io)
- スキル: Customize → スキル(組織のプラグイン)
- スクリプト: Settings → Environment → Blueprints(Files にアップロード。initialize で `sudo install -m 755 "$FILE_RUN_SUMMARY_SH" /usr/local/bin/run-summary`)
