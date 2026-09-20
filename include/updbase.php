<?php

if (!defined("VERSION")) {
    return 0;
}

function get_vernum($version)
{
    // 移除版本号中的'v'前缀，并分割为数组
    $vn = explode('.', str_replace('v', '', (string) $version));

    // 确保数组至少有3个元素，避免未定义偏移错误
    $vn[0] = isset($vn[0]) ? $vn[0] : 0;
    $vn[1] = isset($vn[1]) ? $vn[1] : 0;
    $vn[2] = isset($vn[2]) ? $vn[2] : 0;

    // 格式化版本号：主版本 + 两位次版本 + 两位修订版本
    return $vn[0] . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
}

// 确保配置存在且包含版本信息
if (!isset($conf['version']) || empty($conf['version'])) {
    return 0;
}

$sqlvn = get_vernum($conf['version']);  // 数据库版本
$filevn = get_vernum(constant("VERSION"));  // 文件版本

if (!(isset($conf['build']) ? $conf['build'] : "")) {
    saveSetting('build', date("Y-m-d H:i"));
}

// ===== 上游 v2.x → 独立版 v1.x 迁移 =====
// 数据库 version 字段仍为上游 v2.7.0 时（vernum=20700），表示从上游版本升级到独立版。
// 此时无需执行上游的结构迁移（表/字段已是 v2.7.0），仅需：
// 1. 清理已废弃的微信推送配置行
// 2. 将数据库版本号更新为独立版版本号
if ($sqlvn >= 20700 && $filevn < 20000) {
    // 清理已废弃的微信推送配置行（v1.0.3 整链移除微信推送功能）
    $DB->query("DELETE FROM `homepage_config` WHERE `k` IN ('wxplus', 'wxplustime')");
    // 将数据库版本号切换到独立版体系
    saveSetting('version', constant("VERSION"));
    return 0;
}

if ($sqlvn < $filevn) {
    // 文件版本大于数据库版本，执行更新
    $sql = '';
    $version = '';
    if ($sqlvn < 20300) {
        $version = 'v2.3.0';
        $sql .= "ALTER TABLE `homepage_links` ADD `link_keywords` VARCHAR(512) NULL DEFAULT NULL COMMENT '链接关键词' AFTER `link_desc`;";
    }

    if ($sqlvn < 20600) {
        saveSetting('copyright',  $conf['copyright'] . '<script src="/assets/js/svg.js"></script>'); //注入旧版svg图标
        $version = 'v2.6.0';
    }
    if ($sqlvn < 20700) {
        $version = 'v2.7.0';
    }
    // 执行SQL语句
    if (!empty($sql)) {
        $sqlStatements = explode(';', $sql);

        foreach ($sqlStatements as $sqlStatement) {
            $sqlStatement = trim($sqlStatement);
            if (empty($sqlStatement)) {
                continue;
            }

            try {
                $DB->query($sqlStatement);
            } catch (Exception $e) {
                // 可以选择记录错误日志，但不中断升级流程
                error_log("SQL执行失败: " . $e->getMessage());
            }
        }
    }

    // 保存新版本号
    if (!empty($version)) {
        saveSetting('version', $version);
    }
}
