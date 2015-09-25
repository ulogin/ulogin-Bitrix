<?
IncludeModuleLangFile(__FILE__);

Class delaweb_ulogin extends CModule {
	const MODULE_ID = 'delaweb.ulogin';
	var $MODULE_ID = 'delaweb.ulogin';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("delaweb.ulogin_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("delaweb.ulogin_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("delaweb.ulogin_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("delaweb.ulogin_PARTNER_URI");
	}

	function InstallDB($arParams = array()) {
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CDelawebUlogin', 'OnBuildGlobalMenu');
		global $DB;
		$DB->Query("CREATE TABLE IF NOT EXISTS ulogin_users(
	   	id INTEGER NOT NULL auto_increment,
		userid INTEGER NOT NULL,
		identity VARCHAR(256) NOT NULL,
		network VARCHAR(256) NOT NULL,
		PRIMARY KEY(id))
		ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;");

		return true;
	}

	function UnInstallDB($arParams = array()) {
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CDelawebUlogin', 'OnBuildGlobalMenu');

		return true;
	}

	function InstallEvents() {
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function InstallFiles($arParams = array()) {
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
			if($dir = opendir($p)) {
				while(false !== $item = readdir($dir)) {
					if($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/admin/' . $item . '");?' . '>');
				}
				closedir($dir);
			}
		}
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
			if($dir = opendir($p)) {
				while(false !== $item = readdir($dir)) {
					if($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $item, $ReWrite = true, $Recursive = true);
				}
				closedir($dir);
			}
		}

		return true;
	}

	function UnInstallFiles() {
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
			if($dir = opendir($p)) {
				while(false !== $item = readdir($dir)) {
					if($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item);
				}
				closedir($dir);
			}
		}
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
			if($dir = opendir($p)) {
				while(false !== $item = readdir($dir)) {
					if($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
						continue;
					$dir0 = opendir($p0);
					while(false !== $item0 = readdir($dir0)) {
						if($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/' . $item . '/' . $item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}

		return true;
	}

	function DoInstall() {
		global $APPLICATION, $DB;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		$old_users = CUser::GetList(($by = "id"), ($order = "desc"), array("EXTERNAL_AUTH_ID"));
		while($row = $old_users->Fetch()) {
			list($network, $id) = explode('=', $row['ADMIN_NOTES']);
			if($id) {
				$DB->Query('INSERT INTO ulogin_users (id, userid, identity, network) VALUES (NULL,"' . $id . '","' . urlencode($row['EXTERNAL_AUTH_ID']) . '","' . $network . '")');
			}
		}
	}

	function DoUninstall() {
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}

?>
