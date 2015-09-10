<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    'PARAMETERS' => array(
        'ULOGINID1' => array(
            'NAME' => GetMessage("ULOGIN_SYNC_PANEL_ID1"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
        'ULOGINID2' => array(
            'NAME' => GetMessage("ULOGIN_SYNC_PANEL_ID2"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
    ),
);
?>
