<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php  23-06-2012 23:33:14
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library


class JbdebugViewTasks extends JViewLegacy
{
	public $value;
	// Overwriting JView display method
	function display($tpl = null)
	{
		parent::display($tpl);
		require_once JPATH_ROOT.'/components/com_jbdebug/controllers/demo.php';
		$controller = new JbdebugControllerDemo();
		$this->online_page = $controller->online_page;
		echo '<b>Method list</b><br>';
		echo '<table><tr><td>';
		echo JUri::root().'<br>';
		$methods = array();
		
		$tasks = array_diff(scandir(__DIR__.'/tmpl/'), array('.', '..','default.php','index.html','default.xml'));
		$favorite_tasks = array('sql.php','script.php');
		$tasks = array_diff($tasks,$favorite_tasks);
		
		$tasks = $favorite_tasks + $tasks;
		foreach($tasks as $task){
			$methods[] = array('url'=>'index.php?option=com_jbdebug&view=tasks&layout='.substr($task,0,-4),'name'=>$task);
		}
		foreach ($methods as $method){
			echo '<a href="'.JUri::root().$method['url'].'" class="btn btn-primary btn-wrapper">'.$method['name'].'</a><br>';
			
		}
		
		$links = array();
		if (!file_exists(JPATH_ROOT.'/logs')) {
			mkdir(JPATH_ROOT.'/logs', 0777, true);
		}
		if (!file_exists(JPATH_ROOT.'/logs/script')) {
			mkdir(JPATH_ROOT.'/logs/script', 0777, true);
		}
		if (!file_exists(JPATH_ROOT.'/logs/sql')) {
			mkdir(JPATH_ROOT.'/logs/sql', 0777, true);
		}
		
		//log file
		$log_files = array_diff(scandir(JPATH_ROOT.'/logs'), array('.', '..','index.html','sql','script'));		
		foreach ($log_files as $f){
			$links[$f] = 'logs/'.$f;				
		}		
		//sql cache		
		$run_sql_file = array_diff(scandir(JPATH_ROOT.'/logs/sql'), array('.', '..','index.html'));
		foreach ($run_sql_file as $f){
			$key = 'sql_'.$f;
			$links[$key] = 'index.php?option=com_jbdebug&task=demo.runsql&sqlcode='.base64_encode(file_get_contents(JPATH_ROOT.'/logs/sql/'.$f));
		}
		//script cache
		$run_script_file = array_diff(scandir(JPATH_ROOT.'/logs/script'), array('.', '..','index.html'));
		foreach ($run_script_file as $f){
			$key = 'script_'.$f;
			$links[$key] = 'index.php?option=com_jbdebug&view=tasks&layout=script&code='.base64_encode(base64_encode(file_get_contents(JPATH_ROOT.'/logs/script/'.$f)));
		}
		
		echo "Link<ul>";
		foreach($links as $key=>$li){
			echo '<li><a href="'.JUri::root().$li.'">'.$key.'</li>';
		}
		echo "</ul>";
		
		
		foreach ($this->online_page as $online_page){			
			echo '</td><td>';
			echo $online_page['url'].'<br>';
			foreach ($methods as $method){
				echo '<a href="'.$online_page['url'].$method['url'].'&username='.$online_page['username'].'&password='.$online_page['password'].'" class="btn btn-primary btn-wrapper">'.$method['name'].'</a><br>';
			}
			echo "Link<ul>";
			foreach($links as $key=>$li){
				echo '<li><a href="'.$online_page['url'].$li.'">'.$key.'</li>';
			}
		}
		
		echo "</ul>";
		echo "</td></tr></table>";
		echo '<a href="'.JUri::root().'">Back to trang chu</a><br>';
		echo '<a href="http://localhost/'.$controller->init_test['localhost'].'/index.php?option=com_jbdebug&view=tasks">Back to localhost</a>';
		exit;
		
	}
	
	public function dump($value){
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}
	
	function getFiles($path,$include_path = false){
		$files = scandir($path);
		$result = array();
		foreach ($files as $file){
			if($file != '.'&& $file != '..'){
				if($include_path){
					$result[] = $path.$file;
				}else{
					$result[] = $file;
				}
				 
			}
		}
		asort($result);
		return $result;
	}
		
}
