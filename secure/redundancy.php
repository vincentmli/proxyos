<?php
	/* try and make this page non cacheable */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
	header("Pragma: no-cache");                                   // HTTP/1.0

	require('parse.php'); /* read in the config! Hurragh! */

?>
<HTML>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML Strict Level 3//EN">

<HEAD>
<TITLE>Piranha (Redundancy)</TITLE>
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

<?php
	global $enable;

	$prim['heartbeat'] = 1; /* argh! - permently set afaik */

	// echo "[$redundancy_action] [$enable] [$redundant] [$redundant_private] [$hb_interval] [$dead_after] [$hb_port]<BR>";
	$redundancy_act = "";
	if (isset($_GET['redundancy_action'])) {
		$redundancy_act = $_GET['redundancy_action'];
	}

	if ($redundancy_act == "ACCEPT") {
		if (isset($_GET['redundant'])) {
			$prim['backup'] = $_GET['redundant'];
		}
		if (isset($_GET['redundant_private'])) {
			$prim['backup_private'] = $_GET['redundant_private'];
		}
		if (isset($_GET['hb_interval'])) {
			$prim['keepalive'] = $_GET['hb_interval'];
		}
		if (isset($_GET['dead_after'])) {
			$prim['deadtime'] = $_GET['dead_after'];
		}
		if (isset($_GET['hb_port'])) {
			$prim['heartbeat_port'] = $_GET['hb_port'];
		}
		if (isset($_GET['monitor_links'])) {
			if ($_GET['monitor_links'] == "on") {
				$prim['monitor_links'] = "1";
			} else { 
				$prim['monitor_links'] = "0";
			}
		} else { 
			$prim['monitor_links'] = "0";
		}
		if (isset($_GET['syncdaemon'])) {
			if ($_GET['syncdaemon'] == "on") {
				$prim['syncdaemon'] = "1";
			} else { 
				$prim['syncdaemon'] = "0";
			}
		} else { 
			$prim['syncdaemon'] = "0";
		}
		if (isset($_GET['syncd_iface'])) {
			$prim['syncd_iface'] = $_GET['syncd_iface'];
                }
		if (isset($_GET['syncd_id'])) {
			$prim['syncd_id'] = $_GET['syncd_id'];
                }

		/*
		$prim['backup_private']		= $_GET['redundant_private'];
		$prim['keepalive'] 		= $_GET['hb_interval'];
		$prim['deadtime'] 		= $_GET['dead_after'];
		$prim['heartbeat_port'] 	= $_GET['hb_port'];
		*/
	}

	if ($prim['backup_active'] == "") {
		$prim['backup_active'] = 0;
	}		

	if (($enable == "1") || ($enable == "0")) {
		$prim['backup_active']		= (1 - $enable) ;
	}

	if ($prim['backup'] == "") {		$prim['backup'] = "0.0.0.0"; }
	if ($prim['backup_private'] == "") {	$prim['backup_private'] = ""; }
	if ($prim['keepalive'] == "") {		$prim['keepalive'] = "6"; }
	if ($prim['deadtime'] == "") {		$prim['deadtime'] = "18"; }
	if ($prim['heartbeat_port'] == "") { 	$prim['heartbeat_port'] = "539";}

	if ($prim['syncdaemon'] == "1") {
                if ($prim['syncd_iface'] == "") {
                    $prim['syncd_iface'] = "eth0";
                }
                if ($prim['syncd_id'] == "") {
                    $prim['syncd_id'] = "0";
                }
	}

	if ($redundancy_act == "RESET" ) {
		$prim['backup'] 	= "0.0.0.0";
		$prim['backup_private']	= "";
		$prim['keepalive'] 	= "6";
		$prim['deadtime']	= "18";
		$prim['heartbeat_port']	= "539";
	}
	
	if ((isset($_GET['full_enable'])) &&
	    ($_GET['full_enable'] == "ENABLE")) {
		$prim['backup_active'] = "1";
	}

	if ($redundancy_act=="DISABLE") {
		$prim['backup_active'] = "0";
	}
?>


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR BGCOLOR="#CC0000"> <TD CLASS="logo"> <B>KEEPALIVED</B> CONFIGURATION TOOL </TD>
	<TD ALIGN=right CLASS="logo">
              <A HREF="introduction.html" CLASS="logolink" >
              INTRODUCTION</A> | <A HREF="help.php" CLASS="logolink" >
              HELP</A></TD>
	</TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR>
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">REDUNDANCY</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>


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

<FORM METHOD="GET" ENCTYPE="application/x-www-form-urlencoded" ACTION="redundancy.php">
	


<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="5">
        <TR>
                <TD BGCOLOR="#EEEEEE" COLSPAN="2">Backup: 
        

	<?php
		if ($prim['backup_active'] == "1") {
			echo "<FONT COLOR=green>active</FONT>";
		} else {
			echo "<FONT COLOR=\"#cc0000\">inactive</FONT>";
		}
	?> 
	</TD> </TR> </TABLE>
<P>

<?php 
	if (isset($_GET['full_enable']) &&
	    ($_GET['full_enable']=="ENABLE")) {
		$prim['backup_active'] = "1";
	}
	if ($prim['backup_active'] == "1") { ?>
	<P>

	<HR>
	<TABLE>	
		<TR>
			<TD> Redundant server public IP:</TD> 
			<TD> <INPUT TYPE="TEXT" NAME="redundant" SIZE=16 VALUE=
			<?php
			if ($prim['backup'] != "") {
				echo $prim['backup'];
			};
			echo ">";
			?>
			</TD>
		</TR>
<?php
	if ($prim['primary_private'] != "") { ?>
		
		<TR>
			<TD> Redundant server private IP:</TD> 
			<TD> <INPUT TYPE="TEXT" NAME="redundant_private" SIZE=16 VALUE=
			<?php
			if ($prim['backup_private'] != "") {
				echo $prim['backup_private'];
			};
			echo ">";
			?>
			</TD>
		</TR>
<?php }; ?>		
		<TR>
			<TD COLSPAN="3"> <HR SIZE="1" WIDTH="100%" NOSHADE></TD>
		</TR>
	
		<TR>
			<TD>Heartbeat interval (seconds):</TD>
			<TD><INPUT TYPE="TEXT" NAME="hb_interval" SIZE=5 VALUE=
			<?php
			if ($prim['keepalive'] != "") {
				echo $prim['keepalive'];
			};
			echo ">";
			?>

			</TD>
		</TR>
		<TR>
			<TD>Assume dead after (seconds):</TD>
			<TD><INPUT TYPE="TEXT" NAME="dead_after" SIZE=5 VALUE=
			<?php
			if ($prim['deadtime'] != "") {
				echo $prim['deadtime'];
			};
			echo ">";
			?>
			</TD>
		</TR>
		<TR>
			<TD>Heartbeat runs on port:</TD>
			<TD><INPUT TYPE="TEXT" NAME="hb_port" SIZE=5 VALUE=
			<?php
			if ($prim['heartbeat_port'] != "") {
				echo $prim['heartbeat_port'];
			}
			echo ">";
			?>
			</TD>
		</TR>
		<TR>
			<TD>Monitor NIC links for failures:</TD>
			<TD><INPUT TYPE="checkbox" NAME="monitor_links" 
			<?php
			if ($prim['monitor_links'] == "1") {
				echo "CHECKED";
			};
			echo ">";
			?>
			</TD>
		</TR>
		<TR>
			<TD>Use sync daemon:</TD>
			<TD><INPUT TYPE="checkbox" NAME="syncdaemon" 
			<?php
			if ($prim['syncdaemon'] == "1") {
				echo "CHECKED";
			};
			echo ">";
			?>
			</TD>
		</TR>
		<TR>
                        <TD>Sync daemon interface:</TD>
			<TD><INPUT TYPE="TEXT" NAME="syncd_iface" SIZE=5 VALUE=
			<?php
                        if ($prim['syncd_iface'] != "") {
                                echo $prim['syncd_iface'];
			}
                        echo ">"
			?>
                        </TD>
                </TR>
		<TR>
                        <TD>Sync daemon ID:</TD>
			<TD><INPUT TYPE="TEXT" NAME="syncd_id" SIZE=5 VALUE=
			<?php
                        if ($prim['syncd_id'] != "") {
                                echo $prim['syncd_id'];
			}
                        echo ">"
			?>
                        </TD>
                </TR>
	</TABLE>
	<HR>
<?php } ?>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
        <TR BGCOLOR="#666666">
		<?php if ( $prim['backup_active'] == "0" ) { ?>
			<TD><INPUT TYPE="submit" NAME="full_enable" VALUE=ENABLE></TD>
			<!-- <TD ALIGN=right ><INPUT TYPE="submit" NAME="enable" VALUE=DISABLE></TD> -->
		<?php } else { ?>
			<TD><INPUT TYPE="Submit" NAME="redundancy_action" VALUE="ACCEPT">  <SPAN CLASS="taboff">-- Click here to apply changes to this page</SPAN></TD>
			<TD ALIGN=right><SPAN CLASS="taboff"></SPAN><INPUT TYPE="Submit" NAME="redundancy_action" VALUE="DISABLE"><INPUT TYPE="Submit" NAME="redundancy_action" VALUE="RESET">
		<?php } ?>
        </TR>
</TABLE>


<?php open_file ("w+"); write_config(""); ?>

</TD></TR></TABLE>
</FORM>
</BODY>
</HTML>
