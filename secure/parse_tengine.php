<?php

$TENGINE	=	"/etc/sysconfig/ha/nginx.conf";	/* Global */
//$TENGINE	=	"/etc/sysconfig/ha/keepalived.conf";	/* Global */

/* 1 = debuging, 0 or undefined = no debuging */
//$debug=1;

$main = array (
		"serial_no"			=> "",
		"daemon"			=> "",
		"master_process"		=> "",
		"timer_resolution"		=> "",
		"lock_file"			=> "",
		"user"				=> "",
		"worker_processes"		=> "",
		"worker_priority"		=> "",
		"worker_cpu_affinity"		=> "",
		"worker_rlimit_nofile"		=> "",
		"worker_rlimit_core"		=> "",
		"worker_rlimit_sigpending"	=> "",
		"working_directory"		=> "",
		"working_threads"		=> "",
		"thread_stack_size"		=> "",
		"env"				=> "",
		"debug_points"			=> "",
		"pid"				=> "",
		"error_log"			=> "",
	);

$http = array ();

$upstream = array();

$http_server = array();

$events = array (
		"worker_connections" => "",
		"connections" => "",
		"use" => "",
		"multi_accept" => "",
		"accept_mutex" => "",
		"accept_mutex_delay" => "",
		"debug_connection" => "",
		"reuse_port" => "",

	);

/* Global file descriptor for use as a pointer to the lvs.cf file */
$ngx_fd = 0;
$level = 0 ;
$service = "tengine";
$location_key="";

if (empty($debug)) { $debug = 0; } /* if unset, leave debugging off */

$buffer = "";

function parse_tengine($name, $datum) {
	global $debug;
	global $buffer;
	global $ngx_fd;
	global $main;
	global $http;
	global $http_server;
	global $location_key;
	global $upstream;
	global $service;

	global $level;
	static $server_count = 0;
	static $http_server_count = 0;
	static $upstream_count = 0;
	

	if ($debug) {
		if (!empty($buffer)) {
			echo "<FONT COLOR=\"white\">Level $level &nbsp;&nbsp;&nbsp;&nbsp; buffer $buffer name $name datum $datum</FONT><BR>";
		};
	};

	if (strstr($buffer,"{")) { 
		if ($name == "global_defs"
		    or $name == "http"
		    or $name == "least_conn"
		    or $name == "ip_hash"
		    or $name == "server" 
		) { 
			$datum = ""; 
		} else if ($name == "location") { // if 'location test {' appears, for some reason parse fail to strip the '{', thus here to strip it. 
			if(strstr($datum, "{")){
				$datum = trim($datum, "{");
			}
			$buffer = "$name $datum";
			
		}
			
		$buffer = "$name $datum";
		if ($debug) { echo "<FONT COLOR=\"GOLD\">Striping the \"{\". Level changed up. Calling parse() with buffer $buffer name $name datum $datum. <BR></FONT>"; };
		$level++;

		/* the following /mess/ is because I want to generate 'structures' ie	*/
		/* New VIRTUAL required virt[0]						*/
		/* New vitual.server required virt[0]:[0]				*/
		/* New vitual.server required virt[0]:[1]				*/
		/* New VIRTUAL required virt[1]						*/
		/* New vitual.server required virt[1]:[0]				*/
		/* New vitual.server required virt[1]:[1]				*/

		/* I'm sure my logic is flawed, however, this works			*/
		/* Note to self: NEVER TOUCH THESE TWO LINES AGAIN (I REALLY MEAN THAT)	*/
		if ($level == 1) { $server_count = -1; };
		if ($level == 1) { $http_server_count = -1; };
		if ($level == 1) { $upstream_count = -1; };
		if (($level >  1) && ($name == "real_server")) { $server_count++ ; }; 
		if (($level >  1) && ($name == "server")) { $http_server_count++ ; }; 
		if (($level >  1) && ($name == "upstream")) { $upstream_count++ ; }; 

		parse_tengine($name, $datum);
		return; /* <--- HIGHLY IMPORTANT! do **NOT** remove this VITAL command */
	 };

	if (strstr($buffer,"}")) {
		$name = "";
		$datum = "";
		$buffer = "$name $datum";
		if ($debug) { echo "<FONT COLOR=\"RED\">Striping the \"}\". Level changed down. Calling parse(). <BR></FONT>"; };
		$level--;
		parse_tengine($name, $datum);
		return; /* <--- HIGHLY IMPORTANT! do **NOT** remove this VITAL command */
	};

	/* Level 0 */
	if ($level == 0) {
		switch ($name) {
		
			case "serial_no"		:	$main['serial_no']		= $datum;
								break;
			case "daemon"			:	$main['daemon']			= $datum;
								break;
			case "master_process"		:	$main['master_process']		= $datum;
								break;
			case "timer_resolution"		:	$main['timer_resolution']	= $datum;
								break;
			case "lock_file"		:	$main['lock_file']		= $datum;
								break;
			case "user"			:	$main['user']			= $datum;
								break;
			case "worker_processes"		:	$main['worker_processes']	= $datum;
								break;
			case "worker_priority"		:	$main['worker_priority']	= $datum;
								break;
			case "worker_cpu_affinity"	:	$main['worker_cpu_affinity']	= $datum;
								break;
			case "worker_rlimit_nofile"	:	$main['worker_rlimit_nofile']	= $datum;
								break;
			case "worker_rlimit_core"	:	$main['worker_rlimit_core']	= $datum;
								break;
			case "worker_rlimit_sigpending"	:	$main['worker_rlimit_sigpending']	= $datum;
								break;
			case "worker_rlimit_sigpending"	:	$main['worker_rlimit_sigpending']	= $datum;
								break;
			case "working_directory"	:	$main['working_directory']	= $datum;
								break;
			case "working_threads"		:	$main['working_threads']	= $datum;
								break;
			case "thread_stack_size"	:	$main['thread_stack_size']	= $datum;
								break;
			case "env"			:	$main['env']			= $datum;
								break;
			case "debug_points"		:	$main['debug_points']		= $datum;
								break;
			case "pid"			:	$main['pid']		= $datum;
								break;
			case "error_log"		:	$main['error_log']	= $datum;
								break;

			case "http"			:	/* http block definitition */
									$service="http";
									break;

			case ""					:	break;
			default					:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level $level - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
									break;
		}
	}
	
	/* Level 1 */
	if ($level == 1) {
		switch ($name) {
			case "http"		: 		$service = "http";
						  		if ($service == "http") $http['http']	= $datum;
								if ($debug) { echo "<FONT COLOR=\"yellow\"><I>start of global definition </I><B>$service</B></FONT><BR>"; };
								break;

			case "upstream"		:	
							break;
			case "server"		:	
							break;

			case ""			:	break;

			default			:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level 1 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
							break;
		}
	}

	/* Level 2 */
	if ($level == 2 ) {
		switch ($name) {

			case "upstream"		:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for http block upstream (" 
									. ($upstream_count+1) . 
									")</I> - <B>\$upstream["
									. ($upstream_count+1) .  
									"]</B></FONT><BR>"; };
							$upstream[$upstream_count+1]['name']		= $datum;
							break;
			case "consistent_hash"	:	$upstream[$upstream_count+1]['lb'] =  $name . " " . $datum;
							break;
			case "session_sticky"	:	$upstream[$upstream_count+1]['lb'] =  $name . " " . $datum;
							break;
			case "least_conn"	:	$upstream[$upstream_count+1]['lb'] =  $name;
							break;
			case "ip_hash"		:	$upstream[$upstream_count+1]['lb'] =  $name;
							break;
			case "dynamic_resolve"	:	$upstream[$upstream_count+1]['dynamic_resolve'] =  $name . " " . $datum;
							break;
			case "keepalive"	:	$upstream[$upstream_count+1]['keepalive'] =  $name . " " . $datum;
							break;
			case "keepalive_timeout"	:	$upstream[$upstream_count+1]['keepalive_timeout'] =  $datum;
							break;
	
			case "upstream_server_config"		:
							if ($debug) {
                                                        echo "<FONT COLOR=\"yellow\"><I>service $service server datum" . $datum .  "</FONT><BR>"; 
							}
								$datum = trim($datum);
								$temp = explode(" ", $datum);
                                                        	//echo "<FONT COLOR=\"yellow\"><I>TEMP" . $temp[0] .  "</FONT><BR>"; 
								$upstream[$upstream_count+1]['server'][$temp[0]] = $datum;

							break;
			case "check"		:	$upstream[$upstream_count+1]['check'] = $datum;
							break;
			case "check_keepalive_requests"		:	$upstream[$upstream_count+1]['check_keepalive_requests'] = $datum;
							break;
			case "check_http_send"	:	$upstream[$upstream_count+1]['check_http_send'] = $datum;
							break;
			case "check_http_expect"	:	$upstream[$upstream_count+1]['check_http_expect'] = $datum;
							break;
			case "check_http_expect_alive"	:	$upstream[$upstream_count+1]['check_http_expect_alive'] = $datum;
							break;
			case "check_fastcgi_param"	:	$upstream[$upstream_count+1]['check_fastcgi_param'] = $name . " " . $datum;
							break;
			case "listen"	:			$http_server[$http_server_count+1]['listen'] = $datum;
							break;

			case ""			:	break;
			default			:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level2 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
							break;
		}
	}

	/* Level 3 */
	if ($level == 3 ) {
		switch ($name) {
	
			case "location"		:    	if ($debug) { echo "<FONT COLOR=\"yellow\"><I>location datum" . $datum .  "</FONT><BR>"; }
							$datum = trim($datum);
							$temp_loc = explode(' ', $datum);
							if(count($temp_loc) == 1) {
								$location_key = trim($temp_loc[0]);
								$http_server[$http_server_count+1]['location'][$location_key]['name'] = $datum;
							} else if (count($temp_loc) == 2) {
								$location_key = trim($temp_loc[1]);
								$http_server[$http_server_count+1]['location'][$location_key]['name'] = $datum;
							}
							//if ($debug) { echo "<FONT COLOR=\"yellow\"><I>location key" . $location_key .  "</FONT><BR>"; }
							break;
			case "proxy_pass"	:	//if ($debug) { echo "<FONT COLOR=\"yellow\"><I>proxy_pass location key" . $location_key .  "</FONT><BR>"; }
							$http_server[$http_server_count+1]['location'][$location_key]['proxy_pass'] = $datum;
							//if ($debug) { echo "<FONT COLOR=\"yellow\"><I>proxy_pass " . $http_server[$http_server_count+1]['location'][$location_key]['proxy_pass']  .  "</FONT><BR>"; }
							break;
									
			case ""		:	break;
			default		:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level3 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
						break;
		}
	} // END LEVEL 3

	/* Level 4 */
	if ($level == 4 ) {
		switch ($name) {

			case ""		:	break;
			default		:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level4 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
						break;
		}
	} // END LEVLE 4

}

function next_line() {
	global $ngx_fd;
	global $buffer;
	global $debug;

	while (!feof($ngx_fd)) {
		$buffer = fgets($ngx_fd, 4096);
		if ($debug) { echo "Buffer = [$buffer]<BR>"; }

		/* All data is comprised of a name, an optional seperator and a datum */

		/* oh wow!.. trim()!!! I could hug somebody! */
		$buffer = preg_replace('/;+$/', ' ', $buffer);
		$buffer = trim($buffer);
		//$buffer = rtrim($buffer, ';');

		if (strlen ($buffer) > 4) { /* basically 'if not empty',.. however 'if (!empty($buffer)' didn't work */
			/* explode! oh boy! */
			$pieces = explode(" ", $buffer);

			$name = $pieces[0];
			if (strstr($buffer, "=")) {
				$datum = $pieces[2];
			} else {
				$datum = $pieces[1];
			}
		}
	}
}

function read_config() {
	global $ngx_fd;
	global $buffer;
	global $name;
	global $datum;
	global $debug;
	global $level;
	global $service;


	while (!feof($ngx_fd)) {
		$buffer = fgets($ngx_fd, 4096);
		if ($debug) {
			echo "Buffer = [$buffer]<BR>";
		}

		/* all data is comprised of a name, an optional seperator, and a datum */

		/* oh wow!.. trim()!!! I could hug somebody! */
		//$buffer = preg_replace('/;+$/', ' ', $buffer);
		$buffer = str_replace(';', ' ', $buffer); //replace trailing ; with space
		$buffer = trim($buffer);

		//BUG!!! strlen > 4 condition check cause vrrp track_interface like eth1 cause parsing write failure
		if (strlen ($buffer) >= 2) { /* basically if not empty,.. however if (!empty($buffer) didn't work */
			/* explode! oh boy! */
			//$pieces = explode(" ", $buffer);
			//reference http://fr2.php.net/manual/en/function.preg-split.php#92632 for following regex
			if ( strstr($buffer,"check_http_send" )
			     or preg_match("/^script/", $buffer) //since strstr returns true for string "script" and "vrrp_script", so use preg_match
			     or preg_match("/^check_http_expect$/", $buffer) //match check_http_expect exactly,otherwise messed with check_http_expect_alive
				) { //!!! if strings contains quote and space in quote use following regex!!!
				$pieces = preg_split("/[\s,]*(\"[^\"]+\")[\s,]*/", $buffer, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			} else {
				$pieces = preg_split('/\s+/', $buffer);
			}
/*
			if ($debug) {
				echo "pieces[0] = [$pieces[0]]<BR>";
				echo "pieces[1] = [$pieces[1]]<BR>";
				echo "pieces[2] = [$pieces[2]]<BR>";
				echo "pieces[3] = [$pieces[3]]<BR>";
				echo "pieces[4] = [$pieces[4]]<BR>";
				echo "pieces[5] = [$pieces[5]]<BR>";
				echo "pieces[6] = [$pieces[6]]<BR>";
			}
*/

			$name = $pieces[0];
			

			if ( (  $pieces[0] == "session_sticky"
			       || $pieces[0] == "check"
				) && $level == 2 ) { //virtual_routes
			// http://stackoverflow.com/questions/3591867/how-to-get-the-last-n-items-in-a-php-array-as-another-array
				$datum = implode(" ", array_slice($pieces, -(count($pieces)-1)));
			}
			//a trick to differ the upstream server and http server block config
			//change the $name value so in parse to match the artificial value instead of the real input $name value
			 else if ( $pieces[0] == "server" && $pieces[1] != "{" && $level == 2) {
				$name = "upstream_server_config";
				$count = 0;
				$datum = "";
				foreach ($pieces as $value) {
					if($count == 0) {
						$count++;
						continue;
					} 
					$datum = $datum . " " . $value;
					$count++;
				}
			}
			else if (isset($pieces[2]) 
				&& ( $pieces[0] == "dynamic_resolve" 
				      || $pieces[0] == "keepalive" 
				      || $pieces[0] == "check_fastcgi_param" 
				      || $pieces[0] == "check_http_expect_alive" 
				      || $pieces[0] == "location" 
				    ) ) {
                        	$datum = $pieces[1] . " " . $pieces[2];
			}
			else if (isset($pieces[2]) and $pieces[1] == "weight") {
					$datum = implode(" ", array_slice($pieces, -(count($pieces)-1)));
			}
			else if (isset($pieces[2]) and $pieces[0] == "real_server") {
                                        $datum = $pieces[1] . " " . $pieces[2];
                        } else {
				$datum = $pieces[1];
					
			}

		}
		parse_tengine($name, $datum);
	}
	/* specials that need to be preset if unset */
	if (empty($prim['rsh_command'])) {
		$rsync_tool = $prim['rsh_command'] = "ssh";
	}

	if (empty($prim['debug_level'])) {
		$debug_level = $prim['debug_level'] = "NONE";
	}
	return;
}


function backup_lvs() {
	global $prim;
	global $TENGINE;
	global $SERVER_ADDR;
	global $debug;

	return; /* UNTIL SUCH TIME AS THE GUI/PULSE INTERACTION IS SORTTED OUT */

	$command = "";

	if ($debug) { echo "<HR>Scripts are running on $SERVER_ADDR - Primary server is: " . $prim['primary'] . "<BR>"; }


	/* ---- OLD method ---- I used to allow user nobody to do the scp to root on the other machine, now we use suexec
	                        This code fragment has been left in as a placeholder for future approved file sync scheme
//	if ($SERVER_ADDR == $prim['primary']) {
//		if (($prim['backup'] != "") && ($prim['backup_active'] != 0)) {
//			switch ($prim['rsh_command']) {
//				case "rsh"	:	$command = "rcp /etc/sysconfig/ha/lvs.cf piranha@" . $prim['backup'] . ":/etc/sysconfig/ha/lvs.cf";
//							if ($debug) { echo "<BR>SYNC active, Executing: $command<BR>"; }
//							system($command, $ret);
//							break;
//				case "ssh"	:	$command = "scp /etc/sysconfig/ha/lvs.cf piranha@" . $prim['backup'] . ":/etc/sysconfig/ha/lvs.cf";
//							if ($debug) { echo "<BR>SYNC active, Executing: $command<BR>"; }
//							system($command, $ret);
//							break;
//				default		:	echo "<BR>SYNC active, BUT, No copy to a remote machine made (no copy mode selected)<BR>";
//							break;
//			}
//
//			if (($ret !=0) && ($prim['backup_active'] != 0)) {
//				$user = `/usr/bin/id`;
//				echo "<TABLE BGCOLOR=\"c0c0c0\" WIDTH=\"100%\" BORDER=\"0\"CELLSPACING=\"0\"><TR><TD VALIGN=top><H2><FONT COLOR=red>WARNING</FONT>:&nbsp;</H2></TD>";
//				echo "<TD>It was not possible to syncronize the /etc/sysconfig/ha/lvs.cf file on " .  $prim['backup'] . " using the command <P>$command<BR>as $user<p>";
//				echo "It may be that that system is down OR that the required access privilages for user nobody and/or piranha are incorrect.<BR>";
//				echo "It may be prudent to turn off the backup feature in the '<A HREF=\"redundancy.php\" NAME=\"Redundany\">Redundany</A>' panel ";
//				echo "until this issue is resolved</TD>";
//				echo "</TR></TABLE>";
//			}
//		}
//	}
	 ---- end OLD method ---- */

	if (($prim['primary'] != "") && ($SERVER_ADDR != $prim['primary'])) {
		echo "<TABLE BGCOLOR=\"c0c0c0\" WIDTH=\"100%\" BORDER=\"0\"CELLSPACING=\"0\"><TR><TD VALIGN=top><H2><FONT COLOR=red>WARNING</FONT>:</H2></TD>";
		echo "<TD>You are attempting to edit the lvs.cf file from a server that is not the cluster master<BR>";
		echo "Please use the primary server as any modifications made on the backup machine will be overwritten by the primary<BR>";
		echo "Based on your current lvs.cf configuration clicking <A HREF=\"HTTP://" . $prim['primary'] . "/piranha/piranha.html\" NAME=\"Primary\">HERE</A> will to attempt connection to the primary else please correct the 'Primary LVS server IP' from the global settings page</TD>";
		echo "</TR></TABLE>";
		return;
	}
	
	/* We use apache's suexec module to pass a killall -USR2 pulse */
	/* argh! not anymore since piranha is installed with a uid < 100 DAMN */

	if (($SERVER_ADDR == $prim['primary']) && ($prim['backup_active'] != "0")) {
		echo `/usr/bin/killall -q -USR2 pulse`;
	}

	return;
}

function print_arrays() {
	/* debugging function only */
	global $main;
	global $http;
	global $http_server;
	global $upstream;
	global $debug;

	$loop1 = $loop2 = 0;

	echo "<FONT COLOR=\"Gold\">";
	echo "<HR>DEBUG<HR>";
	echo "<B>Main</B>";
	echo "<BR>serial_no = "			. $main['serial_no'];
	echo "<BR>worker_processes = "		. $main['worker_processes'];
	echo "<BR>worker_cpu_affinity = "	. $main['worker_cpu_affinity'];
	echo "<BR>error_log = "			. $main['error_log'];
	echo "<BR>pid = "			. $main['pid'];

	echo "<P><B>http</B>";
	
	$loop1 = $loop2 = 0;

	echo "<P><B>upstream</B>";
        //echo "<BR>" .  var_dump($upstream);

        while ($upstream[++$loop1]['name'] != "" ) { /* NOTE: must use *pre*incrempent not post */
		$name = $upstream[$loop1]['name'];
                echo "<BR>upstream [$loop1] [name] = "        . $upstream[$loop1]['name'];
                echo "<BR>upstream [$loop1] [lb] = "        . $upstream[$loop1]['lb'];
                echo "<P><B>server</B>";
		echo "<BR>";

		foreach ($upstream[$loop1]['server'] as $key => $value) {
			echo $key . " => " . $value . "<BR>";
		}
                echo "<BR>upstream [$loop1] [check] = "        . $upstream[$loop1]['check'];
                echo "<BR>upstream [$loop1] [check_keepalive_requests] = "        . $upstream[$loop1]['check_keepalive_requests'];
                echo "<BR>upstream [$loop1] [check_http_send] = "        . $upstream[$loop1]['check_http_send'];
                echo "<BR>upstream [$loop1] [check_http_expect_alive] = "        . $upstream[$loop1]['check_http_expect_alive'];
                echo "<BR>upstream [$loop1] [check_http_expect] = "        . $upstream[$loop1]['check_http_expect'];
		echo "<BR>";



        }

	$loop1 = 0;
	echo "<P><B>HTTP Server Block</B><BR>";
        while (isset($http_server[++$loop1])) { /* NOTE: must use *pre*incrempent not post */
                echo "<BR>http server [$loop1] [listen] = "        . $http_server[$loop1]['listen'];
		echo "<BR>";
		foreach ($http_server[$loop1]['location'] as $key => $value) {
			echo "key: " . " => " . $key . "<BR>";
			echo "name: " . " => " . $value['name'] . "<BR>";
			echo "proxy_pass:" . " => " . $value['proxy_pass'] . "<BR>";
		}
		
	}
	

	echo "<HR> </FONT>";

}

function write_config($level="0", $delete_virt="", $delete_item="", $delete_service="") {
	global $ngx_fd;
	global $main;
	global $http;
	global $http_server;
	global $upstream;
	global $debug;
	
	$old_debug=$debug;

	if ($debug) { echo "<BR>Delete array selected_host = $delete_virt selected = $delete_item from level = $level delete_service = $delete_service<BR>"; }

	//too many loop variable :), two is engough
	$loop1 = 1;
	$loop2 = 1;

	$gap1 = "    ";
	$gap2 = $gap1 . $gap1;
	$gap3 = $gap1 . $gap1 . $gap1;
	$gap4 = $gap1 . $gap1 . $gap1 . $gap1;
	$egap1 = "&nbsp;&nbsp;&nbsp;&nbsp;";
	$egap2 = $egap1 . $egap1;
	$egap3 = $egap1 . $egap1 . $egap1;
	$egap4 = $egap1 . $egap1 . $egap1 . $egap1;
	
	if ($debug) { echo "<HR><B>Writing Config</B><HR><P><B>Primary</B><BR>"; };

	if ($main['serial_no'] != "" ) {
		// Basically try and not update the serial number unless the query string appears to have
		// data in it, for this we use '&'. It's not absolutely bulletproof, however it does for
		// our purposes
		if (isset($_SERVER['QUERY_STRING']) && strstr($_SERVER['QUERY_STRING'], '&' ) ) {
			fputs ($ngx_fd, "serial_no "			. (1 + $main['serial_no'])		. "\n", 80);
			if ($debug) { echo "serial_no "		. (1 + $main['serial_no'])		. "<BR>"; };
		} else {
			fputs ($ngx_fd, "serial_no "			. $main['serial_no']			. "\n", 80);
			if ($debug) { echo "serial_no "		. $main['serial_no']			. "<BR>"; };		
		};
	} else {
		fputs ($ngx_fd, "serial_no 1\n");
		if ($debug) { echo "serial_no 1<BR>"; };
	}

	//hard code the worker processes and  cpu affinity here
	fputs ($ngx_fd, "worker_processes "		. '4' . ";\n", 80);
	if ($debug) { echo "worker_processes "		. '4' . ";<BR>"; };		
	fputs ($ngx_fd, "worker_priority "		. '-1' . ";\n", 80);
	if ($debug) { echo "worker_priority "		. '-1' . ";<BR>"; };		
	fputs ($ngx_fd, "worker_cpu_affinity "		. '0001 0010 0100 1000' . ";\n", 80);
	if ($debug) { echo "worker_cpu_affinity "	. '0001 0010 0100 1000' . ";<BR>"; };		
/*
	if (isset($main['worker_processes'])
              && $main['worker_processes'] != "") {
		fputs ($ngx_fd, "worker_processes "		. '4' . ";\n", 80);
		if ($debug) { echo "worker_processes "		. '4' . ";<BR>"; };		
		fputs ($ngx_fd, "worker_cpu_affinity "		. '0001 0010 0100 1000' . ";\n", 80);
		if ($debug) { echo "worker_cpu_affinity "	. '0001 0010 0100 1000' . ";<BR>"; };		
	}
*/

	if (isset($main['error_log'])
              && $main['error_log'] != "") {
		fputs ($ngx_fd, "error_log "		. $main['error_log'] . ";\n", 80);
		if ($debug) { echo "error_log "	. $main['error_log'] . ";<BR>"; };		
	}
	if (isset($main['pid'])
              && $main['pid'] != "") {
		fputs ($ngx_fd, "pid "		. $main['pid'] . "\n", 80);
		if ($debug) { echo "pid "	. $main['pid'] . "<BR>"; };		
	}

	if (isset($http)) {
		fputs ($ngx_fd, "http "				. $http['http'] 	. " {\n", 80);
		if ($debug) { echo "http "			. $http['http'] 	. " {<BR>"; };

		while ( isset($upstream[$loop1]['name']) && $upstream[$loop1]['name'] != "") {

			if (($loop1 == $delete_item) && ($level == "2") && ($delete_service == "upstream")) { 
				$loop1++; $loop2=0; 
			} else {

				if (isset($upstream[$loop1]['name']) &&
		    			$upstream[$loop1]['name'] != "") { 


					fputs ($ngx_fd, "$gap1 upstream " . $upstream[$loop1]['name']	.  " {\n", 80);
					if ($debug) { echo "$egap1 upstream " . $upstream[$loop1]['name'] . " {<BR>"; };

/*
  lb session_sticky have long paramemter options, use strlen+10 and print extra newline to resolve
  issue that end of options get cut out and appended with the next line entry, weird, no idea with the cause. 
*/
					if (isset($upstream[$loop1]['lb']) 
						&& $upstream[$loop1]['lb'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 " . $upstream[$loop1]['lb'] . ";\n", strlen($upstream[$loop1]['lb'])+10);
                                               	if ($debug) { echo "$egap2 " . $upstream[$loop1]['lb'] . ";<BR>"; };
					}
                                        fputs ($ngx_fd, "$gap2 " .  "\n", 80);

/* 
   use host or host:port as key of array and delete server entry if key matches, 
   using index number in array cause weird bug either unable to remove server entry
   or server entry were added automatically from GUI.  
*/
                			foreach ($upstream[$loop1]['server'] as $key => $value) {
						if (($key == $delete_item)
                                                                && ($loop1 == $delete_virt) 
                                                                && ($level == "2")      
                                                                && ($delete_service == "upstream_server"))
                                                        continue;

                                              	fputs ($ngx_fd, "$gap2 server " . $value . ";\n", 80);
                                               	if ($debug) { echo "$egap2 server " . $value . ";<BR>"; };
                			}

					if (isset($upstream[$loop1]['check']) 
						&& $upstream[$loop1]['check'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 check " . $upstream[$loop1]['check'] . ";\n", 80);
                                               	if ($debug) { echo "$egap2 check " . $upstream[$loop1]['check'] . ";<BR>"; };
					}
					if (isset($upstream[$loop1]['check_keepalive_requests']) 
						&& $upstream[$loop1]['check_keepalive_requests'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 check_keepalive_requests " . $upstream[$loop1]['check_keepalive_requests'] . ";\n", 80);
                                               	if ($debug) { echo "$egap2 check_keepalive_requests " . $upstream[$loop1]['check_keepalive_requests'] . ";<BR>"; };
					}
					if (isset($upstream[$loop1]['check_http_send']) 
						&& $upstream[$loop1]['check_http_send'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 check_http_send " . $upstream[$loop1]['check_http_send'] . ";\n", strlen($upstream[$loop1]['check_http_send'])+30);
                                               	if ($debug) { echo "$egap2 check_http_send " . $upstream[$loop1]['check_http_send'] . ";<BR>"; };
					}

                                        fputs ($ngx_fd, "$gap2 " .  "\n", 80);

					if (isset($upstream[$loop1]['check_http_expect_alive']) 
						&& $upstream[$loop1]['check_http_expect_alive'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 check_http_expect_alive " . $upstream[$loop1]['check_http_expect_alive'] . ";\n", 80);
                                               	if ($debug) { echo "$egap2 check_http_expect_alive" . $upstream[$loop1]['check_http_expect_alive'] . ";<BR>"; };
					}
					if (isset($upstream[$loop1]['check_http_expect']) 
						&& $upstream[$loop1]['check_http_expect'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 check_http_expect " . $upstream[$loop1]['check_http_expect'] . ";\n", 80);
                                               	if ($debug) { echo "$egap2 check_http_expect " . $upstream[$loop1]['check_http_expect'] . ";<BR>"; };
					}


					fputs ($ngx_fd,"$gap1 }\n", 80);
					if ($debug) { echo "$egap1 }<BR>"; }
				}
			
				$loop1++;
				$loop2=0;
			}
		} //end upstream loop
		
		$loop1 = 1;

		while ( isset($http_server[$loop1])) {
                        if (($loop1 == $delete_item) && ($level == "2") && ($delete_service == "http_server")) {
                                $loop1++; 
                        } else {
				if (isset($http_server[$loop1])) { 

					fputs ($ngx_fd, "$gap1 server " .  " {\n", 80);
					if ($debug) { echo "$egap1 server " . " {<BR>"; };

					if (isset($http_server[$loop1]['listen']) 
						&& $http_server[$loop1]['listen'] != "") { 
                                               	fputs ($ngx_fd, "$gap2 listen " . $http_server[$loop1]['listen'] . ";\n", 80);
                                               	if ($debug) { echo "$egap2 listen " . $http_server[$loop1]['listen'] . ";<BR>"; };
					}

                			foreach ($http_server[$loop1]['location'] as $key => $value) {
						if (($key == $delete_item)
                                                                && ($loop1 == $delete_virt) 
                                                                && ($level == "3")      
                                                                && ($delete_service == "http_server_location"))
                                                        continue;

                                              	fputs ($ngx_fd, "$gap2 location " . $value['name'] . " {\n", 80);
                                               	if ($debug) { echo "$egap2 location " . $value['name'] . " {<BR>"; };

						if (isset($value['proxy_pass']) 
							&& $value['proxy_pass'] != "") { 
                                               		fputs ($ngx_fd, "$gap3 proxy_pass " . $value['proxy_pass'] . ";\n", 80);
                                               		if ($debug) { echo "$egap3 proxy_pass " . $value['proxy_pass'] . ";<BR>"; };
						}

                                              	fputs ($ngx_fd, "$gap2 }\n", 80);
                                               	if ($debug) { echo "$egap2 }<BR>"; };
                			}

				        fputs ($ngx_fd,"$gap1 }\n", 80);
                                       	if ($debug) { echo "$egap1 }<BR>"; }
				}
				$loop1++;
			}
		} //end http server block

		fputs ($ngx_fd,"}\n", 80);
		if ($debug) { echo "}<BR>"; };
	}


	fclose($ngx_fd);
	backup_lvs();
	if ($debug) { echo "<HR>"; }
	$debug=$old_debug;
}

function open_file($mode) {
        global $ngx_fd;
	global $TENGINE;
	global $debug;

        $ngx_fd = @fopen($TENGINE, $mode);
	if ($ngx_fd == false) {
		include ("lvserror.php");
		exit;
	}
		
        rewind($ngx_fd); /* unnessecary but I'm paranoid */
}


function add_upstream() {

	global $upstream;
	$loop2 = 1;	

	/* find end of existing data */
	while (isset($upstream[$loop2]['name']) &&
	       $upstream[$loop2]['name'] != "") {
		$loop2++;
	}

	$upstream[$loop2]['name']	= "[name]";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_http_server() {

	global $http_server;
	$loop2 = 1;	

	/* find end of existing data */
	while (isset($http_server[$loop2]['listen']) &&
	       $http_server[$loop2]['listen'] != "") {
		$loop2++;
	}

	$http_server[$loop2]['listen']	= "host:port";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_http_server_location($http_server_idx) {

	global $http_server;
	$http_server[$http_server_idx]['location']['/location']['name'] = "/location";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}


function add_http_upstream_server($ups_idx) {

	global $upstream;
	$upstream[$ups_idx]['server']['host:port'] = "host:port";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}


function CIDRtoMask($int) {
	return long2ip(-1 << (32 - (int)$int));
}

function Mask2CIDR($mask){
  $long = ip2long($mask);
  $base = ip2long('255.255.255.255');
  return 32-log(($long ^ $base)+1,2);

  /* xor-ing will give you the inverse mask,
      log base 2 of that +1 will return the number
      of bits that are off in the mask and subtracting
      from 32 gets you the cidr notation */

}




/* -- Main action (open the config file and initialize a set of arrays with the config ------- */
open_file("r+"); /* uses global file descriptor */

read_config();
fclose($ngx_fd);

if ($debug) { print_arrays(); };

?>
