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

    public function get_user_avatar()
    {
        ob_clean();

        /** 获取邮箱 */
        $email = $_GET["email"];
        $size = $_GET["size"];

        $url = "http://1.gravatar.com/avatar/" . md5(strtolower($email)) . ".jpg?d=mm&size=" . $size;

        /** 输出header信息 */
        header("content-type: image/jpeg");

        /** 输出图片 */
        $bin = file_get_contents($url);
        echo $bin;
    }
}
