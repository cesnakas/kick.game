<?php

namespace Sprint\Migration;


class pubgid20210506221217 extends Version
{
    protected $description = "";

    protected $moduleVersion = "3.25.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'USER',
  'FIELD_NAME' => 'UF_PUBG_ID_CHECK',
  'USER_TYPE_ID' => 'enumeration',
  'XML_ID' => 'UF_PUBG_ID_CHECK',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DISPLAY' => 'LIST',
    'LIST_HEIGHT' => 5,
    'CAPTION_NO_VALUE' => '',
    'SHOW_NO_VALUE' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Check PUBG ID and USENAME',
    'ru' => 'Проверка PUBG ID и USENAME',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ENUM_VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'Unverified',
      'DEF' => 'Y',
      'SORT' => '500',
      'XML_ID' => '317e717920611c6a0f09c6bb1806dab2',
    ),
    1 => 
    array (
      'VALUE' => 'Checking',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '454c4133d6506d7f9285954680360f8e',
    ),
    2 => 
    array (
      'VALUE' => 'Verified',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '33a2cbf657643d0017f548b6f344cdc3',
    ),
    3 => 
    array (
      'VALUE' => 'CheckingNext',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '04ad25725a77c02e6c046f74878e9425',
    ),
    4 => 
    array (
      'VALUE' => 'Rejected',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '742f683223f05083e89159d93bce1849',
    ),
    5 => 
    array (
      'VALUE' => 'VerifiedTouchOk',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '9d6dcccf0d79333e34a45e81c77465aa',
    ),
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
