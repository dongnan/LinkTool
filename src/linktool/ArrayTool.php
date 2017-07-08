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
 * ArrayTool
 * 数组工具类
 * @author DongNan <dongyh@126.com>
 * @date 2015-9-1
 */
class ArrayTool {

    static public function replace($search, $replace, &$data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    self::replace($search, $replace, $data[$key]);
                } else {
                    $data[$key] = str_replace($search, $replace, $value);
                }
            }
        } else {
            $data = str_replace($search, $replace, $data);
        }
        return $data;
    }

    /**
     * 根据权重获取数组元素
     * @param array $array
     * @param string $weightKey
     * @return array
     */
    static public function weightRandom($array, $weightKey = 'weight') {
        $count = 0;
        foreach ($array as $item) {
            $count += isset($item[$weightKey]) ? $item[$weightKey] : 1;
        }
        $minNum    = 0;
        $maxNum    = 0;
        $randomNum = random_int($minNum, $count);
        foreach ($array as $item) {
            $maxNum += isset($item[$weightKey]) ? $item[$weightKey] : 1;
            if ($minNum < $randomNum && $randomNum <= $maxNum) {
                return $item;
            }
            $minNum += isset($item[$weightKey]) ? $item[$weightKey] : 1;
        }
        return $array[array_rand($array)];
    }

}
