<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
require_once 'include/UloginSync.class.php';
$arResult = $arParams;
global $DB;
global $USER;
global $APPLICATION;
if(!empty($_POST['token']) && $USER->isAuthorized()) {
	$s = UloginSync::uloginGetUserFromToken($_POST['token']);
	if(!$s) {
		ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_DATA_TOKEN')));

		return;
	}
	$profile = json_decode($s, true);
	$check = UloginSync::CheckTokenError($profile);
	if(!$check) {
		return false;
	}
//провер€ем пользовател€ в таблице uLogin`а
	$user_id = $DB->Query('SELECT * FROM uLogin_users WHERE identity = "' . urlencode($profile['identity']) . '"');
	$user_id = $user_id->GetNext();
	$user_id = $user_id['userid'];
	if($user_id) {
		$loginUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("ID" => $user_id, "ACTIVE" => "Y"));
		if($user_id > 0 && $loginUsers->SelectedRowsCount() > 0)
			UloginSync::uloginCheckUserId($user_id); else $user_id = UloginSync::RegistrationUser($profile, 1, $arParams);
	} else
		$user_id = UloginSync::RegistrationUser($profile, 0, $arParams);
	if($user_id > 0)
		UloginSync::loginUser($profile, $user_id);
	if($arParams["REDIRECT_PAGE"] != "")
		LocalRedirect($arParams["REDIRECT_PAGE"]); else
		LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));
}
if(!isset($GLOBALS['ULOGIN_OK'])) {
	$GLOBALS['ULOGIN_OK'] = 1;
} else {
	$GLOBALS['ULOGIN_OK']++;
}
$code = getPanelCode(0, $arParams);
/*
 * ѕолучает div панель
 */
function getPanelCode($place = 0, $arResult) {
	$default_panel = false;
	switch($place) {
		case 0:
			$uloginID = $arResult['ULOGINID1'];
			break;
		case 1:
			$uloginID = $arResult['ULOGINID2'];
			break;
		default:
			$uloginID = $arResult['ULOGINID1'];
	}
	if(empty($uloginID)) {
		$default_panel = true;
	}
	$panel = '';
	$redirect_uri = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$panel .= '<div id=ulogin' . $GLOBALS['ULOGIN_OK'] . ' class="ulogin_panel"';
	if($default_panel) {
		$_uLoginDefaultOptions = array('display' => 'small', 'providers' => 'vkontakte,odnoklassniki,mailru,facebook', 'hidden' => 'other', 'fields' => 'first_name,last_name,email,photo,photo_big', 'optional' => 'phone', 'redirect_uri' => $redirect_uri,);
		$arResult['REDIRECT_PAGE'] = $redirect_uri;
		$x_ulogin_params = '';
		foreach($_uLoginDefaultOptions as $key => $value) {
			$x_ulogin_params .= $key . '=' . $value . ';';
		}
		$panel .= ' data-ulogin="' . $x_ulogin_params . '"></div>';
	} else {
		$panel .= ' data-uloginid="' . $uloginID . '" data-ulogin="redirect_uri=' . $redirect_uri . '"></div>';
	}

	return $panel;
}

if($GLOBALS['ULOGIN_OK'] == 1) {
	$code = '<link type="text/css" rel="stylesheet" href="https://ulogin.ru/css/providers.css">
<script src="//ulogin.ru/js/ulogin.js"></script>' . $code;
}
$arResult['ULOGIN_CODE'] = $code;
$syncpanel = UloginSync::getuLoginUserAccountsPanel();
$arResult['ULOGIN_SYNC'] = $syncpanel;
$this->IncludeComponentTemplate();
?>