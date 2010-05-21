<?php
require_once '../FrameworkDSW/System.php';

final class TDay extends TEnum {
    const AM = null, PM = null;
}

$aDay = TDay::AM();
$bDay = TDay::PM();

if ($aDay instanceof TDay) {
    echo 'correct', "\n";
}
$bDay = TDay::AM();
if ($aDay == $bDay) {
    echo 'same day section.';
}
echo '================================'."\n";
final class THappyColor extends TEnum {
    const clRed = 1, clGreen = 1, clBlue = 1;
}
$mColor1 = THappyColor::clRed();
$mColor2 = THappyColor::clGreen();
if ($mColor1 instanceof THappyColor) {
    echo 'I am a value in TColor.';
}
if ($mColor1 != $mColor2) {
    echo 'It is not the same.';
}
if ($mColor1 == THappyColor::clRed()) {
    echo 'I am red.';
}
echo "The code of red is {$mColor1->Value()}.";