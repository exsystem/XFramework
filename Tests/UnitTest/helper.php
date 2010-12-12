<?php

/**
 * 
 * @param	string	$info
 */
function logging($info) {
    echo <<<HEREDOC
<span style="font: 12px Monaco, 'Courier New';">
{$info}
</span><br/>
HEREDOC;
}

/**
 * 
 * @param string $name
 */
function logStart($name) {
    echo <<<HEREDOC
## TESETING: {$name}
<p style="font: bold 12px Monaco,'Courier New'; color: green;"></p>
HEREDOC;
}

?>