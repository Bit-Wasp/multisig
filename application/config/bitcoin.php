<?php

$config['ssl'] = false;
$config['host'] = 'localhost';
$config['port'] = '18332';

//$config['ssl'] = false;
//$config['user'] = 'test';
//$config['password'] = '123';
//$config['host'] = 'localhost';
//$config['port'] = '19001';


// Leave these lines intact.
$config['ssl'] = ($config['ssl'] == TRUE) ? 'https://' : 'http://';
$config['url'] = $config['ssl'].$config['user'].':'.$config['password'].'@'.$config['host'].':'.$config['port'].'/';
