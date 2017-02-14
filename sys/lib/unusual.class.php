<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        unusual.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        异常处理类
 * @起始日期    2014-2-25  15:05:12    
 * @文件版本    1.2.5  
 */

/*用法*/
//        try {
//            if (!file_exists($controllerFile)) {
//                throw new Unusual();
//            }
//        } catch (Unusual $usl) {
//            $usl->controllerMsg($controller);
//        }
class Unusual extends Exception{
    
    /**
     *          控制器异常
     * @return \errorController
     */
    public function controllerMsg($message) {
        $controllerFile = SYS_CORE. '/view/errorController.class.php';
        require_once "$controllerFile";
        $obj = new errorController;
        $obj->missingController($message . "Controller", $this->getLine(), $this->getFile());
    }
}

//* End of the file unusual.class.php  
//* File path : ./sys/lib/