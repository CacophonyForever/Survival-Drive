(function(){CKEDITOR.plugins.add("menulinks",{requires:"link,article,jmenu",init:function(){},afterInit:function(n){var t={};n.addMenuGroup("links");t.link={label:n.lang.link.menu,group:"links",command:"link",order:1};t.article={label:n.lang.article.menu,group:"links",command:"article",order:2};t.jmenu={label:n.lang.jmenu.menu,group:"links",command:"jmenu",order:3};n.addMenuItems(t);n.ui.add("MenuLinks",CKEDITOR.UI_MENUBUTTON,{label:"Menu Links",modes:{wysiwyg:1},icon:"link",onMenu:function(){var n={};for(var i in t)n[i]=CKEDITOR.TRISTATE_OFF;return n}});n.getCommand("link")&&n.getCommand("link").setState(CKEDITOR.TRISTATE_OFF);n.getCommand("article")&&n.getCommand("article").setState(CKEDITOR.TRISTATE_OFF);n.getCommand("jmenu")&&n.getCommand("jmenu").setState(CKEDITOR.TRISTATE_OFF)}})})()