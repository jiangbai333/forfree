<?php
if (!defined('FORFREE')) {require_once './conf/macro.php'; exit(__MSG__);}

/**
 * @文件        forfree.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        forfree驱动类
 * @起始日期    2014-2-13  14:19:33    
 * @文件版本    1.2.5   
 */
final class ff {
    private static $_config = array(); //完整的配置数组
    
    public static $conDb = array(); //数据库配置
    
    public static $conLib = array(); //类库配置    
    
    public static $conRouter = array(); //路由器配置
    
    public static $conTemp = array(); //模板引擎配置  
    
    public static $conMail = array(); //邮件服务器配置
    
    public static $coreStartTime = NULL; //内核启动时间
    
    public static $coreComplateTime = NULL; //内核就绪时间
    
    public static $lib = array(); //类操作句柄容器 

    public static $urlArr = array(); //url变量表

    
    public static function run($config){
        self::init($config);
        self::autoLoad(self::$lib);
        self::$urlArr = self::$lib['router']->getUrlParam();
        self::routerToCm(self::$urlArr);
    }
    
    /**
     *          应用初始化方法 [V1.2.5] class ff[私有方法]
     * @param array $config 配置
     */
    private static function init($config) {
        self::$coreStartTime = self::micro(); //获取内核启动时间
        self::$_config = $config; //获取配置数组
        self::$conDb = $config['db']; //分离数据库配置
        self::$conLib = $config['lib']; //分离类库配置
        self::$conRouter = $config['router']; //分离路由器配置
        self::$conTemp = $config['temp']; //分离模板引擎配置
        self::$conMail = $config['mail']; //分离邮件服务器配置
        require_once SYS_CORE . '/controller.class.php'; //包含核心控制器
        require_once SYS_CORE . '/model.class.php'; //包含核心模型
        require_once SYS_COMMON . '/common.php'; //包含工具函数库
        self::setAutoLibs(self::$conLib['shield'], self::$conLib['autoLibs'], self::$conLib['autoPath']); //设置自动加载类
    }

    /**
     *          设置自动加载类 [V1.2.4] class ff[私有方法]
     * @param array $shield 屏蔽 此参数可直接指定为 self::$conLib['shield']
     * @param array|empty $autoLibs 自动加载的类 使用时应传入 self::$conLib['autoLibs']
     * @param string $libPath 加载的路径 默认加载路径为系统类库 SYS_LIB
     * *
     * @用法① self::setAutoLibs(arr1, arr2) 加载系统类库 加载自定义类库中包含在arr2中的类,并且屏蔽arr1中的类
     * @用法② self::setAutoLibs(arr1) 加载系统类库 屏蔽arr1中的类
     * @用法③ setAutoLibs(arr1, arr2|'', string) 将加载路径指定为string 加载自定义类库中包含在arr2中的类,并且屏蔽arr1中的类
     * @提示   建议完全通过配置文件修改核心运行过程 因此用如下方式调用此方法
     *      self::setAutoLibs(self::$conLib['shield'], self::$conLib['autoLibs'], self::$conLib['autoPath']);
     */
    private static function setAutoLibs($shield = array(), $autoLibs = '', $libPath = SYS_LIB) {
        $lib = array(); //数据暂存
        $fp = opendir($libPath); //抓取目录指针
        while (($file = readdir($fp)) !== false) { //循环抓取$fp指向的目录内的文件指针
            if ($file != '.' && $file != '..') { //屏蔽系统内 '.' '..' 文件
                $lib[substr($file, 0, -10)] = substr($libPath, strlen(substr(SCT_FILENAME, 9))) . '/' . $file; //连接类文件名            
            }
        } closedir($fp); //释放文件指针
        $autoLibs === '' ? self::$lib = array_merge(array_diff_key($lib, $shield), self::$lib) : 
            self::$lib = array_merge(array_diff_key(array_merge($autoLibs, $lib), $shield), self::$lib); //设置自动加载的类
    }
    
    /**
     *          自动加载类库 [V1.2.0] class ff[私有方法]
     * @param array $libs 需要加载的类
     */
    private static function autoLoad($libs) {
        foreach($libs as $key => $value) {
            require_once $value;
            $className = ucfirst($key);
            self::$lib[$key] = new $className;
        }
    }

    /**
     *          分配控制器应用 [V1.1.0] class ff[私有方法]
     * @param array $url URL变量表
     */
    private static function routerToCm($url) {
        $controller = $url['controller']; //控制器
        $model = $url['controller']; //模型
        $action = $url['action']; //动作
        $params = $url['param']; //参数
        $group = $url['group']; //MVC分组
        if ($group != '') { //分组是否存在
            self::$urlArr['group'] = $group . '/'; //分组文件夹
            $controllerFile = APP_C . '/' . $group . '/' . $controller . 'Controller.class.php'; //控制器
            $modelFile = APP_M . '/' . $group . '/' . $model . 'Model.class.php'; //模型
        } else {
            $controllerFile = APP_C . '/' . $controller . 'Controller.class.php'; //控制器
            $modelFile = APP_M . '/' . $model . 'Model.class.php'; //模型
        }
        if (file_exists($controllerFile)) { //控制器是否存在
            if (file_exists($modelFile)) //模型是否存在
                require_once $modelFile; //包含模型
            require_once $controllerFile; //包含控制器
            $controller = $controller . 'Controller'; //控制器类名合成
            $controller = new $controller; //实例化控制器
            if (method_exists($controller, $action)) { //action操作是否存在                
                isset($params) ? $controller->$action($params) : $controller->$action(''); //是否存在action操作对应的参数
            }
            else {
                die("控制器" . $url['controller'] . "Controller中不存在方法$action");
            }
        }
        else {
            die("不存在控制器" . $controller . "Controller");
        }
    }
    
    /**
     *         获取运行时间 [V1.2.5] class ff[公共方法]
     * *
     * @用法 该方法返回该方法被调用时的微秒数 主要用以测定代码运行时间 使用时将被测量
     *      的代码块放入 $startTime与$endTime之间
     *          $startTime = ff::micro(); //开始时间
     *          //这里是要测量运行时间的代码
     *          $endTime = ff:micro(); //结束时间
     *          $useTime = $endTime - $startTime; //运行消耗的时间
     * @return float s.m
     */
    public static function micro() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    /**         
     *          保留
     */
    public static function error() {
        
    }

}

//* End of the file forfree.php  
//* File path : ./sys/forfree.php 