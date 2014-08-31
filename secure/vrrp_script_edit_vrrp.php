<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: vrrp_script_main.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: vrrp_script_main.php");
		exit;
	}

	if ($vev_action == "EDIT") {
		/* Redirect browser to editing page */
		header("Location: virtual_edit_services.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */


	if ( $vev_action == "ACCEPT" ) {


		$vrrp_script[$selected_host]['vrrp_script'] 	= $_GET['vrrp_script']; 
		$vrrp_script[$selected_host]['script'] 	= $_GET['script']; 
                $vrrp_script[$selected_host]['interval']			=	$_GET['interval'];
                $vrrp_script[$selected_host]['weight']			=	$_GET['weight'];
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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP SCRIPT</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<?php
	// echo "Query = $QUERY_STRING";

?>

<?php if ($prim['service'] == "fos") { ?>
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:
		
		<A HREF="vrrp_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP instance">VRRP INSTANCE</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_services.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="MONITORING SCRIPTS">MONITORING SCRIPTS</A></TD>

        </TR>
</TABLE>

<?php } else { ?>
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:
		
		<A HREF="vrrp_script_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP instance">VRRP SCRIPT</A>
		&nbsp;|&nbsp;

        </TR>
</TABLE>
<?php } ?>

<FORM METHOD="GET" id="vrrp_script_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_script_edit_vrrp.php">
<TABLE>
	<TR>
		<TD>VRRP script name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="vrrp_script" VALUE= <?php echo $vrrp_script[$selected_host]['vrrp_script'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Script:</TD>
		<TD><INPUT TYPE="TEXT" NAME="script" VALUE="<?php echo  htmlspecialchars($vrrp_script[$selected_host]['script'], ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD>Interval:</TD>
		<TD><INPUT TYPE="TEXT" NAME="interval" VALUE= <?php echo $vrrp_script[$selected_host]['interval'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Weight:</TD>
		<TD><INPUT TYPE="TEXT" NAME="weight" VALUE= <?php echo $vrrp_script[$selected_host]['weight'] ; ?>></TD>
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
