<?php
	$selected_host = "";
	$selected = "";
	
	if (isset($_GET['selected_host'])) {
		$selected_host = $_GET['selected_host'];
	}

	if (isset($_GET['selected'])) {
		$selected = $_GET['selected'];
	}

	if ((isset($_GET['http_upstream_server'])) && ($_GET['http_upstream_server'] == "CANCEL")) {
		/* Redirect browser to editing page */
		header("Location: ngx_http_upstream.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ((isset($_GET['http_upstream_server'])) && ($_GET['http_upstream_server'] == "EDIT")) {
		/* Redirect browser to editing page */
		header("Location: ngx_http_upstream_server_edit.php?selected_host=$selected_host&selected=$selected");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	
	require('parse_tengine.php');

	if ((isset($_GET['http_upstream_server'])) && ($_GET['http_upstream_server'] == "ADD")) {
		add_http_upstream_server($selected_host);
	}

	if ((isset($_GET['http_upstream_server'])) && ($_GET['http_upstream_server'] == "DELETE")) {
		$delete_service = "upstream_server";
		if ($debug) { echo "About to delete entry number selected_host $selected_host selected $selected<BR>"; }
		echo "<HR><H2>Click <A HREF=\"ngx_http_upstream_server.php?selected_host=$selected_host\" NAME=\"upstream_server\">HERE</A></TD> for refresh</H2><HR>";
		open_file("w+");
		write_config("2", $selected_host, $selected, $delete_service);
		exit;
	}

	/* Umm,... just in case someone is dumb enuf to fiddle */
	if (empty($selected_host)) { $selected_host=0; }

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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT HTTP UPSTREAM SERVER</FONT><BR>&nbsp;</TD>
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
	                <A HREF="ngx_http_upstream_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="upstream">UPSTREAM EDIT</A>
                &nbsp;|&nbsp;

                <A HREF="ngx_http_upstream_server.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="upstream server">UPSTREAM SERVER</A>
                &nbsp;|&nbsp;
	
		</TD>

		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http_upstream_server.php">

<TABLE WIDTH="70%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR>
		<TD CLASS="title">&nbsp;</TD>
		<TD CLASS="title">server</TD>
		<TD CLASS="title">weight</TD>
		<TD CLASS="title">max_fails</TD>
		<TD CLASS="title">fail_timeout</TD>
		<TD CLASS="title">backup</TD>
		<TD CLASS="title">down</TD>
	</TR>

<!-- Somehow dynamically generated here -->
	

	<?php
	/* magic */
	echo "<INPUT TYPE=HIDDEN NAME=upstream_server VALUE=$selected_host>";

	$loop=0;

	foreach ($upstream[$selected_host]['server'] as $key => $server) {

		$temp = explode(" ", $server);

		$weight = ""; $max_fails = ""; $fail_timeout = ""; $backup = 'no'; $down = 'no';
		foreach ($temp as $value) {
                	if (strstr($value, "weight=")) {
				$temp1 = explode("=", $value);
                     		$weight = $temp1[1];
				continue;
			}
                	if (strstr($value, "max_fails=")) {
				$temp1 = explode("=", $value);
                     		$max_fails = $temp1[1];
				continue;
			}
                	if (strstr($value, "fail_timeout=")) {
				$temp1 = explode("=", $value);
                     		$fail_timeout = $temp1[1];
				continue;
			}
                	if (strstr($value, "backup")) {
                     		$backup = 'yes';
				continue;
			}
                	if (strstr($value, "down")) {
                     		$down = 'yes';
				continue;
			}
		}


		echo "<TR>";
		echo "<TD><INPUT TYPE=RADIO NAME=selected VALUE=" . $key;  if ($key == $selected) { echo " CHECKED"; }; echo "></TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=server COLS=6 VALUE="; echo $key . ">";
		echo $key . "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=weight COLS=6 VALUE="; echo $weight . ">";
		echo $weight	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=max_fails COLS=6 VALUE="; echo $max_fails . ">";
		echo $max_fails	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=fail_timeout COLS=6 VALUE="; echo $fail_timeout . ">";
		echo $fail_timeout	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=backup COLS=6 VALUE="; echo $backup . ">";
		echo $backup	. "</TD>";

		echo "<TD><INPUT TYPE=HIDDEN NAME=down COLS=6 VALUE="; echo $down . ">";
		echo $down	. "</TD>";


		echo "</TR>";
	
	$loop++;
	}
	echo "</TABLE>";

	?>
	

<!-- end of dynamic generation -->



<!-- should align beside the above table -->

<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="http_upstream_server" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_upstream_server" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_upstream_server" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="http_upstream_server" VALUE="(DE)ACTIVATE"></TD>
		</TR>
</TABLE>


<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5" BGCOLOR="#666666"> 
		<TR> 
			<TD ALIGN="right">
				<INPUT TYPE="SUBMIT" NAME="http_upstream_server" VALUE="CANCEL">
			</TD>
		</TR>
	</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</FORM>
</TD> </TR> </TABLE>
</BODY>
</HTML>
