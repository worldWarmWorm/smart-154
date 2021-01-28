<?php
/**
 * Системные настройки
 *
 */
use YiiHelper as Y;
use AttributeHelper as A;

class SystemController extends DevadminController
{
	public $layout = "column2";
	
	public function actionIndex()
	{
		if (!function_exists('curl_init')) {
			throw new CException('Curl not found');
		}
		
		if(!D::role('sadmin')) 
			throw new \CHttpException(403);
			
		try {
			if(Y::request()->isPostRequest) {
				/*$login=Y::request()->getPost('l');
				$pwd=Y::request()->getPost('p');
				if(!$login || !$pwd) {
					throw new CException('Поля логин и пароль обязательны для заполнения');
				}
				else {
					$file=Yii::getPathOfAlias('application.config').DS.'modules.php';
					$skey=null;
					if(is_file($file)) {
						$config=include($file);
						$skey=A::get($config,DApi::SKEY);
					}*/
					$modules=Y::request()->getPost('modules');
					/*$names=is_array($modules) ? array_keys($modules) : array();
 					
					$data = array(
						'username'=>$login,
						'password'=>$pwd,
						'modules'=>implode(',',$names),
						'domain'=>Y::request()->getPost('domain'),
						'securityKey'=>$skey
					);
					
					$authServer = (Yii::app()->params['authServer'] ?: 'http://login.dishman.ru').'/site/updateModules';
					
 					$curl = curl_init($authServer);
 					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
 					curl_setopt($curl, CURLOPT_POST, 1);
 					curl_setopt($curl, CURLOPT_REFERER, 'http://'. $data['domain']);
 					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					
 					$result = curl_exec($curl);
 					curl_close($curl);
					
					$result = json_decode($result);
					
					if(!($result && $result->success === true)) {
						throw new CException($result->error?:'Произошла ошибка на сервере');
					}
					else {
						$data=array(DApi::SKEY=>$result->securityKey);
						*/
						$data=array();
						foreach($modules as $name=>$active) $data[$name]=true;
						
						file_put_contents(
							Yii::getPathOfAlias('application.config').DS.'modules.php',
							'<?php return '.A::toPHPString($data)
						);
						
						Yii::app()->user->setFlash('success', 'Изменения успешно приняты');
						$this->refresh(true);
					/*}
				}*/
			}
		} 
		catch(Exception $e) {
			Yii::app()->user->setFlash('error', $e->getMessage());
		}
		
		$this->render('index');
	}

}
