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
class App {
	var $path = '';
	var $url = '';
	var $site = GCORE_SITE;
	var $_vars = array();
	var $buffer = '';
	var $extension = '';
	var $controller = '';
	var $action = '';
	var $template = '';
	var $tvout = '';
	var $language = 'en_GB';
	var $direction = 'ltr';
	var $reset = false;
	var $mirrors = [];

	function __construct($site = GCORE_SITE){
		$this->path = \G2\Globals::get('CURRENT_PATH');
		$this->url = \G2\Globals::get('CURRENT_URL');
		$this->language = Config::get('site.language', 'en_gb');
		$this->site = $site;
	}

	public static function instance($site = GCORE_SITE){
		static $instances;
		if(!isset($instances)){
			$instances = array();
		}
		if(empty($instances[$site])){
			/*if(\G2\Globals::get('app')){
				$app = '\G2\L\Apps\App'.strtoupper(\G2\Globals::get('app'));
				$instances[$site] = new $app($site);
			}else{
				$instances[$site] = new self($site);
			}*/
			$instances[$site] = new static($site);
			return $instances[$site];
		}else{
			return $instances[$site];
		}
	}
	/*
	function set($key, $value = null){
		if(is_array($key)){
			$this->_vars = array_merge($this->_vars, $key);
			return;
		}
		$this->_vars[$key] = $value;
	}

	function get($key, $default = null){
		if(isset($this->_vars[$key])){
			return $this->_vars[$key];
		}
		return $default;
	}
	*/
	
	public function get($var, $default = null){
		$value = Arr::getVal($this->_vars, $var, $default);
		
		return $value;
	}
	
	public function set($var, $value){
		$this->_vars = Arr::setVal($this->_vars, $var, $value);
	}
	
	public static function call($site, $extension, $controller = '', $action = '', $params = array()){
		$app = static::instance($site);
		$app->extension = $extension;
		$app->controller = $controller;
		$app->action = $action;
		
		foreach($params as $param => $value){
			$app->set($param, $value);
		}
		
		$app->dispatch(true);
		
		return $app;
	}

	function redirect($url){
		Env::redirect($url);
	}
	
	public static function session(){
		return Session::getInstance(self::config()->get('session.handler', 'php'), array(
			'lifetime' => self::config()->get('session.lifetime', 15)
		));
	}
	
	public static function user(){
		$session = self::session();
		$user = $session->get('user');
		if(empty($user)){
			$user = Authenticate::set_public_user();
		}
		return Obj::getInstance('user', $user);
	}
	
	public static function document(){
		return Document::getInstance();
	}
	
	public static function config(){
		return Config::getInstance();
	}
	
	public static function extension($ext = ''){
		if(empty($ext)){
			$ext = \GApp::instance()->extension;
		}
		return Extension::getInstance($ext);
	}
	
	public static function access($path, $aco, $owner_id = null, $user_id = null){
		return \G2\L\Authorize::authorized(is_array($path) ? $path : 'ext='.$path, $aco, $owner_id, $user_id);
	}
	
	public static function component($com){
		return Component::getInstance($com);
	}
	
	public function setMirror($type, $src, $dest){
		$this->mirrors[$type][$src] = $dest;
	}
	
	public function getMirror($type, $src){
		if(!empty($this->mirrors[$type][$src])){
			return $this->mirrors[$type][$src];
		}else{
			return $src;
		}
	}
	
	public function getMirrored($type, $dest){
		if(!empty($this->mirrors[$type])){
			$src = array_search($dest, $this->mirrors[$type]);
			
			if($src !== false){
				return $src;
			}
		}
		
		return $dest;
	}

	function getBuffer(){
		return $this->buffer;
	}
	
	/*** old code ***/
	function initialize(){
		//start the session
		$this->user();
		//Event::trigger('on_initialize');
	}

	function route(){
		$this->extension = 'chronoforums';
		$this->controller = !empty($params['controller']) ? $params['controller'] : '';
		$this->action = !empty($params['action']) ? $params['action'] : '';
	}

	function dispatch($content_only = false, $check_perm = true){
		//Event::trigger('on_before_dispatch', $this);
		$session = self::session();
		//reset:
		//if no action set, set it to index
		if(strlen(trim($this->action)) == 0){
			$this->action = 'index';
		}
		//set admin path
		$site = '';
		if($this->site == 'admin'){
			$site = '\A';
		}
		//load the extension class
		$controller = !empty($this->controller) ? '\C\\'.\G2\L\Str::camilize($this->controller) : '\\'.\G2\L\Str::camilize($this->extension);
		$extension = !empty($this->extension) ? '\E\\'.\G2\L\Str::camilize($this->extension) : '';
		$classname = '\G2'.$site.$extension.$controller;
		$this->tvout = !empty(\G2\L\Request::data('tvout')) ? \G2\L\Request::data('tvout') : $this->tvout;
		//set referer
		if(!$content_only){
			if(!($this->controller == 'users' AND ($this->action == 'login' OR $this->action == 'logout' OR $this->action == 'register')) AND (!empty($this->extension) OR !empty($this->controller)) AND empty($this->tvout)){
				//$session->set('_referer', Url::current());
			}else{
				//$session->set('_referer', 'index.php');
			}
		}
		
		//$this->set_user();
		
		//if the extension class not found or the action function not found then load an error
		if(!class_exists($classname) OR !in_array($this->action, get_class_methods($classname)) OR substr($this->action, 0, 1) == '_' OR preg_match('/[A-Z]/', $this->action)){
			$this->controller = 'errors';
			$this->action = 'e404';
			//reset the controller
			//$classname = '\G2\C\Errors';
			$this->buffer = 'Page not found';
			\G2\L\Env::e404();
			//we need the rendered content only
			if($content_only){
				return;
			}
		}
		//load language file
		if(!empty($this->extension)){
			\G2\L\Lang::load($this->extension);
		}
		
		if(empty($this->tvout)){
			$doc = \G2\L\Document::getInstance($this->site);
			$doc->_startup();
		}
		
		//load class and run the action
		$contInstance = new $classname($this->site);
		ob_start();
		//set default layout to have the semanticui-body div container
		$contInstance->layouts[] = \G2\Globals::get('FRONT_PATH').'layouts'.DS.'main.php';
		
		$continue = $this->processAction($contInstance, '_initialize');
		
		if($continue !== false){
			$renderView = $this->processAction($contInstance);
			
			if($renderView == true){
				//initialize and render view
				$view = new \G2\L\View($contInstance);
				echo $view->renderView($this->action);
			}
		}

		//get the action output buffer
		$this->buffer = ob_get_clean();
		
		//finalize
		ob_start();
		//$contInstance->_finalize();
		$this->processAction($contInstance, '_finalize');
		//echo '</div>';
		$this->buffer .= ob_get_clean();
		
		if(empty($this->tvout)){
			$doc = \G2\L\Document::getInstance($this->site);
			$doc->_build($this->buffer);
		}
		//Event::trigger('on_after_dispatch');
	}
	
	private function processAction(&$contInstance, $action = ''){
		if(empty($action)){
			$action = $this->action;
		}
		
		$returned = $contInstance->{$action}();
		
		$renderView = $this->processActionResult($contInstance, $returned);
		
		return $renderView;
	}
	
	private function processActionResult(&$contInstance, $returned){
		$renderView = true;
		
		if($returned === false){
			//no view will be loaded
			$renderView = false;
		}else{
			if(is_array($returned)){
				if(!empty($this->tvout)){
					echo json_encode($returned, true);
					$renderView = false;
				}else{
					if(!empty($returned['error'])){
						self::session()->flash('error', $returned['error']);
						if(empty($returned['continue'])){
							$renderView = false;
						}
						if(!empty($returned['reload'])){
							if(isset($contInstance->data[$this->action])){
								unset($contInstance->data[$this->action]);
							}
							$renderView = $this->processAction($contInstance);
						}
					}else if(!empty($returned['success'])){
						self::session()->flash('success', $returned['success']);
					}
					if(!empty($returned['redirect'])){
						$this->redirect($returned['redirect']);
					}
				}
			}
		}
		
		return $renderView;
	}

	function render(){
		Event::trigger('on_before_render');
		$template_model = new \G2\A\M\Template();
		$params = null;
		if(empty($this->template)){
			$template_data = $template_model->find('first', array(
				'conditions' => array('Template.site' => $this->site, 'Template.default' => 1),
				'recursive' => -1,
				'cache' => true
			));
		}else{
			$template_data = $template_model->find('first', array(
				'conditions' => array('Template.name' => $this->template),
				'recursive' => -1,
				'cache' => true
			));
		}
		if(!empty($template_data)){
			$this->template = $template_data['Template']['source'];
			$params = new Parameter($template_data['Template']['params']);
		}
		//get template view from the request
		$this->tvout = strlen(Request::data('tvout', null)) > 0 ? Request::data('tvout') : $this->tvout;
		//render the active template
		$doc = Document::getInstance($this->site, $this->thread);
		$template = Template::getInstance($doc, $this->template, $this->tvout, $params);
		$this->buffer = $template->render();
		Event::trigger('on_after_render');
	}

	function output(){
		echo $this->getBuffer();
		
		if(Config::get('error.debug', 0)){
			\G2\Loader::debug();
		}
	}
}