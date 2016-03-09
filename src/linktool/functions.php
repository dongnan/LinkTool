<?php

/**
 * LinkTool - A useful library for PHP 
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/LinkTool
 * @license     BSD (http://opensource.org/licenses/BSD-3-Clause)
 */
use linktool\Config;
use linktool\Lang;
use linktool\Factory;
use linktool\Register;

/**
 * 获取和设置配置参数
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function conf($name = '', $value = null, $default = null) {
    if (is_string($name) && is_null($value)) {
        return Config::get($name, $default);
    } else {
        return Config::set($name, $value);
    }
}

/**
 * 获取语言定义
 * @param string $name  语言定义的key
 * @param array $value  需要替换的变量
 * @return mixed
 */
function lang($name = '', $value = null) {
    return Lang::get($name, $value);
}

/**
 * 添加日志记录
 * @param string $message 日志内容
 * @param int $level 日志等级
 * @param array $data 日志相关数组数据
 * @return boolean
 */
function record($message, $level = DEBUG, $data = []) {
    return Factory::Log()->log($level, $message, $data);
}

/**
 * 获取和设置全局变量 支持批量定义
 * @param string|array $name 全局变量
 * @param mixed $value 变量值
 * @param mixed $default 默认值
 * @return mixed
 */
function reg($name = null, $value = null, $default = null) {
    if (is_string($name) && is_null($value)) {
        return Register::get($name, $default);
    } else {
        return Register::set($name, $value);
    }
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name = '', $value = '') {
    $prefix = conf('SESSION_PREFIX');
    if (is_array($name)) { // session初始化 在session_start 之前调用
        if (isset($name['prefix']))
            conf('SESSION_PREFIX', $name['prefix']);
        if (conf('VAR_SESSION_ID') && isset($_REQUEST[conf('VAR_SESSION_ID')])) {
            session_id($_REQUEST[conf('VAR_SESSION_ID')]);
        } elseif (isset($name['id'])) {
            session_id($name['id']);
        }
        ini_set('session.auto_start', 0);

        if (isset($name['name']))
            session_name($name['name']);
        if (isset($name['path']))
            session_save_path($name['path']);
        if (isset($name['domain']))
            ini_set('session.cookie_domain', $name['domain']);
        if (isset($name['expire'])) {
            ini_set('session.gc_maxlifetime', $name['expire']);
            ini_set('session.cookie_lifetime', $name['expire']);
        }
        if (isset($name['use_trans_sid']))
            ini_set('session.use_trans_sid', $name['use_trans_sid'] ? 1 : 0);
        if (isset($name['use_cookies']))
            ini_set('session.use_cookies', $name['use_cookies'] ? 1 : 0);
        if (isset($name['cache_limiter']))
            session_cache_limiter($name['cache_limiter']);
        if (isset($name['cache_expire']))
            session_cache_expire($name['cache_expire']);
        if (isset($name['type']))
            conf('SESSION_TYPE', $name['type']);
        if (conf('SESSION_TYPE')) { // 读取session驱动
            $type = conf('SESSION_TYPE');
            $class = strpos($type, '\\') ? $type : 'link\\Session\\' . ucwords(strtolower($type));
            $hander = new $class();
            session_set_save_handler($hander, true);
        }
        // 启动session
        if (conf('SESSION_AUTO_START'))
            session_start();
    }elseif ('' === $value) {
        if ('' === $name) {
            // 获取全部的session
            return $prefix ? $_SESSION[$prefix] : $_SESSION;
        } elseif (0 === strpos($name, '[')) { // session 操作
            if ('[pause]' == $name) { // 暂停session
                session_write_close();
            } elseif ('[start]' == $name) { // 启动session
                session_start();
            } elseif ('[destroy]' == $name) { // 销毁session
                $_SESSION = [];
                session_unset();
                session_destroy();
            } elseif ('[regenerate]' == $name) { // 重新生成id
                session_regenerate_id();
            }
        } elseif (0 === strpos($name, '?')) { // 检查session
            $name = substr($name, 1);
            if (strpos($name, '.')) { // 支持数组
                list($name1, $name2) = explode('.', $name);
                return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
            } else {
                return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
            }
        } elseif (is_null($name)) { // 清空session
            if ($prefix) {
                unset($_SESSION[$prefix]);
            } else {
                $_SESSION = [];
            }
        } elseif ($prefix) { // 获取session
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;
            } else {
                return isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
            }
        } else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            } else {
                return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        }
    } elseif (is_null($value)) { // 删除session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        }
    } else { // 设置session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } else {
            if ($prefix) {
                $_SESSION[$prefix][$name] = $value;
            } else {
                $_SESSION[$name] = $value;
            }
        }
    }
    return null;
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function cookie($name = '', $value = '', $option = null) {
    // 默认设置
    $config = [
        'prefix' => conf('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => conf('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => conf('COOKIE_PATH'), // cookie 保存路径
        'domain' => conf('COOKIE_DOMAIN'), // cookie 有效域名
        'secure' => conf('COOKIE_SECURE'), //  cookie 启用安全传输
        'httponly' => conf('COOKIE_HTTPONLY'), // httponly设置
    ];
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = ['expire' => $option];
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    if (!empty($config['httponly'])) {
        ini_set("session.cookie_httponly", 1);
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    } elseif ('' === $name) {
        // 获取全部的cookie
        return $_COOKIE;
    }
    $name = $config['prefix'] . str_replace('.', '_', $name);
    if ('' === $value) {
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            if (0 === strpos($value, 'link:')) {
                $value = substr($value, 6);
                return array_map('urldecode', json_decode(LT_MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true));
            } else {
                return $value;
            }
        } else {
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if (is_array($value)) {
                $value = 'link:' . json_encode(array_map('urlencode', $value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
    return $ip[$type];
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    ];
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}

/**
 * 不区分大小写的in_array实现
 * @param string $value
 * @param array $array
 * @return boolean
 */
function in_array_case($value, $array) {
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 递归的将回调函数作用到给定数组的单元上
 * @param callable $filter
 * @param array $data
 * @return array
 */
function array_map_recursive($filter, $data) {
    $result = [];
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val) ? array_map_recursive($filter, $val) : call_user_func($filter, $val);
    }
    return $result;
}

/**
 * 返回字符串键名全为小写或大写的数组(递归)
 * @param array $arr
 * @param int $case CASE_LOWER|CASE_UPPER
 * @return array
 */
function array_change_key_case_recursive($arr, $case = CASE_LOWER) {
    return array_map(function($item) use ($case) {
        if (is_array($item)) {
            $item = array_change_key_case_recursive($item, $case);
        }
        return $item;
    }, array_change_key_case($arr, $case));
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
    //多行URL地址支持
    $url = str_replace(["\n", "\r"], '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 生成css html标签
 * @param string $path
 * @param string $site
 * @param string $version
 * @return string
 */
function tag_css($path, $site = 'asset', $version = '1.0') {
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    if (!empty($site)) {
        $path = conf('SITE.' . $site) . $path;
    }
    $htmlTag = '<link href="' . $path . '?v=' . $version . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
    return $htmlTag;
}

/**
 * 生成js html标签
 * @param string $path
 * @param string $site
 * @param string $version
 * @return string
 */
function tag_js($path, $site = 'asset', $version = '1.0') {
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    if (!empty($site)) {
        $path = conf('SITE.' . $site) . $path;
    }
    $htmlTag = '<script src="' . $path . '?v=' . $version . '" type="text/javascript"></script>' . PHP_EOL;
    return $htmlTag;
}

/**
 * 过滤查询特殊字符
 * @param string $value
 */
function lt_filter(&$value) {
    // TODO 其他安全过滤
    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}
