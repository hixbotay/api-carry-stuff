<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('_JEXEC') or die;

class BookproControllerDemo extends JControllerLegacy{
	
public function __construct($config){
		parent::__construct($config);
// 		$this->online_page = asrray('http://ready.joombooking.com/alloandgo/','http://allo-and-go.com/','http://ready.joombooking.com/alloandgo/dev2/');
		$this->checkPermission();
		$this->online_account = array(
				'username'=>'admin',
				'password'=>'123@123a'
		);
	}
	
	private function checkPermission(){
		$user = JFactory::getUser();
		if(!in_array(8, $user->groups) && substr(JUri::root(), 0,16) != 'http://localhost'){
			$username = $this->input->getString('username');
			$password = $this->input->getString('password');
			if(JFactory::getApplication()->login(array('username'=>$username,'password'=>$password),array('silent'=>1))){
				$user = JFactory::getUser();
				if(in_array(8, $user->groups)){
					return true;
				}
			}
			die('invalid request');
		}
		return;
	}
	
	
 private  function write_log($log_file, $error){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$date = date('d/m/Y H:i:s');
		$error = $date.": ".$error."\n";
		
		$log_file = JPATH_ROOT."/logs/".$log_file;
		if(filesize($log_file) > 1048576 || !file_exists($log_file)){
			$fh = fopen($log_file, 'w');
		}
		else{
			//echo "Append log to log file ".$log_file;
			$fh = fopen($log_file, 'a');
		}
		
		fwrite($fh, $error);
		fclose($fh);
	}
	
	private function dump($value){
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}
	
	//show all function
	public function show(){
		$methods = get_class_methods('BookproControllerDemo');
		echo '<b>Method list</b><br>';
		echo '<table cellpadding="5"><tr><td>';
		echo JUri::root().'<br>';
		foreach ($methods as $method){
			if(preg_match('/^debug(\w)*/', $method)){
				echo '<a href="'.JRoute::_('index.php?option=com_bookpro&task=demo.'.$method).'" class="btn btn-primary btn-wrapper">'.(substr($method,5)).'</a><br>';
			}
			
		}
		$links = array('pn_log'=>'index.php?option=com_bookpro&task=demo.showpnlog',
						'test_pn'=>'index.php?option=com_bookpro&task=callback.sendClosestOrderPN&driver_id=50&order_id=3',
						'link_testpayment'=>'index.php?option=com_bookpro&view=formpayment&order_id=90',
						'sql_session'=>'index.php?option=com_bookpro&task=demo.runsql&sqlcode='.base64_encode('select * from #__bookpro_session order by time desc'),
						'sql_order'=>'index.php?option=com_bookpro&task=demo.runsql&sqlcode='.base64_encode('select * from #__bookpro_orders'),
						'sql_customer'=>'index.php?option=com_bookpro&task=demo.runsql&sqlcode='.base64_encode('select * from #__bookpro_customer'));
		$sql_log_pn = 'select pn_log.*,pn.* from #__bookpro_customer_pn_log as pn_log left join #__bookpro_customer_pn as pn ON pn.id = pn_log.pn_id group by(pn_log.id);';
		$links['sql_log_pn'] =  'index.php?option=com_bookpro&task=demo.runsql&sqlcode='.base64_encode($sql_log_pn);
		$links['test_sms'] = 'index.php?option=com_bookpro&task=demo.testSms&phone=8416522982';
		//find in log
		$log_files = scandir(JPATH_ROOT.'/logs');		
		foreach ($log_files as $f){
			if($f != '.' && $f != '..' && $f !='index.html')
				$links[$f] = 'logs/'.$f;
		}
		//sql cache
		$run_sql_file = scandir(JPATH_ROOT.'/logs/sql');
		foreach ($run_sql_file as $f){
			if($f != '.' && $f != '..' && $f !='index.html' && $f!= 'sql')
				$links[$f] = 'index.php?option=com_bookpro&task=demo.runsql&sqlcode='.base64_encode(file_get_contents(JPATH_ROOT.'/logs/sql'.$f));
		}
		echo "Link<ul>";
		foreach($links as $key=>$li){
			echo '<li><a href="'.JUri::root().$li.'">'.$key.'</li>';
		}
		echo "</ul>";
		echo '</td>';
		
		foreach ($this->online_page as $online_page){
			echo '<td>';
			echo $online_page.'<br>';
			foreach ($methods as $method){
					
				if(preg_match('/^debug(\w)*/', $method)){
					echo '<a href="'.JRoute::_($online_page.'index.php?option=com_bookpro&controller=demo&task='.$method).'" class="btn btn-primary btn-wrapper">'.(substr($method,5)).'</a><br>';
				}
					
			}
			echo "Link<ul>";
			foreach($links as $key=>$li){
				echo '<li><a href="'.$online_page.$li.'">'.$key.'</li>';
			}
			echo "</ul>";
			echo '</td>';
		}
		
		
		echo "</tr></table>";
		
		exit;
	}
	public function debugSQL($value= null){
		echo '<form method="Post" action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runsql').'" name="debug">
				SQL query: <br><textarea name="sql" style="width:100%">'.$value.'</textarea><br>
				<input type="checkbox" name="log" value="1"/>Save log<br>
				<input type="checkbox" name="remote" value="1"/>Send request to Remote Host<br>
				<input type="submit"/>
				</form>';
		return;
	}
	
	public function debugAdd_sql_query_cache($value= null){
		echo '<form method="Post" action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runadd_sql_query_cache').'" name="debug">
				File name:<input type="text" required name="name" value=""/><br>
				SQL:<input type="text" required name="content" value=""/><br>
				<input type="submit"/>
				</form>';
		return;
	}
	public function runadd_sql_query_cache(){	
		$file_name = $this->input->getString('name');
		$sql = $this->input->getString('content');
		if(empty($file_name) || empty($sql)){
			$this->dump('empty field');
			return;
		}
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		try{
			$result = ($db->loadObjectList());		
//create folder if not exist			
			$path = JPATH_ROOT.'/logs/sql';
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			//search for duplicate sql file
			$run_sql_file = scandir(JPATH_ROOT.'/logs/sql');
			
			foreach ($run_sql_file as $f){
				if($f != '.' && $f != '..' && $f !='index.html'){
					if($f == $file_name.'.txt'){
						throw new Exception('File name is existed!');
					}
					$file_content = file_get_contents($path.DS.$f);						
					$file_content = trim($file_content);
					$file_content = trim($file_content,';');
					
					$sql = trim($sql);
					$sql = trim($sql,';');
					
					if($file_content == $sql){
						throw new Exception('The query is dupplicated with '.$f);
					}
				}
				
					
			}
			
			$file_name = $path.DS.$file_name.'.txt';
			$fh = fopen($file_name, 'w');		
			fwrite($fh, ($sql));
			fclose($fh);
			$this->dump('write Success');
			$this->render($result);
			$this->show();
		}catch(Exception $e){
			$this->dump('write failed');
			$this->dump($e->getMessage());
			
		}
		$this->debugAdd_sql_query_cache();
		
	}
	
	public function pingUrl($url=NULL,$timeout = 1)  
	{  
	    if($url == NULL) return false;  
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    $data = curl_exec($ch);  
//	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);  
	    return $data;
	}
	
	public function exeSql($sql){
		
	}
	
	public function runSql(){
		
		
		$input = JFactory::getApplication()->input;
		if(isset($_GET['sqlcode'])){
			$sql = base64_decode($_GET['sqlcode']);
		}else{
			$sql = $_POST['sql'];
		}
		$w_log = $input->getInt('log');
		
		//---------run sql-----------------//
		$db = JFactory::getDbo();
		$db->setQuery($sql);		
		try{
			$result = $db->execute();			
		}
		catch(Exception $e){
			$this->dump($e->getMessage()) ;
			$this->dump('sql error','error');
		}
		/*-end-*/
		//write log
		if($result  && $w_log){
			$this->write_log('jb_sql.txt', PHP_EOL.$sql);
		}
		
		if($input->getInt('die')){
			echo $result;
			die;
		}
		//send request to remote host if sql is executed
		$remote = $input->getInt('remote');
		if($remote){
			foreach($this->online_page as $online_page){				
				$url = $online_page.'index.php?option=com_bookpro&log=1&task=demo.runsql&die=1&remote=1&sqlcode='.base64_encode($sql).'&log='.$w_log.'&'.http_build_query($this->online_account);
				
				$remote_result = $this->pingUrl($url,0);
				$this->dump('Remote '.$online_page.': '.$remote_result);
			}
			
			
		}
		
		$this->dump('Result: '.(int)$result);
		if (preg_match("/(select|show|SELECT|SHOW)(\w)*/",$sql)){
		
				$this->render($db->loadObjectList()) ;
			}
		$this->debugSql($sql);
		$this->show();
		exit;
	}
	
	
	//run PHP script
	function debugScript(){
		
		echo '<form action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runScript').'" method="Post">
				<div class="control-group">
					<div class="control-label">
						Script file
					</div>
					<div class="controls">
						<textarea rows="4" cols="30" name="script" style="width:100%"></textarea>
					</div>
				</div>
				<input type="submit"/>
				</form>';
		return;
	}
	public function runScript(){
		
		$input = jFactory::getApplication()->input;
		$script = $_POST['script'];
		$this->dump($script,'Script');
		
		$result = eval($script);
		
		if($result === false){
			$this->dump('error');
		}
		else{
			$this->dump('success');
			AImporter::helper('android');
			AndroidHelper::write_log('jb_script.txt', PHP_EOL.$script);
			
		}
		echo '<form action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runScript').'" method="Post">
				<div class="control-group">
					<div class="control-label">
						Script file
					</div>
					<div class="controls">
						<textarea rows="4" cols="30" name="script" style="width:100%">'.$script.'</textarea>
					</div>
				</div>
				<input type="submit"/>
				</form>';
		$this->show();
	}
	
	
	public function debugString(){
		echo '<form method="Post" action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runString').'" name="debug">
				String: <input type="text" name="sql" style="width:100%"/><br>
				Type 
				<select name="type">
					<option value="JSON_DECODE">JSON_DECODE</option>
					<option value="HTML_ENDCODE">HTML_ENDCODE</option>
				</select>
				<input type="submit"/>
				</form>';
		return;
	}
	
	
	public function runString(){
		$input = JFactory::getApplication()->input;
		
		$sql = $input->getString('sql');
		$type = $input->getString('type');
		switch ($type){
			case 'JSON_DECODE':
				debug(json_decode($sql));
				break;
			default:
				debug(htmlentities($sql));
				break;
		}
					
		$this->show();
	}
	
	public function debugBackup(){
		
		echo '<form method="Post" action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runBackup').'" name="debug">
				Table name: <input type="text" name="sql" style="width:100%"/><br>				
				<input type="submit"/>
				</form>';
		return;
	}
	
	public function runBackup(){
		
		$input = JFactory::getApplication()->input;
		$name = $input->getString('sql');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName($name));
			
		echo 'Table'.$name.'<br>';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		echo  json_encode($result);
	}
	
	public function debugRestore(){
		
		echo '<form method="Post" action="'.JRoute::_('index.php?option=com_bookpro&task=demo.runRestore').'" name="debug">
				Table name: <input type="text" name="name" style="width:100%"/><br>
				Data: <input type="text" name="sql" style="width:100%"/><br>
				Delete Old data: <input type="checkbox" name="delete" value="1"/><br>
				<input type="submit"/>
				</form>';
		return;
	}
	
	public function runRestore(){
		
		$input = JFactory::getApplication()->input;
		$data = $input->getString('sql');
		$name = $input->getString('name');
		$delete = $input->getInt('delete');
		$db = JFactory::getDbo();
		if($delete){
			$db->setQuery('delete * from '.$name.' where 1;');
			try{
				$result = $db->execute();			
			}
			catch(RuntimeException $e){
				die('delete table error');
			}
			echo 'Result: ';
			var_dump($result);
		}
		$sql = 'INSERT INTO '.$name.' values ';
		$datas = json_decode($data);
		$sql_data = array();
		foreach ($datas as $data){
			$row = '';
			foreach ($data as $d){
				$row .= $d.',';
			}
			$sql_data[] = '('.$row.')';
		}
		$sql .= implode(',', $sql_data).';';
		$this->dump($sql);
		
		$db->setQuery($query);
		try{
			$result = $db->execute();
		}
		catch(RuntimeException $e){
			$this->dump('sql error','error');
		}
		$this->dump($result);
		$this->show();
	}
	public function getSessionById($id){
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)->select('data')->from('#__session')->where('session_id LIKE' .$db->quote($id));
		$db->setQuery($query);
		$result =  $db->loadResult();
		return $result;
	}
	public function getSessionByUser($id){
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)->select('data')->from('#__session')->where('userid='.$id);
		$db->setQuery($query);
		$result =  $db->loadResult();
		return $result;
	}
 private function unserialize_php($session_data) {
 	
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
            	debug($offset);
                echo ("invalid data, remaining: " . substr($session_data, $offset));die;
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            debug($varname);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
        
    }
	public function debugResetDriver(){
		AImporter::table('session');
		AImporter::helper('bookpro');
		AImporter::classes('session');
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)->select('s.session_id')->from('#__bookpro_session as s')->innerJoin('#__bookpro_customer as c ON c.id = s.userid')->where('c.user_type = 3');
		$db->setQuery($query);
		$result =  $db->loadColumn();
		
		$l = 0.01;
		foreach ($result as $r){
			$session = new BookproSession();
			$session->session_id = $r;
			$session->loadSessionById();
			$session->setCurrentVehicle();
			$session->free = 1;
			$session->lat = 12.031223 + $l;
			$session->lng = 105.013223 + $l;
			unset($session->data->candidate);
			$l += 0.01;
			debug($session->saveSession());
		}
		
		$this->show();
		
	}
	
	function debugAddTestSession(){
		AImporter::classes('session');
		AImporter::table('customer');
		$db = JFactory::getDbo();
		$customer = new TableCustomer($db);
		$customer->load(array('email'=>"driver1@joombooking.com"));
		if(!$customer->id){
			$customer->load(array('email'=>"drivertest@joombooking.com"));
		}
		$session = new BookproSession();
		$session->session_id = 'l1ff08hFzXwkYL0yYp5oMgENyOk';
		if(!$session->loadSessionById()){
			$session->userid = $customer->id;
		}
		$session->setCurrentVehicle(1);
		$session->free = 1;
		$session->lat = 12.031223;
		$session->lng = 105.013223;
		unset($session->data->candidate);
		
		$check = $session->saveSession();
		$this->show();
	}
function encryptIt( $q ) {
    $cryptKey  = 'Koph4iem132';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}

	function decryptIt( $q ) {
	    $cryptKey  = 'Koph4iem132';
	    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
	    return( $qDecoded );
	}
	
	function showpnlog(){
		$file_name = JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/log/pn_backend.log';
		$content = file_get_contents($file_name);
		echo '<a href="index.php?option=com_bookpro&task=demo.clearlog&file='.base64_encode($file_name).'">Clear log</a>';
		dump($content);
		die;
	}
	
	function clearlog(){
		$file_name = $this->input->getString('file');
		$filename = base64_decode($file_name);
		$fh = fopen( $filename, 'w' );
		fclose($fh);
		$this->show();
	}
	
	function testSms(){
		$dispatcher    = JDispatcher::getInstance();
		$import 	= JPluginHelper::importPlugin('bookpro','product_sms' );
		$sms_content = array(
			'sms'=>'Test sms',
			'phone'=> $this->input->getString('phone')
		);
		$send = $dispatcher->trigger( "onBookproSendSms", array($sms_content));
		debug($send);die;
	}
	
	public function debugPN_mode(){
		$file = JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/common/config.php';
		require $file;
		echo '<pre>Mode: <b>'.RUNNING_MODE.'</b><br>';
		echo '<a class="btn btn-primary" href="index.php?option=com_bookpro&controller=demo&task=runpnmode">Switch mode</a><br></pre>';
		
		$this->show();
	}
	public function runpnmode(){
		$file = JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/common/config.php';
		require $file;
		$data = file_get_contents($file);
		if(RUNNING_MODE == 'production'){
			$data = str_replace("define('RUNNING_MODE', 'production')","//define('RUNNING_MODE', 'production')",$data);			
			$data = str_replace("//define('RUNNING_MODE', 'development')","define('RUNNING_MODE', 'development')",$data);			
		}else{
			$data = str_replace("//define('RUNNING_MODE', 'production')","define('RUNNING_MODE', 'production')",$data);			
			$data = str_replace("define('RUNNING_MODE', 'development')","//define('RUNNING_MODE', 'development')",$data);	
		}
		$fh = fopen($file, 'w');
		fwrite($fh, $data);
		fclose($fh);
		$this->setRedirect('index.php?option=com_bookpro&controller=demo&task=debugPN_mode','swith mode complete!');
		return;
	}
	
	public function debugErrorCode(){
		$dispatcher    = JDispatcher::getInstance();
		$plugins = array('order','user','vehicle');
		foreach($plugins as $name){
			require_once JPATH_ROOT.'/plugins/jbackend/'.$name.'/'.$name.'.php';
		}
		JFactory::getLanguage()->load('com_bookpro_msg_group');		
		echo '<table>';
		for($i=1;$i<98;$i++){
			foreach($plugins as $name){
				$class = 'plgJBackend'.ucfirst($name);
				$result = $class::generateError($i);
				if($result['message']['en'] != "Error excepion" && $result['message']['en'] != "Invalid action"){
					echo '<tr><td>'.$result['code'].'</td><td>'.$result['message']['en'].'</td></tr>'; 
				}
			}
			 
			
		}
		echo '<tr><td>98</td><td>Acction denied</td></tr>';
		echo '<tr><td>99</td><td>Acction is process already</td></tr>';
		echo '<tr><td>100</td><td>No permission</td></tr>';
		echo '</table>';
		echo '<a href="index.php?option=com_bookpro&task=demo.show">Back</a>';
		die;
	}
	
	public function debugPaypalLog(){
		$data = file_get_contents(JPATH_ROOT.'/PayPal.log');
		dump( $data);die;
	}
	
	
	private function render($items){
		if(empty($items)){
			echo 'no record found!';
			return;
		}
		$key = array_keys((array)$items[0]);
		echo '<table class="table" border="1">';
		echo '<tr><th>'.implode('</th><th>',$key).'</th></tr>';
		foreach($items as $item){
			echo '<tr>';
			foreach($item as $k=>$data){
				if($k=='time'){
					echo "<td>".JFactory::getDate($data)->modify('+ 7 hours')->format('Y-m-d H:i:s')."</td>";
				}else{
					echo "<td>$data</td>";
				}
				
			}
			echo '</tr>';
		}
		echo '</table>';
		
		return;
	}
	
	function debugPayment(){
		$dispatcher    = JDispatcher::getInstance();
		$import 	= JPluginHelper::importPlugin('bookpro','payment_braintree' );
		$request->order = new Jobject();
	
		$request->customer = new Jobject();
			
		$request->card = new Jobject();
			
		$request->total = 10;
		$data = $dispatcher->trigger( "restpayment", array($request ));
		
		debug($data);die;
	}
}
