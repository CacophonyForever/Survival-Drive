(function(){var n="jmenu";CKEDITOR.plugins.add("jmenu",{requires:"link",icons:"jmenu",lang:"en",hidpi:!1,init:function(t){CKEDITOR.plugins.link.getSelectedLink2||(CKEDITOR.plugins.link.getSelectedLink2=function(n){var t=n.getSelectedWidget()||null;return t&&(t.inline?!t.wrapper.getAscendant("a"):1)?t.parts.link:CKEDITOR.plugins.link.getSelectedLink(n)||!1});var i={href:t.config.base+"index.php?option=com_menus&view=items&layout=modal&tmpl=component&"+t.config.jhash+"=1&editor={EDITOR}",exec:function(n){var i,r,u,t;this.href=this.href.replace("{EDITOR}",n.name);i=this;r=function(n){n.cancel()};n.editable().once("blur",r,null,null,-100);if(jQuery.fn.squeezeBox?jQuery.fn.squeezeBox({handler:"iframe",size:{x:800,y:500},url:this.href},!0):SqueezeBox.open(null,{handler:"iframe",size:{x:800,y:500},url:this.href}),u=CKEDITOR.plugins.link.getSelectedLink(n),u&&(t=function(){var r=n.getSelection();CKEDITOR.env.ie&&r&&(n._bookmarks=r.createBookmarks2());CKEDITOR.tools.setTimeout(function(){if(typeof SqueezeBox=="object")SqueezeBox.removeEvent("onOpen",t);else jQuery(ARK.squeezeBox).on("onOpen",t)},0,i)},CKEDITOR.env.ie))if(typeof SqueezeBox=="object")SqueezeBox.addEvent("onOpen",t);else jQuery(ARK.squeezeBox).on("onOpen",t)}},r=t.addCommand(n,i);r.modes={wysiwyg:1};t.addMenuItems&&t.addMenuItems({image:{label:t.lang.jmenu.menu,command:n,group:"jmenu"}});t.ui.addButton&&t.ui.addButton("Jmenu",{label:t.lang.jmenu.toolbar,command:n})},afterInit:function(t){t.on("doubleclick",function(i){var r=CKEDITOR.plugins.link.getSelectedLink2(t)||i.data.element;if(!r.isReadOnly()&&r.is("a")&&!r.getAttribute("name")&&(r.getAttribute("href")||!r.getChildCount())&&/Itemid=\d+$/.test(r.getAttribute("href")))return t.execCommand(n),!1},null,null,6);t.contextMenu&&t.contextMenu.addListener(function(n){var i,r;return!n||n.isReadOnly()?null:(i=CKEDITOR.plugins.link.tryRestoreFakeAnchor(t,n),!i&&!(i=CKEDITOR.plugins.link.getSelectedLink2(t)))?null:(r={},i.getAttribute("href")&&i.getChildCount()&&/Itemid=\d+$/.test(i.getAttribute("href"))&&(r={jmenu:CKEDITOR.TRISTATE_OFF}),r)});t.widgets.on("instanceCreated",function(t){var i=t.data;if(i.inline)i.on("contextMenu",function(t){t.data.image=CKEDITOR.TRISTATE_OFF;var i=this.parts.link||this.wrapper.getAscendant("a");i&&/Itemid=\d+$/.test(i.getAttribute("href"))&&(t.data[n]=CKEDITOR.TRISTATE_OFF)})})}})})()