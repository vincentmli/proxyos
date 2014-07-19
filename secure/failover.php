<?php
	/* Some magic used to allow the edit command to pull up another web page */
	if ($virtual_service == "EDIT") {
		/* Redirect browser to editing page */;
		header("Location: virtual_edit_virt.php?selected_host=$selected_host")
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

	//print_arrays(); /* after */

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual Servers)</TITLE>
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
          <A HREF="introduction.html" CLASS="logolink">
          INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
          HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">FAILOVER</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">



<?php
	if ($virtual_service == "ADD") {
		add_virtual(); /* append new data */
	}
	if ($virtual_service == "DELETE" ) {
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "</TD></TR></TABLE><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"5\"><TR><TD BGCOLOR=\"ffffff\"><HR><H2><FONT COLOR=\"#cc0000\" CLASS=\"title\">Click <A HREF=\"virtual_main.php\" NAME=\"Virtual\">HERE</A> for refresh</FONT></H2><HR></TD></TR></TABLE>";
		open_file("w+");
		write_config("1", "", $selected_host);
		exit;
	}

	if ($virtual_service == "(DE)ACTIVATE" ) {
		switch ($virt[$selected_host]['active']) {
			case ""		:	$virt[$selected_host]['active'] = "0";	break;
			case "0"	:	$virt[$selected_host]['active'] = "1";	break;
			case "1"	:	$virt[$selected_host]['active'] = "0";	break;
			default		:	$virt[$selected_host]['active'] = "0";	break;
		}
	}

	// echo "Query = $QUERY_STRING";
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="20%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
		<TD WIDTH="20%" ALIGN="CENTER"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="taboff"><B>VIRTUAL SERVERS</B></A> </TD>
 		<TD WIDTH="20%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="failover.php" NAME="Failover" CLASS="tabon"><B>FAILOVER</B></A> </TD>
       </TR>
</TABLE>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="failover.php">

<P>
<SPAN CLASS=title>WARNING</SPAN>
<BR>
You cannot currently use these services and virtual servers in the same setup, if you do, any configuration you had for virtual servers will be lost.<BR>
You must have at least a 2 node cluster (a primary and backup) in order to use these failover services.<BR>
All nodes must be identically configured Linux systems.
<P>
Failover services provide the most basic form of fault recovery. If any of the services on the active node fail, all of the services will be
shutdown and restarted on a backup node. Services defined here will automatically be started & stopped by LVS, so a backup node is
considered a "warm" standby. This is due to a technical restriction that a service can only be operational on one node at a time, otherwise it may
fail to bind to a virtual IP address that does not yet exist on that system or cause a networking conflict with the active service. The
commands provided for "Initiation command" and "Stop command" must work the same for all nodes. Multiple services can be defined.
<HR>

<TABLE>
	<TR>
		<TD>Name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="hostname" VALUE= <?php echo $fail[$selected_host]['failover'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Address: </TD>
		<TD><INPUT TYPE="TEXT" NAME="address" VALUE=<?php echo $fail[$selected_host]['address'] ?>></TD>
	</TR>
	<TR>
		<TD>Device: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="device" VALUE=<?php echo $prim['nat_device'] ?>></TD>
	</TR>
	<TR>

		<TD>Application port:</TD>
		<TD><INPUT TYPE="TEXT" NAME="port" VALUE=<?php echo  $fail[$selected_host]['port'] ?>></TD></TD>
	</TR>
	<TR>
		<TD>Timeout: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $fail[$selected_host]['timeout'] ?>></TD>
	</TR>

	<TR>
		<TD>Send:</TD>
		<TD> <INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $fail[$selected_host]['timeout'] ?>></TD>
	</TR>
	<TR>
		<TD>Expect:</TD>
		<TD> <INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $fail[$selected_host]['timeout'] ?>></TD>
	</TR>
	<TR>
		<TD>Initiation command:</TD>
		<TD> <INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $fail[$selected_host]['timeout'] ?>></TD>
	</TR>
	<TR>
		<TD>Disable command:</TD>
		<TD> <INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $fail[$selected_host]['timeout'] ?>></TD>
	</TR>	
</TABLE>

</FORM>
</TD></TR></TABLE>
<?php  open_file("w+"); write_config(""); ?>
</BODY>
</HTML>
