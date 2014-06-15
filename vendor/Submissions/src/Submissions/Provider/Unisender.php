<?php
namespace Submissions\Provider;

use Submissions\Exception;
use Submissions\Aware;

// Here should implemented only those libraries that are needed for this provider
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;

/**
 * Unisender Provider. Powered by Protocol interaction with providers mail service Unisender.
 * @package Zend Framework 2
 * @subpackage Submissions
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Submissions/src/Submissions/Provider/Unisender.php
 */
class Unisender extends \ReflectionClass implements Aware\ProviderInterface {
    
    /**
     * $_sm Service Manager
     * @access private
     * @var object 
     */      
    private $_sm    =   null;
   
    /**
     * $__config Configuration for this provider (Cannot modify outside)
     * @see getConfig()
     * @access private
     * @var array $config
     */
    private $__config  =   null; 
    
    /**
     * $__adapterConfig Adapter connection settings
     * @see getAdapterConfig()
     * @access private
     * @var array $config
     */
    private $__adapterConfig  =   null;
    
    /**
     * __construct(\Zend\ServiceManager\ServiceManager $ServiceManager) 
     * @param \Zend\ServiceManager\ServiceManager $ServiceManager
     * @return instance of ServiceManager, Provider Configuration
     * 
     */
    public function __construct(\Zend\ServiceManager\ServiceManager $ServiceManager) 
    {
        parent::__construct(__CLASS__);
        
        // set ServiceManager throught constructor
        if(null === $this->_sm)  $this->_sm =   $ServiceManager;
        $this->__config   =   $this->_sm->get('Config')["Submissions\Provider\Config"][$this->getShortName()];
    }        
  
    /**
     * createList($title) Creates list with specified title
     * @param string $title mail list name
     * @see http://www.unisender.com/ru/help/api/createList/
     * @acces public
     * @return array
     */    
    public function createList($title)
    {
        return $this->sendRequest('createList', ['title' => $title]);
    }  
    
    /**
     * activateContacts($list_ids) Activates one or few lists by ids
     * @param mixed $list_ids mail list id(s)
     * @param string $contact_type [email|phone]
     * @see http://www.unisender.com/ru/help/api/activateContacts/
     * @acces public
     * @return array
     */    
    public function activateContacts($list_ids, $contact_type = 'email')
    {
	if(is_array($list_ids)) $list_ids = implode(',', $list_ids);
        return $this->sendRequest('activateContacts', ['list_ids' => $list_ids, 'contact_type' => $contact_type]);
    }     
    
    /**
     * getDefaultListID() Returns a default list. If it doesn't exist, create it.
     * @acces public
     * @return array
     */     
    public function getDefaultListID()
    {
	$lists = $this->getMailingLists();
	foreach($lists as $list)
	{
            if($list['title'] == $this->__config['default_list'])
            {
                return $list['id'];
            }
	}
        // Create if not exist
	return $this->createList($this->__config['default_list']);
    }    
    
    /**
     * getMailingLists() Obtains a list of existing mailing lists
     * @see http://www.unisender.com/ru/help/api/getLists/
     * @acces public
     * @return array
     */
    public function getMailingLists()
    {
        return $this->sendRequest('getLists');
    }    
    
    /**
     * getMailingInfo($campaign_id) Receive a report on the results of message delivery in a given delivery, grouped by type of result.
     * @param int $campaign_id mailing id
     * @see http://www.unisender.com/ru/help/api/getCampaignAggregateStats/
     * @acces public
     * @return array
     */    
    public function getMailingInfo($campaign_id)
    {
        return $this->sendRequest('getCampaignAggregateStats', ['campaign_id' => $campaign_id]);
    }
 
    /**
     * getMailingStatus($campaign_id) Check mailing status.
     * @param int $campaign_id Code distribution obtained by createCampaign.
     * @see http://www.unisender.com/ru/help/api/getCampaignStatus/
     * @acces public
     * @return array
     */  
    public function getMailingStatus($campaign_id)
    {
        return $this->sendRequest('getCampaignStatus', ['campaign_id' => $campaign_id]);
    }     

    /**
     * getListContacts($list_id, $fields = array('email')) Contacts from selected lists
     * @param string $list_id mail list id(s)
     * @param array $fields exporting fields
     * @acces public
     * @return array
     */    
    public function getListContacts($list_id, $fields = array('email'))
    {
	return $this->exportContacts($list_id, $fields)['data'];
    }    
    
    /**
     * getListFields() Information about user-defined fields
     * @see http://www.unisender.com/ru/help/api/getFields/
     * @acces public
     * @return array
     */
    public function getListFields()
    {
        return $this->sendRequest('getFields');
    }      

    /**
     * subscribe(array $field_lists, $list_ids) Subscribe to mailing list(s)
     * @param string $email subscriber email
     * @param string $list_ids id(s) of mailing lists ex. [112233,887789,6665576]
     * @param array $field_lists example: fields[email]=test@example.org&fields[Name]=UserName
     * @see http://www.unisender.com/ru/help/api/subscribe/
     * @acces public
     * @return array
     */
    public function subscribe(array $field_lists, $list_ids)
    {
        if(is_array($list_ids)) $list_ids = implode(',', $list_ids);

        return $this->sendRequest('subscribe', 
            [
                'fields'            =>  $field_lists,
                'list_ids'          =>  $list_ids,
                'double_optin'      =>  3,              // (0-3) see http://www.unisender.com/ru/help/api/subscribe/
                'overwrite'         =>  2               // (0-2) see http://www.unisender.com/ru/help/api/subscribe/
            ]);
    }  
    
    /**
     * unsubscribe($list_ids, $contact, $contact_type = 'email')
     * @param string $contact mail or phone number
     * @param string $list_ids id(s) of mailing lists ex. [112233,887789,6665576]
     * @param string $contact_type [email|phone]
     * @see http://www.unisender.com/ru/help/api/unsubscribe/
     * @acces public
     * @return array if contains error, empty if success
     */
    public function unsubscribe($contact, $list_ids, $contact_type = 'email')
    {
        if(is_array($list_ids)) $list_ids = implode(',', $list_ids);
        
        return $this->sendRequest('unsubscribe', 
            [
                // see http://www.unisender.com/ru/help/api/subscribe/
                'list_ids'          =>  $list_ids,
                'contact'           =>  $contact,               
                'contact_type'      =>  $contact_type,  // phone | email              
            ]);
    }    
    
    /**
     * exclude($contact, $list_ids, $contact_type = 'email') excludes e-mail or telephone subscriber of one or more lists.
     * @param string $contact mail or phone number
     * @param string $list_ids id(s) of mailing lists ex. [112233,887789,6665576]
     * @param string $contact_type [email|phone]
     * @see http://www.unisender.com/ru/help/api/exclude/
     * @acces public
     * @return array if contains error, empty if success
     */
    public function exclude($contact, $list_ids, $contact_type = 'email')
    {
        if(is_array($list_ids)) $list_ids = implode(',', $list_ids);
        
        return $this->sendRequest('exclude', 
            [
                // see http://www.unisender.com/ru/help/api/exclude/
                'list_ids'          =>  $list_ids,
                'contact'           =>  $contact,               
                'contact_type'      =>  $contact_type,  // phone | email              
            ]);
    } 
    
    /**
     * importContacts(array $contacts, array $field_names, $list_ids) Method bulk import subscribers
     * @param array $contacts imported array contacts
     * @param array $fields required fields
     * @param [string|array] $list_ids mail list
     * @see http://www.unisender.com/ru/help/api/importContacts/
     * @acces public
     * @return array
     */
    public function importContacts(array $contacts, array $fields, $list_ids)
    {
        if(is_array($list_ids)) $list_ids = implode(',', $list_ids);

	$result = [
            'total'         => 0,
            'inserted'      => 0,
            'updated'       => 0,
            'deleted'       => 0,
            'new_emails'    => 0,
            'invalid'       => 0,
            'log'           => []                   
        ];

	$emails_per_iteration = 500;

	$all = sizeof($contacts);
	$iterations = $all / $emails_per_iteration;
	for($i = 0; $i < $iterations; $i++)
	{
            $from = $i * $emails_per_iteration;
            $to   = $from + $emails_per_iteration;

            if($to > $all) $to = $all;
            $data = [
                'field_names'  => $fields,
                'data'         => array_slice($contacts, $from, $to - $from),
                'double_optin' => 1,    // whether the recipient has agreed to subscribe for and confirmed whether your email-address. Valid for attempts to import addresses with status 'active'                
            ];
            $res = $this->sendRequest('importContacts', $data); 
	}
        
	// Get new email addresses
	$emails_to_activate = $this->exportContacts($list_ids, ['email'], 'new');

	// Activate list if needed
	if(sizeof($emails_to_activate['data']) > 0) $this->activateContacts($list_ids);
	return $res;
    }     
    
    /**
     * exportContacts($list_ids = null, $field_names = array('email'), $email_status = NULL, $phone_status = NULL) export data from subscribers UniSender. Different scenarios of using this method.
     * @param string $list_ids id(s) of mailing lists ex. [112233,887789,6665576]
     * @param array $field_names exporting fields
     * @param string $email_status status email
     * @param string $phone_status status phone
     * @see http://www.unisender.com/ru/help/api/exportContacts/
     * @acces public
     * @return array
     */    
    public function exportContacts($list_ids = null, $fields = array('email'), $email_status = NULL, $phone_status = NULL)
    {
        // Status of addresses. Possible field values ​​when exporting:
        
	$statuses = [
            'email' =>  [
                'new',
                'invited',
                'active',
                'inactive',
                'unsubscribed',
                'blocked',
                'activation_requested'                
            ],
            'phone' =>  [
                'new',
                'active',
                'inactive',
                'unsubscribed',
                'blocked',
            ]
        ];
        
        // Check statuses and send request...
        
        if(is_array($list_ids)) $list_ids = implode(',', $list_ids);
        
	if($email_status !== NULL && !in_array($email_status, $statuses['email'])) throw new Exception\ExceptionStrategy($this->getShortName().' undefined status: '.$email_status);
	if($phone_status !== NULL && !in_array($phone_status, $statuses['phone'])) throw new Exception\ExceptionStrategy($this->getShortName().' undefined status: '.$phone_status);
        
        $parameters = ($list_ids != null) ? ['list_id' => $list_ids, 'field_names' => $fields] : ['field_names' => $fields];

        if($email_status !== NULL) $parameters['email_status'] = $email_status;
        if($phone_status !== NULL) $parameters['phone_status'] = $phone_status;
        
        return $this->sendRequest('exportContacts', $parameters);
    }    

    /**
     * createEmailMessage($campaign_id) Creating e-mail messages without sending. Shipping directly by using another method - sendMessage
     * @param   string  $sender_name sender name
     * @param   string  $sender_email sender email
     * @param   string  $subject subject
     * @param   string  $body body HTML
     * @param   int  $list_id subscriber's list
     * @param   string  $text simple text
     * @param   array  $attachments example: attachments[quotes.txt]=text%20file%content
     * @see http://www.unisender.com/ru/help/api/createEmailMessage
     * @acces public
     * @return array
     */     
    public function createEmailMessage($sender_name, $sender_email, $subject, $body, $list_id, $text = null, $attachments = array())
    {
	$parameters = [
            'list_id'       => (int)$list_id,
            'sender_name'   =>  $sender_name,
            'sender_email'  =>  $sender_email,
            'subject'       =>  $subject,
            'body'          =>  $body,            
        ];
        
	if($text !== null) $parameters = array_merge($parameters, ['text_body' => $text, 'generate_text' => 1]);
	if(!empty($attachments)) array_merge($parameters, ['attachments' => $attachments]);

        return $this->sendRequest('createEmailMessage', $parameters);        
    }    

    /**
     * deleteMessage($message_id) delete message
     * @param int $message_id message ID
     * @see http://www.unisender.com/ru/help/api/deleteMessage/
     * @acces public
     * @return array if contains error, empty if success
     */    
    public function deleteMessage($message_id)
    {
        return $this->sendRequest('deleteMessage', ['message_id'  => (int)$message_id]);        
    } 
    
    /**
     * sendMessage($campaign_id) Planned mass sending e-mail or SMS messages
     * @param int $message_id Message code (can be accessed by creating a message)
     * @see http://www.unisender.com/ru/help/api/createCampaign/
     * @acces public
     * @return array
     */     
    public function sendMessage($message_id)
    {
        return $this->sendRequest('createCampaign', [
            'message_id'  => (int)$message_id,
            'track_read'  => !empty($this->__config['track_read']),
            'track_links' => !empty($this->__config['track_links'])            
        ]);
    }    

    /**
     * forceSendMessage($sender_name, $sender_email, $subject, $body, $list_id, $text = null, $attachments = array()) Create and send message
     * @param   string  $sender_name sender name
     * @param   string  $sender_email sender email
     * @param   string  $subject subject
     * @param   string  $body body HTML
     * @param   int  $list_id subscriber's list
     * @param   string  $text simple text
     * @param   array  $attachments example: attachments[quotes.txt]=text%20file%content
     * @acces public
     * @return array
     */  
    public function forceSendMessage($sender_name, $sender_email, $subject, $body, $list_id, $text = null, $attachments = array())
    {
        $result =  $this->createEmailMessage($sender_name, $sender_email, $subject, $body, $list_id, $text = null, $attachments = array());
        return $this->sendMessage($result['message_id']);
    }      
    
    /**
     * getConfig() Get provider configurations
     * @access public
     * @see Aware\ProviderInterface
     * @return array
     * @throws Exception\ExceptionStrategy
     */
    public function getConfig()
    {
        if(empty($this->__config))   throw new Exception\ExceptionStrategy($this->getShortName().' config file is empty');
        return $this->__config;
    }
    
    /**
     * getAdapterConfig() Get adapter configuration by default
     * @access public
     * @see Aware\ProviderInterface
     * @return array
     */
    public function getAdapterConfig()
    {
        if(empty($this->__adapterConfig))   throw new Exception\ExceptionStrategy($this->getShortName().' connection adapter is not configured');
        return $this->__adapterConfig;        
    }
    
    /**
     * __setAdapterConfig() Set adapter additional configuration
     * @param array $adapterConfig  additional configurations
     * @access private
     * @return array
     */
    private function __setAdapterConfig(array $adapterConfig)
    {
        $this->__adapterConfig  =   $adapterConfig+$this->__config['adapter'];
    }
    
    /**
     * sendRequest($uri, array $data) Send request and get response from internal server
     * @param string $uri request URI
     * @param array $data request data
     * @see Aware\ProviderInterface
     * @return json data
     */
    public function sendRequest($uri = null, array $data = null)
    {
        // Compile URL from pattern
	$url = strtr($this->__config['api_url_pattern'].'?q=WWW', [
		':lang' => $this->__config['lang'],
		':uri'  => $uri            
            ]
        );
        
	// apply API key to request data
	$data['api_key'] = $this->__config['api_key'];

        // set Client adapter config
        $this->__setAdapterConfig([]);
        
        // Do the request to server with POST data
        $adapter = new Curl();
        $adapter->setOptions([
        'curloptions' => $this->getAdapterConfig()
        ]);
        
        $client = new Client($url);
        $client->setAdapter($adapter);
        $client->setMethod('POST');
        $client->setParameterPost($data);
        $response = $client->send($client->getRequest());   
        
        if($response->getStatusCode() != 200) 
            throw new Exception\ExceptionStrategy($this->getShortName().' connection error. Code: '.$response->getStatusCode());

        // Parse and return result
	$json = json_decode($response->getContent(), TRUE);
	if(!empty($json['error']))
	{
            $error = iconv(mb_detect_encoding($json['error']), 'UTF-8//TRANSLIT', $json['error']);
            $json['result']['error'] =  $json['code'].': '.$json['error'];
	}
	// Return result
	return $json['result'];        
    }
}
