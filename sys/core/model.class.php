<?php

/**
 * @文件        model.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        核心模型
 * @起始日期    2014-2-23  11:51:06    
 * @文件版本    1.2.5   
 */
class model {
    public $db; //数据库操作对象
    
    /**
     *          构造方法
     */
    final public function __construct() {
        $this->db = ff::$lib['db']; //数据库操作对象付值
    }
    
    /**
     *          抓取类操作句柄 [V1.2.4] class controller[私有]
     * @param string $className 类名 全部小写
     * @param boolean $storage 是否将操作句柄保存到核心驱动类类实例容器
     * @param path $autoLibPath 路径 默认为项目类库
     * @return \class|boolean 
     */
    final protected function getLibHandle($className, $storage = false, $autoLibPath = APP_LIB) {
        $lib = $autoLibPath. '/'. $className. '.class.php'; 
        if(file_exists($lib)) {
            require_once $lib;
            $class = ucfirst($className);
            $storage ? ff::$lib[$className] = new $class : '';
            return new $class;
        } else {
            return false;
        }
    }
}

//* End of the file model.class.php
//* File path : ./sys/core
