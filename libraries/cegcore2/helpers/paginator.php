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
class Paginator extends \G2\L\Helper{
	var $url;
	var $urlparams;
	
	function __construct(&$view = null, $config = []){
		parent::__construct($view, $config);
		$this->url = !empty($this->url) ? $this->url : \G2\L\Url::current();
		
		if(!empty($this->urlparams)){
			$params = [];
			foreach($this->urlparams as $p){
				$params[$p] = $this->data($p);
			}
			$this->url = \G2\L\Url::build($this->url, $params);
		}
	}
	
	public function info($alias = ''){
		if(empty($alias)){
			$alias = \GApp::instance()->get('composer.paginate');
		}
		
		$limit = \GApp::session()->get('composer.paginate.'.$alias.'.limit');
		$count = \GApp::session()->get('composer.paginate.'.$alias.'.count');
		$startat = \GApp::session()->get('composer.paginate.'.$alias.'.startat');
		
		$output = '
		<div class="ui labeled button menu pagination tiny" tabindex="0">
			<div class="ui button tiny">
				'.($startat + 1).' - '.($startat + $limit > $count ? $count : $startat + $limit).'
			</div>
			<div class="ui basic left pointing label">'.$count.'</div>
		</div>
		';
		
		return $output;
	}
	
	public function limiter($alias = ''){
		if(empty($alias)){
			$alias = \GApp::instance()->get('composer.paginate');
		}
		
		$limit = \GApp::session()->get('composer.paginate.'.$alias.'.limit');
		$count = \GApp::session()->get('composer.paginate.'.$alias.'.count');
		
		$output = '<div class="ui icon top left dropdown pointing">';
		$output .= '<i class="filter icon"></i>';
		$output .= '<span class="text">'.$limit.'</span>';
		$output .= '<div class="menu">';
		$values = array(5, 10, 15, 20, 30, 50, 100);
		
		$HtmlHelper = new \G2\H\Html();
		
		foreach($values as $value){
			$url = r2(\G2\L\Url::build($this->url, array('limit' => $value)));
			$output .= $HtmlHelper->attrs(['href' => $url, 'class' => 'item'])->content($value)->tag('a');
		}
		$output .= '</div>';
		$output .= '</div>';
		
		unset($HtmlHelper);
		
		return $output;
	}
	
	public function navigation($alias = ''){
		if(empty($alias)){
			$alias = \GApp::instance()->get('composer.paginate');
			if(empty($alias)){
				return '';
			}
		}
		
		$HtmlHelper = new \G2\H\Html();
		
		$limit = \GApp::session()->get('composer.paginate.'.$alias.'.limit', \G2\L\Config::get('limit.default', 30));
		$count = \GApp::session()->get('composer.paginate.'.$alias.'.count', 0);
		$startat = \GApp::session()->get('composer.paginate.'.$alias.'.startat', 0);
		
		$current_page = ($startat/$limit) + 1;
		$page_count = ceil($count/$limit);
		
		$output = '<div class="ui tiny menu compact secondary">';
		
		//shown
		$current_range = ($count > 0 ) ? (($startat + 1).' - '.($startat + $limit > $count ? $count : $startat + $limit)) : 0;
		$output .= $HtmlHelper->attrs(array('class' => 'item header'))->content($current_range.' / '.$count)->tag('div');
		
		//first
		if($startat > 0){
			$first_tag = 'a';
			$first_tag_class = '';
		}else{
			$first_tag = 'div';
			$first_tag_class = ' disabled';
		}
		$url = r2(\G2\L\Url::build($this->url, array('startat' => 0)));
		$output .= $HtmlHelper->attrs(array('href' => $url, 'class' => 'item icon'.$first_tag_class))->content('<i class="step backward icon"></i>')->tag($first_tag);
		
		//previous
		if(($startat - $limit) >= 0){
			$prev_tag = 'a';
			$prev_tag_class = '';
		}else{
			$prev_tag = 'div';
			$prev_tag_class = ' disabled';
		}
		$url = r2(\G2\L\Url::build($this->url, array('startat' => ($startat - $limit))));
		$output .= $HtmlHelper->attrs(array('href' => $url, 'class' => 'item icon'.$prev_tag_class))->content('<i class="chevron left icon"></i>')->tag($prev_tag);
		
		//prev pages
		if($current_page > 1){
			for($i = -2; $i < 0; $i++){
				if($current_page + $i > 0){
					$url = r2(\G2\L\Url::build($this->url, array('startat' => ($startat + $i * $limit))));
					$output .= $HtmlHelper->attrs(array('href' => $url, 'class' => 'item'))->content($current_page + $i)->tag('a');
				}
			}
		}
		
		//current
		if($count > 0 AND $startat < $count AND $startat >= 0){
			$output .= $HtmlHelper->attrs(['class' => 'item active'])->content($current_page)->tag('div');
		}
		
		//next pages
		if($current_page < $page_count){
			for($i = 1; $i < 3; $i++){
				if($current_page + $i <= $page_count){
					$url = r2(\G2\L\Url::build($this->url, array('startat' => ($startat + $i * $limit))));
					$output .= $HtmlHelper->attrs(['href' => $url, 'class' => 'item'])->content($current_page + $i)->tag('a');
				}
			}
		}
		
		//next
		if(($startat + $limit) < $count){
			$next_tag = 'a';
			$next_tag_class = '';
		}else{
			$next_tag = 'div';
			$next_tag_class = ' disabled';
		}
		$url = r2(\G2\L\Url::build($this->url, array('startat' => ($startat + $limit))));
		$output .= $HtmlHelper->attrs(['href' => $url, 'class' => 'item icon'.$next_tag_class])->content('<i class="chevron right icon"></i>')->tag($next_tag);
		
		//last
		if(($startat + $limit) < $count){
			$last_tag = 'a';
			$last_tag_class = '';
		}else{
			$last_tag = 'div';
			$last_tag_class = ' disabled';
		}
		$url = r2(\G2\L\Url::build($this->url, array('startat' => floor($count/$limit) * $limit)));
		$output .= $HtmlHelper->attrs(['href' => $url, 'class' => 'item icon'.$last_tag_class])->content('<i class="step forward right icon"></i>')->tag($last_tag);
		
		//total
		$output .= $HtmlHelper->attrs(['class' => 'item ui label icon'])->content(ceil($count/$limit))->tag('div');
		
		$output .= '</div>';
		
		unset($HtmlHelper);
		
		return $output;
	}
}