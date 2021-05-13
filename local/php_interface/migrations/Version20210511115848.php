<?php

namespace Sprint\Migration;


class Version20210511115848 extends Version
{
    protected $description = "Обновлены группы";

    protected $moduleVersion = "3.25.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->saveGroup('standart',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Временной интервал',
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
