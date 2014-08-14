<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: virtual_main.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: virtual_main.php");
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


		$virt[$selected_host]['ip'] 	= $_GET['ip']; 
		$virt[$selected_host]['port'] 	= $_GET['port']; 
                $virt[$selected_host]['delay_loop']			=	$_GET['delay_loop'];
                // $virt[$selected_host]['address']		=	$temp[0];
                $virt[$selected_host]['protocol']		=	$_GET['protocol'];
                $virt[$selected_host]['lb_kind']		=	$_GET['lb_kind'];
                $virt[$selected_host]['laddr_group_name']		=	$_GET['laddr_group_name'];
                $virt[$selected_host]['persistence_timeout']		=	$_GET['persistence_timeout'];
                $virt[$selected_host]['virtualhost']		=	$_GET['virtualhost'];
                $virt[$selected_host]['quorum']		=	$_GET['quorum'];
                $virt[$selected_host]['hysteresis']		=	$_GET['hysteresis'];
                $virt[$selected_host]['quorum_up']		=	$_GET['quorum_up'];
                $virt[$selected_host]['quorum_down']		=	$_GET['quorum_down'];
                $virt[$selected_host]['est_timeout']		=	$_GET['est_timeout'];
                $sched = $_GET['sched'];
                switch ($sched) {
                        case "Round robin"					:	$sched="rr"; break;
                        case "Weighted least-connections"			:	$sched="wlc"; break;
                        case "Weighted round robin"				:	$sched="wrr"; break;
                        case "Least-connection"					:	$sched="lc"; break;
                        case "Locality-Based Least-Connection Scheduling"	:	$sched="lblc"; break;
                        case "Locality-Based Least-Connection Scheduling (R)"	:	$sched="lblcr"; break;
                        case "Destination Hash Scheduling"			:	$sched="dh"; break;
                        case "Source Hash Scheduling"				:	$sched="sh"; break;
                        default							:	$sched="wlc"; break;
                }
                $virt[$selected_host]['lb_algo']		=	$sched;
                $pmask = $_GET['persistence_granularity'];
                if ($pmask != "Unused" ) {
                        $virt[$selected_host]['persistence_granularity']		=	$pmask;
                } else {
                        $virt[$selected_host]['persistence_granularity']		=	"";
                }


                $virt[$selected_host]['sorry_server']       =       $_GET['sorry_server'];
	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>

<TITLE>Piranha (Virtual Servers - Editing virtual server)</TITLE>

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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VIRTUAL SERVER</FONT><BR>&nbsp;</TD>
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
		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="FAILOVER">FAILOVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_services.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="MONITORING SCRIPTS">MONITORING SCRIPTS</A></TD>

        </TR>
</TABLE>

<?php } else { ?>
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:
		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VIRTUAL SERVER">VIRTUAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_services.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="MONITORING SCRIPTS">MONITORING SCRIPTS</A></TD>

        </TR>
</TABLE>
<?php } ?>

<FORM METHOD="GET" id="virtual_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="virtual_edit_virt.php">
<TABLE>
	<TR>
		<TD>IP:</TD>
		<TD><INPUT TYPE="TEXT" NAME="ip" VALUE= <?php echo $virt[$selected_host]['ip'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Port:</TD>
		<TD><INPUT TYPE="TEXT" NAME="port" VALUE= <?php echo $virt[$selected_host]['port'] ; ?>></TD>
	</TR>
	<TR>
		<TD>Health Check Interval:</TD>
		<TD><INPUT TYPE="TEXT" NAME="delay_loop" VALUE=<?php echo  $virt[$selected_host]['delay_loop'] ?>></TD>
	</TR>
	<TR>
		<TD> Scheduling: </TD>
		<TD>
		<SELECT NAME="sched">
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "rr")	{ echo "SELECTED"; } ?>> Round robin
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "wlc")	{ echo "SELECTED"; } ?>> Weighted least-connections
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "wrr")	{ echo "SELECTED"; } ?>> Weighted round robin
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "lc")	{ echo "SELECTED"; } ?>> Least-connection
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "lblc")	{ echo "SELECTED"; } ?>> Locality-Based Least-Connection Scheduling
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "lblcr")	{ echo "SELECTED"; } ?>> Locality-Based Least-Connection Scheduling (R)
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "dh")	{ echo "SELECTED"; } ?>> Destination Hash Scheduling
			<OPTION <?php if ($virt[$selected_host]['lb_algo'] == "sh")	{ echo "SELECTED"; } ?>> Source Hash Scheduling
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD>Forward Method:</TD>
		<TD><INPUT TYPE="TEXT" NAME="lb_kind" VALUE=<?php echo  $virt[$selected_host]['lb_kind'] ?>></TD>
	</TR>
	<TR>
		<TD>SNAT Address Group:</TD>
		<TD><INPUT TYPE="TEXT" NAME="laddr_group_name" VALUE=<?php echo  $virt[$selected_host]['laddr_group_name'] ?>></TD>
	</TR>
	<TR>
		<TD>Protocol:</TD>
		<TD>
			<SELECT NAME="protocol">
				<OPTION <?php if (($virt[$selected_host]['protocol'] == "tcp") || 
					       ($virt[$selected_host]['protocol'] == "")) { echo "SELECTED"; } ?>> tcp
				<OPTION <?php if ($virt[$selected_host]['protocol'] == "udp") { echo "SELECTED"; } ?>> udp
			</SELECT>
		</TD>

	</TR>

	<TR>
		<TD>Sorry Server: </TD>
		<TD><INPUT TYPE="TEXT" NAME="sorry_server" VALUE=<?php echo $virt[$selected_host]['sorry_server'] ?>></TD>
	</TR>
	<TR>
		<TD> Persistence Timeout:</TD>
		<TD><INPUT TYPE="TEXT" NAME="persistence_timeout" VALUE=<?php echo $virt[$selected_host]['persistence_timeout'] ?>></TD>
	</TR>
	<TR>
		<TD> Persistence Network Mask:</TD>
		<TD>
		<SELECT NAME="persistence_granularity">
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "") { echo "SELECTED"; } ?>> Unused
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.255")	{ echo "SELECTED"; } ?>> 255.255.255.255
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.252")	{ echo "SELECTED"; } ?>> 255.255.255.252
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.248")	{ echo "SELECTED"; } ?>> 255.255.255.248
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.240")	{ echo "SELECTED"; } ?>> 255.255.255.240
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.224")	{ echo "SELECTED"; } ?>> 255.255.255.224
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.192")	{ echo "SELECTED"; } ?>> 255.255.255.192
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.128") 	{ echo "SELECTED"; } ?>> 255.255.255.128
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.255.0")   	{ echo "SELECTED"; } ?>> 255.255.255.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.254.0")   	{ echo "SELECTED"; } ?>> 255.255.254.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.252.0")   	{ echo "SELECTED"; } ?>> 255.255.252.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.248.0")   	{ echo "SELECTED"; } ?>> 255.255.248.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.240.0")   	{ echo "SELECTED"; } ?>> 255.255.240.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.224.0")	{ echo "SELECTED"; } ?>> 255.255.224.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.192.0")	{ echo "SELECTED"; } ?>> 255.255.192.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.128.0")	{ echo "SELECTED"; } ?>> 255.255.128.0
			<OPTION <?php if ($virt[$selected_host]['persistence_granularity'] == "255.255.0.0")		{ echo "SELECTED"; } ?>> 255.255.0.0

		</SELECT>
		</TD>
	</TR>

	<TR>
		<TD>Virtual Host: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="virtualhost" VALUE="<?php echo $virt[$selected_host]['virtualhost']; ?>"></TD>
	</TR>
	<TR>
		<TD>Quorum: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="quorum" VALUE=<?php echo $virt[$selected_host]['quorum'] ?>></TD>
	</TR>
	<TR>
		<TD>Hysteresis: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="hysteresis" VALUE=<?php echo $virt[$selected_host]['hysteresis'] ?>></TD>
	</TR>
	<TR>
		<TD>Quorum UP: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="quorum_up" VALUE=<?php echo $virt[$selected_host]['quorum_up'] ?>></TD>
	</TR>
	<TR>
		<TD>Quorum DOWN: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="quorum_down" VALUE=<?php echo $virt[$selected_host]['quorum_down'] ?>></TD>
	</TR>
	<TR>
		<TD>Timeout: </TD>
		<TD> <INPUT TYPE="TEXT" NAME="est_timeout" VALUE=<?php echo $virt[$selected_host]['est_timeout'] ?>></TD>
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
