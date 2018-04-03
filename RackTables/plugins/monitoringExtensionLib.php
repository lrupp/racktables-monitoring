<?php
// Monitoring Plugin for RackTables
// Libary file

function getMonitoringServers()
{
        $result = usePreparedSelectBlade
        (
                'SELECT `id`, `name`, `base_url`, `css`, `javascript`, `regularexp`, `username`, `password`' .
                'FROM MonitoringServer AS MS GROUP BY id'
        );
        return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

function getMonitoringBackends()
{
	$result = usePreparedSelectBlade
	(
		'SELECT `id`, `name` FROM MonitoringBackend AS MS GROUP BY id'
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}

?>
