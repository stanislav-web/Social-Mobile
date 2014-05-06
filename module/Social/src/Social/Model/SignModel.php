<?php
namespace Social\Model; // инициализирую текущее пространство имен

// подключаю адаптеры Бд
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql; // для запросов
use Zend\Db\Sql\Select;
// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container; // Сессии (для чтения переменных регистрации)
use Zend\Math\Rand;
use Zend\Crypt\Password\Bcrypt;

/**
 * Модель для авторизации, регистрации и восстановления пароля
 * имплементирую ServiceLocatorAwareInterface, чтоб была возможность
 * использовать сервис менеджер в модели
 * $sm->get('sign.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Model/SignModel.php
 */
class signModel extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * $__authService Свойство хранения сервиса авторизации
     * @access protected
     * @var type object
     */
    protected $_authService;

    /**
     * $__authStorage Свойство хранения информации о сессионном хранилище
     * @access private
     * @var type object
     */
    protected $_authStorage;

    /**
     * Основная таблица, к которой обращаюсь
     * @access protected
     * @var string $table;
     */
    protected $table = 'zf_users';

    /**
     * Таблица с профилями
     * @access protected
     * @var string $table_profile;
     */
    protected $table_profile = 'zf_users_profile';
    
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;

    /**
     * Конструктор адаптера БД
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }
    
   /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }
    
    /**
     * getAuthService() метод достает сервис авторизации по \Zend\Authentication\AuthenticationService()
     * @access public
     * @return object auth.Service
     */
    public function getAuthService()
    {
        if(!$this->_authService) $this->_authService =  $this->zfService()->get('authentification.Service'); // инициализирован в Model.php
        return $this->_authService;
    }

    /**
     * getSessionStorage() метод достает сервис хранилища, посе авторизации по \Zend\Authentication\AuthenticationService()
     * @access public
     * @return object auth.Service
     */
    public function getSessionStorage()
    {
        if(!$this->_authStorage) $this->_authStorage = $this->zfService()->get('auth.Service'); // инициализирован в Model.php
        return $this->_authStorage;
    }

    /**
     * getUserSaltAuthenticate($id) метод достает фиксированную строку при авторизации (для защиты пароля)
     * @param int $id user id
     * @access private
     * @return object CSRF code
     */
    private function __getUserSaltAuthenticate($id)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                ->columns(array(
                    'csrf',
                    'role_id',
                ))
                ->where('`activation` = \'1\' AND `id` = '.(int)$id)
                ->order('id ASC')
                ->limit(1);
               //$select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
        })->current();
        //print_r($resultSet); exit;
        if($resultSet) return $resultSet; // PHP5.4 in use
    }

    /**
     * getRestoreCode($type) генератор случайного кода
     * @param string $type (mail / mobile) для типа восстановления
     * @access private
     * @return string random
     */
    public function getRestoreCode($type = '')
    {
        if($type == 'email') $string = Rand::getString(25, '0123456789', true);
        else $string = Rand::getString(5, '0123456789', true);
        return $string;
    }

    /**
     * dropRestoreCode($id, $type = '') Удаления ключа посе восстановления пароля
     * @param string $type (email / mobile) для типа восстановления
     * @param int $id ID пользователя
     * @access public
     * @return string random
     */
    public function dropRestoreCode($id, $type = '')
    {
        $Adapter = $this->adapter; // Загружаю адаптер БД
        $sql = new Sql($Adapter);

        if($type == 'email')
        {
            //Удаляю ключ который пришел по email

            $update = $sql->update($this->table);
            $update->set(array(
                        'mail_code' => ''
                    )
            );
            $update->where(array('id' => $id));
        }
        else
        {
            //@TODO Удаляю код который пришел в SMS

        }
        $statement = $sql->prepareStatementForSqlObject($update);
        $rows = 0;
        try {
            $result = $statement->execute();
            $rows = $result->getAffectedRows();
            return true;
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    /**
     * __getRestoreUsername($name) метод выборки имени пользователя по логину
     * @param string $login логин пользователя
     * @access private
     * @return object Базы данных
     */
    private function __getRestoreUsername($login)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($login) {
            $select
                ->join($this->table_profile, $this->table_profile.'.user_id = '.$this->table.'.id', array(
                    'name',
                ))
                ->where('`'.$this->table.'`.`activation` = \'1\' AND `mail_code` !=\'\' AND `login` = \''.$login.'\'')
                ->limit(1);
        })->current();
        return $resultSet;
    }

    /**
     * setRestore($login, $type = '') восстановление доступа через email
     * @param string $login Email адрес пользователя
     * @access public
     * @return object DB result
     */
    public function setRestore($login, $type = '')
    {
            $Adapter = $this->adapter; // Загружаю адаптер БД
            $restoreArr = array();
            if($type == 'mobile')
            {
                // Код восстановления для мобильного
                $restoreArr['code'] = $this->getRestoreCode($type);
                $sql = new Sql($Adapter);
                $update = $sql->update($this->table);
                $update->set(array('sms_code' => $restoreArr['code']));
                $update->where(array('login' => $login, 'activation' => '1'));
            }
            else
            {
                // Код восстановления для email
                $restoreArr['code'] = $this->getRestoreCode($type);
                $bcrypt = new Bcrypt();
                $mail_code = $bcrypt->create($restoreArr['code']); // шифрую код
                $sql = new Sql($Adapter);
                $update = $sql->update($this->table);
                $update->set(array('mail_code' => $mail_code));
                $update->where(array('login' => $login, 'activation' => '1'));
                $updateString = $sql->getSqlStringForSqlObject($update);
                $results = $this->adapter->query($updateString, $Adapter::QUERY_MODE_EXECUTE);

                // Беру имя пользоватея и присоединяю
                $restoreArr['name'] = $this->__getRestoreUsername($login)->name;
            }

            if($results) return $restoreArr; // возвращаю оригинальный код для отправки на почту или SMS
            else 
            {
                $this->_lng             = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
                throw new \Exception($this->_lng->translate('Ivalid restore parameter. SQL Query is invalid', 'exceptions'));
            }
     }

    /**
     * setNewPassword($formResult) установка нового пароля
     * @param array $formResult массив валидных данных из формы
     * @access public
     * @return object DB result
     */
    public function setNewPassword($formResult)
    {
        $Adapter = $this->adapter; // Загружаю адаптер БД
        $restoreArr = array();
        if($formResult['restore'] == 'mobile')
        {
                // Обновляю пароль, если форма была для SMS кода
        }
        else
        {
            // Беру пользователя с подходящим логином
            // Обновляю пароль, если форма была для Email кода
            $resultSet = $this->select(function (Select $select) use ($formResult) {
            $select->columns(array('id', 'mail_code'))
                    ->where('`login` = \''.$formResult['login'].'\' AND `activation` = \'1\'')
                    ->limit(1);
                    //print $select->getSqlString($this->adapter->getPlatform()); // SHOW SQL
            })->current();

            // Проверяю ключи
            $bcrypt = new Bcrypt();
            if($bcrypt->verify($formResult['mail_code'], $resultSet->mail_code))
            {
                // Все верно! Шифрую пароль
                $password = md5($formResult['password']).$formResult['csrf'];

                // Перезаписываю у юзера csrf, mail_code, password
                $sql = new Sql($Adapter);
                $update = $sql->update($this->table);
                $update->set(array(
                        'mail_code' => '',
                        'csrf' => $formResult['csrf'],
                        'password' => new \Zend\Db\Sql\Expression("MD5('".$password."')")
                    )
                );
                $update->where(array('id' => $resultSet->id));
                //print $update->getSqlString($this->adapter->getPlatform()); // SHOW SQL
                return $resultSet;
            }
            else return false;
        }
    }

    /**
     * signAuth($id, $password, $remember = 0) авторизация. Все параметры должны передаваться в чистом виде!
     * @param string $id ID User
     * @param string $password  пароль
     * @param int $remember запомнить меня? (true, false) // в секундах
     * @access public
     * @return boolean
     */
    public function signAuth($id, $password, $remember = 0)
    {
        $salt = $this->__getUserSaltAuthenticate($id); // возвращаю код защиты. PS [0] PHP5.4 in use
        if($salt)
        {
            $this->getAuthService()
                                ->getAdapter()
                                ->setIdentity($id)
                                ->setCredential(md5($password).$salt->csrf); // устанавиваю пароль как md5($_POST['password']) + $db['csrf']
            $result = $this->getAuthService()->authenticate();
            if($result->isValid())
            {
                // Авторизция прошла.. проверяю ее на долгое сохранение
                if(isset($remember) && $remember > 0)
                {
                    // Если выбрали запомнить, устанавливаю параметры формы в сессию
                    $config = $this->zfService()->get('Configuration');
                    $this->getSessionStorage()->setRememberMe($config['session']['remember_me_seconds']);
                    // устанавливаю все в хранилище
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                }
                $this->getAuthService()->getStorage()->write($id);
                return true;
            }
            else return false;
        }
        else return false;
    }
    
    /**
     * isAdmin($id) Проверка админа
     * @param string $login
     * @access public
     * @return object DB `zf_users`
     */
    public function isAdmin($id)
    {
        // Использую лямпду как передаваемый объект для выборки
        $resultSet = $this->select(function (Select $select) use ($id) {
            $select
                ->where('`activation` = \'1\' AND `role_id` = 4 AND `id` = \''.$id.'\'')
                ->limit(1);
               //$select->getSqlString($this->_adapter->getPlatform()); // SHOW SQL
        })->current();
        if($resultSet) return true;
        else return false;
    }
    
    /**
     * signRegister() регистрация и запись в базу. Внутри должна быть сессия
     * @param array $array массив с полями
     * @access public
     * @return boolean
     */
    public function signRegister()
    {
        $this->_lng = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        $register   = new Container('register'); // достаю контейнер сессии с регистрацией
        
        if(!isset($register->init)) throw new \Exception($this->_lng->translate('Ivalid register parameter. Session continer could not exist', 'exceptions'));
        else
        {
            // Создаю запись в базе для основной группы в zf_users
            // ZendFramework 1 Style )))
            $Adapter = $this->adapter;
            $sql = new Sql($Adapter);
            $insert = $sql->insert($this->table);
            $dataUsers = array(
                'login'             => $register->login,
                'password'          => $register->password,
                'csrf'              => $register->csrf,
                'activation'        => 1,
                'date_registration' => new \Zend\Db\Sql\Expression("NOW()"),
                'ip'                => new \Zend\Db\Sql\Expression("INET_ATON('{$register->ip}')"),
                'agent'             => $register->agent,
                'online'        => 1,

            );
            $insert->values($dataUsers);
            $selectString = $sql->getSqlStringForSqlObject($insert);
            //print $selectString; exit;
            $results = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
            if($results)
            {
                $lastId = $Adapter->getDriver()->getLastGeneratedValue(); // получаю поледний ID и готовлю для вставки в профиль
                $insert = $sql->insert($this->table_profile);
                $dataProfile = array(
                    'name'          => $register->name,
                    'gender'        => $register->gender,
                    'country_id'    => $register->country_id,
                    'region_id'     => $register->region_id,
                    'city_id'       => $register->city_id,
                    'user_id'       => $lastId,
                );
                if(!empty($register->birthday) &&  $register->birthday !='0000-00-00') $dataProfile['birthday'] = $register->birthday;

                if(isset($register->photo) && !empty($register->photo))$dataProfile['photo'] = $register->photo; // еси было фото
                $insert->values($dataProfile);
                $selectString = $sql->getSqlStringForSqlObject($insert);
                $results = $this->adapter->query($selectString, $Adapter::QUERY_MODE_EXECUTE);
                if($results)
                {
                    $register->user_id = $lastId; // запишу в сессию ID пользователя для создания директорий
                    return true; // возвращаю результат в контроллер
                }
                else throw new \Exception($this->_lng->translate('Ivalid register parameter. SQL Query is invalid', 'exceptions'));
            }
            else throw new \Exception($this->_lng->translate('Ivalid register parameter. SQL Query is invalid', 'exceptions'));
        }
    }
}