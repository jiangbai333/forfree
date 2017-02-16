(function($) { 
    $.fn.clock=function (options) {
        //默认配置
        var _this=this;
        $( this ).empty();
        var defaults = {
            size:4, //数码管尺寸
            showColor:'blue', //点亮字段的颜色
            heidColor:'#fcfcfc', //熄灭字段的颜色
            bgColor:'#80bed6', //数码管背景颜色
            id:$(_this).attr('id')+'_clock', //数码管id
            duan:0x3f //段码 16进制
        };
        // 覆盖默认配置
        var opts=$.extend(defaults,options); //合并配置
		//建立数码管 
        var c=$("<div></div>",{id:opts.id,html:"<div class='clock_div'><div class='duan_div heid duan2'></div><div class='duan_div heid duan3'></div><div class='duan_div heid duan5'></div><div class='duan_div heid duan6'></div><div class='duan_div heid duan1'></div><div class='duan_div heid duan4'></div><div class='duan_div heid duan7'></div><div class='duan_div heid duan8'></div></div>"});
        $(_this).append(c); //焊接
        /**
        *显存处理
         */
        var a=Number(opts.duan);
        var num='';
        for(;a!=0;){var b=a;num=num+b % 2;a=(a-b % 2)/2;}
        if(num.length<8){var l=num.length;for (;l<8;l++) {num+='0';}}
        var display=num.split('');
        for(var obj in display){if(display[obj] == 1){$(_this).find('.duan'+(obj-(-1))).removeClass('heid').addClass('show');}}
 
        /**
        *数码管样式处理
         */
        /*$('#'+opts.id).css({
            padding:opts.size / 2+" "+(opts.size*2-(-1))+" "+opts.size / 2+" "+opts.size / 2,
            width:opts.size*10,
            height:opts.size*19,
            backgroundColor:opts.bgColor
        }).fadeTo('fast', 0.8)*/
        $(_this).find('.clock_div,.duan_div').css({position:'absolute'});
        $(_this).find('.show').css({fontSize:'1px',backgroundColor:opts.showColor});
        $(_this).find('.heid').css({fontSize:'1px',backgroundColor:opts.heidColor});
        $(_this).find('.duan1').css({height:opts.size+'px',width:opts.size*8+'px',left:opts.size+'px'});
        $(_this).find('.duan2').css({height:opts.size*8+'px',width:opts.size+'px',top:opts.size+'px',left:opts.size*9+'px'});
        $(_this).find('.duan3').css({height:opts.size* 8+'px',width:opts.size+'px',top:opts.size*10+'px',left:opts.size*9+'px'});
        $(_this).find('.duan4').css({height:opts.size+'px',width:opts.size*8+'px',top:opts.size*18+'px',left:opts.size+'px'});
        $(_this).find('.duan5').css({height:opts.size*8+'px',width:opts.size+'px',top:opts.size*10+'px'});
        $(_this).find('.duan6').css({height:opts.size*8+'px',width:opts.size+'px',top:opts.size+'px'});
        $(_this).find('.duan7').css({height:opts.size+'px',width:opts.size*8+'px',top:opts.size*9+'px',left:opts.size+'px'});
        $(_this).find('.duan8').css({height:opts.size+'px',width:opts.size+'px',top:opts.size*18+'px',left:opts.size*11+'px'});
    };
    
    
    
    $.fn.drag = function(option) {
        var defaults = {
            
        };
        var options = $.extend(defaults, options);
        var _this = $(this);
        var move = false;
        
        $(this).mousedown(function(e) {
            var pointX = e.pageX;
            var pointY = e.pageY;
            var dX = pointX - parseInt($(this).css('left'));
            var dY = pointY - parseInt($(this).css('top'));
//            $(this).fadeTo(1000,0.4);
            move = true;
            $(document).mousemove(function(e) {
                var _pointX = e.pageX;
                var _pointY = e.pageY;
                if ( move ) {
                    _this.css('left', (_pointX - dX) + 'px');
                    _this.css('top', (_pointY - dY) + 'px');
                }
            });
        }).mouseup(function(){
            move = false;
            $(this).fadeTo(1000,1);
        });
    };
    
    
    $.fn.hl = function(color) {
        return this.each(function() {
            $(this).data('color', $(this).css('color')).css('color', color).one('mouseover', function() {
                $(this).animate({color:$(this).data('color')});
            });
        });
                //css("color", color);
    };
    
    $.fn.superhl = function(options) {
        var defaults = {
            color : 'blue'
        };
        var options = $.extend($.fn.superhl.defaults, options);
        return this.each(function(){
            alert(options.color);
        });
    };

    $.fn.moveto=function(options){
        //默认配置
        var defaults = {
            handler:false,
            opacity:0.5
        };
        // 覆盖默认配置
        var opts = $.extend(defaults, options);
        this.each(function(){
            //初始标记变量
            var isMove = false,
            //handler如果没有设置任何值，则默认为移动对象本身，否则为所设置的handler值
            handler = opts.handler ? $(this).find(opts.handler) : $(this),
            _this=$(this), //移动的对象
            dx,dy;

            $(document).mousemove(function(event){ //移动鼠标，改变对象位置
                if (isMove) {
                    //获得鼠标移动后位置
                    var eX = event.pageX,eY = event.pageY;
                    //更新对象坐标
                    _this.css({'left':eX-dx,'top':eY-dy});
                }
            }).mouseup(function(){ //当放开鼠标，停止拖动
                isMove = false;
                _this.fadeTo('fast', 1);
            });

            handler.mousedown(function(event){ //当按下鼠标，设置标记变量isMouseDown为true
                if($(event.target).is(handler)){ //判断最后触发事件的对象是否是handler
                    isMove=true;
                    $(this).css('cursor','move');
                    _this.fadeTo('fast', opts.opacity);
                    dx=event.pageX-parseInt(_this.css("left"));//鼠标相对于移动对象的坐标
                    dy=event.pageY-parseInt(_this.css("top"));
                }
            });
        });
    };

})(jQuery);