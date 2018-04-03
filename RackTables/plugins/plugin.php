<?php

require_once "monitoringExtensionLib.php";

function plugin_monitoring_info ()
{
        return array
        (
                'name' => 'monitoring',
                'longname' => 'Monitoring',
                'version' => '0.1',
                'home_url' => 'https://github.com/lrupp/racktables-monitoring'
        );
}

function plugin_monitoring_init ()
{
        global $interface_requires, $opspec_list, $page, $tab, $trigger;
        $tab['object']['monitoring'] = 'Monitoring';
        registerTabHandler ('object', 'monitoring', 'renderObjectMonitoring');
        $trigger['object']['monitoring'] = 'triggerMonitoring';
        $ophandler['object']['monitoring']['add'] = 'tableHandler';
        $ophandler['object']['monitoring']['del'] = 'tableHandler';

        $page['monitoring']['title'] = 'Monitoring';
        $page['monitoring']['parent'] = 'config';
        $tab['monitoring']['default'] = 'View';
        $tab['monitoring']['servers'] = 'Manage servers';
        registerTabHandler ('monitoring', 'default', 'renderMonitoringConfig');
        registerTabHandler ('monitoring', 'servers', 'renderMonitoringServersEditor');
        registerOpHandler ('monitoring', 'servers', 'add', 'tableHandler');
        registerOpHandler ('monitoring', 'servers', 'del', 'tableHandler');
        registerOpHandler ('monitoring', 'servers', 'upd', 'tableHandler');
        $interface_requires['monitoring-*'] = 'interface-config.php';

        registerHook ('dispatchImageRequest_hook', 'plugin_monitoring_dispatchImageRequest');
        registerHook ('resetObject_hook', 'plugin_monitoring_resetObject');
        registerHook ('resetUIConfig_hook', 'plugin_monitoring_resetUIConfig');

        $opspec_list['monitoring-servers-add'] = array
        (
                'table' => 'MonitoringServer',
                'action' => 'INSERT',
                'arglist' => array
                (
                    array ('url_argname' => 'name',         'assertion' => 'string0'),
                    array ('url_argname' => 'base_url',     'assertion' => 'string'),
                    array ('url_argname' => 'css',          'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'javascript',   'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'regularexp',   'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'username',     'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'password',     'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                ),
        );
        $opspec_list['monitoring-servers-del'] = array
        (
                'table' => 'MonitoringServer',
                'action' => 'DELETE',
                'arglist' => array
                (
                        array ('url_argname' => 'id', 'assertion' => 'uint'),
                ),
        );
        $opspec_list['monitoring-servers-upd'] = array
        (
                'table' => 'MonitoringServer',
                'action' => 'UPDATE',
                'set_arglist' => array
                (
                    array ('url_argname' => 'name',         'assertion' => 'string0'),
                    array ('url_argname' => 'base_url',     'assertion' => 'string'),
                    array ('url_argname' => 'css',          'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'javascript',   'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'regularexp',   'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'username',     'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                    array ('url_argname' => 'password',     'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
                ),
                'where_arglist' => array
                (
                        array ('url_argname' => 'id', 'assertion' => 'uint'),
                ),
        );
}

function plugin_monitoring_install ()
{
        if (extension_loaded ('curl') === FALSE)
                throw new RackTablesError ('cURL PHP module is not installed', RackTablesError::MISCONFIGURED);

        global $dbxlink;

	$dbxlink->query(
	"DROP TABLE IF EXISTS `MonitoringServer`;");

        $dbxlink->query(
	"CREATE TABLE `MonitoringServer` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `name` char(255),
        `base_url` char(255),
        `css` char(255),
        `javascript` char(255),
        `regularexp` char(255),
        `username` char(255),
        `password` char(255),
        PRIMARY KEY (`id`)
	) ENGINE=InnoDB;");

	$dbxlink->query(
	"DROP TABLE IF EXISTS `MonitoringBackend`;");

	$dbxlink->query(
	"CREATE TABLE `MonitoringBackend` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`name` char(255),
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB; ");

	$dbxlink->query(
	"INSERT INTO `MonitoringBackend` 
	( `id`, `name` ) VALUES 
	( 1, 'Nagios'),
	( 2, 'Icinga 1'),
	( 3, 'Icinga 3'),
	( 4, 'Naemon'),
	( 5, 'Shinken'),
	( 6, 'Thruk'),
	( 7, 'Prometeus');");

	return TRUE;
}

function plugin_monitoring_uninstall ()
{
        global $dbxlink;
        $dbxlink->query ("DROP TABLE `MonitoringServer`");
	$dbxlink->query ("DROP TABLE `MonitoringBackend`");
        return TRUE;
}

function plugin_monitoring_upgrade ()
{
        return TRUE;
}

function renderMonitoringConfig()
{
	$types=getMonitoringBackends();

        $columns = array
        (
                array ('th_text' => 'Name',                'row_key' => 'name', 'td_maxlen' => 30),
# id, name
		array ('th_text' => 'Type', 'row_key' => 'name', printNiftySelect('other', array ('name' => '0', 'name' => '1' ), 'NULL') ),
                array ('th_text' => 'URL to status.cgi',   'row_key' => 'base_url', 'td_escape' => FALSE, 'td_maxlen' => 150),
                array ('th_text' => 'CSS URL',             'row_key' => 'css', 'td_maxlen' => 150),
                array ('th_text' => 'Javascript URL',      'row_key' => 'javascript', 'td_maxlen' => 150),
                array ('th_text' => 'Regular expression',  'row_key' => 'regularexp', 'td_maxlen' => 20),
                array ('th_text' => 'Monitoring Username', 'row_key' => 'username', 'td_maxlen' => 20),
        //        array ('th_text' => 'Monitoring Password', 'row_key' => 'password'),
        );
        $servers = getMonitoringServers();
        startPortlet ('Monitoring servers (' . count ($servers) . ')');
        renderTableViewer ($columns, $servers);
        finishPortlet();
}

function get_name_size()      { return 20; }
function get_base_url_size()  { return 40; }
function get_css_size()       { return 40; }
function get_javascript_size(){ return 40; }
function get_regularexp_size(){ return 20; }
function get_username_size()  { return 20; }
function get_password_size()  { return 20; }

function renderMonitoringServersEditor()
{
        function printNewItemTR()
        {
                printOpFormIntro ('add');
                echo '<tr>';
                echo '<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>';
                echo '<td><input type=text size='.get_name_size().' name=name></td>' .
                     '<td><input type=text size='.get_base_url_size().' name=base_url></td>' .
                     '<td><input type=text size='.get_css_size().' name=css></td>' .
                     '<td><input type=text size='.get_javascript_size().' name=javascript></td>' .
                     '<td><input type=text size='.get_regularexp_size().' name=regularexp></td>' .
                     '<td><input type=text size='.get_username_size().' name=username></td>' .
                     '<td><input type=password size='.get_password_size().' name=password></td>';
                echo '<td>&nbsp;</td>';
                echo '<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>';
                echo '</tr></form>';
        }
        echo '<div class=portlet>Required values are the Name of your server and the full URL to the status.cgi.<br />';
        echo 'Please note that any given username/password combination are stored in clear text.<br />';
        echo 'If you do not provide a standard username/password here, the users will be asked ';
        echo 'for their personal credentials once they click on the Monitoring Tab for the first';
        echo 'time. This is the recommended setting.</div>';
        echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
        echo '<tr>' .
                '<th>&nbsp;</th>' .
                '<th>Name</th>' .
                '<th>URL to status.cgi</th>' .
                '<th>CSS URL</th>' .
                '<th>Javascript URL</th>' .
                '<th>Regular expression</th>' .
                '<th>Default username</th>' .
                '<th>Default password</th>' .
                '<th>&nbsp;</th>' .
                '</tr>';
        if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
                //printNewItemTR($name_size,$base_url_size,$css_size,$javascript_size,$regularexp_size,$username_size,$password_size);
                printNewItemTR();
        foreach (getMonitoringServers() as $server)
        {
                printOpFormIntro ('upd', array ('id' => $server['id']));
                echo '<tr><td>';
                echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
                echo '</td>';
                echo '<td><input type=text size='.get_name_size().' name=name value="'         . htmlspecialchars ($server['name'],       ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size='.get_base_url_size().' name=base_url value="'     . htmlspecialchars ($server['base_url'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size='.get_css_size().' name=css value="'          . htmlspecialchars ($server['css'],        ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size='.get_javascript_size().' name=javascript value="'   . htmlspecialchars ($server['javascript'], ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size='.get_regularexp_size().' name=regularexp value="'   . htmlspecialchars ($server['regularexp'], ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size='.get_username_size().' name=username value="'     . htmlspecialchars ($server['username'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=password size='.get_password_size().' name=password value="' . htmlspecialchars ($server['password'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
                echo '</tr></form>';
        }
        if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
                //printNewItemTR($name_size,$base_url_size,$css_size,$javascript_size,$regularexp_size,$username_size,$password_size);
                printNewItemTR();
        echo '</table>';
}

function triggerMonitoring()
{
	if (! count (getMuninServers ()))
		return '';

	return '';
}

?>
