<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            // Проверка на то, что пользователь не null
            $user = $this->getUser();
            if ($user) {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            }
        }

        // Добавление ошибки взято из удаленного метода validatePassword
        $this->addError('password', 'Incorrect username or password.');
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::login($this->username, $this->password);
        }

        return $this->_user;
    }
}
