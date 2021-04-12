<?php
namespace App;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField;

Loc::loadMessages(__FILE__);

/**
 * Class CookiesTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> C_DATE datetime mandatory
 * <li> CONTENT text mandatory
 * </ul>
 *
 * @package Bitrix\Login
 **/

class UserLoginCookiesTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'user_login_cookies';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('COOKIES_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'USER_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('COOKIES_ENTITY_USER_ID_FIELD')
                ]
            ),
            new DatetimeField(
                'C_DATE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('COOKIES_ENTITY_C_DATE_FIELD')
                ]
            ),
            new TextField(
                'CONTENT',
                [
                    'required' => true,
                    'title' => Loc::getMessage('COOKIES_ENTITY_CONTENT_FIELD')
                ]
            ),
        ];
    }
}