<?php
/*****************************************************************************************************************************************/
/*    ____ ___  _     _        _    ____   ____ ___  
/*   / ___/ _ \| |   | |      / \  | __ ) / ___/ _ \ 
/*  | |  | | | | |   | |     / _ \ |  _ \| |  | | | |
/*  | |__| |_| | |___| |___ / ___ \| |_) | |__| |_| |
/*   \____\___/|_____|_____/_/   \_\____/ \____\___/ 
/* 
/*****************************************************************************************************************************************/
/*  Author:			Collabco Software (Oli Newsham)
/*  Support:		support@collabco.co.uk
/*  Website:		Collabco.co.uk
/*  Twitter:		@collabco
/*****************************************************************************************************************************************/
/*
/*  This source code must retain the above copyright notice, this list of conditions and the following disclaimer.
/*
/*  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT 
/*  NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL 
/*  THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
/*  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
/*  HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
/*  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
/*
/*****************************************************************************************************************************************/

	if (!defined('MOODLE_INTERNAL')) 
	{
		die('Direct access to this script is forbidden.');
	}
	
	@date_default_timezone_set('UTC');	

	require_once($CFG->libdir.'/authlib.php');
	

	class auth_plugin_collabcosso extends auth_plugin_base {
		
        function auth_plugin_collabcosso() {
			$this->authtype = 'collabcosso';
			$this->config = get_config('auth/collabcosco');
		}

		function user_login($username, $password) {
		   return false;
		}

		function can_reset_password() {
			return false;
		}

		function can_signup() {
			return false;
		}

		function can_confirm() {
			return false;
		}

		function can_change_password() {
			return false;
		}
		
		function is_internal() {
			return true;
		}
		
		function loginpage_hook() 
		{      
			$plugin_loggingname = "Collabco SSO";
            
            global $CFG, $USER, $SESSION;    

			if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['u']) && !empty($_GET['t']) && !empty($_GET['h']) && !empty($_GET['r'])) 
			{            
				$username = $_GET['u'];
				$time = $_GET['t'];
				$hash = $_GET['h'];
				$redirect = urldecode($_GET['r']);
				
				//Ensure we don't generate the hash on an escaped backslash
				$usernameH = str_replace("\\\\","\\",$username);
                
                if (strlen($CFG->auth_collabcosso_salt) == 0)
				{
					add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Salt is not set." );
					return false;
				}
                else if (strlen($CFG->auth_collabcosso_salt) <= 10)
                {
                    add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Salt is an insufficient length." );
					return false;
                }
				
				$strToHash = $usernameH . $time . $redirect . $CFG->auth_collabcosso_salt;
				
				$expectedHash = md5($strToHash);

				if (strcmp($hash,$expectedHash) !== 0)
				{
					add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Bad hash: " . addslashes($username));
					return false;
				}

				if (isloggedin() && !isguestuser()) 
				{                
                    add_to_log(SITEID, $plugin_loggingname, 'Warning', 'auth.php', "User is already loggedin: " . addslashes($username));
                    
                    echo "<script language='Javascript'>";
					echo "    alert ('this single sign on link has expired')";
                    echo "    window.location.replace('" . $CFG->wwwroot . "/" . $redirect . "')";
					echo "</script>";
                    
				    //redirect($CFG->wwwroot . "/" . $redirect);
                    
				    return false;
				}
				
				$dbits = explode("-",$time);				
				
				$incomingDateString = $dbits[2].'-'.$dbits[1].'-'.$dbits[0].' '.$dbits[3].':'.$dbits[4].':'.$dbits[5];
				
				$timestamp = strtotime($incomingDateString);				

				$now = time();
								
				if ($now - $timestamp <= 120)
				{
					//Moodle doesn't contain user principals so strip the domain
					if (strpos($username,"\\\\") !== false)
					{
						$usernameBits = explode("\\\\", $username);
						$username = $usernameBits[1];
					}
                    
					if ($user)
					{          
						add_to_log(SITEID, $plugin_loggingname, 'sso login event', "auth.php" , "SSO login: " . addslashes($username), 0, $USER->id);

						$auth = empty($USER->auth) ? 'manual' : $USER->auth;  // use manual if auth not set

						if (!empty($USER->suspended)) 
						{
							add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Suspended login: " . addslashes($username), 0, $USER->id);
							return false;
						}
            
						if ($auth=='nologin' or !is_enabled_auth($auth)) 
						{
							add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Disabled login: " . addslashes($username), 0, $USER->id);
							return false;
						}	
          
						complete_user_login($user);
            
						if (user_not_fully_set_up($USER)) 
						{
						   $urltogo = $CFG->wwwroot . "/user/edit.php";
						}
						else
						{
						   $urltogo = $CFG->wwwroot . "/" . $redirect;
						}
                        
                        add_to_log(SITEID, $plugin_loggingname, 'Sign-In', 'auth.php', "Successfully signed in: " . addslashes($username), 0, $USER->id);
						
						redirect($urltogo);
					}
					else
					{
                        add_to_log(SITEID, $plugin_loggingname, 'Error', 'auth.php', "Unknown user: " . addslashes($username));
						return false;
					}
				}
				else
				{
					echo "<script language='Javascript'>";
					echo "alert ('this single sign on link has expired')";
					echo "</script>";
				}
			}
		}
		
		function config_form($config, $err, $user_fields) {
			include "config.html";
		}
		
		function process_config($config) {
			return true;
		}
	}

?>