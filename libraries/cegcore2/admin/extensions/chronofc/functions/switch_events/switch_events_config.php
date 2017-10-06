<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<script>
	function switch_events_add_events(add_link, n){
		var events_list = jQuery(add_link).closest('.config_area').find('.events_list').first().val().split(',');
		var function_name = jQuery(add_link).closest('.config_area').find('.function_name').first().val();
		jQuery(add_link).closest('.dragged_item').find('.function_event').remove();
		jQuery.each(events_list, function(i, event_name){
			event_name = jQuery.trim(event_name);
			var eve = jQuery('<div class="ui segment function_event draggable-receiver blue" data-name="'+function_name+'/'+event_name+'"><div class="ui label blue">'+event_name+'</div></div>');
			jQuery(add_link).closest('.dragged_item').append(eve);
		});
		jQuery('body').trigger('contentChange');
	}
</script>
<div class="ui segment tab functions-tab active" data-tab="functions-<?php echo $n; ?>">

	<div class="ui top attached tabular menu small G2-tabs">
		<a class="item active" data-tab="functions-<?php echo $n; ?>-general"><?php el('General'); ?></a>
		<a class="item" data-tab="functions-<?php echo $n; ?>-permissions"><?php el('Permissions'); ?></a>
	</div>
	
	<div class="ui bottom attached tab segment active" data-tab="functions-<?php echo $n; ?>-general">
		<input type="hidden" value="switch_events" name="Connection[functions][<?php echo $n; ?>][type]">
		
		<div class="two fields advanced_conf">
			<div class="field">
				<label><?php el('Name'); ?></label>
				<input type="text" value="" name="Connection[functions][<?php echo $n; ?>][name]" class="function_name">
			</div>
		</div>
		
		<div class="field forms_conf">
			<label><?php el('Designer Label'); ?></label>
			<input type="text" value="" name="Connection[functions][<?php echo $n; ?>][label]">
		</div>
		
		<div class="ui segment active" data-tab="functions-<?php echo $n; ?>">
			
			<div class="two fields">
				<div class="field required">
					<label><?php el('Data provider'); ?></label>
					<input type="text" value="" name="Connection[functions][<?php echo $n; ?>][data_provider]">
					<small><?php el('The source data to use for switching, can be a data command for example, {data:field_name}'); ?></small>
				</div>
			</div>
			
			<div class="field">
				<label><?php el('Events - comma separated'); ?></label>
				<input type="text" value="A,B" name="Connection[functions][<?php echo $n; ?>][events]" class="events_list">
				<small><?php el('List of expected values from the data source above, each value will run a different event.'); ?></small>
			</div>
			
			<button type="button" class="ui button small" onclick="switch_events_add_events(this, <?php echo $n; ?>);"><?php el('Update events list'); ?></button>
			
		</div>
		
	</div>
	
	<div class="ui bottom attached tab segment" data-tab="functions-<?php echo $n; ?>-permissions">
		<div class="two fields">
			<div class="field">
				<label><?php el('Owner id value'); ?></label>
				<input type="text" value="" name="Connection[functions][<?php echo $n; ?>][owner_id]">
			</div>
		</div>
		
		<?php $this->view('views.permissions_manager', ['model' => 'Connection[functions]['.$n.']', 'perms' => ['access' => rl('Access')], 'groups' => $this->get('groups')]); ?>
	</div>
	
</div>