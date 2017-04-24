<?php

# TODO: 
# * database table for account info per monitoring server
# * function to query account details per monitoring server
# => not really functional yet
$tabhandler['myaccount']['monitoring'] = 'renderMonitoringAccount';
$tab['myaccount']['monitoring'] = 'Monitoring account';

require_once "monitoringExtensionLib.php";

function getMonitoringServersAccount()
{
        $result = usePreparedSelectBlade
        (
                'SELECT `id`, `name`' .
                'FROM MonitoringServer AS MS GROUP BY id'
        );
        return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function renderMonitoringAccount()
{
	echo "<div class=portlet><h2>Managing monitoring accounts</h2>";
	echo "<p>You can add your user credentials for each configured Monitoring server below.";
	echo "Please keep in mind that this implies that your credentials are stored in the";
	echo "RackTables database!</p></div>";
        $columns = array
        (
                array ('th_text' => 'Name', 'row_key' => 'name'),
#                array ('th_text' => 'Monitoring Username', 'row_key' => 'username'),
#                array ('th_text' => 'Monitoring Password', 'row_key' => 'password'),
        );
        $servers = getMonitoringServersAccount();
        startPortlet ('Monitoring servers (' . count ($servers) . ')');
        renderTableViewer ($columns, $servers);
        finishPortlet();
}


?>
