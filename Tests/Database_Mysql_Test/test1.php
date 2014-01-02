<?php

require_once 'FrameworkDSW/Framework.php';

use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\Database\TConcurrencyType;
use FrameworkDSW\Database\TResultSetType;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\TInteger;


$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
$mConfig                   = new TMap();
$mConfig['Username']       = 'root';
$mConfig['Password']       = '';
$mConfig['ConnectTimeout'] = '2';
//$mConfig['Socket'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

$mDropDDL     = <<<'EOD'
DROP TABLE IF EXISTS `tmysqlconnectiontest`
EOD;
$mCreateDDL   = <<<'EOD'
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
    $stmt1        = $mConn->PrepareStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
    $mLimitParam  = new TInteger(3);
    $mOffsetParam = new TInteger(3);
    $stmt1->BindParam(':limit', $mLimitParam);
    $stmt1->BindParam(':offset', $mOffsetParam);
    $rs1 = $stmt1->Query('select * from tmysqlconnectiontest limit :limit,:offset');
    $rs1[1]['id'];
    foreach ($rs1 as $r) {
        echo $r['id']->Unbox(), "\r\n";
    }
}
catch (EException $Ex) {
    var_dump($Ex);
} finally {
    Framework::Free($mLimitParam);
    Framework::Free($mOffsetParam);
    Framework::Free($rs1);
    Framework::Free($stmt1);
}
Framework::Free($mConn);
Framework::Free($mConfig);