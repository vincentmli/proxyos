<?php
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	global $debug_level;
	global $prim;
	global $global_defs;

	require('parse.php'); /* read in the config! Hurragh! */
	$prim['service'] = "lvs";
	
	if (empty($_GET['nat_nmask'])) {
		$NATRtrNmask	=	$prim['nat_nmask'];
	} else {
		$NATRtrNmask	=	$_GET['nat_nmask'];
	}

	if (isset($_GET['global_action']) &&
	    $_GET['global_action'] == "ACCEPT") {
		$prim['primary']			=	$_GET['PriLVSIP'];
		$prim['primary_private']		=	$_GET['primary_private'];
		
		if (empty($prim['primary_private'])) {
			$prim['backup_private'] = ""; 
                }
		
                $network = $prim['network'];
                switch ($network) {
                        case "NAT"			:	$network="nat"; break;
                        case "Direct Routing"		:	$network="direct"; break;
                        case "Tunneling"		:	$network="tunnel"; break;
                        default				:	break;
                }
        
                $prim['network']		=	$network;
        
                if ($prim['network'] == "nat") {
                        $prim['nat_router']	=	$_GET['NATRtrIP'] . " " . $_GET['NATRtrDev'];
                } else {
                        $prim['nat_router']	=	"";
                }

                if ($NATRtrNmask != "" ) {
                        $prim['nat_nmask']	=	$NATRtrNmask;
                } else {
                        $NATRtrNmask		=	"255.255.255.0";
		}
	}

	if ($debug_level == "" ) {
		if ($prim['debug_level'] != "" ) {
			$debug_level		=	$prim['debug_level'];
		} else {
			$debug_level		=	"NONE";
			$prim['debug_level']	=	"NONE";
		}
	} else {
		$prim['debug_level'] = $debug_level;
	};

	if ($prim['network'] == "") { $prim['network'] = "direct";}

	$network = "";
	if (isset($_GET['network'])) {
		$network = $_GET['network'];
	}
	if ($network == "NAT") { 
		$prim['network'] = "nat"; 
		if (isset($_GET['NATRtrIP']) && isset($_GET['NATRtrDev'])) {
			$prim['nat_router'] = $_GET['NATRtrIP'] . " " . $_GET['NATRtrDev']; 
		}
	}
	if ($network == "Direct Routing") { 
	    $prim['network'] = "direct"; 
	    $prim['nat_router'] = ""; 
	}
	if ($network == "Tunneling") { 
	    $prim['network'] = "tunnel"; 
	    $prim['nat_router'] = ""; 
	}
	/* Make a semi sensible guess */
	if ($prim['primary'] == "") {
		$prim['primary'] = $_SERVER['SERVER_ADDR'];
		$PriLVSIP = $_SERVER['SERVER_ADDR'];
	}

	if (isset($_GET['tcp_timeout'])) {
		$prim['tcp_timeout'] = $_GET['tcp_timeout'];
	}
	if (isset($_GET['tcpfin_timeout'])) {
		$prim['tcpfin_timeout'] = $_GET['tcpfin_timeout'];
	}
	if (isset($_GET['udp_timeout'])) {
		$prim['udp_timeout'] = $_GET['udp_timeout'];
	}

	/* keepalived global defs */

	if (isset($_GET['notification_email'])) {
		$global_defs['notification_email'] = $_GET['notification_email'];
	}
	if (isset($_GET['notification_email_from'])) {
		$global_defs['notification_email_from'] = $_GET['notification_email_from'];
	}
	if (isset($_GET['smtp_server'])) {
		$global_defs['smtp_server'] = $_GET['smtp_server'];
	}
	if (isset($_GET['smtp_connect_timeout'])) {
		$global_defs['smtp_connect_timeout'] = $_GET['smtp_connect_timeout'];
	}
	if (isset($_GET['router_id'])) {
		$global_defs['router_id'] = $_GET['router_id'];
	}
	print_r($_GET['notification_email']);

	// echo "Query = $QUERY_STRING";
?>

<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Global Settings) <?php $debug && print "(DEBUG ON)" ?></TITLE>

<STYLE TYPE="text/css">
<!-- 

TD      {
        font-family: helvetica, sans-serif;
        }
        
.logo   {
        color: #FFFFFF;
        }
        
A.logolink      {
        color: #FFFFFF;
        font-size: .8em;
        }
        
.taboff {
        color: #FFFFFF;
        }
        
.tabon  {
        color: #999999;
        }
        
.title  {
        font-size: .8em;
        font-weight: bold;
        color: #660000;
        }
        
.smtext {
        font-size: .8em;
        }
        
.green  {
        color: 

// -->
</STYLE>

</HEAD>

<BODY BGCOLOR="#660000">

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
           <A HREF="introduction.html" CLASS="logolink">
           INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
           HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">GLOBAL SETTINGS</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="local_address_group.php" NAME="Local address group" CLASS="taboff"><B>SNAT ADDRESS GROUP</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="vrrp_main.php" NAME="VRRP instance" CLASS="taboff"><B>VRRP INSTANCE</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="taboff"><B>VIRTUAL SERVERS</B></A> </TD>

        </TR>
</TABLE>

<P>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="global_settings.php">


<P>
<TABLE  BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title" COLSPAN="2">ENVIRONMENT</TD>
        </TR>

	<!--
	<TR>
		<TD>Primary server public IP:</TD>
		<TD><INPUT TYPE="TEXT" NAME="PriLVSIP" SIZE=16 VALUE="<?php			
			echo $prim['primary'];
		?>"></TD>
	</TR>
	<TR>
		<TD>Primary server private IP:<BR>(May be blank)</TD>
		<TD><INPUT TYPE="TEXT" NAME="primary_private" SIZE=16 VALUE="<?php	
			echo $prim['primary_private'];
		?>"></TD>
	</TR>
	<TR>
		<TD>TCP timeout (seconds):</TD>
		<TD><INPUT TYPE="TEXT" NAME="tcp_timeout" SIZE=6 VALUE="<?php
			echo $prim['tcp_timeout'];
		?>"></TD>
	</TR>
	<TR>
		<TD>TCP FIN timeout (seconds):</TD>
		<TD><INPUT TYPE="TEXT" NAME="tcpfin_timeout" SIZE=6 VALUE="<?php
			echo $prim['tcpfin_timeout'];
		?>"></TD>
	</TR>

	<TR>
		<TD>UDP timeout (seconds):</TD>
		<TD><INPUT TYPE="TEXT" NAME="udp_timeout" SIZE=6 VALUE="<?php
			echo $prim['udp_timeout'];
		?>"></TD>
	</TR>
	-->

	<TR>
		<TD>Notification email :</TD>
		<TD><INPUT TYPE="TEXT" NAME="notification_email" SIZE=26 VALUE="<?php
			echo $global_defs['notification_email'];
		?>"></TD>
	</TR>
	<TR>
		<TD>Notification email from :</TD>
		<TD><INPUT TYPE="TEXT" NAME="notification_email_from" SIZE=26 VALUE="<?php
			echo $global_defs['notification_email_from'];
		?>"></TD>
	</TR>
	<TR>
		<TD>Smtp server :</TD>
		<TD><INPUT TYPE="TEXT" NAME="smtp_server" SIZE=16 VALUE="<?php
			echo $global_defs['smtp_server'];
		?>"></TD>
	</TR>
	<TR>
		<TD>Smtp connect timeout :</TD>
		<TD><INPUT TYPE="TEXT" NAME="smtp_connect_timeout" SIZE=6 VALUE="<?php
			echo $global_defs['smtp_connect_timeout'];
		?>"></TD>
	</TR>
	<TR>
		<TD>Router id :</TD>
		<TD><INPUT TYPE="TEXT" NAME="router_id" SIZE=16 VALUE="<?php
			echo $global_defs['router_id'];
		?>"></TD>
	</TR>

	<?php if ($prim['service'] != "fos") { ?>
	<!--TR>
		<TD>Use network type:<BR>(Current type is: <B><?php echo $prim['network']; ?></B> ) </TD>
		<TD><INPUT TYPE="SUBMIT" NAME="network" SIZE=16 VALUE="NAT"><BR>
		<INPUT TYPE="SUBMIT" NAME="network" SIZE=16 VALUE="Direct Routing"><BR>
		<INPUT TYPE="SUBMIT" NAME="network" SIZE=16 VALUE="Tunneling"><BR>
	</TR-->
	<?php if ($prim['network'] == "nat" ) { ?>
	 <TR>
		<TD>NAT Router IP:</TD>
		<TD><INPUT TYPE="TEXT" NAME="NATRtrIP" SIZE=16 VALUE="<?php
			$ip = explode(" ", $prim['nat_router']);
			echo $ip[0];
			// echo $prim['...???? WHAT??
		?>"></TD>
	</TR>
	<TR>
		<TD>NAT Router netmask:</TD>
		<TD>
			<SELECT NAME="nat_nmask">
				<OPTION <?php if ($NATRtrNmask == "255.255.255.255") { echo "SELECTED"; } ?>> 255.255.255.255
				<OPTION <?php if ($NATRtrNmask == "255.255.255.252") { echo "SELECTED"; } ?>> 255.255.255.252
				<OPTION <?php if ($NATRtrNmask == "255.255.255.248") { echo "SELECTED"; } ?>> 255.255.255.248
				<OPTION <?php if ($NATRtrNmask == "255.255.255.240") { echo "SELECTED"; } ?>> 255.255.255.240
				<OPTION <?php if ($NATRtrNmask == "255.255.255.224") { echo "SELECTED"; } ?>> 255.255.255.224
				<OPTION <?php if ($NATRtrNmask == "255.255.255.192") { echo "SELECTED"; } ?>> 255.255.255.192
				<OPTION <?php if ($NATRtrNmask == "255.255.255.128") { echo "SELECTED"; } ?>> 255.255.255.128
				<OPTION <?php if ($NATRtrNmask == "255.255.255.0")   { echo "SELECTED"; } ?>> 255.255.255.0
				<OPTION <?php if ($NATRtrNmask == "255.255.254.0")   { echo "SELECTED"; } ?>> 255.255.254.0
				<OPTION <?php if ($NATRtrNmask == "255.255.252.0")   { echo "SELECTED"; } ?>> 255.255.252.0
				<OPTION <?php if ($NATRtrNmask == "255.255.248.0")   { echo "SELECTED"; } ?>> 255.255.248.0
				<OPTION <?php if ($NATRtrNmask == "255.255.240.0")   { echo "SELECTED"; } ?>> 255.255.240.0
				<OPTION <?php if ($NATRtrNmask == "255.255.224.0")   { echo "SELECTED"; } ?>> 255.255.224.0
				<OPTION <?php if ($NATRtrNmask == "255.255.192.0")   { echo "SELECTED"; } ?>> 255.255.192.0
				<OPTION <?php if ($NATRtrNmask == "255.255.128.0")   { echo "SELECTED"; } ?>> 255.255.128.0
				<OPTION <?php if ($NATRtrNmask == "255.255.0.0")     { echo "SELECTED"; } ?>> 255.255.0.0

			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD>NAT Router device:</TD>
		<TD><INPUT TYPE="TEXT" NAME="NATRtrDev" SIZE=8 VALUE="<?php
			$dev = explode(" ", $prim['nat_router']);
			if (isset($dev[1]))
				echo $dev[1];
			// echo $prim['..??
	?>"></TD>
	</TR>

	<?php } ?>
	<?php } ?>
</TABLE>
<HR>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR BGCOLOR="#666666">
		<TD>
			<INPUT TYPE="SUBMIT" NAME="global_action" VALUE="ACCEPT"> <SPAN CLASS="taboff"> -- Click here to apply changes on this page</SPAN>
		</TD>
	</TR>
</TABLE>

<?php 
	open_file ("w+"); write_config(""); 
?>


</FORM>

</TD></TR></TABLE>
</BODY>
</HTML>
