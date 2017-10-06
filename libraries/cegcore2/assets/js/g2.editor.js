jQuery.fn.extend({
	insertAtCaret: function(startValue, endValue){
		return this.each(function(i){
			var editor_data = jQuery(this).getCaret();
			
			if(document.selection){
				//For browsers like Internet Explorer
				this.focus();
				var sel = editor_data.sel;
				sel.text = startValue + sel.text + endValue;
				this.focus();
				
				jQuery(this).storeCaret();
			}else if(this.selectionStart || this.selectionStart == '0'){
				//For browsers like Firefox and Webkit based
				var startPos = editor_data.startPos;
				var endPos = editor_data.endPos;
				var scrollTop = editor_data.scrollTop;
				var selectedValue = editor_data.selectedValue;
				
				this.value = this.value.substring(0, startPos) + startValue + selectedValue + endValue + this.value.substring(endPos, this.value.length);
				this.focus();
				this.selectionStart = startPos +  startValue.length ;
				this.selectionEnd = startPos + selectedValue.length + startValue.length ;
				this.scrollTop = scrollTop;
				
				jQuery(this).storeCaret();
			}else{
				this.value += startValue+endValue;
				this.focus();
			}
		});
	},
	replaceAtCaret: function(Value){
		return this.each(function(i){
			var editor_data = jQuery(this).getCaret();
			
			if(document.selection){
				//For browsers like Internet Explorer
				this.focus();
				var sel = editor_data.sel;
				sel.text = Value;
				this.focus();
				
				jQuery(this).storeCaret();
			}else if(this.selectionStart || this.selectionStart == '0'){
				//For browsers like Firefox and Webkit based
				var startPos = editor_data.startPos;
				var endPos = editor_data.endPos;
				var scrollTop = editor_data.scrollTop;
				var selectedValue = editor_data.selectedValue;
				
				this.value = this.value.substring(0, startPos) + Value + this.value.substring(endPos, this.value.length);
				this.focus();
				this.selectionStart = startPos + Value.length ;
				this.selectionEnd = startPos + Value.length ;
				this.scrollTop = scrollTop;
				
				jQuery(this).storeCaret();
			}else{
				this.value += Value;
				this.focus();
			}
		});
	},
	getCaret: function(){
		var editor_data = jQuery(this).data('editor');
		if(typeof editor_data == 'undefined'){
			jQuery(this).storeCaret();
			var editor_data = jQuery(this).data('editor');
		}
		return editor_data;
	},
	getSelection: function(){
		var editor_data = jQuery(this).getCaret();
		
		if(editor_data.hasOwnProperty('sel')){
			//For browsers like Internet Explorer
			var sel = editor_data.sel;
			return sel.text;
		}else if(editor_data.hasOwnProperty('selectedValue')){
			//For browsers like Firefox and Webkit based
			var selectedValue = editor_data.selectedValue;
			return selectedValue;
		}else{
			return '';
		}
	},
	storeCaret: function(event){
		return this.each(function(i){
			if(document.selection){
				var sel = document.selection.createRange();
				jQuery(this).data('editor', {
					'sel':  sel
				});
			}else if(this.selectionStart || this.selectionStart == '0'){
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				var selectedValue = this.value.substring(startPos, endPos);
				var valueLength = this.value.length;
				if(endPos > valueLength){
					endPos = valueLength;
				}
				if(startPos > valueLength){
					startPos = valueLength;
				}
				
				jQuery(this).data('editor', {
					'startPos':  startPos,
					'endPos' : endPos,
					'scrollTop' : scrollTop,
					'selectedValue' : selectedValue
				});
			}else{
				jQuery(this).data('editor', {
					
				});
			}
		});
	}
});

(function($){
	$.G2.editor = {};
	$.G2.editor.clear = function(textarea){
		var editor = textarea.closest('div.editor-box');
		textarea.val('');
		editor.find('.attachments').empty();
	};
	
	$.G2.editor.getTextarea = function(button){
		return button.closest('.editor-box').find('textarea.editor-text').first();
	};
	
	$.G2.editor.getEditor = function(button){
		return button.closest('.editor-box');
	};
	
	$.G2.editor.buttonInsert = function(textarea, button){
		var start_tag = (typeof button.data('start') != 'undefined') ? button.data('start') : '';
		var end_tag = (typeof button.data('end') != 'undefined') ? button.data('end') : '';
		var text = (typeof button.data('text') != 'undefined') ? button.data('text') : '';
		
		button.off('click.editor');
		button.on('click.editor', function(){
			if(text){
				text = button.data('text');
				if(button.attr('data-include')){
					$(button.data('include')).find('input').each(function(i, inp){
						var reg = new RegExp('\\{'+i+'\\}', 'g');
						text = text.replace(reg, $(inp).val());
					});
				}
				textarea.replaceAtCaret(text);
			}else{
				textarea.insertAtCaret(start_tag, end_tag);
			}
			return false;
		});
	};
	
	$.G2.editor.buttonsInit = function(textarea){
		var editor = textarea.closest('div.editor-box');
		
		editor.find('.editor-button').each(function(i, button){
			$.G2.editor.buttonInsert(textarea, $(button));
		});
		
		editor.find('.buttons-bar').first().find('.G2-static').each(function(i, button){
			$.G2.actions.list[$(button).data('id')] = {
				'click' : function(action, event){
					var selection = textarea.getSelection();
					action.next('.popup').find('input[data-selection="1"]').val(selection);
					
					action.next('.popup').find('.editor-button').on('click', function(){
						action.popup('hide');
					});
				}
			};
		});
	};
	
	$.G2.editor.ready = function(textarea){
		var editor = textarea.closest('div.editor-box');
		
		editor.find('.ui.button.dropdown').dropdown({
			on: 'hover'
		});
		
		$.G2.editor.buttonsInit(textarea);
		
		textarea.on('mouseup keyup mouseleave', function(e){
			$(this).storeCaret(e);
		});
		
		$.G2.actions.list['editor-save'] = {
			'success' : function(action, html, json, newContent){
				if(json == false){
					//var newPost = $(html);
					/*
					if(action.data('type') == 'new'){
						action.closest('.editor-box').before(newPost);
					}else if(action.data('type') == 'update'){
						action.closest('.cfu-post').replaceWith(newPost);
					}
					*/
					//$.G2.scrollTo(newPost);
					$.G2.scrollTo(newContent);
					
					$.G2.editor.clear($.G2.editor.getTextarea(action));
					//$.G2.actions.ready(newPost);
					//$.G2.composer.ready('post', {actions : [newPost]});
				}
			}
		};
		
		$.G2.actions.list['editor-clear'] = {
			'click' : function(action, event){
				$.G2.editor.clear($.G2.editor.getTextarea(action));
				$.G2.editor.getTextarea(action).focus();
			}
		};
		
		$.G2.actions.list['editor-close'] = {
			'click' : function(action, event){
				$.G2.editor.getEditor(action).remove();
			}
		};
		
		$.G2.actions.list['editor-attach'] = {
			'success' : function(action, html, json){
				if(json == false){
					var newAttachment = $(html);
					
					$.G2.editor.getEditor(action).find('.attachments').append(newAttachment);
					
					action.closest('.ui.form').find(':input').val('');
					action.closest('.popup').prev('.G2-static').popup('hide');
					
					$.G2.scrollTo($.G2.editor.getEditor(action).find('.attachments').first());
					
					$.G2.editor.buttonsInit($.G2.editor.getTextarea(action));
					$.G2.actions.ready(newAttachment);
				}
			}
		};
		
		$.G2.actions.list['delete-attachment'] = {
			'success' : function(action, data, json){
				if(json == true && data.error == 0){
					action.closest('.cfu-attachment').remove();
				}
			}
		};
		
	};
}(jQuery));