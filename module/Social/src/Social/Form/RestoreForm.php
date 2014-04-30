<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;

/**
 * Форма восстановления аккаунта
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/RestoreForm.php
 */
class RestoreForm extends Form
{
    /**
     * Конструктор формы Восстановления аккаунта
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($translator = null)
    {
        /*
         *  Создаю форму Search
         */
        parent::__construct('restore'); // имя формы
        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле ввода емаил или номера телфеона
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'resign',
            'attributes' => array(
                'id'                =>  'resign',
                'required'          =>  true,
                'data-clear-btn'    =>  'true',
                'data-storage'      =>  'restoreLogin',
                'placeholder'       =>  _('Your email or a mobile number'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('After the submit you shall be read SMS or email box then activate a new access'),
            ),
            'options'   => array(
                    'label' =>  _('E-mail or mobile'),
                    )
                )
        );

        $this->add(array( // идентификатор восстановления аккаунта
            'type'  =>  'Zend\Form\Element\Hidden',
            'name'  =>  'type',
            'attributes' => array(
                'value'     =>  'restore',
            ),
            'options'   => array(
                    'label' => true,
                )
            )
        );

        $this->add(array( // системное поле защиты от спама
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
            'options'   =>  array(
                'csrf_options' => array(
                        'timeout' => 360 // время истечения токена
                    )
                )
        ));

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'value'         =>  _('Reset'),
                'data-mini'     =>  'true',
            ),
        ));
    }
}