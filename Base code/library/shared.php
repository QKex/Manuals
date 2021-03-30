<?php
/**
 * System utilities
 *
 * @author Serhii Shkrabak
 * @package Library\Shared
 */
namespace Library;
trait Shared
{

	public function generateToken ( $size ):string {

		$alphabetUpperCase = 'MNBVCXZLKJHGFDSAPOIUYTREWQ';
		$alphabetLowerCase = 'qwertyuiopasdfghjklzxcvbnm';
		$num = '1234567890';
		$char = '~!@#$%^&*()_+-=/|.,:{}[]';

		$chars = "$alphabetUpperCase$alphabetLowerCase$num";

		$result = '';

		$length = strlen( $chars ) - 1;

		while ( strlen( $result ) < $size )
			$result .= $chars[ rand( 0, $length ) ];

		return $result;

	}

	protected function getVar(String $name, String $type = 'p'):?string {
		$source = null;
		$var = null;
		switch ($type) {
			case 'p':
				$source = &$_POST;
			break;
			case 'g':
				$source = &$_GET;
			case 'r':
				$source = &$_REQUEST;
			break;
			case 'c':
				$source = &$_COOKIE;
			break;
			case 'e':
				$source = &$_SERVER;
			break;
			case 'f':
				$source = &$_FILES;
			break;
			case 'i':
				$var = file_get_contents('php://input');
			break;
			case 'pc':
				if (isset($_POST[$name]))
					$var = $_POST[$name];
				else
					if (isset($_COOKIE[$name]))
						$var = $_COOKIE[$name];
			break;
			case 'i':
				$var = 'VAR_INTERNAL';
			break;
			default:
				throw new \Exception('INTERNAL_ERROR',6);
		}
		if ($var === null && isset($source[$name]))
			$var = $source[$name];
		return $var;
	}

	private static function getDB(Bool $include = true):?\Library\MySQL {
		$result = null;
		if (isset($GLOBALS['DB'])) {
			$result = $GLOBALS['DB'];
		}
		return  $result;
	}
	private function setDB(\Library\MySQL $ORM):void {
		$GLOBALS['DB'] = $ORM;
	}

}