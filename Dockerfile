# Homepage Docker Image - All-in-One
# Apache + PHP 8.2 + MariaDB (内置)

FROM php:8.2-apache-bookworm

LABEL org.opencontainers.image.title="Homepage" \
      org.opencontainers.image.description="Homepage 导航页 - 简洁高效的上网导航" \
      org.opencontainers.image.source="https://github.com/hanyuestar/homepage"

# 设置环境变量
ENV DEBIAN_FRONTEND=noninteractive

# 默认时区为北京时间，可通过 docker run -e TZ=xxx 覆盖
ENV TZ=Asia/Shanghai

# 安装 MariaDB、Supervisor 及 PHP 扩展构建依赖
# 注：curl / mbstring / xml(dom、simplexml) / pdo / opcache 基础镜像已内置并默认启用，无需重复编译
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    supervisor \
    curl \
    tzdata \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/* \
    && ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        zip \
        bcmath

# 配置 MariaDB 只监听本地 socket，同时兼容 ARM 和 x86
RUN mkdir -p /var/run/mysqld && \
    chown mysql:mysql /var/run/mysqld && \
    chmod 755 /var/run/mysqld && \
    echo "[mysqld]" > /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "bind-address = 127.0.0.1" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "skip-ssl" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "innodb_buffer_pool_size = 128M" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "innodb_log_file_size = 32M" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf

# 配置 PHP
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'upload_max_filesize = 50M'; \
        echo 'post_max_size = 50M'; \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 300'; \
        echo 'mysqli.default_socket = /var/run/mysqld/mysqld.sock'; \
        echo 'pdo_mysql.default_socket = /var/run/mysqld/mysqld.sock'; \
        echo 'date.timezone = Asia/Shanghai'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# 启用 Apache 模块
RUN a2enmod rewrite headers expires deflate

# 设置工作目录
WORKDIR /var/www/html

# 复制应用文件（.dockerignore 已排除 .git、文档、Docker 配置等）
COPY . /var/www/html/

# 清理构建期产物，仅保留运行时文件（docker-entrypoint.sh / supervisord.conf 单独 COPY，不留在 web 根）
RUN rm -rf /var/www/html/.git /var/www/html/.github \
        /var/www/html/Dockerfile /var/www/html/docker-compose.yml \
        /var/www/html/docker-entrypoint.sh /var/www/html/supervisord.conf

# 备份一份应用文件，用于 entrypoint 在空卷/bind mount 场景下自动恢复
RUN mkdir -p /app && cp -a /var/www/html/. /app/www_bak/

# 复制初始化文件
RUN mkdir -p /init && cp /var/www/html/install/data/install_struct.sql /init/install.sql

# 复制启动脚本
COPY docker-entrypoint.sh /docker-entrypoint.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 去除 Windows CRLF 换行符（防止 exec 报 no such file or directory / 配置解析异常），
# 并移除镜像内的安装锁，确保首次 docker run 能自动初始化数据库
RUN sed -i 's/\r$//' /docker-entrypoint.sh \
        /etc/supervisor/conf.d/supervisord.conf \
        /init/install.sql \
    && find /var/www/html -name '.htaccess' -exec sed -i 's/\r$//' {} + \
    && rm -f /var/www/html/install/install.lock \
    && chmod +x /docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /app/www_bak \
    && chmod -R 755 /var/www/html /app/www_bak

# 数据目录
VOLUME ["/var/lib/mysql", "/var/www/html"]

# 暴露端口
EXPOSE 80

# 健康检查
HEALTHCHECK --interval=30s --timeout=10s --start-period=90s --retries=3 \
    CMD curl -fsS -o /dev/null http://localhost/ || exit 1

# 入口点
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
