<?php

main($argc, $argv);

function main($argc, $argv = array())
{
    if ($argc < 2) {
        die("Wrong parameters");
    }

    require_once __DIR__ . "/../app/bootstrap.php";
    \Ubikz\Feed\Parser::set(require(__DIR__ . '/../app/feeds.php'), $argv[1]);
}
