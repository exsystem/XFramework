<?php

/**
 * 
 * @param	string	$info
 */
function logging($info) {
    echo "<span style=\"font: 12px Monaco,'Courier New';\">";
    echo $info, "</span><br/>\n";
}

/**
 * 
 * @param string $name
 */
function logStart($name) {
    echo "<p style=\"font: bold 12px Monaco,'Courier New'; color: green;\">\n";
    echo "## TESETING: ", $name, "\n";
    echo "</p>\n";
}