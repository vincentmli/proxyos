<?php
        $edit_action = $_GET['edit_action'];
        $selected_host = $_GET['selected_host'];
        $selected = $_GET['selected'];
	if ($edit_action == "CANCEL") {
		header("Location: virtual_edit_real.php?selected_host=$selected_host&selected=$selected");		
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $serv;

	require('parse.php');


		
	if ($edit_action == "ACCEPT") {
                $notify_up = $_GET['notify_up'];
                $notify_down = $_GET['notify_down'];
                $weight = $_GET['weight'];

		$serv[$selected_host][$selected]['ip']	=	$_GET['ip'];
		$serv[$selected_host][$selected]['port']	=	$_GET['port'];
		
		$serv[$selected_host][$selected]['notify_up']		=	$notify_up;
		$serv[$selected_host][$selected]['notify_down']		=	$notify_down;
		$serv[$selected_host][$selected]['weight']		=	$weight;

		$serv[$selected_host][$selected]['monitor']['type']	=	$_GET['type'];
		$serv[$selected_host][$selected]['monitor']['tcp_connect_port']	=	$_GET['tcp_connect_port'];
		$serv[$selected_host][$selected]['monitor']['tcp_bindto']	=	$_GET['tcp_bindto'];
		$serv[$selected_host][$selected]['monitor']['tcp_connect_timeout']	=	$_GET['tcp_connect_timeout'];

		$serv[$selected_host][$selected]['monitor']['http_path']	=	$_GET['http_path'];
		$serv[$selected_host][$selected]['monitor']['http_digest']	=	$_GET['http_digest'];
		$serv[$selected_host][$selected]['monitor']['http_status_code']	=	$_GET['http_status_code'];
		$serv[$selected_host][$selected]['monitor']['http_connect_port']	=	$_GET['http_connect_port'];
		$serv[$selected_host][$selected]['monitor']['http_bindto']	=	$_GET['http_bindto'];
		$serv[$selected_host][$selected]['monitor']['http_connect_timeout']	=	$_GET['http_connect_timeout'];
		$serv[$selected_host][$selected]['monitor']['http_nb_get_retry']	=	$_GET['http_nb_get_retry'];
		$serv[$selected_host][$selected]['monitor']['http_delay_before_retry']	=	$_GET['http_delay_before_retry'];

		$serv[$selected_host][$selected]['monitor']['ssl_path']	=	$_GET['ssl_path'];
		$serv[$selected_host][$selected]['monitor']['ssl_digest']	=	$_GET['ssl_digest'];
		$serv[$selected_host][$selected]['monitor']['ssl_status_code']	=	$_GET['ssl_status_code'];
		$serv[$selected_host][$selected]['monitor']['ssl_connect_port']	=	$_GET['ssl_connect_port'];
		$serv[$selected_host][$selected]['monitor']['ssl_bindto']	=	$_GET['ssl_bindto'];
		$serv[$selected_host][$selected]['monitor']['ssl_connect_timeout']	=	$_GET['ssl_connect_timeout'];
		$serv[$selected_host][$selected]['monitor']['ssl_delay_before_retry']	=	$_GET['ssl_delay_before_retry'];
		$serv[$selected_host][$selected]['monitor']['ssl_nb_get_retry']	=	$_GET['ssl_nb_get_retry'];

		$serv[$selected_host][$selected]['monitor']['connect_ip']	=	$_GET['connect_ip'];
		$serv[$selected_host][$selected]['monitor']['smtp_connect_port']	=	$_GET['smtp_connect_port'];
		$serv[$selected_host][$selected]['monitor']['smtp_bindto']	=	$_GET['smtp_bindto'];
		$serv[$selected_host][$selected]['monitor']['smtp_connect_timeout']	=	$_GET['smtp_connect_timeout'];
		$serv[$selected_host][$selected]['monitor']['retry']	=	$_GET['retry'];
		$serv[$selected_host][$selected]['monitor']['smtp_delay_before_retry']	=	$_GET['smtp_delay_before_retry'];
		$serv[$selected_host][$selected]['monitor']['helo_name']	=	$_GET['helo_name'];

		$serv[$selected_host][$selected]['monitor']['misc_path']	=	$_GET['misc_path'];
		$serv[$selected_host][$selected]['monitor']['misc_timeout']	=	$_GET['misc_timeout'];
		$serv[$selected_host][$selected]['monitor']['misc_dynamic']	=	$_GET['misc_dynamic'];

	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual servers - Editing virtual server - Editing real server)</TITLE>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="showhide_health_check.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>
<STYLE TYPE="text/css">


TD      {
        font-family: helvetica, sans-serif;
        }
TD.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
        
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT REAL SERVER</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="local_address_group.php" NAME="Local address group" CLASS="taboff"><B>SNAT ADDRESS GROUP</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>

        </TR>
</TABLE>
<?php
	// echo "Query = $QUERY_STRING";

?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:
		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL SERVER">VIRTUAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_services.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="MONITORING SCRIPTS">MONITORING SCRIPTS</A></TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM id="real_server_form" METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="virtual_edit_real_edit.php">

	<TABLE>
		<TR>
			<TD>IP: </TD>
			<TD><INPUT TYPE="TEXT" NAME="ip" VALUE=<?php echo $serv[$selected_host][$selected]['ip'] ?>></TD>
		</TR>
		<TR>
			<TD>Port: </TD>
			<TD><INPUT TYPE="TEXT" NAME="port" VALUE=<?php echo $serv[$selected_host][$selected]['port'] ?>></TD>
		</TR>
		<TR>
			<TD>Notify UP: </TD>
			<TD><INPUT TYPE="TEXT" NAME="notify_up" VALUE=<?php echo $serv[$selected_host][$selected]['notify_up'] ?>></TD>
		</TR>
		<TR>
			<TD>Notify Down: </TD>
			<TD><INPUT TYPE="TEXT" NAME="notify_down" VALUE=<?php echo $serv[$selected_host][$selected]['notify_down'] ?>></TD>
		</TR>
		<TR>
			<TD>Weight: </TD>
			<TD><INPUT TYPE="TEXT" NAME="weight" VALUE=<?php echo $serv[$selected_host][$selected]['weight'] ?>></TD>
		</TR>

		<TR>
			<TD>Health Check: </TD>
			<TD>
				<SELECT id = "type" NAME="type">
					<OPTION value=<?php if ($serv[$selected_host][$selected]['monitor']['type'] == "") { echo "SELECTED"; } ?>> Choose Monitor</OPTION>
					<OPTION value="HTTP_GET"> HTTP_GET </OPTION>
					<OPTION value="SSL_GET"> SSL_GET </OPTION>
					<OPTION value="TCP_CHECK"> TCP_CHECK </OPTION>
					<OPTION value="SMTP_CHECK"> SMTP_CHECK </OPTION>
					<OPTION value="MISC_CHECK"> MISC_CHECK </OPTION>
				</SELECT>
			</TD>
		</TR>

		<TBODY style="width:100%" id="http_get">
			<TR>
			 	<TD style="width:10%">URL Path: </TD>
			 	<TD><INPUT TYPE="TEXT" style="width:100%" NAME="http_path" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_path'] ?>></TD>
			</TR>
			<TR>
			 	<TD>URL Digest: </TD>
			 	<TD><INPUT TYPE="TEXT" style="width:100%" NAME="http_digest" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_digest'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Status Code: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_status_code" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_status_code'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Timeout: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_connect_timeout" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_connect_timeout'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Connect Port: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_connect_port" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_connect_port'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Bindto: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_bindto" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_bindto'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Number of GET Retry: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_nb_get_retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_nb_get_retry'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Delay Before Retry: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="http_delay_before_retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['http_delay_before_retry'] ?>></TD>
			</TR>
		</TBODY>

		<TBODY style="width:100%" id="ssl_get">
			<TR>
			 	<TD style="width:10%">URL Path: </TD>
			 	<TD><INPUT TYPE="TEXT" style="width:100%" NAME="ssl_path" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_path'] ?>></TD>
			</TR>
			<TR>
			 	<TD>URL Digest: </TD>
			 	<TD><INPUT TYPE="TEXT" style="width:100%" NAME="ssl_digest" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_digest'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Status Code: </TD>
			 	<TD><INPUT TYPE="TEXT"  NAME="ssl_status_code" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_status_code'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Timeout: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="ssl_connect_timeout" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_connect_timeout'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Connect Port: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="ssl_connect_port" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_connect_port'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Bindto: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="ssl_bindto" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_bindto'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Number of GET Retry: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="ssl_nb_get_retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_nb_get_retry'] ?>></TD>
			</TR>
			<TR>
			 	<TD style="width:16%">Delay Before Retry: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="ssl_delay_before_retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['ssl_delay_before_retry'] ?>></TD>
			</TR>
		</TBODY>

		<TBODY id="tcp_check">
			<TR>
			 	<TD>Connect port: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="tcp_connect_port" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['tcp_connect_port'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Bind to: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="tcp_bindto" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['tcp_bindto'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Timeout: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="tcp_connect_timeout" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['tcp_connect_timeout'] ?>></TD>
			</TR>
		</TBODY>

		<TBODY id="smtp_check">
			<TR>
			 	<TD>Host IP: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="connect_ip" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['connect_ip'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Host Port: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="smtp_connect_port" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['smtp_connect_port'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Bindto: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="smtp_bindto" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['smtp_bindto'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Timeout: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="smtp_connect_timeout" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['smtp_connect_timeout'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Number of Retry a Failed Check: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['retry'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Delay Before Retry: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="smtp_delay_before_retry" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['smtp_delay_before_retry'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Helo Name: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="helo_name" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['helo_name'] ?>></TD>
			</TR>
		</TBODY>

		<TBODY id="misc_check">
			<TR>
			 	<TD>External Monitor Path: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="misc_path" VALUE=<?php echo htmlentities($serv[$selected_host][$selected]['monitor']['misc_path'], ENT_QUOTES) ?>></TD>
			</TR>
			<TR>
			 	<TD>External Monitor Timeout: </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="misc_timeout" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['misc_timeout'] ?>></TD>
			</TR>
			<TR>
			 	<TD>Exit Code to Dynamic Weight Adjust (bool): </TD>
			 	<TD><INPUT TYPE="TEXT" NAME="misc_dynamic" VALUE=<?php echo $serv[$selected_host][$selected]['monitor']['misc_dynamic'] ?>></TD>
			</TR>
		</TBODY>

	</TABLE>
	
	<?php	
		/* Welcome to the magic show */
		echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>";
		echo "<INPUT TYPE=HIDDEN NAME=selected VALUE=$selected >";
	?>
<P>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
		<TR BGCOLOR="#666666">
			<TD><INPUT TYPE="SUBMIT" NAME="edit_action" VALUE="ACCEPT"></TD>
			<TD ALIGN=right><INPUT TYPE="SUBMIT" NAME="edit_action" VALUE="CANCEL"></TD>
		</TR>
</TABLE>
<?php open_file ("w+"); write_config(""); ?>
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
