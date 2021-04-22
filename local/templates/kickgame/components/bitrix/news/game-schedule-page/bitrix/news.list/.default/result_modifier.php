<?php
if(array_key_exists("IS_AJAX", $_REQUEST) && $_REQUEST["IS_AJAX"] == "Y")
{
    $APPLICATION->RestartBuffer();
}
