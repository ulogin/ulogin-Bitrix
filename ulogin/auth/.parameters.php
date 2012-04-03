<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    'PARAMETERS' => array(
        'PROVIDERS' => array(
            'NAME' => 'Провайдеры',
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'vkontakte,odnoklassniki,mailru,facebook',
            'PARENT' => 'BASE',
        ),
        'HIDDEN' => array(
            'NAME' => 'Скрытые провайдеры',
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'twitter,google,yandex,livejournal,openid',
            'PARENT' => 'BASE',
        ),
        "TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => 'Тип',
            "TYPE" => "LIST",
            "VALUES" => array('small' => 'small', 'panel' => 'panel'),
            "DEFAULT" => 'panel',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),
        "REDIRECT_PAGE" => array(
            'NAME' => 'Страница редиректа после логина',
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
        )
    ),
);
?>
