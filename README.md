# Homepage 导航页

<p align="center">
  <img src="https://img.shields.io/badge/version-2.7.0-blue" alt="Version">
  <a href="./LICENSE"><img src="https://img.shields.io/badge/license-Apache--2.0-green" alt="License"></a>
  <img src="https://img.shields.io/badge/PHP-%3E%3D7.0-purple" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-%3E%3D5.6-orange" alt="MySQL">
</p>

> 简洁高效无广告的上网导航和搜索入口，支持后台管理、多模板切换与自定义搜索引擎，全站无商业推广，简约而不简单。

## 项目地址

<https://github.com/hanyuestar/homepage>

## 快速开始

### Docker 部署（推荐）

一条命令完成部署，开箱即用，自动配置并导入数据库：

```bash
docker run -d -p 8080:80 -v homepage_mysql:/var/lib/mysql -v homepage_www:/var/www/html --name homepage hanyuestar/homepage:latest
```

或使用 Docker Compose（项目根目录已内置 `docker-compose.yml`，含健康检查、自动重启）：

```bash
docker compose up -d
```

| 项目     | 地址                          |
| -------- | ----------------------------- |
| 前台     | <http://localhost:8080>       |
| 后台     | <http://localhost:8080/admin> |
| 默认账号 | `admin`                       |
| 默认密码 | `123456`                      |

> 首次登录后请立即修改默认密码。详细的部署、数据持久化、备份恢复、反向代理等内容请参阅 [Docker.md](Docker.md)。

### 常规安装

1. 下载源码上传至网站根目录解压
2. 访问 `http://域名/install`，按提示配置数据库完成安装
3. 后台地址：`http://域名/admin`

### 环境要求

| 组件 | 要求 |
|------|------|
| PHP | >= 7.0（推荐 8.x） |
| MySQL | >= 5.6（推荐 5.7+） |
| Web 服务器 | Apache / Nginx |

**PHP 扩展**：mysqli、pdo_mysql、gd、curl、mbstring、xml、zip

## 功能特性

### 前台

- **多搜索引擎切换** — 内置多个常用搜索引擎，后台可自定义增删与排序
- **分组导航** — 链接按分组展示，支持分组排序、加密访问
- **收录申请** — 用户可在线提交网站收录申请，支持验证码与限流防护
- **详情页模式** — 支持直接跳转与详情页两种运行模式，详情页自动采集站点信息
- **响应式设计** — 适配 PC 与移动端，支持独立手机端背景
- **Bing 每日壁纸** — 支持通过 CRON 定时抓取 Bing 每日一图作为背景
- **随机一言** — 可选的随机一言展示

### 后台

- **网站设置** — 标题、Logo、背景、SEO 关键词/描述、备案号、版权信息、自定义 Footer 等
- **链接管理** — 增删改查、批量导入、批量操作（启用/禁用/移动/加密/删除）、失效检测
- **分组管理** — 分组增删改查、拖拽排序、分组加密
- **搜索引擎管理** — 搜索引擎的增删改查与排序
- **主题管理** — 内置多套主题模板，后台一键切换，支持主题自定义设置
- **收录审核** — 查看用户提交的收录申请，支持通过/拒绝/删除
- **导航菜单** — 顶部导航菜单的自定义管理
- **加密管理** — 链接/分组密码保护，支持多密码组
- **文件清理** — 图片快速清理
- **账号安全** — 修改管理员账号密码、后台目录自定义、调试模式开关

## 目录结构

```
homepage/
├── admin/        # 后台管理
├── apply/        # 收录申请
├── assets/       # 静态资源
├── include/      # 核心函数库
├── install/      # 安装程序
├── site/         # 站点地图等
├── template/     # 主题模板
├── config.php    # 数据库配置
├── index.php     # 前台入口
├── Dockerfile    # Docker 镜像构建
└── docker-compose.yml
```

## 开源许可

本项目基于 [Apache License 2.0](./LICENSE) 开源。
