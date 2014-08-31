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
                <TD>&nbsp;<BR><FONT SIZE="+2" COLOR="#CC0000">HELP</FONT><BR>&nbsp;</TD>
        </TR>
</TABLE>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR><TD BGCOLOR="#FFFFFF">

<?php include 'menu.php'; ?>

<?php
	// echo "Query = $QUERY_STRING";
?>
<P>

<SPAN CLASS=title>CONTROL/MONITORING</SPAN>
<P>
The purpose of this panel is to guide the cluster administrator through the process of setting up the /etc/sysonfig/ha/lvs.cf configuration file
and to provide a limited monitoring the runtime status of the cluster setup<BR>

Monitoring of the LVS is quite basic and has the facility to automatically update the display at timed intervals. It is thought to be
unwise to try and update more than once every 10 seconds, but you may do so if desired. This is because it is difficult
to change the state of the autoupdate while it's constantly updating. If you find this happens, simply click out to another
panel eg 'global settings' and then back again.
<BR>
Summary:

Update information now
        Display current LVS runtime status.

Auto-update
        Select to display LVS runtime status automatically at the specified
        interval.

<P>
<SPAN CLASS=title>GLOBAL SETTINGS</SPAN>
<P>
<PRE>
Summary:

Primary LVS server IP:
        Contains the public IP address of the primary LVS node.

Primary server private IP:
	Indicates the IP address of an alternative  network device  for private
	heartbeating.  It is not necessary to fill out this field for piranha to
	work as it  simply provides an alternative method of checking an IP
	service is running.

Use network type:
	Select between use of NAT, direct routing or tunneling as the failover
	mechanism

NAT Router IP:
        Contains the floating IP address associated with the network adapter
        connecting the node with the real server host subnet. If this
        node fails, this address migrates to the backup LVS node.

NAT Router netmask:
	This feature allows you to setup split networks where one machine
	exists in a physically seperate but logically similar network. Please refer
	to the HA manual for more details. Normally you can get away with not
	setting this option, or if you think you should set it, the normal netmask
	of the ethernet device is normally sufficiant.

NAT Router Device:
        Associate the floating NAT router address with the network adapter
        name, which must be the same on both LVS nodes.

</PRE>
<P>
<SPAN CLASS=title>REDUNDANCY</SPAN>
<P>
<PRE>
Enable backup server:
        Select to enable failover, and attempt configuration file sync.

Redundant LVS server IP:
        Contains the public IP address of the backup LVS node. 

Heartbeat interval (seconds):
        Contains the number of seconds between heartbeats: the interval
        at which the backup LVS node checks to see if the primary LVS
        node is alive.

Assume dead after (seconds):
        If this number of seconds lapses without a response from the primary
        LVS node, the backup node initiates failover.

Heartbeat runs on port:
	It is possible to alter the port that heartbeat runs on. The default
	if not set is 1050. Normally you should not have to touch this.
</PRE>
<P>
<SPAN CLASS=title>VIRTUAL SERVERS</SPAN>
<P>
This screen displays a row of information for each currently defined virtual
server. Click a row to select it (use the radio button on the left hand side). The buttons on the bottom of the
screen apply to the currently selected virtual server. Click Delete to remove
the selected virtual server. Add will create a 'blank' entry to use. 
You will also notice a '(de)activate' button which is used to enable or disable the state of the service.
<PRE>
Status:
        Displays Active or Down. Click Disable to take down a selected active
        virtual server; click Activate to enable a selected down virtual
        server.
Name:
        The node's name (not the hostname, although it could be).
	eg 'my_web_server' or 'www.fun-html.net'

Port:
        The listen port for incoming requests for service.

Protocol:
        specify tcp or udp


Add/Edit a Virtual Server:

Click the edit button to define a newly-created virtual server
or change an existing virtual server.
</PRE>
<P>
<SPAN CLASS=title>EDIT VIRTUAL SERVER</SPAN>
<P>
<PRE>
Name:
	Enter a descriptive name. Not necessarily the machine's hostname

Application port:
	Type in the service port of the daemon eg for HTTP use 80, for
	FTP use 21, SSH port 22 etc. You should find a list of the commonly
	accepted services in /etc/services

Address:
	Enter the floating IP address where requests for service arrive.
        If this node fails, the address and port are failed-over to the backup
        LVS node.

Virtual IP Network Mask:
	In a similar manner to the NAT Router netmask option, the VIP
	netmask allows you to setup much more complex networking options
	Again, you have the option of simply not using it or of setting it to the
	default netmask of the interface are safe values.
	
Device:
	Associate the floating IP address with the network adapter that
        connects the LVS nodes to the public network. 

Re-entry Time:
	Enter the number of seconds that a failed server (eg Web/FTP) host
        associated with this virtual server must remain alive before it
        will be re-added to the pool.

Service timeout:
	Length of time in seconds before we decide that the server has taken
	too long to respond and can be considered dead.
	
Load monitoring tool:
	Select the tool to use (none, rup, or ruptime) for determining
        the workload on Web/FTP server hosts.

Scheduling:
	Select the altorithm for request routing from this virtual server
        to the Web/FTP server hosts that will perform the services:

        Select Weighted least-connections
        Assign more jobs to hosts that are least busy relative to their
        processing capacity.

        Weighted round robin
        Assign more jobs to hosts with greater processing capacity.

        Round robin
        Distribute jobs equally among the hosts. 

Persistence:
        If greater than zero, enables persistent connection support and
        specifies a timeout value.

Persistence Network Mask:
        If persistence is enabled, this is a netmask to apply to routing
        rules for enabling subnets for persistence.
</PRE>
<P>
<SPAN CLASS=title>EDIT REAL SERVER</SPAN>
<P>
Click the Add button to create an association with a new, undefined Web/server
host. Click the Edit button to define a new host or change an existing one. When
you use this option, to retun to the original page, just click on 'real server'
on the edit title line. Use (de)activate to toggle the availability of this
host in the LVS cluster.
<PRE>
Name:
        Enter a descriptive name.

Address:
        Enter the Web/FTP server's IP address. The listening port will be
        the one specified for the associated virtual server. 

Weight:
        Assign an integer to indicate this host's processing capacity
        relative to that of other hosts in the pool.
</PRE>
<P>
<SPAN CLASS=title>MONITORING SCRIPTS</SPAN>
<P>  
This section allows you to specify a send/expect string sequence
for verifying an IP service is functional. Only one send and/or
expect sequence is allowed, and can only contain printable
characters plus (\n, \r, \t, and/or \').
These messages are occasionally fired at the server and the responses read, if the response
is identical to that expected, no failover occurs.
<P>
&nbsp;

</TD></TR></TABLE>
</BODY>
</HTML>
