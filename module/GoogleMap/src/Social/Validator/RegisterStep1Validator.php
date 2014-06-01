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
 * Валидатор Формы регистрации. (Шаг 1)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Validator/RegisterStep1Validator.php
 */
class RegisterStep1Validator implements InputFilterAwareInterface
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
             * Имя или Никнейм
             */

            $inputFilter->add($factory->createInput([  // Поле ввода Имени
                'name'          =>  'name', // имя проверяемого поля, созданного в Form
                'required'      =>  true,   // обязательное
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую поле
                    array('name'    => 'StripTags'), // удаляю теги
                ),
                'validators' => array(
                    array(  // проверка на пустоту
                        'name'                  => 'NotEmpty', // валидатор
                        'options'   => array(
                            'encoding' => 'UTF-8',
                            'messages' => array(
                                'isEmpty' => $this->translator->translate("You left the field blank to enter your nickname future", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                    array( // валидация на длину поля
                        'name'                  => 'StringLength', // валидатор
                        'options'   => array(
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 125,
                            'messages' => array(
                                'stringLengthTooShort'  => $this->translator->translate("Your Nickname '%value%' is less than %min% characters long", 'errors'),
                                'stringLengthTooLong'   => $this->translator->translate("Your Nickname '%value%' is more than %max% characters long", 'errors'),
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                ),
            ]));

            /**
             * Половая принадлежность
             */
            $inputFilter->add($factory->createInput([
                'name'     => 'gender',
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую поле
                    array('name'    => 'StripTags'), // удаляю теги
                ),
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                            'haystack'  => array('1','2'),
                            'messages'  => array(
                                'notInArray'    =>  $this->translator->translate("Please choose your gender", 'errors'), // сообщение об ошибке'Please select your gender'
                            ),
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                ),
            ]));

            /**
             * Дата рождения
             */
            $inputFilter->add($factory->createInput([
                'name'     => 'birthday',
                'allow_empty' =>  true, // поддержка пустого значения
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую поле
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options'   => array(
                            'pattern'       => '/^(\d){4}-|\.(\d){2}-|\.(\d){2}$/',
                            'messages' => array(
                                'regexNotMatch' => $this->translator->translate("Date of birthday format is wrong. Try like this 1995-08-20", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                ),
            ]));

            /**
             * Список со странами
             */
            $inputFilter->add($factory->createInput([
                'name'     => 'country',
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую поле
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options'   => array(
                            'pattern'  => '/^(\d+)$/',
                            'messages' => array(
                                'regexNotMatch' => $this->translator->translate("Please choose your country", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                    array( // проверка на существование а базе
                        'name'                  => 'Db\RecordExists', // валидатор
                        'options'   => array(
                            'table'     => 'zf_countries', // таблица, где проверяю запись
                            'field'     => 'country_id', // имя поля
                            'messages' => array(
                                'noRecordFound' => $this->translator->translate("Country not found. Do not attempt to crack registration form", 'errors'), // сообщение об ошибке
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
