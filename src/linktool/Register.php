<?php

/**
 * LinkTool - A useful library for PHP 
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/LinkTool
 * @license     BSD (http://opensource.org/licenses/BSD-3-Clause)
 */

namespace linktool;

/**
 * Register
 * 全局变量工具类
 * @author Dong Nan <hidongnan@gmail.com>
 * @date 2015-9-4
 */
class Register {

    static private $register = [];

    /**
     * 获取变量
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    static public function get($name = '', $default = null) {
        // 无参数时获取所有
        if (empty($name)) {
            return self::$register;
        }
        // 优先执行设置获取或赋值
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                $name = strtoupper($name);
                return isset(self::$register[$name]) ? self::$register[$name] : $default;
            }
            // 支持多维数组获取
            $name = explode('.', $name);
            return self::getRecursive(self::$register, $name, $default);
        }
        return null;
    }

    /**
     * 递归的获取变量
     * @param array $register
     * @param array $names
     * @param mixed $default
     * @return mixed
     */
    static private function getRecursive($register, $names, $default = null) {
        if (empty($names)) {
            return $register;
        } else {
            $name = strtoupper(trim(array_shift($names)));
            if ($name) {
                if (isset($register[$name])) {
                    return self::getRecursive($register[$name], $names, $default);
                } else {
                    return $default;
                }
            } else {
                return $register;
            }
        }
    }

    /**
     * 设置变量
     * @param mixed $name
     * @param mixed $value
     * @return boolean
     */
    static public function set($name, $value = null) {
        // 配置定义
        if (is_string($name)) {
            self::$register[$name] = $value;
            return true;
        }
        // 批量定义
        elseif (is_array($name)) {
            self::$register = array_merge(self::$register, $name);
            return true;
        }
        return false;
    }

}
