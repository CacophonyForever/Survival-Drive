<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace G2\H;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Header extends \G2\L\Helper{
	
	public function render(){
		$text = \GApp::instance()->get('composer.header.text');
		$link = \GApp::instance()->get('composer.header.link');
		$string = \GApp::instance()->get('composer.header.string');
		$icon = \GApp::instance()->get('composer.header.icon');
		
		$header = '';
		
		$text = (!empty($icon) ? '<i class="icon '.$icon.'"></i>' : '').$text;
		
		if(!empty($link)){
			$text = '<a href="'.$link.'">'.$text.'</a>';
		}
		
		$header = '<h1 class="ui header medium">'.sprintf($string, $text).'</h1>';
		
		return $header;
	}
}