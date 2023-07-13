<?php


function tokenize($input)
{
    $input = trim($input);
    $input = str_replace(',', '', $input);
    $input = str_replace(' ', '', $input);
    $input = strtolower($input);
    return $input;
}

$naksatraNames = file_get_contents('../data/naksatraNames.json');
$naksatraNames = json_decode($naksatraNames);

function isSameNaksatras($naksatra1, $naksatra2, $naksatraNames)
{
    if (tokenize($naksatra1) == tokenize($naksatra2)) {
        return true;
    }

    preg_match('/([a-zA-z ]*),?.*(\d)/', $naksatra1, $matches);
    $naksatra1 = trim($matches[1]) ?? $naksatra1;
    $pada1 = $matches[2] ?? null;
    preg_match('/([a-zA-z ]*),?.*(\d)/', $naksatra2, $matches);
    $naksatra2 = trim($matches[1]) ?? $naksatra2;
    $pada2 = $matches[2] ?? null;

    if (
        isset($naksatraNames->{$naksatra1})
        && $naksatraNames->{$naksatra1} == $naksatra2
        && $pada1 == $pada2
    ) {
        return true;
    }

    if (
        isset($naksatraNames->{$naksatra2})
        && $naksatraNames->{$naksatra2} == $naksatra1
        && $pada1 == $pada2
    ) {
        return true;
    }

    return false;
}

var_dump(isSameNaksatras('Rohini, 1', 'Rohini 1', $naksatraNames));
var_dump(isSameNaksatras('Purva Phalguni, 1', 'Pooram, 1', $naksatraNames));
var_dump(isSameNaksatras('Moola, 2', 'Mulam 2', $naksatraNames));
