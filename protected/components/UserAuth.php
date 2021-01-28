<?php
use common\components\helpers\HEvent;

/**
 * Идентификация пользователя
 * 
 * @event onAfterAuthUserIdentity(['userIdentity'=>&$this]) 
 * событие вызывается после базовой авторизации пользователя
 */
class UserAuth extends CUserIdentity
{
    public function authenticate()
    {
        if (!function_exists('curl_init') || isset(Yii::app()->params['localauth'])) {
            return $this->localAuth();
        } else {
            return $this->extAuth();
        }
    }

    private function localAuth()
    {
        $users = include(Yii::getPathOfAlias('application.config').DS.'users.php');

        if (!isset($users[$this->username]))
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($users[$this->username]!==$this->password)
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else {
            $this->errorCode=self::ERROR_NONE;
			$this->setState('role', 'admin');
        }
        
        HEvent::raise('onAfterAuthUserIdentity', ['userIdentity'=>&$this]);
        
        return !$this->errorCode;
    }

    private function extAuth()
    {
        if (!function_exists('curl_init')) {
            throw new CException('Curl not found');
        }

        $data = array(
            'username'=>$this->username,
            'password'=>$this->password,
            'domain'=>Yii::app()->request->serverName,
        	/*'modules'=>implode(',',D::yd()->getActived()),
        	'securityKey'=>D::yd()->getSecurityKey()*/
        );

        $authServer = Yii::app()->params['authServer'] ?
            Yii::app()->params['authServer'] : 'http://login.dishman.ru';

        $curl = curl_init($authServer);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_REFERER, 'http://'. $data['domain']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result);
        
        $this->errorCode = $result ? $result->errorCode : 1;
        
        if(!$this->errorCode) {
			$role=($result->role == 'admin') ? 'sadmin' : (($result->role == 'user') ? 'admin' : '');
            $this->setState('role', $role);
            $this->setState('is_top_admin', 1);
        }
        
        HEvent::raise('onAfterAuthUserIdentity', ['userIdentity'=>&$this]);
        
        return !$this->errorCode;
    }
}
