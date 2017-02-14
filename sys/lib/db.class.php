<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        db.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        数据库操作
 * @起始日期    2014-3-1  12:23:57    
 * @文件版本    1.2.5
 */
final class Db {
    private $_config; //数据库配置参数
    
    private $_host = NULL; //主机
    
    private $_user = NULL; //用户
    
    private $_password = NULL; //密码
    
    private $_port = NULL; //端口
    
    private $_database = NULL; //数据库
    
    private $_prefix = NULL; //数据表前缀
    
    private $_charset = NULL; //字符集
    
    private $_queryList; //查询方法表
    
    public $connectFlag; //数据库是否成功连接 true[成功] false[失败]

    public $numRows = 0; // 返回或者影响记录数
    
    public $query_count = 0; //查询次数
    
    public $queryStartTime; //查询开始时间
    
    protected $queryID; //当前查询ID
    
    protected $con; //当前连接
    
    protected $transTimes = 0; //事务指令数
    
    protected $error = ''; // 错误信息
    
    public $querySql = ''; //当前sql语句
    
    /**
     *          构造方法
     */
    public function __construct() {
        $this->init(ff::$conDb);
    }
    
    
    private function init($config) {
        $this->_config = $config;
        $this->_host = $config['host'];
        $this->_user = $config['user'];
        $this->_password = $config['password'];
        $this->_port = $config['port'];
        $this->_database = $config['database'];
        $this->_prefix = $config['prefix'];
        $this->_charset = $config['charset'];
        $this->connectFlag = $this->connect();
    }
    
    /**
     *          连接数据库 [V1.2.0] class Db[私有方法]
     */
    private function connect() {
        $server = $this->_host . ':' . $this->_port;
        $this->con = mysql_connect($server, $this->_user, $this->_password, true) or die('阿帕奇服务器连接出现错误!');
        mysql_select_db($this->_database, $this->con) or die('数据库连接出现错误!');
        mysql_query("set names " . $this->_charset,  $this->con);
        return true;
    }    

    /**
     *          设置数据表对象 [V1.2.0] class Db[公共方法]
     * @param string $table 表名
     * @return \Db
     */
    public function table($table) {
        $this-> _queryList['table'] = $this->_prefix. $table; //连接表前缀
        return $this;
    }
    
    /**
     *          设置查询规则对象 [V1.2.0] class Db[公共方法]
     * @param mixed $where 查询规则
     * @return \Db
     */
    public function where($where) {
        $this-> _queryList['where'] = $where;
        return $this;
    }
    
    /**
     *          设置字段对象 [V1.2.0] class Db[公共方法]
     * @param string $fields 需要查询的字段
     * @return \Db
     */
    public function field($fields){
        $this-> _queryList['fields'] = $fields;
        return $this;
    }
    
    /**
     *          join一个字段 
     * @param string $join 需要join的字段
     * @return \Db
     */
    public function join($join){
        $this-> _queryList['join'] = $join;
        return $this;
    }
    /**
     *          设置数据对象 [V1.2.0] class Db[公共方法]
     * @param mixed $data 增删改查等操作所需的数据
     * @return \Db
     * *
     * @摘要 可批量写入数据 Db::data(array('field'=>array('data1','data2').....))
     */
    public function data($data){
        $this-> _queryList['data'] = $data;
        return $this;
    }

    /**
     *          设置排序规则对象
     * @param string $order 排序规则
     * @return \MySQL
     */
    public function order($order){
        $this-> _queryList['order'] = $order;
        return $this;
    }
    
    /**
     *          查询数据库信息 [V1.2.0] class Db[公共方法]
     * @return array 
     */
    public function select() {
        $sql = 'SELECT ';
        $fields = isset($this-> _queryList['fields']) ? $this-> _queryList['fields'] : '*';unset($this-> _queryList['fields']);
        $sql .= $fields;
        $sql .= ' FROM `' . $this-> _queryList['table'] . '` ';unset($this-> _queryList['table']);
        $sql = isset($this-> _queryList['where']) ? ($sql . ' WHERE ' . $this-> _queryList['where']) : $sql; unset($this-> _queryList['where']);
        $this-> querySql = $sql;
        return $this-> query();
    }
    
    /**
     *          数据写入 [V1.2.5] class Db[公共方法]
     * @return type
     * *
     * @摘要 自1.2.5版本 此方法支持批量写入
     */
    public function add() {
        $sql = 'INSERT INTO `'. $this->_queryList['table']. '`(';
        $data = $this->_queryList['data'];
        $value = $field = $str1 = $str2 = ''; //str1 str2 是sql语句合成时的中间变量
        $ksize = sizeof($data); 
        $vsize = 0;
        $valueText = array();
        if(array_foo($data)) {
            $str1 .= '(';
            foreach ($data as $k => $v) {
                $field .= '`' . $k . '`,';
                $str1 .= is_numeric($v) ? $v . ',' : '\'' . $v . '\','; 
            }
            $value .= chop($str1, ','). ')';
        } else {
            foreach ($data as $k => $v) {
                $field .= '`'. $k. '`,';
                $vsize = sizeof($v);
                for ($i = 0; $i < $vsize; $i++) {
                    $valueText[$i][] = $v[$i];
                }
            }
            foreach ($valueText as $v) {
                $str2 .= '(';
                for ($i = 0; $i < $ksize; $i++) {
                    $str1 .= is_numeric($v[$i]) ? $v[$i]. ',' : '\''. $v[$i]. '\',';
                }
                $str2 .= chop($str1, ','). '),';
                $value .= $str2;
                $str2 = $str1 = '';
            }
        }
        $sql .= chop($field, ','). ') VALUE '. chop($value, ',');
        $this->querySql = $sql;
        return $this-> execute();
    }
    
    /**
     *          删除数据库信息 [V1.2.0] class Db[公共方法]
     * @return type
     */
    public function delete() {
        $sql = 'DELETE FROM `' . $this-> _queryList['table'] . '` WHERE ' . $this-> _queryList['where'];
        $this-> querySql = $sql;
        return $this-> execute();
    }
    
    /**
     *          更新数据库信息 [V1.2.0] class Db[公共方法]
     * @return type
     */
    public function update() {
        $sql = 'UPDATE `' . $this-> _queryList['table'] . '` SET ';
        $data = $this-> _queryList['data'];
        foreach ($data as $k => $v) {
           $sql .= is_numeric($v) ? '`' . $k . '` =' . $v . ',' : '`' . $k . '` =\'' . $v . '\',';
        }
        $sql = chop($sql, ',');
        $sql = isset($this-> _queryList['where']) ? $sql . ' WHERE ' . $this-> _queryList['where'] : $sql;
        $this-> querySql = $sql;
        return $this-> execute();
    }
    
    /**
     *          select查询初始化 [V1.2.0] class Db[公共方法]
     * @return boolean|array
     */
    public function query() {
        $this-> querySql = func_num_args() === 0 ? $this-> querySql : func_get_arg(0);
        if (!$this-> con) return false;
        if ($this-> queryID) $this-> free();
        $this-> queryStartTime = microtime(true);
        $this-> queryID = mysql_query($this-> querySql, $this-> con);
        $this-> query_count++;
        if (false === $this-> queryID) {
            //$this-> error();
            return false;
        } else {
            $this-> numRows = mysql_num_rows($this-> queryID);
            return $this-> getResult();
        }
    } 

    /**
     *          insert|updata|delete 查询 [V1.2.0] class Db[公共方法]
     * @return boolean
     */
    public function execute() {
        $this-> querySql = func_num_args() === 0 ? $this-> querySql : func_get_arg(0);
        if (!$this-> con) return false;
        if ($this-> queryID) $this-> free();
        $this-> queryStartTime = microtime(true);
        $result = mysql_query($this-> querySql, $this-> con);
        $this-> query_count++;
        if (false === $result) {
            //$this-> error();
            return false;
        } else {
            $this-> numRows = mysql_affected_rows($this-> con);
            //return $this-> numRows;
        }
    }
    
    /**
     *          select查询 [V1.2.0] class Db[公共方法]
     * @return type
     */
    private function getResult() {
        $result = array();
        if ($this-> numRows > 1) {
            while ($row = mysql_fetch_assoc($this->queryID)) {
                $result[] = $row;
            }
            mysql_data_seek($this-> queryID, 0);
            return $result;
        }
        else 
            return mysql_fetch_assoc($this->queryID);
    }

    /**
     *          清空表中数据 [V1.2.5] class Db [公共]
     * @param string $table 表名
     * @return bool mysql_query() 语句是否成功执行
     */
    public function emptab($table = '') {
        $table = $table == '' ? $this->_queryList['table'] : $this->_prefix. $table;
        $sql = "TRUNCATE TABLE `{$table}`";
        return mysql_query($sql,  $this-> con);
    }
   
//    public function error() {
//        $this-> error = mysql_error($this-> con);
//        if('' != $this-> querySql){
//            $this-> error .= "\n [ SQL语句 ] : " . $this-> querySql;
//        }
//        return $this-> error;
//    }

    /**
     *          释放结果集 [V1.2.0] class Db[公共方法]
     * 清空内存中的结果集
     */
    public function free() {
        @mysql_free_result($this-> queryID);
        $this-> queryID = 0;
        $this-> _queryList = null;
    }

    /**
     *          关闭数据库连接 [V1.2.0] class Db[公共方法]
     * @throws Exception
     */
     public function close(){
        if ($this-> con && !mysql_close($this-> con)) {
            throw new Exception($this-> error());
        }
        $this-> con = 0;
        $this-> query_count = 0;
    }
    
    /**
     *          魔术方法，当调用的属性不存在或者受到类保护是自动执行 [V1.2.0] class Db[公共方法]
     * @param mixed $param 调用的方法或属性
     */
    public function __get($param) {
        echo 'Db->$'. $param. '属性不存在或为受保护的属性';
    }
    
    /**
     *          析构方法 [V1.2.0] class Db[公共方法]
     */
    function __destruct(){
        $this-> close();
    }    
}

//* End of the file db.class.php  
//* File path : ./sys/lib/
