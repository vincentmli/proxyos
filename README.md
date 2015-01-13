ProxyOS is an open source Linux distribution based on Centos, but focus on
layer 4 and layer 7 network load balancing. Most of the C code is inherited
from various open source project including Linux virtual server,
nginx/tengine, keepalived. the simple web management gui idea is inspired
from redhat piranha, but completely rewritten to read/write keepalived and
nginx configuration file.

The majority of the load balancing code has been used in Alibaba taobao.com to handle world most busy e-commerce web traffic and in China's number one cloud provider aliyun. 

Some of the  features as below:

Layer 4 TCP virtual server load balancing
        SYNPROXY: Defence against syn flooding attack
        FULLNAT: source and destination address translation

Layer 7 HTTP/HTTPS load balancing
	All features of Nginx 1.4 inherited plus
        load balancing methods, e.g., consistent hashing, session persistence, upstream health check, and resolving upstream domain names on the fly
        Input body filter support. It's quite handy to write Web Application Firewalls using this mechanism
        Dynamic scripting language (Lua) support, which is very efficient and makes it easy to extend core functionalities
        Limits retries for upstream servers (proxy, memcached, fastcgi, scgi, uwsgi)
        Protects the server in case system load or memory use goes too high
        The number of worker processes and CPU affinities can be set automatically
        Request limit to prevent HTTP DDOS and whitelist support and more conditions are allowed in a single location
        Sends unbuffered upload directly to HTTP and FastCGI backend servers, which saves disk I/Os
        Logging enhancements. Syslog (local and remote), pipe logging and log sampling are supported

High-Availability and Virtual Router Redundancy Protocol (VRRP)
        All features of keepalived 1.2.13 inherited plus below
        Add tcp virtual server syn flooding protection configuration
        Add tcp virtual server source and destination address configuration
        Replace select with epoll for better I/O handling

WebUI configuration - easy to use WebUI configuration interface

Linux firewall - WebUI to configure iptables rules
