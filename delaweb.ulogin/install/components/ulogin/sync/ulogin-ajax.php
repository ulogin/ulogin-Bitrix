<?php
header('Content-Type: text/html; charset=windows-1251');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
define("STOP_STATISTICS", true);

global $USER;
global $DB;

$user_id = $USER->GetID();

$identity = isset($_POST['identity']) ? $_POST['identity']: '';

if (isset($identity))
{
    $result = $DB->Query('DELETE FROM ulogin_users WHERE identity = "'.$identity.'"');
    if ($result)
        die(json_encode(array(
            'msg' => GetMessage('ULOGIN_DELETE_ACCOUNT'),
            'user' => $user_id,
            'answerType' => 'ok'
        )));
    else
        die(json_encode(array(
            'msg' => GetMessage('ULOGIN_ERROR_QUERY_DELETE'),
            'answerType' => 'error'
        )));
}
else
    die(json_encode(array(
        'msg' => GetMessage('ULOGIN_ERROR_DELETE_ACCOUNT'),
        'answerType' => 'error'
    )));
?>