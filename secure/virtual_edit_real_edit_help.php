<?php
        $selected_host = $_GET['selected_host'];
        $selected = $_GET['selected'];
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Virtual servers - Editing virtual server - Editing real server)</TITLE>
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">HELP</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

                <A HREF="virtual_main.php" NAME="VIRTUAL SERVER">VIRTUAL SERVER</A>
                &nbsp;|&nbsp;

		
		<A HREF="virtual_edit_virt.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " NAME="VIRTUAL SERVER EDIT">VIRTUAL SERVER EDIT</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="REAL SERVER">REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host&selected=$selected"; } ?> " CLASS="tabon" NAME="EDIT REAL SERVER">EDIT REAL SERVER</A>
		&nbsp;|&nbsp;

                <A HREF="virtual_edit_real_edit_help.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host&selected=$selected"; } ?> " CLASS="tabon" NAME="HELP">REAL SERVER HELP</A>
		&nbsp;|&nbsp;

        </TR>
</TABLE>

<P>
<SPAN CLASS=title>IP:</SPAN>
Real server IP address

<P>
<SPAN CLASS=title>Port:</SPAN>
Real server listening port

<P>
<SPAN CLASS=title>Notify UP:</SPAN>
Script to launch when healthchecker considers service as up.

<P>
<SPAN CLASS=title>Notify Down:</SPAN>
Script to launch when healthchecker considers service as down.

<P>
<SPAN CLASS=title>Weight:</SPAN>
relative weight to use, default: 1

<P>
<SPAN CLASS=title>Health Check:</SPAN>
select real server health check monitor type HTTP_GET|SSL_GET|TCP_CHECK|SMTP_CHECK|MISC_CHECK

<P>
<SPAN CLASS=title>URL Path:</SPAN>
An URL for HTTP_GET or SSL_GET to test, example /test

<P>
<SPAN CLASS=title>URL Digest:</SPAN>
URL digest for HTTP_GET or SSL_GET, use command genhash to generate the diagest to the URL path
for example, generate digest to real server 192.168.3.9 port 80 URL /test
<BR># genhash -s 192.168.3.9 -p 80 -u /test
<BR>MD5SUM = eb8fed83c8cabd746579445cfdd0a1cc
<BR>fill in the MD5SUM value in URL Digest, run genhash -h for genhash usage

<P>
<SPAN CLASS=title>Status Code:</SPAN>
status code returned in the HTTP header,  eg status_code 200

<P>
<SPAN CLASS=title>Timeout:</SPAN>
Optional connection timeout in seconds,The default is 5 seconds

<P>
<SPAN CLASS=title>Connect Port:</SPAN>
Optional port to connect to if not, The default is real server’s port

<P>
<SPAN CLASS=title>Bindto:</SPAN>
Optional interface to use to originate the connection

<P>
<SPAN CLASS=title>Bind Port:</SPAN>
Optional source port to originate the connection from

<P>
<SPAN CLASS=title>Number of GET Retry:</SPAN>
number of get retry

<P>
<SPAN CLASS=title>Delay Before Retry:</SPAN>
delay before retry

<P>
<SPAN CLASS=title>Host IP:</SPAN>
SMTP_CHECK:  An optional host interface to check.  If no host directives are present, only
the ip address of the real server will  be checked.
Optional IP address to connect to.  The default is real server IP

<P>
<SPAN CLASS=title>Host Port:</SPAN>
SMTP_CHECK:  An optional host interface to check.  If no host directives are present, only
the ip address of the real server will  be checked.
Optional port to connect to if not the default of 25

<P>
<SPAN CLASS=title>Number of Retry a Failed Check:</SPAN>
SMTP_CHECK:  Number of times to retry a failed check

<P>
<SPAN CLASS=title>Helo Name:</SPAN>
SMTP_CHECK:  Optional string to use for the smtp HELO request

<P>
<SPAN CLASS=title>External Monitor Path:</SPAN>
MISC_CHECK: External system script or program 

<P>
<SPAN CLASS=title>External Monitor Timeout:</SPAN>
MISC_CHECK: Script execution timeout

<P>
<SPAN CLASS=title>Exit Code to Dynamic Weight Adjust:</SPAN>
MISC_CHECK: If set, exit code from healthchecker is used to dynamically adjust the weight as follows:
<BR>  exit status 0: svc check success, weight  unchanged.
<BR>  exit status 1: svc check failed.
<BR>  exit status 2-255: svc check success, weight  changed to 2 less than exit status.  
(for example: exit status of 255 would set weight to 253)
 








</TD></TR></TABLE>
</BODY>
</HTML>
