<?php
        $edit_action = $_GET['edit_action'];
        $selected_host = $_GET['selected_host'];
        $selected = $_GET['selected'];
	if ($edit_action == "CANCEL") {
		header("Location: ngx_http_upstream_server.php?selected_host=$selected_host&selected=$selected");		
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $upstream;

	require('parse_tengine.php');

	if ($edit_action == "ACCEPT") {

                $server     =       $_GET['server'];
                $weight  =       $_GET['weight'];
                $max_fails  =       $_GET['max_fails'];
                $fail_timeout  =       $_GET['fail_timeout'];
                $backup  =       $_GET['backup'];
                $down  =       $_GET['down'];
		if($down =="yes") {
			$server = $server . " " . "down";
		} else {
			if($weight != "") {
				$server = $server . " " . "weight=$weight";
			}
			if($max_fails != "") {
				$server = $server . " " . "max_fails=$max_fails";
			}
			if($fail_timeout != "") {
				$server = $server . " " . "fail_timeout=$max_fails";
			}
			if($backup == "yes") {
				$server = $server . " " . "backup";
			}
		}
			
		$upstream[$selected_host]['server'][$selected] = $server;
		

		header("Location: ngx_http_upstream_server.php?selected_host=$selected_host&selected=$selected");		

	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual servers - Editing virtual server - Editing real server)</TITLE>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="showhide_health_check.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>
<STYLE TYPE="text/css">


TD      {
        font-family: helvetica, sans-serif;
        }
TD.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
        
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
                <TD WIDTH="60%">MENU:
                        <A HREF="ngx_http_upstream_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="upstream">EDIT UPSTREAM</A>
                &nbsp;|&nbsp;

                <A HREF="ngx_http_upstream_server.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="ups">UPSTREAM SERVER</A>
                &nbsp;|&nbsp;

                <A HREF="ngx_http_upstream_server_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="ups_edit">EDIT UPSTREAM SERVER</A>
                &nbsp;|&nbsp;
		

		</TD>
		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>


<FORM id="http_upstream_server_form" METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http_upstream_server_edit.php">


	<TABLE>

	<?php	
                $strings = explode(" ", $upstream[$selected_host]['server'][$selected]);
		$server = $strings[0];
                foreach ($strings as $value) {
                        if (strstr($value, "weight=")) {
                                $temp = explode("=", $value);
                                $weight = $temp[1];
				continue;
			}
                        if (strstr($value, "max_fails")) {
                                $temp = explode("=", $value);
                                $max_fails = $temp[1];
				continue;
                        }
			if (strstr($value, "fail_timeout")) {
                                $temp = explode("=", $value);
                                $fail_timeout = $temp[1];
				continue;
                        }
			if (strstr($value, "backup")) {
                                $backup = "yes";
				continue;
                        }
			if (strstr($value, "down")) {
                                $down = "yes";
				continue;
                        }
                }





		echo "<TR>";
			echo "<TD>server: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=server VALUE=\""; echo $server . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>weight: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=weight VALUE=\""; echo $weight . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>max fails: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=max_fails VALUE=\""; echo $max_fails . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>fail timeout: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=fail_timeout VALUE=\""; echo $fail_timeout . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>backup: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=backup VALUE=\""; echo $backup . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>down: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=down VALUE=\""; echo $down . "\""  . ">"; echo "</TD>";
		echo "</TR>";

	echo "</TABLE>";

	
		/* Welcome to the magic show */
		echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>";
		echo "<INPUT TYPE=HIDDEN NAME=selected VALUE=$selected>";
	?>
<P>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
		<TR BGCOLOR="#666666">
			<TD><INPUT TYPE="SUBMIT" NAME="edit_action" VALUE="ACCEPT"></TD>
			<TD ALIGN=right><INPUT TYPE="SUBMIT" NAME="edit_action" VALUE="CANCEL"></TD>
		</TR>
</TABLE>
<?php open_file ("w+"); write_config(""); ?>
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
