/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){function n(n,t,i){var r=n.join(" "),o,f,e,u;for(r=r.replace(/(,|>|\+|~)/g," "),r=r.replace(/\[[^\]]*/g,""),r=r.replace(/\:{1,2}[^\s]*/g,""),r=r.replace(/\]/g,""),r=r.replace(/\s+/g," "),o=r.split(" "),f=[],e=0;e<o.length;e++)u=o[e],i.test(u)&&!t.test(u)&&CKEDITOR.tools.indexOf(f,u)==-1&&f.push(u);return f}function t(t,i,r,u){for(var a=[],p=[],e,w,b,k,v,s,h,y,f=0;f<i.styleSheets.length;f++)if((e=i.styleSheets[f],e)&&(w=e.ownerNode||e.owningElement,!w.getAttribute("data-cke-temp"))&&(!e.href||e.href.substr(0,9)!="chrome://")&&(t.config.useTemplateCSS||/com_ajax&plugin=arktypography&format=json/.test(e.href))){if(t.config.stylesheets)for(s=0;s<t.config.stylesheets.length;s++)if(b=t.config.stylesheets[s].replace(".",".").replace("-","-").replace("/","/"),k=new RegExp("/"+b+"(.css|.less|.scss|.sass|[?].*)?$"),match=k.test(e.href),match)break;if(!match)try{for(v=e.cssRules||e.rules,s=0;s<v.length;s++)p.push(v[s].selectorText)}catch(d){}}for(h=n(p,r,u),f=0;f<h.length;f++){var c="p",o="",l="class";if(h[f].charAt(0)=="."||h[f].charAt(0)=="#"){if(o=h[f].substr(1),element=c,h[f].charAt(0)=="#"&&(l="id"),o.match(/[\.#]/))continue;if(o.match(/icon\-/))continue}else c=h[f].split(/[\.|#]/),element=c[0].toLowerCase(),o=c[1],/^w+?#w+/.test(h[f])&&(l="id");o&&o!="."&&o!="#"&&(attributes={},attributes[l]=o,y=element+(l=="id"?"#":".")+o,element=="img"?a.push({name:y,type:"widget",widget:"image",attributes:attributes}):a.push({name:y,element:element,attributes:attributes}))}return a}CKEDITOR.plugins.add("autostylesheetparser",{init:function(n){n.filter.disable();var i;n.once("stylesSet",function(r){r.cancel();n.once("contentDom",function(){var r=function(){n.getStylesSet(function(r){var u=n.config.stylesheetParser_skipSelectors||/(^body\.|^\.)/i,f=n.config.stylesheetParser_validSelectors||/\w+\.\w+/;i=r.concat(t(n,n.document.$,u,f));n.getStylesSet=function(n){if(i)return n(i)};n.fire("stylesSet",{styles:i})})};CKEDITOR.env.gecko||CKEDITOR.env.ie?setTimeout(function(){r.apply()},400):r.apply()})},null,null,1)}})})()