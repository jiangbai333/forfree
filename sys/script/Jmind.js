(function(window) {
    window._$ = window.$;
    var dom = window.document,
        loc = window.location,
        jmind = window.jmind = window.$ = function(selector) {
        return new jmind.fn.init(selector);
    };
    var rquickExpr = /^(?:#([\w-]*)|(\w+)|\.([\w-]+))$/;
    jmind.fn = jmind.prototype = {
        init: function(selector) {
            if (!selector)
                return this;
            if (typeof selector === "string") {
                var match = rquickExpr.exec(selector);
                if (match[1]) {
                    this[0] = dom.getElementById(match[1]);
                    return this;
                } else if (match[2]) {
                    this[0] = dom.getElementById(match[2]);
                    return this;
                } else if (match[3]) {
                    this[0] = dom.getElementById(match[3]);
                    return this;
                }
            } else if (typeof selector === 'object') {
                this[0] = selector;
                return this;
            }
        },
        //返回当前元素或指定id元素
        obj: function(id) {
            if (!id)
                return this[0];
            else
                return dom.getElementById(id);
        },
        //设置元素文本
        intext: function(str) {
            this[0].innerTEXT = str;
        },
        //设置元素标签与文本
        inhtml: function(str) {
            this[0].innerHTML = str;
        },
        //设置表单默认值
        value: function(str) {
            this[0].value = str;
        },
        
        /**
         * 隐藏显示中的元素
         * @param {type} delay
         * @returns {undefined}
         */
        hide: function(delay) {
            if (delay && typeof delay === 'number') {
                this[0].style.display = 'none';
            } else
                this[0].style.display = 'none';
        },
        
        /**
         * 显示被隐藏的元素
         * @returns {undefined}
         */
        show: function() {
            this[0].style.display = '';
        },
        
        /**
         * 切换元素隐藏显示
         * @returns {undefined}
         */
        toggle: function() {
            this[0].style.display = this[0].style.display === 'none' ? '' : 'none';
        },
        /**
         * 建立新标签
         * @param {string} parentTagId 父标签id
         * @param {string} childTag 要建立的标签名
         * @param {string} childId 新建立标签的id
         * @returns {_L1.jmind.prototype} 
         */        
        createTag: function(parentTagId, childTag, childId) {
            var thisObj = dom.createElement(childTag);
            var parent = dom.getElementById(parentTagId);
            parent.appendChild(thisObj);
            thisObj.id = childId;
            return this;
        },
        //批量设置元素属性
        setDomProperty: function() {

        },
        /*opacity: function(a, b) {
         this[0].style.opacity = a/b;
         this[0].style.filter = 'alpha(opacity:'+ (a*100)/b +')';
         },*/

        /**
         * 事件绑定
         * @param {object} element 事件将要绑定到的元素对象
         * @param {string} evtName 事件名称
         * @param {function} callback 回调函数 事件发生时执行的方法
         * @param {} useCapture 
         * @returns {mixed}
         */
        add: function(element, evtName, callback, useCapture) {
            if (element.addEventListener) {
                element.addEventListener(evtName, callback, useCapture);
            } else {
                element.attachEvent('on' + evtName, callback);
            }
        },
       
        /**
         * 事件绑定
         * @param {string} evtName 需要绑定的事件名[click.....]
         * @param {type} callback
         * @returns {undefined}
         */
        set: function(evtName, callback) {
            this.add(this[0], evtName, callback, false);
        }
    };
    jmind.fn.init.prototype = jmind.fn;
})(window);