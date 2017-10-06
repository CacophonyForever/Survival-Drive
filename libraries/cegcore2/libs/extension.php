<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace G2\L;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Extension {
	var $name = '';
	var $settings = null;
	
	function __construct($ext){
		$this->name = $ext;
	}
	
	public static function getInstance($ext){
		static $instances;
		if(!isset($instances)){
			$instances = array();
		}
		if(empty($instances[$ext])){
			$instances[$ext] = new self($ext);
			return $instances[$ext];
		}else{
			return $instances[$ext];
		}
	}

	public function settings(){
		if(!empty($this->settings)){
			return $this->settings;
		}else{
			$Extension = new \G2\A\M\Extension();
			$settings = $Extension->where('name', $this->name)->select('first');
			if(!empty($settings['Extension']['settings'])){
				return $this->settings = new Parameter($settings['Extension']['settings']);
			}else{
				return $this->settings = new Parameter('');
			}
		}
	}
	
	public function valid($group = ''){
		$settings = $this->settings();
		$act = 'validated';
		if(!empty($group)){
			$act .= '_'.$group;
		}
		
		return ((bool)$settings->get($act, 0) === true);
	}
}