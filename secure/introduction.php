<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Introduction)</TITLE>
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">
                INTRODUCTION</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<P>
<SPAN CLASS=title> ABOUT THIS TOOL </SPAN>
<P>
 This is a GUI configuration tool inspired  from Piranha and complete rewrite to manage LVS cluster through keepalived configuration
<P>
<SPAN CLASS=title>OPTIONS</SPAN>
<P>
<FONT COLOR="#660000">CONTROL:</FONT> The initially highlighted tab. Used to monitor the cluster keepalived, nginx daemons configurations and the runtime status.<P>
<FONT COLOR="#660000">GLOBAL:</FONT> Used to configure keepalived global settings, including static ip/routes, local source address translation group.<P>
<FONT COLOR="#660000">LAYER 4:</FONT> Used to setup Linux virtual server, groups for tcp layer load balance.<P>
<FONT COLOR="#660000">LAYER 7:</FONT> Used to setup reverse proxy for HTTP/Mail layer 7 load balance.<P>
<FONT COLOR="#660000">FAILOVER:</FONT> Used to set keepalived high availability and redundance.<P>
<FONT COLOR="#660000">FIREWALL:</FONT> Used to set linux iptable firewalls.<P>
<P>
<SPAN CLASS=title>RESOURCES</SPAN>
<P>
<TABLE BORDER="0" CELLSPACING="0" width=90%>
	<TR>
		<TD WIDTH=10% align=center><A HREF="help.php">Help</A> </TR>
		<!--TD WIDTH=10% align=center><A HREF="http://ha.redhat.com" target="_blank">Piranha Project</A> </TR-->
		<TD WIDTH=10% align=center><A HREF="http://www.linuxvirtualserver.org/" target="_blank">The Linux Virtual Server Project</A> </TR>
		<TD WIDTH=10% align=center><A HREF="http://www.keepalived.org/" target="_blank">Keepalived</A> </TR>
		<TD WIDTH=10% align=center><A HREF="http://www.nginx.org/" target="_blank">Ngnix</A> </TR>
		<TD WIDTH=10% align=center><A HREF="http://linux.die.net/man/8/iptables" target="_blank">iptables</A> </TR>
	</TR>
</TABLE>
<P>
<SPAN CLASS=title>AUTHORS</SPAN>
<BR>
<TABLE>
	<TR>
                <TD>&nbsp;&nbsp;</TD>
		<TD>Vincent Li</TD>
	</TR>
</TABLE>
<P>
You could also browse through Redhat Linux Virtual Server Administration for LVS reference
it  applies to layer 4 loadbalancing configuration in this project
<BR>
Please note this project has competely rewritten the GUI to use keepalived as interface to 
LVS, so the GUI configuration section in the Redhat Linux Virtual Server Administration does not apply here.  
<BR>
<!--A HREF="https://listman.redhat.com/mailman/listinfo/piranha-list">https://listman.redhat.com/mailman/listinfo/piranha-list</A-->
<A HREF="./docs/" target="_blank">Redhat Linux Virtual Server Administration Reference</A>
<P>
&nbsp;
</TD></TR></TABLE>
</BODY>
</HTML>
