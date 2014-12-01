<?php
	$control_action = "";
	$auto_update = "";
	$rate = "";
	$backup = "";

	if (isset($_GET['control_action'])) {
        	$control_action = $_GET['control_action'];
	}

		
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

    	require('libiptables.php');
?>


<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Control/Monitoring)</TITLE>
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


<?php
	// echo "Query = $QUERY_STRING";
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="iptables_control.php">
	<P>



	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">IPTABLES</TD>
        </TR>
	</TABLE>

	<TABLE WIDTH="100%" BGCOLOR="#eeeeee"> <TR> <TD> <TT>
	<?php
    // Path of iptables rules. Yours may differ.
    	$rules_file = '/etc/sysconfig/ha/iptables';
    	$ipt = new IptablesConfig($rules_file);
	$fileTree = $ipt->dumpFiletree();
    	foreach ($ipt->getAllTables() as $t) {
        	echo "<pre>Table <b>$t</b><br/>";
        	foreach ($ipt->getTableChains($t) as $c) {
             		echo "  Chain <b>$c</b><br/>";
                 	foreach ($ipt->getAllRuleStrings($t, $c) as $r) {
                     		echo "    <b>Rule</b> $r<br/>";
                	}
        	}
        	echo '</pre>';
    	}

	#echo  var_dump($fileTree) ;
	$ruleArray =  array (
		'A' => 'INPUT',
		'p' => 'tcp',
		'dport' => '888',
		'j' => 'ACCEPT',
	);
	#$ipt->appendRule("filter", "INPUT", $ruleArray); 
	$fileName = $rules_file;
	$ipt->applyNow(false, NULL, $fileName);

	#echo  var_dump($fileTree) ;
	
	?>

	</TT> </TD> </TR>
	<TR> <TD ALIGN=left>
		<INPUT TYPE="Submit" NAME="control_action" VALUE="LOAD CONFIG"> <SPAN CLASS="taboff">
	</TD> </TR>
	 </TABLE>

	
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
