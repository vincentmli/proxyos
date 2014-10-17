<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: ngx_http_upstream.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: ngx_http_upstream.php");
		exit;
	}

	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $upstream;

	require('parse_tengine.php'); /* read in the config! Hurragh! */


	if ( $vev_action == "ACCEPT" ) {


		$upstream[$selected_host]['name'] 	= $_GET['name']; 
                $upstream[$selected_host]['lb']		= $_GET['lb'];

		$check = "check";
		if ($_GET['interval'] != "") {
			$check = $check . " interval=" . trim($_GET['interval']); 
		}
		if ($_GET['rise'] != "") {
			$check = $check . " rise=" . trim($_GET['rise']); 
		}
		if ($_GET['fall'] != "") {
			$check = $check . " fall=" . trim($_GET['fall']); 
		}
		if ($_GET['timeout'] != "") {
			$check = $check . " timeout=" . trim($_GET['timeout']); 
		}
		if ($_GET['type'] != "") {
			$check = $check . " type=" . trim($_GET['type']); 
		}
                $upstream[$selected_host]['check']			= $check;
                $upstream[$selected_host]['check_http_send']		= $_GET['send'];
                $upstream[$selected_host]['check_http_expect_alive']	= $_GET['expect_alive'];
                $upstream[$selected_host]['check_http_expect']		= $_GET['expect'];

                $upstream[$selected_host]['keepalive']			= $_GET['keepalive'];
                $upstream[$selected_host]['keepalive_timeout']		= $_GET['keepalive_timeout'];
	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>

<TITLE>Piranha (VRRP Instances - Editing VRRP instance)</TITLE>

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
	}
// -->
</STYLE>

</HEAD>

<BODY BGCOLOR="#660000">

<?php include 'name.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT UPSTREAM</FONT><BR>&nbsp;</TD>
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

        </TR>
</TABLE>

<FORM METHOD="GET" id="upstream_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http_upstream_edit.php">
<TABLE>
	<?php
		$interval; $rise; $fall; $timeout; $type;
		$name = $upstream[$selected_host]['name'] ;
		$lb = $upstream[$selected_host]['lb'] ;
		$send = $upstream[$selected_host]['check_http_send'] ;
		$expect_alive = $upstream[$selected_host]['check_http_expect_alive'] ;
		$expect = $upstream[$selected_host]['check_http_expect'] ;
		$check = explode(" ", $upstream[$selected_host]['check']);
		foreach ($check as $value) {
			if (strstr($value, "interval")) {
				$temp = explode("=", $value);
				$interval = $temp[1];
			} else if (strstr($value, "rise")) {
				$temp = explode("=", $value);
				$rise = $temp[1];
			} else if (strstr($value, "fall")) {
				$temp = explode("=", $value);
				$fall = $temp[1];
			} else if (strstr($value, "timeout")) {
				$temp = explode("=", $value);
				$timeout = $temp[1];
			} else if (strstr($value, "type")) {
				$temp = explode("=", $value);
				$type = $temp[1];
			}
		}
	?>
		
	<TR>
		<TD>name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="name" VALUE= <?php echo $name; ?>></TD>
	</TR>
	<TR>
		<TD>load balance:</TD>
		<TD><INPUT TYPE="TEXT" NAME="lb" VALUE= <?php echo $lb; ?>></TD>
	</TR>
	<TR>
		<TD>check interval:</TD>
		<TD><INPUT TYPE="TEXT" NAME="interval" VALUE=" <?php echo $interval; ?>"></TD>
	</TR>
	<TR>
		<TD>check rise:</TD>
		<TD><INPUT TYPE="TEXT" NAME="rise" VALUE=" <?php echo $rise; ?>"></TD>
	</TR>
	<TR>
		<TD>check fall:</TD>
		<TD><INPUT TYPE="TEXT" NAME="fall" VALUE=<?php echo $fall; ?>></TD>
	</TR>
	<TR>
		<TD>check timeout:</TD>
		<TD><INPUT TYPE="TEXT" NAME="timeout" VALUE=<?php echo $timeout; ?>></TD>
	</TR>
	<TR>
		<TD>check type:</TD>
		<TD><INPUT TYPE="TEXT" NAME="type" VALUE=<?php echo $type ?>></TD>
	</TR>
	<TR>
		<TD style="width:10%">send:</TD>
		<TD><INPUT TYPE="TEXT" style="width:140%" NAME="send" VALUE="<?php echo htmlspecialchars($send, ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD style="width:20%">expect alive:</TD>
		<TD><INPUT TYPE="TEXT" NAME="expect_alive" VALUE="<?php echo htmlspecialchars($expect_alive, ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD style="width:10%">expect:</TD>
		<TD><INPUT TYPE="TEXT" style="width:40%" NAME="expect" VALUE="<?php echo htmlspecialchars($expect, ENT_QUOTES) ?>"></TD>
	</TR>


</TABLE>
<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD>
                        <INPUT TYPE="Submit" NAME="vev_action" VALUE="ACCEPT">  
                        <SPAN CLASS="taboff">-- Click here to apply changes to this page</SPAN>
                </TD>
                <TD>
                        <INPUT TYPE="Submit" NAME="vev_action" VALUE="CANCEL">  
                        <SPAN CLASS="taboff">-- Click here to cancel the changes</SPAN>
                </TD>
        </TR>
</TABLE>

<?php open_file ("w+"); write_config(""); ?>
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
