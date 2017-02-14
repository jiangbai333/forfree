$(document).ready(function() {
	var surveyid = $("#rom_id").val();
	/**
     * 满意度问卷,单选按钮被点击后的操作
     */
	$("#box").on({
		click   :   function() {
			var digitronGeDivId = $(this).attr('name') + '_digitronge';
			var digitronShiDivId = $(this).attr('name') + '_digitronshi';
			var num = $(this).val();
			$.display(num,  $('#'+digitronShiDivId), $('#'+digitronGeDivId));//显示选择的分数
		
		}
	}
	, '[satisfac]');
	    /**
     * 意向问卷,按钮被点击后的操作
     */
	$("#box").on({
		click   :   function() {
			        }
	}
	, '[intent]');
	    /**
     * 鼠标移动
     */
	$("#box").on({
		mouseover   :   function() {
			$(this).css('borderColor', '#35354F');
		}
		,
		mouseout    :   function() {
			$(this).css('borderColor', '#ddd');
		}
	}
	, 'fieldset');
	    /**
     * 上一页按钮被点击
     */
	$('#lastpage').click(function() {
		var onpage = $("#rom_position").val();//当前要提交的页
		if ( $.sent(onpage) ) {//提交成功返回true
			$('#page' + onpage).addClass('hide');//隐藏当前页
			onpage = Number(onpage) - 1;
			$('#page' + onpage).removeClass('hide');//显示上一页
			$("#rom_position").val(onpage);//把rom中当前页更新
			$.checkButton();//设置相应按钮的隐藏或显示
		
		}
		else {//失败返回false
			        
		}
	});
	    /**
     * 下一页按钮被点击
     */
	$('#nextpage').click(function() {
		var onpage = $("#rom_position").val();//当前要提交的页
		if ( $.sent(onpage) ) {//提交成功返回true
			$('#page' + onpage).addClass('hide');//隐藏当前页
			onpage = Number(onpage) + 1;
			$('#page' + onpage).removeClass('hide');//显示下一页
			$("#rom_position").val(onpage);//把rom中当前页更新
			$.checkButton();//设置相应按钮的隐藏或显示
		
		}
		else {//失败返回false
			        
		}
	});
	    
	//async: false,
	$.ajax({
	type: 'POST',
	url: './index.php?g=survey&c=show&a=answerface',
	data: {
		surveyid:surveyid
	
	}
	,
	success: function(d) {
		console.log(d);
		$.each(d, function(key, value) {
			if( typeof(value)!=='object'){
				var rom=$("<input>",{
					type:'hidden',id:"rom_"+key,value:value
				
				});
				$("div#rom").append(rom);
				if(key==="name"){
					$("title").text(value);
					$("#title").text(value);
				}
			}
			else {
				var pageid=value.pageid;
				delete value.pageid;
				var pageDiv=$("<div></div>",{
					id:key,class:"page hide",pageid:pageid
				
				});
				if(pageDiv.attr("id").slice(4)===$("#rom_position").val()){
					pageDiv.removeClass("hide");
				}//显示第一页
				$.each(value,function(pageKey,pageObj){
					if(pageObj.ancache ){
						var ancache=pageObj.ancache;
						delete pageObj.ancache;
					}
					else{
						var ancache=undefined
					
					};
					if(pageObj.opmod){
						var opmod=pageObj.opmod;
						delete pageObj.opmod;
					}//取选择模式
					var questionid=pageObj.quid;
					delete pageObj.quid;//取问题id
					var name=pageObj.name;
					delete pageObj.name;//取问题文本
					var questionDiv = $("<div></div>", {
						id      :       pageKey,
						class   :       'question'
												
					});//建立问题容器
					if ( ancache ) {
						questionDiv.attr('status', '1');
					}
					else {
						questionDiv.attr('status', '0');
					}
					if ( $('#rom_type').val() == 1 ) {
						var datum=$('#rom_datum').val();//取基准分
						var fieldset=$("<fieldset></fieldset>");//建立问题容器
						var legend = $("<legend></legend>").text(name);//建立问题容器
						var optionDiv=$("<div></div>",{
							class:'option'
						
						});//建立选项容器
						var digitronGeDiv=$("<div></div>",{
							class:'digitronge',id:questionid+'_digitronge'
						
						});//建立数码管个位
						var digitronShiDiv=$("<div></div>", {
							class:'digitronshi',id:questionid+'_digitronshi'
						
						});//建立数码管十位
						var fenDiv=$("<div></div>",{
							class:'fen',id:questionid+'_fen',text:'分'
						
						});//建立"分"字
						optionDiv.append("不满意");
						//循环生成答案按钮 satisfac属性标注答案属于满意度问卷 用于区分满意度和意向的单选按钮
						for(var x=1;x<=datum;x++){
							var radio=$("<input>",{
								type:"radio",name:questionid,id:questionid+'_'+x,value:x,title:x+'分',satisfac:''
							
							});
							if(ancache&&x==ancache.opid){
								radio.attr('checked',true);
							}
							optionDiv.append(radio);
						}
						optionDiv.append("满意");
						if(!ancache){
							$.display('',digitronShiDiv,digitronGeDiv);
						}
						else{
							$.display(ancache.opid,digitronShiDiv,digitronGeDiv);
						}
						questionDiv.append(fieldset.append(legend,digitronShiDiv,digitronGeDiv,fenDiv,optionDiv));
					}
					else if ( $('#rom_type').val() == 0 ) {//如果是意向问卷
						//建立问题容器
						var fieldset=$("<fieldset></fieldset>");
						//标注当前问题是单选还是多选
						if (opmod==0){
							var legend=$("<legend></legend>").html(name+"<sub>(多选)</sub>");
						}
						else if(opmod==1){
							var legend=$("<legend></legend>").html(name+"<sub>(单选)</sub>");
						}
						var optionDiv=$("<div></div>",{
							class:'option'
						
						});//建立问题容器
						$.each(pageObj, function(questionKey, questionObj) {
							var opid=questionObj.opid;
							delete questionObj.opid;//取选项id
							if ( opmod == 0 ) {
								var input = $("<input>", {
									type    :   'checkbox',
									name    :   questionid + '_' + opid,
									id      :   questionid + '_' + opid,
									value   :   opid,
									title   :   '选项 ' + opid,
									intent  :   ''
																		
								});
							}
							else if ( opmod == 1 ) {
								var input = $("<input>", {
									type    :   'radio',
									name    :   questionid,
									id      :   questionid + '_' + opid,
									value   :   opid,
									title   :   '选项 ' + opid,
									intent  :   ''
																		
								});
							}
							if ( ancache[input.val()] ) {
								input.attr('checked',true);
							}
							optionDiv.append(input).append('  ' + questionObj.name).append("<br><br>");
						});
						questionDiv.append(fieldset.append(legend, digitronShiDiv, digitronGeDiv, fenDiv, optionDiv));
					}
					pageDiv.append(questionDiv);
				});
				$("#box").append(pageDiv);
			}
		});
		$.checkButton();
	}
	,
	dataType: 'json'
		
    });
});
(function($) {
	/**
     *          翻页发送答案
     * @param {int} page 页
     * @returns {Boolean} 验证页面信息是否完整,是否发送成功等!  成功返回true 失败返回false
     */
	$.sent = function(page) {
		return true;
	};
	    /**
     *          设置翻页按钮
     * 就是上一页下一页什么的
     */
	$.checkButton=function(){
		if(1!=$("#rom_position").val()){
			$('#lastpage').removeClass('hide');
		}
		else{
			$('#lastpage').addClass('hide');
		}
		if($("#rom_position").val()!=$("#rom_pagenum").val()){
			$('#nextpage').removeClass('hide');
		}
		else{
			$('#nextpage').addClass('hide');
		}
	};
	    /**
     *          数码管显示分数
     * @param {int} num 要显示的数
     * @param {object} shiobj 十位上的数码管
     * @param {object} geobj 个位上的数码管
     */
	$.display=function(num,shiobj,geobj){
		var duan=[0x06,0x5b,0x4f,0x66,0x6d,0x7d,0x07,0x7f,0x6f];
		if(num==10){
			shiobj.clock({
				showColor:'#35354F',size:2,duan:duan[0],heidColor:'#EBEBEB'
			});
			geobj.clock({
				showColor:'#35354F',size:2,heidColor:'#EBEBEB'
			});
		}
		else{
			shiobj.clock({
				showColor:'#35354F',size:2,heidColor:'#EBEBEB'
			});
			geobj.clock({
				showColor:'#35354F',size:2,duan:duan[num-1],heidColor:'#EBEBEB'
			});
		}
	};
})(jQuery);