<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;

/**
 * Форма авторизации
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/SetPasswordForm.php
 */
class SetPasswordForm extends Form
{

    /**
     * Конструктор формы Регистрации
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($translator = null)
    {
        /*
         *  Создаю форму Аворизации
         */

        parent::__construct('setpassword'); // имя формы

        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );
        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле email (login)
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'login',
            'attributes' => array(
                'id'                =>  'login',
                'required'          =>  true,
                'data-clear-btn'    =>  'true',
                'data-storage'      =>  'authLogin',
                'placeholder'       =>  _('Your email or a mobile number'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Login that you used when registering!'),
            ),
            'options'   => array(
                    'label' =>  _('E-mail or mobile'),
                )
        ));

        $this->add(array( // идентификатор смены пароля
            'type'  =>  'Zend\Form\Element\Hidden',
            'name'  =>  'restore',
            'attributes' => array(
                'value'     =>  'true',
                'required'          =>  true,
            ),
            'options'   => array(
                    'label' => true,
                )
            )
        );

        $this->add(array( // Поле пароль
            'type'  =>  'Zend\Form\Element\Password',
            'name'  =>  'password',
            'attributes' => array(
                'id'                =>  'password',
                'data-clear-btn'    =>  'true',
                'required'          =>  true,
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Your new password for access'),
            ),
            'options'   => array(
                    'label' =>  _('New password'),
                    )
            )
        );

        $this->add(array( // Код со строки
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'restoreCode',
            'attributes' => array(
                'id'                =>  'restoreCode',
                'required'          =>  true,
                'data-clear-btn'    =>  'true',
                'data-storage'      =>  'restoreCode',
                'placeholder'       =>  _('Your recovery key'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('This field is required and process the recovery you access!'),
            ),
            'options'   => array(
                    'label' =>  _('Your recovery key'),
                )
        ));

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'data-mini'     =>  'true',
                'value'         =>  _('Restore'),
                ),
            )
        );
    }
}