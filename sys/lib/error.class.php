<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        error.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        错误处理
 * @起始日期    2014-2-25  16:24:30    
 * @文件版本    1.2.5
 */
final class error {
    private $level;
    private $msg;
    private $line;
    private $file;
    private $context;
    
    private $reg = array(
        '~u.*e:(.*)~i'  =>  '变量 \'$$1\' 未定义 forfree无法寻址 请检查[错误位置]以及与[错误文件]相关联的文件 确保 \'$$1\' 变量在使用前已定义 ',
        '~u.*t (.*) - .*~i'  =>  '无法识别常量 \'$1\' 请确定是否丢失变量符号 \'$\' 或者宏 \'$1\' 未定义 修正前forfree将 \'$1\' 按照字符串',
        '~in.*e\\((.*)\\): .*ry~'  =>  '引用 \'$1\' 文件时发生错误 forfree无法找到此文件',
        '~in.*\'(.*\.php)\'.*\\)~'  =>  'forfree无法在指定目录找到开放的\'$1\'文件',
    );
    public function __construct() {
        
    }
    
    /**
     * 错误处理
     * @param int $errno 错误等级
     * @param string $errstr 错误信息
     * @param path $errfile 出错文件
     * @param int $errline 出错位置
     * @param array $context 出错时已经存在的变量数组
     */
    public static function customError($errno, $errstr, $errfile, $errline, $context){ 
        $a = new self; //自身实例化
        $errfile=str_replace(getcwd(),"",$errfile);
        $errstr = preg_replace(array_keys($a->reg), $a->reg, $errstr); //错误信息转换
        echo    "<hr><b style='color: deeppink;'>警告:</b> [<small style='color: tomato;'>错误报告级别 $errno</small>] ".
                "[<small style='color: tomato;'>错误文件:$errfile</small>]". 
                "[<small style='color: tomato;'>错误位置 line:$errline</small>]<br>". 
                "<b style='color: deeppink;'>诊断结果:<small style='color: plum;font-family:Georgia,Serif;'>$errstr</small></b><hr>";
    }
}
set_error_handler(array('error', 'customError')); //设置默认错误处理方法

//* End of the file error.class.php  
//* File path : ./sys/lib