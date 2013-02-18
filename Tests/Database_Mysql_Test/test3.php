<?php
$Command = 'select * from t where a=:abc and 1=":abc" and concat(":xx",1+:bcd,"")';

const CPattern = <<<'EOD'
/('.*?(?<!\\)')|(".*?(?<!\\)")|(:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/
EOD;
$mChunks = preg_split(CPattern, $Command, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
var_dump($mChunks);