<?php
/**
 * Homepage - 公共函数扩展文件
 * 
 * 本文件原为多层加密混淆的商业授权代码，已解密并清理：
 * - 移除了对 ajax_link.php / ajax_apply.php / ajax_theme.php 的授权拦截与授权码校验逻辑
 * - 移除了 _up_guard() 中的恶意删库后门（VIOLATION 状态下 DROP TABLE）
 * - 移除了向 cdn.lylme.com 上报域名/版本的 update() 与授权码自愈逻辑
 * - 移除了依赖 wx.lylme.com 的微信推送远程调用（wxPlus 改为本地桩函数）
 * - 移除了无鉴权的 ?console=update / ?console=list 路由（信息泄露）
 * - 保留主题读取、siteurl、web_list_info 等本地正常功能函数
 */

if (!defined('IN_CRONLITE')) {
    define('IN_CRONLITE', true);
}
if (!isset($conf)) {
    $conf = array();
}
if (!isset($GLOBALS['conf'])) {
    $GLOBALS['conf'] = &$conf;
}

// ========== 内部辅助函数 ==========

if (!function_exists('_cfg_val')) {
    function _cfg_val($k, $d)
    {
        $xkllifcn = isset($GLOBALS['conf']) ? $GLOBALS['conf'] : array();
        return isset($xkllifcn[$k]) ? $xkllifcn[$k] : $d;
    }
}

if (!function_exists('_cfg_val_from')) {
    function _cfg_val_from($a, $k, $d)
    {
        return isset($a[$k]) ? $a[$k] : $d;
    }
}

if (!function_exists('_srv_host')) {
    function _srv_host()
    {
        return explode(':', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost')[0];
    }
}

if (!function_exists('_srv_name')) {
    function _srv_name()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : _srv_host();
    }
}

if (!function_exists('_srv_scheme')) {
    function _srv_scheme()
    {
        $hbuuz = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $hbuuz == 443) ? "https://" : "http://";
    }
}

if (!function_exists('_srv_full')) {
    function _srv_full()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    }
}

// ========== 站点 URL（与 function.php 中的 siteurl 兼容，避免重复定义） ==========

if (!function_exists('siteurl')) {
    function siteurl($type = 0, $mode = 1)
    {
        if ($mode == 2) {
            $lmgtdb = _cfg_val('hostmode', "2");
            if ($lmgtdb == "1") {
                return _srv_host();
            } else {
                return _srv_name();
            }
        }
        if ($type == 1) {
            return _srv_host();
        }
        if ($type == 2) {
            return _srv_name();
        }
        return _srv_scheme() . _srv_full();
    }
}

// ========== 主题文件路径 ==========

if (!function_exists('theme_file')) {
    function theme_file($file)
    {
        $gdzvhhml = _cfg_val('template', 'default');
        $flnwqeq = ROOT . 'template/' . $gdzvhhml . '/' . $file;
        if (file_exists($flnwqeq)) {
            return $flnwqeq;
        } else {
            return 'template/' . $file;
        }
    }
}

// ========== 微信推送（独立版未启用远程推送服务） ==========

if (!function_exists('wxPlus')) {
    function wxPlus($data)
    {
        // 上游商业版的微信推送依赖 wx.lylme.com 远程服务（原 _wx_api 中 base64 藏址），
        // 独立发行版不依赖任何第三方服务，直接返回未启用提示
        return '{"code":-1,"msg":"独立版未启用微信推送服务"}';
    }
}

// ========== 主题配置读取 ==========

if (!function_exists('_theme_raw')) {
    function _theme_raw($theme)
    {
        $avjnir = ROOT . 'template/' . $theme . '/theme.ini';
        if (!file_exists($avjnir)) {
            return false;
        }
        $ggnrer = @file_get_contents($avjnir);
        if ($ggnrer === false) {
            return false;
        }
        $cwhws = json_decode($ggnrer, true);
        if (!is_array($cwhws)) {
            return false;
        }
        return $cwhws;
    }
}

if (!function_exists('theme')) {
    function theme($theme, $str)
    {
        $ualadvg = _theme_raw($theme);
        if ($ualadvg === false) {
            return false;
        }
        if (array_key_exists($str, $ualadvg) && is_array($ualadvg[$str])) {
            return $ualadvg[$str];
        } elseif (!empty($ualadvg[$str])) {
            return strip_tags($ualadvg[$str]);
        } elseif ($str == 'theme_version') {
            return "\xE6\x9C\xAA\xE7\x9F\xA5";
        } elseif ($str == 'theme_name') {
            return $theme;
        } else {
            return false;
        }
    }
}

// ========== 版本更新检查（已整体移除） ==========
// 上游的 update() 会向 cdn.lylme.com 上报域名与版本号，且 _up_guard() 会把服务器
// 下发的授权码自动写回数据库（授权锁自愈），另有 VIOLATION 状态下 DROP TABLE 的
// 恶意后门。独立发行版不依赖任何更新服务器，相关函数与 console 路由全部移除。

// ========== 网站列表信息 ==========

if (!function_exists('_wl_row')) {
    function _wl_row($g, $db)
    {
        $crppec = array();
        $kcdpsvgr = $db->query("SELECT * FROM `homepage_links` WHERE `group_id` = " . $g . " ORDER BY `link_order` ASC;");
        if ($kcdpsvgr !== false) {
            while ($qltmc = $db->fetch($kcdpsvgr)) {
                if ($qltmc === false)
                    break;
                $biwrarm = isset($qltmc['id']) ? $qltmc['id'] : 0;
                $xavoh = isset($qltmc['name']) ? $qltmc['name'] : '';
                $qbzjboe = isset($qltmc['url']) ? $qltmc['url'] : '';
                $crppec[] = $biwrarm . "_[" . $xavoh . "]" . $qbzjboe;
            }
        }
        return $crppec;
    }
}

if (!function_exists('web_list_info')) {
    function web_list_info()
    {
        global $DB;
        $ffdhnk = array();
        if (!isset($DB) || !is_object($DB)) {
            return $ffdhnk;
        }
        $nevwvnm = $DB->query("SELECT * FROM `homepage_groups` ORDER BY `group_order` ASC");
        if ($nevwvnm === false) {
            return $ffdhnk;
        }
        while ($zrzkroow = $DB->fetch($nevwvnm)) {
            if ($zrzkroow === false)
                break;
            $nbglsrse = isset($zrzkroow['group_id']) ? (int) $zrzkroow['group_id'] : 0;
            $qfqwqkol = isset($zrzkroow['group_name']) ? $zrzkroow['group_name'] : '';
            $ffdhnk[] = "[" . $nbglsrse . "]" . $qfqwqkol;
            $ffdhnk[] = _wl_row($nbglsrse, $DB);
        }
        return $ffdhnk;
    }
}

// ========== Console 路由（已整体移除） ==========
// 原代码暴露 ?console=update / ?console=list 两个无鉴权接口：
// - ?console=update 调用 update() 向 cdn.lylme.com 上报域名版本（phone-home，已随 update() 一并移除）
// - ?console=list 无需登录即返回全部分组与链接数据（信息泄露）
// 独立发行版不保留任何 console 路由。

// 授权拦截逻辑已完全移除：
// 原代码会对 ajax_link.php / ajax_apply.php / ajax_theme.php 的操作进行授权码校验，
// 未授权时返回 "操作失败：当前域名[xxx]未授权！请关注公众号获取授权码"。
// 部署方作为超级管理员，可自由管理所有链接，无需第三方授权。
