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
class Composer {
	var $Controller;
	
	function __construct(&$controller = null){
		$this->Controller = $controller;
	}
	
	private function addHelper($helper){
		if(!empty($this->Controller) AND !in_array($helper, $this->Controller->helpers)){
			$this->Controller->helpers[] = $helper;
		}
	}
	
	public function sorter($fields = array(), $Model = null){
		$this->addHelper('\G2\H\Sorter');
		$return = array();
		
		//check if an order field is set in the url
		foreach($fields as $alias => $name){
			if(\G2\L\Request::data('orderfld') == $alias){
				$direction = \G2\L\Request::data('orderdir', 'asc');
				
				if($direction == 'clear'){
					\GApp::session()->clear('composer.order.'.$alias);
				}else{
					$return[$name] = $direction;
					\GApp::session()->set('composer.order.'.$alias, array('fld' => $name, 'dir' => $return[$name]));
				}
			}
		}
		//if no order is set in url then try to find one in session
		//if(empty($return)){
			$saved = \GApp::session()->get('composer.order', array());
			if(count($saved)){
				foreach($fields as $alias => $name){
					if(isset($saved[$alias])){
						$return[$saved[$alias]['fld']] = $saved[$alias]['dir'];
					}
				}
			}
		//}
		if(is_object($Model)){
			$Model->order($return);
		}
		
		return $return;
	}
	
	public function paginate($alias = 'default', $count = 0, $limit = null){
		\GApp::instance()->set('composer.paginate', $alias);
		
		$Model = null;
		if(is_object($count)){
			$Model = $count;
			$ModelParams = $Model->getParams();
			$count = $Model->select('count');
			$Model->setParams($ModelParams);
		}
		
		$this->addHelper('\G2\H\Paginator');
		
		$init_limit = !empty($limit) ? $limit : 0;
		if(is_numeric(\G2\L\Request::data('limit')) AND (int)\G2\L\Request::data('limit') > 0 AND (int)\G2\L\Request::data('limit') <= \GApp::config()->get('limit.max', 100)){
			$limit = \G2\L\Request::data('limit');
		}else{
			$limit = $init_limit ? $init_limit : \GApp::session()->get('composer.paginate.'.$alias.'.limit', \GApp::config()->get('limit.default', 30));
		}
		\GApp::session()->set('composer.paginate.'.$alias.'.limit', $limit);
		
		if(is_numeric(\G2\L\Request::data('startat')) AND (int)\G2\L\Request::data('startat') >= 0 AND (int)\G2\L\Request::data('startat') < $count){
			$startat = \G2\L\Request::data('startat');
			\GApp::session()->set('composer.paginate.'.$alias.'.startat', $startat);
		}else{
			$startat = \GApp::session()->get('composer.paginate.'.$alias.'.startat', 0);
		}
		\GApp::session()->set('composer.paginate.'.$alias.'.count', $count);
		
		if(is_object($Model)){
			$Model->limit($limit);
			$Model->offset($startat);
		}
		
		return [$limit, $startat];
	}
	
	public function pageHeader($text, $link = '', $string = '%s', $icon = ''){
		\GApp::instance()->set('composer.header.text', htmlspecialchars($text));
		\GApp::instance()->set('composer.header.link', $link);
		\GApp::instance()->set('composer.header.string', !empty($string) ? $string : '%s');
		\GApp::instance()->set('composer.header.icon', $icon);
		
		$this->addHelper('\G2\H\Header');
	}
	
	public function breadcrumb($text, $link = '', $string = '%s', $icon = ''){
		static $breadcrumbs;
		if(!isset($breadcrumbs)){
			$breadcrumbs = [];
		}
		$breadcrumbs[$text] = ['link' => $link, 'string' => !empty($string) ? $string : '%s', 'icon' => $icon];
		
		\GApp::instance()->set('composer.breadcrumbs', $breadcrumbs);
		
		$this->addHelper('\G2\H\Breadcrumbs');
	}
}