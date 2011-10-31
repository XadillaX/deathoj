<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:38
 */

/**
 * @brief SecurityAction
 * 安全相关控制器：登录或者登出等。
 */
class SecurityAction extends Action
{
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

    /**
     * 检验登录是否成功
     * @return void
     */
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
