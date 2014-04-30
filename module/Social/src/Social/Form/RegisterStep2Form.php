<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;

/**
 * Форма продолжения регистрации (шаг 2)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/RegisterStep2Form.php
 */
class RegisterStep2Form extends Form
{

    /**
     * Конструктор формы Выбора локации
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($selectStorage, $country_code, $translator = null)
    {
        /*
         *  Создаю форму продолжения регистрации
         */
        $regions = $selectStorage->getRegionsToSelect($country_code); // достаю регионы из БД

        parent::__construct('regstep2'); // имя формы

        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Список с выбором регионов
            'type'  =>  'Zend\Form\Element\Select',
            'name'  =>  'region',
            'attributes' => array(
                'id'                =>  'region',
                'data-mini'         =>  'true',
                'value'             =>  '0', //set selected to '0'
                'options'           =>  $regions,
                'required'          =>  true,
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Setup your realy region. This is will easy way to found you here'),
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
                'value'         =>  _('Next'),
                ),
            )
        );
    }
}