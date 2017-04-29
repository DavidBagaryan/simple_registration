<?php

class Cabinet
{
    static $login;
    static $password;

    static function signup()
    {
        $data = $_POST;
        $errors = [];

        self::$login = $data['login'];
        self::$password = $data['password'];

        if (isset($data['do_signup'])) {

            $login = trim($data['login']);
            $password = $data['password'];
            $password2 = $data['password_2'];

            $query = "SELECT * FROM users WHERE user_login = ?";
            $data = DataBase::getConnection()->prepare($query);
            $data->execute([$login]);
            $login_matches = $data->rowCount();

            if ($login == '') {
                $errors[] = 'введите логин';
            }

            if ($password == '') {
                $errors[] = 'введите пароль';
            }

            if ($password2 != $password) {
                $errors[] = 'пароли не совпадают';
            }

            if ($login_matches > 0) {
                $errors[] = 'пользователь с таким именем существует';
            }

            if (empty($errors)) {
                $query = 'INSERT INTO users (`user_login`, `user_password`) VALUES (?, ?)';

                DataBase::getConnection()->prepare($query)->execute([$login, password_hash($password, PASSWORD_DEFAULT)]);

                $errors[] = 'регистрация прошла успешно';

                header("refresh:1; url=/");
            } else {
                return array_shift($errors);
            }
        }
        return array_shift($errors);
    }

    static function login()
    {
        $data = $_POST;
        $errors = [];

        self::$login = $data['login'];
        self::$password = $data['password'];

        if (isset($data['do_login'])) {
            $login = trim($data['login']);
            $password = $data['password'];

            $query = 'SELECT * FROM users WHERE user_login = ?';
            $user = DataBase::getConnection()->prepare($query);
            $user->execute([$login]);
            $login_matches = $user->rowCount();
            $user = $user->fetch();

            if ($login == '') {
                $errors[] = 'введите логин';
            }

            if ($login_matches > 0) {
                if (password_verify($password, $user['user_password'])) {
                    $_SESSION['logged_user'] = $user;

                    $errors[] = 'вход в аккаунт';

                    header("refresh:1; url=/");
                } else {
                    $errors[] = 'неправильно введен пароль';
                }
            } else {
                $errors[] = 'пользователь с таким логином не найден';
            }
        }
        return array_shift($errors);
    }

    static function logout()
    {
        unset($_SESSION['logged_user']);
        session_destroy();

        header("refresh:1; url=/");
    }
}