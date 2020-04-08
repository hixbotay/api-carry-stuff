<?PHP
#################################################################################
## Developed by Manifest Interactive, LLC                                      ##
## http://www.manifestinteractive.com                                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
##                                                                             ##
## THIS SOFTWARE IS PROVIDED BY MANIFEST INTERACTIVE 'AS IS' AND ANY           ##
## EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE         ##
## IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR          ##
## PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL MANIFEST INTERACTIVE BE          ##
## LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR         ##
## CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF        ##
## SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR             ##
## BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,       ##
## WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE        ##
## OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,           ##
## EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
## Author of file: Peter Schmalfeldt                                           ##
#################################################################################

/**
 * @category Apple Push Notification Service using PHP & MySQL
 * @package EasyAPNs
 * @author Peter Schmalfeldt <manifestinteractive@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link http://code.google.com/p/easyapns/
 */

/**
 * Begin Document
 */

class APN {
	/**
	* Array of APNS Connection Settings
	*
	* @var array
	* @access private
	*/
	private $apns_data;

	
	/**
	* Message to push to user
	*
	* @var string
	* @access private
	*/
	private $message;

	/**
	* Streams connected to APNS server[s]
	*
	* @var array
	* @access private
	*/
	private $ssl_streams;

	/**
	 * Constructor.
	 *
	 * Initializes a database connection and perfoms any tasks that have been assigned.
	 *
	 * Create a new PHP file named apns.php on your website...
	 *
	 * <code>
	 * <?php
	 * $db = new DbConnect('localhost','dbuser','dbpass','dbname');
	 * $db->show_errors();
	 * $apns = new APNS($db);
	 * ?>
 	 * </code>
	 *
	 * Alternate for Different Certificates
	 *
	 * <code>
	 * <?php
	 * $db = new DbConnect('localhost','dbuser','dbpass','dbname');
	 * $db->show_errors();
	 * $apns = new APNS($db, NULL, '/usr/local/apns/alt_apns.pem', '/usr/local/apns/alt_apns-dev.pem');
	 * ?>
 	 * </code>
	 *
	 * Your iPhone App Delegate.m file will point to a PHP file with this APNS Object.  The url will end up looking something like:
	 * https://secure.yourwebsite.com/apns.php?task=register&appname=My%20App&appversion=1.0.1&deviceuid=e018c2e46efe185d6b1107aa942085a59bb865d9&devicetoken=43df9e97b09ef464a6cf7561f9f339cb1b6ba38d8dc946edd79f1596ac1b0f66&devicename=My%20Awesome%20iPhone&devicemodel=iPhone&deviceversion=3.1.2&pushbadge=enabled&pushalert=disabled&pushsound=enabled
	 *
	 * @param object|DbConnectAPNS $db Database Object
	 * @param array $args Optional arguments passed through $argv or $_GET
	 * @param string $certificate Path to the production certificate.
	 * @param string $sandboxCertificate Path to the production certificate.
	 * @param string $logPath Path to the log file.
	 * @access 	public
	 */
	function __construct(){
		$this->apns_data = array(
			'production'=>array(
				'certificate'=>IOS_PN_CERT_PRODUCTION,
				'ssl'=>IOS_PN_URL_PRODUCTION,
				'feedback'=>IOS_PN_URL_FEEDBACK_PRODUCTION
			),
			'development'=>array(
				'certificate'=>IOS_PN_CERT_DEVELOPMENT,
				'ssl'=>IOS_PN_URL_DEVELOPMENT,
				'feedback'=>IOS_PN_URL_FEEDBACK_DEVELOPMENT
			)
		);
		
		check_valid_cert(IOS_PN_CERT_DEVELOPMENT);
		check_valid_cert(IOS_PN_CERT_PRODUCTION);
		
	}
	
	/**
	 * Connect the SSL stream (sandbox or production)
	 *
	 * @param $mode string Development environment - 'development' or 'production'
	 * @return bool|resource status whether the socket connected or not.
	 * @access private
	 */
	private function connect_ssl_socket($mode) {
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apns_data[$mode]['certificate']);
		stream_context_set_option($ctx, 'ssl', 'passphrase', IOS_PN_CERT_PASSPHRASE);
		
		$ssl = $this->apns_data[$mode]['ssl'];
		write_log(LOG_FILE, "Connecting to SSL Socket of APNS ".$ssl." in mode ".$mode."....");
		
		$this->ssl_streams[$mode] = stream_socket_client($this->apns_data[$mode]['ssl'], $error, $error_string, 20, (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT), $ctx);
		
		if(!$this->ssl_streams[$mode]){
			write_log(LOG_FILE, "Failed to connect to APNS: {$error} {$error_string}.");
			unset($this->ssl_streams[$mode]);
			return false;
		}
		
		write_log(LOG_FILE, "Connected to APNS ok");
		
		return $this->ssl_streams[$mode];
	}
	/**
	 * Close the SSL stream (sandbox or production)
	 *
	 * @param $development string Development environment - development or production
	 * @return void
	 * @access private
	 */
	private function close_ssl_socket($mode) {
		if(isset($this->ssl_streams[$mode])) {
			fclose($this->ssl_streams[$mode]);
			unset($this->ssl_streams[$mode]);
		}
	}

	/**
	 * Push APNS Messages
	 *
	 * This gets called automatically by _fetchMessages.  This is what actually deliveres the message.
	 *
	 * @param int $pid
	 * @param string $message JSON encoded string
	 * @param string $token 64 character unique device token tied to device id
	 * @param string $development Which SSL to connect to, Sandbox or Production
	 * @access private
	 */
	public function push_message($device_token, $message, $badge = 1, $sound = true){
	
		$fp = false;
		$mode = RUNNING_MODE;
		if(!isset($this->ssl_streams[$mode]))
			$this->ssl_streams[$mode] = $this->connect_ssl_socket($mode);
				
		$fp = false;
		if(isset($this->ssl_streams[$mode])) {
			$fp = $this->ssl_streams[$mode];
		}

		if(!$fp){
			write_log(LOG_FILE, "A connected socket to APNS is not available");
			return false;
		}
		else {
			write_log(LOG_FILE, "A connected socket to APNS is available");
			// "For optimum performance, you should batch multiple notifications in a single transmission over the
			// interface, either explicitly or using a TCP/IP Nagle algorithm."

			// Simple notification format (Bytes: content.) :
			// 1: 0. 2: Token length. 32: Device Token. 2: Payload length. 34: Payload
			//$msg = chr(0).pack("n",32).pack('H*',$token).pack("n",strlen($message)).$message;

			// Enhanced notification format: ("recommended for most providers")
			// 1: 1. 4: Identifier. 4: Expiry. 2: Token length. 32: Device Token. 2: Payload length. 34: Payload
			// Create the payload body
			if($sound)
				$body['aps'] = array(
					'alert' => $message,
					'badge' => $badge,
					'sound' => 'default'
				);
			else
				$body['aps'] = array(
					'alert' => $message,
					'badge' => $badge
				);
			
			// Encode the payload as JSON
			$payload = $this->encode_json($body);
			write_log(LOG_FILE, "Message to send ".$payload);
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)).$payload;
			//write_log(LOG_FILE, "Message to send ".$msg);
			// Send it to the server
			$fwrite = fwrite($fp, $msg, strlen($msg));
		
			//$expiry = time()+120; // 2 minute validity hard coded!
			//$msg = chr(1).pack("N",$pid).pack("N",$expiry).pack("n",32).pack('H*',$token).pack("n",strlen($message)).$message;

			//$fwrite = fwrite($fp, $msg);
			if(!$fwrite) {
				//$this->_pushFailed($pid);
				write_log(LOG_FILE, "Failed sending push to APNS");
				$this->close_ssl_socket($mode);
				$this->check_feedback($mode);
					
				return false;
			}
			else {
				write_log(LOG_FILE, "Message sent to APNS");
				// "Provider Communication with Apple Push Notification Service"
				// http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/CommunicatingWIthAPS/CommunicatingWIthAPS.html#//apple_ref/doc/uid/TP40008194-CH101-SW1
				// "If you send a notification and APNs finds the notification malformed or otherwise unintelligible, it
				// returns an error-response packet prior to disconnecting. (If there is no error, APNs doesn't return
				// anything.)"
				// 
				// This complicates the read if it blocks.
				// The timeout (if using a stream_select) is dependent on network latency.
				// default socket timeout is 60 seconds
				// Without a read, we leave a false positive on this push's success.
				// The next write attempt will fail correctly since the socket will be closed.
				//
				// This can be done if we start batching the write

				// Read response from server if any. Or if the socket was closed.
				// [Byte: data.] 1: 8. 1: status. 4: Identifier.
				$tv_sec = 1;
				$tv_usec = null; // Timeout. 1 million micro seconds = 1 second
				$r = array($fp); $we = null; // Temporaries. "Only variables can be passed as reference."
				$num_changed = stream_select($r, $we, $we, $tv_sec, $tv_usec);
				
				if(false===$num_changed) {
					
					write_log(LOG_FILE, "Failed selecting stream to read.");
					$this->close_ssl_socket($mode);
					$this->check_feedback($mode);
					return false;
				}
				else if($num_changed>0) {
					$command = ord(fread($fp, 1));
					$status = ord(fread($fp, 1));
					$identifier = implode('', unpack("N", fread($fp, 4)));
					$status_desc = array(
						0 => 'No errors encountered',
						1 => 'Processing error',
						2 => 'Missing device token',
						3 => 'Missing topic',
						4 => 'Missing payload',
						5 => 'Invalid token size',
						6 => 'Invalid topic size',
						7 => 'Invalid payload size',
						8 => 'Invalid token',
						255 => 'None (unknown)',
					);
					
					//write_log(LOG_FILE, "APNS responded with command($command) status($status) pid($identifier).");
					if($status >= 0)
						$desc = isset($status_desc[$status]) ? $status_desc[$status] : 'Unknown';
					else
						$desc = 'Unknow';
					
					if($status>0) {
						write_log(LOG_FILE, "APNS responded with error for pid($identifier). status($status: $desc)", E_USER_ERROR);
						// The socket has also been closed. Cause reopening in the loop outside.
						$this->close_ssl_socket($mode);
						$this->check_feedback($mode);
						return false;
					}
					else {
						// Apple docs state that it doesn't return anything on success though
						//$this->_pushSuccess($pid);
						write_log(LOG_FILE, "APNS responded with success for pid($identifier). status($status: $desc)", E_USER_ERROR);
						echo "Here";
						$this->close_ssl_socket($mode);
						$this->check_feedback($mode);
						return true;
					}
				} else {
					//$this->_pushSuccess($pid);
					$this->close_ssl_socket($mode);
					$this->check_feedback($mode);
					return true;
				}
			}
		}

	}

/**
	 * Fetch APNS Messages
	 *
	 * This gets called automatically by _pushMessage.  This will check with APNS for any invalid tokens and disable them from receiving further notifications.
	 *
	 * @param string $development Which SSL to connect to, Sandbox or Production
	 * @access private
	 */
	private function check_feedback($mode){
		$url = $this->apns_data[$mode]['feedback'];
		echo "Check feedback with ".$url;
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apns_data[$mode]['certificate']);
		stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
		$fp = stream_socket_client($url, $error, $error_string, 60, (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT), $ctx);

		if(!$fp) 
			write_log(LOG_FILE, "Failed to connect to device: {$error} {$error_string}.");
		
		while ($devcon = fread($fp, 38)){
			$arr = unpack("H*", $devcon);
			$rawhex = trim(implode("", $arr));
			$token = substr($rawhex, 12, 64);
			if(!empty($token)){
				//$this->_unregisterDevice($token);
				//$this->_triggerError("Unregistering Device Token: {$token}.");
				write_log(LOG_FILE, "Invalid device token {$token}. Need to be removed");
			}
		}
		fclose($fp);
	}


	/**
	 * JSON Encode
	 *
	 * Some servers do not have json_encode, so use this instead.
	 *
	 * @param array $array Data to convert to JSON string.
	 * @access private
	 * @return string
	 */
	private function encode_json($array=false){
		//Using json_encode if exists
		if(function_exists('json_encode')){
			return json_encode($array);
		}
                if(is_null($array)) return 'null';
		if($array === false) return 'false';
		if($array === true) return 'true';
		if(is_scalar($array)){
			if(is_float($array)){
				return floatval(str_replace(",", ".", strval($array)));
			}
			if(is_string($array)){
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $array) . '"';
			}
			else return $array;
		}
		$isList = true;
		for($i=0, reset($array); $i<count($array); $i++, next($array)){
			if(key($array) !== $i){
				$isList = false;
				break;
			}
		}
		$result = array();
		if($isList){
			foreach($array as $v) $result[] = $this->_jsonEncode($v);
			return '[' . join(',', $result) . ']';
		}
		else {
			foreach ($array as $k => $v) $result[] = $this->_jsonEncode($k).':'.$this->_jsonEncode($v);
			return '{' . join(',', $result) . '}';
		}
	}
	
}
?>
