<?php

/**
 * @文件        answerController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-7-7 18:27:54    
 * @文件版本    cx_survey v0.1
 */
class answerController extends controller{
    public function answer() {
        $quid = $_POST['qu'];
        $opid = $_POST['an'];
        $opid = explode("@#", $opid);
        N('answer')->answer($quid, $opid);
    }
}

?>
