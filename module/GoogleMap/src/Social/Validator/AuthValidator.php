<?php
namespace Social\Validator;
// Валидаторы
use Zend\Validator\Digits;

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
 * Валидатор формы авторизации
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Validator/AuthValidator.php
 */
class AuthValidator implements InputFilterAwareInterface
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
             * Логин (Email или Мобильный)
             */

            $inputFilter->add($factory->createInput([  // Поле ввода логина
                'name'          =>  'login', // имя проверяемого поля, созданного в Form
                'required'      =>  true,   // обязательное
                'filters'       => array(
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
                ),
            ]));

            /*
             * Пароль
             */

            $inputFilter->add($factory->createInput([  // Поле ввода пароля
                'name'          =>  'password', // имя проверяемого поля, созданного в Form
                'required'      =>  true,   // обязательное
                //'allow_empty' =>  true,   // если допустимо пустое значение
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую полк
                ),
                'validators' => array(
                    array(  // проверка на пустоту
                        'name'                  => 'NotEmpty', // валидатор
                        'options'   => array(
                            'encoding' => 'UTF-8',
                            'messages' => array(
                                'isEmpty' => $this->translator->translate("You left the field blank to enter your password future", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),

                    array( // валидация на длину поля
                        'name'                  => 'StringLength', // валидатор
                        'options'   => array(
                            'encoding' => 'UTF-8',
                            'max'      => 50,
                            'messages' => array(
                                'stringLengthTooLong'   => $this->translator->translate("Your Password '%value%' is more than %max% characters long", 'errors'), // сообщение об ошибке
                            )
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
