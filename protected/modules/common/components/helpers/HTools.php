<?php
/**
 * Tools helper class.
 */
namespace common\components\helpers;

use common\components\helpers\HArray as A;

class HTools
{
	public static $gost7792000=[
		'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g',
		'д'=>'d',  'е'=>'e', 'ё'=>'yo', 'ж'=>'zh', 'з'=>'z',
		'и'=>'i', 'й'=>'j', 'к'=>'k', 'л'=>'l',
		'м'=>'m',  'н'=>'n', 'о'=>'o', 'п'=>'p',  'р'=>'r',
		'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f',
		'х'=>'h',  'ц'=>'c', 'ч'=>'ch','ш'=>'sh', 'щ'=>'shh',
		'ъ'=>'',  'ы'=>'y', 'ь'=>'',  'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
		'А'=>'A', 'Б'=>'B',  'В'=>'V', 'Г'=>'G',
		'Д'=>'D', 'Е'=>'E', 'Ё'=>'YO',  'Ж'=>'ZH', 'З'=>'Z',
		'И'=>'I', 'Й'=>'J',  'К'=>'K', 'Л'=>'L',
		'М'=>'M', 'Н'=>'N', 'О'=>'O',  'П'=>'P',  'Р'=>'R',
		'С'=>'S', 'Т'=>'T',  'У'=>'U', 'Ф'=>'F',
		'Х'=>'H', 'Ц'=>'C', 'Ч'=>'CH', 'Ш'=>'SH', 'Щ'=>'SHH',
		'Ъ'=>'',  'Ы'=>'Y',
		'Ь'=>'',
		'Э'=>'E',
		'Ю'=>'YU',
		'Я'=>'YA',
		'a'=>'a', 'b'=>'b', 'c'=>'c', 'd'=>'d', 'e'=>'e',
		'f'=>'f', 'g'=>'g', 'h'=>'h', 'i'=>'i', 'j'=>'j',
		'k'=>'k', 'l'=>'l', 'm'=>'m', 'n'=>'n', 'o'=>'o',
		'p'=>'p', 'q'=>'q', 'r'=>'r', 's'=>'s', 't'=>'t',
		'u'=>'u', 'v'=>'v', 'w'=>'w', 'x'=>'x', 'y'=>'y',
		'z'=>'z',
		'A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D','E'=>'E',
		'F'=>'F','G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K',
		'L'=>'L', 'M'=>'M', 'N'=>'N', 'O'=>'O','P'=>'P',
		'Q'=>'Q','R'=>'R','S'=>'S','T'=>'T','U'=>'U','V'=>'V',
		'W'=>'W', 'X'=>'X', 'Y'=>'Y', 'Z'=>'Z'
	];

	/**
	 * Вычислить выражение.
	 * @param callable|mixed|array $expression выражение вычисления.
	 * callable - функция, которая возвращает результат вычисления.
	 * Может быть передан также массив с элементом "params"=>array(value1, value2),
	 * параметры для callable функции/метода.
	 * string - если в начале строки передано "php:", то будет вычислено PHP выражение. 
	 * array - массив выражений (callable|midex).
	 * @param string|array $operator оператор объединения выражений, если в $expression
	 * передан массив. По умолчанию "&" (и). Если оператор не определен, будет 
	 * использован "&" (и). Для задания различных операторов объединения выражений необходимо 
	 * передать операторы на соотвествующих позициях. 
	 * Может также принимать:
	 * "&"(и), "|"(или), "+"(сложение), "-"(вычитание), "/"(деление), "%"(остаток от деления),
	 * "*" (умножение), "."(объединение строк). 
	 * @param string $defaultOperator оператор объединения выражений по умолчанию. Используется,
	 * если в $expression и $operator переданы массивы, но кол-во элементов в $operator меньше, чем
	 * в $expression. По умолчанию "&" (и). Если оператор не определен, будет использован "&" (и).
	 */
	public static function evaluate($expression, $operator='&', $defaultOperator='&')
	{
		$result=null;
		
		$params=A::get($expression, 'params', []);
		if(A::existsKey($expression, 'params')) {
			unset($expression['params']);
			if(count($expression) == 1) $expression=array_pop($expression);
		}
		
		if(is_callable($expression)) {
			$result=call_user_func_array($expression, $params);
		}
		elseif(is_array($expression)) {
			$prev=null;
			foreach($expression as $pos=>$expr) {
				if($prev === null) $result=self::evaluate($expr);
				else {
					if(is_array($operator)) $op=A::get($operator, $prev, $defaultOperator);
					else $op=$operator;
					
					$result=self::op($result, self::evaluate($expr), $op);
				} 
				$prev=$pos;
			}
		}
		else {
			if(is_string($expression) && (strpos($expression, 'php:') === 0)) {
				try { $result=eval($expression); }
				catch(\Exception $e) { $result=null; }
			}
			else {
				$result=$expression;
			}
		}
		
		return $result;
	}
	
	/**
	 * Получить результат выполнения заданной операции над двумя значениями.
	 * @param mixed $value1 значение 1.
	 * @param mixed $value2 значение 2.
	 * @param string $operator оператор. По умолчанию "&".
	 * Если оператор не определен, будет использован "&" (и).
	 * Может принимать:
	 * "&" - и;
	 * "|" - или;
	 * "+" - сложение;
	 * "-" - вычитание;
	 * "/" - деление;
	 * "%" - остаток от деления;
	 * "*" - умножение;
	 * "." - объединение строк.
	 * @param string $type1 тип преобразования значения 1. По умолчанию "i".
	 * "i" - целое, "f" - дробное. Используется для операторов "+","-","/","%" и "*".
	 * @param string $type2 тип преобразования значения 2. По умолчанию "i".
	 * "i" - целое, "f" - дробное. Используется для операторов "+","-","/","%" и "*".
	 */
	public static function op($value1, $value2, $operator='&', $type1='i', $type2='i')
	{
		switch($operator) {
			case '&': return ((bool)$value1 && (bool)$value2); 
			case '|': return ((bool)$value1 || (bool)$value2); 
			case '.': return (string)$value1 . (string)$value2; 
			case '+': case '-': case '/': case '%': case '*': 
				if ($type1=='f') $value1=(float)$value1;
				else $value1=(int)$value1; 
				if ($type2=='f') $value2=(float)$value2;
				else $value2=(int)$value2; 
			case '+': return $value1 + $value2;
			case '-': return $value1 - $value2;
			case '/': return $value1 / $value2;
			case '%': return $value1 % $value2;
			case '*': return $value1 * $value2;
			default : 
				return ((bool)$value1 && (bool)$value2);
		}
	}

    /**
     * Выкидывает из переданной строки лишние символы и приводит её к типу float
     * @param string|number|null $n
     * @return float|int|null
     */
	public static function makeNumber ($n) {
	    if ($n === '' || $n === null) {
	        return null;
        }
	    if (empty($n)) {
	        return 0;
        }
	    $n = preg_replace(
	        [
                '/(,+)/',
                '/(^[\d\.]+)/'
            ],
            [
                '.',
                ''
            ],
            (string)$n
        );
	    return floatval($n);
    }

    /**
     * @param array $ar
     * @return array
     */
    public static function makeNumberArrayItems ($ar) {
	    foreach ($ar as &$item) {
	        $item = static::makeNumber($item);
        }
        return $ar;
    }

	/**
     * Получить короткое имя класса
     * @param string $classname полное имя класса
     * @return string
     */
    public static function getShortClassName($classname, $lowerCamelCase=false)
    {
        if(is_object($classname)) {
            $classname=get_class($classname);
        }

        $shortClassName=(substr($classname, strrpos($classname, '\\') + 1));

        if($lowerCamelCase) {
            return lcfirst($shortClassName);
        }

        return $shortClassName;
    }

	/**
     * Увеличение числа на определенный процент
     * @param number $number число
     * @param number $percent процент
     */
    public static function incByPersent($number, $percent)
    {
        return (float)$number + (((float)$number / 100) * (float)$percent);
    }

	/**
     * Транслитерация
     * @param string $str строка
     * @param array|false $arr массив транслитерации
     * @param integer|false $length максимальная длина возвращаемой строки
     */
    public static function cyr2lat($str, $arr=false, $length=false)
    {
    	if(!$arr) {
    		$arr=static::$gost7792000;
    	}
    	$lat=strtr($str, $arr);
    	$lat=preg_replace('/[^\/\\\\^., \-()!a-z_#~0-9]/i', '', $lat);
    	$lat=preg_replace('/[^a-z0-9 ]/i', '', $lat);
    	if($length && (strlen($lat) > $length)) {
    		$lat=substr($lat, 0, $length);
    	}
    	return $lat;
    }

    /**
     * Получение значения для ЧПУ
     * @param string $str строка
     * @param array|false $arr массив транслитерации
     * @param integer|false $length максимальная длина возвращаемой строки
     * @param boolean $stip удалять двойные тире. По умолчанию (true) удалять.
     */
 	public static function alias($str, $arr=false, $length=false, $strip=true)
    {
        $alias=preg_replace('/[^0-9a-z\-]+/', '-', strtolower(static::cyr2lat($str, $arr, $length)));
        
        return $strip ? preg_replace('/\-+/', '-', $alias) : $alias;
    }
    
    /**
     * Проверка пустого (нулевого) значения даты в любом формате
     * @param string $date
     * @return bool
     */
    public static function isDateEmpty($date)
    {
        return !preg_replace('/[^1-9]+/', '', $date); 
    }
    
    /**
     * Получение правильной формы склонения слова для числа
     * @param integer $number число
     * @param array $forms формы слова, вида array(один, два, много) 
     * @return string
     */
    public static function pluralLabel($number, $forms) 
    {
        $cases = array (2, 0, 1, 1, 1, 2);
        if(count($forms) === 1) $forms[]=end($forms);
        if(count($forms) === 2) $forms[]=end($forms);
        return $forms[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
    }
    
    /**
     * Нормализация номера телефона для сохранения.
     * Удаление всех сиволов, кроме цифр 
     * @param string $phone нормализация номера телефона
     * @return string
     */
    public static function normalizePhone($phone)
    {
		return preg_replace('/^8(.*)$/', '7$1', preg_replace('/[^0-9]+/', '', $phone));
    }
    
    /**
     * Форматированный вывод номера телефона
     * @param string $phone номер телефона (11 цифр)
     * @param string $format шаблон форматирования. 
     * По умолчанию "+$1 ( $2 ) $3 - $4 - $5"
     * @return string
     */
    public static function formatPhone($phone, $format='+$1 ( $2 ) $3 - $4 - $5')
    {
        return preg_replace('/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/', $format, $phone);
    }
    
    /**
     * Простое преобразование данных в JSON формат для JS скриптов.
     * @param mixed $data данные
     * Поддерживается префикс "js:" у строковых переменных. 
     * Будет добавлено как есть, без префикса "js:". 
     * @param boolean $isArray возвращать массив. 
	 * Может быть передан массив ключей (путь к вложенным ключам задается через точку)
     * которые необходимо обработать как массивы.  
     * По умолчанию (FALSE) будет возвращена строка для объекта.
     * @param string $parentPath родительский путь (через точку). Используется как
     * вспомогательный параметр для параметра $isArray, когда он передан как массив.
     * По умолчанию (FALSE) будет возвращена строка для объекта.
     * @return string
     */
    public static function jsEncode($data, $isArray=false, $parentPath='') 
    {
		$arrayKeys=[];
        if(is_array($isArray)) {
            $arrayKeys=$isArray;
            $isArray=in_array($parentPath, $arrayKeys);
        }

        $json=$isArray ? '[' : '{';
        
        /** @var callable $format форматирование значения */
        $format=function($val) use (&$format, $arrayKeys, $parentPath) {
            if(is_array($val)) {
                while(($k=key($val)) && is_numeric($k));
                if(is_string($k)) {
                    return static::jsEncode($val, $arrayKeys, ($parentPath ? "{$parentPath}.{$k}" : $k));
                }
                else {
                    $_vv=[]; foreach($val as $v) $_vv[]=$format($v);
                    return '[' . implode(',',$_vv) . ']';
                }
            }
            elseif(is_numeric($val)) {
                return $val;
            }
            elseif(is_string($val)) {
                if(mb_strpos($val, 'js:') === 0) return mb_substr($val, 3);
                else return '"' . $val . '"';
            }
            else {
                return 'null';
            }
        };
        
        if(is_array($data)) {
            $_vv=[];
            foreach($data as $k=>$v) {
                if($isArray) $_vv[]='{' . $k.':' . $format($v) . '}';
                else $_vv[]=$k.':' . $format($v);                
            }
            $json.=implode(',', $_vv);
        }
        elseif($isArray) {
            $json.=$format($v);
        }
        else {
            $json.='0:' . $format($v);
        }
        
        $json.=$isArray ? ']' : '}';
        
        return $json;
    }
}
