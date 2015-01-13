<?php
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('libiptables.php');

	// echo "Query = $QUERY_STRING";
?>

<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Global Settings) <?php $debug && print "(DEBUG ON)" ?></TITLE>

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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">FIREWALL</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">MENU:

                <A HREF="iptables_filter_input.php" NAME="FILTER INPUT">FILTER INPUT</A>
                &nbsp;|&nbsp;

                <A HREF="iptables_filter_output.php" CLASS="tabon" NAME="FILTER OUTPUT">FILTER OUTPUT</A>
                &nbsp;|&nbsp;

                <A HREF="iptables_filter_forward.php" NAME="FILTER FORWARD">FILTER FORWARD</A>
                &nbsp;|&nbsp;


                </TD>

        </TR>
</TABLE>


</TD></TR></TABLE>
</BODY>
</HTML>
