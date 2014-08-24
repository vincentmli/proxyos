<?php
	$selected_host = "";
	$selected = "";

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['static_ipaddress_service'])) && ($_GET['static_ipaddress_service'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: static_ipaddress.php?selected_host=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['static_ipaddress_service'])) && ($_GET['static_ipaddress_service'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: static_ipaddress_edit.php?selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['static_ipaddress_service'])) && ($_GET['static_ipaddress_service'] == "ADD")) {
		add_static_ipaddress();
	}

	if ((isset($_GET['static_ipaddress_service'])) && ($_GET['static_ipaddress_service'] == "DELETE")) {
		$delete_service = "static_ipaddress";
		if ($debug) { echo "About to delete entry number $selected<BR>"; }
		echo "<HR><H2>Click <A HREF=\"static_ipaddress.php?selected=$selected\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("1", "", $selected-1, $delete_service);
		exit;
	}

	/* Umm,... just in case someone is dumb enuf to fiddle */
	if (empty($selected)) { $selected=1; }

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

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
	    <A HREF="introduction.php" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT STATIC IPADDRESS</FONT><BR>&nbsp;</TD>
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

                <A HREF="global_settings.php" NAME="GLOBAL SETTING">GLOBAL SETTING</A>
                &nbsp;|&nbsp;

                <A HREF="global_notification_email.php" NAME="GLOBAL NOTIFICATION EMAIL">GLOBAL NOTIFICATION EMAIL</A>
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


<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="static_ipaddress.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">IP</TD>
		<TD CLASS="title">NETMASK</TD>
		<TD CLASS="title">INTERFACE</TD>
		<TD CLASS="title">SCOPE</TD>
<?php //	<TD CLASS="title">NETMASK</TD> ?>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */

	$loop=1;

//	while ((isset($vrrp[$selected_host]['virtual_ipaddress'])) && ($vrrp[$selected_host]['virtual_ipaddress'] != "" )) {
	foreach ($static_ipaddress as $ips) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";
				
		$string = explode(" ", $ips);
		if (isset($string[3]) && $string[3] == "scope") {
			$ipmask = explode("/", $string[0]);
			$ip = $ipmask[0];
			$netmask = $ipmask[1];
			$interface = $string[2];
			$scope = $string[4];
		} else {
			$ipmask = explode("/", $string[0]);
			$ip = $ipmask[0];
			$netmask = $ipmask[1];
			$interface = $string[2];
			$scope = "";
		}

		echo "<TD><INPUT TYPE=HIDDEN NAME=ip COLS=6 VALUE=";		echo $ip	. ">";
		echo $ip	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=netmask COLS=6 VALUE=";		echo $netmask	. ">";
		echo $netmask	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=interface COLS=6 VALUE=";		echo $interface	. ">";
		echo $interface	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=scope COLS=6 VALUE=";		echo $scope	. ">";
		echo $scope	. "</TD>";

		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="static_ipaddress_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="static_ipaddress_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="static_ipaddress_service" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="static_ipaddress_service" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="static_ipaddress_service" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
