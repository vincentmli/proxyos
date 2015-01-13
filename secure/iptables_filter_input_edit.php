<?php
        $selected_host = $_GET['selected_host'];
	$vev_action = "";

	if (isset($_GET['vev_action'])) {
		$vev_action = $_GET['vev_action'];
	}

	if ($vev_action == "CANCEL") {
		/* Redirect browser to editing page */
		header("Location: iptables_filter_input_edit.php?selected_host=$selected_host");
		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}

	if (($selected_host == "")) {
		header("Location: iptables_filter_input.php");
		exit;
	}

	
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('libiptables.php'); /* load php iptables lib! Hurragh! */
        $rules_file = '/etc/sysconfig/ha/iptables';
        $ipt = new IptablesConfig($rules_file);



	if ( $vev_action == "ACCEPT" ) {
		if (isset($_GET['match']) && $_GET['match'] != "") {
             		$match = $_GET['match'];
	    	}
		if (isset($_GET['state']) && $_GET['state'] != "") {
             		$state = $_GET['state'];
	    	}
		if (isset($_GET['protocol']) && $_GET['protocol'] != "") {
             		$protocol = $_GET['protocol'];
	    	}
		if (isset($_GET['source']) && $_GET['source'] != "") {
             		$source = $_GET['source'];
	    	}
		if (isset($_GET['sport']) && $_GET['sport'] != "") {
             		$source_port = $_GET['sport'];
	    	}
		if (isset($_GET['destination']) && $_GET['destination'] != "") {
             		$destination = $_GET['destination'];
	    	}
		if (isset($_GET['dport']) && $_GET['dport'] != "") {
             		$dport = $_GET['dport'];
	    	}
		if (isset($_GET['target']) && $_GET['target'] != "") {
             		$target = $_GET['target'];
	    	}
	     	$ruleArray_temp = array (
			'm' => $match,
			'state' => $state,
			'p' => $protocol,
			's' => $source,
			'sport' => $source_port,
			'd' => $destination,
			'dport' => $dport,
			'j' => $target,
	     	);
		foreach($ruleArray_temp as $key => $value) {
			if (empty($value)) unset($ruleArray_temp[$key]);
	 	}
	     	$ipt->replaceRule("filter", "INPUT", $selected_host, $ruleArray_temp);
	     	$ipt->applyNow(false, NULL, $rules_file);

		header("Location: iptables_filter_input.php?selected_host=$selected_host");

	}

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
<script language="javascript" type="text/javascript" src="jquery.validate.js"></script>
<script language="javascript" type="text/javascript" src="superez.js"></script>

<TITLE>Piranha (IPTABLES INPUT - Editing IPTABLES INPUT rule)</TITLE>

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
            <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">EDIT FILTER INPUT</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<?php
	// echo "Query = $QUERY_STRING";

?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#EEEEEE">
                <TD WIDTH="60%">MENU:
                <A HREF="iptables_filter_input.php" NAME="FILTER INPUT">FILTER INPUT</A>
                &nbsp;|&nbsp;
		
		<A HREF="iptables_filter_input_edit.php<?php if (!empty($selected_host)) { echo "?selected_host=$selected_host"; } ?> " CLASS="tabon" NAME="FILTER INPUT EDIT">EDIT FILTER INPUT</A>
		&nbsp;|&nbsp;


        </TR>
</TABLE>

<FORM METHOD="GET" id="filter_input_form" ENCTYPE="application/x-www-form-urlencoded" ACTION="iptables_filter_input_edit.php">
<TABLE>
	<?php
		$ruleArray = $ipt->getRule("filter", "INPUT", $selected_host);
/*
                if (array_key_exists('A', $ruleArray)) {
                      $command = "append";
                } else if (array_key_exists('I', $ruleArray)) {
                      $command = "insert";
                } else if (array_key_exists('R', $ruleArray)) {
                      $command = "replace";
                }
*/

                if (array_key_exists('i', $ruleArray)) {
                      $interface_in = $ruleArray['i'];
                } else if (array_key_exists('in-interface', $ruleArray)) {
                      $interface_in = $ruleArray['in-interface'];
                }

                if (array_key_exists('o', $ruleArray)) {
                      $interface_out = $ruleArray['o'];
                } else if (array_key_exists('out-interface', $ruleArray)) {
                      $interface_out = $ruleArray['out-interface'];
                }

                if (array_key_exists('p', $ruleArray)) {
                      $protocol = $ruleArray['p'];
                } else if (array_key_exists('protocol', $ruleArray)) {
                      $protocol = $ruleArray['protocol'];
                }
               if (array_key_exists('m', $ruleArray)) {
                      $match = $ruleArray['m'];
                } else if (array_key_exists('match', $ruleArray)) {
                      $match = $ruleArray['match'];
                }

                if (array_key_exists('state', $ruleArray)) {
                      $state = $ruleArray['state'];
                }

                if (array_key_exists('s', $ruleArray)) {
                      $source = $ruleArray['s'];
                } else if (array_key_exists('source', $ruleArray)) {
                      $source = $ruleArray['source'];
                }

                if (array_key_exists('sport', $ruleArray)) {
                      $source_port = $ruleArray['sport'];
                } else if (array_key_exists('source-port', $ruleArray)) {
                      $source_port = $ruleArray['source-port'];
                }

                if (array_key_exists('d', $ruleArray)) {
                      $destination = $ruleArray['d'];
                } else if (array_key_exists('destination', $ruleArray)) {
                      $destination = $ruleArray['destination'];
                }

                if (array_key_exists('dport', $ruleArray)) {
                      $dport = $ruleArray['dport'];
                } else if (array_key_exists('destination-port', $ruleArray)) {
                      $dport = $ruleArray['destination-port'];
                }

                if (array_key_exists('j', $ruleArray)) {
                      $target = $ruleArray['j'];
                } else if (array_key_exists('jump', $ruleArray)) {
                      $target = $ruleArray['jump'];
                }


        echo "<TR>";
        	echo "<TD>match: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=match VALUE=\""; echo $match . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>state: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=state VALUE=\""; echo $state . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>protocol: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=protocol VALUE=\""; echo $protocol . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>source: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=source VALUE=\""; echo $source . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>sport: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=sport VALUE=\""; echo $sport . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>destination: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=destination VALUE=\""; echo $destination . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>dport: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=dport VALUE=\""; echo $dport . "\""  . ">"; echo "</TD>";
        echo "</TR>";

        echo "<TR>";
        	echo "<TD>target: </TD>";
        	echo "<TD><INPUT TYPE=TEXT NAME=target VALUE=\""; echo $target . "\""  . ">"; echo "</TD>";
        echo "</TR>";



	?>


</TABLE>
<?php echo "<INPUT TYPE=HIDDEN NAME=selected_host VALUE=$selected_host>" ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD>
                        <INPUT TYPE="Submit" NAME="vev_action" VALUE="ACCEPT">  
                        <SPAN CLASS="taboff">-- Click here to apply changes to this page</SPAN>
                </TD>
                <TD>
                        <INPUT TYPE="Submit" NAME="vev_action" VALUE="CANCEL">  
                        <SPAN CLASS="taboff">-- Click here to cancel the changes</SPAN>
                </TD>
        </TR>
</TABLE>

<!--?php open_file ("w+"); write_config(""); ?-->
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
