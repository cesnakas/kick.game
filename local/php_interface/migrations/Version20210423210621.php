<?php

namespace Sprint\Migration;


class Version20210423210621 extends Version
{
    protected $description = "Группы юзеров";

    protected $moduleVersion = "3.25.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->saveGroup('base',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Базовый',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
        $helper->UserGroup()->saveGroup('standart',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Стандарт',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
        $helper->UserGroup()->saveGroup('premium',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Премиум',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
        $helper->UserGroup()->saveGroup('elait',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Элайт',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
