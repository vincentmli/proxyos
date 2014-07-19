<?php
	/* Some magic used to allow the edit command to pull up another web page */
	if ($failover_service == "EDIT") {
		/* Redirect browser to editing page */
		header("Location: failover_edit_virt.php?selected_host=$selected_host");
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
<TITLE>Piranha (Failover)</TITLE>
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
	if ($failover_service == "ADD") {
		add_failover(); /* append new data */
	}

	if ($failover_service == "DELETE" ) {
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "</TD></TR></TABLE><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"5\"><TR><TD BGCOLOR=\"ffffff\"><HR><H2><FONT COLOR=\"#cc0000\" CLASS=\"title\">Click <A HREF=\"failover_main.php\" NAME=\"Failover\">HERE</A> for refresh</FONT></H2><HR></TD></TR></TABLE>";
		open_file("w+");
		write_config("1", "", $selected_host);
		exit;
	}

	if ($failover_service == "(DE)ACTIVATE" ) {
		switch ($fail[$selected_host]['active']) {
			case ""		:	$fail[$selected_host]['active'] = "0";	break;
			case "0"	:	$fail[$selected_host]['active'] = "1";	break;
			case "1"	:	$fail[$selected_host]['active'] = "0";	break;
			default		:	$fail[$selected_host]['active'] = "0";	break;
		}
	}

	// echo "Query = $QUERY_STRING";
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
		<TD WIDTH="25%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="failover_main.php" NAME="Failover" CLASS="tabon"><B>FAILOVER</B></A> </TD>
        </TR>
</TABLE>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="failover_main.php">

	<TABLE BORDER="1" CELLSPACING="2" CELLPADDING="6">
		<TR>
			<TD></TD>
			       	<TD CLASS="title">STATUS</TD>
  		              	<TD CLASS="title">NAME</TD>
   		             	<TD CLASS="title">VIP</TD>
   		             	<TD CLASS="title">NETMASK</TD>
   		             	<TD CLASS="title">PORT</TD>
   		             	<TD CLASS="title">INTERFACE</TD>
                </TR>

	<!-- Somehow dynamically generated here -->

<?php
	$loop1 = 1;

	while ($fail[$loop1]['failover'] != "") { /* for all virtual items... */

		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO	NAME=selected_host	VALUE=$loop1";
			if ($selected_host == "") { $selected_host = 1; }
			if ($loop1 == $selected_host) { echo " CHECKED "; }
			echo "> </TD>";

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=status		SIZE=8	COLS=6	VALUE=";
			switch ($fail[$loop1]['active']) {
				case "0"	:	echo "Down><FONT COLOR=red>down</FONT>";	break;
				case "1"	:	echo "Up><FONT COLOR=blue>up</FONT>";		break;
				case "2"	:	echo "Active><FONT COLOR=green>active</FONT>";	break;
				default		:	echo "Undef><FONT COLOR=cyan>undef</FONT>";	break;
			}
	 		echo "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=name	SIZE=16	COLS=10	VALUE="	. $fail[$loop1]['failover']	. ">";
		echo $fail[$loop1]['failover']	. "</TD>";
		
		
		$pieces = explode(" ", $fail[$loop1]['address']);
		
		
		echo "<TD><INPUT TYPE=HIDDEN 	NAME=address	SIZE=16	COLS=10	VALUE="	. $pieces[0]	. ">";
		echo $pieces[0]	. "</TD>";
		

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=vip_nmask	SIZE=16	COLS=10	VALUE="	. $fail[$loop1]['vip_nmask']	. ">";
		echo $fail[$loop1]['vip_nmask']	. "</TD>";
		
		echo "<TD><INPUT TYPE=HIDDEN 	NAME=port	SIZE=6	COLS=10	VALUE="	. $fail[$loop1]['port']		. ">";
		if ($fail[$loop1]['port']) {
			echo  $fail[$loop1]['port']	. "</TD>";
		} else {
			echo "n/a</TD>";
		}

		echo "<TD><INPUT TYPE=HIDDEN 	NAME=interface	SIZE=16	COLS=10	VALUE="	. $pieces[1]	. ">";
		echo $pieces[1]	. "</TD>";

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
			<TD><INPUT TYPE="SUBMIT" NAME="failover_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="failover_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="failover_service" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="failover_service" VALUE="(DE)ACTIVATE"></TD>
		</TR>
	</TABLE>
	<P>
	Note: Use the radio button on the side to select which virtual service you wish to edit before selecting 'EDIT' or 'DELETE'
<?php // echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>
<?php if ($failover_service != "DELETE") { open_file("w+"); write_config(""); } ?>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
