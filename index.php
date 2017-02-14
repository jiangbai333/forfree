<?php

/**   
 * @文件        index.php   
 * @作者        b-jiang
 * @描述        服务器脚本
 * @功能        入口文件
 * @起始日期    2014-2-13  14:23:37    
 * @文件版本    1.2.5
 */
require_once dirname(__FILE__). '/sys/conf/macro.php'; //宏配置
require_once dirname(__FILE__). '/app/macro/a.mac'; 
require_once dirname(__FILE__). '/sys/conf/config.php'; //配置文件
require_once SYS. '/forfree.php';
ff::run($config['system']); //创建应用

/* 入口文件 index.php 结束 */
/* 文件位置: ./index.php   */