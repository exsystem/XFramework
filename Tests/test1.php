<?php
require_once 'FrameworkDSW/Containers.php';

class A {}

if (($a=null) instanceof  A) {
echo 'YES';
}else {
    echo 'NO';
}

die();
TMap::PrepareGeneric(array('K'=>'string', 'V'=>'integer'));
$map=new TMap();
$map->Put('aa', 11);
$map->Put('bb', 22);
$map->Put('cc', 33);

class TT extends TRecord {
    public $a=1;
    public $b=2;
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
