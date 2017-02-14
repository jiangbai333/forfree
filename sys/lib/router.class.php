<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        router.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        路由
 * @起始日期    2014-2-23  15:08:19    
 * @文件版本    1.2.5   
 */
final class Router {
    private $_config = array(); //完整配置数组
    
    private $_controller = NULL; //默认控制器
    
    private $_action = NULL; //默认动作
    
    private $_group = NULL; //默认分组
    
    private $_pathInfoDepr = NULL; //pathinfo默认分隔符

    private $_type = NULL; //url解析方式

    private $_typeFn= array( //各个解析方式对应的解析方法
        1   =>  'queryType',
        2   =>  'pathInfoType',
        3   =>  'mixedType',
    );

    private $_urlQuery = NULL; //url变量表

    /**
     *          构造方法 [V1.2.0] class Router[构造方法]
     * 路由类入口
     */
    public function __construct() {
        $this->init(ff::$conRouter);
    }
    
    /**
     *          路由器初始化 [v1.2.5] class Router[私有方法]
     * @param array $config 路由器配置
     */
    private function init($config) {
        $this->_config = $config; //获取配置数组
        $this->_controller = $config['c']; //分离默认控制器
        $this->_action = $config['a']; //分离默认动作
        $this->_group = $config['g']; //分离默认分组
        $this->_type = $config['urlType']; //获取默认url解析方式
        $this->_pathInfoDepr = $config['PATHINFO_DEPR']; //pathinfo默认分隔符
        if ($this->_type == 1) { //query方式
            parse_str(QUY_STRING, $this->_urlQuery); //将URL符号表解析到URL变量表 self->_urlQuery
        } else if ($this->_type == 2) { //pathinfo方式
            //将URL pathinfo 解析到URL变量表 self->_urlQuery
            $this->_urlQuery = array_diff_key(explode($this->_pathInfoDepr, REQ_URI), explode($this->_pathInfoDepr, SRI_NAME));
        } else if ($this->_type == 3) { //混合方式
            
        } else {
            echo __URL_TYPE_ERROR__; 
            exit();
        }
    }
    
    /**
     *          分配URL解析方法 [V1.2.5] class Router[公共方法]
     * @return array 解析后的URL变量表
     */
    public function getUrlParam() {
        return $this->{$this->_typeFn[$this->_type]}($this->_urlQuery);
    }
    
    /**
     *          query方式 [V1.1.0] class Router[私有方法]
     * @param array $query URL符号表
     * @return array 解析后的URL变量表
     */
    private function queryType($query) {
        $queryArray = array();
        if(isset($query['g'])) { //group是否存在
            $queryArray['group'] = $query['g'];
            unset($query['g']);
        } else $queryArray['group'] = $this->_group; //将group指定为默认group
        
        if(isset($query['a'])) { //action是否存在
            $queryArray['action'] = $query['a'];
            unset($query['a']);
        } else $queryArray['action'] = $this->_action; //将action指定为默认action
        
        if(isset($query['c'])) {
            $queryArray['controller'] = $query['c'];
            unset($query['c']);
        } else $queryArray['controller'] = $this->_controller; //将controller指定为默认controller
        if(sizeof($query) > 0) { //是否存在其他参数
            $queryArray['param'] = $query;
        } else $queryArray['param'] = '';
        return $queryArray;
    }
    
    /**
     *          pathinfo方式 [V1.2.5] class Router[私有方法]
     * @param array $info URL符号表
     * @return array 解析后的URL变量表
     */
    private function pathInfoType($info) {
        $queryA = array();
        $queryB = array();
        $queryArray = array();
        $pramaName = array(0=>'c', 1=>'a', 2=>'g');
        $end = true;
        do {
            str_get_letter(current($info)) === 'p' && is_numeric(str_get_letter(current($info), 1)) ? 
                    $queryA[current($info)] = next($info) : 
                    $queryA[] = current($info);
        } while ($end = next($info));
        foreach ($queryA as $key => $value) {
            if (is_numeric($key) && $key < 3) {
                $queryB[$pramaName[$key]] = $value;
            } else if (is_numeric($key) && $key >= 3) {
                echo "<script>alert('URL内存在未定义符号$value');</script>";
            } else {
                $queryB[$key] = $value;
            }
        }
        if(isset($queryB['g']) && $queryB['g'] != '') { //group是否存在
            $queryArray['group'] = $queryB['g'];
            unset($queryB['g']);
        } else $queryArray['group'] = $this->_group; //将group指定为默认group
        
        if(isset($queryB['a']) && $queryB['a'] != '') { //action是否存在
            $queryArray['action'] = $queryB['a'];
            unset($queryB['a']);
        } else $queryArray['action'] = $this->_action; //将action指定为默认action
        
        if(isset($queryB['c']) && $queryB['c'] != '') {
            $queryArray['controller'] = $queryB['c'];
            unset($queryB['c']);
        } else $queryArray['controller'] = $this->_controller; //将controller指定为默认controller
        if(sizeof($queryB) > 0) { //是否存在其他参数
            $queryArray['param'] = $queryB;
        } else $queryArray['param'] = '';
        return $queryArray;
    }
}

//* End of the file router.class.php 
//* File path : ./sys/lib/
