<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Help)</TITLE>
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

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>PIRANHA</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
<!--       <a href="docs.html" class="logolink">
           DOCUMENTATION</a> |
-->
           <A HREF="secure/introduction.html" CLASS="logolink">
           INTRODUCTION</A> | <A HREF="secure/help.php" CLASS="logolink">
           HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">ABOUT LOGIN</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'secure/menu.php'; ?>

<TABLE>
	<TR>
		<TD>
&nbsp;
<P>
If this is the very first time that you have attempted to log into the
Piranha configuration GUI, then please note that your system
administrator must first set the piranha password by running the
'/usr/sbin/piranha-passwd <password>' utility, and make sure the
Piranha GUI service has been started via the 
"/etc/rc.d/init.f/piranha-gui start" command.
<P>
If that has already been done and you are receiving a "Forbidden" message
indicating you do not have permissions to access the web pages, it is
most likely because you did not include TCP/IP port number of the
Piranha GUI's http service in your URL (for example:
"http://myhost.com:3636/piranha"). The Piranha GUI needs a
unique httpd service port, and the web screens are
protected against accidental access by other httpd services running
on your system. Without including this port number in your URL, your
browser is attempting to access the pages via your main httpd service.
<p>If you included the port number and get an "unable to locate service"
message, it is because the Piranha GUI service is not running, so it's
TCP/IP port is not operating.

		</TD>
	<TR>
</TABLE>
</TD></TR></TABLE>
</BODY>
</HTML>
