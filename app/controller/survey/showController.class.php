<?php

/**
 * @文件        showController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 12:37:16    
 * @文件版本    cx_survey v0.1
 */
class showController extends controller{
    
    /**
     *          用户投票界面初始化
     * @param type $param
     */
    public function surveyInit($param) {
        $surveyid = $param['surveyid'];//传入一个参数
        ff::$lib['session']->set('displayThisSurvey', $surveyid);
        if ( M('show')->table('survey')->where("surveyid='{$surveyid}'")->select() ) {
            $this->sid = $surveyid;
            $this->display('survey');
        } else {
            ff::$lib['session']->del('displayThisSurvey');
            echo "<script>alert(1);</script>";
            echo "<script>window.opener=null;window.open('','_self');window.close();</script>";
        }
    }
    
//    
//    public function getSurerInfo() {
//        $userid = ff::$lib['session']->get('userid');
//        $this->ajaxReturn(array('userid'=>$userid));
//    }
    
    /**
     * @名称        显示未发布问卷
     * @功能        从session里取出firmid和 userid;sendtime=0未发布
     *              若非管理员权限,则返回操作码:4001
     * @return     Json  数组{surveyid：“问卷id”，name：“问卷名”，type：“类型",}111代表没有未发布问卷;
     */    
    public function unissue(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid); 
        $userid= ff::$lib['session']->get('userid');
//        $userid=10000;
        $this->ajaxReturn(N('show')->unissue($firmid,$userid));
    }
    /**
     * @名称        显示发布问卷
     * @功能        从session里取出firmid和 userid，sendtime!=0已发布;type=0意向,type=1满意度;到表survey找isend,endtime比较 
     *              若非管理员权限,则返回操作码:4001
     * @return     Json数组{surveyid：“问卷id”，name：“问卷名”，type：“类型”，endtime：“结束收集时间”，progress：“进程”，status：“状态”}
     *              111代表没有已发布问卷;
     */      
    public function issue(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid); 
        $userid= ff::$lib['session']->get('userid');
//        $userid=10000;
        $this->ajaxReturn(N('show')->issue($firmid,$userid));
    }
    /**
     * @名称        显示已投票人
     * @功能        从前端POST来surveyid,从session里取出管理员的firmid;到表member取出员工的userid
     *              若非管理员权限,则返回操作码:4001
     * @return     json数组{name：“员工名”，userid：“员工id”}
     */
    public function showmember(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);     
        $surveyid=$_POST['surveyid'];
//        $surveyid="wsxedcrfvqaz";
        $this->ajaxReturn(N('show')->showmember($firmid,$surveyid)); 
    }
    /**
     * @名称        显示所有人员
     * @功能        从前端POST来surveyid,从session里取出管理员的firmid;到表member,user取出未参加投票的员工name,userid(排序)
     *              若非管理员权限,则返回操作码:4001
     * @return     json数组{name：“员工名”，userid：“员工id”};111代表公司没有员工
     */
    public function allmember(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);     
        $surveyid= $_POST['surveyid'];  
//        $surveyid= "wfxg4cku6rbdpCdc9mca";
        $this->ajaxReturn(N('show')->allmember($firmid,$surveyid));  
    }
    /**
     * @名称        修改问卷显示界面
     * @功能        前端POST来surveyid,从session里取firmid;
     *              到表survey里取type,name,pagenum;用surveyid到表page里取pageid,name,num ;用surveyid,pageid到表question取quid,name,opmod
     *              满意度型type=1,surveyid到表survey找datum;意向型type=0,用quid到表option取opid,name
     *              若非管理员权限,则返回操作码:4001
     * @return     type,name,pagenum;pageid,name,num;quid,name,opmod;opid,name;datum
     *              survey{"name":"问卷名","type":"问卷类型","pagenum":"页数","datum":"满意度分数基准","page1":{"pageid":"页id","question1":{"quid":"问题id","name":"问题名","option":{"opid":"选项id","name":"选项名"}}}}
     */
    public function showsurvey(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);     
        $surveyid= $_POST['surveyid'];  
//        $surveyid= "wfxg4cku6rbdpCdc9mca";        
        $this->ajaxReturn(N('show')->showsurvey($firmid,$surveyid));  
    }
    /**
     * @名称        用户显示问卷界面
     * @功能        从session里取firmid,userid;到表survey,answer取surveyid,name,type,endtime
     * @return     json数组{surveyid:"问卷id",name:"问卷名",type:"类型"}           
     */
    public function usersurvey(){
        $firmid= ff::$lib['session']->get('firmid');
        $firmid= md5($firmid);    
        $userid= ff::$lib['session']->get('userid');
//        $userid= 4005;
        $this->ajaxReturn(N('show')->usersurvey($firmid,$userid));
    }
    /**
     * @名称        用户答题界面
     * @功能        从前端POST来surveyid,type,name ;到表survey,page,question,option,ancache
     * @return     返回 json数组name(survey),pagenum,pageid,type,datum(满意度),quid,name(question),opid,
     *              survey{"name":"问卷名","type":"问卷类型","pagenum":"页数","datum":"满意度分数基准","page1":{"pageid":"页id","question1":{"quid":"问题id","name":"问题名","option1":{"opid":"选项id","name":"选项名"},"ancache":{"opid":"答案id"}}}}
     */
    public function answerface(){
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);  
        $surveyid=$_POST['surveyid'];
        $userid= ff::$lib['session']->get('userid');
//        $userid="jquery03";
//        $surveyid="ApbLfbfDAeacE5ye5m7w";
        $this->ajaxReturn(N('show')->answerface($firmid,$userid,$surveyid));
    }

    
    
}
