<?php
	$selected_ip="";
	$ip_service="";

	if (isset($_POST['selected_ip'])) {
        	$selected_ip=$_POST['selected_ip'];
	}
	if (isset($_POST['ip_service'])) {
		$ip_service=$_POST['ip_service'];
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ($ip_service == "EDIT") {
		/* Redirect browser to editing page */
		header("Location: static_ipaddress_edit.php?selected_ip=$selected_ip");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Static IPaddress)</TITLE>
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
	}
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">STATIC IP ADDRESSES</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php
	$ip_service = "";
	if (isset($_POST['ip_service'])) {
		$ip_service = $_POST['ip_service'];
	}

	if ($ip_service == "ADD") {
		
		add_staticip(); /* append new data */
		
	}
	if ($ip_service == "DELETE" ) {
		$delete_service = "ip";
		/* if ($debug) { echo "About to delete entry number $selected_ip<BR>"; } */
		echo "</TD></TR></TABLE><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"5\"><TR><TD BGCOLOR=\"ffffff\"><HR><H2><FONT COLOR=\"#cc0000\" CLASS=\"title\">Click <A HREF=\"static_ipaddress.php\" NAME=\"Static ipaddress\">HERE</A> for refresh</FONT></H2><HR></TD></TR></TABLE>";
		open_file("w+");
		write_config("1", "", $selected_ip, $delete_service);
		exit;
	}

?>

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

                <A HREF="global_settings.php" NAME="GLOBAL SETTING">GLOBAL SETTING</A>
                &nbsp;|&nbsp;

                <A HREF="static_ipaddress.php" CLASS="tabon" NAME="STATIC IPADDRESS">STATIC IPADDRESS</A>
                &nbsp;|&nbsp;

                <A HREF="static_routes.php" CLASS="tabon" NAME="STATIC ROUTES">STATIC ROUTES</A>
                &nbsp;|&nbsp;

                <A HREF="local_address_group_main.php" NAME="SNAT ADDRESS GROUP">SNAT ADDRESS GROUP</A>
                &nbsp;|&nbsp;

                </TD>

                <!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>


<FORM METHOD="POST" ENCTYPE="application/x-www-form-urlencoded" ACTION="static_ipaddress.php">

	<TABLE BORDER="1" CELLSPACING="2" CELLPADDING="6">
		<TR>
			<TD></TD>
				<TD CLASS="title">IP</TD>
				<TD CLASS="title">NETMASK</TD>
                		<TD CLASS="title">INTERFACE</TD>
		</TR>

<?php

	$loop1 = 1;
	
	while (isset($static_ipaddress[$loop1]['ip']) && $static_ipaddress[$loop1]['ip'] != "") { /* for all virtual items... */

		/* lhh - this CONFIRM is never made by any form
		if ($virtual_action == "CONFIRM") { $virt[$loop1t]['protocol'] = $index; };
		 */

		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO	NAME=selected_ip	VALUE=$loop1";
			if ($selected_ip == "") { $selected_ip = 1; }
			if ($loop1 == $selected_ip) { echo " CHECKED "; }
			echo "> </TD>";


		echo "<TD><INPUT TYPE=HIDDEN 	NAME=ip		SIZE=16	COLS=10	VALUE="	. $static_ipaddress[$loop1]['ip']	. ">";
		echo $static_ipaddress[$loop1]['ip']	. "</TD>";

		if (isset($static_ipaddress[$loop1]['mask'])) {
			$temp = $static_ipaddress[$loop1]['mask'];
		} else {
			$temp = "0";
		}
		$mask = CIDRtoMask($temp);
		echo "<TD><INPUT TYPE=HIDDEN 	NAME=mask		SIZE=16	COLS=10	VALUE="	. $mask . ">";

		if ($temp == "0") {
			echo "Unused</TD>";
		} else {
			echo $mask	. "</TD>";
		}

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=interface	SIZE=16	COLS=10	VALUE="	. $static_ipaddress[$loop1]['dev']	. ">";
		echo $static_ipaddress[$loop1]['dev']	. "</TD>";
		echo "</TR>";
		$loop1++;
	}
?>
	<!-- end of dynamic generation -->

	</TABLE>
	<BR>

	<P>
	<!-- should align beside the above table -->

	<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="ip_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="ip_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="ip_service" VALUE="EDIT"></TD>
		</TR>
	</TABLE>
	<P>
	Note: Use the radio button on the side to select which virtual service you wish to edit before selecting 'EDIT' or 'DELETE'
<?php // echo "<INPUT TYPE=HIDDEN NAME=selected_ip VALUE=$selected_ip>" ?>

<?php
	if ($ip_service != "DELETE") {
		open_file("w+");
		write_config("");
	}
?>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
