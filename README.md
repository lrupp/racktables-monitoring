# racktables-monitoring

Monitoring Plugin for Racktables.

This is an additional plugin for RackTables, that creates a monitoring tab right beside the 
object page. 

You can configure multiple monitoring servers (supported and tested: Nagios, Icinga) in the
configuration tab - each one with different CSS and JavaScript URLs. The monitoring instance
is selected via a RegEx for each object. 

Example:
URL:   https://nagios.suse.de/cgi-bin/status.cgi
RegEx: suse.de
=> For all machines containing a FQDN with "suse.de", the nagios.suse.de machine will be 
   queried.

URL:   https://icinga.suse.cz/cgi-bin/status.cgi
RegEx: suse.cz
=> For Machines with a FQDN "suse.cz", the icinga.suse.cz machine will be queried.


