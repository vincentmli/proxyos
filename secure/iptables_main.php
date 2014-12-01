<?php
	require('libiptables.php'); /* load php iptables lib! Hurragh! */
	$selected_host="";
	$iptables_service="";
        $rules_file = '/etc/sysconfig/ha/iptables';
        $ipt = new IptablesConfig($rules_file);
	$fileTree = $ipt->dumpFiletree();

	if (isset($_POST['selected_host'])) {
        	$selected_host=$_POST['selected_host'];
	}
	if (isset($_POST['iptables_service'])) {
		$iptables_service=$_POST['iptables_service'];
	}

	/* Some magic used to allow the edit command to pull up another web page */
	if ($iptables_service == "EDIT") {
		/* Redirect browser to editing page */
		header("Location: iptables_main_edit.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0


?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Firewall)</TITLE>
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
	}
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

<?php
	$iptables_service = "";
	if (isset($_POST['iptables_service'])) {
		$iptables_service = $_POST['iptables_service'];
	}

	if ($iptables_service == "ADD") {
		
		add_iptables(); /* append new data */
		
	}
	if ($iptables_service == "DELETE" ) {
		/* if ($debug) { echo "About to delete entry number $selected_host<BR>"; } */
		echo "</TD></TR></TABLE><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"5\"><TR><TD BGCOLOR=\"ffffff\"><HR><H2><FONT COLOR=\"#cc0000\" CLASS=\"title\">Click <A HREF=\"iptables_main.php\" NAME=\"iptables\">HERE</A> for refresh</FONT></H2><HR></TD></TR></TABLE>";
		$ipt->removeRule("filter", "INPUT", $selected_host-1);
		$ipt->applyNow(false, NULL, $rules_file);
		exit;
	}

/*

	if ($iptables_service == "(DE)ACTIVATE" ) {
		switch ($virt[$selected_host]['active']) {
			case ""		:	$virt[$selected_host]['active'] = "0";	break;
			case "0"	:	$virt[$selected_host]['active'] = "1";	break;
			case "1"	:	$virt[$selected_host]['active'] = "0";	break;
			default		:	$virt[$selected_host]['active'] = "0";	break;
		}
	}
*/
?>

<?php include 'menu.php'; ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">EDIT:

                <A HREF="iptables_main.php" NAME="FIREWALL">FIREWALL</A>
                &nbsp;|&nbsp;

                <A HREF="iptables_main_edit.php" CLASS="tabon" NAME="EDIT FIREWALL">EDIT FIREWALL</A>
                &nbsp;|&nbsp;

                </TD>

        </TR>
</TABLE>


<FORM METHOD="POST" ENCTYPE="application/x-www-form-urlencoded" ACTION="iptables_main.php">

	<TABLE BORDER="1" CELLSPACING="2" CELLPADDING="6">
		<TR>
			<TD></TD>
			       	<TD CLASS="title">TABLE</TD>
			       	<TD CLASS="title">CHAIN</TD>
			       	<TD CLASS="title">COMMAND</TD>
			       	<TD CLASS="title">PARAMETERS</TD>
			       	<TD CLASS="title">MATCH</TD>
  		              	<TD CLASS="title">TARGET</TD>
		</TR>

<?php
	$loop1 = 1;
#	$rules_file = '/etc/sysconfig/ha/iptables';
#	$ipt = new IptablesConfig($rules_file);
#	$fileTree = $ipt->dumpFiletree();
	
	foreach ($ipt->getAllTables() as $table) {
		foreach ($ipt->getTableChains($table) as $chain) {
			foreach ($ipt->getAllRuleStrings($table, $chain) as $rule) {

				echo "<TR>";
				echo "<TD><INPUT TYPE=RADIO	NAME=selected_host	VALUE=$loop1";
				if ($selected_host == "") { $selected_host = 1; }
				if ($loop1 == $selected_host) { echo " CHECKED "; }
				echo "> </TD>";

				echo "<TD><INPUT TYPE=HIDDEN NAME=table SIZE=16 COLS=10 VALUE="	. $table . ">";
				echo $table . "</TD>";

				echo "<TD><INPUT TYPE=HIDDEN NAME=chain	SIZE=16	COLS=10	VALUE="	. $chain . ">";
				echo $chain . "</TD>";

				echo "<TD><INPUT TYPE=HIDDEN NAME=rule SIZE=16 COLS=10 VALUE=" . $rule . ">";
				echo $rule . "</TD>";
/*
				echo "<TD><INPUT TYPE=HIDDEN NAME=command SIZE=16 COLS=10 VALUE=" . $command . ">";
				echo $command . "</TD>";

				echo "<TD><INPUT TYPE=HIDDEN NAME=parameters SIZE=16 COLS=10 VALUE=" . $parameters . ">";
				echo $parameters . "</TD>";

				echo "<TD><INPUT TYPE=HIDDEN NAME=target SIZE=16 COLS=10 VALUE=" . $target . ">";
				echo $target . "</TD>";
*/
		
				echo "</TR>";
				$loop1++;
			}
		}
	}
?>
	<!-- end of dynamic generation -->

	</TABLE>
	<BR>

	<P>
	<!-- should align beside the above table -->

	<TABLE>
		<TR>
			<TD><INPUT TYPE="SUBMIT" NAME="iptables_service" VALUE="ADD"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="iptables_service" VALUE="DELETE"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="iptables_service" VALUE="EDIT"></TD>
			<TD><INPUT TYPE="SUBMIT" NAME="iptables_service" VALUE="(DE)ACTIVATE"></TD>
		</TR>
	</TABLE>
	<P>
	Note: Use the radio button on the side to select which virtual service you wish to edit before selecting 'EDIT' or 'DELETE'
<?php // echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

<?php
	if ($iptables_service != "DELETE") {
		open_file("w+");
		write_config("");
	}
?>

</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
