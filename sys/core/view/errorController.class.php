<?php

/**
 * @文件        errorController.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        (使用您组织的语言替换此处,用以描述该文件基本功能)
 * @起始日期    2014-2-25  14:45:48    
 * @文件版本    1.2.2   
 */
class errorController extends controller{
    
    public function missingController($msg, $line, $file) {
        echo "<title>forfree核心发生错误</title><style type='text/css'>*{ padding: 0; margin: 0; }html{ overflow-y: scroll; }body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }img{ border: 0; }.error{ padding: 24px 48px; }.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }h1{ font-size: 32px; line-height: 48px; }.error .content{ padding-top: 10px}.error .info{ margin-bottom: 12px; }.error .info .title{ margin-bottom: 3px; }.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }.error .info .text{ line-height: 24px; }.copyright{ padding: 12px 48px; color: #999; }.copyright a{ color: #000; text-decoration: none; }</style><body><div class='error'><p class='face'>ForFree</p><h1>控制器[$msg]不存在</h1><div class='content'><div class='info'><div class='title'><h3>错误位置</h3></div><div class='text'><p>FILE: $file &#12288;LINE: $line</p></div></div></div></div></body>";
        exit();
    }
    
    public function missingAction($msg, $line, $file) {
        echo "<title>forfree核心发生错误</title><style type='text/css'>*{ padding: 0; margin: 0; }html{ overflow-y: scroll; }body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }img{ border: 0; }.error{ padding: 24px 48px; }.face{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }h1{ font-size: 32px; line-height: 48px; }.error .content{ padding-top: 10px}.error .info{ margin-bottom: 12px; }.error .info .title{ margin-bottom: 3px; }.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }.error .info .text{ line-height: 24px; }.copyright{ padding: 12px 48px; color: #999; }.copyright a{ color: #000; text-decoration: none; }</style><body><div class='error'><p class='face'>ForFree</p><h1>控制器动作[$msg]不存在</h1><div class='content'><div class='info'><div class='title'><h3>错误位置</h3></div><div class='text'><p>FILE: $file &#12288;LINE: $line</p></div></div></div></div></body>";
        exit();
    }
}

//* End of the file errorController.php  
//* File path : ./ 
