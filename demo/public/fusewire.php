<?php declare(strict_types = 1);

namespace FuseWire\Demo;

use FuseWire\Config;
use FuseWire\Reactor;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * This is the entry point for our Demo server.
 * You can call the Reactor from anywhere in your application, and FuseWire will take care of the rest.
 */

ini_set('display_errors','Off');
error_reporting(E_ALL);

include(dirname(__FILE__) . '/../../vendor/autoload.php');

$reactor = new Reactor(
    new Config(null, true),
    HttpRequest::createFromGlobals()
);
$reactor->run();
