# racktables-monitoring

Monitoring Plugin for Racktables.

This is an additional plugin for RackTables, that creates a monitoring tab right beside the 
object page. 

You can configure multiple monitoring servers (supported and tested: Nagios, Icinga) in the
configuration tab - each one with different CSS and JavaScript URLs. The monitoring instance
is selected via a RegEx for each object. 

Example:
* URL:   https://nagios.suse.de/cgi-bin/status.cgi
* RegEx: suse.de
* => For all machines containing a FQDN with "suse.de", the nagios.suse.de machine will be queried.

* URL:   https://icinga.suse.cz/cgi-bin/status.cgi
* RegEx: suse.cz
* => For Machines with a FQDN "suse.cz", the icinga.suse.cz machine will be queried.

While the configuration tab allows to enter a default username and password to log in to the monitoring server, I strongly recommend to use the fallback solution and leave the fields empty.

A nice feature might also be the additional CSS and JavaScript URLs you can configure, which allows to customize the look and feel.

## Installation

* copy the files in the /plugins/ folder to your RackTables plugins installation
* create additional database tables from the 'monitoring.sql' - an example command might be 

<pre>mysql -u root -p racktables_database < monitoring.sql</pre>
