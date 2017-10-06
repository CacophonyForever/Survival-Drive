<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<?php
	ob_start();
?>
<style>
.active_droppable{border:2px solid black !important;}
.draggable{cursor:move;}
.advanced_conf{display:none !important;}
.draggable-receiver{background-color:#eef8e8 !important;}
.draggable-receiver.red{background-color:#ffefea !important;}
.ui.segment.active.functions-tab{background-color:#f2f2f2;}
.ui.segment.active.views-tab{background-color:#f2f2f2;}

.ui.segment.compact .view_section{display:none !important;}
.ui.segment.compact .function_event{display:none !important;}

.mce-tinymce-inline.mce-floatpanel{display:none !important;}
</style>
<script>
	//jQuery('form').addClass('loading');
	jQuery(document).ready(function($) {
		$('.functions-list .draggable, .views-list .draggable').draggable({
			'helper' : 'clone',
			//'revert' : 'invalid',
			connectToSortable: '.draggable-receiver',
			start: function(e, ui){
				$(ui.helper).css('z-index', 9999);
				$(ui.helper).css('min-width', '200px');
			}
		});
		
		//initMovable($('.dragged_item'));
		
		//$('.draggable-receiver').disableSelection();
		
		$.each($('.dragged_item'), function(k, func){
			initFnIcons($(func));
			$(func).find('.config_area').first().find('.functions-tab').first().removeClass('tab');
			$(func).find('.config_area').first().find('.views-tab').first().removeClass('tab');
		});
		
		//initDroppable($('.draggable-receiver'));
		
		$('body').on('contentChange', function(){
			initDroppable($('.draggable-receiver'));
		});
		
		function initDroppable(item){
			/*item.droppable({
				greedy: true,
				tolerance: 'pointer',
				accept: '.draggable, .dragged_item',
				drop: function(event, ui){
					if($(ui.draggable).data('info')){
						drop($(ui.draggable), $(this));
						//update the inputs counter
						//$('#inputs_counter').html($('#admin_form').find(':input').length);
					}else{
						//if($(ui.draggable).hasClass('active_sortable') == false){
							//$(this).append($(ui.draggable).attr('style', ''));
							//$(ui.draggable).find('.dragged_parent').first().val($(this).data('name'));
							//the above line moved to the sorting function
						//}
					}
					$('.draggable-receiver').removeClass('active_droppable');
					$(ui.draggable).removeClass('compact');
				},
				over: function( event, ui ) {
					//if($(ui.draggable).hasClass('active_sortable') == false){
						$(this).addClass('active_droppable');
					//}
				},
				out: function( event, ui ) {
					$(this).removeClass('active_droppable');
				},
				activate: function( event, ui ) {
					if($(ui.draggable).hasClass('dragged_item') == true){
						//$(ui.draggable).addClass('compact');
					}
				},
				deactivate: function( event, ui ) {
					//$(ui.draggable).removeClass('compact');
				}
			});*/
			
			item.sortable({
				//items: item.children('div.dragged_item'), this is causing the sorted item to get into children lists
				//containment:'parent',
				//axis:'y',
				connectWith: ".draggable-receiver",
				scroll:false,
				handle:'.sort_link',
				placeholder: 'ui segment inverted yellow',
				start: function( event, ui ) {
					//$(ui.item).addClass('compact');
				},
				sort: function( event, ui ) {
					$(ui.item).addClass('active_sortable');
					//$(this).addClass('active_droppable');
				},
				receive: function( event, ui ) {
					if($(ui.helper).data('info')){
						$(ui.helper).css('width', '');
						$(ui.helper).find('.segment').addClass('fluid loading');
						drop($(ui.helper), $(this));
					}
					$(ui.item).find('.dragged_parent').first().val($(this).data('name'));
				},
				update: function( event, ui ) {
				
				},
				stop: function( event, ui ) {
					$(ui.item).removeClass('active_sortable');
					//$(this).removeClass('active_droppable');
				},
				over: function( event, ui ) {
					$(this).addClass('active_droppable');
				},
				out: function( event, ui ) {
					$(this).removeClass('active_droppable');
				},
				tolerance: 'pointer'
			});
		}
		
		function initMovable(item){
			/*item.draggable({
				handle:'.drag_link',
				'revert' : 'invalid',
				zIndex:1000,
			});*/
		}
		
		function initFnIcons(newFunc){
			var edit_link = newFunc.find('.edit_link').first();
			edit_link.on('click', function(){
				newFunc.find('.config_area').first().transition('slide down');
				
				if(newFunc.find('.config_area').first().hasClass('visible') == false){
					//jQuery.G2.scrollTo(newFunc);
					edit_link.addClass('circular inverted');
				}else{
					edit_link.removeClass('circular inverted');
				}
			});	
			
			newFunc.find('.config_area').first().find('.close_config').on('click', function(){
				newFunc.find('.edit_link').first().trigger('click');
			});
			//add sort link
			var sort_link = newFunc.find('.sort_link').first();
			//add delete link
			var delete_link = newFunc.find('.delete_link').first();
			
			delete_link.on('click', function(){
				newFunc.transition({
					'animation' : 'fly right', 
					'onComplete' : function(){
						newFunc.remove();
					}
				});
			});
			//add copy link
			var copy_link = newFunc.find('.copy_link').first();
			
			copy_link.on('click', function(){
				var blockType = $(this).data("block");
				newFunc.addClass('loading');
				
				$.ajax({
					url: "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=copy_element&tvout=view'); ?>" + "&block="+$(this).data("block")+"&count=" + (parseInt($('#'+blockType+'s-count').val()) + 1),
					data: newFunc.find(':input').serialize(),
					type: 'POST',
					//proccessData: false,
					success: function(result){
						var newFunc2 = $(result);
						
						initFnIcons(newFunc2);
						if(newFunc2.find('.dragged_item').length > 0){
							newFunc2.find('.dragged_item').each(function(di, newFunc2child){
								initFnIcons($(newFunc2child));
							});
						}
						
						newFunc.after(newFunc2);
						newFunc.removeClass('loading');
						
						$('#'+blockType+'s-count').val(parseInt($('#'+blockType+'s-count').val()) + 1 + newFunc.find('.dragged_item').length);
						
						$('body').trigger('contentChange');
					}
				});
			});
			/*
			newFunc.children('.icon.link').on('mouseover', function(){
				$(this).addClass('circular inverted');
			}).on('mouseleave', function(){
				$(this).removeClass('circular inverted');
			});
			*/
		}
		
		function drop(draggable, droppable){
			var blockType = draggable.data('type');
			var dropInfo = draggable.data('info');
			var type = dropInfo.name;
			$('#'+blockType+'s-count').val(parseInt($('#'+blockType+'s-count').val()) + 1);
			
			//get the view config
			//droppable.addClass('loading');
			
			$.ajax({
				url: "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=block_config&tvout=view'); ?>",
				data: {'block' : blockType+'s', 'name' : type, 'count' : $('#'+blockType+'s-count').val()},
				success: function(result){
					var newFunc = $(result);
					
					initFnIcons(newFunc);
					if(newFunc.find('.dragged_item').length > 0){
						newFunc.find('.dragged_item').each(function(di, newFuncchild){
							initFnIcons($(newFuncchild));
						});
					}
					//droppable.append(newFunc);
					draggable.replaceWith(newFunc);
					//set events as droppables
					if(newFunc.find('.draggable-receiver').length > 0){
						initDroppable(newFunc.find('.draggable-receiver'));
					}
					//make it movable
					//initMovable(newFunc);
					//set the parent event value
					newFunc.find('.dragged_parent').first().val(droppable.data('name'));
					//apply the auto name from label
					newFunc.find('.field_label').on('keyup change', function(){
						newFunc.find('.field_label_slug').each(function(iinp, inpauto){
							$(inpauto).val(newFunc.find('.field_label').first().val().toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'_'));
							if($(inpauto).data('brackets')){
								$(inpauto).val($(inpauto).val()+'[]');
							}
						});
					});
					newFunc.find('.field_label_slug').on('keyup', function(){
						$(this).removeClass('field_label_slug');
					});
					
					//droppable.removeClass('loading');
					$('body').trigger('contentChange');
					//delete_block();
				}
			});
		}
		
		$('[data-class="preview-tab"]').each(function(i, section_tab){
			$(section_tab).on('click', function(){
				var section = $(section_tab).data('name');
				
				$('#'+section+'-preview').addClass('loading');
				
				var section_data = $('#'+section+'-general').find(':input').serializeArray();
				var data2 = {};
				jQuery.each(section_data, function(k, section_object){
					data2[section_object.name] = section_object.value;
				});
				
				$.ajax({
					url: "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=preview_section&tvout=view'); ?>",
					data: data2,
					method: 'POST',
					success: function(result){
						var precontent = $(result);
						precontent.find('button[type="submit"]').each(function(b, but){
							$(but).attr('type', 'button');
						});
						$('#'+section+'-preview').html(precontent);
						$('#'+section+'-preview').removeClass('loading');
						$('body').trigger('contentChange');
					}
				});
			});
		});
		
		main('event', "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=events_config&tvout=view'); ?>");
		main('section', "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=sections_config&tvout=view'); ?>");
		
		function main(type, configURL){
			//events code
			$('#new_' + type + '_name').on('keyup mouseup', function(){
				if($(this).val()){
					$('#add_new_' + type + '').removeClass('disabled');
				}else{
					$('#add_new_' + type + '').addClass('disabled');
				}
			});
			
			$('#add_new_' + type + '').on('click', function(){
				
				$('#add_new_' + type + '').closest('.segment').addClass('loading');
				
				$.ajax({
					url: configURL,
					data: {'name' : $('#new_' + type + '_name').val()},
					success: function(result){
						$('.' + type + 's-tab').removeClass('active');
						//$('.' + type + 's-data').append(result);
						$('#add_new_' + type + '').closest('.ui.input').before(result);
						$('.' + type + 's-tab').last().addClass('active');
						
						$('#add_new_' + type + '').closest('.segment').removeClass('loading');
						
						initDroppable($('.draggable-receiver'));
						
						$('#new_' + type + '_name').val('');
						$('#add_new_' + type + '').addClass('disabled');
						
						$('body').trigger('contentChange');
						//delete_block();
						initEventIcons($('.main-'+type));
					}
				});
			});
		}
		
		locale('locale', "<?php echo r2('index.php?ext=chronoforms&cont=connections&act=locales_config&tvout=view'); ?>");
		function locale(type, configURL){
			//events code
			$('#new_' + type + '_name').on('keyup mouseup', function(){
				if($(this).val()){
					$('#add_new_' + type + '').removeClass('disabled');
				}else{
					$('#add_new_' + type + '').addClass('disabled');
				}
			});
			
			$('#add_new_' + type + '').on('click', function(){
				$('.' + type + 's-list').children().removeClass('active');
				
				$('.' + type + 's-list').append(
					$('<a class="blue item active"></a>')
					.html('<div class="ui red label delete_block"><?php el('Delete'); ?></div>' + $('#new_' + type + '_name').val())
					.attr('data-tab', '' + type + 's-'+$('#new_' + type + '_name').val())
				);
				
				$('#add_new_' + type + '').closest('.segment').addClass('loading');
				
				$.ajax({
					url: configURL,
					data: {'name' : $('#new_' + type + '_name').val()},
					success: function(result){
						$('.' + type + 's-tab').removeClass('active');
						$('.' + type + 's-data').append(result);
						$('.' + type + 's-tab').last().addClass('active');
						
						$('#add_new_' + type + '').closest('.segment').removeClass('loading');
						
						$('#new_' + type + '_name').val('');
						$('#add_new_' + type + '').addClass('disabled');
						
						$('body').trigger('contentChange');
						delete_block();
					}
				});
			});
		}
		
		function delete_block(){
			$('.delete_block').off('click').on('click', function(){
				var block_id = $(this).parent().attr('data-tab');
				$('*[data-tab="'+block_id+'"]').remove();
			});
		}
		
		delete_block();
		
		//manage scrolling position
		jQuery(window).scroll(function(){
			$.each($('.scrollableBox'), function(i, dragList){
				if($(dragList).closest('.ui.segment.tab').hasClass('active')){
					if(jQuery(window).scrollTop() > $(dragList).closest('.ui.segment.tab').offset().top){
						$(dragList).stop().animate({'marginTop': (jQuery(window).scrollTop() - $(dragList).closest('.ui.segment.tab').offset().top + 100) + 'px'}, 'slow');
					}else{
						$(dragList).stop().animate({'marginTop': (0) + 'px'}, 'slow');
					}
				}
			});
		});
		
		initEventIcons($('.main-event'), 'event');
		initEventIcons($('.main-section'), 'section');
		
		function initEventIcons(Events, type){
			$.each(Events, function(k, Eve){
				//add delete link
				var delete_link = $(Eve).find('.main_delete_link').first();
				
				delete_link.off('click');
				delete_link.on('click', function(){
					$(Eve).transition({
						'animation' : 'fly right', 
						'onComplete' : function(){
							$(Eve).remove();
						}
					});
				});
			});
			
			$('.'+type+'s-data').first().sortable({
				items: 'div.main-'+type,
				//containment:'parent',
				//axis:'y',
				scroll:false,
				handle:'.main_sort_link',
				placeholder: 'ui segment inverted yellow',
			});
			
		}
		//build the payments events switchers
		//setup_switchers();
		$('body').on('contentChange', function(){
			setup_switchers();
			
			$('.minimize_link').each(function(i, minimizer){
				$(minimizer).off('click').on('click', function(){
					$('.draggable-receiver[data-name="'+$(minimizer).data('named')+'"]').toggleClass('hidden');
					$('.minimized-shadow[data-name="'+$(minimizer).data('named')+'"]').toggleClass('hidden');
					$(minimizer).toggleClass('minimize');
					$(minimizer).toggleClass('maximize');
					if($(minimizer).hasClass('maximize')){
						$('[data-minimized="'+$(minimizer).data('named')+'"]').val(1);
					}else{
						$('[data-minimized="'+$(minimizer).data('named')+'"]').val(0);
					}
				});
			});
		});
		function setup_switchers(){
			$('[data-event_switcher]').each(function(i, sw){
				toggle_subevents(sw);
				$(sw).on('click, change', function(){
					toggle_subevents(sw);
				});
			});
		}
		function toggle_subevents(sw){
			var ev = $(sw).closest('.dragged_item').children('.draggable-receiver[data-name$="/'+$(sw).val()+'"]');
			var evt = $(sw).closest('.dragged_item').children('.draggable-receiver-title[data-name$="/'+$(sw).val()+'"]');
			if($(sw).prop('checked')){
				ev.removeClass('hidden');
				evt.removeClass('hidden');
			}else{
				ev.children().not('.ui.label').remove();
				ev.addClass('hidden');
				evt.addClass('hidden');
			}
		}
		
		$('body').on('contentChange', function(){
			$('.save_block').on('actions.complete', function(e, data, is_json, newContent){
				if(!data.error){
					//alert($(this).data("completeMessage"));
					var save_button = $(this);
					var text = save_button.text();
					save_button.text($(this).data("completeMessage"));
					save_button.addClass('green');
					save_button.removeClass('black');
					
					setTimeout(function(){
						save_button.text(text);
						save_button.addClass('black');
						save_button.removeClass('green');
					}, 1000);
				}
			});
		});
		
	});
	
	
	function Fields_add_field_event(link){
		var new_event = jQuery(link).closest('.fieldsEvents').clone();
		new_event.find('.delete_button').removeClass('hidden');
		var events_count = 1 + parseInt(jQuery(link).closest('.fields_events_list').find('.fields_events_counter').first().val());
		jQuery(link).closest('.fields_events_list').find('.fields_events_counter').first().val(events_count);
		
		new_event.html(new_event.html().replace(/\[events\]\[[0-9]+\]/g, '[events][' + events_count + ']'));
		
		jQuery(link).closest('.fields_events_list').append(new_event);
		//new_event.find('.ui.dropdown').dropdown('setup menu');
		new_event.find('.ui.dropdown').dropdown('clear');
		new_event.find('.ui.dropdown').dropdown('refresh');
	}
	function Fields_delete_field_event(link){
		jQuery(link).closest('.fieldsEvents').remove();
	}
	//
	function saveform(btn){
		/*if(jQuery('#admin_form').find(':input').length > <?php echo ini_get('max_input_vars'); ?>){
			//alert("<?php el('Your form size has exceeded the server limit, please reduce the number of form elements or adjust your server config first.'); ?>");
			//return false;
		}*/
		btn.closest('form').addClass('loading');
		var chunks_counter = 0;
		var chunks = btn.closest('form').find(':input[name^="Connection"]');//.serialize().match(/.{1,100}/g);
		var maxcount = 900;
		
		//jQuery.each(chunks, function(i, c){
		if(chunks.length > maxcount){
			for(i = 0; i <= chunks.length; i = i + maxcount){
				var $chunk_clone = jQuery('<textarea></textarea>').attr('class', '_chunk').attr('name', '_formchunks['+chunks_counter+']').val(chunks.slice(i, i + maxcount).serialize());
				btn.closest('form').append($chunk_clone.hide());
				chunks_counter++;
			}
			chunks.prop('disabled', true);
		}
		//});
		
		btn.closest('form').submit();
	}
	
</script>
<?php
	$jscode = ob_get_clean();
	\GApp::document()->addHeaderTag($jscode);
	\GApp::document()->_('jquery-ui');
	\GApp::document()->__('keepalive');
	\GApp::document()->_('semantic-ui', ['css' => ['accordion']]);
	//\GApp::document()->_('tooltipster');
	\GApp::document()->_('tinymce');
	
	if($this->get('permissions_deactivated', false)){
		\GApp::document()->addCssCode('a[data-tab$="-permissions"]{display:none !important;}');
	}
?>