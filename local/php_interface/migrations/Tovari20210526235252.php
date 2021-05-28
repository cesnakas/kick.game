<?php

namespace Sprint\Migration;


class Tovari20210526235252 extends Version
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
        $iblockId = $helper->Iblock()->getIblockIdIfExists('tovari', 'subscr');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Подпись (EN)',
  'ACTIVE' => 'Y',
  'SORT' => '500',
  'CODE' => 'SIGN_ENG',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'S',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => NULL,
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'IS_REQUIRED' => 'N',
  'VERSION' => '1',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
));

    }

    public function down()
    {
        //your code ...
    }
}
