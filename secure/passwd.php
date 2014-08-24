<?php
	if ($help_action =="Close") {
		header("Location: control.php");	/* Redirect browser to editing page */
		exit;  					/* Make sure that code below does not get executed when we redirect. */
	}	

	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

        require('parse.php'); /* read in the config! Hurrah! */
	
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

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>PIRANHA</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
	    <A HREF="introduction.php" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">PASSWORD</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="25%" ALIGN="CENTER" BGCOLOR=#ffffff> <A HREF="control.php" NAME="Control/Monitoring" CLASS="tabon"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
                <TD WIDTH="25%" ALIGN="CENTER"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="taboff"><B>VIRTUAL SERVERS</B></A> </TD>
        </TR>
</TABLE>

<?php
	// echo "Query = $QUERY_STRING";
?>
<P>

<SPAN CLASS=title>PASSWORD CHANGE FOR PIRANHA</SPAN>
<P>
<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="passwd.php">

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="5">
	<TD>To access piranha_gui via the web, you must login as the user 'piranha'.
            The administrator can set the password for this user with the 
            piranha-passwd utility. 
        </TD>
	</TR>
	<TR>
	<TD>For example:</TD>
	</TR>
	<TR>
	<TD>
        <tt>
        [root@server]# /usr/sbin/piranha-passwd<br>
        New Password:<br>
        Verify:<br>
        Updating password for user piranha
        </tt></TD>
	</TR>

</TABLE>

<SPAN CLASS=title>NOTE:</SPAN> Be aware that password changes have <U>immediate</U> effect and that you should choose a passwd that does not contain proper nouns, commonly used acronyms, or words in a dictionary from any language. Also, do not leave the password in clear text anywhere on the system.

<P>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
		<TD>&nbsp;
                </TD>
        </TR>
</TABLE>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
