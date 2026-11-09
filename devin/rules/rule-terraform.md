# rule-terraform

- 説明: Terraform の実行範囲と、課金を伴う操作の扱い
- トリガー: Always on

## 内容

Terraform を扱うときは、次のルールに従う。

1. `terraform apply` と `terraform destroy` は実行しない。`terraform plan` までにする
2. 課金が発生するリソースは作成しない
