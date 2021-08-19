<?php

class ULoginSync {

	/**
	 * �������� ���������������� ������, ���������� �� ������
	 * @param $u_user - ���������������� ������
	 * @return bool
	 */
	public static function CheckTokenError($u_user) {
		if(!is_array($u_user)) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_DATA')));

			return false;
		}
		if(isset($u_user['error'])) {
			$strpos = strpos($u_user['error'], 'host is not');
			if($strpos) {
				ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_HOST')));

				return false;
			}
			switch($u_user['error']) {
				case 'token expired':
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_TIMEOUT')));

					return false;
					break;
				case 'invalid token':
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_TOKEN')));

					return false;
					break;
				default:
					ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR')));

					return false;
			}
		}
		if(!isset($u_user['identity'])) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_IDENTITY')));

			return false;
		}

		return true;
	}

	/**
	 * �������� ������ ������������
	 * � ������ ��������� ���������� ���������� ���������� ����� ������������
	 * @param $first_name
	 * @param string $last_name
	 * @param string $nickname
	 * @param string $bdate
	 * @param array $delimiters
	 * @return string
	 */
	public static function generateNickname($profile) {
		$first_name = $profile['first_name'];
		$last_name = isset($profile['last_name']) ? $profile['last_name'] : '';
		$nickname = isset($profile['nickname']) ? $profile['nickname'] : '';
		$bdate = isset($profile['bdate']) ? $profile['bdate'] : '';
		$delimiters = array('.', '_');
		$delim = array_shift($delimiters);
		$first_name = uLogin::uLoginTranslitIt($first_name);
		$first_name_s = substr($first_name, 0, 1);
		$variants = array();
		if(!empty($nickname))
			$variants[] = $nickname;
		$variants[] = $first_name;
		if(!empty($last_name)) {
			$last_name = uLogin::uLoginTranslitIt($last_name);
			$variants[] = $first_name . $delim . $last_name;
			$variants[] = $last_name . $delim . $first_name;
			$variants[] = $first_name_s . $delim . $last_name;
			$variants[] = $first_name_s . $last_name;
			$variants[] = $last_name . $delim . $first_name_s;
			$variants[] = $last_name . $first_name_s;
		}
		if(!empty($bdate)) {
			$date = explode('.', $bdate);
			$variants[] = $first_name . $date[2];
			$variants[] = $first_name . $delim . $date[2];
			$variants[] = $first_name . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $last_name . $date[2];
			$variants[] = $first_name . $delim . $last_name . $delim . $date[2];
			$variants[] = $first_name . $delim . $last_name . $date[0] . $date[1];
			$variants[] = $first_name . $delim . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name . $date[2];
			$variants[] = $last_name . $delim . $first_name . $delim . $date[2];
			$variants[] = $last_name . $delim . $first_name . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name . $delim . $date[0] . $date[1];
			$variants[] = $first_name_s . $delim . $last_name . $date[2];
			$variants[] = $first_name_s . $delim . $last_name . $delim . $date[2];
			$variants[] = $first_name_s . $delim . $last_name . $date[0] . $date[1];
			$variants[] = $first_name_s . $delim . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name_s . $date[2];
			$variants[] = $last_name . $delim . $first_name_s . $delim . $date[2];
			$variants[] = $last_name . $delim . $first_name_s . $date[0] . $date[1];
			$variants[] = $last_name . $delim . $first_name_s . $delim . $date[0] . $date[1];
			$variants[] = $first_name_s . $last_name . $date[2];
			$variants[] = $first_name_s . $last_name . $delim . $date[2];
			$variants[] = $first_name_s . $last_name . $date[0] . $date[1];
			$variants[] = $first_name_s . $last_name . $delim . $date[0] . $date[1];
			$variants[] = $last_name . $first_name_s . $date[2];
			$variants[] = $last_name . $first_name_s . $delim . $date[2];
			$variants[] = $last_name . $first_name_s . $date[0] . $date[1];
			$variants[] = $last_name . $first_name_s . $delim . $date[0] . $date[1];
		}
		$i = 0;
		$exist = true;
		while(true) {
			if($exist = uLogin::uLogin_userExist($variants[$i]) && strlen($variants[$i]) < 4) {
				foreach($delimiters as $del) {
					$replaced = str_replace($delim, $del, $variants[$i]);
					if($replaced !== $variants[$i]) {
						$variants[$i] = $replaced;
						if(!$exist = uLogin::uLogin_userExist($variants[$i]))
							break;
					}
				}
			}
			if($i >= count($variants) - 1 || !$exist)
				break;
			$i++;
		}
		if($exist) {
			while($exist) {
				$nickname = $first_name . mt_rand(1, 100000);
				$exist = uLogin::uLogin_userExist($nickname);
			}

			return iconv('utf-8', 'windows-1251', $nickname);
		} else {
			return iconv('utf-8', 'windows-1251', $variants[$i]);
		}
	}

	/**
	 * ��������
	 */
	public static function uLoginTranslitIt($str) {
		$tr = array("�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "j", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "u", "�" => "f", "�" => "h", "�" => "ts", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "", "�" => "yi", "�" => "", "�" => "e", "�" => "yu", "�" => "ya", "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "j", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "u", "�" => "f", "�" => "h", "�" => "ts", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "y", "�" => "y", "�" => "", "�" => "e", "�" => "yu", "�" => "ya");
		if(preg_match('/[^A-Za-z0-9\_\-]/', $str)) {
			$str = strtr($str, $tr);
			$str = preg_replace('/[^A-Za-z0-9\_\-\.]/', '', $str);
		}

		return $str;
	}

	/**
	 * �������� ���������� �� ������������ � �������� �������
	 */
	public function ulogin_userExist($login) {
		$loginUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("LOGIN" => $login, "ACTIVE" => "Y")); //$login
		if($loginUsers->SelectedRowsCount() > 0) {
		    return true;
		}

        return false;
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	public function uloginCheckUserId($user_id) {
		global $USER;
		$current_user = $USER->GetID();
		if(($current_user > 0) && ($user_id > 0) && ($current_user != $user_id)) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_SYNC')));
			die('<a href="' . $_POST['backurl'] . '">' . GetMessage('ULOGIN_BACK') . '</a>');
		}

		return true;
	}

	/**
	 * �������� ������������ email
	 */
	public static function check($arParams) {
		if($arParams['UNIQUE_EMAIL'] == 'Y') {
			$emailUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("EMAIL" => $arParams['USER']["EMAIL"], "ACTIVE" => "Y"));
			if((int)$emailUsers->SelectedRowsCount() > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * ���������� ����� �� ���������������� ������
	 * @param bool $token
	 * @return bool|mixed|string
	 */
	public static function uloginGetUserFromToken($token = false) {
		$response = false;
		if($token) {
			$data = array('cms' => 'Bitrix', 'version' => constant('SM_VERSION'));
			$request = 'https://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST'] . '&data=' . base64_encode(json_encode($data));
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
	 * ����������� �� ����� � � ������� uLogin
	 * @param Array $u_user - ������ � ������������, ���������� �� uLogin
	 * @param int $in_db - ��� �������� 1 ���������� ���������� ������ � ������� uLogin
	 * @return bool|int|Error
	 */
	public static function RegistrationUser($u_user, $in_db = 0, $arParams) {
		if(!isset($u_user['email'])) {
			ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_EMAIL')));
			die('<br/><a href="' . $_POST['backurl'] . '">' . GetMessage('ULOGIN_BACK') . '</a>');
		}
		global $DB;
		if($in_db == 1) {
			$result = $DB->Query('DELETE FROM ulogin_users WHERE identity = "' . urlencode($u_user['identity']) . '"');
		}
		$rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("EMAIL" => $u_user['email'], "ACTIVE" => "Y"));
		$arUser = $rsUsers->GetNext();
//		$check_m_user == true -> ���� ������������ � ����� email
		$check_m_user = $arUser['ID'] > 0 ? true : false;
		global $USER;
		$current_user = $USER->GetID();
		// $isLoggedIn == true -> ����������� ������
		$isLoggedIn = $current_user > 0 ? true : false;
		if(!$check_m_user && !$isLoggedIn) {
		} else {
			if(!isset($u_user["verified_email"]) || (int)$u_user["verified_email"] != 1) {
				die('<script src="//ulogin.ru/js/ulogin.js"  type="text/javascript"></script><script type="text/javascript">uLogin.mergeAccounts("' . $_POST['token'] . '")</script>' . GetMessage('ULOGIN_ERROR_SYNC_CONFIRM') . '<br/><a href="' . $_POST['backurl'] . '">' . GetMessage('ULOGIN_BACK') . '</a>');
			}
			if((int)$u_user["verified_email"] == 1) {
				$user_id = $isLoggedIn ? $current_user : $arUser['ID'];
				$other_u = $DB->Query('SELECT identity,network FROM ulogin_users WHERE userid = "' . $user_id . '"');
				$other = array();
				while($row = $other_u->Fetch()) {
					$ident = $row['identity'];
					$key = $row['network'];
					$other['identity'] = $ident;
				}
				if($other) {
					if(!$isLoggedIn && !isset($u_user['merge_account'])) {
						die('<script src="//ulogin.ru/js/ulogin.js"  type="text/javascript"></script><script type="text/javascript">uLogin.mergeAccounts("' . $_POST['token'] . '","' . $other['identity'] . '")</script>' . GetMessage('ULOGIN_ERROR_SYNC_MERGE') . '<br/><a href="' . $_POST['backurl'] . '">' . GetMessage('ULOGIN_BACK') . '</a>');
					}
				}
				$result = $DB->Query('INSERT INTO ulogin_users (id, userid, identity, network) VALUES (NULL,"' . $user_id . '","' . urlencode($u_user['identity']) . '","' . $u_user['network'] . '")');

				return $user_id;
			}
		}

		return false;
	}

	/**
	 * ���������� ������ � ������������ � ����
	 * @param $u_user - ������ � ������������, ���������� �� uLogin
	 * @param $id_customer - ������������� ������������
	 * @return string
	 */
	public function loginUser($u_user, $id_customer) {
		global $USER;
		//���������� ������������
		//�������� �������� ��������� ������
		$USER->Authorize($id_customer);
	}

	/**
	 * ����� ������ ��������� ������������
	 * @param int $user_id - ID ������������ (���� �� ����� - ������� ������������)
	 * @return string
	 */
	static function getuLoginUserAccountsPanel($user_id = 0) {
		global $USER;
		global $DB;
		$current_user = $USER->GetID();
		$user_id = empty($user_id) ? $current_user : $user_id;
		if(empty($user_id))
			return '';
		$results = $DB->Query('SELECT * FROM ulogin_users  WHERE  userid= "' . $user_id . '"');
		$networks = array();
		while($row = $results->Fetch()) {
			$key = $row['network'];
			$value = $row['identity'];
			$networks[$key] = $value;
		}
		$output = '';
		if($networks) {
			$output .= '<div id="ulogin_accounts">';
			foreach($networks as $key => $network) {
				$output .= "<div data-ulogin-network='{$key}'  data-ulogin-identity='{$network}' class='ulogin_network big_provider {$key}_big'></div>";
			}
			$output .= '</div><div style="clear: both"></div>';

			return $output;
		}

		return '';
	}
}

?>