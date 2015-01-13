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

<SPAN CLASS=title>CONTROL</SPAN>
<P>
The purpose of this panel is to guide the cluster administrator through the process of setting up the keepalived configuration file
and to provide a limited monitoring the runtime status of the cluster setup<BR>

after finishing keepalived configuration, load the config to reload keepalived to replace the running configuration. also you can 
select an old configuration to restore. 
<BR>
Summary:

Update information now
        Display current LVS runtime status.

Auto-update
        Select to display LVS runtime status automatically at the specified
        interval.

<P>
<SPAN CLASS=title>GLOBAL</SPAN>
<P>
<PRE>
keepalived global definition

        global_defs           # Block id
        {
        notification_email    # To:
               {
               admin@example1.com
               ...
               }
        # From: from address that will be in header
        notification_email_from admin@example.com
        smtp_server 127.0.0.1        # IP
        smtp_connect_timeout 30      # integer, seconds
        router_id my_hostname        # string identifying the machine,
                                     # (doesn’t have to be hostname).
        vrrp_mcast_group4 224.0.0.18 # optional, default 224.0.0.18
        vrrp_mcast_group6 ff02::12   # optional, default ff02::12
        enable_traps                 # enable SNMP traps
	}

Static routes/addresses
       keepalived can configure static addresses and routes. These addresses are NOT moved by vrrpd, they stay on the machine.  If you
       already have IPs and routes on your machines and your machines can ping each other, you don’t need this section.

       The syntax is the same as for virtual addresses and virtual routes.

        static_ipaddress
        {
        192.168.1.1/24 dev eth0 scope global
        ...
        }

        static_routes
        {
        192.168.2.0/24 via 192.168.1.100 dev eth0
        ...
        }

Local source address translation group (FULLNAT)
	you can define the these ip in keepalived VRRP instance virtual ip section
        so these ip are floating ip between HA pair
        for example:
	local_address_group laddr_g1 {
     		192.168.3.164
     		192.168.3.165
     		192.168.3.167
	}


</PRE>
<P>
<SPAN CLASS=title>FAILOVER</SPAN>
<P>
<PRE>

VRRPD CONFIGURATION
       contains subblocks of VRRP synchronization group(s) and VRRP instance(s)

VRRP synchronization group(s)
        #string, name of group of IPs that failover together
        vrrp_sync_group VG_1 {
           group {
             inside_network   # name of vrrp_instance (below)
             outside_network  # One for each moveable IP.
             ...
           }

           # notify scripts and alerts are optional
           #
           # filenames of scripts to run on transitions
           # can be unquoted (if just filename)
           # or quoted (if has parameters)
           # to MASTER transition
           notify_master /path/to_master.sh
           # to BACKUP transition
           notify_backup /path/to_backup.sh
           # FAULT transition
           notify_fault "/path/fault.sh VG_1"

           # for ANY state transition.
           # "notify" script is called AFTER the
           # notify_* script(s) and is executed
           # with 3 arguments provided by keepalived
           # (ie don’t include parameters in the notify line).
           # arguments
           # $1 = "GROUP"|"INSTANCE"
           # $2 = name of group or instance
           # $3 = target state of transition
           #     ("MASTER"|"BACKUP"|"FAULT")
           notify /path/notify.sh

           # Send email notifcation during state transition,
           # using addresses in global_defs above.
           smtp_alert
        }

VRRP instance(s)
       describes  the  moveable IP for each instance of a group in vrrp_sync_group.  Here are described two IPs (on inside_network and
       on outside_network), on machine "my_hostname", which belong to the group VG_1 and which will transition together on  any  state
       change.

        #You will need to write another block for outside_network.
        vrrp_instance inside_network {
           # Initial state, MASTER|BACKUP
           # As soon as the other machine(s) come up,
           # an election will be held and the machine
           # with the highest "priority" will become MASTER.
           # So the entry here doesn’t matter a whole lot.
           state MASTER

           # interface for inside_network, bound by vrrp
           interface eth0

           # Use VRRP Virtual MAC.
           use_vmac <VMAC_INTERFACE>

           # Send/Recv VRRP messages from base interface instead of
           # VMAC interface
           vmac_xmit_base

           # Ignore VRRP interface faults (default unset)
           dont_track_primary

           # optional, monitor these as well.
           # go to FAULT state if any of these go down.
           track_interface {
             eth0
             eth1
             ...
           }

           # default IP for binding vrrpd is the primary IP
           # on interface. If you want to hide location of vrrpd,
           # use this IP as src_addr for multicast or unicast vrrp
           # packets. (since it’s multicast, vrrpd will get the reply
           # packet no matter what src_addr is used).
           # optional
           mcast_src_ip <IPADDR>
           unicast_src_ip <IPADDR>

           # Do not send VRRP adverts over VRRP multicast group.
           # Instead it sends adverts to the following list of
           # ip addresses using unicast design fashion. It can
           # be cool to use VRRP FSM and features in a networking
           # environement where multicast is not supported !
           # IP Addresses specified can IPv4 as well as IPv6
           unicast_peer {
             <IPADDR>
             ...
           }

           # Binding interface for lvs syncd
           lvs_sync_daemon_interface eth1

           # delay for gratuitous ARP after transition to MASTER
           garp_master_delay 10 # secs, default 5

           # arbitary unique number 0..255
           # used to differentiate multiple instances of vrrpd
           # running on the same NIC (and hence same socket).
           virtual_router_id 51

           # for electing MASTER, highest priority wins.
           # to be MASTER, make 50 more than other machines.
           priority 100

           # VRRP Advert interval, secs (use default)
           advert_int 1
           authentication {     # Authentication block
               # PASS||AH
               # PASS - Simple Passwd (suggested)
               # AH - IPSEC (not recommended))
               auth_type PASS
               # Password for accessing vrrpd.
               # should be the same for all machines.
               # Only the first eight (8) characters are used.
               auth_pass 1234
           }

           #addresses add|del on change to MASTER, to BACKUP.
           #With the same entries on other machines,
           #the opposite transition will be occuring.
           virtual_ipaddress {
               <IPADDR>/<MASK> brd <IPADDR> dev <STRING> scope <SCOPE> label <LABEL>
               192.168.200.17/24 dev eth1
               192.168.200.18/24 dev eth2 label eth2:1
           }
           #VRRP IP excluded from VRRP
           #optional.
           #For cases with large numbers (eg 200) of IPs
           #on the same interface. To decrease the number
           #of packets sent in adverts, you can exclude
           #most IPs from adverts.
           #The IPs are add|del as for virtual_ipaddress.
           virtual_ipaddress_excluded {
            <IPADDR>/<MASK> brd <IPADDR> dev <STRING> scope <SCOPE>
            <IPADDR>/<MASK> brd <IPADDR> dev <STRING> scope <SCOPE>
               ...
           }
           # routes add|del when changing to MASTER, to BACKUP
           virtual_routes {
               # src <IPADDR> [to] <IPADDR>/<MASK> via|gw <IPADDR> [or <IPADDR>] dev <STRING> scope <SCOPE> tab
               src 192.168.100.1 to 192.168.109.0/24 via 192.168.200.254 dev eth1
               192.168.110.0/24 via 192.168.200.254 dev eth1
               192.168.111.0/24 dev eth2
               192.168.112.0/24  via 192.168.100.254      192.168.113.0/24 via 192.168.200.254 or 192.168.100.254 dev eth1      black-
       hole 192.168.114.0/24
           }

           # VRRP will normally preempt a lower priority
           # machine when a higher priority machine comes
           # online.  "nopreempt" allows the lower priority
           # machine to maintain the master role, even when
           # a higher priority machine comes back online.
           # NOTE: For this to work, the initial state of this
           # entry must be BACKUP.
           nopreempt

           # Seconds after startup until preemption
           # (if not disabled by "nopreempt").
           # Range: 0 (default) to 1,000
           # NOTE: For this to work, the initial state of this
           # entry must be BACKUP.
           preempt_delay 300    # waits 5 minutes
           # Debug level, not implemented yet.
           debug

           # notify scripts, alert as above
           notify_master <STRING>|<QUOTED-STRING>
           notify_backup <STRING>|<QUOTED-STRING>
           notify_fault <STRING>|<QUOTED-STRING>
           notify <STRING>|<QUOTED-STRING>
           smtp_alert
        }


</PRE>
<P>
<SPAN CLASS=title>LAYER 4</SPAN>
<P>
This screen displays a row of information for each currently defined virtual
server. Click a row to select it (use the radio button on the left hand side). The buttons on the bottom of the
screen apply to the currently selected virtual server. Click Delete to remove
the selected virtual server. Add will create a 'blank' entry to use. 
You will also notice a '(de)activate' button which is used to enable or disable the state of the service.
<PRE>

LVS CONFIGURATION
       contains subblocks of Virtual server group(s) and Virtual server(s)

       The subblocks contain arguments for ipvsadm(8).  A knowlege of ipvsadm(8) will be helpful here.

Virtual server group(s)
        # optional
        # this groups allows a service on a real_server
        # to belong to multiple virtual services
        # and to be only health checked once.
        # Only for very large LVSs.
        virtual_server_group <STRING> {
               #VIP port
               <IPADDR> <PORT>
               <IPADDR> <PORT>
               ...
               #
               # <IPADDR RANGE> has the form
               # XXX.YYY.ZZZ.WWW-VVV eg 192.168.200.1-10
               # range includes both .1 and .10 address
               <IPADDR RANGE> <PORT># VIP range VPORT
               <IPADDR RANGE> <PORT>
               ...
               fwmark <INT>  # fwmark
               fwmark <INT>
               ...  }
Virtual server(s)
       A virtual_server can be a declaration of one of

       vip vport (IPADDR PORT pair)

       fwmark <INT>

       (virtual server) group <STRING>

                  #setup service
                  virtual_server IP port |
                  virtual_server fwmark int |
                  virtual_server group string
                  {
                  # delay timer for service polling
                  delay_loop <INT>

                  # LVS scheduler
                  lb_algo rr|wrr|lc|wlc|lblc|sh|dh
                  # Enable One-Packet-Scheduling for UDP (-O in ipvsadm)
                  ops
                  # LVS forwarding method
                  lb_kind NAT|DR|TUN
                  # LVS persistence timeout, sec
                  persistence_timeout <INT>
                  # LVS granularity mask (-M in ipvsadm)
                  persistence_granularity <NETMASK>
                  # Only TCP is implemented
                  protocol TCP
                  # If VS IP address is not set,
                  # suspend healthchecker’s activity
                  ha_suspend

                  # VirtualHost string for HTTP_GET or SSL_GET
                  # eg virtualhost www.firewall.loc
                  virtualhost <STRING>
                  # Assume silently all RSs down and healthchecks
                  # failed on start. This helps preventing false
                  # positive actions on startup. Alpha mode is
                  # disabled by default.
                  alpha

                  # On daemon shutdown, consider quorum and RS
                  # down notifiers for execution, where appropriate.
                  # Omega mode is disabled by default.
                  omega

                  # Minimum total weight of all live servers in
                  # the pool necessary to operate VS with no
                  # quality regression. Defaults to 1.
                  quorum <INT>

                  # Tolerate this much weight units compared to the
                  # nominal quorum, when considering quorum gain
                  # or loss. A flap dampener. Defaults to 0.
                  hysteresis <INT>

                  # Script to launch when quorum is gained.
                  quorum_up <STRING>|<QUOTED-STRING>

                  # Script to launch when quorum is lost.
                  quorum_down <STRING>|<QUOTED-STRING>

                  # setup realserver(s)

                  # RS to add when all realservers are down
                  sorry_server <IPADDR> <PORT>
                  # applies inhibit_on_failure behaviour to the
                  # preceding sorry_server directive
                  sorry_server_inhibit

                  # one entry for each realserver
                  real_server <IPADDR> <PORT>
                     {
                         # relative weight to use, default: 1
                         weight <INT>
                         # Set weight to 0
                         # when healthchecker detects failure
                         inhibit_on_failure

                         # Script to launch when healthchecker
                         # considers service as up.
                         notify_up <STRING>|<QUOTED-STRING>
                         # Script to launch when healthchecker
                         # considers service as down.
                         notify_down <STRING>|<QUOTED-STRING>

                         # pick one healthchecker
                         # HTTP_GET|SSL_GET|TCP_CHECK|SMTP_CHECK|MISC_CHECK

                         # HTTP and SSL healthcheckers
                         HTTP_GET|SSL_GET
                         {
                             # A url to test
                             # can have multiple entries here
                             url {
                               #eg path / , or path /mrtg2/
                               path <STRING>
                               # healthcheck needs status_code
                               # or status_code and digest
                               # Digest computed with genhash
                               # eg digest 9b3a0c85a887a256d6939da88aabd8cd
                               digest <STRING>
                               # status code returned in the HTTP header
                               # eg status_code 200
                               status_code <INT>
                             }
                             # number of get retry
                             nb_get_retry <INT>
                             # delay before retry
                             delay_before_retry <INT>
                             # ======== generic connection options
                             # Optional IP address to connect to.
                             # The default is real server’s IP
                             connect_ip <IP ADDRESS>
                             # Optional port to connect to if not
                             # The default is real server’s port
                             connect_port <PORT>
                             # Optional interface to use to
                             # originate the connection
                             bindto <IP ADDRESS>
                             # Optional source port to
                             # originate the connection from
                             bind_port <PORT>
                             # Optional connection timeout in seconds.
                             # The default is 5 seconds
                             connect_timeout <INTEGER>
                             # Optional fwmark to mark all outgoing
                             # checker pakets with
                             fwmark <INTEGER>

                             # Optional random delay to begin initial check for
                             # maximum N seconds.
                             # Useful to scatter multiple simultaneous
                             # checks to the same RS. Enabled by default, with
                             # the maximum at delay_loop. Specify 0 to disable
                             warmup <INT>
                         } #HTTP_GET|SSL_GET

                         #TCP healthchecker (bind to IP port)
                         TCP_CHECK
                         {
                             # ======== generic connection options
                             # Optional IP address to connect to.
                             # The default is real server’s IP
                             connect_ip <IP ADDRESS>
                             # Optional port to connect to if not
                             # The default is real server’s port
                             connect_port <PORT>
                             # Optional interface to use to
                             # originate the connection
                             bindto <IP ADDRESS>
                             # Optional source port to
                             # originate the connection from
                             bind_port <PORT>
                             # Optional connection timeout in seconds.
                             # The default is 5 seconds
                             connect_timeout <INTEGER>
                             # Optional fwmark to mark all outgoing
                             # checker pakets with
                             fwmark <INTEGER>

                             # Optional random delay to begin initial check for
                             # maximum N seconds.
                             # Useful to scatter multiple simultaneous
                             # checks to the same RS. Enabled by default, with
                             # the maximum at delay_loop. Specify 0 to disable
                             warmup <INT>
                         } #TCP_CHECK

                         # SMTP healthchecker
                         SMTP_CHECK
                         {
                             # An optional host interface to check.
                             # If no host directives are present, only
                             # the ip address of the real server will
                             # be checked.
                             host {
                               # ======== generic connection options
                               # Optional IP address to connect to.
                               # The default is real server’s IP
                               connect_ip <IP ADDRESS>
                               # Optional port to connect to if not
                               # the default of 25
                               connect_port <PORT>
                               # Optional interface to use to
                               # originate the connection
                               bindto <IP ADDRESS>
                               # Optional source port to
                               # originate the connection from
                               bind_port <PORT>
                               # Optional per-host connection timeout.
                               # Default is outer-scope connect_timeout
                               connect_timeout <INTEGER>
                               # Optional fwmark to mark all outgoing
                               # checker pakets with
                               fwmark <INTEGER>
                            }
                            # Connection and read/write timeout
                            # in seconds. The default is 5 seconds
                            connect_timeout <INTEGER>
                            # Number of times to retry a failed check
                            retry <INTEGER>
                            # Delay in seconds before retrying
                            delay_before_retry <INTEGER>
                            # Optional string to use for the smtp HELO request
                            helo_name <STRING>|<QUOTED-STRING>

                            # Optional random delay to begin initial check for
                            # maximum N seconds.
                            # Useful to scatter multiple simultaneous
                            # checks to the same RS. Enabled by default, with
                            # the maximum at delay_loop. Specify 0 to disable
                            warmup <INT>
                         } #SMTP_CHECK

                         #MISC healthchecker, run a program
                         MISC_CHECK
                         {
                             # External system script or program
                             misc_path <STRING>|<QUOTED-STRING>
                             # Script execution timeout
                             misc_timeout <INT>

                             # Optional random delay to begin initial check for
                             # maximum N seconds.
                             # Useful to scatter multiple simultaneous
                             # checks to the same RS. Enabled by default, with
                             # the maximum at delay_loop. Specify 0 to disable
                             warmup <INT>

                             # If set, exit code from healthchecker is used
                             # to dynamically adjust the weight as follows:
                             #   exit status 0: svc check success, weight
                             #     unchanged.
                             #   exit status 1: svc check failed.
                             #   exit status 2-255: svc check success, weight
                             #     changed to 2 less than exit status.
                             #   (for example: exit status of 255 would set
                             #     weight to 253)
                             misc_dynamic
                         }
                     } # realserver defn
                  } # virtual service

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

</PRE>
<P>
<SPAN CLASS=title>MONITORING SCRIPTS</SPAN>
<P>  
<P>
&nbsp;

</TD></TR></TABLE>
</BODY>
</HTML>
