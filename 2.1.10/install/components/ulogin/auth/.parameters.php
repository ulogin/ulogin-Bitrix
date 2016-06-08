<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$group_list = CGroup::GetList(($by = "id"), ($order = "asc"), array("ACTIVE" => "Y"));
$groups = array();

while($group =  $group_list->GetNext()){
    $groups[$group['ID']] = $group['NAME'];
}

$arComponentParameters = array(
    'PARAMETERS' => array(
        'ULOGINID1' => array(
            'NAME' => GetMessage("ULOGIN_PANEL_ID1"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
        'ULOGINID2' => array(
            'NAME' => GetMessage("ULOGIN_PANEL_ID2"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'PARENT' => 'BASE',
        ),
	    "SEND_EMAIL" => array(
	        'NAME' => GetMessage("ULOGIN_SEND_EMAIL").' email '.GetMessage("ULOGIN_ADMINISTRATORU_PRI_R"),
	        'TYPE' => 'CHECKBOX',
	        'PARENT' => 'BASE',
	        'DEFAULT' => 'N'
	    ),
        "SOCIAL_LINK" => array(
	        'NAME' => GetMessage("ULOGIN_SOCIAL"),
	        'TYPE' => 'CHECKBOX',
	        'PARENT' => 'BASE',
	        'DEFAULT' => 'N'
	    ),
        "LOGIN_AS_EMAIL" => array(
	        'NAME' => GetMessage("ULOGIN_LOGIN_AS_EMAIL"),
	        'TYPE' => 'CHECKBOX',
	        'PARENT' => 'BASE',
	        'DEFAULT' => 'N'
	    ),
        "GROUP_ID" => array(
            'NAME' => GetMessage("ULOGIN_GROUPS_MESSAGE"),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $groups,
            'PARENT' => 'BASE',
            'DEFAULT' => '5'
        )
    ),
);
?>
