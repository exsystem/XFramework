<?php

require_once 'FrameworkDSW/Framework.php';

use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\TResultSetType;
use FrameworkDSW\Database\TConcurrencyType;
use FrameworkDSW\Database\TPrimitiveParam;
use FrameworkDSW\System\EException;


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
//     $a=null;$b=null;$c=null;$d=null;$e=null;
//     $mysqli=new mysqli('localhost', 'root', '', 'test', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
//     $stmt= $mysqli->prepare('select * from tmysqlconnectiontest limit ?,?');
//     $stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_NO_CURSOR);
//     $p='3';
//     $q='3';
//     $stmt->bind_param('ss', $p,$q);
//     $rm=$stmt->result_metadata();
//     $rm->fetch_fields();
//     $rm->close();
//     $stmt->execute();
//     $stmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE);
//     $stmt->store_result();
//     $stmt->bind_result($a, $b, $c, $d, $e);
//     $stmt->reset();
//     $stmt->execute();
//     echo 'DONE!!';
//SEE MySQL bug: #68401

    $stmt1 = $mConn->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
    TPrimitiveParam::PrepareGeneric(['T' => 'integer']);
    $mLimitParam = new TPrimitiveParam(3);
    TPrimitiveParam::PrepareGeneric(['T' => 'integer']);
    $mOffsetParam = new TPrimitiveParam(3);
    $stmt1->BindParam(':limit', $mLimitParam);
    $stmt1->BindParam(':offset', $mOffsetParam);
    $rs1 = $stmt1->Query('select * from tmysqlconnectiontest limit :limit,:offset');
    $rs1[1]['id']->getValue();
    foreach ($rs1 as $r) {
        echo $r['id']->getValue(), "\r\n";
    }

    Framework::Free($rs1);
    Framework::Free($stmt1);
}
catch (EException $Ex) {
    var_dump($Ex);
}
Framework::Free($mConn);
Framework::Free($mConfig);