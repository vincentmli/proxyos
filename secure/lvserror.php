<?php
	if ($help_action =="Close") {
		header("Location: control.php");	/* Redirect browser to editing page */
		exit;  					/* Make sure that code below does not get executed when we redirect. */
	}	
	
?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Help file)</TITLE>
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">ERROR</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<TABLE>
	<TR>
		<TD>
There was an error opening or creating the lvs.cf configuration file<BR>
The most likely cause is that the file permissions are incorrect.<BR>
They should be set as follows<P><PRE>
-rw-rw----   1 root  piranha	0 Mar 1 12:00 /etc/sysconfig/ha/lvs.cf</PRE><P>
You can achieve this by issuing the following 3 commands as root<BR>
&nbsp;touch /etc/sysconfig/ha/lvs.cf<BR>
&nbsp;chmod 660 /etc/sysconfig/ha/lvs.cf<BR>
&nbsp;chown root.piranha /etc/sysconfig/ha/lvs.cf
		</TD>
	</TR>
	<TR>
		<TD>
Additionally, if the problem persists, please confirm that the group<BR>
piranha exists in /etc/group and that the Group directive defined in<BR>
/etc/sysconfig/ha/conf/httpd.conf is set as piranha.
		</TD>
	</TR>
</TABLE>
</TD></TR></TABLE>
</BODY>
</HTML>
