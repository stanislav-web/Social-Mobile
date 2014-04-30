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
 * Валидатор формы регистрации. Шаг 3
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Validator/RegisterStep3Validator.php
 */
class RegisterStep3Validator implements InputFilterAwareInterface
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

            $inputFilter->add($factory->createInput([ // Список с регионами
                'name'     => 'city',
                'filters'       => array(
                    array('name'    => 'StringTrim'), // тримирую поле
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options'   => array(
                            'pattern'  => '/^(\d+)$/',
                            'messages' => array(
                                'regexNotMatch' => $this->translator->translate("Please choose your location", 'errors'), // сообщение об ошибке
                            )
                        ),
                        'break_chain_on_failure' => true, // прерывать при ошибке дальнейшую обработку
                    ),
                    array( // проверка на существование а базе
                        'name'                  => 'Db\RecordExists', // валидатор
                        'options'   => array(
                            'table'     => 'zf_cities', // таблица, где проверяю запись
                            'field'     => 'city_id', // имя поля
                            'messages' => array(
                                'noRecordFound' => $this->translator->translate("City not found. Do not attempt to crack registration form", 'errors'), // сообщение об ошибке
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
