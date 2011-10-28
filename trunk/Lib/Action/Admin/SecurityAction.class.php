<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午6:09
 * To change this template use File | Settings | File Templates.
 */
 
class SecurityAction extends Action {
    /**
     * 登录操作
     * @return void
     */
    public function login()
    {
        $MConfig = new ConfigModel("config");
        $webconfig["webname"] = $MConfig->get_value("webname");

        $this->assign($webconfig);
        $this->display();
    }

    public function chklogin()
    {
        $MUser = new UserModel("user");

        /** 令牌验证失败 */
        if(!$MUser->autoCheckToken($_POST))
        {
            die("Illegal submission.");
        }

        /** 验证用户名密码并登录 */
        $result = $MUser->login($_POST["username"], $_POST["password"]);
        if(null == $result)
        {
            die("1");
        }
        else
        {
            die($result);
        }
    }
}
