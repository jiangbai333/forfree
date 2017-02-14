<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        cookie.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        cookie操作类
 * @起始日期    2014-3-21  12:07:54    
 * @文件版本    1.2.5
 */
class Cookie {
    private $prefix = 'forfree'; //cookie名前缀
    
    private $expire = 2592000; //cookie有效期
    
    private $path = '/'; //cookie开放的路径
    
    private $domain = ''; //cookie的有效域名
    
    /**
     *          设置cookie值 [V1.2.5] class cookie [公共]
     * @param string $key cookie键名
     * @param string $val cookie键名
     * @param int $expire cookie有效时间
     * @param string $path cookie开放路径 
     * @param string $domain cookie有效域名
     * @实例：
     * @用法① ff::$lib['cookie']->set('name', 'forfree'); 设置$_COOKIE['forfreename'] = 'forfree' 关闭浏览器后将自动删除
     * @用法② ff::$lib['cookie']->set('name', 'forfree', 3600); 设置$_COOKIE['forfreename'] = 'forfree' 有效期1小时
     */
    public function set($key, $val, $expire = '', $path = '', $domain = '') {
        $expire = empty($expire) ? time() + $this->expire : time() + $expire;
        $path   = empty($path) ? $this->path : $path;
        $domain = empty($domain) ? $this->domain : $domain;
        if (empty($domain)) {
            setcookie($this->prefix. $key, $val, $expire, $path);
        } else {
            setcookie($this->prefix. $key, $val, $expire, $path, $domain);
        }
        $_COOKIE[$this->prefix. $key] = $val;
    }
    
    /**
     *          捕捉cookie值 [V1.2.5] class cookie [公共]
     * @param string $key 键名
     * @return string $_COOKIE[$this->prefix. $key]的值
     */
    public function get($key) {
        return $_COOKIE[$this->prefix. $key];
    }
    
    /**
     *          删除cookie值 [V1.2.5] class cookie [公共]
     * @param string $key 键名
     * @param string $path 有效路径
     * @return boolean   
     * @实例：
     * @用法① ff::$lib['cookie']->del('name'); 删除$_COOKIE['forfreename']
     */
    public function del($key, $path = '') {
        $this->set($key, '', time() - 1, $path);
        $_COOKIE[$this->prefix.$key] = '';
        unset($_COOKIE[$this->prefix.$key]);
        return true;
    }    
    
    /**
     *          察看cookie值是否存在 [V1.2.5] class cookie [公共]
     * @param string $key 键名
     * @return boolean
     * @实例：
     * @用法① ff::$lib['cookie']->is_set('name'); $_COOKIE['forfreename']存在返回true 否则返回false
     */
    public function is_set($key) {
        return isset($_COOKIE[$this->prefix.$key]);
    }
}

//* End of the file cookie.php  
//* File path : ./sys/lib/
