Yii-Transliteration-Helper
==========================

Help transliteration ported from [Drupal Transliteration module] (http://drupal.org/project/transliteration).

INSTALL
==========================

Put the `Transliteration.php` and `transliteration` directory into `application.components`, `ext.components` or `ext.helpers`.

Include it in your config.

~~~
[php]
	'import' => array(
		'application.components.*',
//		'ext.components.*',
//		'ext.helpers.*',
~~~

That's it.

USAGE
==========================

~~~
	// Transliterate a string
	$text = Transliteration::text($text);
	
	// Convert special character into ASCII one, with Lowercase
	$lowercased_filename = Transliteration::file($filename);
	
	// Or just replace special chars and keep the case as normal
	$filename = Transliteration::file($filename, FALSE);
~~~


UPDATE FROM DRUPAL
==========================

1. Download the latest transliteration module from [Drupal transliteration module] (http://drupal.org/project/transliteration)
2. Copy the `data` folder in this module to old `transliteration` folder.
3. That's it.	
