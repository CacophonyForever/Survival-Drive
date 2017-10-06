<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronoforms\C;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Languages extends \G2\L\Controller {
	use \G2\A\E\Chronofc\C\Languages;
	
	function _initialize(){
		$this->layout('default');
	}
	
	function index(){
		$this->_index('chronoforms');
	}
	
	function build(){
		$this->_build('chronoforms');
	}
}
?>