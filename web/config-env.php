<?php

// Database connection configuration
$core_config['db']['type'] = getenv('DBTYPE') ?: 'mysql';		// database engine
$core_config['db']['host'] = getenv('DBHOST') ?: 'localhost';		// database host/server
$core_config['db']['port'] = getenv('DBPORT') ?: '3306';		// database port
$core_config['db']['user'] = getenv('DBUSER') ?: 'playsms';		// database username
$core_config['db']['pass'] = getenv('DBPASS') ?: 'playsmspassword';	// database password
$core_config['db']['name'] = getenv('DBNAME') ?: 'playsms';		// database name

// SMTP configuration
$core_config['smtp']['relm'] = getenv('SMTP_RELM') ?: ''; // yes, not realm, it's relm
$core_config['smtp']['user'] = getenv('SMTP_USER') ?: '';
$core_config['smtp']['pass'] = getenv('SMTP_PASS') ?: '';
$core_config['smtp']['host'] = getenv('SMTP_HOST') ?: 'localhost';
$core_config['smtp']['port'] = getenv('SMTP_PORT') ?: '25';


// Do not change anything below this line unless you know what to do
// -----------------------------------------------------------------


// you can turn on or off PHP error reporting
// on production level you should turn off PHP error reporting (set to 0), by default it's on
//error_reporting(0);
//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
error_reporting(getenv('PHP_ERROR_REPORTING') ?: E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED);

// logs directories
$core_config['apps_path']['logs'] = getenv('PATHLOG') ?: '/var/log/playsms';

// log level: 0=disabled, 1=info, 2=warning, 3=debug, 4=verbose
// WARNING: log level 3 and 4 will also save sensitive information such as password for used gateway
$core_config['logstate'] = getenv('PLAYSMS_LOG_LEVEL') ? (int)getenv('PLAYSMS_LOG_LEVEL') : 2;

// log file
$core_config['logfile'] = getenv('PLAYSMS_LOG_FILE') ?: 'playsms.log';

// WARNING: will log almost anything but passwords
$core_config['logaudit'] = getenv('PLAYSMS_LOG_AUDIT') ? in_array(getenv('PLAYSMS_LOG_AUDIT'), array('true', '1', 'yes')) : true;

// log audit file
$core_config['logauditfile'] = getenv('LOG_AUDIT_FILE') ?: 'audit.log';

// are we using http or https ? the default is using https instead http
$core_config['ishttps'] = getenv('IS_HTTPS') ? in_array(getenv('IS_HTTPS'), array('true', '1', 'yes')) : true;

// are we using dlrd or not. the default is using dlrd
$core_config['isdlrd'] = getenv('IS_DLRD') ? in_array(getenv('IS_DLRD'), array('true', '1', 'yes')) : true;

// limit the number of DLR processed by dlrd in one time
$core_config['dlrd_limit'] = getenv('DLRD_LIMIT') ? (int)getenv('DLRD_LIMIT') : 1000;

// are we using recvsmsd or not. the default is using recvsmsd
$core_config['isrecvsmsd'] = getenv('IS_RECVSMSD') ? in_array(getenv('IS_RECVSMSD'), array('true', '1', 'yes')) : true;

// are we using recvsmsd queue or not. the default is using recvsmsd queue
$core_config['isrecvsmsd_queue'] = getenv('IS_RECVSMSD_QUEUE') ? in_array(getenv('IS_RECVSMSD_QUEUE'), array('true', '1', 'yes')) : true;

// are we using sendsmsd or not. the default is using sendsmsd
$core_config['issendsmsd'] = getenv('IS_SENDSMSD') ? in_array(getenv('IS_SENDSMSD'), array('true', '1', 'yes')) : true;

// webservices require username
$core_config['webservices_username'] = getenv('WEBSERVICES_USERNAME') ? in_array(getenv('WEBSERVICES_USERNAME'), array('true', '1', 'yes')) : true;

// use alternate $_SERVER['REMOTE_ADDR']
// keep this empty unless you know what you are doing
$core_config['remote_addr'] = getenv('REMOTE_ADDR') ?: '';

