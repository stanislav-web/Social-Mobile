<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;

/**
 * Форма продолжения регистрации (шаг 1)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/RegisterStep1Form.php
 */
class RegisterStep1Form extends Form
{

    /**
     * Конструктор формы и выборка страны
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($selectStorage, $translator = null)
    {
        /*
         *  Создаю форму продолжения регистрации
         */
        $countries = $selectStorage->getCountriesToSelect(); // достаю страны из БД
        //print_r($countries);
        parent::__construct('regstep1'); // имя формы

        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле ввода имени
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'name',
            'attributes' => array(
                'id'                =>  'name',
                'required'          =>  'true',
                'data-clear-btn'    =>  'true',
                'data-storage'      =>  'profileName',
                'placeholder'       =>  _('You can specify an actual name or nickname'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('This field is required'),
            ),
            'options'   => array(
                    'label' =>  _('Your display name'),
                    )
                )
        );

        $this->add(array( // Список с выбором половой принадлежности
            'type'  =>  'Zend\Form\Element\Select',
            'name'  =>  'gender',
            'attributes' => array(
                'id'                =>  'gender',
                'data-mini'         =>  'true',
                'value'             =>  '0', //set selected to '0',
                'required'          =>  false,
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Choose is required'),
            ),
            'options'   => array(
                    'label' =>  _('&nbsp;'),
                    'value_options' => array(
                        '0'  => _('Choose gender'),
                        '1' => _('Male'),
                        '2' => _('Female')
                        ),
                    ),
                )
        );

        $this->add(array( // Поле с выбором даты рождения
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'birthday',
            'attributes' => array(
                'id'                =>  'birthday',
                'data-storage'      =>  'profileBirthday',
                'placeholder'       =>  _('Leave empty if you will not to show your age'),
                'data-mini'         =>  'true',
                'required'          =>  false,
                'readonly'          =>  false,
                'allow_empty'       =>  true,
                'data-tooltip'      =>  'true',
                'data-role'         =>  'datebox',
                'data-options'  =>  '{"mode": "datebox", "overrideDateFormat" : "%Y-%m-%d", "useNewStyle" : true, "themeButton" : "a", "themeInput" : "b", "themeHeader": "b"}',
                //, "method" : "set", "value" : "2012-01-14", "date" : new Date(\'2012-01-14\')}
                'title'             =>  $translator->translate('Please setup your realy date of birthday. Course if you wish to be founded in a Search'),
            ),
            'options'   => array(
                    'label' =>  _('Date of birthday (year-month-day)'),
                    )
                )
        );

        $this->add(array( // Список с выбором страны
            'type'  =>  'Zend\Form\Element\Select',
            'name'  =>  'country',
            'attributes' => array(
                'id'                =>  'country',
                'data-mini'         =>  'true',
                'value'             =>  '0', //set selected to '0'
                'options'           => $countries,
                'required'          =>  true,
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Setup your realy country. This is will easy way to found you here'),
            ),
            'options'   => array(
                    'label' =>  _('&nbsp;'),
                    ),
                )
        );

        $this->add(array( // Поле загрузки фотографии
            'type'  =>  'Zend\Form\Element\File',
            'name'  =>  'filename',
            'attributes' => array(
                'id'                =>  'filename',
                'data-clear-btn'    =>  'true',
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('You can get your profile avatar'),
            ),
            'options'   => array(
                    'label' =>  _('Your profile picture'),
                    )
                )
        );

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'data-mini'     => 'true',
                'value'         =>  _('Next'),
                ),
            )
        );
    }
}