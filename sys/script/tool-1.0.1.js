(function($) {
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
    
    
    /**
     *          assign JQ-tool V[1.0] 
     *          post后跳转 对$.post()的封装
     * @param {string} purl 服务端脚本路径
     * @param {string} turl 跳转到的位置
     * @param {json} data 传输到服务端的数据
     * @param {type} str 条件
     * @param {type} back 条件不符合时 返回的信息
     */
    $.assign = function(options) {
        $.assign.defaults = {
            purl : '',
            turl : '',
            data : {},
            str  : 'success',
            back : ''
        };
        var options = $.extend($.assign.defaults, options);
        if ( options.purl === '' ) {
            if ( options.turl === '' ) {
                alert('请指定一个要跳转到的链接');
            } else {
                window.location.assign(options.turl);
            }
        } else {
            if ( options.turl === '' ) {
                alert('请指定一个要跳转到的链接');
            } else {
                $.post(options.purl,options.data,function(d1, t1){
                    if( d1 === options.str ) {
                        window.location.assign(options.turl);
                    } else {
                        if ( options.back != '' ) {
                            alert(options.back);
                        }
                    }
                });
            }
        }
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
