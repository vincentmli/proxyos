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
<TITLE>VRRP instance editing - HELP)</TITLE>
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

<P>
<SPAN CLASS=title>VRRP instance name:</SPAN>
Virtual Router Redundancy Protocol (VRRP) instance name

<P>
<SPAN CLASS=title>State:</SPAN>
Initial state, MASTER|BACKUP
<BR>As soon as the other machine(s) come up,  an election will be held and the machine with the highest "priority" will become MASTER.
So the entry here doesn't matter a whole lot.

<P>
<SPAN CLASS=title>Interface:</SPAN>
Interface for inside_network/outside_network, bound by vrrp instance

<P>
<SPAN CLASS=title>Unicast source ip:</SPAN>
Most of cloud network does not allow multicast traffic, use unicast source ip for VRRP unicast in cloud network like Amazon AWS

<P>
<SPAN CLASS=title>Do not track primary:</SPAN>
Ignore VRRP interface faults (default unset)

<P>
<SPAN CLASS=title>Multicast source ip:</SPAN>
default IP for binding vrrpd is the primary IP  on interface. If you want to hide location of vrrpd, use this IP as src_addr for multicast or unicast vrrp  packets.  (since it is multicast, vrrpd will get the reply  packet no matter what src_addr is used).   optional

<P>
<SPAN CLASS=title>LVS sync daemon interface:</SPAN>
Binding interface for lvs syncd

<P>
<SPAN CLASS=title>Garp master delay:</SPAN>
delay for gratuitous ARP after transition to MASTER

<P>
<SPAN CLASS=title>Virtual router id:</SPAN>
arbitary unique number 0..255, used to differentiate multiple instances of vrrpd  running on the same NIC (and hence same socket).

<P>
<SPAN CLASS=title>Priority:</SPAN>
for electing MASTER, highest priority wins.  to be MASTER, make 50 more than other machines.

<P>
<SPAN CLASS=title>Advert int:</SPAN>
VRRP Advert interval, secs (use default)

<P>
<SPAN CLASS=title>No preempt:</SPAN>
VRRP will normally preempt a lower priority  machine when a higher priority machine comes  online.  "nopreempt" allows the lower priority machine to maintain the master role, even when  a higher priority machine comes back online.
<BR> NOTE: For this to work, the initial state of this entry must be BACKUP.

<P>
<SPAN CLASS=title>Preempt delay:</SPAN>
Seconds after startup until preemption  (if not disabled by "nopreempt").  Range: 0 (default) to 1,000
<BR>NOTE: For this to work, the initial state of this  entry must be BACKUP.

<P>
<SPAN CLASS=title>Debug:</SPAN>
Debug level, not implemented yet

<P>
<SPAN CLASS=title>Notify master:</SPAN>
notify scripts and alerts are optional,  filenames of scripts to run on transitions  can be unquoted (if just filename)  or quoted (if has parameters)
<BR>to MASTER transition

<P>
<SPAN CLASS=title>Notify backkup:</SPAN>
to BACKUP transition

<P>
<SPAN CLASS=title>Notify fault:</SPAN>
to FAULT transition

<P>
<SPAN CLASS=title>Notify:</SPAN>
for ANY state transition.  "notify" script is called AFTER the notify_* script(s) and is executed with 3 arguments provided by keepalived (ie dont include parameters in the notify line).
<BR> arguments
<BR> $1 = "GROUP"|"INSTANCE"
<BR> $2 = name of group or instance
<BR> $3 = target state of transition

<P>
<SPAN CLASS=title>SMTP alert:</SPAN>
Send email notifcation during state transition, using addresses in global email notification setting.

<P>
<SPAN CLASS=title>Authentication type:</SPAN>
Authentication block  PASS|AH 
<BR> PASS - Simple Passwd (suggested)
<BR> AH - IPSEC (not recommended))

<P>
<SPAN CLASS=title>Authentication pass:</SPAN>
Password for accessing vrrpd.  should be the same for all machines.  Only the first eight (8) characters are used.




</TD> </TR> </TABLE>
</BODY>
</HTML>
