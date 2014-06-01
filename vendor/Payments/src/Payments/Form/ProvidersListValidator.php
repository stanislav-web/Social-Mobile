<?php
namespace Payments\Form;

// Using validate intarfaces
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator;
/**
 * Payments form validator
 * @package Zend Framework 2
 * @subpackage Payments
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Form/ProvidersListValidator.php
 */
class ProvidersListValidator implements InputFilterAwareInterface
{

    /**
     * $_inputFilter Form configuration
     * @access protected
     * @var  object
     */
    protected $_inputFilter;

    /**
     * $_config Form configuration
     * @access protected
     * @var  array
     */
    protected $_config;

    /**
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    /**
     * __construct($config) Set config array
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_config    = $config;
    }

    /**
     * getConfig() get config array
     * @access public
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Фильтр формы
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if(!$this->_inputFilter)
        {
            $inputFilter    = new InputFilter();
            $factory        = new InputFactory();

            // select list validator
            
            $inputFilter->add($factory->createInput([
                'name'     => 'id',
                'filters'       => [
                    ['name'    => 'StringTrim'],
                ],
                'validators' => [

                    [
                        'name'    => 'NotEmpty',
                        'options' => [
                            'messages'  => [
                                \Zend\Validator\NotEmpty::IS_EMPTY =>  "Please select the payment method",
                            ],
                        ],
                        'break_chain_on_failure' => true,
                    ],                    
                    
                    [
                        'name'    => 'InArray',
                        'options' => [
                            'haystack'  => $this->getConfig(),  // add value for list
                            'messages'  => [
                                \Zend\Validator\InArray::NOT_IN_ARRAY    =>  "Wrong select value",
                            ],
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
            ]));
           
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
