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
