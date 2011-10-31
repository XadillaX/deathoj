<?php
/**
 * 后台安全相关控制器：登录登出等。
 * User: 死月
 * Date: 11-10-26
 * Time: 下午6:09
 * Version: $Id$
 */
class SecurityAction extends Action{

    //避免空请求报错
    public function _empty(){}
    
    /**
     * 登录操作
     * @return void
     */
    public function login()
    {
        $this->display();
    }

    public function chklogin()
    {
        $MUser = new UserModel("user");

        /** 令牌验证失败 */
        if (!$MUser->autoCheckToken($_POST)) {
            die("Illegal submission.");
        }

        /** 验证用户名密码并登录 */
        $result = $MUser->login($_POST["username"], $_POST["password"]);
        if (null == $result) {
            die("1");
        }
        else
        {
            die($result);
        }
    }
}
