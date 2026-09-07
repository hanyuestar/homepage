# Homepage 导航页 Docker 部署指南

**Homepage** 致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。

## 功能特性

- 简洁高效的上网导航与搜索入口
- 后台可视化添加链接、自定义搜索引擎
- 全站无广告、无商业推广
- 一键 Docker 部署，开箱即用，自动导入数据库
- 默认使用北京时间（CST），时区可配置

## 快速开始

安装 [Docker](https://docs.docker.com/get-docker/) 后，任选以下一种方式部署。

### 方式一：快速体验（不推荐）

```bash
docker run -d -p 8080:80 ghcr.io/hanyuestar/homepage:latest
```

> 注意：此方式容器删除后数据会丢失，仅用于体验。

### 方式二：数据持久化（推荐）

```bash
docker run -d -p 8080:80 -v homepage_mysql:/var/lib/mysql -v homepage_www:/var/www/html --name homepage ghcr.io/hanyuestar/homepage:latest
```

> 使用命名卷，数据永久保存，容器删除/重建不丢失。

### 方式三：Docker Compose 部署（推荐）

项目根目录已内置 `docker-compose.yml`（含健康检查、自动重启、内部网络），克隆或下载本项目后执行：

```bash
docker compose up -d
```

## 访问信息

| 项目 | 地址 |
|------|------|
| 前台 | http://localhost:8080 |
| 后台 | http://localhost:8080/admin/ |
| 默认账号 | `admin` |
| 默认密码 | `123456` |

> ⚠️ 首次登录后请立即修改默认密码（后台 → 账号安全）。

## 镜像说明

镜像为 **All-in-One** 单容器方案（Apache + PHP 8.2 + MariaDB），首次启动自动完成：

1. 初始化 MariaDB 数据目录
2. 创建数据库与用户（可通过环境变量覆盖，见下表）
3. 导入初始数据
4. 生成 `config.php` 并写入安装锁文件

### 环境变量

| 变量 | 默认值 | 说明 |
|------|--------|------|
| `TZ` | `Asia/Shanghai` | 时区 |
| `MYSQL_USER` | `homepage` | 数据库用户名 |
| `MYSQL_PASSWORD` | `homepage123456` | 数据库密码 |
| `MYSQL_DATABASE` | `homepage` | 数据库名 |

### 端口与卷

| 类型 | 值 | 说明 |
|------|-----|------|
| 端口 | `80` | Web 服务端口 |
| 卷 | `/var/lib/mysql` | 数据库数据 |
| 卷 | `/var/www/html` | 网站文件（含上传的图片等） |

## 数据持久化与备份

### 数据备份

```bash
# 备份数据库
docker exec homepage sh -c 'mysqldump -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl homepage' > homepage_db_$(date +%F).sql

# 备份网站文件
docker cp homepage:/var/www/html ./homepage_www_backup
```

### 数据恢复

```bash
# 恢复数据库
cat homepage_db_2026-09-07.sql | docker exec -i homepage sh -c 'mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl homepage'

# 恢复网站文件
docker cp ./homepage_www_backup/. homepage:/var/www/html/
```

## 反向代理示例（Nginx）

```nginx
server {
    listen 80;
    server_name nav.example.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 常见问题

### 首次启动很慢？

首次启动需要初始化数据库并导入数据，通常需要 30~90 秒，请耐心等待健康检查通过后再访问。

### 如何修改后台地址？

登录后台 → 账号安全 → 后台目录，修改后原 `/admin` 目录将失效，请使用新地址访问。

### 如何升级？

1. 备份数据库与网站文件（见上文备份章节）
2. 拉取新版本镜像：`docker pull ghcr.io/hanyuestar/homepage:latest`
3. 重建容器：`docker compose up -d --force-recreate`

数据库结构由程序在启动时自动兼容处理，无需手动导入升级 SQL。

### 忘记后台密码？

```bash
# 进入容器重置为 123456（md5('homepage' + '123456')）
docker exec -it homepage sh -c "mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl homepage -e \"UPDATE homepage_config SET v='f9b55a6241157762cf83b330bd84a1ed' WHERE k='admin_pwd';\""
```

## 本地构建镜像

```bash
git clone https://github.com/hanyuestar/homepage.git
cd homepage
docker build -t ghcr.io/hanyuestar/homepage:latest .
```
