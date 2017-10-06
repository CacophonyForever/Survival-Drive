<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronoforms\C;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Installer extends \G2\L\Controller {
	use \G2\A\E\Chronofc\C\Installer;
	var $models = array('\G2\A\M\Acl');
	
	function index(){
		$this->_install('chronoforms');
	}
	
}
?>