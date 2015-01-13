<?php
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	global $debug_level;
	global $http;

	require('parse_tengine.php'); /* read in the config! Hurragh! */


	if (isset($_GET['http_action']) &&
	    $_GET['http_action'] == "ACCEPT") {

	/* nginx main config */

		if (isset($_GET['ssl'])) {
			$http['ssl'] = $_GET['ssl'];
		}
		if (isset($_GET['ssl_protocols'])) {
			$http['ssl_protocols'] = $_GET['ssl_protocols'];
		}
	}

	// echo "Query = $QUERY_STRING";
?>

<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Global Settings) <?php $debug && print "(DEBUG ON)" ?></TITLE>

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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">HTTP SETTINGS</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

                <A HREF="ngx_main_settings.php" NAME="MAIN SETTING">MAIN SETTING</A>
                &nbsp;|&nbsp;
                <A HREF="ngx_http.php" NAME="HTTP">HTTP</A>
                &nbsp;|&nbsp;
                <A HREF="ngx_http_upstream.php" NAME="UPSTREAM">UPSTREAM</A>
                &nbsp;|&nbsp;
                <A HREF="ngx_http_server.php" NAME="HTTP SERVER">HTTP VIRTUAL SERVER</A>
                &nbsp;|&nbsp;



                </TD>

        </TR>
</TABLE>


<P>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="ngx_http.php">


<P>
<TABLE  BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title" COLSPAN="2">ENVIRONMENT</TD>
        </TR>

	<TR>
		<TD>ssl :</TD>
		<TD><INPUT TYPE="TEXT" NAME="ssl" SIZE=26 VALUE="<?php echo $http['ssl']; ?>"></TD>
	</TR>
	<TR>
		<TD>ssl protocols:</TD>
		<TD><INPUT TYPE="TEXT" NAME="ssl_protocols" SIZE=26 VALUE="<?php echo $http['ssl_protocols']; ?>"></TD>
	</TR>


</TABLE>
<HR>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TR BGCOLOR="#666666">
		<TD>
			<INPUT TYPE="SUBMIT" NAME="http_action" VALUE="ACCEPT"> <SPAN CLASS="taboff"> -- Click here to apply changes on this page</SPAN>
		</TD>
	</TR>
</TABLE>

<?php 
	open_file ("w+"); write_config(""); 
?>


</FORM>

</TD></TR></TABLE>
</BODY>
</HTML>
