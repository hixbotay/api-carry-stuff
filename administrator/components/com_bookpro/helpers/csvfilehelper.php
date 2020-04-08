<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 27 2012-07-08 17:15:11Z quannv $
 **/
/**
 *Action with CSV file
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.filesystem.file' );
AImporter::helper('date');
class CsvFileHelper {
	
	//Upload file and save to Host/tmp folder
	static function upload() {
		// Get the uploaded file information
		$userfile = JRequest::getVar ( 'fileUpload', null, 'files', 'array' );
		//debug($userfile); die;
		//check file is csv file
		$filePath = explode('.',$userfile["name"]);
		
		$length = count($filePath);
		if (($filePath[$length-1] != 'docx')&& $length > 0){
			JError::raiseWarning('', JText::_('abc'));
			return false;
		}
		
		// Make sure that file uploads are enabled in php
		if (! ( bool ) ini_get ( 'file_uploads' )) {
			JError::raiseWarning ( '', JText::_ ( 'UPLOAD_FILE_IS_DISABLE_IN_PHP' ) );
			return false;
		}
		
		// If there is no uploaded file, we have a problem...
		if (! is_array ( $userfile )) {
			JError::raiseWarning ( '', JText::_ ( 'NO_FILE_IS_UPLOAD' ) );
			return false;
		}
		
		// Check if there was a problem uploading the file.
		if ($userfile ['error'] || $userfile ['size'] < 1) {
			JError::raiseWarning ( '', JText::_ ( 'COM_JBTRACKING_MSG_INSTALL_WARN_INSTALLUPLOADERROR' ) );
			return false;
		}
		
		// Build the appropriate paths
		$config = JFactory::getConfig ();		
		$tmp_dest = $config->get ( 'tmp_path' ) . '/' . $userfile ['name'];		
		$tmp_src = $userfile ['tmp_name'];
		debug($tmp_dest);die;
		// Move uploaded file		
		JFile::upload ( $tmp_src, $tmp_dest );
		
		//Read file
		$fileData = self::readFile($tmp_dest);
		
		return $fileData;
		
		
	}
	
	/**
	 * Function to get data from file
	 * Return data of file
	 */
	protected function readFile($filename) {		
		$file = fopen($filename,"r") or die("Can't open file");
		
		//get data
		$i=0;		
		$data = array();
		while(! feof($file))
		{			
		  $data[$i] = fgetcsv($file);
		  
		  	$data[$i] = self::checkFormatData($data[$i],$i);
		 
		  
		  if($data[$i] == false)		 
		  	return false;
		 
		  		
		  //remove empty row	 	 	
		  if($data[$i] == 'empty')
		  	unset($data[$i]);
		  $i++;
		}	
	
		fclose($file);
		
		//end get data
		
		//Check file is empty
		if ($i<1){
			JError::raiseWarning('', JText::_('FILE_HAVE_NO_DATA'));
			return false;
		}
		
		
		return $data;
		
	}
	
	private function checkFormatData($data,$row_of_data = 0){
		
		if(empty($data))
			return 'empty';
	
		$row_of_data = $row_of_data + 1; 		
		//count number of status
		$stt_number = count($data);
		
		/*remove null row */			
		if ($data[$j][0] == null){
			if($data[1] == null && $data[2] == null && $data[3] == null){
				return 'empty';						
			}
		}
		//check empty status
		if($data[4] == null){
			return 'empty';	
		}
		//check format of file	
		//raise error if first colume not have code		
		if($data[0]== null){
			JError::raiseWarning('', JText::sprintf('COM_JBTRACKING_ROW_N_EMPTY_CODE',$row_of_data));			
			return false;
		}		
		//raise error if colume 3rd is not state
		if(!self::check($data[2],array(0,1))){
			JError::raiseWarning('', JText::sprintf('COM_JBTRACKING_ROW_N_STATE_IS_WRONG',$row_of_data));			
			return false;
		}
		
		$result = array();
		$result[code] = $data[0];
		$result[title] = $data[1];
		$result[state] = $data[2];
		$result[notes] = $data[3];
		
		/*
		 * get status data
		 */
		//get list status from database
		$liststt = self::getListstt();
		$level = 1;//level of each status
		$stt_convert = array();
		for($i = 4; $i < $stt_number; $i++){
			//check empty data
			if($data[$i] != null){
				$stt = explode('/', $data[$i]);
				//status with format name,stt,date
				$status = new stdClass();				
				$status->name = $level;
				$status->stt = strtoupper($stt[1]);
				//validate date
				$date = self::checkDate($stt[0]);
				if(!empty($date)){	
					$status->date = $date;
				}
				else{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JBTRACKING_ROW_N_TIME_IS_WRONG',$row_of_data), 'error');
					return false;
				}
				if(!self::check($status->stt,$liststt)){
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JBTRACKING_ROW_N_STATUS_IS_WRONG',$row_of_data), 'error');
					return false;
				}
				$stt_convert[]=$status;
				$level++;
			}
		}
		$result[status] = json_encode($stt_convert);
		
		return $result;
	}
	/**
	 * Check format date
	 */
	private function checkDate($date){
		return DateHelper::createFromFormatYmd($date);
	}
	
	private function getListstt(){
		require_once JPATH_COMPONENT.'/models/jbtrackings.php';
		$status = new JbtrackingModelJbtrackings();
		$liststts = $status->getListstt();
		$countlist = 0;
		$liststt = array();
		foreach ($liststts as $item) {
			$liststt[$countlist] = $item->code;
			$countlist++;
		}
		return $liststt;
	}
	
	/*
	 * Function to compare a value with an array.
	 * It return true if value is equal with one of the array
	 */
	protected function check($data,$checkValue){
		$flag = false;
		$n = count($checkValue);
		for ($i = 0;$i < $n; $i++){
			if ($data == $checkValue[$i]){
				return true;
			}		
			
		}
		echo "<br> '".$data."' is not true";
		return $flag;
	}
	
	static function download($datas,$name) {	
		try{				
			// Open the output stream
			$fh = fopen('php://output', 'w');
			// Start output buffering (to capture stream contents)
			ob_start();
			foreach ($datas as $data) {			
				fputcsv($fh, $data);
			}
			$string = ob_get_clean();
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $name . '.csv";');
			header('Content-Transfer-Encoding: binary');
			// Stream the CSV data
			exit($string);
		}
		catch( RuntimeException $e ) {			
			JError::raiseError ( 500, $e->getMessage () );
			return false;
		}
		
	}
	
	/**
	 * export data to csv file
	 * @param Array $data data to write
	 * @param String $fileName name of file
	 */
	static function writeFile($data,$fileName){
		$r_number = count($data);
		$datas = array();
		for($i=0; $i<($r_number); $i++){			
			$line = array($data[$i]->code,$data[$i]->title,$data[$i]->state,$data[$i]->notes);
			//Convert status to format Date/Stt to save
			$status = json_decode($data[$i]->status);
			$stt_str = array();
			foreach($status as $stt){
				$line[] = JFactory::getDate($stt->date)->format(DateHelper::getConvertDateFormat('P')).'/'.$stt->stt; 
			}
			$datas[] = $line;
							
		}		
		self::download($datas, $fileName);
	}
	
}