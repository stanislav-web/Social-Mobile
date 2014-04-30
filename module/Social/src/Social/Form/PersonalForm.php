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
 * @filesource /module/Social/src/Form/PersonalForm.php
 */
class PersonalForm extends Form
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
        parent::__construct('personal'); // имя формы
        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле TEXTAREA
            'name'  =>  'personal',
            'type'  =>  'Zend\Form\Element\Textarea',
            'attributes' => array(
                'id'                => 'personal',
                'placeholder'       => _('Whats Up?'),
                'autofocus'         => 'true',
                'data-mini'         => 'true',
                'maxlength'         => '255',
                'cols'              => '40',
                'rows'              => '8',
            ),
        ));

        $this->add(array( // фажок "Расшарить на стену"
            'type'  =>  'Zend\Form\Element\Checkbox',
            'name'  =>  'share',
            'attributes'    =>  array(
                'type'      => 'checkbox',
                'id'        => 'share',
                'checked'   => false,
                'data-mini' =>  'true',
            ),
            'options'   => array(
                    'label' =>  _('Post on a wall'),
                    'checked_value' => '1',
                    'unchecked_value' => '',
                    'use_hidden_element' => false
                )
            )
        );

        $this->add(array( // кнопка submit
            'name' => 'submit',
            'type'  =>  'Zend\Form\Element\Submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'twitter',
                'value'         =>  _('Share It!'),
                'data-mini'     =>  'true',
            ),
        ));
    }
}