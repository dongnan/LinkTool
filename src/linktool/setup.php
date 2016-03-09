<?php

/**
 * LinkTool - A useful library for PHP 
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/LinkTool
 * @license     BSD (http://opensource.org/licenses/BSD-3-Clause)
 */
//常量定义
/** 内存 */
defined('LT_MEMORY_ON') || define('LT_MEMORY_ON', function_exists('memory_get_usage'));
// 系统信息
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    ini_set('magic_quotes_runtime', 0);
    define('LT_MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
} else {
    define('LT_MAGIC_QUOTES_GPC', false);
}
//验证条件
/** 存在字段就验证（默认） */
define('LT_VALIDATE_EXISTS', 0);
/** 必须验证 */
define('LT_VALIDATE_MUST', 1);
/** 值不为空的时候验证 */
define('LT_VALIDATE_VALUE', 2);

//触发验证事件（可选）
/** 新增数据时候验证 */
define('LT_MODEL_INSERT', 1);
/** 编辑数据时候验证 */
define('LT_MODEL_UPDATE', 2);
/** 全部情况下验证（默认） */
define('LT_MODEL_BOTH', 3);
