<?php

namespace app\models;

class UserRegister extends User
{
    public $password_confirmation = '';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge (
            parent::rules(),
            [
                ['password_confirmation', 'compare', 'compareAttribute' => 'password']
            ]
        );
    }
}
