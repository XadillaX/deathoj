<?php
/**
 * NBUT Online Judge System
 *
 * 后台相关控制器
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package Security
 */
class SecurityAction extends CommonAction{

    //避免空请求报错
    public function _empty()
    {
        /** ! - - ! */
    }

    /** 构造函数：调用父类构造函数 */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 登录操作
     * @return void
     */
    public function login()
    {
        $this->assign("HC", $this->web_config);
        $this->display();
    }

    /**
     * 检验登录操作
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

    public function logout()
    {
        Session::set("userdata", null);

        redirect(U("Security/login"));
    }
}
