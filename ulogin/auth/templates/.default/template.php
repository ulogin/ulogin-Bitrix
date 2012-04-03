<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>



<? if (!$USER->IsAuthorized()): ?>

    <?php echo $arResult['ULOGIN_CODE']; ?>

<? else: ?>

    Привет, <strong><?=$USER->GetLogin()?></strong>! <a
    href="<?=$APPLICATION->GetCurPageParam("logout=yes", array("logout"))?>">Выйти</a>

<?endif; ?>