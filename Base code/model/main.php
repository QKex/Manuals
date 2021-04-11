<?php
/**
 * User Controller
 *
 * @author Serhii Shkrabak
 * @global object $CORE->model
 * @package Model\Main
 */
namespace Model;
class Main
{
	use \Library\Shared;

	public function tgwebhook(String $token, String $input):?array {
		if ($token == $this->getVar('TGToken', 'e')) {

			$input = json_decode( $input, true );

			file_put_contents(ROOT . "media/log.txt", file_get_contents('php://input') . "\n\n", FILE_APPEND);

			if (isset($input['callback_query'])) {
				$this->TG->process($input['callback_query']['message'], $input['callback_query']['data']);
			}
			else
				if (isset($input['edited_message']))
					$this->TG->process($input['edited_message'], edited: true);
				else
					if (isset($input['message']))
						$this->TG->process($input['message']);
					else
						if (isset($input['my_chat_member'])) {
							$update = $input['my_chat_member'];
							$this->TG->alert("\n*" . $update['new_chat_member']['status'] . ":* " . $update['chat']['first_name'] . ' ' . $update['chat']['last_name']);
						}
						else
							$this->TG->alert($data['input']);
		} else
			throw new \Exception('TG token incorrect', 3);
		return [];
	}

	public function uniwebhook(String $type = '', String $value = '', Int $code = 0):?array {
		$result = null;
		switch ($type) {
			case 'message':
				if ($value == 'вихід') {
					$result = ['type' => 'context', 'set' => null];
				} else
				$result = [
					'to' => $GLOBALS['uni.user'],
					'type' => 'message',
					'value' => "Сервіс `Texнічні дані` отримав повідомлення $value"
				];
				break;
				case 'click':
					$result = [
						'to' => $GLOBALS['uni.user'],
						'type' => 'message',
						'value' => "Сервіс `Texнічні дані`. Натиснуто кнопку $code",
						'keyboard' => [
							'inline' => false,
							'buttons' => [
								[['id' => 9, 'title' => 'Надати номер', 'request' => 'contact']]
							]
						]
					];
					break;
				case 'contact':
					$result = [
						'to' => $GLOBALS['uni.user'],
						'type' => 'message',
						'value' => "Сервіс `Texнічні дані`. Отримано номер $value"
					];
					break;
		}

		return $result;
	}

	public function formsubmitAmbassador(String $firstname, String $secondname, String $phone, String $position = ''):?array {
		$result = null;
		$chat = 891022220;
		$this->TG->alert("Нова заявка в *Цифрові Амбасадори*:\n$firstname $secondname, $position\n*Зв'язок*: $phone");
		$result = [];
		return $result;
	}

	public function __construct() {
		$this->db = new \Library\MySQL('core',
			\Library\MySQL::connect(
				$this->getVar('DB_HOST', 'e'),
				$this->getVar('DB_USER', 'e'),
				$this->getVar('DB_PASS', 'e')
			) );
		$this->setDB($this->db);
		$this -> TG = new Services\Telegram(key: $this->getVar('TGKey', 'e'), emergency: 280751679);
	}
}