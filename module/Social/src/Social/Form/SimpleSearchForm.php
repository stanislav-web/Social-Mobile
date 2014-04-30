<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;
/**
 * Простая форма поиска на главной странице
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/SimpleSearchForm.php
 */
class SimpleSearchForm extends Form
{
    /**
     * Конструктор формы Поиска Simple
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($name = null)
    {
        /*
         *  Создаю форму Search
         */
        parent::__construct('search'); // имя формы
        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле поиска
            'name'  =>  'search',
            'type'  =>  'Zend\Form\Element\Search',
            'attributes' => array(
                'id'                => 'search',
                'data-clear-btn'    => 'true',
                'placeholder'       => _('Look for...'),
                'autofocus'         => 'true',
                'data-mini'         =>  'true',
            ),
        ));

        $this->add(array( // выбор online
            'type'  =>  'Zend\Form\Element\Checkbox',
            'name'  =>  'online',
            'attributes'    =>  array(
                'type'      => 'checkbox',
                'id'        => 'online',
                'checked'   => false,
                'data-mini' =>  'true',
            ),
            'options'   => array(
                    'label' =>  _('Online'),
                    'checked_value' => '1',
                    'unchecked_value' => '0'
                )
            )
        )->setOptions(array(
            'use_hidden_element' => false
            )
        );

        $this->add(array( // выбор с фото
            'type'  =>  'Zend\Form\Element\Checkbox',
            'name'  =>  'photo',
            'attributes'    =>  array(
                'type'      => 'checkbox',
                'id'        => 'photo',
                'checked'   => false,
                'data-mini' =>  'true',
            ),
            'options'   => array(
                    'label' =>  _('Photos'),
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                )
            )
        )->setOptions(array(
            'use_hidden_element' => false
            )
        );

        $this->add(array( // список с выбором пола
            'type'  =>  'Zend\Form\Element\Select',
            'name'  =>  'gender',
            'attributes' => array(
                'id'        => 'gender',
                'data-mini' =>  'true',
                'value'     => '' // выбранный по умолчанию
            ),
            'options'   => array(
                'value_options' => array(
                    ''  => _('Choose gender'),
                    'f' => _('Female'),
                    'm' => _('Male')
                ),
            )
        ));

        $this->add(array( // кнопка submit
            'name' => 'submit',
            'type'  =>  'Zend\Form\Element\Submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'search',
                'value'         =>  _('Search'),
                'data-mini'     =>  'true',
            ),
        ));
    }
}