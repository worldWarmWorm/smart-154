<?php
/**
 * Hash helper
 * 
 * @version 1.0
 */
namespace common\components\helpers;

class HHash 
{
	/**
	 * @access private
	 * @var integer кол-во генераций слуйчайных чисел.
	 * Требуется для улучшения генерации случайных чисел 
	 * метода HHash::random(). 
	 */
	private static $mtRandSeedCount=0;
	
	/**
	 * @access private
	 * @var array массив сгенерированных хэшей.
	 * Требуется для улучшения генерации случайных чисел 
	 * метода HHash::random(). 
	 */
	private static $mtRandSeed=[];
	
	/**
	 * Генерирует хэш-строку
	 * 
	 * @param string $data строка для хеширования. Если передано значение NULL, 
	 * строка генерится случайным образом. По умолчанию NULL.
	 * @param number $length длина возвращаемой строки.  
	 * @param string $algo алгоритм хеширования. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * По умолчанию "sha1".
	 * @see \hash()
	 *  	 
	 * @return string сгенерированный хэш.
	 */
	public static function get($data=null, $length=0, $algo='sha1')
	{
		if($data === null) $data = self::random();
		
		return $length ? substr($algo($data), 0, $length) : $algo($data);
	}

	/**
	 * Generate random hash string
	 * @param number $length длина возвращаемого хэша. Если значение 
	 * не передано или 0(нуль), возвращется полностью вся строка 
	 * сгенерированного хэша. Default is 0(zero).
	 * @param string $algo алгоритм используемый при хешировании. Default is sha1. 
	 */
	public static function generate($length=0, $algo='sha1')
	{
		return self::get(null, $length, $algo);
	}
	
	/**
	 * Generate random string
	 * @return number
	 */
	public static function random()
	{
		self::$mtRandSeedCount++;
		
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		if(isset(self::$mtRandSeed[$seed])) $seed+=1000*self::$mtRandSeedCount;
		mt_srand($seed);
		
		self::$mtRandSeed[$seed]=true;
		
		return mt_rand();
	}
	
	/**
	 * Generate security hash
	 * @param mixed $data входные данные
	 * @param string $securityKey секретный ключ
	 * @param number $cost @see \CPasswordHelper::hashPassword()
	 * @return string
	 */
	public static function security($data, $securityKey=null, $cost=13)
	{
		if($securityKey) 
			$securityKey = self::get($securityKey);
		
		return \CPasswordHelper::hashPassword(self::get($data) . $securityKey, $cost);
	}
	
	/**
	 * Verify security hash
	 * @param mixed $data входные данные
	 * @param string $hash хеш-строка
	 * @param string $securityKey секретный ключ
	 * @param number $cost @see \CPasswordHelper::hashPassword()
	 * @return boolean
	 */
	public static function verifySecurity($data, $hash, $securityKey=null, $cost=13)
	{
		return \CPasswordHelper::verifyPassword(self::security($data, $securityKey, $cost), $hash);
	}
	
	/**
	 * Generate hash. 
	 * @param string $str хешируемая строка
	 * @param string $algo алгоритм хеширования. По умолчанию md5. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * @return string
	 */
	public static function hash($str, $algo='md5')
	{
		return $algo($str);
	}
	
	/**
	 * Verify md5 hash.
	 * @param string $str source string
	 * @param string $hash hash
	 * @param string $algo алгоритм хеширования. По умолчанию md5. 
	 * (например, "md5", "sha256", "haval160,4" и т.д.)
	 * @return boolean
	 */
	public static function verifyHash($str, $hash, $algo='md5')
	{
		return ($hash === self::hash($str, $algo));
	}
	
	/**
	 * Алгоритм crc32
	 * @see HHash::get()
	 */
	public static function crc32($str=null)
	{
		return self::get($str, 0, 'crc32');
	}
	
	/**
	 * Алгоритм md5
	 * @see HHash::get()
	 */
	public static function md5($str=null)
	{
		return self::get($str, 0, 'md5');
	} 
	
	/**
	 * Получить хэш модели.
	 * @param mixed $model объект модели или имя класса модели.
	 * @return string 
	 */
	public static function hashModel($model)
	{
		return self::md5(\CHtml::modelName($model));
	}
	
	/**
	 * Получить имя параметра по хэшу модели.
	 * хэш(md5) имени параметра должен совпадать с $hash.
	 * Для генерации значения хэша можно использовать метод HHash::hashModel().
	 * @param string $hash хэш имени параметра.
	 * @param string $isPost данные переданы методом POST. По умолчанию TRUE.
	 * @return string|NULL имя параметра, либо NULL если параметр не найден.
	 */
	public static function nameModel($hash, $isPost=true)
	{
		$get=function($array) use ($hash) {
			foreach($array as $name=>$value) {
				if(self::md5($name) == $hash)
					return $name;
			}
			return null;
		};
		
		return $isPost ? $get($_POST) : $get($_REQUEST);
	}
	
	/**
	 * Обратимое шифрование методом "Двойного квадрата" (Reversible crypting of "Double square" method)
	 * @see http://habrahabr.ru/post/61309/
	 * 
	 * @param  String $input   Строка с исходным текстом
	 * @param  bool   $decrypt Флаг для дешифрования
	 * @return String          Строка с результатом Шифрования|Дешифрования
	 * @author runcore
	 */
	function dsCrypt($input,$decrypt=false) {
		$o = $s1 = $s2 = array(); // Arrays for: Output, Square1, Square2
		// формируем базовый массив с набором символов
		$basea = array('?','(','@',';','$','#',"]","&",'*');  // base symbol set
		$basea = array_merge($basea, range('a','z'), range('A','Z'), range(0,9) );
		$basea = array_merge($basea, array('!',')','_','+','|','%','/','[','.',' ') );
		$dimension=9; // of squares
		for($i=0;$i<$dimension;$i++) { // create Squares
			for($j=0;$j<$dimension;$j++) {
				$s1[$i][$j] = $basea[$i*$dimension+$j];
				$s2[$i][$j] = str_rot13($basea[($dimension*$dimension-1) - ($i*$dimension+$j)]);
			}
		}
		unset($basea);
		$m = floor(strlen($input)/2)*2; // !strlen%2
		$symbl = $m==strlen($input) ? '':$input[strlen($input)-1]; // last symbol (unpaired)
		$al = array();
		// crypt/uncrypt pairs of symbols
		for ($ii=0; $ii<$m; $ii+=2) {
			$symb1 = $symbn1 = strval($input[$ii]);
			$symb2 = $symbn2 = strval($input[$ii+1]);
			$a1 = $a2 = array();
			for($i=0;$i<$dimension;$i++) { // search symbols in Squares
				for($j=0;$j<$dimension;$j++) {
					if ($decrypt) {
						if ($symb1===strval($s2[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s1[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s2[$i][$j])) $al=array($i,$j);
					}
					else {
						if ($symb1===strval($s1[$i][$j]) ) $a1=array($i,$j);
						if ($symb2===strval($s2[$i][$j]) ) $a2=array($i,$j);
						if (!empty($symbl) && $symbl===strval($s1[$i][$j])) $al=array($i,$j);
					}
				}
			}
			if (sizeof($a1) && sizeof($a2)) {
				$symbn1 = $decrypt ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
				$symbn2 = $decrypt ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
			}
			$o[] = $symbn1.$symbn2;
		}
		if (!empty($symbl) && sizeof($al)) // last symbol
			$o[] = $decrypt ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];
		
		return implode('',$o);
	}

	/**
	 * Unsigned crc32
	 */
	public static function ucrc32($str)
	{
		return sprintf('%u', crc32($str));
	}
	
	/**
	 * Получить уникальную строку.
	 * @param string $prefix префикс. По умолчанию пустая строка.
	 * @return string
	 */
	public static function u($prefix='')
	{
		return $prefix . self::random();
	}
	
	/**
	 * Получить уникальный идентификатор для скрипта js.
	 * @return string
	 */
	public static function ujs()
	{
		return self::u('js');
	}

	/**
	 * Простое обратимое шифрование. Шифрование.
	 * @see http://qaru.site/questions/728132/simple-encryption-in-php
	 * @param mixed $data данные для шифрования
	 * @param string $key ключ шифрования
	 * @return string
	 */
	public static function srEcrypt($data, $key=''){
	    $str=json_encode($data, JSON_UNESCAPED_UNICODE);
	    $result='';
	    $keylen=strlen($key);
	    $strlen=3*strlen($str);
	    for($i=0; $i<$strlen; $i++) {
	        if(!isset($str[$i])) break;
	        $char=$str[$i];
			$keypos=$keylen ? (($i % $keylen)-1) : 0;
	        $keychar=(($keypos > $keylen) || ($keypos < 0)) ? '@' : $key[$keypos];
	        $char=chr(ord($char)+ord($keychar));
	        $result.=$char;
	    }
	    return urlencode(base64_encode($result));
	}
	
	/**
	 * Простое обратимое шифрование. Разшифрование.
	 * @see http://qaru.site/questions/728132/simple-encryption-in-php
	 * @param string $str зашифрованная строка методом HHash::simpleEcrypt
	 * @param string $key ключ шифрования
	 * @param boolean $assoc true - возвращать ассоциативный массив (по умолчанию), 
	 * false - возвращать объект.
	 * @return mixed
	 */
	public static function srDecrypt($str, $key='', $assoc=true){
	    $str=base64_decode(urldecode($str));
	    $result='';
	    $keylen=strlen($key);
	    $strlen=3*strlen($str);
	    for($i=0; $i<$strlen; $i++) {
	        if(!isset($str[$i])) break;
	        $char=$str[$i];
			$keypos=$keylen ? (($i % $keylen)-1) : 0;
	        $keychar=(($keypos > $keylen) || ($keypos < 0)) ? '@' : $key[$keypos];
	        $char=chr(ord($char)-ord($keychar));
	        $result.=$char;
	    }
	    return @json_decode($result, $assoc);
	}

	/**
	 * Генерация пароля на основе openssl_random_pseudo_bytes()
	 * @param integer $bytes кол-во байтов
	 * @return string
	 */
	public static function opensslGeneratePassword($bytes=4)
	{
	    return bin2hex(openssl_random_pseudo_bytes($bytes));
	}

	/**
     * Генерирование пароля
     * @link https://stackoverflow.com/a/35501972/7341026
     * @param integer $length длина пароля
     * @param boolean $difficulty использовать более сложный пароль.
     * Если передано (false) в пароле будут использованы только буквы и цифры.
     * По умолчанию (true) генерировать более сложный пароль со спецсимволами.
     * @return string
     */
    public static function generatePassword($length=12, $difficulty=true)
    {
        $chars='abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ1234567890';
        
        if($difficulty) {
            $chars='!@#$%*&' . $chars;
        }
        
        return substr(str_shuffle($chars), 0, $length);
    }

	/**
     * OpenSSL. Простое обратимое шифрование. Шифрование.
     * @param mixed $data данные для шифрования
     * @param string $key ключ шифрования
     * @param string $method метод шифрования. По умолчанию "AES-256-CFB".
     * @return string
     */
    public static function sslEcrypt($data, $key='', $method='AES-256-CFB')
    {
        $str=json_encode($data, JSON_UNESCAPED_UNICODE);

        $result=openssl_encrypt($str, $method, $key);

        return urlencode(base64_encode($result));
    }
    
    /**
     * OpenSSL. Простое обратимое шифрование. Разшифрование.
     * @param string $str зашифрованная строка методом HHash::simpleEcrypt
     * @param string $key ключ шифрования
     * @param boolean $assoc true - возвращать ассоциативный массив (по умолчанию), 
     * false - возвращать объект.
     * @param string $method метод шифрования. По умолчанию "AES-256-CFB".
     * @return string
     */
    public static function sslDecrypt($str, $key='', $assoc=true, $method='AES-256-CFB')
    {
        $str=base64_decode(urldecode($str));
        
        $result=openssl_decrypt($str, $method, $key);
        
        return @json_decode($result, $assoc);
    }
}
