<?php
$hostname = 'viaduct.proxy.rlwy.net';
$database = 'railway';
$username = 'root';
$password = '4e3gbafcHFbgeF2gDC1bcB5hCE6EGGeA';
$port = 24441;

$mysqli = new mysqli($hostname, $username, $password, $database, $port);
if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ")" . $mysqli->connect_error;
}
