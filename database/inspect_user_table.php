<?php
require 'database/config.php';
$cfg = require 'database/config.php';
$dsn = 'mysql:host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['dbname'].';charset='.$cfg['charset'];
$pdo = new PDO($dsn, $cfg['user'], $cfg['pass']);
$stmt = $pdo->query('DESCRIBE usuario');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'].' '.$row['Type'].' '.$row['Null'].'\n';
}
