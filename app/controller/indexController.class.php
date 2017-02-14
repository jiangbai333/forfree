<?php

/**
 * @文件        indexController.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        
 * @起始日期    2014-1-7  17:07:28    
 * @文件版本    cx_survey v0.1
 */
class indexController extends controller{
    /**
     *          登录页面初始化，渲染
     * @param string $param 公司代码
     */
    public function index($param) {
        if ( !empty($param) ) { //传没传公司代码
            $firmId = md5($param['p0']); 
            $firm = M("index")->table("firm")->where("firmId='{$firmId}'")->select(); //检索公司
            M("index")->numRows == 1 ? //如果有
                    ff::$lib['session']->set(array('firmid'=>$param['p0'],'firmkey'=>$firm['id'],'firm'=>$firm['name'])) : ff::$lib['session']->clear(); //存进session
        } else {
            ff::$lib['session']->clear();
        }
        $this->title = TITLE_TEXT;
        $this->logo = array(
            'text'  =>  LOGO_TEXT,
            'image' =>  LOGO_IMAGE,
        );
        $this->display();
    }
    
    /**
     *          APIs 检查公司 
     * 若进入网站时指定了公司id,则返回一个json数组[{firm: "测试公司1",firmid: "test1"}]
     * 否则返回错误代码: 1000 session中不存在公司
     */
    public function checkFirm() { 
        if( (bool)ff::$lib['session']->get('firmid') ) { //公司是否存在session中
            $data = array(
                'firm' => ff::$lib['session']->get('firm'),
                'firmid' => ff::$lib['session']->get('firmid'),                
            );
            $this->ajaxReturn($data); //存在返回数据
        } else {
            $this->ajaxReturn(1000); //不存在返回１０００
        }
    }
    
    /**
     *          用户登录判断
     * 需要前端传入userid, password, firmid. firmid不是不要参数,当用户从公司页面访问时,可以不传firmid
     * 返回操作码：
     * 1001：检索公司发生错误
     * 1002：用户或密码错误，或用户不存在
     * 1000：正确
     */
    public function login() {
        $userid = $_POST['userid'];
        $password = md5($_POST['password']);
        if ( isset($_POST['firmid']) ) {
            $firmid = md5($_POST['firmid']);
            $firm = M("index")->table("firm")->where("firmId='{$firmid}'")->select(); //检索公司
            if ( M("index")->numRows == 1 ) {
                ff::$lib['session']->set(array('firmid'=>$_POST['firmid'],'firmkey'=>$firm['id'],'firm'=>$firm['name']));
            } else {
                $this->ajaxReturn(1001); 
            }
        }
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $user = M('index')->table('user')->where("userid='{$userid}' AND firmid='{$firmid}' AND password='{$password}'")->select();
        if ( M('index')->numRows != 1) {
            $this->ajaxReturn(1002);
        } else {
            ff::$lib['session']->set(array('userid'=>$user['userid'],'name'=>$user['name'],'password'=>$password,'email'=>$user['email'],'lv'=>$user['lv']));
            $dateTime = ff::$lib['date']->showDateTime();
            $ip = IP;
            M('index')->table('loginfo')->data(array('lastip'=>$ip,'lasttime'=>$dateTime))->where("userid='{$userid}' AND firmid='{$firmid}'")->update();
            $this->ajaxReturn(1000);
        }
    }
}
