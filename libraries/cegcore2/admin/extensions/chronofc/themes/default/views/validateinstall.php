<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="ui container fluid">
	<div class="ui header"><?php el('Validate your installation'); ?></div>
	<form action="<?php echo r2('index.php?ext='.$this->get('ext_name').'&act=validateinstall'); ?>" method="post" name="admin_form" id="admin_form" class="ui form">
		<div class="field">
			<label>Domain name detected: <input type="text" readonly value="<?php echo $domain; ?>">
			<input type="hidden" name="domain_name" value="<?php echo $domain; ?>">
		</div>
		
		<div class="field">
			<label><?php el('Your validation key generated on ChronoEngine.com'); ?></label>
			<input type="text" name="license_key" value="">
		</div>
		
		<div class="field">
			<label><?php el('Serial number (optional)'); ?></label>
			<input type="text" name="serial_number" value="">
		</div>
		
		<button class="compact ui button green icon labeled">
		<i class="checkmark icon"></i>Validate
		</button>
	</form>
</div>