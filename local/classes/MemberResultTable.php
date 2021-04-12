<?php
namespace App;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField;

Loc::loadMessages(__FILE__);

/**
 * Class MemberResultTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> MATCH_ID int mandatory
 * <li> TOTAL int mandatory
 * <li> KILLS int mandatory
 * <li> PLACE int mandatory
 * <li> TYPE_MATCH int mandatory
 * </ul>
 *
 * @package Bitrix\Squad
 **/

class MemberResultTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_squad_member_result';
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
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'USER_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_USER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'MATCH_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_MATCH_ID_FIELD')
                ]
            ),
            new IntegerField(
                'TOTAL',
                [
                    'required' => true,
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_TOTAL_FIELD')
                ]
            ),
            new IntegerField(
                'KILLS',
                [
                    'required' => true,
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_KILLS_FIELD')
                ]
            ),
            new IntegerField(
                'PLACE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_PLACE_FIELD')
                ]
            ),
          new IntegerField(
            'TYPE_MATCH',
            [
              'required' => true,
              'title' => Loc::getMessage('MEMBER_RESULT_ENTITY_TYPE_MATCH_FIELD')
            ]
          ),
        ];
    }
}