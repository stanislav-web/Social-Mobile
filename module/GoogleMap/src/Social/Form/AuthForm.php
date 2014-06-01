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
 * @filesource /module/Social/src/Form/AuthForm.php
 */
class AuthForm extends Form
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

        parent::__construct('auth'); // имя формы

        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );
        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле email
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
                'title'             =>  $translator->translate('Login field is required and must contain your real mobile or email!'),
            ),
            'options'   => array(
                    'label' =>  _('E-mail or mobile'),
                )
        ));

        $this->add(array( // идентификатор авторизации
            'type'  =>  'Zend\Form\Element\Hidden',
            'name'  =>  'type',
            'attributes' => array(
                'value'     =>  'auth',
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
                'title'             =>  $translator->translate('We need your password access :)'),
            ),
            'options'   => array(
                    'label' =>  _('Password'),
                    )
            )
        );

        $this->add(array( // фажок соглашения запоминанием
            'type'  =>  'Zend\Form\Element\Checkbox',
            'name'  =>  'remember',
            'attributes'    =>  array(
                'type'      => 'checkbox',
                'id'        => 'remember',
                'checked'   => true,
                'data-mini' => 'true',
            ),
            'options'   => array(
                    'label' =>  _('Do remember my login?'),
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                    'use_hidden_element' => false
                )
            )
        );

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'data-mini'     =>  'true',
                'value'         =>  _('Login'),
                ),
            )
        );
    }
}