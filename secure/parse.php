<?php

$LVS	=	"/etc/sysconfig/ha/web/lvs.cf";	/* Global */
//$LVS	=	"/etc/sysconfig/ha/keepalived.conf";	/* Global */

/* 1 = debuging, 0 or undefined = no debuging */
//$debug=1;

/* Bah 8( ....
$serv = array ( "",
		array ( "",
			array (
				"server"	=> "",
				"address"	=> "",
				"active"	=> "",
				"nmask"		=> "",
				"heartbeat"	=> "",
				"port"		=> ""
				"weight"	=> ""
			),
		),
	);
*/

$global_defs = array (
		"global_defs" => "",
  	 	"notification_email" => "", 
		"notification_email_from" => "",
		"smtp_server" => "",
   		"smtp_connect_timeout" => "",
		"router_id" => "",

	);

$static_ipaddress = array ( "",
			array (
				"ip" => "",
				"mask" => "",
  	 			"dev" => "", 
			)
	);

$local_address_group = array ();

$virt = array ( "",
		array (
	//		"virtual_server"		=> "",
			"ip"				=> "",
			"port"				=> "",
			"delay_loop"			=> "",
			"lb_algo"			=> "",
			"lb_kind"			=> "",
			"syn_proxy"			=> "",
			"laddr_group_name"		=> "",
			"persistence_timeout"		=> "",
			"persistence_granularity"	=> "",
			"protocol"			=> "",
			"ha_suspend"			=> "",
			"virtualhost"			=> "",
			"quorum"			=> "",
			"hysteresis"			=> "",
			"quorum_up"			=> "",
			"quorum_down"			=> "",
			"est_timeout"			=> "",
			"sorry_server"			=> "",
		)
	);

$vrrp_instance = array ( "",
		array (
			"state"				=> "",
			"interface"			=> "",
			"dont_track_primary"		=> "",
			"track_interface"		=> "",
			"mcast_src_ip"			=> "",
			"lvs_sync_daemon_interface"	=> "",
			"garp_master_delay"		=> "",
			"virtual_router_id"		=> "",
			"priority"			=> "",
			"advert_int"			=> "",
			"authentication"		=> "",
			"virtual_ipaddress"		=> "",
			"virtual_routes"		=> "",
			"nopreempt"			=> "",
			"preempt_delay"			=> "",
			"debug"				=> "",
			"notify_master"			=> "",
			"notify_backup"			=> "",
			"notify_fault"			=> "",
			"notify"			=> "",
			"smtp_alert"			=> "",
		)
	);

$vrrp_sync_group = array ( "",
		array (
			"group"				=> "",
			"notify_master"			=> "",
			"notify_backup"			=> "",
			"notify_fault"			=> "",
			"notify"			=> "",
			"smtp_alert"			=> "",
		)
	);


$prim = array (
		"serial_no"			=> "",
		"primary"			=> "",
		"primary_private"		=> "",
		"primary_shared"		=> "",
		"rsh_command"			=> "",
		"backup_active"			=> "",
		"backup"			=> "",
		"backup_private"		=> "",
		"backup_shared"			=> "",
		"heartbeat"			=> "",
		"heartbeat_port"		=> "",
		"keepalive"			=> "",
		"network"			=> "",
		"nat_router"			=> "",
		"nat_nmask"			=> "",
		"service"			=> "",
		"deadtime"			=> "",
		"reservation_conflict_action"	=> "",
		"debug_level"			=> "",
		"monitor_links"			=> "",
		"syncdaemon"			=> "",
		"syncd_iface"			=> "",
		"syncd_id"			=> "",
		"tcp_timeout"			=> "",
		"tcpfin_timeout"		=> "",
		"udp_timeout"			=> "",
	);

$serv = array ( );


/* Global file descriptor for use as a pointer to the lvs.cf file */
$fd = 0;
$service = "lvs";
$monitor_service="";
$ip_of="";

if (empty($debug)) { $debug = 0; } /* if unset, leave debugging off */

$buffer = "";

function parse($name, $datum) {
	global $debug;
	global $buffer;
	global $fd;
	global $prim;
	global $virt;
	global $vrrp_instance;
	global $vrrp_sync_group;
	global $serv;
	global $service;
	global $monitor_service;
	global $global_defs;
	global $static_ipaddress;
	global $local_address_group;
	global $ip_of;
	global $is_track_interface;
	global $is_group;

	static $email_regex = '[\w\-]+\@[\w\-]+\.[\w\-]+';
	static $ipmask_regex = '\d+\.\d+\.\d+\.\d+\/\d+';
	static $ip_regex = '\d+\.\d+\.\d+\.\d+';
	static $interface_regex = 'eth*'; //may adjust to other interface naming
	static $sync_group_regex = '\w*'; //vrrp sync group name
	static $laddrgname;
	static $level = 0 ;
	static $server_count = 0;
	static $virt_count = 0;
	static $ip_count = 0;
	static $vrrp_instance_count = 0;
	static $vrrp_sync_group_count = 0;
	

	if ($debug) {
		if (!empty($buffer)) {
			echo "<FONT COLOR=\"white\">Level $level &nbsp;&nbsp;&nbsp;&nbsp; buffer $buffer name $name datum $datum</FONT><BR>";
		};
	};

	if (strstr($buffer,"{")) { 
		if ($name == "global_defs"
		    or $name == "notification_email"
		    or $name == "static_ipaddress"
		    or $name == "authentication"
		    or $name == "virtual_ipaddress"
		    or $name == "virtual_routes"
		    or $name == "track_interface"
		    or $name == "group"
		    or $name == "TCP_CHECK"
		    or $name == "HTTP_GET"
		    or $name == "SSL_GET"
		    or $name == "SMTP_CHECK"
		    or $name == "MISC_CHECK"
		    or $name == "url"
		    or $name == "host"
		) {
			$datum = "";
		}
		$buffer = "$name $datum";
		if ($debug) { echo "<FONT COLOR=\"GOLD\">Striping the \"{\". Level changed up. Calling parse() with name $name datum $datum. <BR></FONT>"; };
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
		if (($level >  1) && ($name == "real_server")) { $server_count++ ; }; 
//		if ($level >  1) { $server_count++ ; }; 

		parse($name, $datum);
		return; /* <--- HIGHLY IMPORTANT! do **NOT** remove this VITAL command */
	 };

	if (strstr($buffer,"}")) {
		$name = "";
		$datum = "";
		$buffer = "$name $datum";
		if ($debug) { echo "<FONT COLOR=\"RED\">Striping the \"}\". Level changed down. Calling parse(). <BR></FONT>"; };
		$level--;
		parse($name, $datum);
		return; /* <--- HIGHLY IMPORTANT! do **NOT** remove this VITAL command */
	};

	/* Level 0 */
	if ($level == 0) {
		switch ($name) {
		
			case "serial_no"			:	$prim['serial_no']			= $datum;
									break;

			case "global_defs"			:	/* global definitition */
									$service="global_defs"; echo $service;
									break;
			case "static_ipaddress"			:	/* static ip definitition */
									$service="static_ipaddress"; echo $service;
									break;
			case "local_address_group"			:/* local address group definitition */
									$service="local_address_group"; echo $service;
									break;
			case "virtual_server"				:	/* new virtual server definitition */
									$service="lvs"; echo $service;
									break;
/*
			case "vrrp_instance"				: $service="vrrp_instance"; echo $service;
									break;
			case "vrrp_sync_group"				: $service="vrrp_sync_group"; echo $service;
									break;
*/
			case "monitor_links"			:	$prim['monitor_links']			= $datum;
									break;

			case "syncdaemon"			:	$prim['syncdaemon']			= $datum;
									break;

			case "syncd_iface"			:	$prim['syncd_iface']                    = $datum;
                                                                        break;

			case "syncd_id"				:	$prim['syncd_id']			= $datum;
                                                                        break;

			case ""					:	break;
			default					:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level $level - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
									break;
		}
	}
	
	/* Level 1 */
	if ($level == 1) {
		switch ($name) {
			case "global_defs"		: 	$service = "global_defs";
						  		if ($service == "global_defs") $global_defs['global_defs']	= $datum;
								if ($debug) { echo "<FONT COLOR=\"yellow\"><I>start of global definition </I><B>$service</B></FONT><BR>"; };
								break;
			case "notification_email_from"	:	if ($service == "global_defs") $global_defs['notification_email_from'] 	= $datum;
								break;
			case "smtp_server"		:	if ($service == "global_defs") $global_defs['smtp_server'] 	= $datum;
								break;
			case "smtp_connect_timeout"	:	if ($service == "global_defs") $global_defs['smtp_connect_timeout'] 	= $datum;
								break;
			case "router_id"		:	if ($service == "global_defs") $global_defs['router_id'] 	= $datum;
								break;

			case "static_ipaddress"		: 	$service = "static_ipaddress";
						  		if ($service == "static_ipaddress") $static_ipaddress['static_ipaddress']	= $datum;
								if ($debug) { echo "<FONT COLOR=\"yellow\"><I>start of static ip address definition </I><B>$service</B></FONT><BR>"; };
								break;

			case (preg_match("/$ipmask_regex/", $name) ? true : false )	:	
				if ($name != "" ) { //http://stackoverflow.com/questions/4043741/regexp-in-switch-statement
						    //This only works when $name evaluates to true. If $name == '' this will yield wrong results. -1 
					$ip_count++;
					$service = "static_ipaddress";
					if ($debug) { 
						echo "<FONT COLOR=\"yellow\"><I>Asked for static ipaddress </I><B>\$static_ipaddress[$ip_count]</B></FONT><BR>"; 
					};
					if($service == "static_ipaddress") {
						$ipmask = explode('/', $name);
						$ip = $ipmask[0];
						$mask = $ipmask[1];
						$deveth = explode(" ", $datum);
						$dev = $deveth[1];
						$static_ipaddress[$ip_count]['ip']       = $ip;
						$static_ipaddress[$ip_count]['mask']       = $mask;
						$static_ipaddress[$ip_count]['dev']       = $dev;
					}
				}
				break;

			case "local_address_group"		: 	$service = "local_address_group";
						  		if ($service == "local_address_group") {
									$laddrgname = $datum;
									$local_address_group[$laddrgname]	= array();
								}
								if ($debug) { echo "<FONT COLOR=\"yellow\"><I>start of local address group definition </I><B>$service</B></FONT><BR>"; };
								break;

			case (preg_match("/$ip_regex/", $name) ? true : false )	:	
				if ($name != "" ) { 
					$service = "local_address_group";
					$local_address_group[$laddrgname][] = $name;
					if ($debug) { 
						echo "<FONT COLOR=\"yellow\"><I>Asked for local address group</I><B>\$local_address_group[$laddrgname]</B></FONT><BR>"; 
					};
				}
				break;


			case "vrrp_instance"	:	$vrrp_instance_count++;
							$service="vrrp_instance";
							if ($debug) { echo "<FONT COLOR=\"yellow\"><I>Asked for failover service </I><B>\$vrrp_instance[$vrrp_instance_count]</B></FONT><BR>"; };
                                                        if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['vrrp_instance']     = $datum;

							break;
			case "state"			: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['state'] = $datum;
						  	break;
			case "interface"		: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['interface'] = $datum;
							break;
			case "dont_track_primary"	: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['dont_track_primary'] = $datum;
							break;
			case "mcast_src_ip"		: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['mcast_src_ip']	= $datum;
							break;
			case "lvs_sync_daemon_interface" : if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['lvs_sync_daemon_interface'] = $datum;
								break;
			case "garp_master_delay"	: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['garp_master_delay'] = $datum;
							break;
			case "virtual_router_id"	: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['virtual_router_id'] = $datum;
							break;
			case "priority"			: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['priority'] = $datum;
							break;
			case "advert_int"		: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['advert_int'] = $datum;
							break;
			case "nopreempt"		: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['nopreempt'] = $datum;
							break;
			case "preempt_delay"		: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['preempt_delay'] = $datum;
							break;
			case "debug"			: if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['debug'] = $datum;
							break;
			case "notify_master"		: if ($service == "vrrp_instance") {
								$vrrp_instance[$vrrp_instance_count]['notify_master'] = $datum;
							  } else if ($service == "vrrp_sync_group") {
							  	$vrrp_sync_group[$vrrp_sync_group_count]['notify_master'] = $datum;	
							  }
							break;
			case "notify_backup"		: if ($service == "vrrp_instance") {
								$vrrp_instance[$vrrp_instance_count]['notify_backup'] = $datum;
							  } else if ($service == "vrrp_sync_group") {
								$vrrp_sync_group[$vrrp_sync_group_count]['notify_backup'] = $datum;
							  }
							break;
			case "notify"		: if ($service == "vrrp_instance") {
								$vrrp_instance[$vrrp_instance_count]['notify'] = $datum;
							  } else  if ($service == "vrrp_sync_group") {
								$vrrp_sync_group[$vrrp_sync_group_count]['notify'] = $datum;
							  }
							break;
			case "notify_fault"		: if ($service == "vrrp_instance") {
								$vrrp_instance[$vrrp_instance_count]['notify_fault'] = $datum;
							  } else  if ($service == "vrrp_sync_group") {
								$vrrp_sync_group[$vrrp_sync_group_count]['notify_fault'] = $datum;
							  }
							break;
			case "smtp_alert"		: if ($service == "vrrp_instance") {
								$vrrp_instance[$vrrp_instance_count]['smtp_alert'] = $datum;
							  } else if ($service == "vrrp_sync_group") {
								$vrrp_sync_group[$vrrp_sync_group_count]['smtp_alert'] = $datum;
							  }
							break;
			case "authentication"		: 
							break;
			case "virtual_ipaddress"	: /* ignore here for vrrp_instance */ 
							break;
			case "virtual_routes"	: /* ignore here for vrrp_instance */ 
							break;
			case "track_interface"	: /* ignore here for vrrp_instance */ 
							break;

			case "vrrp_sync_group"	:	$vrrp_sync_group_count++;
							$service="vrrp_sync_group";
							if ($debug) { echo "<FONT COLOR=\"yellow\"><I>Asked for failover service </I><B>\$vrrp_sync_group[$vrrp_sync_group_count]</B></FONT><BR>"; };
                                                        if ($service == "vrrp_sync_group") $vrrp_sync_group[$vrrp_sync_group_count]['vrrp_sync_group']     = $datum;

							break;

			case "group"		: 
							break;


			case "virtual_server"		:	$virt_count++;
							$service = "lvs";
							if ($debug) { echo "<FONT COLOR=\"yellow\"><I>Asked for virtual server service </I><B>\$virt[$virt_count]</B></FONT><BR>"; };
							if ($service == "lvs") {
								$virt_value = explode(" ", $datum);
								if ($virt_value[0] == "group") {
									$virt[$virt_count]['group'] = $virt_value[1];
									$virt[$virt_count]['ip'] = "";
									$virt[$virt_count]['port'] = "";
									$virt[$virt_count]['fwmark'] = "";
								} else if ($virt_value[0] == "fwmark") {
									$virt[$virt_count]['group'] = "";
									$virt[$virt_count]['ip'] = "";
									$virt[$virt_count]['port'] = "";
									$virt[$virt_count]['fwmark'] = $virt_value[1];
								} else {
									$virt[$virt_count]['group'] = "";
									$virt[$virt_count]['ip'] = $virt_value[0];
									$virt[$virt_count]['port'] = $virt_value[1];
									$virt[$virt_count]['fwmark'] = "";
								}
							}
							break;
			case "sorry_server"	:	if ($service == "lvs") $virt[$virt_count]['sorry_server']	= $datum;
							break;
			case "delay_loop"	:	if ($service == "lvs") $virt[$virt_count]['delay_loop']	= $datum;
							break;
			case "lb_algo"		:	if ($service == "lvs") $virt[$virt_count]['lb_algo']	= $datum;
							break;
			case "lb_kind"		:	if ($service == "lvs") $virt[$virt_count]['lb_kind']	= $datum;
							break;
			case "protocol"		:	if ($service == "lvs") $virt[$virt_count]['protocol']	= $datum;
							break;
			case "laddr_group_name"	:	if ($service == "lvs") $virt[$virt_count]['laddr_group_name']	= $datum;
							break;
			case "persistence_timeout"	:	if ($service == "lvs") $virt[$virt_count]['persistence_timeout']	= $datum;
							break;
			case "persistence_granularity"	:	if ($service == "lvs") $virt[$virt_count]['persistence_granularity']	= $datum;
							break;
			case "ha_suspend"	:	if ($service == "lvs") $virt[$virt_count]['ha_suspend']	= $datum;
							break;
			case "virtualhost"	:	if ($service == "lvs") $virt[$virt_count]['virtualhost']	= $datum;
							break;							
			case "quorum"	:	if ($service == "lvs") $virt[$virt_count]['quorum']	= $datum;
							break;							
			case "hysteresis"	:	if ($service == "lvs") $virt[$virt_count]['hysteresis']	= $datum;
							break;							
			case "quorum_up"	:	if ($service == "lvs") $virt[$virt_count]['quorum_up']	= $datum;
							break;							
			case "quorum_down"	:	if ($service == "lvs") $virt[$virt_count]['quorum_down']	= $datum;
							break;							
			case "est_timeout"	:	if ($service == "lvs") $virt[$virt_count]['est_timeout']	= $datum;
							break;							
			case "real_server"		:	/* ignored (compatibility) */
							break;
			case ""			:	break;

			default			:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level 1 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
							break;
		}
	}

	/* Level 2 */
	if ($level == 2 ) {
		switch ($name) {
	
			case "real_server"		:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]</B></FONT><BR>"; };
							$ipport = explode(" ", $datum);
							$serv[$virt_count][$server_count+1]['ip']		= $ipport[0];
							$serv[$virt_count][$server_count+1]['port']		= $ipport[1];
							break;
			case "notify_up"		:	$serv[$virt_count][$server_count+1]['notify_up']		= $datum;
							break;
			case "notify_down"		:	$serv[$virt_count][$server_count+1]['notify_down']		= $datum;
							break;
//			case "active"		:	$serv[$virt_count][$server_count+1]['active']		= $datum;
							break;
			case "weight"		:	$serv[$virt_count][$server_count+1]['weight']		= $datum;
							break;

			case (preg_match("/($email_regex)/", $name) ? true : false )	:	
				if ($debug) {
                                       echo "<FONT COLOR=\"yellow\"><I>Asked for global_defs.notification_email "
                                        . 
                                        "</I> - <B>\$global_defs[notification_email]:"
                                        . $name .
                                        "</B></FONT><BR>"; };
                                       $global_defs['notification_email']       = $global_defs['notification_email'] .  " $name";
                                                        break;

			case "authentication"		:  if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['authentication'] = $name; 
							break;
			case "auth_type"		:  if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['auth_type'] = $datum; 
							break;
			case "auth_pass"		:  if ($service == "vrrp_instance") $vrrp_instance[$vrrp_instance_count]['auth_pass'] = $datum; 
							break;
			case "virtual_ipaddress"	:  if ($service == "vrrp_instance") { 
								$ip_of = "virtual_ipaddress";
								$vrrp_instance[$vrrp_instance_count]['virtual_ipaddress'] = array();
							   }
							break;

			case "virtual_routes"	:  if ($service == "vrrp_instance") { 
								$ip_of = "virtual_routes";
								$vrrp_instance[$vrrp_instance_count]['virtual_routes'] = array(); 
						    }
							break;

			case (preg_match("/$ipmask_regex/", $name) ? true : false )	:	
				if ($name != "" ) { //http://stackoverflow.com/questions/4043741/regexp-in-switch-statement
						    //This only works when $name evaluates to true. If $name == '' this will yield wrong results. -1 
					if ($debug) { 
						echo "<FONT COLOR=\"yellow\"><I>Asked for vrrp_instance ip address </I><B></B></FONT><BR>"; 
					};
					if($service == "vrrp_instance") {
					   if($ip_of == "virtual_ipaddress") {
						    $vrrp_instance[$vrrp_instance_count]['virtual_ipaddress'][] = $name . " " . $datum;
					   } else if ($ip_of == "virtual_routes") {
						    $vrrp_instance[$vrrp_instance_count]['virtual_routes'][] = $name . " " . $datum;
						    if ($debug) {
								echo "$name $datum<BR>";
						    }
					   }
					}
				}
				break;

			case "src"	:  if ($service == "vrrp_instance" and $ip_of == "virtual_routes")  
					   	$vrrp_instance[$vrrp_instance_count]['virtual_routes'][] = $name . " " . $datum;
				break;

			case "track_interface"	:  if ($service == "vrrp_instance") { 
								$is_track_interface = "track_interface";
								$vrrp_instance[$vrrp_instance_count]['track_interface'] = array(); 
						    }
							break;

			case (preg_match("/$interface_regex/", $name) ? true : false )	:	
				if ($name != "" ) {
					if ($debug) { 
						echo "<FONT COLOR=\"yellow\"><I>Asked for vrrp_instance track interface </I><B></B></FONT><BR>"; 
					};
					if(($service == "vrrp_instance") && ($is_track_interface == "track_interface")) {
						    $vrrp_instance[$vrrp_instance_count]['track_interface'][] = $name;
					 } 
				}
				break;

			case "group"	:  if ($service == "vrrp_sync_group") { 
								$is_group = "group";
								$vrrp_sync_group[$vrrp_sync_group_count]['group'] = array(); 
						    }
							break;

			case (preg_match("/$sync_group_regex/", $name) ? true : false )	:	
				if ($name != "" ) {
					if ($debug) { 
						echo "<FONT COLOR=\"yellow\"><I>Asked for vrrp_sync_group group </I><B></B></FONT><BR>"; 
					};
					if(($service == "vrrp_sync_group") && ($is_group == "group")) {
						    $vrrp_sync_group[$vrrp_sync_group_count]['group'][] = $name;
					 } 
				}
				break;
									
			case ""			:	break;
			default			:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level2 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
							break;
		}
	}

	/* Level 3 */
	if ($level == 3 ) {
		switch ($name) {
	
			case "TCP_CHECK"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "tcp_check";
							if ($monitor_service == "tcp_check") $serv[$virt_count][$server_count+1]['monitor']['type'] = $name;
							break;
			case "connect_port" :	if ($monitor_service == "tcp_check") {
							$serv[$virt_count][$server_count+1]['monitor']['tcp_connect_port'] = $datum;
					     	} 
				              	else if ($monitor_service == "http_get") {
							$serv[$virt_count][$server_count+1]['monitor']['http_connect_port'] = $datum;
					      	}
				              	else if ($monitor_service == "ssl_get") {
							$serv[$virt_count][$server_count+1]['monitor']['ssl_connect_port'] = $datum;
					      	}
				              	break;
			case "bindto"       :	if ($monitor_service == "tcp_check") {
							$serv[$virt_count][$server_count+1]['monitor']['tcp_bindto'] = $datum;
						}
						else if ($monitor_service == "http_get") {
							$serv[$virt_count][$server_count+1]['monitor']['http_bindto'] = $datum;
						}
						else if ($monitor_service == "ssl_get") {
							$serv[$virt_count][$server_count+1]['monitor']['ssl_bindto'] = $datum;
						}

							break;
			case "connect_timeout"	:	if ($monitor_service == "tcp_check") {
								$serv[$virt_count][$server_count+1]['monitor']['tcp_connect_timeout'] = $datum;
							}
							else if ($monitor_service == "http_get") {
								$serv[$virt_count][$server_count+1]['monitor']['http_connect_timeout'] = $datum;
							}
							else if ($monitor_service == "ssl_get") {
								$serv[$virt_count][$server_count+1]['monitor']['ssl_connect_timeout'] = $datum;
							}
							else if ($monitor_service == "smtp_check") {
								$serv[$virt_count][$server_count+1]['monitor']['smtp_connect_timeout'] = $datum;
							}
							break;


			case "HTTP_GET"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "http_get";
							if ($monitor_service == "http_get") $serv[$virt_count][$server_count+1]['monitor']['type'] = $name;
							break;

			case "SSL_GET"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "ssl_get";
							if ($monitor_service == "ssl_get") $serv[$virt_count][$server_count+1]['monitor']['type'] = $name;
							break;

			case "nb_get_retry"	:	if ($monitor_service == "http_get") {
								$serv[$virt_count][$server_count+1]['monitor']['http_nb_get_retry'] = $datum;
							} 
							else if ($monitor_service == "ssl_get") {
								$serv[$virt_count][$server_count+1]['monitor']['ssl_nb_get_retry'] = $datum;
							}
							break;
			case "delay_before_retry"	: if ($monitor_service == "http_get") {
								$serv[$virt_count][$server_count+1]['monitor']['http_delay_before_retry'] = $datum;
							   }
							   else if ($monitor_service == "ssl_get") {
								$serv[$virt_count][$server_count+1]['monitor']['ssl_delay_before_retry'] = $datum;
						           }
							   else if ($monitor_service == "smtp_check") {
								$serv[$virt_count][$server_count+1]['monitor']['smtp_delay_before_retry'] = $datum;
						           }
								
							break;

			case "SMTP_CHECK"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "smtp_check";
							if ($monitor_service == "smtp_check") $serv[$virt_count][$server_count+1]['monitor']['type'] = $name;
							break;

			case "retry"	:	if ($monitor_service == "smtp_check")	$serv[$virt_count][$server_count+1]['monitor']['retry'] = $datum;
						break;
			case "helo_name"	:	if ($monitor_service == "smtp_check")	$serv[$virt_count][$server_count+1]['monitor']['helo_name'] = $datum;
						break;

			case "MISC_CHECK"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "misc_check";
							if ($monitor_service == "misc_check") $serv[$virt_count][$server_count+1]['monitor']['type'] = $name;
							break;

			case "misc_path"	: if ($monitor_service == "misc_check")	$serv[$virt_count][$server_count+1]['monitor']['misc_path'] = $datum;
						  break;
			case "misc_timeout"	: if ($monitor_service == "misc_check")	$serv[$virt_count][$server_count+1]['monitor']['misc_timeout'] = $datum;
						  break;
			case "misc_dynamic"	: if ($monitor_service == "misc_check")	$serv[$virt_count][$server_count+1]['monitor']['misc_dynamic'] = 1;
						  break;

									
			case ""		:	break;
			default		:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level3 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
						break;
		}
	} // END LEVEL 3

	/* Level 4 */
	if ($level == 4 ) {
		switch ($name) {
	
			case "url"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							//$monitor_service = "http_get";

			case "path"	: if ($monitor_service == "http_get") {
						$serv[$virt_count][$server_count+1]['monitor']['http_path'] = $datum;
					  }
					  else if ($monitor_service == "ssl_get") {
						$serv[$virt_count][$server_count+1]['monitor']['ssl_path'] = $datum;
					  }
					  break;

			case "digest"	: if ($monitor_service == "http_get") {	
						$serv[$virt_count][$server_count+1]['monitor']['http_digest'] = $datum;
					  }
					  else if ($monitor_service == "ssl_get") {
						$serv[$virt_count][$server_count+1]['monitor']['ssl_digest'] = $datum;
					  }
				          break;

			case "status_code"	: if ($monitor_service == "http_get") {	
						$serv[$virt_count][$server_count+1]['monitor']['http_status_code'] = $datum;
					  }
					  else if ($monitor_service == "ssl_get") {
						$serv[$virt_count][$server_count+1]['monitor']['ssl_status_code'] = $datum;
					  }
				          break;

			case "host"	:	if ($debug) { 
							echo "<FONT COLOR=\"yellow\"><I>Asked for vitual.server (" 
									. ($server_count+1) . 
									")</I> - <B>\$serv[$virt_count]:["
									. ($server_count+1) . 
									"]" . "[" . $name . "]" . "</B></FONT><BR>"; };
							$monitor_service = "smtp_check";

			case "connect_ip"	: if ($monitor_service == "smtp_check")	$serv[$virt_count][$server_count+1]['monitor']['connect_ip'] = $datum;
					  break;

			case "connect_port"	: if ($monitor_service == "smtp_check")	$serv[$virt_count][$server_count+1]['monitor']['smtp_connect_port'] = $datum;
					  break;
			case "bindto"	: if ($monitor_service == "smtp_check")	$serv[$virt_count][$server_count+1]['monitor']['smtp_bindto'] = $datum;
					  break;

			case ""		:	break;
			default		:	if ($debug) { echo "<FONT COLOR=\"BLUE\">Level4 - garbage [$name] (ignored line [$buffer])</FONT><BR>"; }
						break;
		}
	} // END LEVLE 4

}

function next_line() {
	global $fd;
	global $buffer;
	global $debug;
	global $test;

	while (!feof($fd)) {
		$buffer = fgets($fd, 4096);
		if ($debug) { echo "Buffer = [$buffer]<BR>"; }

		/* All data is comprised of a name, an optional seperator and a datum */

		/* oh wow!.. trim()!!! I could hug somebody! */
		$buffer = trim($buffer);

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
	global $fd;
	global $buffer;
	global $name;
	global $datum;
	global $debug;


	while (!feof($fd)) {
		$buffer = fgets($fd, 4096);
		if ($debug) {
			echo "Buffer = [$buffer]<BR>";
		}

		/* all data is comprised of a name, an optional seperator, and a datum */

		/* oh wow!.. trim()!!! I could hug somebody! */
		$buffer = trim($buffer);

		//BUG!!! strlen > 4 condition check cause vrrp track_interface like eth1 cause parsing write failure
		if (strlen ($buffer) >= 2) { /* basically if not empty,.. however if (!empty($buffer) didn't work */
			/* explode! oh boy! */
			//$pieces = explode(" ", $buffer);
			//reference http://fr2.php.net/manual/en/function.preg-split.php#92632 for following regex
			if ( strstr($buffer,"notify_fault" )
			     or strstr($buffer,"misc_path" )
			     or strstr($buffer,"notify_master" )
			     or strstr($buffer,"notify_backup" )
			     or strstr($buffer,"notify" )
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
			}

*/
			$name = $pieces[0];
			if (strstr($buffer,"=")) {
				if (isset($pieces[2]))
					$datum = $pieces[2];
			}
			else if ( $pieces[0] == "src"
				  or $pieces[1] == "via"
				  or $pieces[1] == "gw" ) { //virtual_routes
			// http://stackoverflow.com/questions/3591867/how-to-get-the-last-n-items-in-a-php-array-as-another-array
				$datum = implode(" ", array_slice($pieces, -(count($pieces)-1)));
				
			}
			else if (isset($pieces[2]) and $pieces[1] == "dev") {
                                        $datum = $pieces[1] . " " . $pieces[2];
			}
			else if (isset($pieces[2]) and $pieces[0] == "virtual_server") {
                                        $datum = $pieces[1] . " " . $pieces[2];
			}
			else if (isset($pieces[2]) and $pieces[0] == "real_server") {
                                        $datum = $pieces[1] . " " . $pieces[2];
                        } else {
				$datum = $pieces[1];
					
			}

			//if (!empty($pieces[3])) { $datum = $pieces[2] . " " . $pieces[3] ; }
/*
			if (!empty($pieces[4]) ) { // must be a send or expect string 
				$datum = strstr($buffer, "\"");
				$test = $datum;
			}
*/

		}
		parse($name, $datum);
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
	global $LVS;
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
	global $prim;
	global $virt;
	global $vrrp_instance;
	global $vrrp_sync_group;
	global $serv;
	global $debug;
	global $global_defs;
	global $static_ipaddress;
	global $local_address_group;
	global $ip_of;

	$loop1 = $loop2 = 0;

	echo "<FONT COLOR=\"Gold\">";
	echo "<HR>DEBUG<HR>";
	echo "<B>Primary</B>";
	echo "<BR>serial_no = "					. $prim['serial_no'];

	echo "<P><B>Global_defs</B>";
	echo "<BR>Global_defs  [notification_email]  = "    	. $global_defs['notification_email'];
	echo "<BR>Global_defs  [notification_email_from] = "	. $global_defs['notification_email_from'];
	echo "<BR>Global_defs  [smtp_server] = "		. $global_defs['smtp_server'];
	echo "<BR>Global_defs  [smtp_connect_timeout] = "	. $global_defs['smtp_connect_timeout'];
	echo "<BR>Global_defs  [router_id] = "			. $global_defs['router_id'];
	
	echo "<P><B>Static_ipaddress</B>";
	while ($static_ipaddress[++$loop1]['ip'] != "" ) { /* NOTE: must use *pre*increment not post */
		echo "<BR>Static_ipaddress [$loop1] [ip] = "	. $static_ipaddress[$loop1]['ip'];
		echo "<BR>Static_ipaddress [$loop1] [mask] = "	. $static_ipaddress[$loop1]['mask'];
		echo "<BR>Static_ipaddress [$loop1] [dev] = "	. $static_ipaddress[$loop1]['dev'];
	}

	echo "<P><B>Local address group</B>";
	echo "<BR>" .  var_dump($local_address_group);

	$loop1 = $loop2 =  0;

	while ($vrrp_instance[++$loop1]['vrrp_instance'] != "" ) { /* NOTE: must use *pre*incrempent not post */
	echo "<P><B>vrrp_instance</B>";
		echo "<BR>vrrp_instance [$loop1] [vrrp_instance] = "	. $vrrp_instance[$loop1]['vrrp_instance'];
		echo "<BR>vrrp_instance [$loop1] [state] = "	. $vrrp_instance[$loop1]['state'];
		echo "<BR>vrrp_instance [$loop1] [interface] = "	. $vrrp_instance[$loop1]['interface'];
		echo "<BR>vrrp_instance [$loop1] [dont_track_primary] = "	. $vrrp_instance[$loop1]['dont_track_primary'];
		echo "<BR>vrrp_instance [$loop1] [mcast_src_ip] = "	. $vrrp_instance[$loop1]['mcast_src_ip'];
		echo "<BR>vrrp_instance [$loop1] [lvs_sync_daemon_interface] = "	. $vrrp_instance[$loop1]['lvs_sync_daemon_interface'];
		echo "<BR>vrrp_instance [$loop1] [garp_master_delay] = "	. $vrrp_instance[$loop1]['garp_master_delay'];
		echo "<BR>vrrp_instance [$loop1] [virtual_router_id] = "	. $vrrp_instance[$loop1]['virtual_router_id'];
		echo "<BR>vrrp_instance [$loop1] [priority] = "	. $vrrp_instance[$loop1]['priority'];
		echo "<BR>vrrp_instance [$loop1] [advert_int] = "	. $vrrp_instance[$loop1]['advert_int'];
		echo "<BR>vrrp_instance [$loop1] [nopreempt] = "	. $vrrp_instance[$loop1]['nopreempt'];
		echo "<BR>vrrp_instance [$loop1] [preempt_delay] = "	. $vrrp_instance[$loop1]['preempt_delay'];
		echo "<BR>vrrp_instance [$loop1] [debug] = "	. $vrrp_instance[$loop1]['debug'];
		echo "<BR>vrrp_instance [$loop1] [notify_master] = "	. $vrrp_instance[$loop1]['notify_master'];
		echo "<BR>vrrp_instance [$loop1] [notify_backup] = "	. $vrrp_instance[$loop1]['notify_backup'];
		echo "<BR>vrrp_instance [$loop1] [notify] = "	. $vrrp_instance[$loop1]['notify'];
		echo "<BR>vrrp_instance [$loop1] [notify_fault] = "	. $vrrp_instance[$loop1]['notify_fault'];
		echo "<BR>vrrp_instance [$loop1] [smtp_alert] = "	. $vrrp_instance[$loop1]['smtp_alert'];
		echo "<BR>vrrp_instance [$loop1] [auth_type] = "	. $vrrp_instance[$loop1]['auth_type'];
		echo "<BR>vrrp_instance [$loop1] [auth_pass] = "	. $vrrp_instance[$loop1]['auth_pass'];

		echo "<P><B>vrrp_instance virtual_ipaddress</B>";
		echo "<BR>" .  var_dump($vrrp_instance[$loop1]['virtual_ipaddress']);

		echo "<P><B>vrrp_instance virtual_routes</B>";
		echo "<BR>" .  var_dump($vrrp_instance[$loop1]['virtual_routes']);

                foreach ($vrrp_instance[$loop1]['virtual_ipaddress'] as $ip) {
				if ($debug) { echo "$egap1" . $ip . "<BR>"; };
		}
                foreach ($vrrp_instance[$loop1]['virtual_routes'] as $ip) {
				if ($debug) { echo "$egap1" . $ip . "<BR>"; };
		}

		echo "<P><B>vrrp_instance track_interface</B>";
		echo "<BR>" .  var_dump($vrrp_instance[$loop1]['track_interface']);

                foreach ($vrrp_instance[$loop1]['track_interface'] as $interface) {
				if ($debug) { echo "$egap1" . $interface . "<BR>"; };
		}

	}

	$loop1 = $loop2 =  0;

	while ($vrrp_sync_group[++$loop1]['vrrp_sync_group'] != "" ) { /* NOTE: must use *pre*incrempent not post */
	echo "<P><B>vrrp_sync_group</B>";
		echo "<BR>vrrp_sync_group [$loop1] [vrrp_sync_group] = "	. $vrrp_sync_group[$loop1]['vrrp_sync_group'];
		echo "<BR>vrrp_sync_group [$loop1] [notify_master] = "	. $vrrp_sync_group[$loop1]['notify_master'];
		echo "<BR>vrrp_sync_group [$loop1] [notify_backup] = "	. $vrrp_sync_group[$loop1]['notify_backup'];
		echo "<BR>vrrp_sync_group [$loop1] [notify] = "	. $vrrp_sync_group[$loop1]['notify'];
		echo "<BR>vrrp_sync_group [$loop1] [notify_fault] = "	. $vrrp_sync_group[$loop1]['notify_fault'];
		echo "<BR>vrrp_sync_group [$loop1] [smtp_alert] = "	. $vrrp_sync_group[$loop1]['smtp_alert'];


		echo "<P><B>vrrp_sync_group group</B>";
		echo "<BR>" .  var_dump($vrrp_sync_group[$loop1]['group']);

                foreach ($vrrp_sync_group[$loop1]['group'] as $group) {
				if ($debug) { echo "$egap1" . $group . "<BR>"; };
		}

	}
	
	
	
	$loop1 = $loop2 = 0;
	
while ($virt[++$loop1]['ip'] != "" ) { /* NOTE: must use *pre*increment not post */
		echo "<P><B>Virtual</B>";
		echo "<BR>Virtual [$loop1] [fwmark] "	. $virt[$loop1]['fwmark'];
		echo "<BR>Virtual [$loop1] [group] "	. $virt[$loop1]['group'];
		echo "<BR>Virtual [$loop1] [ip] "	. $virt[$loop1]['ip'];
		echo "<BR>Virtual [$loop1] [port] "	. $virt[$loop1]['port'];
	//	echo "<BR>Virtual [$loop1] [active] = "		. $virt[$loop1]['active'];
		echo "<BR>Virtual [$loop1] [delay_loop] "	. $virt[$loop1]['delay_loop'];
		echo "<BR>Virtual [$loop1] [lb_algo] "		. $virt[$loop1]['lb_algo'];
		echo "<BR>Virtual [$loop1] [lb_kind] "		. $virt[$loop1]['vip_nmask'];
		echo "<BR>Virtual [$loop1] [laddr_group_name] "		. $virt[$loop1]['laddr_group_name'];
		echo "<BR>Virtual [$loop1] [persistence_timeout] "	. $virt[$loop1]['persistence_timeout'];
		echo "<BR>Virtual [$loop1] [persistence_granularity] "	. $virt[$loop1]['persistence_granularity'];
		echo "<BR>Virtual [$loop1] [ha_suspend] "		. $virt[$loop1]['ha_suspend'];
		echo "<BR>Virtual [$loop1] [protocol] "	. $virt[$loop1]['protocol'];
		echo "<BR>Virtual [$loop1] [virtualhost] "	. $virt[$loop1]['virtualhost'];
		echo "<BR>Virtual [$loop1] [quorum] "		. $virt[$loop1]['quorum'];
		echo "<BR>Virtual [$loop1] [hysteresis] "		. $virt[$loop1]['hysteresis'];
		echo "<BR>Virtual [$loop1] [quorum_up] "		. $virt[$loop1]['quorum_up'];
		echo "<BR>Virtual [$loop1] [quorum_down] "	. $virt[$loop1]['quorum_down'];
		echo "<BR>Virtual [$loop1] [est_timeout] "	. $virt[$loop1]['est_timeout'];
		echo "<BR>Virtual [$loop1] [sorry_server] "	. $virt[$loop1]['sorry_server'];
		
		echo "<BR>";
	}

	$loop1 = 1; /* reuse loop1 */
	$loop2 = 1;

	echo "<P><B>Server</B>";
	while ( $serv[$loop1][$loop2]['ip'] != "" ) { 
		while ($serv[$loop1][$loop2]['ip'] != "") {
			echo "<BR>Server [$loop1]:[$loop2]['ip'] "	. $serv[$loop1][$loop2]['ip'];
			echo "<BR>Server [$loop1]:[$loop2]['port'] "	. $serv[$loop1][$loop2]['port'];
			echo "<BR>Server [$loop1]:[$loop2]['notify_up'] "	. $serv[$loop1][$loop2]['notify_up'];
			echo "<BR>Server [$loop1]:[$loop2]['notify_down'] = "		. $serv[$loop1][$loop2]['notify_down'];
//			echo "<BR>Server [$loop1]:[$loop2]['active'] = "	. $serv[$loop1][$loop2]['active'];
			echo "<BR>Server [$loop1]:[$loop2]['weight'] = "	. $serv[$loop1][$loop2]['weight'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['type'] = "	. $serv[$loop1][$loop2]['monitor']['type'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['tcp_connect_port'] = "	. $serv[$loop1][$loop2]['monitor']['tcp_connect_port'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_connect_port'] = "	. $serv[$loop1][$loop2]['monitor']['http_connect_port'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_connect_port'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_connect_port'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['tcp_bindto'] = "	. $serv[$loop1][$loop2]['monitor']['tcp_bindto'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_bindto'] = "	. $serv[$loop1][$loop2]['monitor']['http_bindto'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_bindto'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_bindto'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['tcp_connect_timeout'] = " . $serv[$loop1][$loop2]['monitor']['tcp_connect_timeout'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_connect_timeout'] = " . $serv[$loop1][$loop2]['monitor']['http_connect_timeout'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_connect_timeout'] = " . $serv[$loop1][$loop2]['monitor']['ssl_connect_timeout'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['smtp_connect_timeout'] = " . $serv[$loop1][$loop2]['monitor']['ssl_connect_timeout'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_nb_get_retry'] = "	. $serv[$loop1][$loop2]['monitor']['http_nb_get_retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_nb_get_retry'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_nb_get_retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_delay_before_retry'] = "	. $serv[$loop1][$loop2]['monitor']['http_delay_before_retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_delay_before_retry'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_delay_before_retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['smtp_delay_before_retry'] = "	. $serv[$loop1][$loop2]['monitor']['smtp_delay_before_retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_path'] = "	. $serv[$loop1][$loop2]['monitor']['http_path'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_path'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_path'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_digest'] = "	. $serv[$loop1][$loop2]['monitor']['http_digest'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_digest'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_digest'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['http_status_code'] = "	. $serv[$loop1][$loop2]['monitor']['http_status_code'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['ssl_status_code'] = "	. $serv[$loop1][$loop2]['monitor']['ssl_status_code'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['retry'] = "	. $serv[$loop1][$loop2]['monitor']['retry'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['helo_name'] = "	. $serv[$loop1][$loop2]['monitor']['helo_name'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['connect_ip'] = "	. $serv[$loop1][$loop2]['monitor']['connect_ip'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['smtp_connect_port'] = "	. $serv[$loop1][$loop2]['monitor']['smtp_connect_port'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['smtp_bindto'] = "	. $serv[$loop1][$loop2]['monitor']['smtp_bindto'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['misc_path'] = "	. $serv[$loop1][$loop2]['monitor']['misc_path'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['misc_timeout'] = "	. $serv[$loop1][$loop2]['monitor']['misc_timeout'];
			echo "<BR>Server [$loop1]:[$loop2]['monitor']['misc_dynamic'] = "	. $serv[$loop1][$loop2]['monitor']['misc_dynamic'];
			echo "<BR>";
			$loop2++;
		}
		$loop1++;
		$loop2 = 1;
	}
	echo "<HR> </FONT>";

}

function write_config($level="0", $delete_virt="", $delete_item="", $delete_service="") {
	global $fd;
	global $prim;
	global $virt;
	global $vrrp_instance;
	global $vrrp_sync_group;
	global $serv;
	global $debug;
	global $global_defs;
	global $static_ipaddress;
	global $local_address_group;
	global $ip_of;
	
	$old_debug=$debug;

	if ($debug) { echo "<BR>Delete array number = $delete_item from level = $level<BR>"; }

	$loop1 = $loop2 = 1;
	$loop3 = $loop4 = 1;
	$loop5 = $loop6 = 1;
	$loop7  = 1;
	$loop8  = 0; //vrrp virtual_ipaddress
	$loop9 = 0; //vrrp virtual_routes 
	$loop10 = 0; //vrrp track interface 
	$loop11  = 1; //vrrp sync group
	$loop12  = 0; //vrrp sync group group

	$gap1 = "    ";
	$gap2 = $gap1 . $gap1;
	$gap3 = $gap1 . $gap1 . $gap1;
	$gap4 = $gap1 . $gap1 . $gap1 . $gap1;
	$egap1 = "&nbsp;&nbsp;&nbsp;&nbsp;";
	$egap2 = $egap1 . $egap1;
	$egap3 = $egap1 . $egap1 . $egap1;
	$egap4 = $egap1 . $egap1 . $egap1 . $egap1;
	
	if ($debug) { echo "<HR><B>Writing Config</B><HR><P><B>Primary</B><BR>"; };

	if ($prim['serial_no'] != "" ) {
		// Basically try and not update the serial number unless the query string appears to have
		// data in it, for this we use '&'. It's not absolutely bulletproof, however it does for
		// our purposes
		if (isset($_SERVER['QUERY_STRING']) && strstr($_SERVER['QUERY_STRING'], '&' ) ) {
			fputs ($fd, "serial_no = "			. (1 + $prim['serial_no'])		. "\n", 80);
			if ($debug) { echo "serial_no = "		. (1 + $prim['serial_no'])		. "<BR>"; };
		} else {
			fputs ($fd, "serial_no = "			. $prim['serial_no']			. "\n", 80);
			if ($debug) { echo "serial_no = "		. $prim['serial_no']			. "<BR>"; };		
		};
	} else {
		fputs ($fd, "serial_no = 1\n");
		if ($debug) { echo "serial_no = 1<BR>"; };
	}
	
	if (isset($global_defs)) {
		fputs ($fd, "global_defs "				. $global_defs['global_defs'] 	. " {\n", 80);
		if ($debug) { echo "global_defs "			. $global_defs['global_defs'] 	. " {<BR>"; };
	}
	if (isset($global_defs['notification_email'])) {
		fputs ($fd, "$gap1 notification_email "		 	. " {\n", 80);
		if ($debug) { echo "$egap1 notification_email "	 	. " {<BR>"; };
	}

	if ($global_defs['notification_email'] != ""){
		$global_defs['notification_email'] = trim($global_defs['notification_email']);
		$email = explode(" ", $global_defs['notification_email']);
		$i = 0;
		while ($email[$i] != "") {
			fputs ($fd, "$gap2 $email[$i]"	. "\n", 80);
			if ($debug) { echo "$egap2 $email[$i]"	. "<BR>"; };
			$i++;
		}
	}
	
	fputs ($fd,"$gap1 }\n", 80);
	if ($debug) { echo "$egap1 }<BR>"; };

	if ($global_defs['notification_email_from'] != ""){
		fputs ($fd, "$gap1 notification_email_from "			. $global_defs['notification_email_from']	. "\n", 80);
		if ($debug) { echo "$egap1 notification_email_from "		. $global_defs['notification_email_from']	. "<BR>"; };
	}
	if ($global_defs['smtp_server'] != ""){
		fputs ($fd, "$gap1 smtp_server "				. $global_defs['smtp_server']		. "\n", 80);
		if ($debug) { echo "$egap1 smtp_server "			. $global_defs['smtp_server']		. "<BR>"; };
	}
	if ($global_defs['smtp_connect_timeout'] != ""){
		fputs ($fd, "$gap1 smtp_connect_timeout "			. $global_defs['smtp_connect_timeout']	. "\n", 80);
		if ($debug) { echo "$egap1 smtp_connect_timeout "		. $global_defs['smtp_connect_timeout']	. "<BR>"; };
	}
	if ($global_defs['router_id'] != ""){
		fputs ($fd, "$gap1 router_id "				. $global_defs['router_id']		. "\n", 80);
		if ($debug) { echo "$egap1 router_id "				. $global_defs['router_id']		. "<BR>"; };
	}

	fputs ($fd,"}\n", 80);
	if ($debug) { echo "}<BR>"; };

	if ($debug) { echo "<P><B>Static IPADDRESS</B><BR>"; };

	if (isset($static_ipaddress)) {
		fputs ($fd, "static_ipaddress "				. " {\n", 80);
		if ($debug) { echo "static_ipaddress "			. " {<BR>"; };
	}


	while (isset($static_ipaddress[$loop5]['ip']) &&
	       $static_ipaddress[$loop5]['ip'] != "") { 
		
		if ((($loop5 == $delete_item ) && ($level == "1")) && ($delete_service == "ip")) {
			$loop5++;
		} else {

			if (isset($static_ipaddress[$loop5]['ip']) &&
			    $static_ipaddress[$loop5]['ip'] != "") {
				$ipstring = $static_ipaddress[$loop5]['ip']   . "/" . $static_ipaddress[$loop5]['mask'] . " " . "dev" . " " . $static_ipaddress[$loop5]['dev'];
				fputs ($fd, "$gap1" . $ipstring . "\n", 80);
				if ($debug) { echo "$egap1" . $ipstring . "<BR>"; };
			}
			$loop5++;
		}
	}

	fputs ($fd,"}\n", 80);
	if ($debug) { echo "}<BR>"; };


	if ($debug) { echo "<P><B>Local Address Group</B><BR>"; };

	foreach ($local_address_group as $laddrgname => $ips ) {
	    if(count($local_address_group[$laddrgname]) > 0 ) {
		fputs ($fd, "local_address_group $laddrgname"		. " " . "{\n", 80);
		if ($debug) { echo "local_address group $laddrgname"	. " " . "{<BR>"; };
                foreach ($ips as $ip) {
	                if ((($loop6 == $delete_item ) && ($level == "1")) && ($delete_service == "local_address_group")) {
                        	$loop6++;
				continue;
			}
                	else {

				fputs ($fd, "$gap1" . $ip . "\n", 80);
				if ($debug) { echo "$egap1" . $ip . "<BR>"; };
				$loop6++;
                	}
		}
		fputs ($fd,"}\n", 80);
		if ($debug) { echo "}<BR>"; };
           }
	}


	while ( $vrrp_instance[$loop7]['vrrp_instance'] != "" ) {
		if ((($loop7 == $delete_item ) && ($level == "1")) && ($delete_service == "vrrp_instance")) {  $loop7++; $loop8 = 0; $loop9 = 0; $loop10 = 0;} else {
			if ($debug) { echo "<P><B>vrrp_instance</B><BR>"; };	

			if (isset($vrrp_instance[$loop7]['vrrp_instance']) &&
			    $vrrp_instance[$loop7]['vrrp_instance'] != "") {
				fputs ($fd, "vrrp_instance "				. $vrrp_instance[$loop7]['vrrp_instance']	. " {\n", 80);
				if ($debug) { echo "vrrp_instance "			. $vrrp_instance[$loop7]['vrrp_instance']	. " {<BR>"; };
			}

			if (isset($vrrp_instance[$loop7]['state']) &&
			    $vrrp_instance[$loop7]['state'] != "") {
				fputs ($fd, "$gap1 state "			. $vrrp_instance[$loop7]['state']	. "\n", 80);
				if ($debug) { echo "$egap1 state "		. $vrrp_instance[$loop7]['state']	. "<BR>"; };
			}
			
			if (isset($vrrp_instance[$loop7]['interface']) &&
			    $vrrp_instance[$loop7]['interface'] != "") {
				fputs ($fd, "$gap1 interface "		. $vrrp_instance[$loop7]['interface']	. "\n", 80);
				if ($debug) { echo "$egap1 interface "	. $vrrp_instance[$loop7]['interface']	. "<BR>"; };
			}

			if (isset($vrrp_instance[$loop7]['dont_track_primary']) &&
			    $vrrp_instance[$loop7]['dont_track_primary'] != "") {
				fputs ($fd, "$gap1 dont_track_primary "		. $vrrp_instance[$loop7]['dont_track_primary']	. "\n", 80);
				if ($debug) { echo "$egap1 dont_track_primary "	. $vrrp_instance[$loop7]['dont_track_primary']	. "<BR>"; };
			}

			if (isset($vrrp_instance[$loop7]['mcast_src_ip']) &&
			    $vrrp_instance[$loop7]['mcast_src_ip'] != "") {
				fputs ($fd, "$gap1 mcast_src_ip "		. $vrrp_instance[$loop7]['mcast_src_ip']	. "\n", 80);
				if ($debug) { echo "$egap1 mcast_src_ip "	. $vrrp_instance[$loop7]['mcast_src_ip']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['lvs_sync_daemon_interface']) &&
			    $vrrp_instance[$loop7]['lvs_sync_daemon_interface'] != "") {
				fputs ($fd, "$gap1 lvs_sync_daemon_interface "		. $vrrp_instance[$loop7]['lvs_sync_daemon_interface']	. "\n", 80);
				if ($debug) { echo "$egap1 lvs_sync_daemon_interface "	. $vrrp_instance[$loop7]['lvs_sync_daemon_interface']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['garp_master_delay']) &&
			    $vrrp_instance[$loop7]['garp_master_delay'] != "") {
				fputs ($fd, "$gap1 garp_master_delay "		. $vrrp_instance[$loop7]['garp_master_delay']	. "\n", 80);
				if ($debug) { echo "$egap1 garp_master_delay "	. $vrrp_instance[$loop7]['garp_master_delay']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['virtual_router_id']) &&
			    $vrrp_instance[$loop7]['virtual_router_id'] != "") {
				fputs ($fd, "$gap1 virtual_router_id "		. $vrrp_instance[$loop7]['virtual_router_id']	. "\n", 80);
				if ($debug) { echo "$egap1 virtual_router_id "	. $vrrp_instance[$loop7]['virtual_router_id']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['priority']) &&
			    $vrrp_instance[$loop7]['priority'] != "") {
				fputs ($fd, "$gap1 priority "		. $vrrp_instance[$loop7]['priority']	. "\n", 80);
				if ($debug) { echo "$egap1 priority "	. $vrrp_instance[$loop7]['priority']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['advert_int']) &&
			    $vrrp_instance[$loop7]['advert_int'] != "") {
				fputs ($fd, "$gap1 advert_int "		. $vrrp_instance[$loop7]['advert_int']	. "\n", 80);
				if ($debug) { echo "$egap1 advert_int "	. $vrrp_instance[$loop7]['advert_int']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['nopreempt']) &&
			    $vrrp_instance[$loop7]['nopreempt'] != "") {
				fputs ($fd, "$gap1 nopreempt "		. $vrrp_instance[$loop7]['nopreempt']	. "\n", 80);
				if ($debug) { echo "$egap1 nopreempt "	. $vrrp_instance[$loop7]['nopreempt']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['preempt_delay']) &&
			    $vrrp_instance[$loop7]['preempt_delay'] != "") {
				fputs ($fd, "$gap1 preempt_delay "		. $vrrp_instance[$loop7]['preempt_delay']	. "\n", 80);
				if ($debug) { echo "$egap1 preempt_delay "	. $vrrp_instance[$loop7]['preempt_delay']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['debug']) &&
			    $vrrp_instance[$loop7]['debug'] != "") {
				fputs ($fd, "$gap1 debug "		. $vrrp_instance[$loop7]['debug']	. "\n", 80);
				if ($debug) { echo "$egap1 debug "	. $vrrp_instance[$loop7]['debug']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['notify_master']) &&
			    $vrrp_instance[$loop7]['notify_master'] != "") {
				fputs ($fd, "$gap1 notify_master "		. $vrrp_instance[$loop7]['notify_master']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_master "	. $vrrp_instance[$loop7]['notify_master']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['notify_backup']) &&
			    $vrrp_instance[$loop7]['notify_backup'] != "") {
				fputs ($fd, "$gap1 notify_backup "		. $vrrp_instance[$loop7]['notify_backup']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_backup "	. $vrrp_instance[$loop7]['notify_backup']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['notify_fault']) &&
			    $vrrp_instance[$loop7]['notify_fault'] != "") {
				fputs ($fd, "$gap1 notify_fault "		. $vrrp_instance[$loop7]['notify_fault']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_fault "	. $vrrp_instance[$loop7]['notify_fault']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['notify']) &&
			    $vrrp_instance[$loop7]['notify'] != "") {
				fputs ($fd, "$gap1 notify "		. $vrrp_instance[$loop7]['notify']	. "\n", 80);
				if ($debug) { echo "$egap1 notify "	. $vrrp_instance[$loop7]['notify']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['smtp_alert']) &&
			    $vrrp_instance[$loop7]['smtp_alert'] != "") {
				fputs ($fd, "$gap1 smtp_alert "		. $vrrp_instance[$loop7]['smtp_alert']	. "\n", 80);
				if ($debug) { echo "$egap1 smtp_alert "	. $vrrp_instance[$loop7]['smtp_alert']	. "<BR>"; };
			}
			if (isset($vrrp_instance[$loop7]['authentication']) &&
			    $vrrp_instance[$loop7]['authentication'] != "") {

				fputs ($fd, "$gap1 authentication "		. " {\n", 80);
				if ($debug) { echo "$egap1 authentication "	. " {<BR>"; };

				if (isset($vrrp_instance[$loop7]['auth_type']) &&
			    		$vrrp_instance[$loop7]['auth_type'] != "") {
					fputs ($fd, "$gap2 auth_type "		. $vrrp_instance[$loop7]['auth_type']	. "\n", 80);
					if ($debug) { echo "$egap2 auth_type "	. $vrrp_instance[$loop7]['auth_type']	. "<BR>"; };
				}
				if (isset($vrrp_instance[$loop7]['auth_pass']) &&
			    		$vrrp_instance[$loop7]['auth_pass'] != "") {
					fputs ($fd, "$gap2 auth_pass "		. $vrrp_instance[$loop7]['auth_pass']	. "\n", 80);
					if ($debug) { echo "$egap2 auth_pass "	. $vrrp_instance[$loop7]['auth_pass']	. "<BR>"; };
				}

				fputs ($fd,"$gap1 }\n", 80);
				if ($debug) { echo "$egap1 }<BR>"; }

			}

			if (isset($vrrp_instance[$loop7]['virtual_ipaddress']) &&
			    $vrrp_instance[$loop7]['virtual_ipaddress'] != ""  &&
			    count($vrrp_instance[$loop7]['virtual_ipaddress']) > 0) {
				fputs ($fd, "$gap1 virtual_ipaddress "		. " {\n", 80);
				if ($debug) { echo "$egap1 virtual_ipaddress "	. " {<BR>"; };

		                foreach ($vrrp_instance[$loop7]['virtual_ipaddress'] as $ip) {

                                	if (($loop8 == $delete_item) && ($loop7 == $delete_virt) && ($level == "2") && ($delete_service == "vrrp_virtual_ipaddress")) {
                                        	$loop8++;

                        		}
                        		else {
						fputs ($fd, "$gap2 "		. $ip	. "\n", 80);
						if ($debug) { echo "$egap2 "	. $ip	. "<BR>"; };
						$loop8++;
					}
                		}

				fputs ($fd,"$gap1 }\n", 80);
				if ($debug) { echo "$egap1 }<BR>"; }
			}

			if (isset($vrrp_instance[$loop7]['virtual_routes']) &&
			    $vrrp_instance[$loop7]['virtual_routes'] != ""  &&
			    count($vrrp_instance[$loop7]['virtual_routes']) > 0) {
				fputs ($fd, "$gap1 virtual_routes "		. " {\n", 80);
				if ($debug) { echo "$egap1 virtual_routes "	. " {<BR>"; };

		                foreach ($vrrp_instance[$loop7]['virtual_routes'] as $ip) {

                                	if (($loop9 == $delete_item) && ($loop7 == $delete_virt) && ($level == "2") && ($delete_service == "vrrp_virtual_routes")) {
                                        	$loop9++;

                        		}
                        		else {
						fputs ($fd, "$gap2 "		. $ip	. "\n", 80);
						if ($debug) { echo "$egap2 "	. $ip	. "<BR>"; };
						$loop9++;
					}
                		}

				fputs ($fd,"$gap1 }\n", 80);
				if ($debug) { echo "$egap1 }<BR>"; }
			}

			if (isset($vrrp_instance[$loop7]['track_interface'])
			    && $vrrp_instance[$loop7]['track_interface'] != ""
			    && count($vrrp_instance[$loop7]['track_interface']) > 0) {
				fputs ($fd, "$gap1 track_interface "		. " {\n", 80);
				if ($debug) { echo "$egap1 track_interface "	. " {<BR>"; };

		                foreach ($vrrp_instance[$loop7]['track_interface'] as $interface) {

                                	if (($loop10 == $delete_item) && ($loop7 == $delete_virt) && ($level == "2") && ($delete_service == "vrrp_track_interface")) {
                                        	$loop10++;

                        		}
                        		else {
						fputs ($fd, "$gap2 "		. $interface	. "\n", 80);
						if ($debug) { echo "$egap2 "	. $interface	. "<BR>"; };
						$loop10++;
					}
                		}

				fputs ($fd,"$gap1 }\n", 80);
				if ($debug) { echo "$egap1 }<BR>"; }
			}

			fputs ($fd,"}\n", 80);
			if ($debug) { echo "}<BR>"; }

			$loop7++;
			$loop8 = 0;
			$loop9 = 0;
			$loop10 = 0;
			
		}
	}

	while ( $vrrp_sync_group[$loop11]['vrrp_sync_group'] != "" ) {
		if ((($loop11 == $delete_item ) && ($level == "1")) && ($delete_service == "vrrp_sync_group")) {  $loop11++; $loop12 = 0;} else {
			if ($debug) { echo "<P><B>vrrp_sync_group</B><BR>"; };	

			if (isset($vrrp_sync_group[$loop11]['vrrp_sync_group']) &&
			    $vrrp_sync_group[$loop11]['vrrp_sync_group'] != "") {
				fputs ($fd, "vrrp_sync_group "				. $vrrp_sync_group[$loop11]['vrrp_sync_group']	. " {\n", 80);
				if ($debug) { echo "vrrp_sync_group "			. $vrrp_sync_group[$loop11]['vrrp_sync_group']	. " {<BR>"; };
			}

			if (isset($vrrp_sync_group[$loop11]['notify_master']) &&
			    $vrrp_sync_group[$loop11]['notify_master'] != "") {
				fputs ($fd, "$gap1 notify_master "		. $vrrp_sync_group[$loop11]['notify_master']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_master "	. $vrrp_sync_group[$loop11]['notify_master']	. "<BR>"; };
			}
			if (isset($vrrp_sync_group[$loop11]['notify_backup']) &&
			    $vrrp_sync_group[$loop11]['notify_backup'] != "") {
				fputs ($fd, "$gap1 notify_backup "		. $vrrp_sync_group[$loop11]['notify_backup']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_backup "	. $vrrp_sync_group[$loop11]['notify_backup']	. "<BR>"; };
			}
			if (isset($vrrp_sync_group[$loop11]['notify_fault']) &&
			    $vrrp_sync_group[$loop11]['notify_fault'] != "") {
				fputs ($fd, "$gap1 notify_fault "		. $vrrp_sync_group[$loop11]['notify_fault']	. "\n", 80);
				if ($debug) { echo "$egap1 notify_fault "	. $vrrp_sync_group[$loop11]['notify_fault']	. "<BR>"; };
			}
			if (isset($vrrp_sync_group[$loop11]['notify']) &&
			    $vrrp_sync_group[$loop11]['notify'] != "") {
				fputs ($fd, "$gap1 notify "		. $vrrp_sync_group[$loop11]['notify']	. "\n", 80);
				if ($debug) { echo "$egap1 notify "	. $vrrp_sync_group[$loop11]['notify']	. "<BR>"; };
			}
			if (isset($vrrp_sync_group[$loop11]['smtp_alert']) &&
			    $vrrp_sync_group[$loop11]['smtp_alert'] != "") {
				fputs ($fd, "$gap1 smtp_alert "		. $vrrp_sync_group[$loop11]['smtp_alert']	. "\n", 80);
				if ($debug) { echo "$egap1 smtp_alert "	. $vrrp_sync_group[$loop11]['smtp_alert']	. "<BR>"; };
			}


			if (isset($vrrp_sync_group[$loop11]['group'])
			    && $vrrp_sync_group[$loop11]['group'] != ""
			    && count($vrrp_sync_group[$loop11]['group']) > 0) {
				fputs ($fd, "$gap1 group "		. " {\n", 80);
				if ($debug) { echo "$egap1 group "	. " {<BR>"; };

		                foreach ($vrrp_sync_group[$loop11]['group'] as $group) {

                                	if (($loop12 == $delete_item) && ($loop11 == $delete_virt) && ($level == "2") && ($delete_service == "vrrp_sync_group_group")) {
                                        	$loop12++;

                        		}
                        		else {
						fputs ($fd, "$gap2 "		. $group	. "\n", 80);
						if ($debug) { echo "$egap2 "	. $group	. "<BR>"; };
						$loop12++;
					}
                		}

				fputs ($fd,"$gap1 }\n", 80);
				if ($debug) { echo "$egap1 }<BR>"; }
			}

			fputs ($fd,"}\n", 80);
			if ($debug) { echo "}<BR>"; }

			$loop11++;
			$loop12 = 0;
			
		}
	}
	
	while ( (isset($virt[$loop3]['ip']) or isset($virt[$loop3]['group']) or isset($virt[$loop3]['fwmark']) ) && 
		( $virt[$loop3]['ip'] != "" or $virt[$loop3]['group'] != "" or $virt[$loop3]['fwmark'] != "" ) ) { 
		
		if ((($loop3 == $delete_item ) && ($level == "1")) && ($delete_service == "virtual")) {
			$loop3++;
			$loop4 = 1;
		} else {
			if ($debug) { echo "<P><B>Virtual</B><BR>"; };

			if (isset($virt[$loop3]['ip']) && isset($virt[$loop3]['port'])
			    && $virt[$loop3]['ip'] != ""  && $virt[$loop3]['port'] != "") {
				fputs ($fd, "virtual_server "	. $virt[$loop3]['ip'] . " " . $virt[$loop3]['port'] . " {\n", 80);
				if ($debug) { echo "virtual_server " . $virt[$loop3]['ip'] . " " . $virt[$loop3]['port'] . " {<BR>"; };
			} 
			else if (isset($virt[$loop3]['group']) && $virt[$loop3]['group'] != "" ) {
				fputs ($fd, "virtual_server "	. "group" . " " . $virt[$loop3]['group'] . " {\n", 80);
				if ($debug) { echo "virtual_server " . "group" . " " . $virt[$loop3]['group'] . " {<BR>"; };
			} 
			else if (isset($virt[$loop3]['fwmark']) && $virt[$loop3]['fwmark'] != "" ) {
				fputs ($fd, "virtual_server "	. "fwmark" . " " . $virt[$loop3]['fwmark'] . " {\n", 80);
				if ($debug) { echo "virtual_server " . "fwmark" . " " . $virt[$loop3]['fwmark'] . " {<BR>"; };
			} 

			if (isset($virt[$loop3]['delay_loop']) &&
			    $virt[$loop3]['delay_loop'] != "") {
				fputs ($fd, "$gap1 delay_loop "			. $virt[$loop3]['delay_loop']	. "\n", 80);
				if ($debug) { echo "$egap1 delay_loop "		. $virt[$loop3]['delay_loop']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['sorry_server']) &&
			    $virt[$loop3]['sorry_server'] != "") {
				fputs ($fd, "$gap1 sorry_server "			. $virt[$loop3]['sorry_server']	. "\n", 80);
				if ($debug) { echo "$egap1 sorry_server "		. $virt[$loop3]['sorry_server']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['lb_algo']) &&
			    $virt[$loop3]['lb_algo'] != "") {
				fputs ($fd, "$gap1 lb_algo "		. $virt[$loop3]['lb_algo']	. "\n", 80);
				if ($debug) { echo "$egap1 lb_algo "		. $virt[$loop3]['lb_algo']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['lb_kind']) &&
			    $virt[$loop3]['lb_kind'] != "") {
				fputs ($fd, "$gap1 lb_kind "			. $virt[$loop3]['lb_kind']	. "\n", 80);
				if ($debug) { echo "$egap1 lb_kind "		. $virt[$loop3]['lb_kind']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['laddr_group_name']) &&
			    $virt[$loop3]['laddr_group_name'] != "") {
				fputs ($fd, "$gap1 laddr_group_name "			. $virt[$loop3]['laddr_group_name']		. "\n", 80);
				if ($debug) { echo "$egap1 laddr_group_name "		. $virt[$loop3]['laddr_group_name']		. "<BR>"; };
			}

			if (isset($virt[$loop3]['persistence_timeout']) &&
			    $virt[$loop3]['persistence_timeout'] != "") {
				fputs ($fd, "$gap1 persistence_timeout "		. $virt[$loop3]['persistence_timeout']	. "\n", 80);
				if ($debug) { echo "$egap1 persistence_timeout "	. $virt[$loop3]['persistence_timeout'] . "<BR>"; };
			}

			if (isset($virt[$loop3]['persistence_granularity']) &&
			    $virt[$loop3]['persistence_granularity'] != "") {
				fputs ($fd, "$gap1 persistence_granularity "			. $virt[$loop3]['persistence_granularity']	. "\n", 80);
				if ($debug) { echo "$egap1 persistence_granularity "		. $virt[$loop3]['persistence_granularity']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['ha_suspend']) &&
			    $virt[$loop3]['ha_suspend'] != "") {
				fputs ($fd, "$gap1 ha_suspend "			. $virt[$loop3]['ha_suspend']		. "\n", 300);
				if ($debug) { echo "$egap1 ha_suspend "		. $virt[$loop3]['ha_suspend']		. "<BR>"; };
			}

			if (isset($virt[$loop3]['virtualhost']) &&
			    $virt[$loop3]['virtualhost'] != "") {
				fputs ($fd, "$gap1 virtualhost "			. $virt[$loop3]['virtualhost']	. "\n", 300);
				if ($debug) { echo "$egap1 virtualhost "		. $virt[$loop3]['virtualhost']	. "<BR>"; };
			}
			
			if (isset($virt[$loop3]['quorum']) &&
			    $virt[$loop3]['quorum'] != "") {
				fputs ($fd, "$gap1 quorum "			. $virt[$loop3]['quorum']	. "\n", 300);
				if ($debug) { echo "$egap1 quorum "		. $virt[$loop3]['quorum']	. "<BR>"; };
			}
			
			if (isset($virt[$loop3]['hysteresis']) &&
			    $virt[$loop3]['hysteresis'] != "") {
				fputs ($fd, "$gap1 hysteresis "		. $virt[$loop3]['hysteresis']	. "\n", 300);
				if ($debug) { echo "$egap1 hysteresis "	. $virt[$loop3]['hysteresis']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['quorum_up']) &&
			    $virt[$loop3]['quorum_up'] != "") {
				fputs ($fd, "$gap1 quorum_up "		. $virt[$loop3]['quorum_up']. "\n", 300);
				if ($debug) { echo "$egap1 quorum_up "	. $virt[$loop3]['quorum_up']. "<BR>"; };
			}
			
			if (isset($virt[$loop3]['quorum_down']) &&
			    $virt[$loop3]['quorum_down'] != "") {
				fputs ($fd, "$gap1 quorum_down "		. $virt[$loop3]['quorum_down']	. "\n", 80);
				if ($debug) { echo "$egap1 quorum_down "	. $virt[$loop3]['quorum_down']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['est_timeout']) &&
			    $virt[$loop3]['est_timeout'] != "") {
				fputs ($fd, "$gap1 est_timeout "		. $virt[$loop3]['est_timeout']	. "\n", 80);
				if ($debug) { echo "$egap1 est_timeout "	. $virt[$loop3]['est_timeout']	. "<BR>"; };
			}

			if (isset($virt[$loop3]['protocol']) &&
			    $virt[$loop3]['protocol'] != "") {
				fputs ($fd, "$gap1 protocol "			. $virt[$loop3]['protocol']	. "\n", 80);
				if ($debug) { echo "$egap1 protocol "		. $virt[$loop3]['protocol']	. "<BR>"; };
			}

			while ( isset($serv[$loop3][$loop4]['ip']) && $serv[$loop3][$loop4]['ip'] != "") {

				if (($loop4 == $delete_item) && ($loop3 == $delete_virt) && ($level == "2") && ($delete_service == "server")) { 
					$loop4++;
				} else {

					if ($debug) { echo "<P><B>Server</B><BR>"; };
				
					if (isset($serv[$loop3][$loop4]['ip']) &&
					    $serv[$loop3][$loop4]['ip'] != "") { 
						fputs ($fd, "$gap1 real_server " . $serv[$loop3][$loop4]['ip']	. " " . $serv[$loop3][$loop4]['port'] . " {\n", 80);
						if ($debug) { echo "$egap1 real_server " . $serv[$loop3][$loop4]['ip'] . " " . $serv[$loop3][$loop4]['port'] . " {<BR>"; };
					}

					if (isset($serv[$loop3][$loop4]['notify_up']) &&
					    $serv[$loop3][$loop4]['notify_up'] != "") {
						fputs ($fd, "$gap2 notify_up "		. $serv[$loop3][$loop4]['notify_up']	. "\n", 80);
						if ($debug) { echo "$egap2 notify_up "	. $serv[$loop3][$loop4]['notify_up']	. "<BR>"; };
					}

					if (isset($serv[$loop3][$loop4]['notify_down']) &&
					    $serv[$loop3][$loop4]['notify_down'] != "") {
						fputs ($fd, "$gap2 notify_down "		. $serv[$loop3][$loop4]['notify_down']	. "\n", 80);
						if ($debug) { echo "$egap2 notify_down "	. $serv[$loop3][$loop4]['notify_down']	. "<BR>"; };
					}
				
					if (isset($serv[$loop3][$loop4]['weight']) &&
					    $serv[$loop3][$loop4]['weight'] != "") {
						fputs ($fd, "$gap2 weight "		. $serv[$loop3][$loop4]['weight']	. "\n", 80);
						if ($debug) { echo "$egap2 weight "	. $serv[$loop3][$loop4]['weight']	. "<BR>"; };
					}

					if (isset($serv[$loop3][$loop4]['monitor']['type'])) {
					    	if ($serv[$loop3][$loop4]['monitor']['type'] == "TCP_CHECK") { 
							fputs ($fd, "$gap2 TCP_CHECK " 	. " {\n", 80);
							if ($debug) { echo "$egap2 TCP_CHECK "  . " {<BR>"; };
					

							if (isset($serv[$loop3][$loop4]['monitor']['tcp_connect_port']) &&
					    			$serv[$loop3][$loop4]['monitor']['tcp_connect_port'] != "") {
								fputs ($fd, "$gap3 connect_port " . $serv[$loop3][$loop4]['monitor']['tcp_connect_port']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_port " . $serv[$loop3][$loop4]['monitor']['tcp_connect_port']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['tcp_bindto']) &&
					    			$serv[$loop3][$loop4]['monitor']['tcp_bindto'] != "") {
								fputs ($fd, "$gap3 bindto " . $serv[$loop3][$loop4]['monitor']['tcp_bindto']	. "\n", 80);
								if ($debug) { echo "$egap3 bindto " . $serv[$loop3][$loop4]['monitor']['tcp_bindto']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['tcp_connect_timeout']) &&
					    			$serv[$loop3][$loop4]['monitor']['tcp_connect_timeout'] != "") {
								fputs ($fd, "$gap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['tcp_connect_timeout']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['tcp_connect_timeout']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap2 }\n", 80);
                                                	if ($debug) { echo "$egap2 }<BR>"; }

						} else if ($serv[$loop3][$loop4]['monitor']['type'] == "HTTP_GET") {
							fputs ($fd, "$gap2 HTTP_GET " 	. " {\n", 80);
							if ($debug) { echo "$egap2 HTTP_GET "  . " {<BR>"; };

							fputs ($fd, "$gap3 url " 	. " {\n", 80);
							if ($debug) { echo "$egap3 url "  . " {<BR>"; };

							if (isset($serv[$loop3][$loop4]['monitor']['http_path']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_path'] != "") {
								fputs ($fd, "$gap4 path " . $serv[$loop3][$loop4]['monitor']['http_path']	. "\n", 80);
								if ($debug) { echo "$egap4 path " . $serv[$loop3][$loop4]['monitor']['http_path']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_digest']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_digest'] != "") {
								fputs ($fd, "$gap4 digest " . $serv[$loop3][$loop4]['monitor']['http_digest']	. "\n", 80);
								if ($debug) { echo "$egap4 digest " . $serv[$loop3][$loop4]['monitor']['http_digest']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_status_code']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_status_code'] != "") {
								fputs ($fd, "$gap4 status_code " . $serv[$loop3][$loop4]['monitor']['http_status_code']	. "\n", 80);
								if ($debug) { echo "$egap4 status_code " . $serv[$loop3][$loop4]['monitor']['http_status_code']	. "<BR>"; };
							}


                                                	fputs ($fd,"$gap3 }\n", 80);
                                                	if ($debug) { echo "$egap3 }<BR>"; }

							if (isset($serv[$loop3][$loop4]['monitor']['http_connect_timeout']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_connect_timeout'] != "") {
								fputs ($fd, "$gap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['http_connect_timeout']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['http_connect_timeout']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_connect_port']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_connect_port'] != "") {
								fputs ($fd, "$gap3 connect_port " . $serv[$loop3][$loop4]['monitor']['http_connect_port']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_port " . $serv[$loop3][$loop4]['monitor']['http_connect_port']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_bindto']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_bindto'] != "") {
								fputs ($fd, "$gap3 bindto " . $serv[$loop3][$loop4]['monitor']['http_bindto']	. "\n", 80);
								if ($debug) { echo "$egap3 bindto " . $serv[$loop3][$loop4]['monitor']['http_bindto']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_nb_get_retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_nb_get_retry'] != "") {
								fputs ($fd, "$gap3 nb_get_retry " . $serv[$loop3][$loop4]['monitor']['http_nb_get_retry']	. "\n", 80);
								if ($debug) { echo "$egap3 nb_get_retry " . $serv[$loop3][$loop4]['monitor']['http_nb_get_retry']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['http_delay_before_retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['http_delay_before_retry'] != "") {
								fputs ($fd, "$gap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['http_delay_before_retry']	. "\n", 80);
								if ($debug) { echo "$egap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['http_delay_before_retry']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap2 }\n", 80);
                                                	if ($debug) { echo "$egap2 }<BR>"; }

						} else if ($serv[$loop3][$loop4]['monitor']['type'] == "SSL_GET") { 
							fputs ($fd, "$gap2 SSL_GET " 	. " {\n", 80);
							if ($debug) { echo "$egap2 SSL_GET "  . " {<BR>"; };

							fputs ($fd, "$gap3 url " 	. " {\n", 80);
							if ($debug) { echo "$egap3 url "  . " {<BR>"; };

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_path']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_path'] != "") {
								fputs ($fd, "$gap4 path " . $serv[$loop3][$loop4]['monitor']['ssl_path']	. "\n", 80);
								if ($debug) { echo "$egap4 path " . $serv[$loop3][$loop4]['monitor']['ssl_path']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_digest']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_digest'] != "") {
								fputs ($fd, "$gap4 digest " . $serv[$loop3][$loop4]['monitor']['ssl_digest']	. "\n", 80);
								if ($debug) { echo "$egap4 digest " . $serv[$loop3][$loop4]['monitor']['ssl_digest']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_status_code']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_status_code'] != "") {
								fputs ($fd, "$gap4 status_code " . $serv[$loop3][$loop4]['monitor']['ssl_status_code']	. "\n", 80);
								if ($debug) { echo "$egap4 status_code " . $serv[$loop3][$loop4]['monitor']['ssl_status_code']	. "<BR>"; };
							}


                                                	fputs ($fd,"$gap3 }\n", 80);
                                                	if ($debug) { echo "$egap3 }<BR>"; }


							if (isset($serv[$loop3][$loop4]['monitor']['ssl_connect_port']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_connect_port'] != "") {
								fputs ($fd, "$gap3 connect_port " . $serv[$loop3][$loop4]['monitor']['ssl_connect_port']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_port " . $serv[$loop3][$loop4]['monitor']['ssl_connect_port']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_bindto']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_bindto'] != "") {
								fputs ($fd, "$gap3 bindto " . $serv[$loop3][$loop4]['monitor']['ssl_bindto']	. "\n", 80);
								if ($debug) { echo "$egap3 bindto " . $serv[$loop3][$loop4]['monitor']['ssl_bindto']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_connect_timeout']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_connect_timeout'] != "") {
								fputs ($fd, "$gap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['ssl_connect_timeout']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['ssl_connect_timeout']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_nb_get_retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_nb_get_retry'] != "") {
								fputs ($fd, "$gap3 nb_get_retry " . $serv[$loop3][$loop4]['monitor']['ssl_nb_get_retry']	. "\n", 80);
								if ($debug) { echo "$egap3 nb_get_retry " . $serv[$loop3][$loop4]['monitor']['ssl_nb_get_retry']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['ssl_delay_before_retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['ssl_delay_before_retry'] != "") {
								fputs ($fd, "$gap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['ssl_delay_before_retry']	. "\n", 80);
								if ($debug) { echo "$egap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['ssl_delay_before_retry']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap2 }\n", 80);
                                                	if ($debug) { echo "$egap2 }<BR>"; }

						} else if ($serv[$loop3][$loop4]['monitor']['type'] == "SMTP_CHECK") {
                                                        fputs ($fd, "$gap2 SMTP_CHECK "    . " {\n", 80);
                                                        if ($debug) { echo "$egap2 SMTP_CHECK "  . " {<BR>"; };

							fputs ($fd, "$gap3 host " 	. " {\n", 80);
							if ($debug) { echo "$egap3 host "  . " {<BR>"; };

							if (isset($serv[$loop3][$loop4]['monitor']['connect_ip']) &&
					    			$serv[$loop3][$loop4]['monitor']['connect_ip'] != "") {
								fputs ($fd, "$gap4 connect_ip " . $serv[$loop3][$loop4]['monitor']['connect_ip']	. "\n", 80);
								if ($debug) { echo "$egap4 connect_ip " . $serv[$loop3][$loop4]['monitor']['connect_ip']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['smtp_connect_port']) &&
					    			$serv[$loop3][$loop4]['monitor']['smtp_connect_port'] != "") {
								fputs ($fd, "$gap4 connect_port " . $serv[$loop3][$loop4]['monitor']['smtp_connect_port']	. "\n", 80);
								if ($debug) { echo "$egap4 connect_port " . $serv[$loop3][$loop4]['monitor']['smtp_connect_port']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['smtp_bindto']) &&
					    			$serv[$loop3][$loop4]['monitor']['smtp_bindto'] != "") {
								fputs ($fd, "$gap4 bindto " . $serv[$loop3][$loop4]['monitor']['smtp_bindto']	. "\n", 80);
								if ($debug) { echo "$egap4 bindto " . $serv[$loop3][$loop4]['monitor']['smtp_bindto']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap3 }\n", 80);
                                                	if ($debug) { echo "$egap3 }<BR>"; }

							if (isset($serv[$loop3][$loop4]['monitor']['smtp_connect_timeout']) &&
					    			$serv[$loop3][$loop4]['monitor']['smtp_connect_timeout'] != "") {
								fputs ($fd, "$gap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['smtp_connect_timeout']	. "\n", 80);
								if ($debug) { echo "$egap3 connect_timeout " . $serv[$loop3][$loop4]['monitor']['smtp_connect_timeout']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['retry'] != "") {
								fputs ($fd, "$gap3 retry " . $serv[$loop3][$loop4]['monitor']['retry']	. "\n", 80);
								if ($debug) { echo "$egap3 retry " . $serv[$loop3][$loop4]['monitor']['retry']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['smtp_delay_before_retry']) &&
					    			$serv[$loop3][$loop4]['monitor']['smtp_delay_before_retry'] != "") {
								fputs ($fd, "$gap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['smtp_delay_before_retry']	. "\n", 80);
								if ($debug) { echo "$egap3 delay_before_retry " . $serv[$loop3][$loop4]['monitor']['smtp_delay_before_retry']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['helo_name']) &&
					    			$serv[$loop3][$loop4]['monitor']['helo_name'] != "") {
								fputs ($fd, "$gap3 helo_name " . $serv[$loop3][$loop4]['monitor']['helo_name']	. "\n", 80);
								if ($debug) { echo "$egap3 helo_name " . $serv[$loop3][$loop4]['monitor']['helo_name']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap2 }\n", 80);
                                                	if ($debug) { echo "$egap2 }<BR>"; }

						} else if ($serv[$loop3][$loop4]['monitor']['type'] == "MISC_CHECK") {
                                                        fputs ($fd, "$gap2 MISC_CHECK "    . " {\n", 80);
                                                        if ($debug) { echo "$egap2 MISC_CHECK "  . " {<BR>"; };

							if (isset($serv[$loop3][$loop4]['monitor']['misc_path']) &&
					    			$serv[$loop3][$loop4]['monitor']['misc_path'] != "") {
								fputs ($fd, "$gap3 misc_path " . $serv[$loop3][$loop4]['monitor']['misc_path']	. "\n", 80);
								if ($debug) { echo "$egap3 misc_path " . $serv[$loop3][$loop4]['monitor']['misc_path']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['misc_timeout']) &&
					    			$serv[$loop3][$loop4]['monitor']['misc_timeout'] != "") {
								fputs ($fd, "$gap3 misc_timeout " . $serv[$loop3][$loop4]['monitor']['misc_timeout']	. "\n", 80);
								if ($debug) { echo "$egap3 misc_timeout " . $serv[$loop3][$loop4]['monitor']['misc_timeout']	. "<BR>"; };
							}

							if (isset($serv[$loop3][$loop4]['monitor']['misc_dynamic']) &&
					    			$serv[$loop3][$loop4]['monitor']['misc_dynamic'] != "") {
								fputs ($fd, "$gap3 misc_dynamic" . "\n", 80);
								if ($debug) { echo "$egap3 misc_dynamic " . $serv[$loop3][$loop4]['monitor']['misc_dynamic']	. "<BR>"; };
							}

                                                	fputs ($fd,"$gap2 }\n", 80);
                                                	if ($debug) { echo "$egap2 }<BR>"; }
						}

					}
	
				
					$loop4++;
					fputs ($fd,"$gap1 }\n", 80);
					if ($debug) { echo "$egap1 }<BR>"; }
				}
				
			} //end server loop

			fputs ($fd,"}\n", 80);
			if ($debug) { echo "}<BR>"; };

			$loop3++;
			$loop4 = 1;
			
		}

	} // end virtual server loop
	fclose($fd);
	backup_lvs();
	if ($debug) { echo "<HR>"; }
	$debug=$old_debug;
}

function open_file($mode) {
        global $fd;
	global $LVS;
	global $debug;

        $fd = @fopen($LVS, $mode);
	if ($fd == false) {
		include ("lvserror.php");
		exit;
	}
		
        rewind($fd); /* unnessecary but I'm paranoid */
}

function add_vrrp() {

	global $vrrp_instance;
	$loop2 = 1;	

	/* find end of existing data */
	while ($vrrp_instance[$loop2]['vrrp_instance'] != "" ) { $loop2++; }
	
	$vrrp_instance[$loop2]['vrrp_instance']	= "[vrrp_instance_name]";
	$vrrp_instance[$loop2]['state']	= "MASTER|SLAVE";
	$vrrp_instance[$loop2]['priority']	= "[priority]";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_vrrp_sync_group() {

	global $vrrp_sync_group;
	$loop2 = 1;	

	/* find end of existing data */
	while ($vrrp_sync_group[$loop2]['vrrp_sync_group'] != "" ) { $loop2++; }
	
	$vrrp_sync_group[$loop2]['vrrp_sync_group']	= "[vrrp_sync_group_name]";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_virtual() {

	global $virt;
	$loop2 = 1;	

	/* find end of existing data */
	while (isset($virt[$loop2]['ip']) &&
	       $virt[$loop2]['ip'] != "") {
		$loop2++;
	}

	$virt[$loop2]['ip']	= "[ip]";
	$virt[$loop2]['port']	= "[port]";
	$virt[$loop2]['group']	= "[group]";
	$virt[$loop2]['fwmark']	= "[fwmark]";
	$virt[$loop2]['delay_loop']	= "5";
	$virt[$loop2]['lb_algo']		= "wrr";
	$virt[$loop2]['lb_kind']		= "FNAT";
	$virt[$loop2]['laddr_group_name']	= "none";
	$virt[$loop2]['protocol']	= "tcp";
	$virt[$loop2]['persistence_timeout']	= "";
	$virt[$loop2]['persistence_granularity']		= "";
	//$virt[$loop2]['send']		= "\"GET / HTTP/1.0\\r\\n\\r\\n\"";
	$virt[$loop2]['ha_suspend']		= "";
	//$virt[$loop2]['expect']		= "\"HTTP\"";	
	$virt[$loop2]['virtualhost']	= "";	
	$virt[$loop2]['quorum']	= "";
	$virt[$loop2]['hysteresis']	= "";
	$virt[$loop2]['quorum_up']	= "1";
	$virt[$loop2]['quorum_down']	= "";
	$virt[$loop2]['est_timeout']	= "15";
	$virt[$loop2]['sorry_server']	= "0";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_staticip() {

	global $static_ipaddress;
	$loop2 = 1;	

	/* find end of existing data */
	while (isset($static_ipaddress[$loop2]['ip']) &&
	       $static_ipaddress[$loop2]['ip'] != "") {
		$loop2++;
	}

	$static_ipaddress[$loop2]['ip']		= "0.0.0.0";
	$static_ipaddress[$loop2]['mask']	= "24";
	$static_ipaddress[$loop2]['dev']	= "eth1";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_local_address_group() {

	global $local_address_group;
	$loop2 = 1;	

	$default_laddrgname = "snat group name";
	$local_address_group[$default_laddrgname][]		= "snat ip";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_service($virt_idx) {

	global $serv;
	
	$loop2 = 1;

	/* find end of existing data */
	while ($serv[$virt_idx][$loop2]['ip'] != "" ) { $loop2++; }

	/* Insert default record */
	$serv[$virt_idx][$loop2]['ip']		= "[ip]";
	$serv[$virt_idx][$loop2]['port']		= "[port]";
	$serv[$virt_idx][$loop2]['notify_up']		= "";
//	$serv[$virt_idx][$loop2]['active']		= "0";
	$serv[$virt_idx][$loop2]['notify_down']		= "";
	$serv[$virt_idx][$loop2]['weight']		= "1";

	open_file("w+"); write_config(""); /* umm save this quick to file */;

}

function add_vrrp_virtual_ipaddress($vrrp_idx) {

	global $vrrp_instance;
	$vrrp_instance[$vrrp_idx]['virtual_ipaddress'][] = "ip/netmask dev ethxxx";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_vrrp_virtual_routes($vrrp_idx) {

	global $vrrp_instance;
	$vrrp_instance[$vrrp_idx]['virtual_routes'][] = "src ip to network/netmask via gateway dev ethxxx";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_vrrp_track_interface($vrrp_idx) {

	global $vrrp_instance;
	$vrrp_instance[$vrrp_idx]['track_interface'][] = "ethxxx";

	open_file("w+"); write_config(""); /* umm save this quick to file */
}

function add_vrrp_sync_group_group($vrrp_sync_group_idx) {

	global $vrrp_sync_group;
	$vrrp_sync_group[$vrrp_sync_group_idx]['group'][] = "vrrp_instance_name";

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
fclose($fd);

if ($debug) { print_arrays(); };

?>
