<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
require_once "include/Ulogin.class.php";
require_once "include/functions.php";
$arResult = $arParams;
global $DB;
global $USER;
global $APPLICATION;

if(!empty($_POST['token']) && !$USER->isAuthorized()) {
	$s = Ulogin::uloginGetUserFromToken($_POST['token']);
	if(!$s) {
		ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => GetMessage('ULOGIN_ERROR_DATA_TOKEN')));

		return;
	}
	$profile = json_decode($s, true);
	$check = Ulogin::CheckTokenError($profile);
	if(!$check) {
		return false;
	}
//проверяем пользователя в таблице uLogin`а
	$user_id = $DB->Query('SELECT userid FROM ulogin_users WHERE identity = "' . urlencode($profile['identity']) . '"');
	$user_id = $user_id->GetNext();
	$user_id = $user_id['userid'];
	if($user_id) {
		$loginUsers = CUser::GetList(($by = "id"), ($order = "desc"), array("ID" => $user_id, "ACTIVE" => "Y"));
		if($user_id > 0 && $loginUsers->SelectedRowsCount() > 0)
			Ulogin::uloginCheckUserId($user_id); else $user_id = Ulogin::RegistrationUser($profile, 1, $arParams);
	} else
		$user_id = Ulogin::RegistrationUser($profile, 0, $arParams);
	if($user_id > 0)
		Ulogin::loginUser($profile, $user_id);
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

if($GLOBALS['ULOGIN_OK'] == 1) {
	$code = <<<PHP_EOL
<script>
	if(typeof window.uLoginCallbacks === 'undefined') window.uLoginCallbacks = [];
	function uLoginOnload() {
		if(window.uLoginCallbacks.length){
			for (var i = 0; i < window.uLoginCallbacks.length; i++) {
				var f = window.uLoginCallbacks[i];
				if(typeof f === 'function'){
					f.call(window);
				}
				window.uLoginCallbacks.splice(i--,1)
			}
		}
	}
</script>
<script src="//ulogin.ru/js/ulogin.js" onload="uLoginOnload()"></script>
$code
PHP_EOL;
}
$arResult['ULOGIN_CODE'] = $code;
$this->IncludeComponentTemplate();
?>