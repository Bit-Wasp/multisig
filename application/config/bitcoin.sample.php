<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['ssl'] = TRUE;		// TRUE or FALSE
$config['user'] = '';
$config['password'] = '';
$config['host'] = '';
$config['port'] = '';


// Leave these lines intact.
$config['ssl'] = ($config['ssl'] == TRUE) ? 'https://' : 'http://';
$config['url'] = $config['ssl'].$config['user'].':'.$config['password'].'@'.$config['host'].':'.$config['port'].'/';
