<?php
$a = null;
$b = null;
$c = 10;
$d = '中国hi';
$e = 1;

$mysqli = new mysqli('localhost', 'root', '', 'test', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

$stmt1 =$mysqli->stmt_init();
$stmt1->prepare('select _vchar from tmysqlconnectiontest where _int = ?');
$stmt1->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_NO_CURSOR);
$stmt1->bind_param('s', $c);
$rm1 = $stmt1->result_metadata();
$rm1->fetch_fields();
$rm1->close();
$stmt1->execute();
$stmt1->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE);
$stmt1->store_result();
$stmt1->bind_result($a);

$stmt2 = $mysqli->stmt_init();

$stmt3 = $mysqli->stmt_init();

$stmt1->reset();
$stmt1->execute();
$stmt1->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE);
$stmt1->store_result();
$stmt1->num_rows;
$stmt1->data_seek(0);
$stmt1->fetch();

$stmt2->prepare('select id from tmysqlconnectiontest where _vchar = ? limit ?');
$stmt2->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_NO_CURSOR);
$stmt2->bind_param('ss', $a, $e);
$stmt2->execute();
$stmt2->bind_result($b);
$rm2 = $stmt2->result_metadata();
$rm2->fetch_fields();
$stmt2->fetch();
$rm2->close();
var_dump($stmt2);
$stmt3->prepare('select id, _vchar from tmysqlconnectiontest where _vchar = ? limit ?');

echo 'DONE!!';
