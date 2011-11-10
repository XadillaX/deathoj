///import core
///commands 删除
/**
 * 删除
 * @function
 * @name baidu.editor.execCommand
 * @param  {String}    cmdName    delete删除
 */
(function (){
    var domUtils = baidu.editor.dom.domUtils,
        browser = baidu.editor.browser;
    baidu.editor.commands['delete'] = {
        execCommand : function (){
            
            var range = this.selection.getRange(),
            
                mStart = 0,
                mEnd = 0,
                me = this;
            if(range.collapsed)
                return;
            while(!range.startOffset &&  !domUtils.isBody(range.startContainer) ){
                mStart = 1;
                range.setStartBefore(range.startContainer);
            }

            while(!domUtils.isBody(range.endContainer)){
                var child,endContainer = range.endContainer,endOffset = range.endOffset;
                if(endContainer.nodeType == 3 &&  endOffset == endContainer.nodeValue.length){
                    range.setEndAfter(endContainer);
                    continue;
                }
                child = endContainer.childNodes[endOffset];
                if(!child || domUtils.isBr(child) && endContainer.lastChild === child){
                    range.setEndAfter(endContainer);
                    continue;
                }
                break;

            }
            if(mStart){
                var start = me.document.createElement('span');
                start.innerHTML = 'start';
                start.id = '_baidu_cut_start';
                range.insertNode(start).setStartBefore(start)
            }
            if(mEnd){
                var end = me.document.createElement('span');
                end.innerHTML = 'end';
                end.id = '_baidu_cut_end';
                range.cloneRange().collapse(false).insertNode(end);
                range.setEndAfter(end)

            }



            range.deleteContents();


            if(domUtils.isBody(range.startContainer) && domUtils.isEmptyNode(me.body)){
                me.body.innerHTML = '<p>'+(browser.ie?'':'<br/>')+'</p>';
                range.setStart(me.body.firstChild,0).collapse(true);
            }

            range.select(true)
        },
        queryCommandState : function(){
            return this.selection.getRange().collapsed ? -1 : 0;
        }
    };
})();
