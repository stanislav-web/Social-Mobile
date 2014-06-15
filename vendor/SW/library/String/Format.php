<?php
namespace SW\String; // пространство имен для моей библиотеки
/**
 * Класс форматирования различного вида строк
 * @package Zend Framework 2
 * @subpackage SWEB Library
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /vendor/SW/library/String/Format.php
 */
class Format  {

    /**
     * declareRight($number, $array) Метод спряжения окончаний числительных
     * @param type $number номер 0-9
     * @param array $array массив со спряжениями окончаний array('день', 'дня', 'дней')
     * @return string результат преобразования
     */
    public static function declareRight($number, $array)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $number." ".$array[($number%100>4&&$number%100<20) ? 2 : $cases[min($number%10, 5)]];
    }

    /**
     * getTimeString($timestamp, $arrayOfEnds) Метод определения разницы времени из timestamp формата
     * @param type  $timestamp time()
     * @param array $arrayOfEnds массив с окончаниями (0, 1, 2, 3)
     * @return string результат преобразования
     */
    public static function getTimeString($timestamp, $arrayOfEnds)
    {
        $days    = floor($timestamp/(60*60*24));
        $hours   = floor(($timestamp-($days*60*60*24))/(60*60));
        $min     = floor(($timestamp-($days*60*60*24)-($hours*60*60))/60);
        print $min.' ';
        $r = null;
	$r .= (!empty($days))   ? self::declareRight($days,     array($arrayOfEnds[0][0], $arrayOfEnds[0][1], $arrayOfEnds[0][2]))  : '';
	$r .= (!empty($hours))  ? self::declareRight($hours,    array($arrayOfEnds[1][0], $arrayOfEnds[1][1], $arrayOfEnds[1][2]))        : '';
	$r = self::declareRight($min,      array($arrayOfEnds[2][0], $arrayOfEnds[2][1], $arrayOfEnds[2][2]));
        return $r;
    }
    
    /**
     * getRealtimeFromSeconds($seconds, $arrayOfEnds) преобразование секунд в читаемый формат
     * @param type  $seconds секунды
     * @param array $arrayOfEnds массив с окончаниями (0, 1, 2, 3)
     * @return string результат преобразования
     */
    public static function getRealtimeFromSeconds($seconds, $arrayOfEnds)
    {
        //$s  =   $seconds%60;
        $m  =   floor(($seconds%3600)/60);
        $h  =   floor(($seconds%86400)/3600);
        $D  =   floor(($seconds%2592000)/86400);
        $M  =   floor($seconds/2592000);
        $r  = null;
	$r .= (!empty($M))   ? self::declareRight($M,     array($arrayOfEnds[0][0], $arrayOfEnds[0][1], $arrayOfEnds[0][2])).' '  : '';
	$r .= (!empty($D))   ? self::declareRight($D,     array($arrayOfEnds[1][0], $arrayOfEnds[1][1], $arrayOfEnds[1][2])).' '  : '';
	$r .= (!empty($h))   ? self::declareRight($h,     array($arrayOfEnds[2][0], $arrayOfEnds[2][1], $arrayOfEnds[2][2])).' '  : '';
	$r .= (!empty($m))   ? self::declareRight($m,     array($arrayOfEnds[3][0], $arrayOfEnds[3][1], $arrayOfEnds[3][2])).' '  : '';
        return $r;
    }
    
    /**
     * getFormatTime($timestamp, $translator) Метод определения разницы времени из timestamp формата
     * @param datetime  $datetime MySQL datetime
     * @param string $tz временная зона
     * @return array результат преобразования
     */
    public static function getFormatTime($datetime, $tz = null)
    {    
        // Получаю дату на анализ

        $DT     = new \DateTime($datetime);
        
        // Устанавливаю временную зону
        if(!$tz)    $TZ = $DT->getTimezone();
        else        $TZ = new \DateTimeZone($tz);  
                    $DT = $DT->setTimezone($TZ);
        // Получаю время
        return array(
            'y' => $DT->format('Y'),
            'm' => 'of '.$DT->format('F'),
            'd' => $DT->format('j'),
            'h' => $DT->format('H'),
            'i' => $DT->format('i'),
        );
    }
    
    /**
     * timestampToDatetime($timestamp) Форматирование из timesatmp в datetime
     * @param timestamp  $timestamp время с эпохи UNIX
     * @return string результат преобразования
     */
    public static function timestampToDatetime($timestamp)
    {    
        $DT     = new \DateTime();
        $DT->setTimestamp($timestamp);
        return $DT->format('Y-m-d H:i:s');
    }
    
    /**
     * datetimeToTimestamp($timestamp) Форматирование из datetime в timesatmp 
     * @param datetime  $datetime время с эпохи UNIX
     * @return int результат преобразования
     */
    public static function datetimeToTimestamp($datetime)
    {    
        $DT     = new \DateTime($datetime);
        return $DT->getTimestamp();
    }
    
    /**
     * getYears($datetime, $tz = null) Позраст (лет) между текущей датой , и указанной
     * @param datetime $datetime отформатированное время
     * @param string $tz временая зона
     * @param string $delimiter разделитель
     * @param object $translate разделитель
     * @return int
     */
    public function getYears($datetime, $tz = null, $delimiter = '', $translate = '')
    {   
        $DT     = new \DateTime();
        if(!$tz)    $TZ = $DT->getTimezone();
        else        $TZ = new \DateTimeZone($tz);  
                    $DT = $DT->setTimezone($TZ);
        $NOW    = new \DateTime('now', $TZ);
        $age = $DT->createFromFormat('Y-m-d', $datetime, $TZ)
            ->diff($NOW)
            ->y;
        if($age != $NOW->format('Y')) return $delimiter.$age.$translate;
    }
    
    /**
     * isJson($string) проверка входящей строки на Json
     * @param mixed $string Строка для проверки
     * @access static
     * @return boolean
     */
    public static function isJson($string)
    {
        return ((is_string($string) && 
         (is_object(json_decode($string)) || 
            is_array(json_decode($string))))) ? true : false;        
    }
    
    /**
     * isSerialized($string) проверка входящей строки на Json
     * @param mixed $string Строка для проверки
     * @access static
     * @return boolean
     */
    public static function isSerialized($string)
    {
        $array = @unserialize($string);
        if($array === false && $string !== 'b:0;') return false;
        else return true;
    }    
    
    /**
     * getTimezones() Часовые пояса
     * @access static
     * @return boolean
     */    
    public static function getTimezones()
    {
        return [
            '-1'    =>  '(GMT - 1:00) Azores, islands of Cape Verde',
            '-2'    =>  '(GMT - 2:00) Mid-Atlantic time',
            '-3'    =>  '(GMT - 3:00) Brazil, Buenos Aires, Georgetown, Greenland',
            '-3.5'  =>  '(GMT - 3:30) Newfoundland',
            '-4'    =>  '(GMT - 4:00) Atlantic Time (Canada), Caracas, La Paz, Santiago',
            '-5'    =>  '(GMT - 5:00) Eastern Time (U.S. & Canada), Bogota, Lima, Quito',
            '-6'    =>  '(GMT - 6:00) Central Time (U.S. & Canada), Mexico',
            '-7'    =>  '(GMT - 7:00) Mountain Time (U.S. and Canada), Arizona',
            '-8'    =>  '(GMT - 8:00) Pacific Time (U.S. & Canada), Tijuana',
            '-9'    =>  '(GMT - 9:00) Alaska',
            '-10'   =>  '(GMT - 10:00) Hawaii',
            '-11'   =>  '(GMT - 11:00) o.Miduey, Samoa',
            '-12'   =>  '(GMT - 12:00) Enevetok, Kwajalein',
            '0'     =>  '(GMT) Casablanca, Dublin, Edinburgh, Lisbon, London, Monrovia',
            '1'     =>  '(GMT + 1:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome',
            '2'     =>  '(GMT + 2:00) Cairo, Helsinki, Kaliningrad, South Africa, Warsaw, Kyiv',
            '3'     =>  '(GMT + 3:00) Baghdad, Riyadh, Moscow, Nairobi',
            '3.5'   =>  '(GMT + 3:30) Tehran',
            '4'     =>  '(GMT + 4:00) Abu Dhabi, Baku, Muscat, Tbilisi',
            '4.5'   =>  '(GMT + 4:30) Kabul',
            '5'     =>  '(GMT + 5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
            '5.5'   =>  '(GMT + 5:30) Bombay, Calcutta, Madras, New Delhi',
            '6'     =>  '(GMT + 6:00) Alma-Ata, Colombo, Dhaka, Novosibirsk, Omsk',
            '7'     =>  '(GMT + 7:00) Bangkok, Hanoi, Jakarta, Krasnoyarsk',
            '8'     =>  '(GMT + 8:00) Beijing, Hong Kong, Perth, Singapore, Taipei',
            '9'     =>  '(GMT + 9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk',
            '9.5'   =>  '(GMT + 9:30) Adelaide, Darwin',
            '10'    =>  '(GMT + 10:00) Canberra, Melbourne, Guam, Sydney, Vladivostok',
            '11'    =>  '(GMT + 11:00) Magadan, New Caledonia, Solomon Islands',
            '12'    =>  '(GMT + 12:00) Auckland, Fiji, Kamchatka, Wellington',
            '13'    =>  '(GMT + 13:00) Kamchatka',
            '14'    =>  '(GMT + 14:00) Kiritimati (Christmas Island)',            
        ];
    }
}
?>
