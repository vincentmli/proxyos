<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['vrrp_virtual_ipaddress_excluded'])) && ($_GET['vrrp_virtual_ipaddress_excluded'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: vrrp_edit_vrrp.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['vrrp_virtual_ipaddress_excluded'])) && ($_GET['vrrp_virtual_ipaddress_excluded'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: vrrp_edit_virtual_ipaddress_excluded_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['vrrp_virtual_ipaddress_excluded'])) && ($_GET['vrrp_virtual_ipaddress_excluded'] == "ADD")) {
		add_vrrp_virtual_ipaddress_excluded($selected_host);
	}

	if ((isset($_GET['vrrp_virtual_ipaddress_excluded'])) && ($_GET['vrrp_virtual_ipaddress_excluded'] == "DELETE")) {
		$delete_service = "vrrp_virtual_ipaddress_excluded";
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "<HR><H2>Click <A HREF=\"vrrp_edit_virtual_ipaddress_excluded.php?selected_host=$selected_host\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("2", $selected_host, $selected-1, $delete_service);
		exit;
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP VIRTUAL IPADDRESS EXCLUDED</FONT><BR>&nbsp;</TD>
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
		
		<A HREF="vrrp_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="INSTANCE">VRRP INSTANCE</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VIRTUAL IPADDRESS">VIRTUAL IPADDRESS</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_unicast_peer.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP UNICAST PEER">UNICAST PEER</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress_excluded.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VIRTUAL IPADDRESS EXCLUDED">VIRTUAL IPADDRESS EXCLUDED</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_routes.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL ROUTES">VIRTUAL ROUTES</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_interface.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK INTERFACE">TRACK INTERFACE</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_script.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK SCRIPT">TRACK SCRIPT</A>
                &nbsp;|&nbsp;

		</TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_edit_virtual_ipaddress_excluded.php">

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
	echo "<INPUT TYPE=HIDDEN NAME=vrrp VALUE=$selected_host>";

	$loop=1;

//	while ((isset($vrrp[$selected_host]['virtual_ipaddress'])) && ($vrrp[$selected_host]['virtual_ipaddress'] != "" )) {
	foreach ($vrrp_instance[$selected_host]['virtual_ipaddress_excluded'] as $ips) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";
				
		$string = explode(" ", $ips);
		if (isset($string[3]) && $string[3] == "scope") {
			$ipnetmask = explode("/", $string[0]);
			$ip = $ipnetmask[0];
			$netmask = $ipnetmask[1];
			$interface = $string[2]; 
			$scope = $string[4];
		} else {	
			$ipnetmask = explode("/", $string[0]);
			$ip = $ipnetmask[0];
			$netmask = $ipnetmask[1];
			$interface = $string[2];
			$scope = "";
		}

		echo "<TD><INPUT TYPE=HIDDEN NAME=ip COLS=6 VALUE=";	echo $ip	. ">";
		echo $ip	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=netmask COLS=6 VALUE=";	echo $netmask	. ">";
		echo $netmask	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=interface COLS=6 VALUE=";	echo $interface	. ">";
		echo $interface	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=scope COLS=6 VALUE=";	echo $scope	. ">";
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
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_virtual_ipaddress_excluded" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_virtual_ipaddress_excluded" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_virtual_ipaddress_excluded" VALUE="EDIT"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="vrrp_virtual_ipaddress_excluded" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
