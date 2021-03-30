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

	public function tgwebhook(array $data):?array {
		if ($data['token'] == $this->getVar('TGToken', 'e')) {
			$input = $data['input'];
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

	public function formsubmitAmbassador(array $data):?array {
		$result = null;
		$chat = 891022220;
		$this->TG->alert("Нова заявка в *Цифрові Амбасадори*:\n" . $data['firstname'] . ' '. $data['secondname']. ', '. $data['position'] . "\n*Зв'язок*: " . $data['phone']);
		$result = [];
		return $result;
	}

	public function corewebhook(array $data):?array {
		$result = [];
		$token = isset($data['token']) ? $data['token'] : null;
		if (!$token)
			throw new \Exception('UIS Token incorrect', 2);
		else {
			$service = \Model\Entities\Service::search(signature: $token, limit: 1);
			if (!$service)
				throw new \Exception('UIS Access denied', 3);
			else {
				$input = json_decode($data['query'], true);
				if ($input && isset($input['push'])) {
					foreach ($input['push'] as $task) {
						$this->TG->setChat($task['user'])
								->send('🏳️ *' . $service->title . "*\n" . $task['value']);
					}
				}
			}
		}
		$data['token'] = '';
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