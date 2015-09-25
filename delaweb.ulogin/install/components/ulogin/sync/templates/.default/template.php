<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die(); ?>
<? CJSCore::Init(array("jquery")); ?>
<? if($USER->IsAuthorized()): ?>

	<style>
		#ulogin_synchronisation, .ulogin_synchronisation {
			padding: 0px 0px 15px 0;
		}

		.ulogin-title-grey {
			font-family: Arial, Helvetica, sans-serif;
			color: grey;
			font-weight: lighter;
			padding: 0px;
		}

		.ulogin_network {
			float: left;
			margin: 0 5px 0 0;
		}
	</style>

	<div class="profile-link profile-user-div-link"><?= GetMessage("ULOGIN_ACCOUNTS") ?></div>

	<div class="profile-link profile-user-div-link"><?= GetMessage("ULOGIN_SYNC_ACCOUNTS") ?></div>
	<div class="ulogin_synchronisation">
		<?php echo $arResult['ULOGIN_CODE']; ?>
	</div>
	<div class="ulogin-title-grey">
		<?= GetMessage("ULOGIN_ACCOUNTS_DESC") ?><br><br>
	</div>

	<div class="profile-link profile-user-div-link"><?= GetMessage("ULOGIN_ACCOUNTS_DESC_MES") ?></div>
	<div id="ulogin_synchronisation">
		<?php echo $arResult['ULOGIN_SYNC']; ?>
	</div>
	<div class="ulogin-title-grey">
		<?= GetMessage("ULOGIN_SYNC_ACCOUNTS_DESC") ?><br><br>
	</div>

	<script type="text/javascript">
		$(document).ready(function () {
			var uloginNetwork = $('#ulogin_synchronisation').find('.ulogin_network');

			uloginNetwork.click(function () {
				var network = $(this).attr('data-ulogin-network');
				var identity = $(this).attr('data-ulogin-identity');
				uloginDeleteAccount(network, identity);
			});
		});
		function uloginDeleteAccount(network, identity) {
			$.ajax({
				url: '/bitrix/components/ulogin/sync/ulogin-ajax.php',
				type: 'POST',
				dataType: 'json',
				data: {
					identity: identity
				},
				error: function (data, textStatus, errorThrown) {
					alert("Не удалось выполнить запрос");
				},
				success: function (data) {
					switch (data.answerType) {
						case 'error':
							alert(data.title + "\n" + data.msg);
							break;
						case 'ok':
							alert(data.msg);
							var accounts = $('#ulogin_accounts'),
								nw = accounts.find('[data-ulogin-network=' + network + ']');
							if (nw.length > 0) nw.hide();
							break;
						default:
							break;
					}
				}
			});
		}
	</script>

<? endif; ?>