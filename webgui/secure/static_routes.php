<?php
	$selected_host = "";
	$selected = "";

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['static_routes_service'])) && ($_GET['static_routes_service'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: static_routes.php?selected_host=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['static_routes_service'])) && ($_GET['static_routes_service'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: static_routes_edit.php?selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['static_routes_service'])) && ($_GET['static_routes_service'] == "ADD")) {
		add_static_routes();
	}

	if ((isset($_GET['static_routes_service'])) && ($_GET['static_routes_service'] == "DELETE")) {
		$delete_service = "static_routes";
		if ($debug) { echo "About to delete entry number $selected<BR>"; }
		echo "<HR><H2>Click <A HREF=\"static_routes.php?selected=$selected\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
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

<?php include 'name.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT STATIC ROUTES</FONT><BR>&nbsp;</TD>
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

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="static_routes.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">SOURCE IP</TD>
		<TD CLASS="title">DESTINATION NETWORK</TD>
		<TD CLASS="title">NETMASK</TD>
		<TD CLASS="title">GATEWAY</TD>
		<TD CLASS="title">INTERFACE</TD>
<?php //	<TD CLASS="title">NETMASK</TD> ?>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */

	$loop=1;

//	while ((isset($vrrp[$selected_host]['virtual_ipaddress'])) && ($vrrp[$selected_host]['virtual_ipaddress'] != "" )) {
	foreach ($static_routes as $route) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";
				
		$ips = explode(" ", $route);
		if ($ips[0] == "src") {
			if ($ips[2] == "to") {
				$srcip = $ips[1];
				$dst = explode("/", $ips[3]);
				$network = $dst[0];
				$netmask = $dst[1];
				$gateway = $ips[5];
				$interface = $ips[7];
			} else {
				$srcip = $ips[1];
				$dst = explode("/", $ips[2]);
				$network = $dst[0];
				$netmask = $dst[1];
				$gateway = $ips[4];
				$interface = $ips[6];
			}
		} else if ($ips[1] == "dev") {
			$srcip = "";
			$dst = explode("/", $ips[0]);
			$network = $dst[0];
			$netmask = $dst[1];	
			$gateway = "";
			$interface = $ips[2];
		} else {
			$srcip = "";
			$dst = explode("/", $ips[0]);
			$network = $dst[0];
			$netmask = $dst[1];
			$gateway = $ips[2];
			$interface = $ips[4];
		}

		echo "<TD><INPUT TYPE=HIDDEN NAME=srcip COLS=6 VALUE=";		echo $srcip	. ">";
		echo $srcip	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=network COLS=6 VALUE=";		echo $network	. ">";
		echo $network	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=netmask COLS=6 VALUE=";		echo $netmask	. ">";
		echo $netmask	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=gateway COLS=6 VALUE=";		echo $gateway	. ">";
		echo $gateway	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=interface COLS=6 VALUE=";		echo $interface	. ">";
		echo $interface	. "</TD>";

		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="static_routes_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="static_routes_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="static_routes_service" VALUE="EDIT"></TD>
		</TR>
</TABLE>


	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="static_routes_service" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
