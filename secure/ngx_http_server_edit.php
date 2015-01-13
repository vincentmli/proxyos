<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: ngx_http_server.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: ngx_http_server.php");
		exit;
	}

	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $http_server;

	require('parse_tengine.php'); /* read in the config! Hurragh! */


	if ( $vev_action == "ACCEPT" ) {


		$http_server[$selected_host]['listen'] 	= $_GET['listen']; 
		$http_server[$selected_host]['ssl'] 	= $_GET['ssl']; 
		$http_server[$selected_host]['ssl_protocols'] 	= $_GET['ssl_protocols']; 
		$http_server[$selected_host]['ssl_ciphers'] 	= $_GET['ssl_ciphers']; 

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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT HTTP VIRTUAL SERVER</FONT><BR>&nbsp;</TD>
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

        </TR>
</TABLE>

<FORM METHOD="GET" id="http_server_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http_server_edit.php">
<TABLE>
	<?php
		$listen = $http_server[$selected_host]['listen'] ;
		$ssl = $http_server[$selected_host]['ssl'] ;
		$ssl_protocols = $http_server[$selected_host]['ssl_protocols'] ;
		$ssl_ciphers = $http_server[$selected_host]['ssl_ciphers'] ;
	?>
		
	<TR>
		<TD>listen:</TD>
		<TD><INPUT TYPE="TEXT" NAME="listen" VALUE="<?php echo $listen; ?>"></TD>
	</TR>
	<TR>
		<TD>ssl:</TD>
		<TD><INPUT TYPE="TEXT" NAME="ssl" VALUE="<?php echo $ssl; ?>"></TD>
	</TR>
	<TR>
		<TD>ssl protocols:</TD>
		<TD><INPUT TYPE="TEXT" NAME="ssl_protocols" VALUE="<?php echo $ssl_protocols; ?>"></TD>
	</TR>
	<TR>
		<TD style="width:10%">ssl ciphers:</TD>
		<TD><INPUT TYPE="TEXT" style="width:100%" NAME="ssl_ciphers" VALUE="<?php echo $ssl_ciphers; ?>"></TD>
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
