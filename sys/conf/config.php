<?php

/**
 * @文件:       config.php
 * @作者:       b-jiang
 * @版本:       1.2.6
 * @创建时间:   2014-7-9 11:15:57
 */

/**
 *          系统配置参数
 * 通过此参数,将应用初始化过程中需要的默认信息传递给核心驱动类
 */
$config = array();

/**
 *          数据库配置
 */
$config['system']['db'] = array(
    'auto'          =>      false, //是否自动连接 是:true 否:false(默认)
    
    'host'          =>      'localhost', //主机
    
    'user'          =>      'root', //用户
    
    'password'      =>      '', //密码
    
    'port'          =>      3306, //端口号
    
    'database'      =>      'test', //数据库名
    
    'prefix'        =>      '', //数据表前缀
    
    'charset'       =>      'utf8', //数据字符集
);

/**
 *          邮件配置
 */
$config['system']['mail'] = array(
    'host'      =>  'smtp.exmail.qq.com', //主机
    
    'user'      =>  '', //用户
    
    'pwd'       =>  '', //密码
    
    'port'      =>  25, //端口号
    
    'nickName'  =>  'forfree', //昵称
    
    'homePage'    =>  "forfreeb.sinaapp.com" //主页
);

/**
 *          类库配置
 */
$config['system']['lib'] = array( 
    'autoLoad'	=>	true, //是否自动加载 是:true(默认) 否:false 
    
    'autoPath'  =>      SYS_LIB, //默认类库加载地址
    
    'autoLibs'  =>      array( //默认加载类
        'my'                =>      'app/clib/my.class.php'
    ),
    
    'shield'    =>      array( //屏蔽的类
        //'db'                =>      'sys/lib/Db.class.php',
    ),
);

/**
 *          路由器配置
 * 配置系统默认控制器 控制器动作 应用分组
 * 指定url解析模式
 */
$config['system']['router'] = array(
    'c' =>  'index', //默认控制器
    
    'a' =>  'index', //默认控制器动作
    
    'g' =>  '', //默认应用分组
    
    //url解析模式 
    //1: query方式 [index.php?c=controller&a=action&g=group&p0=str0&p1=str1&..] px是控制器动作action的形参名
    //2: pathinfo方式 [index.php/controller/action/group/p0=str0&p1=str1&..] px是控制器动作action的形参名
    'urlType'   =>  '2', 
    
    //该版本只能用 [?] 或者 [/]
    //pathinfo方式下数据分隔符 若指定为 ? 则url格式为 [index.php?controller?action?group?p0=str0&p1=str1&..]
    'PATHINFO_DEPR' =>  '?'
);

/**
 *          模板引擎配置
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
 *          系统字符集
 */
$config['charset'] = 'UTF-8';//简体中文

/**
 *          时区
 */
$config['timeZone'] = array( 
        'cn'                =>      'Etc/GMT-8', //8
    
        'jp'                =>      'Etc/GMT-9', //9
);

header("Content-Type: text/html; charset=". $config['charset']); //字符集
date_default_timezone_set($config['timeZone']['cn']); //时区

//End of file config.php