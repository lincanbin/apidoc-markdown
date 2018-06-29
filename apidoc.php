<?php
require_once __DIR__ . '/ApiDocGenerator.php';

$shortOptions = "";
$shortOptions .= "i:";
$shortOptions .= "o:";
$shortOptions .= "t:";
$shortOptions .= "h::";

$longOptions = array(
    "input:",
    "output:",
    "template:",
    "help::",
);
$options = getopt($shortOptions, $longOptions);

$getPathOption = function ($shortOption, $longOption, $default = '') use ($options) {
    $result = isset($options[$shortOption])
        ? $options[$shortOption]
        : (isset($options[$longOption]) ? $options[$longOption] : null);
    $result = is_null($result) ? $default : $result;
    $result = is_dir($result) ? (realpath($result) ?: $result) : getcwd() . DIRECTORY_SEPARATOR . $result;
    if (substr($result, -1, 1) !== DIRECTORY_SEPARATOR) {
        $result .= DIRECTORY_SEPARATOR;
    }
    return $result;
};
$optionInput = $getPathOption('i', 'input');
$optionOutput = $getPathOption('o', 'output', 'doc');
$optionTemplate = $getPathOption('t', 'template', __DIR__ . DIRECTORY_SEPARATOR . 'template');
if (isset($options['h']) || isset($options['help'])) {
    $phpPath = PHP_BINARY;
    echo <<<help

Usage: $phpPath apidoc [options]

Options:
   -i, --input             Input / source dirname. [$optionInput]
   -o, --output            Output dirname.  [$optionOutput]
   -t, --template          Use template for output files.  [$optionTemplate]
   

help;
    exit(0);
}
//var_dump($options);
//var_dump($argv);
//var_dump(getopt("h::"));
echo "Input: " . $optionInput . "\n";
echo "Output: " . $optionOutput . "\n\n";
$docGenerator = new ApiDocGenerator($optionInput, $optionOutput, $optionTemplate);
echo "\n\nSuccessfully generated! \n";