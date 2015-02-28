<?php

use Vhrb\Git\Command;
use Vhrb\Git\Git;

include_once __DIR__ . '/../vendor/autoload.php';

$c = new Command('git --version');
$c->run();
//var_dump($c->isValid());
//var_dump($c->getOut());
//var_dump($c->getError());

$c = new Command('gitt --version');
$c->run();
//var_dump($c->isValid());
//var_dump($c->getOut());
//var_dump($c->getError());


$c = (new Git())->runCommand(['status']);
//var_dump($c->isValid());
//var_dump($c->getOut());
//var_dump($c->getError());

$r = (new Git())->createRepository(__DIR__ . '/../tmp/status', TRUE);
var_dump($r->addRemote('test', 'git@github.com:vhrb/git.git')); // git@github.com:vhrb/git.git
var_dump($r->remoteList());
//var_dump($r->fetch('test'));
//var_dump($r->checkout('master'));