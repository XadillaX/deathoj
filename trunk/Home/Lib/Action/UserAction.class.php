<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-8 上午10:51
 */

/**
 * @brief UserAction
 * 前台用户控制器
 */
class UserAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录页面
     * @return void
     */
    public function login()
    {
        /** 若已登录则返回首页 */
        if(null != $this->get_current_user())
        {
            redirect(__ROOT__);
            die(0);
        }

        /** 显示模板 */
        $this->web_config["title"] .= "Login";
        $this->assign("HC", $this->web_config);
        $this->display();
    }

    /**
     * 注册页面
     * @return void
     */
    public function register()
    {
        /** 若已登录则返回首页 */
        if(null != $this->get_current_user())
        {
            redirect(__ROOT__);
            die(0);
        }

        /** 显示模板 */
        $this->web_config["title"] .= "Register";
        $this->assign("HC", $this->web_config);
        $this->display();
    }

    public function chkreg()
    {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $repwd = $_POST["repwd"];
        $nickname = $_POST["nickname"];
        $email = $_POST["email"];
        $school = $_POST["school"];
        $motto = $_POST["motto"];

        /** 令牌验证 */
        if(!$this->user_model->autoCheckToken($_POST))
        {
            die("非法提交。");
        }

        /** 数据验证 */
        if(strlen($username) < 4 || strlen($username) > 16)
        {
            die("用户名长度必须介于4~16之间。");
        }
        if(strlen($password) < 6 || strlen($password) > 16)
        {
            die("密码长度必须结余6~16之间。");
        }

        /** TODO: 继续验证吖验证 */
    }

    /**
     * 确认登录信息操作
     * @return void
     */
    public function chklogin()
    {
        /** 令牌验证失败 */
        if (!$this->user_model->autoCheckToken($_POST))
        {
            die("非法提交。");
        }

        /** 验证用户名密码并登录 */
        $result = $this->user_model->login($_POST["username"], $_POST["password"]);
        if (null == $result)
        {
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

        redirect(__ROOT__);
    }

    /**
     * 根据邮箱获取用户头像URL
     * @return void
     */
    public function get_user_avatar_url_by_email()
    {
        /** 获取邮箱 */
        $email = $_GET["email"];
        $size = $_GET["size"];

        $url = $this->user_model->get_avatar_url($email, $size);
        
        echo $url;
    }

    /**
     * 根据用户名获取用户头像URL
     * @return void
     */
    public function get_user_avatar_url()
    {
        /** 获取用户名 */
        $username = $_GET["username"];
        $size = $_GET["size"];

        $url = "";
        $array = $this->user_model->get_user_info("username", $username);
        if(false == $array)
        {
            $url = $this->user_model->get_avatar_url("", $size);
        }
        else
        {
            $url = $this->user_model->get_avatar_url($array[0]["email"], $size);
        }

        echo $url;
    }
}
