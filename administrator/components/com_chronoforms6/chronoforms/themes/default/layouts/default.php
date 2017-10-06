<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="ui menu inverted">
	<a class="item icon blue <?php if($this->action == 'index' AND $this->controller == ''): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms'); ?>">
		<i class="home icon"></i>
	</a>
	<a class="item blue <?php if($this->controller == 'connections'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&cont=connections'); ?>">
		<i class="folder icon"></i><?php el('Forms'); ?>
	</a>
	<a class="item blue <?php if($this->controller == 'blocks'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&cont=blocks'); ?>">
		<i class="cubes icon"></i><?php el('Blocks'); ?>
	</a>
	
	<a class="item blue <?php if($this->action == 'install_feature'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&act=install_feature'); ?>">
		<i class="magic icon"></i><?php el('Install action'); ?>
	</a>
	
	<a class="item blue <?php if($this->action == 'clear_cache'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&act=clear_cache'); ?>">
		<i class="trash icon"></i><?php el('Clear cache'); ?>
	</a>
	<a class="item blue <?php if($this->controller == 'languages'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&cont=languages'); ?>">
		<i class="translate icon"></i><?php el('Languages'); ?>
	</a>
	
	<a class="item blue <?php if($this->action == 'validateinstall'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&act=validateinstall'); ?>">
		<i class="checkmark green icon"></i><?php el('Validate'); ?>
	</a>
	
	<a class="item yellow" href="<?php echo r2('index.php?ext=chronoforms&cont=installer'); ?>">
		<i class="search icon"></i><?php el('Apply updates'); ?>
	</a>
	
	<a class="item blue <?php if($this->action == 'info'): ?>active<?php endif; ?>" href="<?php echo r2('index.php?ext=chronoforms&act=info'); ?>">
		<i class="keyboard icon"></i><?php el('Shortcodes'); ?>
	</a>
	
	<div class="right item red">
		<i class="red warning icon"></i><?php el('Server capacity'); ?>: <?php echo ini_get('max_input_vars'); ?>
	</div>
	
</div>

<?php if((bool)\GApp::extension('chronoforms')->settings()->get('validated', 0) !== true): ?>
<div class="ui message red">
	Your ChronoForms installation on <strong><?php echo str_replace(array('http://', 'https://'), '', \G2\L\Url::domain()); ?></strong> is NOT validated, validate now to be able to display more than 15 designer elements and to remove the credits link displayed under the forms.
</div>
<?php endif; ?>

<div class="ui segment fluid container">
	{VIEW}
</div>