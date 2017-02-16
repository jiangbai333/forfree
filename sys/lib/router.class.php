<?php

/**
 * @文件:       router.class.php
 * @作者:       b-jiang
 * @版本:       1.2.6
 * @创建时间:   2014-7-9 15:13:18
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
     *          构造方法 [ForFree v1.2.6] class router [public]
     * @功能    路由器构造方法，执行路由器初始化方法
     */
    public function __construct() {
        $this->init(ff::$conRouter);
    }
    
    /**
     *          路由器初始化方法  [ForFree v1.2.6] class router [private]
     * @功能    分离配置参数，获取URL变量
     * @param array $config 路由器配置
     */
    private function init($config) {
        $this->_config = $config; //路由器配置
        $this->_controller = $config['c']; //分离默认控制器
        $this->_action = $config['a']; //分离默认动作
        $this->_group = $config['g']; //分离默认分组
        $this->_type = $config['urlType']; //获取默认url解析方式
        $this->_pathInfoDepr = $config['PATHINFO_DEPR']; //pathinfo默认分隔符
        if ($this->_type == 1) { //query方式
            parse_str(QUY_STRING, $this->_urlQuery); //将URL符号表解析到URL变量表 self->_urlQuery
        } else if ($this->_type == 2) { //pathinfo方式
            //将URL pathinfo 解析到URL变量表 self->_urlQuery
            $this->_urlQuery = explode($this->_pathInfoDepr, str_ireplace(SRI_NAME. $this->_pathInfoDepr, "", REQ_URI));
        } else if ($this->_type == 3) { //混合方式
            
        } else {
            echo __URL_TYPE_ERROR__; 
            exit();
        }
    }
    
    /**
     *          分配URL解析方法  [ForFree v1.2.6] class router [public]
     * @功能    通过配置文件中选择的URL解析模式，散转到相应的处理方法
     * @return array 解析后的URL变量表
     */
    public function getUrlParam() {
        return $this->{$this->_typeFn[$this->_type]}($this->_urlQuery);
    }
    
    /**
     *          query方式 [ForFree v1.2.6] class router [private]
     * @param array $query URL符号表
     * @return array 解析后的URL变量表
     */
    private function queryType($query) {
        $q = array();
        if ( isset($query['g']) ) { //获得分组
            $q['group'] = $query['g'];
            unset($query['g']);
        } else { $q['group'] = $this->_group; }
        if ( isset($query['a']) ) { //获得分组
            $q['action'] = $query['a'];
            unset($query['a']);
        } else { $q['action'] = $this->_action; }        
        if ( isset($query['c']) ) { //获得分组
            $q['controller'] = $query['c'];
            unset($query['c']);
        } else { $q['controller'] = $this->_controller; }
        if(sizeof($query) > 0) { //是否存在其他参数
            $q['param'] = $query;
        } else $q['param'] = '';
        return $q;
    }
    
    /**
     *          pathinfo方式 [ForFree v1.2.6] class router [private]
     * @param array $info URL符号表
     * @return array 解析后的URL变量表
     */
    private function pathInfoType($info) {
        $q = array();
        $paramName = array(0=>'controller', 1=>'action', 2=>'group');
        $num = 0;
        foreach ($info as $key => $value) {
            if ( preg_match('~.*&.*~', $value) ) { //分离参数项
                $param = $value;
                unset($info[$key]);
            } else {
                if ( $num < 3 ) { //分离控制器 模型 分组等组件
                    $q[$paramName[$num++]] = $value;
                    unset($info[$key]);
                }
            }
        }
        $q['controller'] = isset( $q['controller'] ) ? $q['controller'] : $this->_controller;
        $q['action'] = isset( $q['action'] ) ? $q['action'] : $this->_action;
        $q['group'] = isset( $q['group'] ) ? $q['group'] : $this->_group;
        if ( isset($param) ) {
            parse_str($param, $q['param']); //合并参数
        } else {
            $q['param'] = '';
        }
//         [预留]：URL容错处理
//        if ( sizeof( $info ) > 0 ) { 
//            提示URL有多余参数，是否输入错误
//        }
        return $q;
    }    
}

//End of file router.class.php