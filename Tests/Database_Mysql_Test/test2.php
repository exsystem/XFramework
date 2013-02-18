<?php
//set_include_path(get_include_path() . ':/media/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');
//set_include_path(get_include_path().';E:\\Documents\\ZendStudioWorkspace\\FrameworkDSW');
//set_include_path(get_include_path() . ':/Volumes/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');
set_include_path(get_include_path() . ':/Users/exsystem/Documents/ZendStudioWorkspace/FrameworkDSW');
require_once 'FrameworkDSW/Database_Mysql.php';

$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
$mConfig = new TMap();
$mConfig['Username'] = 'root';
$mConfig['Password'] = '';
$mConfig['ConnectTimeout'] = '2';
$mConfig['Socket'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

$mDropDDL = <<<'EOD'
DROP TABLE IF EXISTS `tmysqlconnectiontest`
EOD;
$mCreateDDL = <<<'EOD'
CREATE TABLE IF NOT EXISTS `tmysqlconnectiontest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `_int` int(11) NOT NULL,
  `_bool` tinyint(1) NOT NULL,
  `_vchar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `_float` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
EOD;
$mTruncateDDL = <<<'EOD'
TRUNCATE TABLE `tmysqlconnectiontest`
EOD;
$mConn->Execute($mDropDDL);
$mConn->Execute($mCreateDDL);
$mConn->Execute($mTruncateDDL);

for ($i = 1; $i < 50; ++$i) {
    echo $mConn->Execute("insert into `tmysqlconnectiontest` values({$i}, 10, 1, '中国hi', 20.5)");
}
echo "\r\n";

try {
    TPrimitiveParam::PrepareGeneric(['T' => 'integer']);
    $p1 = new TPrimitiveParam(10);
    TPrimitiveParam::PrepareGeneric(['T' => 'integer']);
    $p3 = new TPrimitiveParam(1);
    $stmt1 = $mConn->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
    $stmt1->BindParam(':p1', $p1);
    $rs1 = $stmt1->Query('select _vchar from tmysqlconnectiontest where _int = :p1');
    $stmt2 = $mConn->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
    $stmt3 = $mConn->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
    foreach ($rs1 as $r) {
        echo $r['_vchar']->getValue();
        $stmt2->ClearParams();
        $stmt2->BindParam(':p2', $r['_vchar']);
        $stmt2->BindParam(':p3', $p3);
        $stmt2->setCommand('select id from tmysqlconnectiontest where _vchar = :p2 limit :p3');
        $s = $stmt2->FetchAsScalar();
        echo $s->getValue(), "\r\n";
        $stmt3->ClearParams();
        $stmt3->BindParam(':p2', $r['_vchar']);
        $stmt3->BindParam(':p3', $p3);
        $rs3 = $stmt3->Query('select id, _vchar from tmysqlconnectiontest where _vchar = :p2 limit :p3');
        echo $rs3[0]['_vchar']->getValue(), $rs3[0]['id']->getValue(), "<<<\r\n";
        Framework::Free($rs3);
    }
    Framework::Free($rs1);
    Framework::Free($stmt3);
    Framework::Free($stmt2);
    Framework::Free($stmt1);
}
catch (EException $Ex) {
    Framework::Free($rs1);
    Framework::Free($stmt3);
    Framework::Free($stmt2);
    Framework::Free($stmt1);
    var_dump($Ex);
}
Framework::Free($mConn);

//TODO: for those mysqli_* things: try and catch the warning saying 'couldnt fetch mysqli_*' since those objects are never used.
