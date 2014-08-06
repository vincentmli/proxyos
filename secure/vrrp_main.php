<?php
	$selected_host="";
	$vrrp_service="";

	if (isset($_POST['selected_host'])) {
        	$selected_host=$_POST['selected_host'];
	}
	if (isset($_POST['vrrp_service'])) {
		$vrrp_service=$_POST['vrrp_service'];
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ($vrrp_service == "EDIT") {
		/* Redirect browser to editing page */
		header("Location: vrrp_edit_vrrp.php?selected_host=$selected_host");
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
	}
// -->
</STYLE>

</HEAD>

<BODY BGCOLOR="#660000">

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD> <TD ALIGN=right CLASS="logo">
            <A HREF="introduction.html" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">VRRP INSTANCE</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php
	$vrrp_service = "";
	if (isset($_POST['vrrp_service'])) {
		$vrrp_service = $_POST['vrrp_service'];
	}

	if ($vrrp_service == "ADD") {
		
		add_vrrp(); /* append new data */
		
	}
	if ($vrrp_service == "DELETE" ) {
		$delete_service = "vrrp";
		/* if ($debug) { echo "About to delete entry number $selected_host<BR>"; } */
		echo "</TD></TR></TABLE><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"5\"><TR><TD BGCOLOR=\"ffffff\"><HR><H2><FONT COLOR=\"#cc0000\" CLASS=\"title\">Click <A HREF=\"virtual_main.php\" NAME=\"Virtual\">HERE</A> for refresh</FONT></H2><HR></TD></TR></TABLE>";
		open_file("w+");
		write_config("1", "", $selected_host, $delete_service);
		exit;
	}

/*

	if ($virtual_service == "(DE)ACTIVATE" ) {
		switch ($virt[$selected_host]['active']) {
			case ""		:	$virt[$selected_host]['active'] = "0";	break;
			case "0"	:	$virt[$selected_host]['active'] = "1";	break;
			case "1"	:	$virt[$selected_host]['active'] = "0";	break;
			default		:	$virt[$selected_host]['active'] = "0";	break;
		}
	}
*/
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="local_address_group.php" NAME="Local address group" CLASS="taboff"><B>SNAT ADDRESS GROUP</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="vrrp_main.php" NAME="VRRP instance" CLASS="taboff"><B>VRRP INSTANCE</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>
        </TR>
</TABLE>

<FORM METHOD="POST" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_main.php">

	<TABLE BORDER="1" CELLSPACING="2" CELLPADDING="6">
		<TR>
			<TD></TD>
			       	<TD CLASS="title">VRRP NAME</TD>
			       	<TD CLASS="title">STATE</TD>
  		              	<TD CLASS="title">PRIORITY</TD>
		</TR>

<?php
	$loop1 = 1;
	
	while (isset($vrrp_instance[$loop1]['vrrp_instance']) && $vrrp_instance[$loop1]['vrrp_instance'] != "") { /* for all virtual items... */

		/* lhh - this CONFIRM is never made by any form
		if ($virtual_action == "CONFIRM") { $virt[$loop1t]['protocol'] = $index; };
		 */

		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO	NAME=selected_host	VALUE=$loop1";
			if ($selected_host == "") { $selected_host = 1; }
			if ($loop1 == $selected_host) { echo " CHECKED "; }
			echo "> </TD>";
/*

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=status		SIZE=8	COLS=6	VALUE=";
			switch ($virt[$loop1]['active']) {
				case "0"	:	echo "Down><FONT COLOR=red>down</FONT>"; break;
				case "1"	:	echo "Up><FONT COLOR=blue>up</FONT>"; break;
				case "2"	:	echo "Active><FONT COLOR=green>active</FONT>"; break;
				default		:	echo "Undef><FONT COLOR=cyan>undef</FONT>"; break;
			}
	 		echo "</TD>";
*/

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=vrrp_instance		SIZE=16	COLS=10	VALUE="	. $vrrp_instance[$loop1]['vrrp_instance']	. ">";
		echo $vrrp_instance[$loop1]['vrrp_instance']	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=state		SIZE=16	COLS=10	VALUE="	. $vrrp_instance[$loop1]['state']	. ">";
		echo $vrrp_instance[$loop1]['state']	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=priority		SIZE=16	COLS=10	VALUE="	. $vrrp_instance[$loop1]['priority']	. ">";
		echo $vrrp_instance[$loop1]['priority']	. "</TD>";

		
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
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_service" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_service" VALUE="(DE)ACTIVATE"></TD>
		</TR>
	</TABLE>
	<P>
	Note: Use the radio button on the side to select which virtual service you wish to edit before selecting 'EDIT' or 'DELETE'
<?php // echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

<?php
	if ($vrrp_service != "DELETE") {
		open_file("w+");
		write_config("");
	}
?>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
