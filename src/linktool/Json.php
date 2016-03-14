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
 * Json 工具类
 * 
 * @author Dong Nan <hidongnan@gmail.com>
 * @date 2015-9-1
 */
class Json {

    /**
     * 数据编译为json字符串
     * @param array $data
     * @return string
     */
    static public function encode($data) {
        if (!is_array($data)) {
            return $data;
        }

        $array = Url::encode($data);
        $json = json_encode($array);
        //将urlendoce后的已经为\"的先转换为",防止被转成\\"
        $json = str_replace('%5C%22', '%22', $json);
        //将urlencode后的"替换为\"再入库，防止"未转义
        $json = str_replace('%22', '%5C%22', $json);
        $json = Url::decode($json);

        return $json;
    }

    /**
     * json字符串解码为数组
     * @param string $data
     * @return array
     */
    public static function decode($data) {
        if (is_array($data)) {
            return $data;
        } elseif (is_string($data) && in_array($data[0], ['{', '['])) {
            $array = json_decode(str_replace(['#', "\r\n", "\r", "\n", "\t"], [':#', '##r#n##', '##r##', '##n##', '##t##'], $data), TRUE);
            return String::replace([ '##r#n##', '##r##', '##n##', '##t##', ':#'], [ "\r\n", "\r", "\n", "\t", '#'], $array);
        } else {
            return $data;
        }
    }

}
