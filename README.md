# AWS Web Infrastructure Portfolio

AWS上にALB、EC2、RDSを使用したWebインフラを構築し、
監視・障害対応・復旧まで検証したポートフォリオです。

**技術:** AWS VPC / Subnet / ALB / EC2 / RDS / Security Group / IAM / CloudWatch / SNS / Amazon Linux 2023 / Apache / PHP / MySQL

## 構成

![AWS Web Infrastructure Architecture](docs/architecture.png)

## 実施内容

- VPC内にPublic Subnet / Private DB Subnetを2つのAvailability Zoneへ構成
- Amazon Linux 2023 EC2を2台構築し、Apache / PHPをセットアップ
- Application Load Balancerによる負荷分散とヘルスチェックを構成
- Amazon RDS for MySQLをPrivate DB Subnetに配置し、Webサーバーからの接続経路を構成
- Security GroupでALB → EC2 → RDSの必要な通信のみに制限
- EC2にIAM Roleを付与し、長期アクセスキーを配置しない構成を採用
- CloudWatchによるメトリクス監視とSNSによるメール通知を構成
- CloudWatch Agentを使用してメモリ・ディスク使用率を収集し、Apacheのアクセスログ / エラーログをCloudWatch Logsへ転送

## 運用・障害対応

- 障害試験としてCPU高負荷を発生させ、CloudWatch Alarm → SNSメール通知までの監視動作を確認
- `top` / `ps` で高負荷プロセスを特定し、プロセス停止後にCPU使用率とAlarmが正常状態へ戻ることを確認
- Apache停止試験を実施し、ALBのヘルスチェックがUnhealthyとなった際も正常なWebサーバーへ通信が継続されることを確認
- `systemctl` / `journalctl` でApacheの状態・ログを確認し、サービス復旧後にHealthyへ戻ることを確認
- RDSへのMySQL 3306通信を障害試験としてSecurity Groupで遮断し、Webサーバー・RDS本体・通信経路を切り分けたうえで復旧

障害試験の詳細は [`troubleshooting.md`](docs/troubleshooting.md) を参照。

## 実装・検証資料

- [`design.md`](docs/design.md) - 構成・設計理由
- [`security.md`](docs/security.md) - Security Group / IAMのセキュリティ設計
- [`monitoring.md`](docs/monitoring.md) - CloudWatch / SNSの監視設計
- [`troubleshooting.md`](docs/troubleshooting.md) - 障害試験・切り分け・復旧の詳細
- [`posts.php`](web/posts.php) - RDS MySQLからデータを取得するPHPページ

## 動作確認

ALBによる負荷分散、CloudWatch / SNSによる異常通知、Apache障害時のフェイルオーバーを確認しました。

![ALB正常稼働](screenshots/01-alb-healthy.png)

![CloudWatch Alarm / SNS通知](screenshots/04-cpu-alarm-notification.png)

![ALBフェイルオーバー](screenshots/05-alb-failover.png)
