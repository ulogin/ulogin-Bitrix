<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: text/html; charset=windows-1251');
define("STOP_STATISTICS", true);
global $USER;
global $DB;
$user_id = $USER->GetID();
$identity = isset($_POST['identity']) ? $_POST['identity'] : '';
if(isset($identity)) {
	$result = $DB->Query('DELETE FROM ulogin_users WHERE identity = "' . $identity . '"');
	if($result) {
		die(json_encode(array('msg' => "Удаление аккаунта успешно выполнено", 'user' => $user_id, 'answerType' => 'ok')));
	} else {
		die(json_encode(array('msg' => "Ошибка при выполнении запроса на удаление", 'answerType' => 'error')));
	}
} else
	die(json_encode(array('msg' => "Ошибка при удаление аккаунта", 'answerType' => 'error')));
?>