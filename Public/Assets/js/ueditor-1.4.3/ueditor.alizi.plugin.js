 UE.plugins["aliziPlugin"] = function() {
     var me = this, editor = this;
     var utils = baidu.editor.utils,
             Popup = baidu.editor.ui.Popup,
             Stateful = baidu.editor.ui.Stateful,
             uiUtils = baidu.editor.ui.uiUtils,
             UIBase = baidu.editor.ui.UIBase;
     var domUtils = baidu.editor.dom.domUtils;

     var clickPop = new baidu.editor.ui.Popup({
         content: "",
         editor: me,
         _remove: function() {
             $(clickPop.anchorEl).remove();
             clickPop.hide();
         },
         _blank: function() {
             $('<p><br/></p>').insertAfter(clickPop.anchorEl);
             clickPop.hide();
         },
         className: 'edui-bubble'
     });

     me.addListener("click",
         function(t, evt) {
             evt = evt || window.event;
             var el = evt.target || evt.srcElement;
             if (el.tagName == "IMG") {  return; }
             if ($(el).parents('.alizi-editor').size() > 0) {
                 el = $(el).parents('.alizi-editor:first').get(0);

                 current_active_ueitem = $(el);
                 clickPop.render();
                 var html = clickPop.formatHtml('<nobr class="alizi-poptools edui-default">模块：' + '<a href="javascript:;" onclick="$$._remove()" stateful>' + '删除</a>' + ' | <a href="javascript:;" onclick="$$._blank()" stateful>' + '其后插入空行</a>' + '</nobr>');
                 var content = clickPop.getDom('content');
                 content.innerHTML = html;
                 clickPop.anchorEl = el;
                 clickPop.showAnchor(clickPop.anchorEl);
             } else {
                 el.style.border="0";
                 //if (current_active_ueitem) {  current_active_ueitem = null; }
             }
         });
 };
 
 UE.registerUI('aliziOrder',function(editor,uiName){
    var btn = new UE.ui.Button({
        name:uiName,
        title:'添加订单标签',
		label:'<b style="color:#06C">【订单标签】</b>',
       cssRules :'width:0  !important;',
        onclick:function () {
            editor.execCommand('insertHtml', '{[AliziOrder]}');
            $.alert('订单标签添加成功',1,'',{time:1500});
        }
    });
    return btn;
})
 UE.registerUI('aliziComment',function(editor,uiName){
    var btn = new UE.ui.Button({
        name:uiName,
        title:'添加评论标签',
		label:'<b style="color:#06C">【评论标签】</b>',
       cssRules :'width:0  !important;',
        onclick:function () {
            editor.execCommand('insertHtml', '{[AliziComment-1]}');
            $.alert('评论标签添加成功',1,'',{time:1500});
        }
    });
    return btn;
})
 UE.registerUI('aliziScroll',function(editor,uiName){
    var btn = new UE.ui.Button({
        name:uiName,
        title:'添加滚动标签',
		label:'<b style="color:#06C">【滚动标签】</b>',
       cssRules :'width:0  !important;',
        onclick:function () {
            editor.execCommand('insertHtml', '{[AliziScroll-1]}');
            $.alert('滚动标签添加成功',1,'',{time:1500});
        }
    });
    return btn;
})
 UE.registerUI('aliziWeixin',function(editor,uiName){
    var btn = new UE.ui.Button({
        name:uiName,
        title:'添加微信客服标签',
		label:'<b style="color:#06C">【微信标签】</b>',
       cssRules :'width:0  !important;',
        onclick:function () {
            editor.execCommand('insertHtml', '{[AliziWeixin]}');
            $.alert('微信客服标签添加成功',1,'',{time:1500});
        }
    });
    return btn;
})

 UE.registerUI('aliziTemplate',function(editor,uiName){
 
	var dialog = new UE.ui.Dialog({
		iframeUrl:'Public/Assets/js/ueditor-1.4.3/dialogs/template/template.html',
		name:'微信图文模板',
		editor:this,
		cssRules:"width:640px;height:400px;",
		buttons:[
		{
			className:'edui-okbutton',
			label:'确定',
			onclick:function () {
				dialog.close(true);
			}
		}]
	});
		
			
    var btn = new UE.ui.Button({
        name:uiName,
        title:'添加模板',
		label:'<b style="color:#06C">【添加模板】</b>',
       cssRules :'width:0  !important;',
        //点击时执行的命令
        onclick:function () {
            dialog.render();
			dialog.open();
        }
    });
    return btn;
})

