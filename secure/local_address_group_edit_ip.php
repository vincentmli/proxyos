<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['local_address_group_ip'])) && ($_GET['local_address_group_ip'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: local_address_group_edit_group.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['local_address_group_ip'])) && ($_GET['local_address_group_ip'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: local_address_group_edit_ip_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse.php');

	if ((isset($_GET['local_address_group_ip'])) && ($_GET['local_address_group_ip'] == "ADD")) {
		add_local_address_group_ip($selected_host);
	}

	if ((isset($_GET['local_address_group_ip'])) && ($_GET['local_address_group_ip'] == "DELETE")) {
		$delete_service = "local_address_group_ip";
		if ($debug) { echo "About to delete entry number $selected_host<BR>"; }
		echo "<HR><H2>Click <A HREF=\"local_address_group_edit_ip.php?selected_host=$selected_host\" NAME=\"Virtual\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("1", $selected_host, $selected-1, $delete_service);
		exit;
	}

	if ((isset($_GET['local_address_group_ip'])) && ($_GET['local_address_group_ip'] == "(DE)ACTIVATE")) {
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT SNAT ADDRESS GROUP IP</FONT><BR>&nbsp;</TD>
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
		
		<A HREF="local_address_group_main.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="LOCAL ADDRESS GROUP">SNAT ADDRESS GROUP</A>
		&nbsp;|&nbsp;

		<A HREF="local_address_group_edit_group.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="LOCAL ADDRESS GROUP NAME">SNAT ADDRESS GROUP NAME</A>
		&nbsp;|&nbsp;

                <A HREF="local_address_group_edit_ip.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="LOCAL ADDRESS GROUP IP">SNAT ADDRESS GROUP IP</A>
                &nbsp;|&nbsp;


		</TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="local_address_group_edit_ip.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">IP</TD>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */
	echo "<INPUT TYPE=HIDDEN NAME=local_address_group VALUE=$selected_host>";

	$loop=1;

	foreach ($local_address_group[$selected_host]['ip'] as $ip) {
		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $loop; if ($selected == "" ) { $selected = 1; }; if ($loop == $selected) { echo " CHECKED"; }; echo "></TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=ip COLS=6 VALUE=";	echo $ip	. ">";
		echo $ip	. "</TD>";

		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="local_address_group_ip" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="local_address_group_ip" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="local_address_group_ip" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="local_address_group_ip" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="local_address_group_ip" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
