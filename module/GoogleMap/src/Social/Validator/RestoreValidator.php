<?php
namespace Social\Validator;

// Интерфейсы и классы фильтров
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

// Объект адаптера БД
use Zend\Db\Adapter\Adapter;

//Интерфейсы сервиса
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Валидатор формы восстановления аккаунта
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Validator/RestoreValidator.php
 */
class RestoreValidator implements InputFilterAwareInterface
{
    /**
     * @var Translator ln18
     */
    protected $translator;

    /**
     * @var inputFilter
     */
    protected $inputFilter;

    /**
     * @var Database Adapter
     */
    protected $dbAdapter;

    /**
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    /**
     * @param \Zend\Db\Adapter $dbAdapter, object $translator
     */
    public function __construct(Adapter $dbAdapter, $translator)
    {
        $this->translator   = $translator;  // передал и установил
        $this->dbAdapter    = $dbAdapter;   // адаптер базы для проверки записей на существование... правила ниже
    }

    /**
     *
     * @return Zend\Db\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * Фильтр формы
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if(!$this->inputFilter)
        {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            /*
             * Сначала проверяю токен CSRF
             */

            $inputFilter->add($factory->createInput([
                'name'          =>  'csrf', // имя проверяемого поля, созданного в Form
                'validators' => array(
                    array(  // проверка на пустоту
                        'name'                  => 'Csrf', // валидатор
                        'timeout'   =>  360, // таймаут
                        'options'   => array(
                            'messages' => array(
                                'notSame'  => $this->translator->translate("There is a suspicion that the data from this were sent from another site or source, please re-enter again", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                ),
            ]));

            /**
             * Затем проверяю email и телефон
             */
            $inputFilter->add($factory->createInput([  // Поле ввода емаил или телефон
                'name'          =>  'resign', // имя проверяемого поля, созданного в Form
                'required'      =>  true,   // обязательное
                //'allow_empty' =>  true,   // если допустимо пустое значение
                'filters'       => array(
                    array('name'    => 'StripTags'), // удаляю теги
                    array('name'    => 'StringTrim'), // тримирую поле
                ),
                'validators' => array(
                    array(  // проверка на пустоту
                        'name'                  => 'NotEmpty', // валидатор
                        'options'   => array(
                            'encoding' => 'UTF-8',
                            'messages' => array(
                                'isEmpty' => $this->translator->translate("You left the field blank to enter your login", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                    array( // валидация по Regex
                        'name'                  => 'Regex', // валидатор
                        'options'   => array(
                            'pattern'  => '/^(([\w]([-.\w]*[\w])*@([\w][-\w]*[\w]\.)+[a-zA-Z]{2,9})|((\+?)(\d){11,12}))$/i',
                            'messages' => array(
                                'regexNotMatch' => $this->translator->translate("Invalid input format %value%! Only Email or number formats accepted", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                    array( // проверка на существование а базе
                        'name'                  => 'Db\RecordExists', // валидатор
                        'options'   => array(
                            'table'     => 'zf_users', // таблица, где проверяю запись
                            'field'     => 'login', // имя поля
                            'messages' => array(
                                'noRecordFound' => $this->translator->translate("No user account with %value% was found", 'errors'), // сообщение об ошибке
                            ),
                            'adapter' => $this->getDbAdapter(), // установил адаптер для проверки этой записи
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                ),
            ]));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
