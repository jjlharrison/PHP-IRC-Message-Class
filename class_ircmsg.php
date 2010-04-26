<?php
/**
 * PHP version 5
 *
 * Copyright (c) 2010 James Harrison (jjlharrison.me.uk),
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author James Harrison
 * @copyright  2010 James Harrison (jjlharrison.me.uk)
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    1.0
 */

/**
* Class to handle IRC protocol messages.
 * 
 * Note: This class was created based on information from RFC 1459 (http://tools.ietf.org/html/rfc1459). I do not guarantee
* that it is fully compliant with RFC 1459 but I did try.
*/
class IRCMessage{
	
	/**
	 * The regular expression for parsing IRC protocol messages.
	 */
	const REGEXMSG = "^(?P<message>((?P<prefix>:((?P<nick>[A-Za-z][a-z0-9\-\[\]\`^{}]*)|(?P<servername>((?P<host>(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9]))|(?P<ip>(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])))))(!(?P<user>[^ |\r|\n]+?))?(@(?P<userhost>(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9]))?)?) )?(?<command>([a-zA-Z]+)|[0-9]{3}) (?P<params>.+)?)$";

	/**
	 * Property for storing the message as a raw string.
	 *
	 * @var string
	 */
	public $message;

	public $prefix;
	public $nick;
	public $servername;
	public $user;
	public $userhost;
	public $command;
	public $params;
	
	/**
	 * This function takes an IRC protocol message and parses it and constructs the
   * IRCMessage object.
	 *
	 * @param string $message The IRC protocol message as a string.
	 * @author James Harrison
	 */
	public function __construct($message){
		if(preg_match("/".IRCMessage::REGEXMSG."/", $message, $msg)){
			$this->message = $msg['message'];
			$this->prefix = $msg['prefix'];
			$this->nick = $msg['nick'];
			$this->servername = $msg['servername'];
			$this->user = $msg['user'];
			$this->userhost = $msg['userhost'];
			$this->command = $msg['command'];
			//Parse params:
			$params = explode(' ', $msg['params']);
			$t = false;
			$trailing = '';
			foreach($params as $param){
				if($t || substr($param, 0, 1)==':'){ //Trailing param
					$trailing .= (($t===false)?substr($param, 1):' '.$param);
					$t = true;
				}
				else{ //Middle param
					$this->params[] = $param;
				}
			}
			if($t){
				$this->params[] = $trailing;
			}
			//End parsing of params.
		}
		else{
			throw new IRCMessageException("Message passed to IRCMessage constructor in invalid format ($message).");
		}
	}
}

/**
* Exception for the IRCMessage class.
*/
class IRCMessageException extends Exception{}
?>