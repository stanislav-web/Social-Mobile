<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;

/**
 * Форма продолжения регистрации (шаг 3)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/RegisterStep3Form.php
 */
class RegisterStep3Form extends Form
{

    /**
     * Конструктор формы Выбора локации
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($selectStorage, $country_code, $region_code, $translator = null)
    {
        /*
         *  Создаю форму продолжения регистрации
         */
        $cities = $selectStorage->getCitiesToSelect($country_code, $region_code); // достаю регионы из БД
        parent::__construct('regstep3'); // имя формы

        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Список с выбором городов
            'type'  =>  'Zend\Form\Element\Select',
            'name'  =>  'city',
            'attributes' => array(
                'id'                =>  'city',
                'data-mini'         =>  'true',
                'value'             =>  '0', //set selected to '0'
                'options'           =>  $cities,
                'required'          =>  false,
                'data-tooltip'      =>  'true',
                'title'             => $translator->translate('Setup your realy city. This is will easy way to found you here'),
            ),
            'options'   => array(
                    'label' =>  _('&nbsp;'),
                    ),
                )
        );

        $this->add(array( // кнопка назад
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'back',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'back',
                'data-mini'     => 'true',
                'value'         =>  _('Back'),
                ),
            )
        );

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'data-mini'     => 'true',
                'value'         =>  _('Complete and Login automaticaly'),
                ),
            )
        );
    }
}