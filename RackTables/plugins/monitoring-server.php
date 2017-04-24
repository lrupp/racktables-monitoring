<?php
$page['monitoring']['title'] = 'Monitoring';
$page['monitoring']['parent'] = 'config';
$tab['monitoring']['default'] = 'View';
$tab['monitoring']['servers'] = 'Manage servers';
$tabhandler['monitoring']['default'] = 'renderMonitoringConfig';
$tabhandler['monitoring']['servers'] = 'renderMonitoringServersEditor';
$ophandler['monitoring']['servers']['add'] = 'tableHandler';
$ophandler['monitoring']['servers']['del'] = 'tableHandler';
$ophandler['monitoring']['servers']['upd'] = 'tableHandler';

require_once "monitoringExtensionLib.php";

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

function renderMonitoringConfig()
{
        $columns = array
        (
                array ('th_text' => 'Name',                'row_key' => 'name'),
                array ('th_text' => 'URL to status.cgi',   'row_key' => 'base_url'),
                array ('th_text' => 'CSS URL',             'row_key' => 'css'),
                array ('th_text' => 'Javascript URL',      'row_key' => 'javascript'),
                array ('th_text' => 'Regular expression',  'row_key' => 'regularexp'),
                array ('th_text' => 'Monitoring Username', 'row_key' => 'username'),
#                array ('th_text' => 'Monitoring Password', 'row_key' => 'password'),
        );
        $servers = getMonitoringServers();
        startPortlet ('Monitoring servers (' . count ($servers) . ')');
        renderTableViewer ($columns, $servers);
        finishPortlet();
}

function renderMonitoringServersEditor()
{
        function printNewItemTR()
        {
                printOpFormIntro ('add');
                echo '<tr>';
                echo '<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>';
                echo '<td><input type=text size=24 name=name></td>' .
                     '<td><input type=text size=48 name=base_url></td>' .
                     '<td><input type=text size=48 name=css></td>' .
                     '<td><input type=text size=48 name=javascript></td>' .
                     '<td><input type=text size=24 name=regularexp></td>' .
                     '<td><input type=text size=24 name=username></td>' .
                     '<td><input type=password size=24 name=password></td>';
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
                printNewItemTR();
        foreach (getMonitoringServers() as $server)
        {
                printOpFormIntro ('upd', array ('id' => $server['id']));
                echo '<tr><td>';
                echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
                echo '</td>';
                echo '<td><input type=text size=24 name=name value="'         . htmlspecialchars ($server['name'],       ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size=48 name=base_url value="'     . htmlspecialchars ($server['base_url'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size=48 name=css value="'          . htmlspecialchars ($server['css'],        ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size=48 name=javascript value="'   . htmlspecialchars ($server['javascript'], ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size=24 name=regularexp value="'   . htmlspecialchars ($server['regularexp'], ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=text size=24 name=username value="'     . htmlspecialchars ($server['username'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td><input type=password size=24 name=password value="' . htmlspecialchars ($server['password'],   ENT_QUOTES, 'UTF-8') . '"></td>';
                echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
                echo '</tr></form>';
        }
        if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
                printNewItemTR();
        echo '</table>';
}

?>
