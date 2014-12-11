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

                <A HREF="virtual_edit_real_edit_help.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host&selected=$selected"; } ?> " CLASS="tabon" NAME="HELP">HELP</A>
		&nbsp;|&nbsp;

        </TR>
</TABLE>

<P>

</TD></TR></TABLE>
</BODY>
</HTML>
