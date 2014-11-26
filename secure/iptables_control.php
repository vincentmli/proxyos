<?php
	$control_action = "";
	$auto_update = "";
	$rate = "";
	$backup = "";

	if (isset($_GET['control_action'])) {
        	$control_action = $_GET['control_action'];
	}
	if (isset($_GET['auto_update'])) {
        	$auto_update = $_GET['auto_update'];
	}
	if (isset($_GET['rate'])) {
        	$rate = $_GET['rate'];
	}
	if (isset($_GET['backup'])) {
		$backup = $_GET['backup'];
	}

	if ($auto_update == 1) {
        	if ($rate == '' || $rate < 10) { 
			$rate=10; 
		}
        }
	if ($control_action == "CHANGE PASSWORD") {
		header("Location: passwd.php");
		exit;
	}
	
	

	if ($control_action == "LOAD CONFIG") {
		$temp = tempnam(sys_get_temp_dir(), 'ipvs');
		$today = date("Y-m-d-H:i:s"); 
		exec("/usr/bin/sudo /usr/bin/rsync -p -o --backup --backup-dir=/etc/sysconfig/ha --suffix $today /etc/sysconfig/ha/lvs.cf /etc/keepalived/keepalived.conf>>".$temp." 2>&1");
                exec('/usr/bin/sudo /sbin/service keepalived reload >>'.$temp.' 2>&1', $output, $rc);
		if ($rc == 0) {
			exec("/usr/bin/sudo /bin/cp -f /etc/sysconfig/ha/lvs.cf /etc/sysconfig/ha/keepalived.conf>>".$temp." 2>&1");
			exec("/usr/bin/sudo /bin/chown root.piranha /etc/sysconfig/ha/keepalived.conf>>".$temp." 2>&1");
		}
		#unlink($temp);

		header("Location: control.php");
		exit;
	}

	if ($control_action == "RESTORE CONFIG") {
		$temp = tempnam(sys_get_temp_dir(), 'keepalived');
		exec("/usr/bin/sudo /bin/cp -f $backup /etc/keepalived/keepalived.conf>>".$temp." 2>&1");
                exec('/usr/bin/sudo /sbin/service keepalived reload >>'.$temp.' 2>&1', $output, $rc);
		if ($rc == 0) {
			exec("/usr/bin/sudo /bin/cp -f $backup /etc/sysconfig/ha/keepalived.conf>>".$temp." 2>&1");
			exec("/usr/bin/sudo /bin/chown root.piranha /etc/sysconfig/ha/keepalived.conf>>".$temp." 2>&1");
		}
		#unlink($temp);

		header("Location: control.php");
		exit;
	}

		
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */
    	require('libiptables.php');
	if ($auto_update == "1") {
		echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"$rate;control.php?auto_update=1&rate=$rate\"> ";
	}
	if ($prim['service'] == "") {
		$prim['service'] = "lvs";
	}
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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">CONTROL / MONITORING</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<?php
	// echo "Query = $QUERY_STRING";
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="control.php">
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

	echo  var_dump($fileTree) ;
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
