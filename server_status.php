<?php
header('Content-Type: application/json');

$serverIP = 'videos-shut.gl.at.ply.gg';
$serverPort = 5291;

// Create UDP socket
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 2, 'usec' => 0]);

// Send getstatus request
$message = "\xFF\xFF\xFF\xFF" . "getstatus\n";
socket_sendto($socket, $message, strlen($message), 0, $serverIP, $serverPort);

// Receive response
$response = '';
socket_recvfrom($socket, $response, 4096, 0, $fromIP, $fromPort);
socket_close($socket);

// Parse response
$lines = explode("\n", $response);
$info = [];

foreach ($lines as $line) {
    if (preg_match('/\\([a-zA-Z0-9_]+)\\(.+?\)/', $line, $matches)) {
        $key = $matches[1];
        $value = $matches[2];
        $info[$key] = $value;
    }
}

$mapName = $info['mapname'] ?? 'unknown';

echo json_encode([
    'map' => $mapName,
    'players' => $info['clients'] ?? 0,
    'hostname' => $info['sv_hostname'] ?? 'Antias FFA'
]);
?>