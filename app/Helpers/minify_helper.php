<?php
use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;

if(!function_exists('minify_css')){
    function minify_css($inputFilePaths, $outputFilePath)
    {
        $minifier = new CSS();

        foreach ($inputFilePaths as $inputFilePath) {
            $minifier->add($inputFilePath);
        }

        $minifier->minify($outputFilePath);
    }
}

if(!function_exists('minify_js')){
    function minify_js($inputFilePaths, $outputFilePath)
    {
        $minifier = new JS();

        foreach ($inputFilePaths as $inputFilePath) {
            $minifier->add($inputFilePath);
        }

        $minifier->minify($outputFilePath);
    }
}


