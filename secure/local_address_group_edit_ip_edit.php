<?php
        $edit_action = $_GET['edit_action'];
        $selected_host = $_GET['selected_host'];
        $selected = $_GET['selected'];
	if ($edit_action == "CANCEL") {
		header("Location: local_address_group_edit_ip.php?selected_host=$selected_host&selected=$selected");		
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0
	global $local_address_group;

	require('parse.php');


		
	if ($edit_action == "ACCEPT") {

		$ip	=	$_GET['ip'];
		$local_address_group[$selected_host]['ip'][$selected-1]		= "$ip";	

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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT SNAT ADDRESS GROUP IP</FONT><BR>&nbsp;</TD>
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

                <A HREF="local_address_group_main.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="SNAT ADDRESS GROUP">SNAT ADDRESS GROUP</A>
                &nbsp;|&nbsp;

                <A HREF="local_address_group_edit_group.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="SNAT ADDRESS GROUP NAME">SNAT ADDRESS GROUP NAME</A>
                &nbsp;|&nbsp;

                <A HREF="local_address_group_edit_ip.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="SNAT ADDRESS GROUP IP">SNAT ADDRESS GROUP IP</A>
                &nbsp;|&nbsp;


		</TD>
		<!-- <TD WIDTH="30%" ALIGN="RIGHT"><A HREF="virtual_main.php">MAIN PAGE</A></TD> -->
        </TR>
</TABLE>

<P>


<FORM id="local_address_group_ip_form" METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="local_address_group_edit_ip_edit.php">


	<TABLE>

	<?php	
		$ip =  $local_address_group[$selected_host]['ip'][$selected-1];

		echo "<TR>";
			echo "<TD>IP: </TD>";
			echo "<TD><INPUT TYPE=TEXT NAME=ip VALUE=\""; echo $ip . "\""  . ">"; echo "</TD>";
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