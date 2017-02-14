<?php
//// 保存一天 
//    $lifeTime = 24 * 3600; 
//    session_set_cookie_params($lifeTime); 
//    session_start();
/**
 * @文件        userControllerController.class.php   
 * @描述        类文件
 * @功能        帐号管理：修改个人信息，添加用户，删除用户，导入用户
 * @起始日期    14:13:42    
 * @文件版本    cx_survey v0.1
 */
class userController extends controller {
    /**
     *          用户导入
     */
    public function userImport() {
        if(empty($_FILES['file']['name'])){
            exit('<script>alert(\'请选择文件\')</script>');
        }
        $data = file($_FILES["file"]['tmp_name']);
        $lines = count($data);
        $i = 0;
        $errorFlag = FALSE;
        $errorData = array();
        $userid = array();
        $password = array();
        $name = array();
        $email = array();
//        $part = array();
        while ($i < $lines){
            $line = explode(",",$data[$i++]);            
            $userid[$i] = $line[0];
            if( !preg_match(_USER_REG_, $line[0]) ) {
                $errorFlag = TRUE;
                $errorData[$i]['userid'] = "<span style='color:red;'>[</span>用户账号 <b>{$line[0]}</b> 格式不正确<span style='color:red;'>]</span>";
            } else if ( N('user')->touchUser($line[0]) ) {
                $errorFlag = TRUE;
                $errorData[$i]['userid'] = "<span style='color:red;'>[</span>用户账号 <b>{$line[0]}</b> 已存在<span style='color:red;'>]</span>";
            }            
            $password[$i] = $line[1];
            if( !preg_match(_PASS_REG_, $line[1]) ) {
                $errorFlag = TRUE;
                $errorData[$i]['pass'] = "<span style='color:red;'>[</span>用户 <b>{$line[0]}</b> 的密码 <b>{$line[1]}</b> 格式不正确<span style='color:red;'>]</span>";
            }            
            $name[$i] = $line[2];
            if( !preg_match(_CHAR_REG_, $line[2]) ) {
                $errorFlag = TRUE;
                $errorData[$i]['name'] = "<span style='color:red;'>[</span>用户 <b>{$line[0]}</b> 的姓名 <b>{$line[2]}</b> 格式不正确<span style='color:red;'>]</span>";
            }            
            $email[$i] = $line[3];
            if( !preg_match(_MAIL_REG_, $line[3]) ) {
                $errorFlag = TRUE;
                $errorData[$i]['mail'] = "<span style='color:red;'>[</span>用户 <b>{$line[0]}</b> 的email <b>{$line[3]}</b> 格式不正确<span style='color:red;'>]</span>";
            }
        }
        
        if ( $errorFlag ) {
            foreach ($errorData as $k => $v) {
                $k = $k < 10 ? '0'. $k : $k;
                echo $k. '行: ';
                foreach ($v as $k1 => $v1) {
                    echo $v1. ' ';
                }
                echo '<br><br>';
            }
        } else {
            $firmid = md5(ff::$lib['session']->get('firmid'));
            $data = array();
            for ($i = 1; $i <= $lines; $i++) {
                $data['userid'][] = $userid[$i];
                $data['name'][] = $name[$i];
                $data['password'][] = md5($password[$i]);
                $data['email'][] = $email[$i];
                $data['firmid'][] = $firmid;
            }
            N('user')->userImport($data, $lines, $firmid);
        }
    }
    
    /**
     *@名称         主页面初始化                                
     *@功能         登录后的主页面初始化，通过获取session里的权限来区分主页面是管理员(admin)还是普通用户(normal)界面
     */
     public function inital(){
        ff::$lib['session']->get('lv') == 0 ? $this->display('admin') : $this->display('normal');     
     }
    /**
     * @名称        显示用户名
     * @功能        从session中取登录信息并返回用户名数据
     * @return     $this->ajaxReturn()
     */    
    public function showname(){    
        $userid= ff::$lib['session']->get('userid'); //user没约定
        $firmid= ff::$lib['session']->get('firmid');
        $firmid= md5($firmid);       
        $name= M("user")->table("user")->field("name")->where("userid='{$userid}' and firmid='{$firmid}'")->select();
        $this->ajaxReturn($name);     
    }
    /**
     * @名称        注销
     * @功能        从session 中清空登录信息
     * @return     操作码1000代表完成
     */
    public function logout(){
        ff::$lib['session']->clear();
        $this->ajaxReturn(1000);
    }
   
    /**
     * @名称        显示管理员信息
     * @功能        从session总获得firmid和userid,通过firmid和userid在cx_user中找出管理者的信息，用户名、姓名、电邮并显示在弹窗 
                    若非管理员权限,则返回操作码:4001
     * @return     $this->ajaxReturn($start);  输出userid,name,email;返回的userid在更改个人信息使用到,前端要保存       
     */
    public function showinfo(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid=ff::$lib['session']->get('firmid');
        $userid=ff::$lib['session']->get('userid');
        $firmid=md5($firmid);
        $start= M("user")->table("user")->field("userid,name,email,password")->where("userid='{$userid}' and firmid='{$firmid}'")->select();        
        $this->ajaxReturn($start);
    }
    /**
     * @名称        修改管理员信息
     * @功能        从session获得userid和firmid在cx_user表中找出管理员信息，前端POST过来name,email,password,用update修改姓名(name),电邮(email)，密码（password）
                    若新密码与原密码不同changepass加1,
                    若非管理员权限,则返回操作码:4001;成功返回4002
     */
    public function updateinfo(){ 
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $userid= ff::$lib['session']->get('userid');
//        $userid=$_POST['userid'];
        $firmid=ff::$lib['session']->get('firmid');
        $firmid=md5($firmid);
        $name= $_POST["name"];
        $email= $_POST["email"];
        $password = $_POST["password"];  
        $password =md5($password);
        M("user")->table("user")->data(array('name'=>$name,'email'=>$email))->where("userid='{$userid}' and firmid='{$firmid}'")->update();           
        if($_POST["password"]!="1234"){
           M("user")->table("user")->data(array('password'=>$password))->where("userid='{$userid}' and firmid='{$firmid}'")->update();
           $passnum=  M("user")->table("user")->field("changepass")->where("userid='{$userid}' and firmid='{$firmid}'")->select();
           $passnum['changepass']++;
           M("user")->table("user")->data(array('changepass'=>$passnum['changepass']))->where("userid='{$userid}' and firmid='{$firmid}'")->update();   
        }       
        ff::$lib['session']->set(array('name'=>$name,'email'=>$email)); //修改后的 个人信息存到session     
        $this->ajaxReturn(4002);        
            }
    /**
     * @名称        用户首次登录显示信息
     * @功能        用session里的userid和firmid查看表cx_user里的changepass次数,若是0则必须修改密码
     * @return     返回布尔型,4001改密码 4002错 
     */
    public function firstpass(){
       $userid= ff::$lib['session']->get('userid');
       $firmid= ff::$lib['session']->get('firmid');
       $firmid= md5($firmid);
       $select= M("user")->table("user")->field("changepass")->where("userid='{$userid}' and firmid='{$firmid}'")->select();
       if($select['changepass']==0){
           $this->ajaxReturn(4001); //提示修改密码
       }  else {
           $this->ajaxReturn(4002);
       }
    }
    /**
     * @名称        用户首次登录修改密码
     * @功能        密码不得与初始密码重复，修改后changepass加1,取session里的firmid和userid,从表cx_user取原有密码,若与post的旧密码不同,返回false;
     *              若一致则用前端POST新密码(newpass)替换旧密码(oldpass)
     * @return     返回布尔型,4002是对(改密码) 或4001是错   
     */
    public function changepass(){
        $userid= ff::$lib['session']->get('userid');
        $firmid= md5(ff::$lib['session']->get('firmid'));
        $oldpass= md5($_POST["oldpass"]);
        $newpass= md5($_POST["newpass"]);
        $selpass=M("user")->table("user")->field("password")->where("userid='{$userid}' and firmid='{$firmid}'")->select();
        if($oldpass!=$selpass['password']){
           $this->ajaxReturn(4001);
        }else{
            $passnum= M("user")->table("user")->field("changepass")->where("userid='{$userid}' and firmid='{$firmid}'")->select();
            if($oldpass!=$newpass){     
                M("user")->table("user")->data(array('password'=>$newpass))->where("userid='{$userid}' and firmid='{$firmid}'")->update(); 
                $passnum['changepass']=$passnum['changepass']+1;
                M("user")->table("user")->data(array('changepass'=>$passnum['changepass']))->where("userid='{$userid}' and firmid='{$firmid}'")->update();  
                $this->ajaxReturn(4002);
            }else{
                $this->ajaxReturn(4001);
            }  
        }
       
      
    }
    /**
     * @名称        显示全部员工信息        
     * @功能        通过session里的 firmid和lv=1,到cx_user表找除了管理员以外的所有userid员工,把员工的userid和name显示在界面上并按姓名升序排列
                    若非管理员权限,则返回操作码:4001
     * @return     返回4002表示公司没有员工;返回Json 数组userid和name
     */
    public function showstaffs(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');
        $firmid= md5($firmid);
        $lv= "1";   
        $one= M("user")->table("user")->field("userid,name,email")->where("lv='{$lv}' and firmid='{$firmid}' ORDER BY name")->select();  
        if(M("user")->numRows==0){
            $this->ajaxReturn(4002);//公司没有员工
        }else{
            $this->ajaxReturn($one);
        }
    }
    /**
     * @名称        显示特定员工的详细信息(编辑员工)
     * @功能        通过前端POST过来的userid 和session里的firmid, 从user表里取出员工的用户名,姓名,密码显示为星号,电邮;从partman表里取出partid;用partid到part表中取name
                    若非管理员权限,则返回操作码:4001
     * @return     json数组,userid,name(用户的),partid,name(部门的),email,password
     */
    public function showonestaff(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $userid= $_POST["userid"];
//        $userid= 4005;
        $firmid= ff::$lib['session']->get('firmid');
        $firmid= md5($firmid);      
        $onestaff= M("user")->table("user")->field("userid,name,password,email")->where("userid='{$userid}' and firmid='{$firmid}'")->select();  
        $sql = "Select DISTINCT cx_part.partid,cx_part.name FROM `cx_part` JOIN `cx_partman` ON cx_part.partid=cx_partman.partid WHERE cx_partman.userid='{$userid}' and cx_partman.firmid='{$firmid}'";
        $onestaffpart= M("user")->query($sql);       
//从表cx_part和cx_partman里找出员工所在部门的name和 partid       
//        $onestaffpart= M("user")
//                ->table("partman")
//                ->field("cx_part.name,cx_part.partid")
//                ->join("cx_part ON cx_part.partid=cx_partman.partid")
//                ->where("cx_partman.userid='{$userid}' and cx_partman.firmid='{$firmid}'")
//                ->select();              
        $this->ajaxReturn($onestaff+ $onestaffpart);
    }
    /**
     * @名称        显示员工未加入的部门(编辑员工)
     * @功能        通过管理员session里的firmid查询表cx_part里本公司的部门(没有,一个,多个),通过POST过来的userid去表cx_partman
                    若非管理员权限,则返回操作码:4001
     * @return     返回json数组,partid和 name,返回1001是公司不存在部门
     */
    public function someparts(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001); 
        $firmid=ff::$lib['session']->get('firmid');
        $firmid= md5($firmid);
        $data = array();
        $part = array();       
        $userid= $_POST['userid'];
        $allpart= M("user")->table("part")->field("partid,name")->where("firmid='{$firmid}'")->select();
        if ( M("user")->numRows == 0 ) {
            $this->ajaxReturn(1001); //公司不存在部门
        }
        else if ( M("user")->numRows == 1 ) { 
            $part[0] = $allpart;//构成二维数组的形式
        } else {
            $part = $allpart;//输出二维数组
        }
        foreach ($part as $v) {//遍历 选取符合userid,firmid,partid的数据
            M("user")->table("partman")->field("*")->where("userid='{$userid}' and firmid='{$firmid}' and partid='{$v['partid']}'")->select();
            if ( M("user")->numRows == 0 ) {//若符合条件的数据不存在,则输出这条数据的partid和name 
                $data[] = $v;
            }
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * @名称        编辑员工信息(编辑员工)
     * @功能        用session里的userid,firmid和前端POST的员工name,email,partid(没有就传空),password去更新(update)原来的信息(showonestaff里的信息)
                    若非管理员权限,则返回操作码:4001
     * @return     布尔型,4002是对(添加成功) 或4001是错 
     */
    public function editonestaff(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid=ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid); 
//        $email=4566;
//        $name=7899;
//        $newuserid=1007;
//        $password=4444;
        $email= $_POST["email"];
        $name= $_POST["name"];                   
        $userid= $_POST["userid"];
        $password= $_POST["password"];
        $password= md5($password);
        $editstaff= M("user")->table("user")->data(array('email'=>$email,'password'=>$password,'name'=>$name))->where("userid='{$userid}' and firmid='{$firmid}'")->update();                            
    
        $this->ajaxReturn(4002);
        }

    /**
     * @名称        添加员工信息
     * @功能        通过弹出信息写入对应字段,前端POST来userid password email partid(没有就传空)到表user,partman,loginfo
                    若非管理员权限,则返回操作码:4001
     * @return     4002是对 (可以添加员工,没有重复的员工)或4001是错(员工已存在) ,4003是前端没有传部门,
     */
    public function addstaff(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $userid=$_POST['userid'];
        $name=$_POST['name'];
        $password=$_POST['password'];
        $password=md5($password);
        $email=$_POST['email'];
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid); 
//        $partid=$_POST['partid'];    
        
//        $userid=13;
//        $name=2;
//        $password=345;
//        $email=867879;
//        $partid=;
        
        $selectuser= M("user")->table("user")->field("userid")->where("userid='{$userid}' and firmid='{$firmid}'")->select();       
        if(!$selectuser){
            
            M("user")->table("user")->data(array('userid'=>$userid,'name'=>$name,'password'=>$password,'email'=>$email,'firmid'=>$firmid))->add();                
            M("user")->table("loginfo")->data(array('userid'=>$userid,'firmid'=>$firmid))->add();             
//            if($partid){
//                $array= explode(",",$partid);
//                foreach ($array as $v) {
//                $aaa['userid'][] = $userid;
//                $aaa['partid'][] = $v;
//                $aaa['firmid'][] = $firmid;
//                }  
//                
//                M("user")->table("partman")->data(array('userid'=>$aaa['userid'],'partid'=>$aaa['partid'],'firmid'=>$aaa['firmid']))->add();                 
//            }else{
//                $this->ajaxReturn(4003);
//                }        
        
                $this->ajaxReturn(4002);
            }else{
            $this->ajaxReturn(4001);
            }    
    }
    /**
     * @名称        显示所有部门(添加员工)
     * @功能        从管理员的session里取出本公司的firmid,
     * @return     返回json数组partid name;返回4002是没有部门；
     */  
    public function allparts(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid= ff::$lib['session']->get('firmid');//可取管理员session里的firmid
        $firmid= md5($firmid);         
        $parts=M("user")->table("part")->field("partid,name")->where("firmid='{$firmid}'")->select();//取出全部部门
        if($this->db->numRows==0){
            $this->ajaxReturn(4002);
        }else{
            $this->ajaxReturn($parts);
        }
    }   
    /**
     * @名称        删除员工信息
     * @功能        通过POST过来userid 和session  里的firmid 找到cx_user  cx_partman  cx_groupman  cx_loginfo  cx_member cx_ancache  cx_answer表中相关信息删除多用户删除同上
                    若非管理员权限,则返回操作码:4001
     */
    public function deletestaff(){
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $userid=$_POST['userid'];
        $firmid= ff::$lib['session']->get('firmid');
        $firmid= md5($firmid); 
        M("user")->table("user")->where("userid='{$userid}' and firmid='{$firmid}'")->delete();
        M("user")->table("partman")->where("userid='{$userid}' and firmid='{$firmid}'")->delete();
        M("user")->table("groupman")->where("userid='{$userid}' and firmid='{$firmid}'")->delete();
        M("user")->table("loginfo")->where("userid='{$userid}' and firmid='{$firmid}'")->delete();
        M("user")->table("member")->where("userid='{$userid}' and  firmid='{$firmid}'")->delete();
        M("user")->table("ancache")->where("userid='{$userid}'")->delete();
        M("user")->table("answer")->where("userid='{$userid}'")->delete();
        }                                       
}