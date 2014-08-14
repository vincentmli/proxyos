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

	if (isset($_GET['global_action']) &&
	    $_GET['global_action'] == "ACCEPT") {
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

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

                <A HREF="global_settings.php" NAME="GLOBAL SETTING">GLOBAL SETTING</A>
                &nbsp;|&nbsp;

                <A HREF="static_ipaddress.php" CLASS="tabon" NAME="STATIC IPADDRESS">STATIC IPADDRESS</A>
                &nbsp;|&nbsp;

                <A HREF="local_address_group.php" NAME="SNAT ADDRESS GROUP">SNAT ADDRESS GROUP</A>
                &nbsp;|&nbsp;

                </TD>

                <!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>


<P>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="global_settings.php">


<P>
<TABLE  BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title" COLSPAN="2">ENVIRONMENT</TD>
        </TR>

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
