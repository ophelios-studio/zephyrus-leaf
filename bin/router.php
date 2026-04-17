<?php

require __DIR__ . '/../vendor/autoload.php';

if ((new \Leaf\DevRouter(__DIR__ . '/..'))->handle() === false) {
    return false;
}
