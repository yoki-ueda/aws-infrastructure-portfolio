# Troubleshooting

## 1. EC2 CPU高負荷

### 事象

Web-1にCPU負荷を発生させ、
CloudWatch AlarmとSNS通知を確認。

```bash
timeout 300 yes > /dev/null &
```

### 切り分け

```bash
top
ps -C yes -o pid,ppid,%cpu,%mem,cmd
```

高負荷の`yes`プロセスを特定。

### 復旧

```bash
kill <PID>
```

CPU使用率低下後、
CloudWatch Alarmが`OK`へ戻ることを確認。

---

## 2. Apache停止

### 事象

```bash
sudo systemctl stop httpd
```

Web-1が`Unhealthy`となり、
Web-2へ通信が継続されることを確認。

### 切り分け

```bash
sudo systemctl status httpd
sudo journalctl -u httpd --since "10 minutes ago"
```

Apache停止を確認。

### 復旧

```bash
sudo systemctl start httpd
```

Web-1が`Healthy`へ戻り、
CloudWatch Alarmが`OK`へ復旧。

---

## 3. RDS通信遮断

### 事象

RDS Security Groupの
MySQL 3306許可を一時的に削除。

```text
TCP 3306 OK
↓
TCP 3306 NG
```

### 切り分け

```text
Apache : 正常
RDS    : Available
3306   : NG
```

通信経路へ原因を絞り込み。

### 復旧

Security Groupの3306許可を戻し、

```text
TCP 3306 OK
```

となることを確認。
