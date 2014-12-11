<?php

       if (isset($_GET['selected_host'])) {
                $selected_host = $_GET['selected_host'];
       }

	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	/* Umm,... just in case someone is dumb enuf to fiddle */
	if (empty($selected_host)) { $selected_host=1; }

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual Servers - Editing real server)</TITLE>
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">HELP</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">MENU:

                <A HREF="virtual_main.php" NAME="VIRTUAL SERVER">VIRTUAL SERVER</A>
                &nbsp;|&nbsp;
		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="EDIT VIRTUAL SERVER">EDIT VIRTUAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_virt_help.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="HELP">HELP</A>
		&nbsp;|&nbsp;

        </TR>
</TABLE>

<P>
<SPAN CLASS=title>IP:</SPAN>
Virtual IP address
<P>
<SPAN CLASS=title>Group:</SPAN>
The Virtual Server Group name string, configured in Virtual Server Group section
<P>
<SPAN CLASS=title>FWmark:</SPAN>
The firewall mark set in iptables mangle table for a VIP, firewall mark is for advance use, detail see :
<A HREF="http://www.austintek.com/LVS/LVS-HOWTO/HOWTO/LVS-HOWTO.fwmark.html">LVS HOWTO fwmark</A>
<BR>Please note, IP, Group, FWmark here are exclusive, only use one or another


</TD> </TR> </TABLE>
</BODY>
</HTML>
