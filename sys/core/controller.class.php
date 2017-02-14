<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        controller.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        核心控制器
 * @起始日期    2014-2-23  11:18:53    
 * @文件版本    1.2.5 
 */
class controller {        
    public $ctrlStartTime = NULL; //控制器启动时间
    
    public $ctrlComplateTime = NULL; //控制器就绪时间
    
    public $var = array(); //外部模板变量 在子类中进行模板变量附值
    
    private $_var = array(); //内部模板变量 子类不可访问 若某个值同存在于$var $_var中 则渲染视图时 以$_var为准
    
    private $_group = array(); //MVC分组
    
    private $_view = NULL; //默认需要渲染的视图
    
    protected $lib = array(); //类库操作句柄
    
    /**
     *          构造方法
     */
    final public function __construct() {
        $this->init();
    }
    
    /**
     *          设置模板变量 [V1.2.3] class controller[魔术]
     * @param string $param 变量名
     * @param mixed $value 变量值
     */
    final public function __set($param, $value) {
        $this->_var[$param]=$value; //设置模板变量 $str = 'test'; <=> $this->var = array('str' => 'test');
    }
   
    /**
     *          核心控制器初始化 [V1.2.5] class controller[私有]
     */
    final protected function init() {
        $this->isOutTime(); //页面是否过期
        $this->ctrlStartTime = $this->micro(); //获取控制器启动时间
        $this->_group = ff::$urlArr['group']; //抓取分组
        $this->_view = ff::$urlArr['action']; //抓取默认渲染视图
        $this->lib = ff::$lib; //抓取类库操作句柄
    }
    
    /**
     *          建立html标签 [V1.2.4] class controller[私有]
     * @param type $tag 标签名 默认为span
     * @param string $str 标签内文本
     * @param type $property 属性 id|className|.....
     * @return tag
     * *
     * @用法 html页面{$tag} 控制器页面 $this->tag = $this->makeTag('string', 'a', "href='#'")； 在{$tag}位置生成a标签 文本:string href:#
     */
    final protected function makeTag($tag = 'span', $str = '', $property = '') {
        $str = preg_replace('/</', '&lt;', $str);
        $str = preg_replace('/>/', '&gt;', $str);
        return "<$tag $property>$str</$tag>";
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


    /**
     *          ajax返回方式 [V1.2.4] class controller[私有]
     * @param str|array|int $data 需要返回的数据
     * @param bool $is_end ajax结束标记,若已经完成全部信息的返回,则此参数设置为true
     * @param str $type 返回的数据类型
     */
    final protected function ajaxReturn($data, $is_end = true, $type = 'json') {
        $returnData = $data;
        if(is_array($returnData)) { //返回信息是否是数组
            $type = strtolower($type); //将字符串转换成小写
            if($type === 'json') { //判断返回格式是否为json
                $returnData = json_encode($returnData); //转换为json格式
            } else if ($type === 'xml') { //判断返回格式是否为json
                /*支持三维数组转换，键名转换成标签名，键值转换为键名对应标签名的文本*/
                $xml = '<?xml version="1.0" encoding="utf-8"?>';
                $xml .= '<return>';
                foreach ($returnData as $key1 => $value1) {
                    $xml .= '<'. $key1. '>';
                    if(is_array($value1)) {
                        foreach ($value1 as $key2 => $value2) {
                            $xml .= '<'. $key2. '>';
                            $xml .= $value2;
                            $xml .= '</'. $key2. '>';
                        }
                    } else $xml .= $value1;
                    $xml .= '</'. $key1. '>';
                }
                $xml .= '</return>';
                $returnData = $xml; //转换为json格式
            } else if ($type === 'eval') { //判断返回格式是否为eval
                $returnData = $returnData; //保持原值
            } else {$returnData = $returnData;} //!! 其他数据类型 {}内可自定义要返回数据的类型,在这里,我不添加其他类型,所以按原数据返回
            echo $returnData; 
            $is_end ? exit() : ''; //是否结束
        } else {
            echo $returnData;
            $is_end ? exit() : '';
        }
    }
    
    /**
     *          渲染视图 [V1.2.4] class controller[私有]
     * @param string $file 待渲染视图
     * @return void
     */
    final protected function display($file = '') {
        $this->_var = array_merge($this->var, $this->_var); //合并模板变量
        $this->var = array(); //清空外部模板变量
        $file = $file == '' ? $this->_view. '.html' : $file. '.html'; //初始化视图
        $path = VIEW. '/'. $this->_group. $file;
        $this->lib['temp']->temInit($path, $file, $this->_group, $this->_var); //模板引擎初始化
        $this->_var = array(); //视图渲染完毕,释放受保护的模板变量
        return;
    }

    /**
     *         获取运行时间 [V1.2.5] class controller[私有]
     * *
     * @用法 该方法返回该方法被调用时的微秒数 主要用以测定代码运行时间 使用时将被测量
     *      的代码块放入 $startTime与$endTime之间
     *          $startTime = ff::micro(); //开始时间
     *          //这里是要测量运行时间的代码
     *          $endTime = ff:micro(); //结束时间
     *          $useTime = $endTime - $startTime; //运行消耗的时间
     * @return float s.m
     */
    final protected static function micro() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    /**
     *          判断用户操作权限
     * @return boolean
     */
    protected function checkLevel() {
        if ( ff::$lib['session']->get('lv') == 0 ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     *          更新问卷最后修改时间
     * @param string $surveyid 问卷id
     */
    protected function changeSvEditTime($surveyid) {
//        ff::$lib['db']->table('surver')->data()->;
    }
    
    /**
     * 页面是否过期
     */
    protected function isOutTime() {
        if( ff::$urlArr['controller'] != ff::$conRouter['c'] ) {
            if( !ff::$lib['session']->get('userid') ) {
                redirect();
            }
        }
    }
    
    
    
    /**
     * 验证
     */
    protected function verification() {
        
    }
}

//* End of the file controller.class.php
//* File path : ./sys/core