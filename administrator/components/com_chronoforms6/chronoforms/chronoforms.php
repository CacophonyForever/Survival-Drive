<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronoforms;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Chronoforms extends \G2\L\Controller {
	use \G2\A\E\Chronofc\C\Basics;
	var $models = array('\G2\A\M\Group', '\G2\A\M\Acl', '\G2\A\M\Extension');
	var $libs = array('\G2\L\Request');
	var $helpers= array(
		'\G2\H\Html',
		'\G2\H\Sorter',
		'\G2\H\Paginator',
	);
	
	function _initialize(){
		$this->layout('default');
	}
	
	function editor(){
		
	}
	
	function dynamic(){
		
	}
	
	function index(){
		$this->redirect(r2('index.php?ext=chronoforms&cont=connections'));
	}
	
	function install_feature(){
		$this->_install_feature('chronoforms');
	}
	
	function info(){
		
	}
	
	function settings(){
		$this->_settings('chronoforms');
	}
	
	function save_settings(){
		$this->_save_settings('chronoforms');
	}
	
	function clear_cache(){
		$this->_clear_cache('chronoforms');
	}
	
	function validateinstall(){
		$this->_validateinstall('chronoforms', 'forms');
	}
}
?>