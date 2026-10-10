# Monitoring

このポートフォリオでは、
EC2・ALB・RDSの主要な状態をCloudWatchで監視し、
異常検知時にAmazon SNSからメール通知する構成としました。

また、CloudWatch Agentを使用して、
EC2のメモリ・ディスク使用率とApacheログを収集しています。

## 1. 監視・通知構成

```text
EC2 / ALB / RDS
       │
       ▼
Amazon CloudWatch
       │
       ▼
CloudWatch Alarm
       │
       ▼
Amazon SNS
       │
       ▼
     Email
