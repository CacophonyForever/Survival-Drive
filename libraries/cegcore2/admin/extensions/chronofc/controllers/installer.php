<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronofc\C;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
trait Installer {
	
	function _install($ext_name){
		//apply updates
		$sql = file_get_contents(\G2\Globals::ext_path($ext_name, 'admin').'sql'.DS.'install.'.$ext_name.'.sql');
		
		$queries = \G2\L\Database::getInstance()->split_sql($sql);
		
		foreach($queries as $query){
			\G2\L\Database::getInstance()->exec(\G2\L\Database::getInstance()->_prefixTable($query, true));
		}
		
		\GApp::session()->flash('success', rl('Database tables have been installed.'));
		$this->redirect(r2('index.php?ext='.$ext_name.'&act=clear_cache'));
	}
	
}
?>