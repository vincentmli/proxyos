<?php
        $selected_ip = $_GET['selected_ip'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: static_ipaddress.php?selected_ip=$selected_ip");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_ip == "")) {
		header("Location: static_ipaddress.php");
		exit;
	}

	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */

	if ( $vev_action == "ACCEPT" ) {

                $static_ipaddress[$selected_ip]['ip']			=	$_GET['ip'];
                $mask = $_GET['mask'];
                if ($mask != "Unused" ) {
			$maskcidr = Mask2CIDR($mask);
                        $static_ipaddress[$selected_ip]['mask']	=	$maskcidr;
                } else {
                        $static_ipaddress[$selected_ip]['mask']	=	"24";
                }

                $static_ipaddress[$selected_ip]['dev']      		=       $_GET['dev'];

	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>

<TITLE>Keepalived (Static IPaddress - Editing static ipaddress)</TITLE>

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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT STATIC IPADDRESS</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="local_address_group.php" NAME="Local address group" CLASS="taboff"><B>SNAT ADDRESS GROUP</B></A> </TD>
		<TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="vrrp_main.php" NAME="VRRP instance" CLASS="taboff"><B>VRRP INSTANCE</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>

        </TR>
</TABLE>
<?php
	// echo "Query = $QUERY_STRING";

?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:
		
		<A HREF="static_ipaddress_edit.php<?php if (!empty($selected_ip)) { echo "?selected_ip=$selected_ip"; } ?> " CLASS="tabon" NAME="STATIC IPADDRESS">STATIC IPADDRESS</A>
        </TR>
</TABLE>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="static_ipaddress_edit.php">
<TABLE>
	<TR>
		<TD>IP Address:</TD>
		<TD><INPUT TYPE="TEXT" NAME="ip" VALUE= <?php echo $static_ipaddress[$selected_ip]['ip'] ; ?>></TD>
	</TR>

	<TR>
		<TD> IP Network Mask:</TD>
		<TD>
			<SELECT NAME="mask">
				<?php if (!isset($static_ipaddress[$selected_ip]['mask'])) { 
					$static_ipaddress[$selected_ip]['mask'] = "0.0.0.0";
				} ?>

				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "0.0.0.0") { echo "SELECTED"; } ?>> Unused
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.255") { echo "SELECTED"; } ?>> 255.255.255.255
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.252") { echo "SELECTED"; } ?>> 255.255.255.252
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.248") { echo "SELECTED"; } ?>> 255.255.255.248
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.240") { echo "SELECTED"; } ?>> 255.255.255.240
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.224") { echo "SELECTED"; } ?>> 255.255.255.224
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.192") { echo "SELECTED"; } ?>> 255.255.255.192
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.128") { echo "SELECTED"; } ?>> 255.255.255.128
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.255.0")   { echo "SELECTED"; } ?>> 255.255.255.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.254.0")   { echo "SELECTED"; } ?>> 255.255.254.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.252.0")   { echo "SELECTED"; } ?>> 255.255.252.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.248.0")   { echo "SELECTED"; } ?>> 255.255.248.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.240.0")   { echo "SELECTED"; } ?>> 255.255.240.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.224.0")	{ echo "SELECTED"; } ?>> 255.255.224.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.192.0")	{ echo "SELECTED"; } ?>> 255.255.192.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.128.0")	{ echo "SELECTED"; } ?>> 255.255.128.0
				<OPTION <?php if ($static_ipaddress[$selected_ip]['mask'] == "255.255.0.0")	{ echo "SELECTED"; } ?>> 255.255.0.0

			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD>Interface: </TD>
		<TD><INPUT TYPE="TEXT" NAME="dev" VALUE=<?php echo $static_ipaddress[$selected_ip]['dev'] ?>></TD>
	</TR>
</TABLE>
<?php echo "<INPUT TYPE=HIDDEN NAME=selected_ip VALUE=$selected_ip>" ?>

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
