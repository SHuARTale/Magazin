<?php

require_once "common/Page.php";
use common\Page;
use common\DbHelper;
class reg extends Page
{
    private $auth;
    private $error;
    public function __construct()
    {
        parent::__construct();
        if (isset($_POST['login']))
            $this->error = $this->regUser();
    }

    protected function showContent()
    {
        switch ($this->error){
            case 1:{
                $e_msg = "Неверный логин!";
                break;
            }
            case 2:{
                $e_msg = "Неверный пароль!";
                break;
            }
            case 3:{
                $e_msg = "Пароли не совпадают!";
                break;
            }
            case 4:{
                $e_msg = "Неверное имя пользователя!";
                break;
            }
            case -1:{
                $e_msg = "Заполните все поля формы!";
                break;
            }
            case -2:{
                $e_msg = "Не удалось зарегистрировать пользователя. Возможно такое имя уже занято!";
                break;
            }
        }
        if (isset($e_msg)) print ("<div class='error'>$e_msg</div>");
        ?>
        <form method="post" action="reg.php">
            <table class='auth'>
                <tr>
                    <td colspan="2" class="tdhead">Регистрация пользователя:</td>
                </tr>
                <tr>
                    <td>Логин:</td>
                    <td><label>
                            <input type="text" size="30" maxlength="50" name="login" value="<?php isset($_POST['login'])?print($_POST['login']):"";?>" placeholder="Ваш уникальный идентификатор">
                        </label></td>
                </tr>
                <tr>
                    <td>Пароль:</td>
                    <td><label>
                            <input type="password" size="30" maxlength="50" name="password" value="<?php isset($_POST['password'])?print($_POST['password']):"";?>" placeholder="Пароль">
                        </label></td>
                </tr>
                <tr>
                    <td>Повтор пароля:</td>
                    <td><label>
                            <input type="password" size="30" maxlength="50" name="password2"  value="<?php isset($_POST['password2'])?print($_POST['password2']):"";?>"placeholder="Повтор пароля">
                        </label></td>
                </tr>
                <tr>
                    <td>Как Вас зовут:</td>
                    <td><label>
                            <input type="text" size="30" maxlength="100" name="name" value="<?php isset($_POST['name'])?print($_POST['name']):"";?>" placeholder="Ваши фамилия и имя">
                        </label></td>
                </tr>
                <tr><td colspan="2" class="tdhead"><input type="submit" value="Зарегистрироваться"></td></tr>
                <tr><td colspan="2" class="tdhead"><a href="auth.php">Уже зарегистрированы?</a></td></tr>
            </table>
        </form>
        <?php
    }

    private function regUser(): int
    {
        $error = 0;
        if (
            isset($_POST['login'])
            and isset($_POST['password'])
            and isset($_POST['password2'])
            and isset($_POST['name'])
        ){
            $login = htmlspecialchars($_POST['login']);
            if (mb_strlen($login) < 4 || mb_strlen($login) > 30) $error = 1;
            $password = htmlspecialchars($_POST['password']);
            if (mb_strlen($password) < 6 || mb_strlen($password) > 30) $error = 2;
            $password2 = htmlspecialchars($_POST['password2']);
            if ($password !== $password2) $error = 3;
            $name = htmlspecialchars($_POST['name']);
            if (mb_strlen($name) < 2 || mb_strlen($password) > 100) $error = 4;
        } else $error = -1;
        if ($error === 0){
            $dbh = DbHelper::getInstance();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if (!$dbh->saveUser($login, $hash, $name)) $error = -2;
        }
        return $error;
    }
}

(new reg())->show();