<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        session.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        session操作类
 * @起始日期    2014-3-5  10:49:19    
 * @文件版本    1.2.5
 */
final class Session {
    public $id = 'forfree'; //session ID
    
    /**
     *          设置session值 [V1.2.5] class Session [公共]
     * @param string|int|array $key session键名
     * @param mixed $value session键值
     * @实例：
     * @用法① ff::$lib['session']->set('name', 'forfree'); 设置$_SESSION['name'] = 'forfree'
     * @用法② ff::$lib['session']->set(array('name' => 'forfree', 'age' => 1)) 设置$_SESSION['name'] = 'forfree' , $_SESSION['age'] = 1
     */
    public function set($key, $value = '') {
        if ( ! session_id()) $this->start(); 
        if ( ! is_array($key)) {
            $_SESSION[$key] = $value;
        } else {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
        }
    }
    
    /**
     *          捕捉session值 [V1.2.5] class session [公共]
     * @param string|int $key session键名
     * @return mixed $_SESSION[$key]对应的键名
     * @实例：
     * @用法① ff::$lib['session']->get('name'); 捕捉$_SESSION['name']的值 
     */
    public function get($key) {
        if ( ! session_id()) $this->start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    }
    
    /**
     *          删除session值 [V1.2.5] class session [公共]
     * @param string|int|array $key session键名
     * @return boolean 
     * @实例：
     * @用法① ff::$lib['session']->del('name'); 删除$_SESSION['name']的值 
     * @用法② ff::$lib['session']->del(array('name', 'age')) 删除$_SESSION['name']与$_SESSION['age']的值
     */
    public function del($key){
        if ( ! session_id()) $this->start ();
        if ( ! is_array($key)) {
            if (isset($_SESSION[$key])) unset ($_SESSION[$key]);
        } else {
            foreach ($key as $k) {
                if (isset($_SESSION[$k])) unset ($_SESSION[$k]);
            }
        }
        return true;
    }
    
    /**
     *          清空session会话 [V1.2.5] class session [公共]
     */
    public function clear() {
        if ( ! session_id()) $this->start();
        session_destroy();
        $_SESSION = array();
    }
    
    /**
     *          创建session会话 [V1.2.5] class session [私有]
     */
    private function start() {
        session_id();
        session_start();
    }
    /**
     *          更新session值[V1.2.5] class session [公有]
     * @param string|int|array $key键名
     * @param mixed $oldvalue 旧键值
     * @param mixed $newvalue 新键值
     * @return 
     * @用法     
     */
    public function update($key,$newvalue=''){
        if ( ! session_id()) $this->start();
        
        
        
    }
}

//* End of the file cesn.class.php  
//* File path : ./sys/lib/
