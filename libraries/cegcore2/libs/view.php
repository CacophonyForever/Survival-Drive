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
class View {
	var $site = '';
	var $extension = '';
	var $controller = '';
	var $action = '';
	
	var $view = true;
	var $vars = array();
	var $data = array();
	var $errors = array();
	var $views_site = '';
	var $layouts = [];
	var $theme = 'default';
	var $tvout = 'view';

	function __construct(&$controller = null){
		/*if(!empty($controller->_vars)){
			$this->vars = $controller->_vars;
		}*/
		
		$this->site = \GApp::instance()->site;
		$this->extension = \GApp::instance()->extension;
		$this->controller = \GApp::instance()->controller;
		$this->action = \GApp::instance()->action;
		
		$this->views_site = $this->site;
		
		if(!empty($controller)){
			$this->vars = &$controller->_vars;
			$this->theme = $controller->theme;
			$this->layouts = array_merge($this->layouts, $controller->layouts);
			$this->view = $controller->view;
			//$this->views_dir = isset($controller->views_dir) ? $controller->views_dir : '';
			$this->views_site = isset($controller->views_site) ? $controller->views_site : $this->site;
			$this->data = &$controller->data;
			$this->errors = $controller->errors;
			$this->tvout = $controller->tvout;
			//set helpers properties
			if(!empty($controller->helpers)){
				$controller->helpers = (array)$controller->helpers;
				foreach($controller->helpers as $k => $helper){
					$config = array();
					if(is_string($k)){
						$config = $helper;
						$helper = $k;
					}
					$alias = Base::getClassName($helper);
					
					if(empty($this->$alias)){
						$this->$alias = new $helper($this, $config);
					}
				}			
			}
		}
	}
	
	function renderView($action = ''){
		if($this->view === false){
			return false;
		}
		
		if(!empty($this->view)){
			$action = $this->view;
		}
		
		$action_file = $this->get_file('views', $action);
		
		if(file_exists($action_file)){
			//view file exists, load it
			if(!empty($this->layouts) AND $this->tvout != 'view'){
				$output = '{VIEW}';
				
				$output = $this->renderLayouts($this->layouts, $output);
				
				return str_replace('{VIEW}', $this->get_contents($action_file), $output);
			}
			return $this->get_contents($action_file);
		}
	}
	
	function renderLayouts($layouts, $output){
		foreach($layouts as $layoutpath => $layout){
			
			$layout_file = $this->get_file('layouts', $layout);
			
			$layout_content = $this->get_contents($layout_file);
			$output = str_replace('{VIEW}', $layout_content, $output);
		}
		return $output;
	}
	
	private function get_contents($__file__, $__vars__ = []){
		if(empty($__vars__)){
			foreach($this->vars as $k => $val){
				$$k = $val;
			}
		}
		
		foreach($__vars__ as $k => $val){
			$$k = $val;
		}
		
		$contents = '';
		
		if(file_exists($__file__)){
			ob_start();
			include($__file__);
			$contents = ob_get_clean();
		}
		
		if(!empty($this->data)){
			$DataLoader = new \G2\H\DataLoader();
			$contents = $DataLoader->load($contents, Request::raw());
			unset($DataLoader);
			
			$ErrorLoader = new \G2\H\ErrorLoader();
			$contents = $ErrorLoader->load($contents, $this->errors);
			unset($ErrorLoader);
		}
		return $contents;
	}
	
	private function get_path($type = 'views', $section = 'extension'){
		if($section == 'extension'){
			$strings = array(\G2\Globals::ext_path(\GApp::instance()->getMirrored('ext', $this->extension), $this->views_site).'themes');
		}else if($section == 'site'){
			$strings = array(\G2\Globals::get('ADMIN_PATH').'themes');
		}
		//$strings[] = 'themes';
		$strings[] = $this->theme;
		
		if($type == 'views'){
			$strings[] = 'views';
			if(!empty($this->controller)){
				$strings[] = \GApp::instance()->getMirrored('cont', $this->controller);
			}
			
		}else if($type == 'layouts'){
			$strings[] = 'layouts';
			
		}else if($type == 'theme'){
			
		}
		
		return implode(DS, $strings).DS;
	}
	
	private function get_file($type, $name, $section = 'extension'){
		if(strpos($name, DS) !== false){
			$file = $name;
		}else{
			if(strpos($name, '.') !== false){
				$theme_path = $this->get_path('theme', $section);
				$chunks = explode('.', $name);
				$file = $theme_path.implode(DS, $chunks).'.php';
			}else{
				$type_path = $this->get_path($type, $section);
				$file = $type_path.$name.'.php';
			}
		}
		
		if(file_exists($file) === false){
			if($section != 'site'){
				return $this->get_file($type, $name, 'site');
			}else{
				return false;
			}
		}
		
		return $file;
	}
	
	public function view($path, $vars = [], $return = false){
		
		$view_file = $this->get_file('views', $path);
		
		$content = '';
		if($view_file !== false){
			$content = $this->get_contents($view_file, $vars);
		}
		
		if($return){
			return $content;
		}
		echo $content;
	}
	
	function layout($layout){
		echo $output = $this->renderLayouts([$layout], '{VIEW}');
	}
	
	public function get($var, $default = null){
		$value = Arr::getVal($this->vars, $var, $default);
		
		return $value;
	}
	
	public function set($var, $value){
		$this->vars = Arr::setVal($this->vars, $var, $value);
	}
	
	function data($key, $default = null, $setter = false){
		if($setter){
			$this->data = Arr::setVal($this->data, explode('.', $key), $default);
			return $default;
		}else{
			$value = Arr::getVal($this->data, explode('.', $key), $default);
			return $value;
		}
	}
	
}