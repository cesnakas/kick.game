<?php

namespace Sprint\Migration;


class users20210322155228 extends Version
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

        $helper->UserGroup()->saveGroup('administrators',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '1',
  'ANONYMOUS' => 'N',
  'NAME' => 'Администраторы',
  'DESCRIPTION' => 'Полный доступ к управлению сайтом.',
  'SECURITY_POLICY' => NULL,
));
        $helper->UserGroup()->saveGroup('everyone',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '2',
  'ANONYMOUS' => 'Y',
  'NAME' => 'Все пользователи (в том числе неавторизованные)',
  'DESCRIPTION' => 'Все пользователи, включая неавторизованных.',
  'SECURITY_POLICY' => NULL,
));
        $helper->UserGroup()->saveGroup('RATING_VOTE',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '3',
  'ANONYMOUS' => 'N',
  'NAME' => 'Пользователи, имеющие право голосовать за рейтинг',
  'DESCRIPTION' => 'В эту группу пользователи добавляются автоматически.',
  'SECURITY_POLICY' => NULL,
));
        $helper->UserGroup()->saveGroup('RATING_VOTE_AUTHORITY',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '4',
  'ANONYMOUS' => 'N',
  'NAME' => 'Пользователи имеющие право голосовать за авторитет',
  'DESCRIPTION' => 'В эту группу пользователи добавляются автоматически.',
  'SECURITY_POLICY' => NULL,
));
        $helper->UserGroup()->saveGroup('USER_PLATFORM',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Пользователи платформы',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
        $helper->UserGroup()->saveGroup('REFERY',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '100',
  'ANONYMOUS' => 'N',
  'NAME' => 'Рефери',
  'DESCRIPTION' => '',
  'SECURITY_POLICY' => 
  array (
  ),
));
        $helper->UserGroup()->saveGroup('MAIL_INVITED',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '201',
  'ANONYMOUS' => 'N',
  'NAME' => 'Почтовые пользователи',
  'DESCRIPTION' => 'Пользователи, авторизуемые на портале по прямой ссылке из почтовых уведомлений',
  'SECURITY_POLICY' => NULL,
));
    }

    public function down()
    {
        //your code ...
    }
}
