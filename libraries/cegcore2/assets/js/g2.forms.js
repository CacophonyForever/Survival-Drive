(function($){
	if($.G2 == undefined){
		$.G2 = {};
	}
	$.G2.forms = {};
	
	$.G2.forms.initializeForm = function (thisForm){
		var validationRules = {};
		
		jQuery.fn.form.settings.rules.required = function(value){
			if(value){
				return true;
			}else{
				return false;
			}
		};
		
		jQuery.fn.form.settings.rules.email = function(value){
			if(value.match(/^([a-zA-Z0-9_\.\-\+%])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{1,11})+$/)){
				return true;
			}else{
				return false;
			}
		};
		
		jQuery.fn.form.settings.rules.minChecked = function(value, minChecked){
			jQuery(this).closest('.fields').find(':input').off('change.validation').on('change.validation', function(){
				thisForm.form('validate form');
			});
			
			if(jQuery(this).closest('.fields').find(':input:checked').length >= minChecked){
				jQuery(this).closest('.fields').removeClass('error');
				return true;
			}else{
				jQuery(this).closest('.fields').addClass('error');
				return false;
			}
		};
		
		jQuery.fn.form.settings.rules.maxChecked = function(value, maxChecked){
			jQuery(this).closest('.fields').find(':input').off('change.validation').on('change.validation', function(){
				thisForm.form('validate form');
			});
			
			if(jQuery(this).closest('.fields').find(':input:checked').length > maxChecked){
				jQuery(this).closest('.fields').addClass('error');
				return false;
			}else{
				jQuery(this).closest('.fields').removeClass('error');
				return true;
			}
		};
		
		jQuery.fn.form.settings.rules.exactChecked = function(value, exactChecked){
			jQuery(this).closest('.fields').find(':input').off('change.validation').on('change.validation', function(){
				thisForm.form('validate form');
			});
			if(jQuery(this).closest('.fields').find(':input:checked').length != exactChecked){
				jQuery(this).closest('.fields').addClass('error');
				return false;
			}else{
				jQuery(this).closest('.fields').removeClass('error');
				return true;
			}
		};
		
		thisForm.find('[data-validationrules]').each(function(i, inp){
			if(jQuery(inp).data('validationrules').disabled == undefined || jQuery(inp).data('validationrules').disabled == 0){
				validationRules['field'+i] = jQuery(inp).data('validationrules');
				
				//jQuery.each(['empty', 'required', 'checked', 'minChecked', 'maxChecked', 'exactChecked'], function(i, r){
				jQuery.each(jQuery(inp).data('validationrules')['rules'], function(i, r){
					//if(jQuery(inp).data('validationrules')['rules'][0]['type'].indexOf(r) >= 0){
					jQuery.each(['empty', 'required', 'checked', 'minChecked', 'maxChecked', 'exactChecked'], function(ir, vr){
						if(r['type'].indexOf(vr) > -1){
							if(jQuery(inp).parent().hasClass('checkbox')){
								if(jQuery(inp).closest('.fields').length > 0){
									jQuery(inp).closest('.fields').addClass('required');
								}else{
									jQuery(inp).closest('.field').addClass('required');
								}
							}else{
								jQuery(inp).closest('.field').addClass('required');
							}
						}
					});
				});
			}
		});
		
		thisForm.form({
			//inline : true,
			inline : thisForm.data('valloc') ? ((thisForm.data('valloc') == 'inline' || thisForm.data('valloc') == 'inlinetext') ? true : false) : true,
			on : 'blur',
			fields: validationRules,
			onInvalid: function(){
				if(thisForm.data('valloc') == 'inlinetext'){
					var erlabel = $(this).closest('.field').find('.ui.label.prompt.pointing').first();
					erlabel.css('display', 'none !important');
					var field = $(this).closest('.multifield.fields.grouped, .multifield.fields.inline').length > 0 ? $(this).closest('.multifield.fields.grouped, .multifield.fields.inline') : $(this).closest('.field');
					if(field.find('small.custom-error').length > 0){
						field.find('small.custom-error').show();
					}else{
						field.append($('<small class="custom-error">').css('color', 'red').css('display', 'block').text(erlabel.text()));
					}
					erlabel.remove();
				}
			},
			onValid: function(){
				var field = $(this).closest('.multifield.fields.grouped, .multifield.fields.inline').length > 0 ? $(this).closest('.multifield.fields.grouped, .multifield.fields.inline') : $(this).closest('.field');
				field.find('small.custom-error').hide();
			}
		});
	}
	
	$.G2.forms.initializeEvents = function (thisForm){
		thisForm.find('[data-events]').each(function(i, inp){
			//var events = jQuery(inp).data('events');
			var events = JSON.parse(jQuery(inp).attr('data-events'));
			
			jQuery(inp).off('change.events click.events ready.events');
			jQuery.each(events, function(ei, event){
				//jQuery(inp).off('change click ready');
				jQuery(inp).on('change.events click.events ready.events', function($eve){
					
					if(event.hasOwnProperty('identifier') != true || event['identifier'] == '' || event.hasOwnProperty('action') != true || event.action.length == 0){
						return;
					}
					
					var event_condition = false;
					var inp_value = jQuery(inp).data('value') ? jQuery(inp).data('value') : jQuery(inp).val();
					
					if(jQuery(inp).attr('type') == 'checkbox'){
						inp_value = (jQuery(inp).is(':checked') ? inp_value : '');
					}
					if(jQuery(inp).prop('tagName') == 'SELECT'){
						inp_value = jQuery(inp).find(':selected').data('value') ? jQuery(inp).find(':selected').data('value') : jQuery(inp).val();
					}
					
					if(event.hasOwnProperty('value') != true){
						event['value'] = [jQuery(inp).val()];
					}
					if(event.hasOwnProperty('group') && event.group == 1){
						inp_value = [];
						jQuery.each(jQuery(inp).closest('.fields').find(':input:checked'), function(kk, checked){
							if(jQuery(checked).data('value')){
								inp_value.push(jQuery(checked).data('value'));
							}else{
								inp_value.push(jQuery(checked).val());
							}
						});
					}
					
					if(jQuery.isArray(inp_value)){
						if(event.sign == '='){
							//event_condition = (jQuery.inArray(event['value'], inp_value) > -1);
							event_condition = (jQuery(inp_value).filter(event['value']).length > 0);
						}else if(event.sign == '!='){
							//event_condition = (jQuery.inArray(event['value'], inp_value) == -1);
							event_condition = (jQuery(inp_value).filter(event['value']).length == 0);
						}else if(event.sign == 'change'){
							if($eve.type != 'ready'){
								event_condition = true;
							}
						}
					}else{
						if(event.sign == '='){
							//event_condition = (inp_value == event['value']);
							event_condition = (jQuery([inp_value]).filter(event['value']).length > 0);
						}else if(event.sign == '!='){
							//event_condition = (inp_value != event['value']);
							event_condition = (jQuery([inp_value]).filter(event['value']).length == 0);
						}else if(event.sign == 'change'){
							if($eve.type != 'ready'){
								event_condition = true;
							}
						}else if(event.sign == 'click' && $eve.type == 'click'){
							event_condition = true;
						}
					}
					
					var event_targets = [];
					jQuery.each(event['identifier'], function(idi, ident){
						if(ident.substring(0, 1) == '#' || ident.substring(0, 1) == '.' || ident.substring(0, 1) == '['){
							event_targets = jQuery.merge(event_targets, jQuery(ident));
						}else{
							event_targets = jQuery.merge(event_targets, jQuery(':input[name="' + ident + '"]'));
							if(jQuery.inArray('function', event.action) > -1){
								event_targets = [ident];
							}
						}
					});
					
					jQuery.each(event_targets, function(ix, event_target){
						event_target = jQuery(event_target);
						var event_target_one = event_target;
						
						var target_element = event_target.closest('.field');
						if(jQuery.inArray(event_target.prop('tagName'), ['BUTTON', 'DIV']) > -1){
							target_element = event_target;
						}
						if(jQuery.inArray(event_target.prop('type'), ['checkbox', 'radio']) > -1){
							target_element = event_target.closest('.multifield.fields');
						}
						
						if(event_target.data('ghost')){
							var real_event_target = event_target.closest('.multifield.fields').find(':checkbox, :radio');
							target_element = event_target.closest('.multifield.fields');
							if(real_event_target.length > 0){
								event_target = real_event_target;
								event_target_one = real_event_target.first();
							}
						}
						
						if(jQuery.isArray(event.action) == false){
							event.action = [event.action];
						}
						if(event_condition){
							if(jQuery.inArray('hide', event.action) > -1){
								target_element.hide();
							}
							if(jQuery.inArray('show', event.action) > -1){
								//target_element.show();
								target_element.css('display', '');
								target_element.removeClass('hidden');
							}
							if(jQuery.inArray('disable', event.action) > -1){
								target_element.addClass('disabled');
								event_target.prop('disabled', true);
							}
							if(jQuery.inArray('enable', event.action) > -1){
								target_element.removeClass('disabled');
								event_target.prop('disabled', false);
							}
							if(jQuery.inArray('disable_validation', event.action) > -1){
								if(event_target_one.data('validationrules')){
									var vrules = event_target_one.data('validationrules');
									vrules['disabled'] = 1;
									event_target_one.data('validationrules', vrules);
									
									$.G2.forms.initializeForm(thisForm);
									target_element.removeClass('required error');
									target_element.find('.ui.label.red.pointing.prompt').remove();
								}
							}
							if(jQuery.inArray('enable_validation', event.action) > -1){
								if(event_target_one.data('validationrules')){
									var vrules = event_target_one.data('validationrules');
									vrules['disabled'] = 0;
									event_target_one.data('validationrules', vrules);
									
									$.G2.forms.initializeForm(thisForm);
								}
							}
							if(jQuery.inArray('reload', event.action) > -1){
								if($eve.type != 'ready' && event_target.length > 0){
									target_element.addClass('ui form loading');
									
									$.ajax({
										url: event_target.data('reloadurl'),
										data: jQuery(inp).closest('.form').serialize(),
										success: function(result){
											var newContent = $(result);
											
											target_element.replaceWith(newContent);
											
											$('body').trigger('contentChange');
										}
									});
								}
							}
							if(jQuery.inArray('function', event.action) > -1){
								jQuery.each(event['identifier'], function(idi, ident){
									if($eve.type != 'ready' && window[ident] != undefined){
										window[ident](jQuery(inp));
									}
								});
							}
							//if(jQuery.inArray(event.action, ['add', 'sub', 'multiply', 'set']) > -1){
							if(jQuery(event.action).filter(['add', 'sub', 'multiply', 'set']).length){
								target_element = event_target;
								
								var current_value = parseFloat(target_element.val());
								if(isNaN(current_value)){
									current_value = 0;
								}
								
								if(jQuery.isArray(inp_value)){
									var inp_value_float = 0;
									jQuery.each(inp_value, function(iv, inp_value_v){
										if(!isNaN(parseFloat(inp_value_v))){
											inp_value_float = inp_value_float + parseFloat(inp_value_v);
										}
									});
								}else{
									var inp_value_float = parseFloat(inp_value);
									if(isNaN(inp_value_float)){
										inp_value_float = 0;
										if(event.action == 'multiply'){
											inp_value_float = 1;
										}
									}
								}
								
								var calcList = {};
								var inp_name = jQuery(inp).attr('name');
								
								if(target_element.data('calclist')){
									calcList = target_element.data('calclist');
								}
								
								var prev_inp_value = 0;
								if(calcList.hasOwnProperty(inp_name)){
									prev_inp_value = calcList[inp_name];
								}
								
								calcList[inp_name] = inp_value_float;
								target_element.data('calclist', calcList);
								var change_value = 0;
								
								if(jQuery.inArray('add', event.action) > -1){
									var total = current_value + inp_value_float - prev_inp_value;
								}else if(jQuery.inArray('sub', event.action) > -1){
									var total = current_value - inp_value_float + prev_inp_value;
								}else if(jQuery.inArray('multiply', event.action) > -1){
									if(prev_inp_value == 0){
										prev_inp_value = 1;
									}
									var total = (current_value/prev_inp_value) * inp_value_float;
								}else if(jQuery.inArray('set', event.action) > -1){
									var total = inp_value_float;
								}
								
								target_element.val(total);
								
								if(target_element.data('display')){
									jQuery('#'+target_element.data('display')).text(total);
								}
							}
						}
					});
				});
				
				jQuery(inp).trigger('ready.events');
			});
		});
	}
	
	$.G2.forms.initializeFeatures = function (thisForm){
		thisForm.find('.partitioned .ui.button.next, .partitioned .ui.button.forward').off('click');
		thisForm.find('.partitioned .ui.button.next, .partitioned .ui.button.forward').on('click', function(e){
			e.preventDefault();
			var activeTab = jQuery(this).closest('.partitioned').find('.ui.segment.tab.active').first();
			activeTab.find(':input').trigger('blur');
			
			if(activeTab.next('.ui.segment.tab').length > 0 && activeTab.find('.field.error').length == 0){
				activeTab.removeClass('active');
				jQuery('[data-tab="'+activeTab.data('tab')+'"]').removeClass('active');
				activeTab.next('.ui.segment.tab').addClass('active');
				jQuery('[data-tab="'+activeTab.next('.ui.segment.tab').data('tab')+'"]').addClass('active').removeClass('disabled');
			}else{
				
			}
		});
		
		thisForm.find('.partitioned .ui.button.prev, .partitioned .ui.button.backward').off('click');
		thisForm.find('.partitioned .ui.button.prev, .partitioned .ui.button.backward').on('click', function(e){
			e.preventDefault();
			var activeTab = jQuery(this).closest('.partitioned').find('.ui.segment.tab.active').first();
			activeTab.find(':input').trigger('blur');
			
			if(activeTab.prev('.ui.segment.tab').length > 0 && activeTab.find('.field.error').length == 0){
				activeTab.removeClass('active');
				jQuery('[data-tab="'+activeTab.data('tab')+'"]').removeClass('active');
				activeTab.prev('.ui.segment.tab').addClass('active');
				jQuery('[data-tab="'+activeTab.prev('.ui.segment.tab').data('tab')+'"]').addClass('active').removeClass('disabled');
			}else{
				
			}
		});
		
		thisForm.find('.repeater .ui.source-item').hide().find(':input').prop('disabled', true);
		thisForm.find('.repeater .ui.source-item').hide().find(':input').each(function(i, inp){
			if(jQuery(inp).data('validationrules')){
				var vrules = jQuery(inp).data('validationrules');
				vrules['disabled'] = 1;
				jQuery(inp).data('validationrules', vrules);
			}
		});
		
		thisForm.find('.repeater .ui.button.multiply').off('click.repeater');
		thisForm.find('.repeater .ui.button.multiply').on('click.repeater', function(e){
			e.preventDefault();
			
			var cloned = jQuery(this).closest('.repeater').find('.ui.source-item').clone().show();
			cloned.find(':input').prop('disabled', false);
			
			var newHTML = cloned.html().replace(/-N-/g, jQuery(this).closest('.repeater').data('count'));
			if(cloned.data('name')){
				repeaterRegex = new RegExp('#'+cloned.data('name')+'.count', 'gi');
				newHTML = newHTML.replace(repeaterRegex, jQuery(this).closest('.repeater').data('count'));
			}
			
			cloned.html(newHTML);
			jQuery(this).closest('.repeater').data('count', parseInt(jQuery(this).closest('.repeater').data('count')) + 1);
			
			if(jQuery(this).closest('.repeater').data('limit')){
				if(jQuery(this).closest('.repeater').find('.clone-item').length >= parseInt(jQuery(this).closest('.repeater').data('limit'))){
					return;
				}
			}
			jQuery(this).before(cloned.removeClass('source-item').addClass('clone-item'));
			
			jQuery('body').trigger('contentChange');
		});
		
		thisForm.find('.repeater .ui.button.remove').off('click.repeater');
		thisForm.find('.repeater .ui.button.remove').on('click.repeater', function(e){
			e.preventDefault();
			
			jQuery(this).closest('.ui.clone-item').remove();
			
			jQuery('body').trigger('contentChange');
		});
		
		thisForm.find('.modaled > .ui.button.green, .modaled > .ui.button.launch').off('click');
		thisForm.find('.modaled > .ui.button.green, .modaled > .ui.button.launch').on('click', function(e){
			e.preventDefault();
			var theModal = jQuery(this).closest('.modaled').find('.ui.modal').first();
			theModal.modal({detachable : false, closable : (theModal.data('closable') ? true : false)}).modal('show');
		});
		
		thisForm.on('submit', function(e){
			if(thisForm.form('is valid') == false){
				if(thisForm.find('.field.error').first().is(':visible')){
					jQuery.G2.scrollTo(thisForm.find('.field.error').first());
				}else{
					if(thisForm.find('.field.error').first().closest('.partitioned').length > 0){
						var activeTab = thisForm.find('.field.error').first().closest('.partitioned').find('.ui.segment.tab.active').first();
			
						activeTab.removeClass('active');
						jQuery('[data-tab="'+activeTab.data('tab')+'"]').removeClass('active');
						thisForm.find('.field.error').first().closest('.ui.segment.tab').addClass('active');
						jQuery('[data-tab="'+thisForm.find('.field.error').first().closest('.ui.segment.tab').data('tab')+'"]').addClass('active');
						jQuery('[data-tab="'+thisForm.find('.field.error').first().closest('.ui.segment.tab').data('tab')+'"]').removeClass('disabled');
					}
				}
			}else{
				if(thisForm.data('subanimation')){
					thisForm.addClass('loading');
				}
				//thisForm.form('submit');
			}
		});
	}
	
	$.G2.forms.ready = function(){
		jQuery('div[data-invisible="1"]').each(function(i, invForm){
			var content = jQuery(invForm).html();
			var newForm = jQuery('<form>').html(content);
			jQuery.each(jQuery(invForm).get(0).attributes, function(i, att){
				newForm.attr(att.name, att.value);
			});
			jQuery(invForm).replaceWith(newForm);
			jQuery('body').trigger('contentChange');
		});
		
		jQuery('.G2-form').each(function(fk, Form){
			var thisForm = jQuery(Form);
			jQuery.G2.forms.initializeFeatures(thisForm);
			
			jQuery.G2.forms.initializeEvents(thisForm);
			//fields_initialize_duplicators(thisForm);
			jQuery.G2.forms.initializeForm(thisForm);
			//masks
			if(jQuery.fn.inputmask != undefined){
				thisForm.find(':input').inputmask();
			}
			//$('body').trigger('contentChange');
			if($.G2.actions != undefined){
				jQuery.G2.actions.list[thisForm.data('id')] = {
					'beforeStart' : function(element){
						if(element.form('is valid') != true){
							return false;
						}
					}
				};
			}
		});
	}
	
}(jQuery));