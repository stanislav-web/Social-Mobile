<?php
namespace SW\String; // пространство имен для моей библиотеки
/**
 * Класс форматирования различного вида строк
 * @package Zend Framework 2
 * @subpackage SWEB Library
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
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
}
?>
