<?php
/**
* COMPONENT FILE HEADER
**/
namespace G2\A\E\Chronofc\C;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
trait Basics {
	
	function _settings($ext_name){
		$this->data = $this->Extension->where('name', $ext_name)->select('first', ['json' => ['settings']]);
		
		//permissions groups
		$groups = $this->Group->fields(['id', 'title'])->select('list');
		$this->set('groups', $groups);
	}
	
	function _save_settings($ext_name){
		$ext = $this->Extension->where('name', $ext_name)->select('first', ['json' => ['settings']]);
		if(empty($ext['Extension'])){
			$ext['Extension'] = [];
		}
		$this->data['Extension']['name'] = $ext_name;
		$this->data['Extension']['enabled'] = 1;
		$this->data['Extension'] = array_replace_recursive($ext['Extension'], $this->data['Extension']);
		
		$result = $this->Extension->save($this->data['Extension'], ['json' => ['settings']]);
		
		if($result !== false){
			\GApp::session()->flash('success', rl('Settings saved successfully.'));
		}else{
			\GApp::session()->flash('error', rl('Error updating settings.'));
		}
		
		$this->redirect(r2('index.php?ext='.$ext_name.'&act=settings'));
	}
	
	function _permissions($ext_name, $perms){
		$this->set('perms', $perms);
		//permissions groups
		$groups = array_merge([['Group' => ['id' => 'owner', 'title' => rl('Owner'), '_depth' => 0]]], $this->Group->select('flat'));
		$this->set('groups', $groups);
		
		$acl = $this->Acl->where('aco', 'ext='.$ext_name)->select('first', ['json' => ['rules']]);
		if(!empty($acl)){
			$this->data = $acl;
		}
	}
	
	function _save_permissions($ext_name){
		if(empty($this->data['Acl'])){
			$this->redirect(r2('index.php?ext='.$ext_name.'&act=permissions'));
		}
		$this->data['Acl']['title'] = $ext_name;
		$this->data['Acl']['aco'] = 'ext='.$ext_name;
		$this->data['Acl']['enabled'] = 1;
		$result = $this->Acl->save($this->data['Acl'], ['json' => ['rules']]);
		
		if($result !== false){
			\GApp::session()->flash('success', rl('Permissions updated successfully.'));
		}else{
			\GApp::session()->flash('error', rl('Error updating permissions.'));
		}
		
		$this->redirect(r2('index.php?ext='.$ext_name.'&act=permissions'));
	}
	
	function _clear_cache($ext_name){
		$path = \G2\Globals::get('FRONT_PATH').'cache'.DS;
		$files = \G2\L\Folder::getFiles($path);
		$count = 0;
		foreach($files as $k => $file){
			if(basename($file) != 'index.html'){
				$result = \G2\L\File::delete($file);
				if($result){
					$count++;
				}
			}
		}
		if(function_exists('apc_delete')){
			apc_clear_cache('user');
		}
		$session = \GApp::session();
		$session->flash('info', $count.' '.rl('Cache files deleted successfully.'));
		$this->redirect(r2('index.php?ext='.$ext_name));
	}
	
	function _install_feature($ext_name){
		$session = \GApp::session();
		
		if(isset($_FILES['upload'])){
			$upload = $_FILES['upload'];
			if(\G2\L\Upload::valid($upload) AND \G2\L\Upload::not_empty($upload) AND \G2\L\Upload::check_type($upload, 'zip')){
				
				$pcs = explode('.', $upload['name']);
				$type = array_shift($pcs).'s';
				
				$target = \G2\Globals::get('FRONT_PATH').'cache'.DS.rand().$upload['name'];
				$result = \G2\L\Upload::save($upload['tmp_name'], $target);
				if(empty($result)){
					$session->flash('error', rl('Upload error.'));
					$this->redirect(r2('index.php?ext='.$ext_name.'&act=install_feature'));
				}
				//file upload, let's extract it
				$zip = new \ZipArchive();
				$handler = $zip->open($target);
				if($handler === true){
					$extract_path = \G2\Globals::ext_path('chronofc', 'admin').$type.DS;
					$zip->extractTo($extract_path);
					$zip->close();
					unlink($target);
					
					$session->flash('success', rl('New feature was installed successfully.'));
					$this->redirect(r2('index.php?ext='.$ext_name));
				}else{
					$session->flash('error', rl('Error extracting file.'));
					$this->redirect(r2('index.php?ext='.$ext_name.'&act=install_feature'));
				}
			}else{
				$session->flash('error', rl('File missing or incorrect.'));
				$this->redirect(r2('index.php?ext='.$ext_name.'&act=install_feature'));
			}
		}
	}
	
	function _vupdate($ext_name, $v, $update_fld = 'validated'){
		$ext = $this->Extension->where('name', $ext_name)->select('first', ['json' => ['settings']]);
		if(empty($ext)){
			$ext = [];
			$ext['Extension']['name'] = $ext_name;
			$ext['Extension']['enabled'] = 1;
		}
		$ext['Extension']['settings'][$update_fld] = $v;
		$result = $this->Extension->save($ext['Extension'], ['json' => ['settings']]);
		return $result;
	}
	
	function _validateinstall($ext_name, $prod){
		$domain = str_replace(array('http://', 'https://'), '', \G2\L\Url::domain());
		$this->set('domain', $domain);
		if(!empty($this->data['license_key'])){
			
			$fields = '';
			//$postfields = array();
			unset($this->data['option']);
			unset($this->data['act']);
			foreach($this->data as $key => $value){
				$fields .= "$key=".urlencode($value)."&";
			}
			
			$update_fld = 'validated';
			if(strpos($this->data['license_key'], '@') !== false){
				$update_fld = explode('@', $this->data['license_key'])[0];
			}
			
			$target_url = 'http://www.chronoengine.com/index.php?option=com_chronocontact&task=extra&chronoformname=validateLicense&ver=6&api=3';
			
			if(ini_get('allow_url_fopen')){
				$output = file_get_contents($target_url.'&'.rtrim($fields, "& "));
			}else if(function_exists('curl_init')){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $target_url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($fields, "& "));
				$output = curl_exec($ch);
				curl_close($ch);
			}
			$validstatus = $output;
			
			if(strpos($validstatus, 'valid') === 0){
				$valresults = explode(':', $validstatus, 2);
				$valprods = json_decode($valresults[1], true);
				$result = false;
				
				foreach($valprods as $valprod){
					if(!empty($valprod['ext']) AND $valprod['ext'] == $prod){
						$result = $this->_vupdate($ext_name, 1, 'validated');
					}else if(!empty($valprod['plg'])){
						$result = $this->_vupdate($ext_name, 1, 'validated_'.$valprod['plg']);
					}
				}
				
				if($result){
					\GApp::session()->flash('success', 'Validated successfully.');
					$this->redirect(r2('index.php?ext='.$ext_name));
				}else{
					\GApp::session()->flash('error', 'Validation error.');
				}
			}else if($validstatus == 'invalid'){
				\GApp::session()->flash('error', 'Validation error, you have provided incorrect data.');
			}else{
				if(!empty($this->data['serial_number'])){
					$blocks = explode("-", trim($this->data['serial_number']));
					$hash = md5($this->data('license_key').str_replace('www.', '', $domain).$blocks[3]);
					if(substr($hash, 0, 7) == $blocks[4]){
						$result = $this->_vupdate($ext_name, 1, $update_fld);
						
						if($result){
							\GApp::session()->flash('success', 'Validated successfully.');
							$this->redirect(r2('index.php?ext='.$ext_name));
						}else{
							\GApp::session()->flash('error', 'Validation error.');
						}
					}else{
						\GApp::session()->flash('error', 'Serial number invalid!');
					}
				}
				\GApp::session()->flash('error', 'Validation error, your server does NOT have the CURL function enabled, please ask your host admin to enable the CURL, or please try again using the Serial number, or please contact us on www.chronoengine.com');
				$this->redirect(r2('index.php?ext='.$ext_name));
			}
		}
		
		$this->set('ext_name', $ext_name);
		$this->view = \G2\Globals::ext_path('chronofc', 'admin').DS.'themes'.DS.'default'.DS.'views'.DS.'validateinstall.php';
	}
}
?>