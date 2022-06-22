<?php

require("config.php");

// Define our functions
function stop()
{
die();
}

function mailtokg()
{

mail($to,$subject,$message,$headers);
}


//$me=".";
//there now stop complaining
//-------
//       SETS BOTS HOSTMASK
//-------

$bot = ":venashelper!www-data@vena.euronetworking.co.cc";

// Prevent PHP from stopping the script after 30 sec
set_time_limit(0);
 
// Opening the socket to the network
$socket = fsockopen("irc.kottnet.net", 6667);
 
// Send auth info
fputs($socket,"USER Vena 8 *  : Vena Helen Rogoff \r\n");
fputs($socket,"NICK Vena\r\n");

sleep(1);

// Identify with NickServ
fputs($socket,"PRIVMSG NickServ :identify Vena ******\r\n");

sleep(1);

// Join channel. This doesnt work now so message it with .join
foreach ($channels as $channel)
{
		fputs($socket,"JOIN ".$channel."\r\n");
		sleep(1);
}
$unameunusable = shell_exec('uname -mnrs');
$uname = chop($unameunusable);
$dateunusable = shell_exec('date +%Y-%m-%d_%H:%M:%S');

$date = chop($dateunusable);
if ($infoatlaunch == "true") {
		fputs($socket,"PRIVMSG ".$ex[2]." :Hi! I am Vena running on ".$uname.". The current local time is ".$date.". For more help talk to Niles or do .help, .commands or .users \r\n");
}
// headers for out txt file
foreach ($channels as $channel) {
$fh = fopen($logDir."".$channel, 'a') or die("can't open file");
$stringData = "                Connected at $date\r\n\r\n               ---------------------\r\n\r\n";
fwrite($fh, $stringData);
fclose($fh);
}

// Force an endless while
while(1) {

	// Continue the rest of the script here
	while($data = fgets($socket, 128)) {

		echo nl2br($data);
		flush();

		// Separate all data
		$ex = explode(' ', $data);
		$colins = explode(':', $data);
		$points = explode('!', $data);
		// Send PONG back to the server
		if($ex[0] == "PING"){
			fputs($socket, "PONG ".$ex[1]."\r\n");
		}

//-------
//       SETS EXACT NICK
//-------
		$pm = str_replace(":", '', $points[0]);


		$dateunusable = shell_exec('date +%Y-%m-%d_%H:%M:%S');
		$date = chop($dateunusable);
		$command = str_replace(array(chr(10), chr(13)), '', $ex[3]);
		$ex[4] = str_replace(array(chr(10), chr(13)), '', $ex[4]);

//-------
//       LOGGING
//-------
		if ($ex[1] == "PRIVMSG") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." <".$points[0]."> ".$colins[2];
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "JOIN") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." ".$points[0]." has joined ".$ex[2];
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "PART") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." ".$points[0]." has left ".$ex[2];
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "QUIT") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." ".$points[0]." has quit with message:  ".$colins[3].":".$colins[4];
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "NICK") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." ".$points[0]." is now known as ".$ex[2];
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "MODE") {
			$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
			$stringData = $date." ".$points[0]." sets mode on ".$ex[2].": ".$ex[3]." ".$ex[4]." ".$ex[4]."\r\n";
			fwrite($fh, $stringData);
			fclose($fh);
		}
		elseif ($ex[1] == "001" || $ex[1] == "002" || $ex[1] == "003" || $ex[1] == "004" || $ex[1] == "005" || $ex[1] == "251" || $ex[1] == "252" || $ex[1] == "254" || $ex[1] == "255" || $ex[1] == "265" || $ex[1] == "266" || $ex[1] == "250" || $ex[1] == "375" ||$ex[1] == "372" || $ex[1] == "376" ||$ex[1] == "333" || $ex[1] == "353" || $ex[1] == "366" ||$ex[1] == "396") {
			;
		}
		else {
			// Logging
			if (system('echo \"'.$data.'\"|grep \#|wc -l') == "1") {
				foreach ($channels as $channel) {
					$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
					$stringData = $date." ".$data;
					fwrite($fh, $stringData);
					fclose($fh);
				}
			}
		}
		// Say something in the channel
                if ($command == ":".$commandchar."ping") {
					fputs($socket, "PRIVMSG $ex[2] :Pong!\r\n");
                }
                if ($command == ":".$commandchar."op") {
                        if ($ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $me[0] || $ex[0] == $me[1]) 
			        			fputs($socket, "MODE $ex[2] +o :".$ex[4]."\r\n");
						else
	                    		fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");

				}
                if ($command == ":".$commandchar."deop") {
                        if ($ex[0] == $ops[0] || $ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $ops[1]) 
	                        	fputs($socket, "MODE $ex[2] -o :".$ex[4]."\r\n");
                        else
                                fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
                if ($command == ":".$commandchar."voice") {
                        if ($ex[0] == $voices[0] || $ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $ops[0] || $ex[0] == $ops[1]) 
                        	fputs($socket, "MODE $ex[2] +v :".$ex[4]."\r\n");
                        else
                                fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
				if ($command == ":".$commandchar."devoice") {
                        if ($ex[0] == $voices[0] || $ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $hops) 
								fputs($socket, "MODE $ex[2] -v :".$ex[4]."\r\n");
                        else
                                fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");

				}
                if ($command == ":".$commandchar."halfop") {
                        if ($ex[0] == $hops || $ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $ops[0] || $ex[0] == $ops[1]) 
								fputs($socket, "MODE $ex[2] +h :".$ex[4]."\r\n");
                        else
                        		fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
        		if ($command == ":".$commandchar."dehalfop") {
                        if ($ex[0] == $hops || $ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $ops[0] || $ex[0] == $ops[1]) {
		                        fputs($socket, "MODE $ex[2] -h :".$ex[4]."\r\n");
                        }
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                        				fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
						}
				}

				if ($command == ":".$commandchar."identify") {
    				    if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
						        fputs($socket,"PRIVMSG NickServ :identify Vena ******\r\n");
						        fputs($socket,"PRIVMSG $ex[2] :Identified as Vena successfully!\r\n");
						}
				}

				if ($command == ":".$commandchar."say") {
						if ($ex[5] == "!kick" || $ex[5] == "!kickban" || $ex[5] == "!ban" || $ex[5] == "!flags" || $ex[5] == "!op" || $ex[5] == "!deop" || $ex[5] == "!voice" || $ex[5] == "!devoice" || $ex[5] == "!invite" || $ex[5] == "!set" || $ex[5] == "!unban" || $ex[5] == "!access" || $ex[5] == "!akick" || $ex[5] == "!drop" || $ex[5] == "!getkey" || $ex[5] == "!info" || $ex[5] == "!quiet" || $ex[5] == "!role" || $ex[5] == "!status" || $ex[5] == "!sync" || $ex[5] == "!taxonomy" || $ex[5] == "!template" || $ex[5] == "!topicappend" || $ex[5] == "!topicprepend" || $ex[5] == "!unquiet") {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :No.\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :No.\r\n");
								}
								unset($ischannel);
						}
						else {
								fputs($socket, "PRIVMSG #".$ex[4]." :".$ex[5]." ".$ex[6]." ".$ex[7]." ".$ex[8]." ".$ex[9]."\r\n");
			}	} 

                if ($command == ":".$commandchar."me") 
                        fputs($socket, "PRIVMSG #".$ex[4]." :\x01ACTION ".$ex[5]." ".$ex[6]." ".$ex[7]." ".$ex[8]." ".$ex[9]."\x01 \r\n");
                if ($command == ":".$commandchar."mode") {
                        if ($ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $me[0] || $ex[0] == $me[1]) 
	                        	fputs($socket, "MODE $ex[2] ".$ex[4]." :".$ex[5]."\r\n");
                        else
                        		fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
                if ($command == ":".$commandchar."kick") {
                        if ($ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $me[0] || $ex[0] == $me[1]) 
		                        fputs($socket, "KICK $ex[2] ".$ex[4]." :".$ex[5]."\r\n");
                        else
                        		fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
				if ($command == ":".$commandchar."enters") {
						fputs($socket, "PRIVMSG $ex[2] :cant you put\r\n");
                        fputs($socket, "PRIVMSG $ex[2] :your shit\r\n");
                        fputs($socket, "PRIVMSG $ex[2] :on one line\r\n");
				}
				if ($command == ":".$commandchar."join") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1])
								fputs($socket,"JOIN $ex[4]\r\n");
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
				}
                if ($command == ":".$commandchar."part") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1])
                                fputs($socket,"PART $ex[4]\r\n");
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
                }
                if ($command == ":".$commandchar."cycle") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
		                        fputs($socket,"PART ".$ex[4]."\r\n");
								fputs($socket,"JOIN ".$ex[4]."\r\n");
						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}

                }


                if ($command == ":".$commandchar."die") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
								fputs($socket, "QUIT :die command recived from owner (".$ex[0].")\r\n");
								$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
								$stringData = "\r\n                Die called\r\n\r\n";
								fwrite($fh, $stringData);
								fclose($fh);
								stop(); // Hammertime!
						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
                } 
                if ($command == ":".$commandchar."reboot") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
								fputs($socket, "QUIT :reboot command recived from owner (".$ex[0].")\r\n");
								$fh = fopen($logDir."".$ex[2], 'a') or die("can't open file");
								$stringData = "\r\n                Reboot called\r\n\r\n";
								fwrite($fh, $stringData);
								fclose($fh);
								system('php ~/bot.php');
								stop(); // Hammertime!
						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}                }
                if ($command == ":".$commandchar."raw") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1] || $ex[0] == $bot)
                                fputs($socket, $ex[4]." ".$ex[5]." ".$ex[6]." ".$ex[7]." ".$ex[8]." ".$ex[9]." ".$ex[10]." ".$ex[11]." ".$ex[12]." ".$ex[13]." ".$ex[14]."\r\n");
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
                }
                if ($command == ":".$commandchar."php") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1])
                                eval($ex[4]." ".$ex[5]." ".$ex[6]." ".$ex[7]." ".$ex[8]."\r\n");
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
                }
                if ($command == ":Vena" || $command == ":Vena," || $command == ":Vena:" || $command == ":Vena:?") {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
				                        fputs($socket,"PRIVMSG $ex[2] :huh?\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :huh?\r\n");
								}

                }
                if ($command == ":".$commandchar."ban") {
                        if ($ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $me[0] || $ex[0] == $me[1]) 
		                        fputs($socket, "MODE $ex[2] +b ".$ex[4]."\r\n");
		                else
		                		fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
                if ($command == ":".$commandchar."kickban" || $command == ":".$commandchar."kb") {
                        if ($ex[0] == $ops[0] || $ex[0] == $ops[1] || $ex[0] == $me[0] || $ex[0] == $me[1]) {
                        		fputs($socket, "KICK $ex[2] ".$ex[4]."\r\n");
		                        fputs($socket, "MODE $ex[2] +b ".$ex[4]."!*@*\r\n");
		                }
		                else
		                		fputs($socket, "PRIVMSG $ex[2] :".$denymsg."\r\n");
                }
                if ($command == ":pls") {
                        fputs($socket, "PRIVMSG $ex[2] :if you say pls instead of please when asking for help because its shorter, we will say no instead of yes because its shorter\r\n");
                }
                if ($command == ":plz") {
                        fputs($socket, "PRIVMSG $ex[2] :if you say plz instead of please when asking for help because its shorter, we will say no instead of yes because its shorter\r\n");
                }
				if ($command == ":".$commandchar."nick") {
						fputs($socket,"NICK $ex[4]\r\n");
						foreach ($channels as $channel) {
							fputs($socket, "PRIVMSG $channel :".$pm." changing nick to ".$ex[4]."\r\n");
						}
				}
                if ($command == ":".$commandchar."users") {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                        				fputs($socket, "PRIVMSG $ex[2] :Owner: Niles\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :CoOwner: Helen (KittyGirl)\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :Ops: Plasmastar and ozzy\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :HalfOps: None\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :Voices: rcmaehl\r\n");
								}
								else {
                        				fputs($socket, "PRIVMSG $pm :Owner: Niles\r\n");
                        				fputs($socket, "PRIVMSG $pm :CoOwner: Helen (KittyGirl)\r\n");
                        				fputs($socket, "PRIVMSG $pm :Ops: Plasmastar and ozzy\r\n");
                        				fputs($socket, "PRIVMSG $pm :HalfOps: None\r\n");
                        				fputs($socket, "PRIVMSG $pm :Voices: rcmaehl\r\n");
                        		}
				}
                if ($command == ":".$commandchar."info") {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG ".$ex[2]." :Hi! I am Vena running on ".$uname.". The current local time is ".$date.". For more help talk to Niles or do .help, .commands or .users \r\n");
								}
								else {
										fputs($socket,"PRIVMSG ".$pm." :Hi! I am Vena running on ".$uname.". The current local time is ".$date.". For more help talk to Niles or do .help, .commands or .users \r\n");
								}
                }
				if ($command == ":".$commandchar."commands") {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                        				fputs($socket, "PRIVMSG $ex[2] :Owner and Co-Owner:".$commandchar."php ".$commandchar."raw ".$commandchar."die ".$commandchar."join ".$commandchar."part ".$commandchar."cycle ".$commandchar."reboot\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :Ops: ".$commandchar."op ".$commandchar."deop ".$commandchar."mode ".$commandchar."kick ".$commandchar."ban\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :HalfOps: ".$commandchar."halfop ".$commandchar."dehalfop\r\n");
                        				fputs($socket, "PRIVMSG $ex[2] :Voices: ".$commandchar."voice ".$commandchar."devoice\r\n");
										fputs($socket, "PRIVMSG $ex[2] :Misc Commands: ".$commandchar."say ".$commandchar."me ".$commandchar."enters ".$commandchar."cycle ".$commandchar."users ".$commandchar."info ".$commandchar."commands ".$commandchar."time ".$commandchar."uname ".$commandchar."version ".$commandchar."rules ".$commandchar."face\r\n");
								}
								else {
                        				fputs($socket, "PRIVMSG $pm :Owner and Co-Owner:".$commandchar."php ".$commandchar."raw ".$commandchar."die ".$commandchar."join ".$commandchar."part ".$commandchar."cycle ".$commandchar."reboot\r\n");
                        				fputs($socket, "PRIVMSG $pm :Ops: ".$commandchar."op ".$commandchar."deop ".$commandchar."mode ".$commandchar."kick ".$commandchar."ban\r\n");
                        				fputs($socket, "PRIVMSG $pm :HalfOps: ".$commandchar."halfop ".$commandchar."dehalfop\r\n");
                        				fputs($socket, "PRIVMSG $pm :Voices: ".$commandchar."voice ".$commandchar."devoice\r\n");
										fputs($socket, "PRIVMSG $pm :Misc Commands: ".$commandchar."say ".$commandchar."me ".$commandchar."enters ".$commandchar."cycle ".$commandchar."users ".$commandchar."info ".$commandchar."commands ".$commandchar."time ".$commandchar."uname ".$commandchar."version ".$commandchar."rules ".$commandchar."face\r\n");
								}
				}


                if ($command == ":".$commandchar."logout") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
								fputs($socket, "PRIVMSG NickServ :logout\r\n");
								fputs($socket, "PRIVMSG $ex[2] :logged out from Vena successfully!\r\n");
						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
				}
				
                if ($command == ":".$commandchar."face") {
						$face = Rand (1,8); 
						foreach ($channels as $channel) {
								if ($ex[2] == $channel) {
										$ischannel="yesh";
								}
								else {
										;
								}
						}
						if ($ischannel == "yesh") {
								if ($face == "1")
                        		        fputs($socket, "PRIVMSG $ex[2] ::)\r\n");
								if ($face == "2")
                        		        fputs($socket, "PRIVMSG $ex[2] ::(\r\n");
								if ($face == "3")
                        		        fputs($socket, "PRIVMSG $ex[2] :._.\r\n");
								if ($face == "4")
                        		        fputs($socket, "PRIVMSG $ex[2] :o.O\r\n");
								if ($face == "5")
                        		        fputs($socket, "PRIVMSG $ex[2] :._o\r\n");
								if ($face == "6")
                        		        fputs($socket, "PRIVMSG $ex[2] :D:<\r\n");
								if ($face == "7")
                        		        fputs($socket, "PRIVMSG $ex[2] ::D\r\n");
								if ($face == "8")
                        		        fputs($socket, "PRIVMSG $ex[2] :(:<\r\n");
                                
                        }
						else {

								if ($face == "1")
                        		        fputs($socket, "PRIVMSG ".$pm." ::)\r\n");
								if ($face == "2")
                        		        fputs($socket, "PRIVMSG ".$pm." ::(\r\n");
								if ($face == "3")
                        		        fputs($socket, "PRIVMSG ".$pm." :._.\r\n");
								if ($face == "4")
                        		        fputs($socket, "PRIVMSG ".$pm." :o.O\r\n");
								if ($face == "5")
                        		        fputs($socket, "PRIVMSG ".$pm." :._o\r\n");
								if ($face == "6")
                        		        fputs($socket, "PRIVMSG ".$pm." :D:<\r\n");
								if ($face == "7")
                        		        fputs($socket, "PRIVMSG ".$pm." ::D\r\n");
								if ($face == "8")
                        		        fputs($socket, "PRIVMSG ".$pm." :(:<\r\n");

						}
						unset($ischannel);
				}
				if ($command == ":".$commandchar."time") {
						foreach ($channels as $channel) {
								if ($ex[2] == $channel) {
										$ischannel="yesh";
								}
								else {
										;
								}
						}
						if ($ischannel == "yesh") {
								fputs($socket, "PRIVMSG ".$ex[2]." :".$date."\r\n");
						}
						else {
								fputs($socket, "PRIVMSG ".$pm." :".$date."\r\n");
						}
						unset($ischannel);
				}
                if ($command == ":".$commandchar."uname") {
						foreach ($channels as $channel) {
								if ($ex[2] == $channel) {
										$ischannel="yesh";
								}
								else {
										;
								}
						}
						if ($ischannel == "yesh") {
								fputs($socket, "PRIVMSG ".$ex[2]." :".$uname."\r\n");
						}
						else {
								fputs($socket, "PRIVMSG ".$pm." :".$uname."\r\n");
						}
						unset($ischannel);
						
                }
                if ($command == ":".$commandchar."rules") {
							foreach ($channels as $channel) {
									if ($ex[2] == $channel) {
											$ischannel="yesh";
									}
									else {
											;
									}
							}
							if ($ischannel == "yesh") {
                        			fputs($socket, "PRIVMSG ".$ex[2]." :1. No sitting in the pink leopard chair that's helen's favourite chair.\r\n");
                        			fputs($socket, "PRIVMSG ".$ex[2]." :2. No spamming\r\n");
                        			fputs($socket, "PRIVMSG ".$ex[2]." :3. Be respectful\r\n");
                        			fputs($socket, "PRIVMSG ".$ex[2]." :4. No Transphobes\r\n");
                        			fputs($socket, "PRIVMSG ".$ex[2]." :5. Trolls must behave.\r\n");
                        	}
							else {
                        			fputs($socket, "PRIVMSG ".$pm." :1. No sitting in the pink leopard chair that's helen's favourite chair.\r\n");
                        			fputs($socket, "PRIVMSG ".$pm." :2. No spamming\r\n");
                        			fputs($socket, "PRIVMSG ".$pm." :3. Be respectful\r\n");
                        			fputs($socket, "PRIVMSG ".$pm." :4. No Transphobes\r\n");
                        			fputs($socket, "PRIVMSG ".$pm." :5. Trolls must behave.\r\n");
							}
							unset($ischannel);
                }
                if ($command == ":".$commandchar."version") {
							foreach ($channels as $channel) {
									if ($ex[2] == $channel) {
											$ischannel="yesh";
									}
									else {
											;
									}
							}
							if ($ischannel == "yesh") {
								    fputs($socket, "PRIVMSG $ex[2] :".$pm.": KittyBot version N/A by Niles\r\n");
							}
							else {
									fputs($socket, "PRIVMSG ".$pm." :KittyBot version N/A by Niles\r\n");
							}
							unset($ischannel);
                }
                if ($command == ":".$commandchar."clearlog") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
								$fh = fopen($logDir."".$ex[2], 'w') or die("can't open file");
								$stringData = "                Log cleared at $date by $ex[0] \r\n\r\n               ---------------------\r\n\r\n";
								fwrite($fh, $stringData);
								fclose($fh);
						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG ".$pm." :".$denymsg."\r\n");
								}
								unset($ischannel);
						}
				}
                if ($command == ":".$commandchar."channels") {
                            $i=1;
							foreach ($channels as $channel) {
									if ($ex[2] == $channel) {
											$ischannel="yesh";
									}
									else {
											;
									}
							}
							if ($ischannel == "yesh") {
									foreach ($channels as $channel){
						    				fputs($socket, "PRIVMSG $ex[2] :".$i.". ".$channel."\r\n");
						    				$i++;
									}
							}
                     				else {
                							foreach ($channels as $channel){
					    							fputs($socket, "PRIVMSG ".$pm." :".$i.". ".$channel."\r\n");
					    							$i++;
											}
							}	
							unset($ischannel);
                }
                if ($command == ":".$commandchar."login") {
						if ($ex[4] == $pass['niles']) {
								$me[0] = $ex[0];
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :Logged in as niles\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Logged in as niles\r\n");
                        		}	
						}
						elseif ($ex[4] == $pass['helen']) {
								$me[1] = $ex[0];
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :Logged in as KG\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Logged in as KG\r\n");
                        		}	
                        }
						elseif ($ex[4] == $pass['Plasmastar']) {
								$ops[0] = $ex[0];
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :Logged in as Plasmastar\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Logged in as Plasmastar\r\n");
                        		}	
                        }
						elseif (md5($ex[4]) == $pass['ozzy']) {
								$ops[1] = $ex[0];
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :Logged in as ozzy\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Logged in as ozzy\r\n");
                        		}	
						}
						elseif ($ex[4] == $pass['rcmaehl']) {
								$voices[0] = $ex[0];
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :Logged in as rcmaehl\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Logged in as rcmaehl\r\n");
                        		}	
						}
						else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                                		fputs($socket, "PRIVMSG $ex[2] :".$pm."Login failure. bad password.\r\n");
                                }
                     			else {
		                                fputs($socket, "PRIVMSG ".$pm." :Login failure. bad password.\r\n");
                        		}	
						}
						unset($ischannel);
                }
                if ($command == ":".$commandchar."whoami") {
						if ($ex[0] == $me[0]) {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :You are logged in as niles.\r\n");
                     		    }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :You are logged in as niles.\r\n");
                        		}
						}
						elseif ($ex[0] == $me[1]) {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :You are logged in as KG.\r\n");
                     		    }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :You are logged in as KG.\r\n");
                        		}						
                        }
						elseif ($ex[0] == $ops[0]) {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :You are logged in as Plasmastar.\r\n");
                     		    }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :You are logged in as Plasmastar.\r\n");
                        		}						}
						elseif ($ex[0] == $ops[1]) {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :You are logged in as ozzy.\r\n");
                     		   }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :You are logged in as ozzy.\r\n");
                        		}						}
						elseif ($ex[0] == $voices[0]) {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :You are logged in as rcmaehl.\r\n");
                     		   }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :You are logged in as rcmaehl.\r\n");
                        		}						}
						else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
                     		           fputs($socket, "PRIVMSG $ex[2] :".$pm.": You are not logged in.\r\n");
                     		   }
                     			else {
                     		           fputs($socket, "PRIVMSG ".$pm." :".$pm.": You are not logged in.\r\n");
                        		}
                        		}
						unset($ischannel);
                }
                if ($command == ":".$commandchar."dance") {
						foreach ($channels as $channel) {
								if ($ex[2] == $channel) {
										$ischannel="yesh";
								}
								else {
										;
								}
						}
						if ($ischannel == "yesh") {
                        		fputs($socket, "PRIVMSG $ex[2] ::D\-<\r\n");
                        		fputs($socket, "PRIVMSG $ex[2] ::D|-<\r\n");
                        		fputs($socket, "PRIVMSG $ex[2] ::D/-<\r\n");
                        }
						else {
                        		fputs($socket, "PRIVMSG ".$pm." ::D\-<\r\n");
                        		fputs($socket, "PRIVMSG ".$pm." ::D|-<\r\n");
                        		fputs($socket, "PRIVMSG ".$pm." ::D/-<\r\n");
                        }
						unset($ischannel);

                }
                if ($command == ":".$commandchar."requestshell") {
						if ($ex[2] == "#KittyShells") {
								mailtokg();
								fputs($socket, "PRIVMSG ".$ex[2]." :Shell requested\r\n");
						}
						else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
		                        		fputs($socket, "PRIVMSG ".$ex[2]." :Please request a shell from #KittyShells\r\n");
		                        }
								else {
		                        		fputs($socket, "PRIVMSG ".$pm." :Please request a shell from #KittyShells\r\n");
		                        }
								unset($ischannel);
						}
                }
                if ($pm == "StAtIsTiCs") {
                		fputs($socket, "KICK #HelloKitty StAtIsTiCs\r\n");
                }
                if ($command == ":!get") {
                		foreach ($channels as $channel) {
		                		fputs($socket, "KICK $channel $pm :Get out of here you racist fuck.\r\n");
		                		fputs($socket, "MODE $channel +b ".$pm."!*@*\r\n");
						}
                }
                if ($command == ":".$commandchar."reload") {
                        if ($ex[0] == $me[0] || $ex[0] == $me[1]) {
								require("config.php");
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :Reloaded successfully at ".$date."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :Reloaded successfully at ".$date."\r\n");
								}
								unset($ischannel);

						}
                        else {
								foreach ($channels as $channel) {
										if ($ex[2] == $channel) {
												$ischannel="yesh";
										}
										else {
												;
										}
								}
								if ($ischannel == "yesh") {
										fputs($socket,"PRIVMSG $ex[2] :".$denymsg."\r\n");
								}
								else {
										fputs($socket,"PRIVMSG $pm :".$denymsg."\r\n");
								}
								unset($ischannel);
						}

                }
	}
 
}
 
?>
