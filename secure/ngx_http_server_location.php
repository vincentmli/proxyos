<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['http_server_location'])) && ($_GET['http_server_location'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: ngx_http_server.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['http_server_location'])) && ($_GET['http_server_location'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: ngx_http_server_location_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse_tengine.php');

	if ((isset($_GET['http_server_location'])) && ($_GET['http_server_location'] == "ADD")) {
		add_http_server_location($selected_host);
	}

	if ((isset($_GET['http_server_location'])) && ($_GET['http_server_location'] == "DELETE")) {
		$delete_service = "http_server_location";
		if ($debug) { echo "About to delete entry number selected_host $selected_host selected $selected<BR>"; }
		echo "<HR><H2>Click <A HREF=\"ngx_http_server_location.php?selected_host=$selected_host\" NAME=\"server_location\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("3", $selected_host, $selected, $delete_service);
		exit;
	}

	/* Umm,... just in case someone is dumb enuf to fiddle */
	if (empty($selected_host)) { $selected_host=0; }

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (HTTP Server Location )</TITLE>
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000"> HTTP SERVER LOCATION</FONT><BR>&nbsp;</TD>
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
                <A HREF="ngx_http_server.php" NAME="HTTP SERVER">HTTP VIRTUAL SERVER</A>
                &nbsp;|&nbsp;

                <A HREF="ngx_http_server_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="http_server">EDIT HTTP VIRTUAL SERVER</A>
                &nbsp;|&nbsp;

                <A HREF="ngx_http_server_location.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="location">LOCATION</A>
                &nbsp;|&nbsp;

	
		</TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http_server_location.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">match</TD>
		<TD CLASS="title">location</TD>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */
	echo "<INPUT TYPE=HIDDEN NAME=server_location VALUE=$selected_host>";

	$loop=0;

	foreach ($http_server[$selected_host]['location'] as $key => $value) {

		$temp = explode(" ", $value);

		$match = ""; $location = "";
		if(count($temp) < 2) {
			$location = $temp[0];
		} else {
			$match = $temp[0];
			$location = $temp[1];
		}

		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $key;  if ($key == $selected) { echo " CHECKED"; }; echo "></TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=match COLS=6 VALUE="; echo $match . ">";
		echo $match . "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=location COLS=6 VALUE="; echo $location . ">";
		echo $location . "</TD>";

		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="http_server_location" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_server_location" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_server_location" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_server_location" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="http_server_location" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
