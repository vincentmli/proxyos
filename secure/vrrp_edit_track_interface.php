<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['vrrp_track_interface'])) && ($_GET['vrrp_track_interface'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: vrrp_edit_vrrp.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['vrrp_track_interface'])) && ($_GET['vrrp_track_interface'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: vrrp_edit_track_interface_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['vrrp_track_interface'])) && ($_GET['vrrp_track_interface'] == "ADD")) {
		add_vrrp_track_interface($selected_host);
	}

	if ((isset($_GET['vrrp_track_interface'])) && ($_GET['vrrp_track_interface'] == "DELETE")) {
		$delete_service = "vrrp_track_interface";
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "<HR><H2>Click <A HREF=\"vrrp_edit_track_interface.php?selected_host=$selected_host\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("2", $selected_host, $selected-1, $delete_service);
		exit;
	}

	if ((isset($_GET['vrrp_track_interface'])) && ($_GET['vrrp_track_interface'] == "(DE)ACTIVATE")) {
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP TRACK INTERFACE</FONT><BR>&nbsp;</TD>
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
		
		<A HREF="vrrp_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VRRP INSTANCE">INSTANCE</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP VIRTUAL IPADDRESS">VIRTUAL IPADDRESS</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress_excluded.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP VIRTUAL IPADDRESS EXCLUDED">VIRTUAL IPADDRESS EXCLUDED</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_routes.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VRRP VIRTUAL ROUTES">VIRTUAL ROUTES</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_interface.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VRRP TRACK INTERFACE">TRACK INTERFACE</A>
                &nbsp;|&nbsp;

		</TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_edit_track_interface.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">INTERFACE</TD>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */
	echo "<INPUT TYPE=HIDDEN NAME=vrrp VALUE=$selected_host>";

	$loop=1;

	foreach ($vrrp_instance[$selected_host]['track_interface'] as $interface) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=interface COLS=6 VALUE=";	echo $interface	. ">";
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
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_track_interface" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_track_interface" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_track_interface" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="vrrp_track_interface" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="vrrp_track_interface" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
