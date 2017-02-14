<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        temp.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        第三代模板引擎
 * @起始日期    2014-2-28  14:22:18    
 * @文件版本    1.2.5   
 */
final class Temp {
    public $data; //模板变量

    private $tempExpires = NULL; //缓存过期时间
    
    private $temPath = NULL; //视图路径

    private $cache = NULL; //缓存路径
    
    private $cacheFileName = NULL; //缓存文件名
    
    private $type = NULL; //解析方式 1: 内置解析器 2: 外置解析器

    public $reg = array( //外置解析器
        
    );
    
    private $_reg = array( //内置解析器
        '~\{\$([a-z0-9_]+)\}~i'   =>  '<?php echo $this->data[\'$1\'];?>', //$str 
        '~\{\$([a-z0-9_]+)\.([a-z0-9_]+)\}~i'   =>  '<?php echo $this->data[\'$1\'][\'$2\'];?>', //$arr.key
        '~\{\$([a-z0-9_]+)\.([a-z0-9_]+)\.([a-z0-9_]+)\}~i' =>  '<?php echo $this->data[\'$1\'][\'$2\'][\'$3\'];?>', // $arr.key.key1
    );
    
    /**
     *          构造方法
     */
    public function __construct() {
        $this->init(ff::$conTemp);
    }
    
    /**
     *          模板引擎初始化 [V1.2.5] class Temp[私有方法]
     * @param array $config 配置
     */
    private function init($config) {
        $this->tempExpires = $config['tempExpires']; //抓取缓存过期时间
        $this->type = $config['type']; //抓取解析类型
        $this->reg = $config['reg']; //抓取外置解析器
    }
    
    /**
     *          视图渲染初始化 [V1.2.5] class Temp[公共方法]
     * @param string $path 视图文件地址
     * @param string $file 视图文件名
     * @param string $group 视图分组
     * @param array $data 模板变量
     * @return file 显示的视图
     */
    public function temInit($path, $file, $group, $data = array()) {
        $this->temPath = $path; //视图文件地址
        $this->cache =  'run/cache/'. md5($group. $file). '.php'; //缓存路径
        $this->data = $data; //模板变量
        return $this->type === 1 ? $this->fetch($this->_reg) : $this->fetch($this->reg); //判断解析类型
    }
    
    /**
     *          视图解析与编译 [V1.1.0] class Temp[私有方法]
     * @param array $reg 解析器
     */
    private function fetch($reg) {
        if (!file_exists($this->temPath)) { //视图文件是否存在
            die('视图文件不存在');
        }
        /*缓存文件是否不存在，缓存文件是否超过缓存时间，缓存文件修改时间是否在视图文件修改时间之前,以上逻辑值为真，则进行模板编译*/
        if(!file_exists($this->cache) || 
            filemtime($this->temPath) > filemtime($this->cache) || 
            filemtime($this->temPath) + 6000 <=1) {
            $view = file_get_contents($this->temPath); //抓取视图
            $view = preg_replace(array_keys($reg), $reg, $view); //视图编译解析
            file_put_contents($this->cache, $view); //缓存视图
        }
        require $this->cache; //加载缓存视图        
    }
}

//* End of the file temp.class.php  
//* File path : ./sys/lib/