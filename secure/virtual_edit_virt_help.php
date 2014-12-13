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

                <A HREF="virtual_edit_virt_help.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?>" CLASS="tabon" NAME="VIRTUAL HELP">VIRTUAL HELP</A>
                &nbsp;|&nbsp;
		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="EDIT VIRTUAL SERVER">EDIT VIRTUAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_main.php" NAME="VIRTUAL SERVER">VIRTUAL SERVER</A>
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
<A HREF="http://www.austintek.com/LVS/LVS-HOWTO/HOWTO/LVS-HOWTO.fwmark.html" target="_blank">LVS HOWTO fwmark</A>
<BR>Please note, IP, Group, FWmark here are exclusive, only use one or another

<P>
<SPAN CLASS=title>Port:</SPAN>
Virtual server listening port

<P>
<SPAN CLASS=title>Health Check Interval (delay_loop):</SPAN>
real server services health check interval

<P>
<SPAN CLASS=title>Load Balance Algorithm:</SPAN>
Load Balance Algorithm to distribute request to real server

<P>
<SPAN CLASS=title>Forward Method:</SPAN>
Linux Virtual Server forwarding method: NAT|DR|TUN|FNAT
<BR>Please note: FNAT is a new forwarding method to enable source and destination IP address translation
simiar to <A HREF="http://www.austintek.com/LVS/LVS-HOWTO/HOWTO/LVS-HOWTO.non-modified_realservers.html" target="_blank">F5 SNAT</A>. the source address translation IP is selected from "SNAT Address Group" 
by round roubin, so to use FNAT, "SNAT Address Group" need to be created to contain members of local IP 
addresses on Linux Virtual Server director on real server side network interface or network vlans, these
local IP addresses should be also be floating addresses/shared IP addresses between high availability pair.
so you can define these local IP addresses in keepalived Virtual Router Redundancy Protocol virtual_ipaddress
block on the real server side network interface or vlans, if failover event happens, these local IP addresses will be
auto assigned to the new active Linux Virtual Server director. the FNAT requires no network topology changes, less
network maintenance, it is recommended forwarding method.

<P>
<SPAN CLASS=title>SYN Proxy:</SPAN>
Defence against synflooding attack
<BR> this is a new feature to protect real server from syn flooding attack, the virtual server will reject syn flooding request,
accept legitimate request and forward to legitimate request to real server

<P>
<SPAN CLASS=title>SNAT Address Group:</SPAN>
Local source address translation group, only valid and used for FNAT forward method

<P>
<SPAN CLASS=title>Protocol:</SPAN>
Virtual Server Protocol, TCP|UDP

<P>
<SPAN CLASS=title>Sorry Server:</SPAN>
when all real server are down, the sorry server to respond to request

<P>
<SPAN CLASS=title>Persistence Timeout:</SPAN>
Persistence timeout to a real server, if timeout expired, use virtual server load balance algorithm to select real server 

<P>
<SPAN CLASS=title>Persistence Network Mask:</SPAN>
client subnet mask to persist to a real server, for example client in 1.1.1.0/24 subnet could persist to real server A, 
client in subnet 1.1.2.0/24 could persist to real server B

<P>
<SPAN CLASS=title>Virtual Host:</SPAN>
VirtualHost string for monitor HTTP_GET or SSL_GET

<P>
<SPAN CLASS=title>Quorum:</SPAN>
Minimum total weight of all live real servers in  the pool necessary to operate VS with no  quality regression. Defaults to 1.

<P>
<SPAN CLASS=title>Hystersis:</SPAN>
Tolerate this much weight units compared to the  nominal quorum, when considering quorum gain  or loss. A flap dampener. Defaults to 0.

<P>
<SPAN CLASS=title>Quorum UP:</SPAN>
Script to launch when quorum is gained

<P>
<SPAN CLASS=title>Quorum DOWN:</SPAN>
Script to launch when quorum is lost.

</TD> </TR> </TABLE>
</BODY>
</HTML>
