<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['real_service'])) && ($_GET['real_service'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: virtual_edit_virt.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['real_service'])) && ($_GET['real_service'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: virtual_edit_real_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['real_service'])) && ($_GET['real_service'] == "ADD")) {
		add_service($selected_host);
	}

	if ((isset($_GET['real_service'])) && ($_GET['real_service'] == "DELETE")) {
		$delete_service = "server";
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "<HR><H2>Click <A HREF=\"virtual_edit_real.php?selected_host=$selected_host\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("2", $selected_host, $selected, $delete_service);
		exit;
	}

	if ((isset($_GET['real_service'])) && ($_GET['real_service'] == "(DE)ACTIVATE")) {
		switch ($serv[$selected_host][$selected]['active']) {
			case	""	:	$serv[$selected_host][$selected]['active'] = "0"; break;
			case	"0"	:	$serv[$selected_host][$selected]['active'] = "1"; break;
			case	"1"	:	$serv[$selected_host][$selected]['active'] = "0"; break;
			default		:	$serv[$selected_host][$selected]['active'] = "0"; break;
		}
	}

	/* Umm,... just in case someone is dumb enuf to fiddle */
	if (empty($selected_host)) { $selected_host=1; }

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual Servers - Editing real server)</TITLE>
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

<?php include 'name.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT REAL SERVER</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

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

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="virtual_edit_real.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">STATUS</TD>
		<TD CLASS="title">IP</TD>
		<TD CLASS="title">PORT</TD>
<?php //	<TD CLASS="title">NETMASK</TD> ?>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */
	echo "<INPUT TYPE=HIDDEN NAME=virtual VALUE=$selected_host>";

	$loop=1;

	while ((isset($serv[$selected_host][$loop]['ip'])) && ($serv[$selected_host][$loop]['ip'] != "" )) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";
		echo "<TD><INPUT TYPE=HIDDEN NAME=active COLS=6 VALUE=";
			switch ($serv[$selected_host][$loop]['active']) {
				case "0"	:	echo "down><FONT COLOR=red>Down</FONT>";	break;
				case "1"	:	echo "up><FONT COLOR=blue>up</FONT>";		break;
				case "2"	:	echo "Active><FONT COLOR=green></FONT>";	break;
				default		:	$serv[$selected_host][$loop]['active'] = "0";
							echo "down><FONT COLOR=red></FONT>";		break;
			}
				
		echo "</TD>";
		echo "<TD><INPUT TYPE=HIDDEN NAME=ip COLS=6 VALUE=";		echo $serv[$selected_host][$loop]['ip']	. ">";
		echo $serv[$selected_host][$loop]['ip']	. "</TD>";
		echo "<TD><INPUT TYPE=HIDDEN NAME=port COLS=6 VALUE=";		echo $serv[$selected_host][$loop]['port']	. ">";
		echo $serv[$selected_host][$loop]['port']	. "</TD>";
//		if (empty($serv[$selected_host][$loop]['nmask']) || $serv[$selected_host][$loop]['nmask'] == "0.0.0.0") {
//			echo "<TD>Unused</TD>";
//		} else {
//			echo "<TD><INPUT TYPE=HIDDEN NAME=netmask COLS=10 VALUE=";	echo $serv[$selected_host][$loop]['nmask']	. ">";
//			echo $serv[$selected_host][$loop]['nmask']	. "</TD>";
//		}
		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="real_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="real_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="real_service" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="real_service" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="real_service" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
