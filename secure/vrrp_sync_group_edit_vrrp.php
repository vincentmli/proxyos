<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: vrrp_sync_group_main.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: vrrp_sync_group_main.php");
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


		$vrrp_sync_group[$selected_host]['vrrp_sync_group'] 	= $_GET['vrrp_sync_group']; 
                $vrrp_sync_group[$selected_host]['notify_master']		=	$_GET['notify_master'];
                $vrrp_sync_group[$selected_host]['notify_backup']		=	$_GET['notify_backup'];
                $vrrp_sync_group[$selected_host]['notify_fault']		=	$_GET['notify_fault'];
                $vrrp_sync_group[$selected_host]['notify']		=	$_GET['notify'];
                $vrrp_sync_group[$selected_host]['smtp_alert']		=	$_GET['smtp_alert'];
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

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
            <A HREF="introduction.html" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP SYNC GROUP</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="vrrp_main.php" NAME="VRRP instance" CLASS="taboff"><B>VRRP INSTANCE</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="vrrp_sync_group_main.php" NAME="VRRP sync group" CLASS="taboff"><B>VRRP SYNC GROUP</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>

        </TR>
</TABLE>
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
		
		<A HREF="vrrp_sync_group_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP instance">VRRP SYNC GROUP</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_sync_group_edit_group.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="GROUP">GROUP</A>
		&nbsp;|&nbsp;


        </TR>
</TABLE>
<?php } ?>

<FORM METHOD="GET" id="vrrp_sync_group_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_sync_group_edit_vrrp.php">
<TABLE>
	<TR>
		<TD>VRRP sync group name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="vrrp_sync_group" VALUE= <?php echo $vrrp_sync_group[$selected_host]['vrrp_sync_group'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Notify master:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_master" VALUE="<?php echo  htmlspecialchars($vrrp_sync_group[$selected_host]['notify_master'], ENT_QUOTES)?>"></TD>
	</TR>
	<TR>
		<TD>Notify backup:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_backup" VALUE="<?php echo  htmlspecialchars($vrrp_sync_group[$selected_host]['notify_backup'], ENT_QUOTES)?>"></TD>
	</TR>
	<TR>
		<TD>Notify fault:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_fault" VALUE="<?php echo  htmlspecialchars($vrrp_sync_group[$selected_host]['notify_fault'], ENT_QUOTES)?>"></TD>
	</TR>
	<TR>
		<TD>Notify:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify" VALUE="<?php echo  htmlspecialchars($vrrp_sync_group[$selected_host]['notify'], ENT_QUOTES)?>"></TD>
	</TR>
	<TR>
		<TD>SMTP alert:</TD>
		<TD><INPUT TYPE="TEXT" NAME="smtp_alert" VALUE="<?php echo  $vrrp_sync_group[$selected_host]['smtp_alert'] ?>"></TD>
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
