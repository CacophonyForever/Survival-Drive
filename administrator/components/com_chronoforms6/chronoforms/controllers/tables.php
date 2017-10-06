<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronoforms\C;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Tables extends \G2\A\E\Chronofc\C\Tables {
	
	function _initialize(){
		parent::_initialize();
		$this->layout(\G2\Globals::ext_path('chronoforms', 'admin').'themes'.DS.'default'.DS.'layouts'.DS.'default.php');
		
		\GApp::instance()->setMirror('ext', 'chronofc', 'chronoforms');
		
		$this->Connection = new \G2\A\E\Chronoforms\M\Connection();
	}
}
?>