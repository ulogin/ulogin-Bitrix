<?php

class Ulogin {

	/**
	 * Проверка пользовательских данных, полученных по токену
	 * @param $u_user - пользовательские данные
	 * @return bool
	 */
	public static function CheckTokenError($u_user) {
		if(!is_array($u_user)) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin: Данные о пользователе содержат неверный формат.'));

			return false;
		}
		if(isset($u_user['error'])) {
			$strpos = strpos($u_user['error'], 'host is not');
			if($strpos) {
				ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin: адрес хоста не совпадает с оригиналом'));

				return false;
			}
			switch($u_user['error']) {
				case 'token expired':
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin: время жизни токена истекло'));

					return false;
					break;
				case 'invalid token':
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin: неверный токен'));

					return false;
					break;
				default:
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin:'));

					return false;
			}
		}
		if(!isset($u_user['identity'])) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка работы uLogin: В возвращаемых данных отсутствует переменная "identity"'));

			return false;
		}

		return true;
	}

	/**
	 * Гнерация логина пользователя
	 * в случае успешного выполнения возвращает уникальный логин пользователя
	 * @param $first_name
	 * @param string $last_name
	 * @param string $nickname
	 * @param string $bdate
	 * @param array $delimiters
	 * @return string
	 */
	function ulogin_generateNickname($first_name, $last_name = "", $delimiters = array('.', '_')) {
		$delim = array_shift($delimiters);
		$first_name = ulogin::uLoginTranslitIt($first_name);
		$first_name_s = substr($first_name, 0, 1);
		$variants = array();
		if(!empty($nickname))
			$variants[] = $nickname;
		$variants[] = $first_name;
		if(!empty($last_name)) {
			$last_name = ulogin::uLoginTranslitIt($last_name);
			$variants[] = $first_name . $delim . $last_name;
			$variants[] = $last_name . $delim . $first_name;
			$variants[] = $first_name_s . $delim . $last_name;
			$variants[] = $first_name_s . $last_name;
			$variants[] = $last_name . $delim . $first_name_s;
			$variants[] = $last_name . $first_name_s;
		}
		$i = 0;
		$exist = true;
		while(true) {
			if($exist = ulogin::ulogin_userExist($variants[$i])) {
				foreach($delimiters as $del) {
					$replaced = str_replace($delim, $del, $variants[$i]);
					if($replaced !== $variants[$i]) {
						$variants[$i] = $replaced;
						if(!$exist = ulogin::ulogin_userExist($variants[$i]))
							break;
					}
				}
			}
			if($i >= count($variants) - 1 || !$exist)
				break;
			$i++;
		}

		return $variants[$i];
	}

	/**
	 * Транслит
	 */
	public static function uLoginTranslitIt($str) {
		$tr = array("А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ж" => "j", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch", "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "yi", "Ь" => "", "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya");
		if(preg_match('/[^A-Za-z0-9\_\-]/', $str)) {
			$str = strtr($str, $tr);
			$str = preg_replace('/[^A-Za-z0-9\_\-\.]/', '', $str);
		}

		return $str;
	}

	/**
	 * Проверка существует ли пользователь с заданным логином
	 */
	public function ulogin_userExist($login) {
		$loginUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("LOGIN" => $login, "ACTIVE" => "Y"));
		if($loginUsers->SelectedRowsCount() > 0) {
			return false;
		}

		return true;
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	public function uloginCheckUserId($user_id) {
		global $USER;
		$current_user = $USER->GetID();
		if(($current_user > 0) && ($user_id > 0) && ($current_user != $user_id)) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Данный аккаунт привязан к другому пользователю. Вы не можете использовать этот аккаунт'));
			die('<br/><a href="' . $_POST['backurl'] . '">Назад</a>');
		}

		return true;
	}

	/**
	 * проверка уникальности email
	 */
	public static function check($arParams) {
		if($arParams['UNIQUE_EMAIL'] == 'Y') {
			$emailUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("EMAIL" => $arParams['USER']["EMAIL"], "ACTIVE" => "Y"));
			if(intval($emailUsers->SelectedRowsCount()) > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Обменивает токен на пользовательские данные
	 * @param bool $token
	 * @return bool|mixed|string
	 */
	public static function uloginGetUserFromToken($token = false) {
		$response = false;
		if($token) {
			$data = array('cms' => 'Bitrix', 'version' => constant('SM_VERSION'));
			$request = 'http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST'] . '&data=' . base64_encode(json_encode($data));
			if(in_array('curl', get_loaded_extensions())) {
				$c = curl_init($request);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($c);
				curl_close($c);
			} elseif(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
				$response = file_get_contents($request);
		}

		return $response;
	}

	/**
	 * Регистрация на сайте и в таблице uLogin
	 * @param Array $u_user - данные о пользователе, полученные от uLogin
	 * @param int $in_db - при значении 1 необходимо переписать данные в таблице uLogin
	 * @return bool|int|Error
	 */
	public static function RegistrationUser($u_user, $in_db = 0, $arParams) {
		global $APPLICATION;
		if(!isset($u_user['email'])) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Через данную форму выполнить регистрацию невозможно. Сообщите администратору сайта о следующей ошибке:
            Необходимо указать "email" в возвращаемых полях uLogin'));
			die('<br/><a href="' . $_POST['backurl'] . '">Назад</a>');
		}
		global $USER;
		global $DB;
		$rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("EMAIL" => $u_user['email']));
		$arUser = $rsUsers->GetNext();
		if($in_db == 1) {
			$result = $DB->Query('DELETE FROM ulogin_users WHERE identity = "' . urlencode($u_user['identity']) . '"');
		}
//		// $check_m_user == true -> есть пользователь с таким email
		$check_m_user = $arUser['ID'] > 0 ? true : false;
		$current_user = $USER->GetID();
		if($check_m_user == false) {
			$u_user['first_name'] = isset($u_user['first_name']) ? $APPLICATION->ConvertCharset($u_user['first_name'], "UTF-8", SITE_CHARSET) : "";
			$u_user['last_name'] = isset($u_user['last_name']) ? $APPLICATION->ConvertCharset($u_user['last_name'], "UTF-8", SITE_CHARSET) : "";
			$u_user['nickname'] = isset($u_user['nickname']) ? $APPLICATION->ConvertCharset($u_user['nickname'], "UTF-8", SITE_CHARSET) : "";
			var_dump($u_user['bdate']);
			$u_user['bdate'] = isset($u_user['bdate']) ? $u_user['bdate'] : "";
			// регистрируем пользователя
			if(!empty($u_user['bdate'])) {//можно просто представить в другом формате стандартной функцией php
				list($d, $m, $y) = explode('.', $u_user['bdate']);
				$m = ($m < 10) ? '0'.$m : $m;
				$d = ($d < 10) ? '0'.$d : $d;
				$y = !empty($y) ? $y : '2000';
				$u_user['bdate'] = $d.'.'.$m.'.'. $y;
			}

			$longLogin = uLogin::ulogin_generateNickname($u_user['first_name'], $u_user['last_name']);
			$arResult['USER'] = array(
				'EMAIL' => $u_user['email'],
				'PERSONAL_GENDER' => $u_user['sex'] == 2 ? 'M' : 'F',
				'PERSONAL_CITY' => isset($u_user['city']) ? $u_user['city'] : '',
				'PERSONAL_BIRTHDAY' => $u_user['bdate'],
				'PHOTO' => $u_user['photo'],
				'PHOTO_BIG' => $u_user['photo_big'],
				'NETWORK' => $u_user['network']
			);
			if($arParams['SOCIAL_LINK'] == 'Y') {
				$arResult['USER']['PERSONAL_WWW'] = isset($u_user['profile']) ? $u_user['profile'] : '';
			}
			$GroupID = "5";
			$passw = RandString();
			if(is_array($arParams["GROUP_ID"]))
				$GroupID = $arParams["GROUP_ID"];
			if(!is_array($GroupID))
				$GroupID = array($GroupID);
			$arIMAGE = '';
			if(isset($u_user['photo'])) {
				$imageContent = file_get_contents($u_user['photo']);
				$ext = strtolower(substr($u_user['photo'], -3));
				if(!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'bmp')))
					$ext = 'jpg';
				$tmpName = $tmpName = md5(rand()) . '.' . $ext;
				$tmpName = $_SERVER["DOCUMENT_ROOT"] . "/images/" . $tmpName;
				file_put_contents($tmpName, $imageContent);
				$arIMAGE = CFile::MakeFileArray($tmpName);
				$arIMAGE["MODULE_ID"] = "main";
			}
			$user = new CUser;
			$arFields = array(
				"NAME" => $u_user['first_name'],
				"LAST_NAME" => $u_user['last_name'],
				"EMAIL" => $u_user['email'],
				"LOGIN" => $longLogin,
				"ACTIVE" => "Y",
				"GROUP_ID" => array(5),
				"PASSWORD" => $passw,
				"CONFIRM_PASSWORD" => $passw,
				"PERSONAL_PHOTO" => $arIMAGE,
				'PERSONAL_GENDER' => $u_user['sex'] == 2 ? 'M' : 'F',
				'PERSONAL_CITY' => isset($u_user['city']) ? $u_user['city'] : '',
				'PERSONAL_BIRTHDAY' => $u_user['bdate'],
				'PERSONAL_PHONE' => isset($u_user['phone']) ? $u_user['phone'] : '',
				'PERSONAL_COUNTRY' => isset($u_user['country']) ? $u_user['country'] : '',
			);
			if($arParams['SOCIAL_LINK'] == 'Y') {
				$arFields['PERSONAL_WWW'] = isset($u_user['profile']) ? $u_user['profile'] : '';
			}

			$UserID = $user->Add($arFields);
			if($UserID > 0) {
				$result = $DB->Query('INSERT INTO ulogin_users (id, userid, identity, network) VALUES (NULL,"' . $UserID . '","' . urlencode($u_user['identity']) . '","' . $u_user['network'] . '")');
			} else {
				ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => 'Ошибка регистрации: Не удалось зарегистрировать пользователя.'));
				die('<br/><a href="' . $_POST['backurl'] . '">Назад</a>');
			}
			if($UserID && $arParams['SEND_EMAIL'] == 'Y') {
				$arEventFields = array(
					'USER_ID' => $UserID,
					'LOGIN' => $arFields['LOGIN'],
					'EMAIL' => $arFields['EMAIL'],
					'NAME' => $arFields['NAME'],
					'LAST_NAME' => $arFields['LAST_NAME'],
					'USER_IP' => '',
					'USER_HOST' => ''
				);
				$event = new CEvent;
				$msg = $event->SendImmediate("NEW_USER", SITE_ID, $arEventFields);
				ShowMessage($msg);
			}
			unlink($tmpName);

			return $UserID;
		} else {
			if(!isset($u_user["verified_email"]) || intval($u_user["verified_email"]) != 1) {
				die('<script src="//ulogin.ru/js/ulogin.js"  type="text/javascript"></script><script type="text/javascript">uLogin.mergeAccounts("' . $_POST['token'] . '")</script>' . 'Электронный адрес данного аккаунта совпадает с электронным адресом существующего пользователя. Требуется подтверждение на владение указанным email.' . '<br/><a href="' . $_POST['backurl'] . '">Назад</a>');
			}
			if(intval($u_user["verified_email"]) == 1) {
				$user_id = $arUser['ID'];
				$other_u = $DB->Query('SELECT identity,network FROM ulogin_users WHERE userid = "' . $user_id . '"');
				$other = array();
				while($row = $other_u->Fetch()) {
					$ident = $row['identity'];
					$key = $row['network'];
					$other[$key] = $ident;
				}
				if($other) {
					if(!isset($u_user['merge_account'])) {
						die('<script src="//ulogin.ru/js/ulogin.js"  type="text/javascript"></script><script type="text/javascript">uLogin.mergeAccounts("' . $_POST['token'] . '","' . $other[$key] . '")</script>' . 'С данным аккаунтом уже связаны данные из другой социальной сети. Требуется привязка новой учётной записи социальной сети к этому аккаунту' . '<br/><a href="' . $_POST['backurl'] . '">Назад</a>');
					}
				}
			}
			$result = $DB->Query('INSERT INTO ulogin_users (id, userid, identity, network) VALUES (NULL,"' . $arUser['ID'] . '","' . urlencode($u_user['identity']) . '","' . $u_user['network'] . '")');
			$result = $result->GetNext();

			return $arUser['ID'];
		}
	}

	/**
	 * Обновление данных о пользователе и вход
	 * @param $u_user - данные о пользователе, полученные от uLogin
	 * @param $id_customer - идентификатор пользователя
	 * @return string
	 */
	public function loginUser($u_user, $id_customer) {
		global $USER;
		//авторизуем пользователя
		//дописать проверку изменения данных
		$USER->Authorize($id_customer);
	}
}

?>

