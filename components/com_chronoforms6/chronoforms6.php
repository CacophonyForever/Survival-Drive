<?php
/**
* COMPONENT FILE HEADER
**/
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or define("GCORE_SITE", "front");
jimport('cegcore2.joomla_gcloader');
if(!class_exists('JoomlaGCLoader2')){
	JError::raiseWarning(100, "Please download the CEGCore framework from www.chronoengine.com then install it using the 'Extensions Manager'");
	return;
}

$chronoforms6_setup = function(){
	$mainframe = \JFactory::getApplication();
	//$conn = G2\L\Request::data('chronoform', '');
	$params = $mainframe->getPageParameters('com_chronoforms6');
	$connection = $params->get('form_name', '');
	$extra = $params->get('form_params', '');
	$controller = null;//'manager';//G2\L\Request::data('cont', 'manager');
	$ps = [];
	if(!empty($connection)){
		if(!empty($extra)){
			parse_str($extra, $ps);
		}
		return array_merge(array('chronoform' => $connection, 'controller' => $controller), $ps);
	}else{
		return array('controller' => $controller);
	}
};

$output = new JoomlaGCLoader2('front', 'chronoforms6', 'chronoforms', $chronoforms6_setup);