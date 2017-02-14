/**
    * @文件        index.js   
    * @描述        登陆页面JS文件
    * @功能        登陆时相关信息的判断     
    * @作者        
    * @创建日期    
 */

/**
    * @函数      jquery调用开始    
    * @描述        
    * @功能          
 */

$(document).ready(function() {
    $('#firm_input_show,#user_input_show,#pass_input_show').click(function() {
        var hides = $(this).addClass('hide').attr('id').slice(0,-4)+'hide';
        $('#'+hides).removeClass('hide').focus();
    });
    
    $('#firm_input_hide,#user_input_hide,#pass_input_hide').blur(function() {
        if( false == $(this).val() ) {
            var hides = $(this).addClass('hide').val('').attr('id').slice(0,-4)+'show';
            $('#'+hides).removeClass('hide');
        }
    });
    $.post(
        './index.php?a=checkFirm',
        {},
        function(d1, t1) {
            if ( 1000 !== d1 ) {
                $('#login_title span').append("<b>" + d1.firm + "</b>");
            } else {
                $('#firm').removeClass('hide');
                $('#login_title span').append("<b>欢迎使用</b>");
            }
        },
        'json'
    );
});

/**
    * @函数      登录按钮函数 
    * @描述      处理登陆页面相关信息  
    * @功能      url登陆，数据格式判断，发送账号信息   
 */
function login(){
        var urlfirm = getUrlParam('p0');
        var firm = $("#firm_input_hide").val(); 
        var user = $("#user_input_hide").val(); 
        var pass = $("#pass_input_hide").val(); 
        if(!urlfirm){urlfirm=firm;}
         if(user ===""){
            var i = layer.alert('请输入您的账号！', 3, !1);
            setTimeout(function(){
                layer.close(i);
            }, 1000);
            $("#user_input_hide").focus();
        }
        else if(pass ===""){
            var i = layer.alert('请输入您的密码！', 2, !1);
            setTimeout(function(){
                layer.close(i);
            }, 1000);
            $("#pass_input_hide").focus();
        }
        else{
            
            $.post("./index.php?a=login", {'firmid':urlfirm,'userid':user,'password':pass},
                function (data, textStatus){
                      var ii = layer.load('登录中...');
                //此处用setTimeout演示ajax的回调
                      setTimeout(function(){
                        layer.close(ii);
                    }, 1000);
                    if(data === 1001){
                        var i = layer.alert('登陆失败，请核对您的公司名称！', 5, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                    }
                     if(data === 1002){
                        var i = layer.alert('用户或密码错误，或用户不存在！', 3, !1);
                        setTimeout(function(){
                            layer.close(i);
                        }, 1000);
                        $("#user_input_hide").focus();
                    }
                    if(data === 1000){
                          window.location.href="./index.php?a=inital&g=user&c=user";
                    }
            },'json');
        }
    
}

/**
    * @函数      通过javascript来获取url中的某个参数    
    * @描述      
    * @功能      例如：index.php?a=login  获取代码 geturlparam(a); 就可以将参数login在前台取得 
 */
function getUrlParam(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象.
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r!==null) return unescape(r[2]); return null; //返回参数值
} 