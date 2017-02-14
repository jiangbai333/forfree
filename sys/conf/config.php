<?php

/**   
 * @文件        config.php   
 * @作者        b-jiang
 * @描述        服务器脚本
 * @功能        配置文件
 * @起始日期    2014-2-13  14:26:45    
 * @文件版本    1.2.5
 */

/**
 *          定义核心配置参数
 * 当入口文件执行run()方法时 将此参数传入核心驱动
 */
$config['system']=array();

/**
 *          定义数据库配置
 * 配置自动连接方式 主机名 用户名 用户密码 端口号 数据库名 数据库表前缀 数据库字符集
 * 
 */
$config['system']['db'] = array(
    'host'          =>      'localhost', //主机
    
    'user'          =>      'root', //用户
    
    'password'      =>      'b-jiang+123', //密码
    
    'port'          =>      3306, //端口号
    
    'database'      =>      'cx_survey', //数据库名
    
    'prefix'        =>      'cx_', //数据表前缀
    
    'charset'       =>      'utf8', //数据字符集
);

/**
 *          定义数据库配置
 * 配置自动连接方式 主机名 用户名 用户密码 端口号 数据库名 数据库表前缀 数据库字符集
 * 
 */
$config['system']['mail'] = array(
    'host'      =>  'smtp.exmail.qq.com', //主机
    
    'user'      =>  '1129087617@qq.com', //用户
    
    'pwd'       =>  '13114626726', //密码
    
    'port'      =>  25, //端口号
    
    'nickName'  =>  'forfree', //昵称
    
    'homePage'    =>  "forfreeb.sinaapp.com" //主页
);

/**
 *          定义类库配置
 * 配置默认加载方式 加载路径 加载文件 屏蔽文件
 */
$config['system']['lib'] = array( 
    'autoLoad'	=>	true, //是否自动加载
    
    'autoPath'  =>      SYS_LIB, //默认类库加载地址
    
    'autoLibs'  =>      array( //默认加载类
        
    ),
    
    'shield'    =>      array( //屏蔽的类
        //'db'                =>      'sys/lib/Db.class.php',
    ),
);

/**
 *          定义路由器配置参数
 * 配置系统默认控制器 控制器动作 应用分组
 * 指定url解析模式
 */
$config['system']['router'] = array(
    'c' =>  'index', //默认控制器
    
    'a' =>  'index', //默认控制器动作
    
    'g' =>  '', //默认应用分组
    
    'urlType'   =>  '1', //url解析模式 
    //1: query方式 [*.php?c=controller&a=action&g=group&p0=str0&p1=str1&..] px是控制器动作action的形参名
    //2: pathinfo方式 [*.php/controller/action/group/p0/str0/p1/str1/../] px 是控制器动作需要的形参名
    //3: 混合方式 [*.php/?param=PATHINFO_DEPR*PATHINFO_DEPR*..] 兼容query方式和pathinfo方式 PATHINFO_DEPR是数据分隔符
    
    'PATHINFO_DEPR' =>  '/', //pathinfo方式下数据分隔符 若指定为 - 则url格式为 [*.php/group-controller-action-p0-str0-p1-str1/../]
);

/**
 *          定义模板引擎配置参数
 */
$config['system']['temp'] = array(
    'tempExpires'   =>  6000, //缓存时间
    
    'type'  =>  1, //解析模式 
    //
    //1: 通过内置解析器解析
    //2: 通过外置解析器解析
    
    'reg'   => array( //外置模板解析器 开发者可通过此处自定义模板解析的方式
        
    ),
);

/**
 *          定义系统当前字符集
 */
$config['charset'] = 'UTF-8';//简体中文

/**
 *          定义时区
 */
$config['timeZone'] = array( 
        'cn'                =>      'Etc/GMT-8', //8
    
        'jp'                =>      'Etc/GMT-9', //9
);

header("Content-Type: text/html; charset=". $config['charset']); //字符集
date_default_timezone_set($config['timeZone']['cn']); //时区

//* End of the file config.php 
//* File path : ./sys/conf