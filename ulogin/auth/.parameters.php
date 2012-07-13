<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    'PARAMETERS' => array(
        'PROVIDERS' => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_PROVAYDERY"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'vkontakte,odnoklassniki,mailru,facebook',
            'PARENT' => 'BASE',
        ),
        'HIDDEN' => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_SKRYTYE_PROVAYDERY"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'other',
            'PARENT' => 'BASE',
        ),
        "TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("TALKHARD_ULOGIN_TIP"),
            "TYPE" => "LIST",
            "VALUES" => array('small' => 'small', 'panel' => 'panel'),
            "DEFAULT" => 'panel',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),
        "REDIRECT_PAGE" => array(
            'NAME' => GetMessage("TALKHARD_ULOGIN_STRANICA_REDIREKTA_P"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
        ),
        "UNIQUE_EMAIL" => array(
	  'NAME' => GetMessage("TALKHARD_ULOGIN_REGISTRIROVATQ_POLQZ").' email',
	  'TYPE' => 'CHECKBOX',
	  'PARENT' => 'BASE',
	  'DEFAULT' => 'N'
	),
	"SEND_MAIL" => array(
	  'NAME' => GetMessage("TALKHARD_ULOGIN_OTPRAVLATQ").' email '.GetMessage("TALKHARD_ULOGIN_ADMINISTRATORU_PRI_R"),
	  'TYPE' => 'CHECKBOX',
	  'PARENT' => 'BASE',
	  'DEFAULT' => 'N'
	),
    "SYNC_ULOGIN" => array(
        'NAME' => GetMessage("TALKHARD_ULOGIN_SYNC").' uLogin',
        'TYPE' => 'CHECKBOX',
        'PARENT' => 'BASE',
        'DEFAULT' => 'N'
    )
    ),
);
?>
