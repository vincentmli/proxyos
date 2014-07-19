<?php
	$control_action = "";
	$auto_update = "";
	$rate = "";

	if (isset($_GET['control_action'])) {
        	$control_action = $_GET['control_action'];
	}
	if (isset($_GET['auto_update'])) {
        	$auto_update = $_GET['auto_update'];
	}
	if (isset($_GET['rate'])) {
        	$rate = $_GET['rate'];
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
		
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */
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


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
            <A HREF="introduction.html" CLASS="logolink">
            INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink">
            HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">CONTROL / MONITORING</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


<?php
	// echo "Query = $QUERY_STRING";
?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR BGCOLOR="#666666">
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="control.php" NAME="Control/Monitoring" CLASS="taboff"><B>CONTROL/MONITORING</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="global_settings.php" NAME="Global Settings" CLASS="taboff"><B>GLOBAL SETTINGS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="static_ipaddress.php" NAME="Static ipaddress" CLASS="taboff"><B>STATIC IPADDRESS</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="local_address_group.php" NAME="Local address group" CLASS="taboff"><B>SNAT ADDRESS GROUP</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER"> <A HREF="redundancy.php" NAME="Redundancy" CLASS="taboff"><B>REDUNDANCY</B></A> </TD>
                <TD WIDTH="16.66%" ALIGN="CENTER" BGCOLOR="#FFFFFF"> <A HREF="virtual_main.php" NAME="Virtual" CLASS="tabon"><B>VIRTUAL SERVERS</B></A> </TD>
        </TR>

</TABLE>

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="control.php">
	<P>
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">CONTROL</TD>
        </TR>


	<TR> <TD BGCOLOR="#EEEEEE">
	<?php
		$retval=1;
		exec("/etc/rc.d/init.d/pulse status",$lines,$retval);
		
		if ($retval == 0) { 
			echo "Daemon: <FONT COLOR=\"green\"> running </FONT>";
		} else {
			echo "Daemon: <FONT COLOR=\"#cc0000\"> stopped </FONT>";
		}
	?>
		</TD> </TR> </TABLE>
		<BR>
	<P>

	<P>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">MONITOR</TD>
        </TR>
	</TABLE>
	
	
	<INPUT TYPE="CHECKBOX" NAME="auto_update" VALUE="1" <?php if ($auto_update == 1) { echo "CHECKED"; } ?> > Auto update
	&nbsp;Update Interval: <INPUT TYPE="TEXT" NAME="rate" SIZE=3 VALUE=
		<?php 
			if (($auto_update == "1") && ($rate == "")) {
				$rate="10" ;
			}
			echo $rate ;
			
		?>
	> seconds<BR>
<!--	Rates lower than 10 seconds are not recommended as, when the page updates, you will lose any<BR>
	modifications you have made which have not been actioned using the 'Accept' button<P>
-->
	<INPUT TYPE="SUBMIT" NAME="refresh" VALUE="Update information now">
	<BR><P>
	<HR>


<?php if ( $prim['service'] == "lvs" ) { ?>
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">CURRENT LVS ROUTING TABLE</TD>
        </TR>
	</TABLE>
	
	<TABLE WIDTH="100%" BGCOLOR="#eeeeee"> <TR> <TD> <TT>
	<?php
		#echo `/sbin/ipvsadm -Ln` ;
		#ipvsadm -Ln is not effective as non-root, so we pull the data from /proc
		# (all this code is to replace the hex ip:port with the more standard form)
		$fn="/proc/net/ip_vs";
		if ( is_readable($fn)) {
                    $fd=fopen($fn,"r");
                    while (!feof ($fd)) {
                        $line = fgets($fd, 4096);
                        if ( ereg("([[:xdigit:]]{2})([[:xdigit:]]{2})([[:xdigit:]]{2})([[:xdigit:]]{2}):([[:xdigit:]]{4})",$line,$parts)) {
                            $ip = join(".",array_map("hexdec",array_slice($parts,1,4)));
                            $port = hexdec($parts[5]);
                            $line = str_replace($parts[0],$ip.":".$port,$line );
                        }
                        echo htmlentities(rtrim($line))."<br>";
                    }
                    fclose($fd);
		}

	?>
	</TT> </TD> </TR> </TABLE>
	
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">CURRENT LVS PROCESSES</TD>
        </TR>
	</TABLE>
	
	<TABLE WIDTH="100%" BGCOLOR="#eeeeee"> <TR> <TD> <TT>
	<?php echo nl2br(htmlspecialchars(`/bin/ps auxw | /bin/egrep "pulse|lvs|send_arp|nanny|fos|ipvs" | /bin/grep -v grep`)); ?>
	&nbsp;	
	</TT> </TD> </TR> </TABLE>


<?php } else { ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD CLASS="title">CURRENT FOS PROCESSES</TD>
        </TR>
	</TABLE>

	<TABLE WIDTH="100%" BGCOLOR="#eeeeee"> <TR> <TD>
	<PRE><?php echo `/bin/ps auxw | /bin/egrep "pulse|lvs|send_arp|nanny|fos|ipvs" | /bin/grep -v grep`; ?></PRE>
	&nbsp;	
	</TD> </TR> </TABLE>

	
<?php } ?>

	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5" >
 		<TR BGCOLOR="#666666">
 
               	<!-- Start of comment out
		<TD>
                        <INPUT TYPE="Submit" NAME="control_action" VALUE="ACCEPT"> <SPAN CLASS="taboff"> -- Click here to apply changes to this page</SPAN>
                </TD>
		End of comment -->
		<TD ALIGN=right>
			<INPUT TYPE="Submit" NAME="control_action" VALUE="CHANGE PASSWORD"> <SPAN CLASS="taboff">
		</TD>
       		</TR>
	</TABLE>
	
</FORM>
</TD></TR></TABLE>
</BODY>
</HTML>
