# Design

このポートフォリオでは、AWS上でLinux Webサーバーを構築するだけでなく、
可用性・セキュリティ・運用性・コストを考慮して構成を決定しました。

## 1. ALB + EC2 2台構成

WebサーバーにはAmazon Linux 2023のEC2を2台使用し、
異なるAvailability Zoneへ配置しました。

Application Load Balancer（ALB）をWebアクセスの入口とし、
Web-1 / Web-2へ通信を振り分けています。

1台のWebサーバーで障害が発生した場合でも、
ALBのヘルスチェックによって異常なサーバーへの転送を停止し、
正常なサーバーでWebアクセスを継続できる構成としました。

Apache停止試験ではWeb-1をUnhealthy状態にし、
正常なWeb-2へ通信が継続されることを確認しています。

## 2. EC2をPublic Subnetへ配置

今回の検証環境では、WebサーバーをPublic Subnetへ配置しています。

本番環境ではPrivate Subnetへの配置も検討できますが、
今回は学習・検証環境として継続コストを抑えるため、
NAT Gatewayを使用しない構成としました。

その代わり、Security Groupによって以下のようにアクセスを制限しています。

- HTTP 80：ALBのSecurity Groupからのみ許可
- SSH 22：管理端末のグローバルIP `/32` からのみ許可

## 3. RDSをPrivate DB Subnetへ配置

データベースにはAmazon RDS for MySQLを使用し、
Private DB Subnetへ配置しました。

Public Accessは無効化し、
インターネットからRDSへ直接接続できない構成としています。

MySQL 3306番ポートは、
WebサーバーのSecurity Groupからの通信のみ許可しています。

## 4. RDSはSingle-AZ構成を採用

DB Subnetは2つのAvailability Zoneに作成していますが、
今回のRDSインスタンス自体はSingle-AZ構成としました。

本番環境ではMulti-AZによる可用性向上を検討しますが、
今回はポートフォリオ環境のコストを考慮してSingle-AZを選択しています。

検証環境と本番環境の違いを理解したうえで、
目的とコストに応じて構成を選択しました。

## 5. Security Groupによる通信制御

必要な通信のみを許可する方針とし、
通信経路を以下に限定しています。

- Internet → ALB：HTTP 80
- ALB → EC2：HTTP 80
- 管理端末 → EC2：SSH 22
- EC2 → RDS：MySQL 3306

可能な箇所ではIPアドレスではなくSecurity Groupを送信元として指定し、
必要なリソース間の通信だけを許可しています。

## 6. IAM Roleを使用

EC2からCloudWatchを利用する際は、
Access Key / Secret Access KeyをEC2内へ保存せず、
IAM Roleを使用しています。

CloudWatch Agentに必要な権限をIAM Roleへ付与し、
EC2からAWSサービスへアクセスできる構成としました。

また、EC2 Instance Metadata ServiceはIMDSv2を必須としています。

## 本番環境での改善案

今回の構成は、学習・検証環境として
セキュリティとコストのバランスを考慮しています。

本番環境では以下の改善を検討します。

- EC2をPrivate Subnetへ配置
- SSHの代わりにAWS Systems Manager Session Managerを利用
- RDSをMulti-AZ構成へ変更
- ACMを利用してALBをHTTPS化
- Terraform / CloudFormationなどによるInfrastructure as Code化
