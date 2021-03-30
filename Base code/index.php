<?php
/**
 * Core bootloader
 *
 * @author Serhii Shkrabak
 */

/* RESULT STORAGE */
$RESULT = [
	'state' => 0,
	'data' => [],
	'debug' => []
];

/* ENVIRONMENT SETUP */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'); // Unity entrypoint;

register_shutdown_function('shutdown', 'OK'); // Unity shutdown function

spl_autoload_register('load'); // Class autoloader

set_exception_handler('handler'); // Handle all errors in one function

/* HANDLERS */

/*
 * Class autoloader
 */
function load (String $class):void {
	$class = strtolower(str_replace('\\', '/', $class));
	$file = "$class.php";
	if (file_exists($file))
		include $file;
}

/*
 * Debug logger
 */
function printme ( Mixed $var ):void {
	$stack = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[ 0 ];
	$GLOBALS[ 'RESULT' ][ 'debug' ][] = [
		'type' => 'Trace',
		'details' => $var,
		'file' => $stack[ 'file' ],
		'line' => $stack[ 'line' ]
	];
}

/*
 * Error logger
 */
function handler (Throwable $e):void {
	global $RESULT;
	$codes = ['SUCCESS', 'REQUEST_INCOMLETE', 'REQUEST_INCORRECT', 'ACCESS_DENIED', 'RESOURCE_LOST', 'REQUEST_UNKNOWN', 'INTERNAL_ERROR'];
	$message = $e -> getMessage();
	$code = $e -> getCode();
	$RESULT['state'] = $code ? $code : 6;
	$RESULT[ 'debug' ][] = [
		'type' => get_class($e),
		'details' => $message,
		'code' => $code,
		'file' => $e -> getFile(),
		'line' => $e -> getLine(),
		'trace' => $e -> getTrace()
	];
}

/*
 * Shutdown handler
 */
function shutdown():void {
	global $RESULT;
	$error = error_get_last();
	if ( ! $error ) {
		header("Content-Type: application/json");
		echo json_encode($GLOBALS['RESULT'], JSON_UNESCAPED_UNICODE);
	}
}

$CORE = new Controller\Main;
$data = $CORE->exec();

if ($data !== null)
	$RESULT['data'] = $data;
else { // Error happens
	throw new Exception(code: 6);
	unset($RESULT['data']);
}