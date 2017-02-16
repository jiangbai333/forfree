<?php

/**
 * @文件:       index.php
 * @作者:       b-jiang
 * @版本:       1.2.6
 * @创建时间:   2014-7-9 11:10:15
 */

require_once dirname(__FILE__). '/sys/conf/macro.php'; //宏配置
require_once dirname(__FILE__). '/sys/conf/config.php'; //配置文件
require_once SYS. '/forfree.php';
ff::run($config['system']); //创建应用

//End of file index.php
