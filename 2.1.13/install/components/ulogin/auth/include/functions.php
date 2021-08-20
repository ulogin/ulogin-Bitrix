<?php

/**
 * �������� div ������
 * @param int $place
 * @param $arResult
 * @return string
 */
function getPanelCode($place = 0, $arResult)
{
	$default_panel = false;
	switch ($place) {
		case 0:
			$uloginID = $arResult['ULOGINID1'];
			break;
		case 1:
			$uloginID = $arResult['ULOGINID2'];
			break;
		default:
			$uloginID = $arResult['ULOGINID1'];
	}
	if (empty($uloginID)) {
		$default_panel = true;
	}
	$panel = '';
	$redirect_uri = urlencode(uloginGetCurrentPageUrl());
	$id = 'uLogin_' . rand();
	$panel .= '<div id="' . $id . '" class="ulogin' . $GLOBALS['ULOGIN_OK'] . ' ulogin_panel"';
	if ($default_panel) {
		$_uLoginDefaultOptions = array(
			'display' => 'small',
			'providers' => 'vkontakte,odnoklassniki,mailru,facebook',
			'hidden' => 'other',
			'fields' => 'first_name,last_name,email,photo,photo_big',
			'optional' => 'sex,bdate,country,city',
			'redirect_uri' => $redirect_uri
		);
		$arResult['REDIRECT_PAGE'] = $redirect_uri;
		$dataUlogin = '';
		foreach ($_uLoginDefaultOptions as $key => $value) {
			$dataUlogin .= $key . '=' . $value . ';';
		}
		$panel .= ' data-ulogin="' . $dataUlogin . '"></div>';
	} else {
		$panel .= ' data-uloginid="' . $uloginID . '" data-ulogin="redirect_uri=' . $redirect_uri . '"></div>';
	}

	$panel .= <<<PHP_EOL
<script>
if(typeof window.uLogin === 'undefined'){
	window.uLoginCallbacks.push(function () {
		window.uLogin.customInit("$id");
	});
} else {
	window.uLogin.customInit("$id");
}
</script>
PHP_EOL;

	return $panel;
}

/**
 * ���������� ������� url
 */
function uloginGetCurrentPageUrl() {
    global $APPLICATION;
    // ������� ������ URI ������� ��������
    $CURRENT_PAGE = (CMain::IsHTTPS()) ? "https://" : "http://";
    $CURRENT_PAGE .= $_SERVER["HTTP_HOST"];
    $CURRENT_PAGE .= $APPLICATION->GetCurUri();
    // � ���������� $CURRENT_PAGE �������� ����� ��������,
    // "http://www.mysite.ru/ru/index.php?id=23"
    return $CURRENT_PAGE;
}