<?php

/**   
 * @文件        macro.php   
 * @作者        b-jiang
 * @描述        服务器脚本
 * @功能        宏
 * @起始日期    2014-2-24  14:39:50    
 * @文件版本    1.2.5
 */

/**
 *          定义入口宏
 * 项目内所有服务器脚本文件均要在头部位置检验入口宏 FORFREE 若未定义 则脚本拒绝被访问
 * 拒绝访问的提示信息 用户可通过修改 __MSG__ 宏来添加自己的样式
 * *
 * 用法：针对 forfree.php 举例！
 *   if (!defined('FORFREE')) {require_once './conf/config.php';exit(__MSG__);}
 */
define('FORFREE', 1); //Just ForFree

/**
 *          定义提示信息宏
 */
define('__MSG__',
       "<title>！警告</title><style type='text/css'>*{ padding: 0; margin: 0; }html{ overflow-y: scroll; }body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }img{ border: 0; }.error{ padding: 24px 48px; }.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }h1{ font-size: 32px; line-height: 48px; }.error .content{ padding-top: 10px}.error .info{ margin-bottom: 12px; }.error .info .title{ margin-bottom: 3px; }.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }.error .info .text{ line-height: 24px; }.copyright{ padding: 12px 48px; color: #999; }.copyright a{ color: #000; text-decoration: none; }</style><body><div class='error'><p class='face'>ForFree</p><h1>连接错误</h1><div class='content'><div class='info'><div class='title'><h3>您想访问的页面为受保护页面 forfree拒绝为您解析</h3></div></div></div></div></body>"
); //入口错误 提示信息
define('__URL_TYPE_ERROR__',
       "<title>！警告</title><style type='text/css'>*{ padding: 0; margin: 0; }html{ overflow-y: scroll; }body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }img{ border: 0; }.error{ padding: 24px 48px; }.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }h1{ font-size: 32px; line-height: 48px; }.error .content{ padding-top: 10px}.error .info{ margin-bottom: 12px; }.error .info .title{ margin-bottom: 3px; }.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }.error .info .text{ line-height: 24px; }.copyright{ padding: 12px 48px; color: #999; }.copyright a{ color: #000; text-decoration: none; }</style><body><div class='error'><p class='face'>ForFree</p><h1>URL解析错误</h1><div class='content'><div class='info'><div class='title'><h3>您所指定的URL解析方式不存在 请修改配置文件中urlType属性</h3></div></div></div></div></body>"
); //URL解析模式错误 提示信息

/**
 *          定义系统路径宏
 * 核心文件路径宏 方便程序中对核心文件进行引用
 * 用户可根据需要裁减系统文件 定义系统文件树 但在进行上述操作后 请确定以下宏是否匹配
 */
define('SYS', substr(dirname(__FILE__), 0, -5)); //sys 驱动类 配置文件 公共函数库 核心组件 系统类库....
define('SYS_LIB', SYS . '/lib'); //系统类库
define('SYS_CORE', SYS . '/core'); //核心组件
define('SYS_COMMON', SYS . '/common'); //系统公共函数库
define('ROOT_PATH', substr(SYS, 0, -4)); //根目录
define('RUN_PATH', ROOT_PATH. '/run'); //运行状态目录
define('APP', ROOT_PATH . '/app'); //app 开发人员文档目录 
define('APP_LIB', APP . '/clib'); //开发者自定义类库及第三方类库
define('APP_C', APP . '/controller'); //C
define('APP_M', APP . '/model'); //M
define('VIEW', './view'); //V

/**
 *          定义超全局宏
 * 将超全局数组 $_SERVER 内的基本服务端信息转存 方便程序处理
 */
define('SRV_NAME', $_SERVER['SERVER_NAME']); //当前主机名 localhost
define('SRI_NAME', $_SERVER['SCRIPT_NAME']); //当前脚本的路径 /new/marico/index.php
define('REQ_METHOD', $_SERVER['REQUEST_METHOD']); //访问页面时的请求方法 GET HEAD POST PUT
define('QUY_STRING', $_SERVER['QUERY_STRING']); //查询字符串
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']); //当前运行脚本所在的文档根目录 C:/xampp/htdocs
define('SCT_FILENAME', $_SERVER['SCRIPT_FILENAME']); //当前执行脚本的绝对路径 C:/xampp/htdocs/forfree1.2.3/index.php
define('REQ_URI', $_SERVER['REQUEST_URI']); //访问此页面所需的URI /new/marico/index.php?a=asd
define('IP', $_SERVER['REMOTE_ADDR']); //正在浏览当前页面用户的IP地址 


//* End of the file macro.php  
//* File path : ./sys/conf
