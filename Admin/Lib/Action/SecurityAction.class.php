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
        /** 令牌验证失败 */
        if (!$this->MUser->autoCheckToken($_POST))
        {
            die("非法提交。");
        }

        /** 初判断用户名密码 */
        $username = $_POST["username"];
        $password = $_POST["password"];
        if(!$this->common_str_validate($username, 4, 32))
        {
            die("用户名太长或者太短。");
        }
        if(!$this->common_str_validate($password, 6, 16, false))
        {
            die("密码太长或者太短。");
        }

        /** 验证用户名密码并登录 */
        $result = $this->MUser->check_username_and_password($_POST["username"], $_POST["password"]);

        /** 用户名或者密码错误 */
        if (false == $result)
        {
            die("用户名或者密码错误。");
        }
        else
        {
            /** 蛋疼的加密类 */
            import("@.Plugin.XHaffmanSec");
            $encrypt = new XHaffman();

            /** 信息数组（用于implode） */
            $session_array = array(
                $result["roleid"],
                $result["rolename"],
                $result["userid"],
                $result["username"],
                $result["email"],
                time()
            );

            /** 将信息数组转化为字符串 */
            $session_data = implode("|", $session_array);
            $session_data = $encrypt->Encode($session_data, C("ENCRYPTION_KEY"));

            /** 写入Session */
            Session::set("user_data", $session_data);

            die("1");
        }
    }

    public function logout()
    {
        Session::set("user_data", null);

        redirect(U("Security/login"));
    }
}
