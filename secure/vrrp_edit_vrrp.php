<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: vrrp_main.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: vrrp_main.php");
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


		$vrrp_instance[$selected_host]['vrrp_instance'] 	= $_GET['vrrp_instance']; 
		$vrrp_instance[$selected_host]['state'] 	= $_GET['state']; 
                $vrrp_instance[$selected_host]['interface']			=	$_GET['interface'];
                // $virt[$selected_host]['address']		=	$temp[0];
                $vrrp_instance[$selected_host]['dont_track_primary']		=	$_GET['dont_track_primary'];
                $vrrp_instance[$selected_host]['mcast_src_ip']		=	$_GET['mcast_src_ip'];
                $vrrp_instance[$selected_host]['lvs_sync_daemon_interface']		=	$_GET['lvs_sync_daemon_interface'];
                $vrrp_instance[$selected_host]['garp_master_delay']		=	$_GET['garp_master_delay'];
                $vrrp_instance[$selected_host]['virtual_router_id']		=	$_GET['virtual_router_id'];
                $vrrp_instance[$selected_host]['priority']		=	$_GET['priority'];
                $vrrp_instance[$selected_host]['advert_int']		=	$_GET['advert_int'];
                $vrrp_instance[$selected_host]['nopreempt']		=	$_GET['nopreempt'];
                $vrrp_instance[$selected_host]['preempt_delay']		=	$_GET['preempt_delay'];
                $vrrp_instance[$selected_host]['debug']		=	$_GET['debug'];
                $vrrp_instance[$selected_host]['notify_master']		=	$_GET['notify_master'];
                $vrrp_instance[$selected_host]['notify_backup']		=	$_GET['notify_backup'];
                $vrrp_instance[$selected_host]['notify_fault']		=	$_GET['notify_fault'];
                $vrrp_instance[$selected_host]['notify']		=	$_GET['notify'];
                $vrrp_instance[$selected_host]['smtp_alert']		=	$_GET['smtp_alert'];
                $vrrp_instance[$selected_host]['authentication']	=	true;
                $vrrp_instance[$selected_host]['auth_type']		=	$_GET['auth_type'];
                $vrrp_instance[$selected_host]['auth_pass']		=	$_GET['auth_pass'];
	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>
<script>
function delIPPort() {
    //remove selected index from selected
    //see http://api.jquery.com/remove/
}
function addIPPort() {
    var ip = $("#ip"); // see http://api.jquery.com/category/selectors/
    var port = $("#port");
    //http://api.jquery.com/val/ and http://api.jquery.com/append/
    $("#virtual_ipaddress").append("<option>" + ip.val() + ":" + port.val() + "</option>"); 
    port.val("");
    ip.val("");
}
</script>

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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP INSTANCE</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

               <A HREF="vrrp_edit_vrrp_help.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="HELP">HELP</A>
                &nbsp;|&nbsp;
		
		<A HREF="vrrp_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP instance">VRRP INSTANCE</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL IPADDRESS">VIRTUAL IPADDRESS</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress_excluded.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP VIRTUAL IPADDRESS EXCLUDED">VIRTUAL IPADDRESS EXCLUDED</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_routes.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL ROUTES">VIRTUAL ROUTES</A>
		&nbsp;|&nbsp;
                <A HREF="vrrp_edit_track_interface.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK INTERFACE">TRACK INTERFACE</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_script.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK SCRIPT">TRACK SCRIPT</A>
		&nbsp;|&nbsp;


        </TR>
</TABLE>

<FORM METHOD="GET" id="vrrp_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_edit_vrrp.php">
<TABLE>
	<TR>
		<TD>VRRP instance name:</TD>
		<TD><INPUT TYPE="TEXT" NAME="vrrp_instance" VALUE= <?php echo $vrrp_instance[$selected_host]['vrrp_instance'] ; ?>></TD>
	</TR>
	<TR>
		<TD>State:</TD>
		<TD><INPUT TYPE="TEXT" NAME="state" VALUE= <?php echo $vrrp_instance[$selected_host]['state'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Interface:</TD>
		<TD><INPUT TYPE="TEXT" NAME="interface" VALUE=<?php echo  $vrrp_instance[$selected_host]['interface'] ?>></TD>
	</TR>
	<TR>
		<TD>Do not track primary:</TD>
		<TD><INPUT TYPE="TEXT" NAME="dont_track_primary" VALUE=<?php echo  $vrrp_instance[$selected_host]['dont_track_primary'] ?>></TD>
	</TR>
	<TR>
		<TD>Multicast source ip:</TD>
		<TD><INPUT TYPE="TEXT" NAME="mcast_src_ip" VALUE=<?php echo  $vrrp_instance[$selected_host]['mcast_src_ip'] ?>></TD>
	</TR>
	<TR>
		<TD>LVS sync daemon interface:</TD>
		<TD><INPUT TYPE="TEXT" NAME="lvs_sync_daemon_interface" VALUE=<?php echo  $vrrp_instance[$selected_host]['lvs_sync_daemon_interface'] ?>></TD>
	</TR>
	<TR>
		<TD>Garp master delay:</TD>
		<TD><INPUT TYPE="TEXT" NAME="garp_master_delay" VALUE=<?php echo  $vrrp_instance[$selected_host]['garp_master_delay'] ?>></TD>
	</TR>
	<TR>
		<TD>Virtual router id:</TD>
		<TD><INPUT TYPE="TEXT" NAME="virtual_router_id" VALUE=<?php echo  $vrrp_instance[$selected_host]['virtual_router_id'] ?>></TD>
	</TR>
	<TR>
		<TD>Priority:</TD>
		<TD><INPUT TYPE="TEXT" NAME="priority" VALUE=<?php echo  $vrrp_instance[$selected_host]['priority'] ?>></TD>
	</TR>
	<TR>
		<TD>Advert int:</TD>
		<TD><INPUT TYPE="TEXT" NAME="advert_int" VALUE=<?php echo  $vrrp_instance[$selected_host]['advert_int'] ?>></TD>
	</TR>
	<TR>
		<TD>No preempt:</TD>
		<TD><INPUT TYPE="TEXT" NAME="nopreempt" VALUE=<?php echo  $vrrp_instance[$selected_host]['nopreempt'] ?>></TD>
	</TR>
	<TR>
		<TD>Preempt delay:</TD>
		<TD><INPUT TYPE="TEXT" NAME="preempt_delay" VALUE=<?php echo  $vrrp_instance[$selected_host]['preempt_delay'] ?>></TD>
	</TR>
	<TR>
		<TD>Debug:</TD>
		<TD><INPUT TYPE="TEXT" NAME="debug" VALUE=<?php echo  $vrrp_instance[$selected_host]['debug'] ?>></TD>
	</TR>
	<TR>
		<TD>Notify master:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_master" VALUE="<?php echo  htmlspecialchars($vrrp_instance[$selected_host]['notify_master'], ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD>Notify backup:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_backup" VALUE="<?php echo  htmlspecialchars($vrrp_instance[$selected_host]['notify_backup'], ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD>Notify fault:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify_fault" VALUE="<?php echo  htmlspecialchars($vrrp_instance[$selected_host]['notify_fault'], ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD>Notify:</TD>
		<TD><INPUT TYPE="TEXT" NAME="notify" VALUE="<?php echo  htmlspecialchars($vrrp_instance[$selected_host]['notify'], ENT_QUOTES) ?>"></TD>
	</TR>
	<TR>
		<TD>SMTP alert:</TD>
		<TD><INPUT TYPE="TEXT" NAME="smtp_alert" VALUE="<?php echo  $vrrp_instance[$selected_host]['smtp_alert'] ?>"></TD>
	</TR>
	<TR>
		<TD>Authentication type:</TD>
		<TD><INPUT TYPE="TEXT" NAME="auth_type" VALUE=<?php echo  $vrrp_instance[$selected_host]['auth_type'] ?>></TD>
	</TR>
	<TR>
		<TD>Authentication pass:</TD>
		<TD><INPUT TYPE="TEXT" NAME="auth_pass" VALUE=<?php echo  $vrrp_instance[$selected_host]['auth_pass'] ?>></TD>


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
