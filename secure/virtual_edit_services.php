<?php
        $gen_service = $_GET['gen_service'];
        $selected_host = $_GET['selected_host'];
	if ($gen_service == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: virtual_edit_virt.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */

	// print_arrays(); /* before */

	// print_arrays(); /* after */

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual Servers - MONITORING SCRIPTS)</TITLE>
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
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>PIRANHA</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
	    <A HREF="introduction.php" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT MONITORING SCRIPTS</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
		<TD WIDTH="20%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
                <TD WIDTH="20%" ALIGN="CENTER" BGCOLOR="#ffffff"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>
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

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_services.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="MONITORING SCRIPTS">MONITORING SCRIPTS</A></TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>
<?php
	// echo "Query = $QUERY_STRING";
	// echo "<PRE>[" . $virt[$selected_host]['send'] . "] [$send]</PRE><BR>";
	// echo "<PRE>[" . $virt[$selected_host]['expect'] . "] [$expect]</PRE><BR>";
	// echo "<PRE>[" . $virt[$selected_host]['send_program'] . "] [$send_program]</PRE><BR>";
	// echo "<PRE>[" . $virt[$selected_host]['expect_program'] . "] [$expect_program]</PRE><BR>";
		
	/* 
	 * php escapes \n to \\n ! argh! { fortunately there is stripslashes();
	 * this statement is right only if magic_quotes_gpc is set to ON !
	 */
	$lst = array ("send", "expect", "start_cmd", "stop_cmd", "send_program", "expect_program", "use_regex");
	foreach ($lst as $i) {
		if (!isset($_GET[$i])) { 
			$_GET[$i] = "";
		}
	}
	
	if (get_magic_quotes_gpc() == 1) {
		$send = stripslashes($_GET['send']);  
		$expect = stripslashes($_GET['expect']);   
		$start_cmd = stripslashes($_GET['start_cmd']);
		$stop_cmd = stripslashes($_GET['stop_cmd']);
		$send_program = stripslashes($_GET['send_program']);  
		$expect_program = stripslashes($_GET['expect_program']);
	} else {
		$send = $_GET['send'];      
		$expect = $_GET['expect'];       
		$start_cmd = $_GET['start_cmd'];    
		$stop_cmd = $_GET['stop_cmd'];    
		$send_program = $_GET['send_program'];      
		$expect_program = $_GET['expect_program'];    
	}
	
	$use_regex      = $_GET['use_regex'];
        if ( $use_regex != "1" ) { $use_regex = "0"; }

	if ($gen_service == "ACCEPT") {
	
                $virt[$selected_host]['use_regex'] = $use_regex;

		/* take values and enclose them in quotes */
		if (!empty($send))	{
			$send		= "\"" . $send . "\"";
			$virt[$selected_host]['send'] = $send;
		}

		if (!empty($expect)) {
			$expect		= "\"" . $expect . "\"";
			$virt[$selected_host]['expect'] = $expect;
		}
		
		if (!empty($send_program))	{
			$send_program	= "\"" . $send_program . "\"";
			$virt[$selected_host]['send_program'] = $send_program;
		}

		if (!empty($expect_program)) {
			$expect_program	= "\"" . $expect_program . "\"";
			$virt[$selected_host]['expect_program'] = $expect_program;
		}
	}

	if ($gen_service == "BLANK SEND") {
		$send = "";
		if (!empty($selected_host)) {
			$virt[$selected_host]['send'] = "";

		}
	}
	if ($gen_service == "BLANK EXPECT") {
		$expect = "";
		if (!empty($selected_host)) {
			$virt[$selected_host]['expect'] = "";
		}
	}
	
	if ($gen_service == "NO SEND PROGRAM") {
		$start_cmd = "";
		if (!empty($selected_host)) {
			$virt[$selected_host]['send_program'] = "";
		}
	}	
	if ($gen_service == "NO EXPECT PROGRAM") {
		$stop_cmd = "";
		if (!empty($selected_host)) {
			$virt[$selected_host]['expect_program'] = "";
		}
	}	

?>


<P>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="virtual_edit_services.php">

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">

	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">Current text</TD>
		<TD CLASS="title">Replacement text</TD>
		<TD CLASS="title"></TD>
	</TR>

	<?php
		echo "<TD CLASS=title>Sending Program:</TD>";
		echo "<TD VALIGN=center><PRE>" . $virt[$selected_host]['send_program'] . "</PRE></TD>";
		echo "<TD><INPUT NAME=send_program SIZE=30 MAXLENGTH=255 VALUE=" . $virt[$selected_host]['send_program'] . "></TD>";
		echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"gen_service\" VALUE=\"NO SEND PROGRAM\"></TD>";
		echo "</TR>";
			
		echo "<TD CLASS=title>Send:</TD>";
		echo "<TD VALIGN=center><PRE>" . $virt[$selected_host]['send'] . "</PRE></TD>";
		echo "<TD><INPUT NAME=send SIZE=30 MAXLENGTH=255 VALUE=" . $virt[$selected_host]['send'] . "></TD>";
		echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"gen_service\" VALUE=\"BLANK SEND\"></TD>";
		echo "</TR>";
		
		echo "<TD CLASS=title>Expect:&nbsp;&nbsp;</TD>";	
		echo "<TD VALIGN=middle><PRE>" . $virt[$selected_host]['expect'] . "</PRE></TD>";
		echo "<TD><INPUT TYPE=TEXT NAME=expect SIZE=30 MAXLENGTH=255 VALUE=" . $virt[$selected_host]['expect'] . "></TD>";
		echo "<TD><INPUT TYPE=\"SUBMIT\" NAME=\"gen_service\" VALUE=\"BLANK EXPECT\"></TD>";
		echo "</TR>";

                $virt[$selected_host]['use_regex'] ? $checked="CHECKED" : $checked = '';
                echo "<TR><TD COLSPAN=\"4\" ALIGN=\"LEFT\">
                        <INPUT TYPE=\"CHECKBOX\" NAME=\"use_regex\" VALUE=\"1\" $checked> Treat expect string as a regular expression</TD></TR>";
	
	?>
</TABLE>
<P>
<TABLE>
	<TR>
		<TD VALIGN=TOP>Please note:</TD>
		<TD><P>
                    You may either use the simple send/expect mechanism built into piranha or a custom monitoring script (send program).
                    The send program takes priority over the send string. 

                    <P>
                    The send program should output a string matching the the expect string.  If the argument %h is used in the
                    send program command, it will be replaced with the ip address of the server to be checked.
                </TD>
        </TR>
</TABLE>
<!--
<TABLE>
	<TR>
		<TD VALIGN=TOP>Please note:</TD>
		<TD> There are two methods of checking that the service is running. Using a program and using plain text.
		     Plain text is useful for simple services like normal web services where you're not looking for a
		     complicated mechanism of detecting a working service.
		     <P>
		     For more advanced detection of services that require dynamically changing data (eg HTTPS or SSL) you
		     can optionally use the Sending Program field to have that service checked by an external program. If
		     the program field is used, the 'Send' field is depreciated and unused in the actual monitoring. An external
		     program SHOULD return some form of textual response for the expect field to be compared with.
		     <P>
		     Because the calling program will likely need to know the IP of the real server it needs to check, the
		     special token '%h' is used as a subsitute for all the IP's of the real servers.
		     eg '/usr/local/scripts/check_service %h' which would be replaced with the ip of each real server, one at a
		     time per invokation of the command. An example shell script is shown below.
		     <P>
<PRE>
/usr/local/scripts/check_service:
	#!/bin/sh
	# This script simply checks our own nameservers that it knows about itself
	# really dumb script, however this is all TCP/UDP communications that would
	# be extremely difficult to represent in piranha's textual 'Send' field
	# We use $1 as the argument in the TEST which will be the various IP's
	# of the real servers in the cluster.

	TEST=`dig -t soa redhat.com @$1 | grep -c ns.corp.redhat.com`

	if [ $TEST != "1" ]; then
	        echo "OK"
	else
	        echo "FAIL"
	fi
</PRE>
		     <P>
		     Message strings are limited to a maximum of 255 chars. Characters must be typical printable characters.
		     No binary, hex notation, or escaped characters. Case IS important! Also no wildcards are supported.
		</TD>
		     
	</TR>
</TABLE>
-->
<P>
<!-- should align beside the above table -->

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" BGCOLOR="#666666">
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="gen_service" VALUE="ACCEPT"></TD>
			<TD ALIGN=right><INPUT TYPE="SUBMIT" NAME="gen_service" VALUE="CANCEL"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

<?php 
	open_file ("w+"); write_config("");
?>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>

