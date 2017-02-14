<?php

/**
 * @文件        answerModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-7-7 18:28:19    
 * @文件版本    cx_survey v0.1
 */
class answerModel extends model{
    public function answer($quid, $opid) {
        $antime = ff::$lib['date']->showDateTime();
        $surveyid = ff::$lib['session']->get('displayThisSurvey');
        $userid = ff::$lib['session']->get('userid');
        $data = array();
        foreach ($opid as $k => $v) {
            $data['surveyid'][] = $surveyid;
            $data['userid'][] = $userid;
            $data['quid'][] = $quid;
            $data['opid'][] = $v;
            $data['antime'][] = $antime;
        }
        $this->db->table('ancache')->data(array('surveyid'=>$data['surveyid'],'userid'=>$data['userid'],'quid'=>$data['quid'],'opid'=>$data['opid'],'antime'=>$data['antime']))->add();
    }
}

?>
