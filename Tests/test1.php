<?php
namespace AA\BB;
require_once 'FrameworkDSW/Framework.php';
$r=new \ReflectionClass('FrameworkDSW\Reflection\TClass');
echo 1;
$q=new \ReflectionClass('\FrameworkDSW\Reflection\TClass');
echo 2;
die();
use FrameworkDSW\Reflection\TModifiers;
use FrameworkDSW\Reflection\TField;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Containers\TList;
require_once 'FrameworkDSW/Framework.php';

TClass::PrepareGeneric(['T' => 'FrameworkDSW\System\TObject']);
$mClass = new TClass();
$mFields = $mClass->getFields();
foreach ($mFields as $mField) {
    if ($mField instanceof TField) {
        $mModifiers = $mField->getModifiers();
        if ($mModifiers->IsIn(TModifiers::ePrivate)) {
        	echo 'private ';
        }
        if ($mModifiers->IsIn(TModifiers::eProtected)) {
        	echo 'protected ';
        }
        if ($mModifiers->IsIn(TModifiers::ePublic)) {
        	echo 'public ';
        }
        if ($mModifiers->IsIn(TModifiers::eConst)) {
        	echo 'const ';
        }
        echo '$', $mField->getName(), ";\n";
        Framework::Free($mField);
    }
}
Framework::Free($mClass);
die();
require_once 'FrameworkDSW/Containers.php';

class A {
}

if (($a = null) instanceof A) {
    echo 'YES';
}
else {
    echo 'NO';
}

die();
TMap::PrepareGeneric(array('K' => 'string', 'V' => 'integer'));
$map = new TMap();
$map->Put('aa', 11);
$map->Put('bb', 22);
$map->Put('cc', 33);

class TT extends TRecord {
    public $a = 1;
    public $b = 2;
}
echo json_encode(new TT());

die();

/**
 *
 * @param TMap $M <K: integer, V: integer>
 */
function P($M) {
    $itr = $M->Iterator();
    while ($itr->valid()) {
        $curr = $itr->current();
        echo $curr;
        echo ' ';
        $M->Delete($curr);
        P($M);
        $M->Put($curr, $curr);
        $itr->next();
    }
    echo "\n";
}

/**
 *
 * @param integer $n
 */
function S($n) {
    TMap::PrepareGeneric(array('K' => 'integer', 'V' => 'integer'));
    $m = new TMap();
    for ($i = 1; $i <= $n; ++$i) {
        $m->Put($i, $i);
    }
    P($m);
}

S(3);
