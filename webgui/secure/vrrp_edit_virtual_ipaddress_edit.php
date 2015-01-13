<?php
        $edit_action = $_GET['edit_action'];
        $selected_host = $_GET['selected_host'];
        $selected = $_GET['selected'];
	if ($edit_action == "CANCEL") {
		header("Location: vrrp_edit_virtual_ipaddress.php?selected_host=$selected_host&selected=$selected");		
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $vrrp_instance;

	require('parse.php');

	if ($edit_action == "ACCEPT") {

                $ip     =       $_GET['ip'];
                $netmask        =       $_GET['netmask'];
		$cidr 		=	Mask2CIDR($netmask);
                $interface      =       $_GET['interface'];
                $scope  =       $_GET['scope'];
                if(isset($scope) && $scope !="") {
                        $vrrp_instance[$selected_host]['virtual_ipaddress'][$selected-1]               = "$ip/$cidr dev $interface scope $scope"; 
                } else {
                        $vrrp_instance[$selected_host]['virtual_ipaddress'][$selected-1]               = "$ip/$cidr dev $interface";
                }

		header("Location: vrrp_edit_virtual_ipaddress.php?selected_host=$selected_host&selected=$selected-1");		

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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT VRRP VIRTUAL IPADDRESS</FONT><BR>&nbsp;</TD>
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
		
		<A HREF="vrrp_edit_vrrp.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="INSTANCE">VRRP INSTANCE</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VIRTUAL IPADDRESS">VIRTUAL IPADDRESS</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_unicast_peer.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VRRP UNICAST PEER">UNICAST PEER</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_ipaddress_excluded.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="VIRTUAL IPADDRESS EXCLUDED">VIRTUAL IPADDRESS EXCLUDED</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_virtual_routes.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL ROUTES">VIRTUAL ROUTES</A>
		&nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_interface.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK INTERFACE">TRACK INTERFACE</A>
                &nbsp;|&nbsp;

                <A HREF="vrrp_edit_track_script.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="TRACK SCRIPT">TRACK SCRIPT</A>
                &nbsp;|&nbsp;

		</TD>
		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>


<FORM id="vrrp_virtual_ipaddress_form" METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="vrrp_edit_virtual_ipaddress_edit.php">


	<TABLE>

	<?php	
                $string = explode(" ", $vrrp_instance[$selected_host]['virtual_ipaddress'][$selected-1]);
                if (isset($string[3]) && $string[3] == "scope") {
                        $ipnetmask = explode("/", $string[0]);
                        $ip = $ipnetmask[0];
                        $cidr = $ipnetmask[1];
			$netmask = CIDRtoMask($cidr);
                        $interface = $string[2];
                        $scope = $string[4];
                } else {
                        $ipnetmask = explode("/", $string[0]);
                        $ip = $ipnetmask[0];
                        $cidr = $ipnetmask[1];
			$netmask = CIDRtoMask($cidr);
                        $interface = $string[2];
                        $scope = "";
                }


		echo "<TR>";
			echo "<TD>IP: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=ip VALUE=\""; echo $ip . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>NETMASK: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=netmask VALUE=\""; echo $netmask . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>INTERFACE: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=interface VALUE=\""; echo $interface . "\""  . ">"; echo "</TD>";
		echo "</TR>";

		echo "<TR>";
			echo "<TD>SCOPE: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=scope VALUE=\""; echo $scope . "\""  . ">"; echo "</TD>";
		echo "</TR>";

	echo "</TABLE>";

	
		/* Welcome to the magic show */
		echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>";
		echo "<INPUT TYPE=HIDDEN NAME=selected VALUE=$selected >";
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
