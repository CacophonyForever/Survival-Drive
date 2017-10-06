(function(){"use strict";function n(n){var t=this.getDialog();return t.getValueOf("background",n).trim()}CKEDITOR.plugins.add("divbgimage",{init:function(){}});CKEDITOR.on("dialogDefinition",function(t){var r=t.data.name,u=t.data.definition,i=t.editor;(r=="creatediv"||r=="editdiv")&&u.addContents({id:"background",label:i.lang.divbgimage.bgImageTitle,title:i.lang.divbgimage.bgImageTitle,accessKey:"Q",elements:[{type:"vbox",padding:0,children:[{type:"hbox",widths:["280px","100px;vertical-align: middle;"],align:"right",styles:"",children:[{type:"text",label:i.lang.divbgimage.imageUrl,id:"imageURL",setup:function(n){var t=n.getStyle("background-image");t=t.trim();t&&(t=t.replace(/^url\(['"]?([^'"]+)['"]?\)/,"$1"),this.setValue(t))},commit:function(n){var t,i="background-image";(t=this.getValue().trim())?n.setStyle(i,"url("+t+")"):n.removeStyle(i)}},{type:"button",id:"browse",label:i.lang.common.browseServer,hidden:!0,filebrowser:{action:"Browse",target:"background:imageURL",url:i.config.filebrowserImageBrowseUrl||i.config.filebrowserBrowseUrl}}]}]},{type:"vbox",padding:0,children:[{type:"hbox",widths:["150px","150px"],align:"right",children:[{type:"select",id:"repeat",label:i.lang.divbgimage.repeat,items:[["repeat"],["no-repeat"],["repeat-x"],["repeat-y"],],"default":"repeat",setup:function(n){var t=n.getStyle("background-repeat");t&&this.setValue(t)},commit:function(t){var i,r="background-repeat";n.call(this,"imageURL")&&(i=this.getValue())?t.setStyle(r,i):t.removeStyle(r)}},{type:"select",id:"attachment",label:i.lang.divbgimage.attachment,items:[["scroll"],["fixed"],["local"],],setup:function(n){var t=n.getStyle("background-attachment");t&&this.setValue(t)},commit:function(t){var i,r="background-attachment";n.call(this,"imageURL")&&(i=this.getValue())?t.setStyle(r,i):t.removeStyle(r)}}]}]},{type:"vbox",padding:0,children:[{type:"hbox",widths:["150px","150px"],align:"right",children:[{type:"select",id:"blend",label:i.lang.divbgimage.blendMode,items:[["normal"],["multiply"],["screen"],["overlay"],["darken"],["lighten"],["color-dodge"],["saturation"],["color"],["luminosity"],],style:"float:left","default":"normal",setup:function(n){var t=n.getStyle("background-blend-mode");t&&this.setValue(t)},commit:function(t){var i,r="background-blend-mode";n.call(this,"imageURL")&&(i=this.getValue())?t.setStyle(r,i):t.removeStyle(r)}},{type:"select",label:i.lang.divbgimage.position,id:"pos",align:"right",items:[["left top"],["left center"],["left bottom"],["right top"],["right center"],["center top"],["center center"],["center center"],],"default":"left top",setup:function(n){var t=n.getStyle("background-position");t&&this.setValue(t)},commit:function(t){var i,r="background-position";n.call(this,"imageURL")&&(i=this.getValue())?t.setStyle(r,i):t.removeStyle(r)}},]}]},{type:"vbox",padding:0,children:[{type:"hbox",widths:["150px","150px"],align:"right",children:[{type:"text",id:"width",label:i.lang.divbgimage.bgWidth,width:"50px",setup:function(n){var t=n.getStyle("background-size").split()[0];t&&t!="auto"&&t&&this.setValue(t)},commit:function(t){var i,r="background-position",u=this.getDialog(),f=n.call(this,"height");(i=this.getValue())?t.setStyle(r,i+" "+f||"auto"):u.getValueOf("background","height")||t.removeStyle(r)}},{type:"text",label:i.lang.divbgimage.bgHeight,id:"height",align:"right",width:"50px",setup:function(n){var t=n.getStyle("background-size").split()[0];t&&t!="auto"&&t&&this.setValue(t)},commit:function(t){var i,r="background-position",u=this.getDialog(),f=n.call(this,"width");(i=this.getValue())?t.setStyle(r,(f||"auto")+" "+i):u.getValueOf("background","width")||t.removeStyle(r)}}]}]}]})},null,null,9);CKEDITOR.plugins.setLang("divbgimage","en",{bgImageTitle:"Background image",imageUrl:"Image URL",repeat:"Repeat",attachment:"Attachment",blendMode:"Blend mode",position:"Position",bgWidth:"Width (CSS unit eg: 20px)",bgHeight:"Height (CSS unit eg: 20%)"});CKEDITOR.plugins.setLang("divbgimage","fa",{bgImageTitle:"تصویر پس زمینه",imageUrl:"آدرس عکس",repeat:"تکرار",attachment:"ضمیمه",blendMode:"حالت لایه",position:"موقعیت",bgWidth:"(eg : 20px) عرض",bgHeight:"(eg : 20px) ارتفاع"});CKEDITOR.plugins.setLang("divbgimage","ru",{bgImageTitle:"Фоновое изображение",imageUrl:"URL изображения",repeat:"Повторение",attachment:"Привязка",blendMode:"Режим наложения",position:"Позиция"})})()