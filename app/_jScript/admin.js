/**
    * @文件        admin.js   
    * @描述        
    * @功能               
    * @作者        杜旭普
    * @创建日期    2014-5-28 14:26:57
 */

/**
    * @函数       管理员页面初始化  
    * @描述       登陆成功后显示 一系列处理
    * @功能       加载左侧菜单栏   
 */
$(function(){
    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > span').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
    
/**
    * @函数       
    * @描述       第一次登陆页面提示修改密码
    * @功能          
 */
    $.post(
        './index.php?a=firstpass&g=user&c=user',
        {},
        function(data, textStatus) {
            if(data === 4001){
                $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '修改密码',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['250px','275px' ],
                    page: {
                        dom: '#update_first_pass'
                    }, 
                    success: function(){
                        layer.shift('top'); //上边动画弹出
                    }
                }); 
            } 
        },
        'json'
    );
        
/**
    * @函数       注销按钮  
    * @描述       清空session数据，返回登陆页面
    * @功能          
 */
$("#logoff").on('click',function(){ 
    $.post(
        './index.php?a=logout&g=user&c=user',
        {},
        function(data, textStatus) {
            if ( data === 1000 ) {
             window.location.href="./index.php";
            }
        },
        'json'
    );
}); 

/**
    * @函数         
    * @描述       显示登录用户信息
    * @功能          
 */
   $.post(
        './index.php?a=showname&g=user&c=user',
        {},
        function(data, textStatus) {
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限1！', 3, !1);
                setTimeout(function(){
                layer.close(i);
                }, 1000);
            }
            else{
             $('#show_name').append("<b>" + data.name + "</b>");
            }
        },
        'json'
    );

 /**
    * @函数         
    * @描述       修改个人信息弹窗
    * @功能          
 */  
   $.post(
        './index.php?a=showinfo&g=user&c=user',
        {},
        function(data, textStatus) {
             if(data.action==='ture'){

                        alert('操作成功!'); 

                  }
              else{     
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限2！', 3, !1);
                setTimeout(function(){
                layer.close(i);
                }, 1000);
            }
            else{
            $('#update_info_show').append('<div><span class="center_box_span">用户名:</span><input id="update_manager_id" class="center_box_input" maxlength="15" type="text" value=" '
                    + data.userid + '"/><font color=red>*</font></div><div><span class="center_box_span">姓名:</span><input id="update_manager_name" class="center_box_input" maxlength="10" type="text" value="'  
                    + data.name + '"/><font color=red>*</font></div><div><span class="center_box_span">新密码:</span><input id="update_manager_npwd" class="center_box_input" maxlength="32" type="password"/></div>\n\
                    <div><span class="center_box_span">确认新密码:</span><input id="update_manager_qnpwd" class="center_box_input" maxlength="32" type="password"/></div>\n\
                    <div><span class="center_box_span">电子邮件:</span><input id="update_manager_email" class="center_box_input" type="text" value="'
                            + data.email + '"/><font color=red>*</font></div><div><input id="update_manager_submit" class="btn btn_primary" type="submit" onclick="update_info_submit();"/></div>');
           
            }}
          },
            'json'
        );
  $('#update_info').click(function() {
       $.layer({
            type: 1,
//          maxmin: true,
//          shadeClose: true,
            title: '修改个人信息',
            shade: [0.5,'#000'],
            offset: ['50px',''],
            area: ['400px','500px' ],
            page: {
                dom: '#update_info_show'
            }, 
            success: function(){
                layer.shift('top'); //上边动画弹出
            }
        }); 
    });
    

 /**
    * @函数         
    * @描述       用户管理按钮淡入淡出层(同时显示全部员工信息)
    * @功能          
 */  
        $.post(
            './index.php?a=showstaffs&g=user&c=user',
            {},
            function(data, textStatus) {
                 if(data.action === 'ture'){
                        alert('操作成功!'); 
                  }
              else{     
                if ( data === 4001 ) {
                    var i = layer.alert('您没有权限3！', 3, !1);
                    setTimeout(function(){
                    layer.close(i);
                    }, 1000);
                }
                else{/*遍历json数据 例子
                    $.each(data,function(index,item){ 
                        alert("userid:"+item.userid+",name:"+item.name); 
                    });*/
                    $.each(data,function(index,item){
//                        alert("userid:"+item.userid+",name:"+item.name); 
                       $('#show_user_tbody').append("<tr  class='myclass'><td>"+item.userid+"</td><td>" +item.name+"</td><td>" +item.email+"</td><td><a href='#' id='' class='button green small'>编辑</a></td><td><a href='#' id='b_"+item.userid+"' class='button orange small' onclick='delete_worker(this);'>删除</a></td><td><input name='subBox' type='checkbox'/></td></tr>");
                       $('#show_group_member_all_tbody').append("<tr  class='myclass'><td>"+item.userid+"</td><td>" +item.name+"</td><td><a href='#' id='all_"+item.userid+"' class='' onclick='add_group_member_submit(this);'>添加</a></td></tr>");
                       $('#show_group_member_all_show_tbody').append("<tr  class='myclass'><td>"+item.userid+"</td><td>" +item.name+"</td><td><a href='#' id='bbb_"+item.userid+"' class='' onclick='add_group_member_submit_bbb(this);'>添加</a></td></tr>");

                    });
                }
            }
        },
            'json'
        );

  $('#user_manager').click(function() {
        $('.right_box').addClass('hide');
        $('#user_show').removeClass('hide');
        $('#user_show').animate({height:"500px",width:"77%"},300);
        
/*显示一个人员信息*/
    setTimeout(function(){      
        $("#show_user_tbody tr").each(function(index, value){   
            var r = $(this).children().eq(3);
            var userid=r.parent().children().eq(0).text();
            var name=r.parent().children().eq(1).text();
            var email=r.parent().children().eq(2).text();
            r.on("click",function(){
                $('#show_one_staff').empty().append('<div><span class="center_box_span">用户名:</span><input id="" class="center_box_input" maxlength="15" disabled="disabled" type="text" value=" '
                    + userid + '"/><font color=red>不可更改</font></div><div><span class="center_box_span">姓名:</span><input class="center_box_input" maxlength="10" type="text" name="name" value="'  
                    + name + '"/><font color=red>*</font></div><div><span class="center_box_span">email:</span><input id="" class="center_box_input" name="email" type="text" value="'
                    + email + '"/></div><div><span class="center_box_span">新密码:</span><input class="center_box_input" maxlength="32" name="pass" type="password"/></div>\n\
                    <div><span class="center_box_span">确认新密码:</span><input id="" class="center_box_input" maxlength="32" name="npass" type="password"/></div>\n\
                    <div><input  id="update_'+userid+'" class="btn btn_primary" type="submit" onclick="update_info_worker(this);"/></div>');
                
                $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '修改人员信息',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['400px','500px' ],
                    page: {
                        dom: '#show_one_staff'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
            }); 
        });  
    }, 1500);
    
});
/*选择复选框*/
        $("#checkallbox").click(function() {
//            $('input[name="subBox"]').attr("checked",this.checked);
$("input[name='subBox']").prop("checked", this.checked);
        });
//        var $subBox = $("input[name='subBox']");
//        $subBox.click(function(){
//            $("#checkallbox").attr("checked",$subBox.length === $("input[name='subBox']:checked").length ? true : false);
//        });
$("input[name='subBox']").click(function() {
    var $subs = $("input[name='subBox']");
    $("#checkallbox").prop("checked" , $subs.length === $subs.filter(":checked").length ? true :false);
  });



  /**
    * @函数         
    * @描述       分组管理按钮淡入淡出层
    * @功能          
 */  
     $.post(
          './index.php?g=user&c=group&a=showGroup',
        {},
        function(data, textStatus) {
            if ( data === 1001 ) {
                var i = layer.alert('您没有权限4！', 3, !1);
                setTimeout(function(){
                    layer.close(i);
                }, 1000);
            }
            else{
                $.each(data,function(index,item){
                    $('#show_group_tbody').append("<tr class='myclass'><td><a href='#' id='update_"+item.groupid+"' class='button green small' onclick='group_group_update(this);'>编辑</a></td><td><a href='#' id='delete_"+item.groupid+"' class='button orange small' onclick='group_group_delete(this);'>删除</a></td><td><input  id='group_"+item.groupid+"'  class='button pink small' type='button' onclick='group_member_show(this);' value='"+item.name+"'/></td></tr>");
                });
            }
        },
        'json'
        );
  $('#group_manager').click(function(){
      $('.right_box').addClass('hide');
      $('#group_show').removeClass('hide');
      $('#group_show').animate({height:"500px",width:"77%"},300);
  });  
  $("#add_group_member_name").focus(function () {
    $("#group_member_all_show").css('display','inline');
    $('#add_group_member_name').css('display','none');
    $('#show_group_member_select_table').css('display','inline');
    });
//$("#add_group_member_name").blur(function () {
//  $("#group_member_all_show").css('display','none');
//});
$('#add_group_id').focus(function(){
    $('#group_member_all_show').css('display','none');
    $('#add_group_member_name').css('display','inline');
});
   /**
    * @函数         
    * @描述       部门管理按钮淡入淡出层
    * @功能          
 */  
     $.post(
          './index.php?g=user&c=part&a=showPart',
        {},
        function(data, textStatus) {
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限5！', 3, !1);
                setTimeout(function(){
                    layer.close(i);
                }, 1000);
            }
            else{
                $.each(data,function(index,item){
                    $('#show_part_tbody').append("<tr class='myclass'><td><a href='#' id='' class='button green small'>编辑</a></td><td><a href='#' id='' class='button orange small'>删除</a></td><td><a href='#' id='' class='button pink small'>"+item.name+"</a></td></tr>");
                });
            }
        },
        'json'
        );
  $('#part_manager').click(function() {
        $('.right_box').addClass('hide');
        $('#part_show').removeClass('hide');
        $('#part_show').animate({height:"500px",width:"77%"},300);
    });
/**
    * @函数         
    * @描述       新建问卷管理按钮淡入淡出层
    * @功能          
 */  
  $('#new_survey').click(function() {
//        $('.right_box').addClass('hide');
//        $('#new_survey_show').removeClass('hide');
//        $('#new_survey_show').animate({height:"500px",width:"77%"},1000);
        $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '新建问卷',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['400px','430px' ],
                    page: {
                        dom: '#new_survey_show'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
    });
/**
    * @函数         
    * @描述       问卷管理按钮淡入淡出层
    * @功能          
 */  
  $('#manager_survey').click(function() {
        $('.right_box').addClass('hide');
        $('#manager_survey_show').removeClass('hide');
        $('#manager_survey_show').animate({height:"500px",width:"77%"},1000);
    });
    /**
    * @函数         
    * @描述       新建模板问卷按钮淡入淡出层
    * @功能          
 */  
  $('#template_survey').click(function() {
//        $('.right_box').addClass('hide');
//        $('#template_survey_show').removeClass('hide');
//        $('#template_survey_show').animate({height:"500px",width:"77%"},1000);
         $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '新建模板问卷',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['400px','430px' ],
                    page: {
                        dom: '#template_survey_show'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
    });
    /**
    * @函数         
    * @描述       问卷统计按钮淡入淡出层
    * @功能          
 */  
  $('#count_survey').click(function() {
        $('.right_box').addClass('hide');
        $('#count_survey_show').removeClass('hide');
        $('#count_survey_show').animate({height:"500px",width:"77%"},1000);
    });
/*右下角弹窗*/
//$.layer({
//    type: 2,
//    closeBtn: false,
//    shadeClose: true,
//    shade: [0.1, '#fff'],
//    time: 3,
//    iframe: {
//        src: 'http://www.baidu.com'
//    },
//    title: false,
//    area: ['300px','250px'],
//    success : function(){
//        layer.shift('right-bottom', 500);
//    }, end : function(){
//       
//            area: ['1024px', ($(window).height() - 110) +'px']
//        
//    }
//});

 });
 
 /**
    * @函数       修改个人信息提交函数 
    * @描述       判断密码是否修改依据特殊关键字
    * @功能          
 */ 
 function update_info_submit(){
        var name = document.getElementById('update_manager_name').value;
        var userid = document.getElementById('update_manager_id').value;
        var pass = '1234';
        var npass = document.getElementById('update_manager_npwd').value;
        var qnpass = document.getElementById('update_manager_qnpwd').value;
        var email = document.getElementById('update_manager_email').value;
        var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
          if (!reg.test(email)) {
              var i = layer.alert('您填写的邮箱格式不正确,请重新填写！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },2000);
             return false;}
//        alert(pass+npass+'#@'+qnpass);
//        alert(name + pass +npass+qnpass+email);
        if(name === "" || userid ==="" || email===""){
            var i = layer.alert('请检查必填选项是否为空！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },2000);
        }
        else if(npass === qnpass && npass !== ''&&npass.length >= 6 &&qnpass.length >= 6){
            $.post( './index.php?a=updateinfo&g=user&c=user',{'name':name,'password':npass,'email':email},
                function(data, textStatus) {
                    if ( data === 4001 ) {
                        var i = layer.alert('您没有权限6！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                       var ii = layer.load('保存中..');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                            layer.close(ii);
                        }, 1500);
                        location.reload();
                    }
                },
                'json'
            );
        }
        else if(pass === '1234'&& npass === ''){
            $.post( './index.php?a=updateinfo&g=user&c=user',{'name':name,'password':pass,'email':email},
                function(data, textStatus) {
                    if ( data === 4001 ) {
                        alert(data.name);
                        var i = layer.alert('您没有权限7！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                        var ii = layer.load('保存中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1500);
                        location.reload() ;
                    }
                },
                'json'
            );
        }
        else{
            if(npass.length < 6 || qnpass.length <6){
                var i = layer.alert('您输入的密码必须大于6位！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1500);
            }else{
                var i = layer.alert('您两次输入的密码不一致！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1500);
            }
        }
    }
    
 /**
    * @函数       修改个人信息提交函数 
    * @描述       判断密码是否修改依据特殊关键字
    * @功能          
 */ 
 function update_first_submit(){
     var oldpass = document.getElementById('update_first_pwd').value;
     var npass = document.getElementById('update_first_npwd').value;
     var newpass = document.getElementById('update_first_qnpwd').value;
      if(npass === "" || newpass ==="" ){
            var i = layer.alert('密码不能为空！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
        else if(npass !== newpass){
            var i = layer.alert('两次输入的密码不一致！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
        else if(npass.length < 6 || newpass.length <6){
                 var i = layer.alert('您输入的密码必须大于6位！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1500);
            }
            else {
                alert(oldpass + newpass);
                 $.post( './index.php?a=changepass&g=user&c=user',{'oldpass':oldpass,'newpass':newpass},
                function(data, textStatus) {
                    if ( data === 4001 ) {
                        var i = layer.alert('修改密码失败！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                       var ii = layer.load('提交中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1500);
                        location.reload() ;
                    }
                },
                'json'
            );
        }
 }   

/*添加账号按钮*/
function add_account(){
                $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '添加人员信息',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['400px','500px' ],
                    page: {
                        dom: '#add_account_div'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
        }

/*添加账号提交按钮*/
function add_info_submit(){
    var userid =  $('#add_manager_id').val();
    var name =  $('#add_manager_name').val();
    var password =  $('#add_manager_npwd').val();
    var npassword =  $('#add_manager_qnpwd').val();
    var email =  $('#add_manager_email').val();
     if(userid === "" || name ==="" || password === "" || npassword ==="" ){
            var i = layer.alert('信息不能为空！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
        
        else if(password !== npassword){
            var i = layer.alert('两次输入的密码不一致！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
        else if(password.length < 6 || npassword.length <6){
                 var i = layer.alert('您输入的密码必须大于6位！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1500);
            }
            else { 
                 $.post( './index.php?a=addstaff&g=user&c=user',
                 {'userid':userid,'name':name,'password':password,'email':email},
                
                function(data, textStatus) {
                    if ( data === 4001 ) {
                        var i = layer.alert('修改密码失败！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                       var ii = layer.load('提交中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1500);
                        location.reload() ;
                    }
                },
                'json'
            );
        }
}

/*导入账号按钮*/
function import_account(){
                $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '导入人员信息',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['500px','350px' ],
                    page: {
                        dom: '#import_account_div'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
        }
        
/*新建问卷*/
function add_survey(){
    alert('d');
}

/*选择评分等级*/
function select_grade(){
    var survey_type = document.getElementById("survey_type");
    var str = survey_type.options[survey_type.selectedIndex].id;
	if(str ==="satisfaction"){ 
            document.getElementById("survey_grade").style.display="block";
        }
	else{
            document.getElementById("survey_grade").style.display="none";
        }
}

/*删除员工行*/
function delete_worker(id) {
    $.layer({
    shade: [0],
    area: ['auto','auto'],
    dialog: {
        msg: '您确定要删除此员工？',
        btns: 2,                    
        type: 4,
        btn: ['确定','取消'],
        yes: function(){
            var userid = $(id).attr('id').split("_")[1];
      var d = $(id).parent().parent().remove();
//      alert(d);
       $.post(
          './index.php?g=user&c=user&a=deletestaff',
        {'userid':userid},
        function(data, textStatus) {
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限8！', 3, !1);
                setTimeout(function(){
                    layer.close(i);
                }, 1000);
            }
            else{
//                   d.remove();
//                $.each(data,function(index,item){
//                    $('#show_part_tbody').remove('');
//                });
            }
        },
        'json'
        );
layer.msg('删除成功', 1, function(){
   
});
        }, no: function(){
            
        }
    }
});
      
    
//  siblings
}
/*编辑账号提交按钮*/
function update_info_worker(id){
   var userid = $(id).attr('id').split("_")[1];
   var name = $("input[name='name']").val();  
   var email = $("input[name='email']").val();  
   var password = $("input[name='pass']").val();  
   var npass = $("input[name='npass']").val();  
   if( password !== npass){
            var i = layer.alert('两次输入的密码不一致！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
            else {
                 $.post( './index.php?g=user&c=user&a=editonestaff',
                 { 'userid':userid,'name':name,'email':email,'password':password },
                function(data, textStatus) {
                    if ( data === 4001 ) {
                        var i = layer.alert('您没有权限8！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                            var ii = layer.load('提交中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1500);
                        location.reload() ;
                    }
//                       var ii = layer.load('提交中...');
//                //此处用setTimeout演示ajax的回调
//                      setTimeout(function(){
//                        layer.close(ii);
//                    }, 1000);
//                     var i = parent.layer.getFrameIndex(window.name);
//                     alert(i);
//            parent.layer.close(i);
                      
        
                    
                },
                'json'
            );
        }
     
}
/*分组显示*/
function group_member_show(id){
    var groupid = $(id).attr('id').split("_")[1]; 
    
    $.post(
          './index.php?g=user&c=group&a=showGroupMember',
        {'groupid':groupid},
        function(data, textStatus) {
            if ( data === 1001 ) {
                var i = layer.alert('分组里没有成员！', 3, !1);
                setTimeout(function(){
                    layer.close(i);
                }, 1000);
            }
            else{
                
                $('#show_group_member_tbody').empty();/*删除以前事件，解决多次post问题*/
                $.each(data,function(index,item){
                    $('#show_group_member_tbody').append("<tr class='rightclass'><td><a href='#' id='' class='button orange small'>删除</a></td><td><a href='#' id='' class='button pink small' onclick=''>"+item.name+"</a></td></tr>");
                });
               
            }
           
        },
        'json'
        ); 
//    $("#$(id).attr('id')").attr('disabled',"true");
//   document.getElementById("$(id).attr('id')").disabled="disable";
}
/*添加分组*/
function add_group_submit(){
             $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '添加分组',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['600px','500px' ],
                    page: {
                        dom: '#add_group_div'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
}
/*分组增加成员*/
function add_group_member_submit(id){
     var groupid = $(id).attr('id').split("_")[1]; 
     var d = $(id).parent().parent().remove();
     var userid = $(id).parent().parent().children().eq(0).text();
     var name = $(id).parent().parent().children().eq(1).text();
//     alert(userid+name);
//    var str = $('#add_group_member_name').val()+userid+"&nbsp;"+name+"<br>";
//         $('#add_group_member_name').val(str);
       $('#show_group_member_select_tbody').append("<tr  class='myclass'><td>"+userid+"</td><td class='group_member_class'>" +name+"</td><td><a href='#' id='select_"+userid+"' class='button green small' onclick='add_group_member_delete(this);'>删除</a></td></tr>");
//     alert(strr);
}
/*分组删除成员*/
function add_group_member_delete(id){
     var groupid = $(id).attr('id').split("_")[1]; 
    
     var d = $(id).parent().parent().remove();
       var userid = $(id).parent().parent().children().eq(0).text();
     var name = $(id).parent().parent().children().eq(1).text();
     $('#show_group_member_all_tbody').append("<tr  class='myclass'><td>"+userid+"</td><td>" +name+"</td><td><a href='#' id='select_"+userid+"' class='button green small' onclick='add_group_member_submit(this);'>添加</a></td></tr>");

}
/*分组提交成功按钮*/
function add_group_member_button(){
    var name = $('#add_group_id').val(); 
    var str="";
    $("#show_group_member_select_tbody tr").each(function(){   
//        var userid = $(this).find('td').eq(0).text();
//        var name = $(this).find('td').eq(1).text();
        str+=$(this).find('td').eq(0).text()+"@#";
    });
    var groupMember = str.slice(0,-2);
   if( name === ''){
            var i = layer.alert('组名不能为空！',3,!1);
                setTimeout(function(){
                    layer.close(i);
                },1500);
        }
            else  {
                $.post( './index.php?g=user&c=group&a=addGroup',
                 { 'name':name,'groupMember':groupMember },
                function(data, textStatus) {
                    if ( data === 1001 ) {
                        var i = layer.alert('组已经存在！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                    else{
                        
                        var ii = layer.load('提交中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1500);
                        location.reload() ;
                    }
                },
                'json'
            );
                }
}

/*删除分组*/
function group_group_delete(id) {
    $.layer({
    shade: [0],
    area: ['auto','auto'],
    dialog: {
        msg: '您确定要删除此员工？',
        btns: 2,                    
        type: 4,
        btn: ['确定','取消'],
        yes: function(){
            var groupid = $(id).attr('id').split("_")[1];
      var d = $(id).parent().parent().remove();
//      alert(d);
       $.post(
          './index.php?g=user&c=group&a=deleteGroup',
        {'groupid':groupid},
        function(data, textStatus) {
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限8！', 3, !1);
                setTimeout(function(){
                    layer.close(i);
                }, 1000);
            }
            else{
//                   d.remove();
//                $.each(data,function(index,item){
//                    $('#show_part_tbody').remove('');
//                });
            }
        },
        'json'
        );
layer.msg('删除成功', 1, function(){
   
});
        }, no: function(){
            
        }
    }
});
      
    
//  siblings
}

/*编辑分组信息*/
function group_group_update(id){
     var groupid = $(id).attr('id').split("_")[1];
     var groupname = $(id).parent().parent().children().eq(2).children().val();
    $.post(
        './index.php?a=showGroupMember&g=user&c=group',
        {'groupid':groupid},
        function(data, textStatus) {     
            if ( data === 4001 ) {
                var i = layer.alert('您没有权限2！', 3, !1);
                setTimeout(function(){
                layer.close(i);
                }, 1000);
            }
            else{
                $('#update_group_div').empty();    
                $('#update_group_div').append('<div class="float_left_place"><div><span class=" ">分组名:</span><input id="update_group_id" class="center_box_input"  type="text" value=" '+groupname+'"/><font color=red>*</font></div><div><span class=" ">分组成员:</span><div><table id="show_group_member_show_table" border="1" style=""><thead><th>用户名</th><th>姓名</th><th></th></thead><tbody id="show_group_member_show_tbody"></tbody></table></div><div><input id="update_group_submit" class="btn btn_primary" type="submit" onclick="update_group_member_button();"/></div></div></div><span class=" ">全体用户:</span><div id="group_member_all_show_show"><table border="1"><thead><th>用户名</th><th>姓名</th><th></th></thead><tbody id="show_group_member_all_show_tbody"></tbody></table></div>');
 
               $.each(data,function(index,item){
           $('#show_group_member_show_tbody').append("<tr  class='myclass'><td>"+item.userid+"</td><td class='group_member_class'>" +item.name+"</td><td><a href='#' id='aaa_"+data.userid+"' class='button green small' onclick='add_group_member_delete_aaa(this);'>删除</a></td></tr>");
       });
                $.post(
            './index.php?a=showGroupOtherMember&g=user&c=group',
            {'groupid':groupid},
            function(data, textStatus) {
                 if(data.action === 'ture'){
                        alert('操作成功!'); 
                  }
              else{     
                if ( data === 4001 ) {
                    var i = layer.alert('您没有权限3！', 3, !1);
                    setTimeout(function(){
                    layer.close(i);
                    }, 1000);
                }
                else{
                    
                       $.each(data,function(index,item){
                        $('#show_group_member_all_show_tbody').append("<tr  class='myclass'><td>"+item.userid+"</td><td>" +item.name+"</td><td><a href='#' id='bbb_"+item.userid+"' class='button green small' onclick='add_group_member_submit_bbb(this);'>添加</a></td></tr>");
                       

                    });
                }
            }
        },
            'json'
        );

               }
          },
            'json'
        );
     $.layer({
                    type: 1,
        //          maxmin: true,
        //          shadeClose: true,
                    title: '修改分组',
                    shade: [0.5,'#000'],
                    offset: ['50px',''],
                    area: ['600px','500px' ],
                    page: {
                        dom: '#update_group_div'
                    }, 
                    success: function(){
        //                layer.shift('top'); //上边动画弹出
                    }
                }); 
}

function a(){
//     var groupid = $(id).attr('id').split("_")[1]; 
//    
//    $.post(
//          './index.php?g=user&c=group&a=showGroupMember',
//        {'groupid':groupid},
//        function(data, textStatus) {
//            if ( data === 1001 ) {
//                var i = layer.alert('分组里没有成员！', 3, !1);
//                setTimeout(function(){
//                    layer.close(i);
//                }, 1000);
//            }
//            else{
//                
//                $('#show_group_member_tbody').empty();/*删除以前事件，解决多次post问题*/
//                $.each(data,function(index,item){
//                    $('#show_group_member_tbody').append("<tr class='rightclass'><td><a href='#' id='' class='button orange'>删除</a></td><td><a href='#' id='' class='button pink' onclick=''>"+item.name+"</a></td></tr>");
//                });
//               
//            }
//           
//        },
//        'json'
//        ); 
}

//add_group_member_submit_bbb  add_group_member_delete_aaa

function userpost(){
   
}