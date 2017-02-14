<?php

/**
 * @文件        countController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 12:37:29    
 * @文件版本    cx_survey v0.1
 */
class countController extends controller{
    /**
     * @名称        用户显示结果界面
     * @功能        从session里取firmid;从表survey里取出满足ispublic=1的surveyid,name,type
     * @return     json数组{surveyid:"问卷id",name:"问卷名",type:"类型"}
     */
    public function showresult(){
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);
        $this->ajaxReturn(N('count')->showresult($firmid));
    }
    
    /**
     * @名称        用户显示特定问卷结果
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,从表tsurvey,tpage,tquestion,toption
     * @return     json数组{count":{"nowcount":"当前回答人数","allcount":"总人数","ratio":"比例"},"datum":"评分基准(满意度)","time":{"sendtime":"发布时间","endtime":"收集时间"},"ave":所有体平均分,
     *              "lowrank"(后三名):{"no1":{"quid":"问题id","score":"总分"},...},"highrank"(前三名):{"no1":{"quid":"问题id","score":"总分"},...},,"sum"(总分排名递增):{"no1":{"quid":"","score":""}}
     */
    public function suresult(){
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);
        $surveyid= $_POST['surveyid'];
        $type= $_POST['type'];
//        $surveyid= "zxcvbnm"; 
//        $type = 1;
        $this->ajaxReturn(N('count')->suresult($firmid,$surveyid,$type));
    }
    /**
     * @名称        显示特定问卷 详细信息
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,lv,userid从表intention,satisfy,survey,answer
     *              type=0意向,type=1满意度;
     * @return     {"qu1":{"quid":"问题id","opnum1":{"opid":选项id,"ratio":比率},"mychoose":{"opid":"答案id"}}
     */
    public function particular(){
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);
        $lv= ff::$lib['session']->get('lv');
        $userid= ff::$lib['session']->get('userid');
        $surveyid= $_POST['surveyid'];
        $type= $_POST['type'];
//        $surveyid= "zxcvbnm"; 
//        $type = 1;
//        $lv=1;
//        $userid=1025;
        $this->ajaxReturn(N('count')->particular($firmid,$surveyid,$type,$lv,$userid));
    }
    /**
     * @名称        显示特定问卷即时信息
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,userid从表intention,satisfy,survey,ancache
     *              type=0意向,type=1满意度;
     *              若非管理员权限,则返回操作码:4001
     * @return     {"qu1":{"quid":"问题id","opnum1":{"opid":选项id,"ratio":比率}}
     */
    public function temporary(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);
        $userid= ff::$lib['session']->get('userid');
        $surveyid= $_POST['surveyid'];
        $type= $_POST['type'];
//        $surveyid= "zxcvbnm"; 
//        $type = 1;
//        $userid=1025;
        $this->ajaxReturn(N('count')->temporary($firmid,$surveyid,$type,$userid));
    }
    
}



