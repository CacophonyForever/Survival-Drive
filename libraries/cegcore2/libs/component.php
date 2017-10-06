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
class Component {
	var $config = [];
	var $settings = [];
	
	function __construct($config = []){
		if(!empty($config)){
			foreach($config as $k => $v){
				if(is_numeric($k) AND is_array($v)){
					foreach($v as $setk => $setv){
						$this->set($setk, $setv);
					}
				}else{
					$this->$k($v);
					$this->config[] = $k;
				}
			}
		}
		
		return $this;
	}
	
	public static function getInstance($com){
		static $instances;
		if(!isset($instances)){
			$instances = array();
		}
		if(empty($instances[$com])){
			$com_name = '\G2\Com\\'.Str::camilize($com).'\\'.Str::camilize($com);
			$instances[$com] = new $com_name();
			return $instances[$com];
		}else{
			return $instances[$com];
		}
	}
	
	public function reset(){
		$vars = get_object_vars($this);
		foreach($vars as $k => $v){
			if($k != 'config' AND $k != 'settings' AND !in_array($k, $this->config)){
				if(is_array($v)){
					$this->$k = [];
				}else if(is_object($v)){
					//do nothing
				}else{
					$this->$k = null;
				}
			}
		}
	}
	
	public function settings($settings = []){
		$this->settings = $settings;
	}
	
	function get($k, $default = null){
		if(isset($this->settings[$k])){
			return $this->settings[$k];
		}else{
			return $default;
		}
		
		return $this;
	}
	
	function set($k, $v){
		$this->settings[$k] = $v;
		return $this;
	}
}