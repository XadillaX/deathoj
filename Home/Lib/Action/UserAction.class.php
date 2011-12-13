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
    private $user_per_page;

    public function __construct()
    {
        parent::__construct();

        $this->user_per_page = C("USER_NUM_PER_PAGE");
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

    /**
     * 验证注册资料
     * @return void
     */
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
        if(!$this->common_str_validate($username, 4, 32))
        {
            die("用户名长度必须介于4~32之间，且只能是字母、数字和下划线。");
        }
        if(!$this->common_str_validate($password, 6, 32, false))
        {
            die("密码长度必须介于6~16之间。");
        }
        if($password != $repwd)
        {
            die("两次密码输入不一致。");
        }
        /** TODO: 让昵称可以是中文。 -- 小熊,谢谢 */
        if(!$this->common_str_validate($nickname, 1, 32, false))
        {
            die("昵称长度必须介于1~32之间。");
        }
        if(!ereg("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+", $email) || !$this->common_str_validate($email, 5, 255, false))
        {
            die("邮箱地址有误！");
        }
        if(!$this->common_str_validate($school, 0, 255, true, true, false))
        {
            die("学校输入有误。");
        }
        if(!$this->common_str_validate($motto, 0, 255, false, true, false))
        {
            die("格言输入过长。");
        }

        /** 是否唯一 */
        $condition["username"] = $username;
        $condition["email"] = $email;
        $condition["nickname"] = $nickname;
        $condition["_logic"] = "or";
        $temp = $this->user_model->where($condition)->select();
        if(false != $temp)
        {
            die("用户名或者邮箱或者昵称已存在。");
        }

        /** 注册成功 */
        $userid = $this->user_model->create_user($username, md5($password), $nickname, $email, $school, $motto);
        if(false == $userid)
        {
            die("系统错误，注册失败。\n" . $this->user_model->getLastSql());
        }
        else
        {
            /** 登录 */
            /** 蛋疼的加密类 */
            import("@.Plugin.XHaffmanSec");
            $encrypt = new XHaffman();

            /** 信息数组（用于implode） */
            $result = $this->user_model->check_username_and_password($username, $password);
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

    /**
     * 确认登录信息操作
     * @version $Id$
     * ↑将Session搞到这里了- -|||
     * 
     * @return void
     */
    public function chklogin()
    {
        /** 令牌验证失败 */
        if (!$this->user_model->autoCheckToken($_POST))
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
        $result = $this->user_model->check_username_and_password($_POST["username"], $_POST["password"]);

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

    public function avatar()
    {
        /** 获取用户名 */
        $username = strtolower($_GET["username"]);
        $size = $_GET["size"];

        /** 去后缀 */
        $pos = strpos($username, ".jpg");
        if(false != $pos)
        {
            $username = substr($username, 0, $pos);
        }

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

        $bin = file_get_contents($url);

        ob_clean();
        header("Content-type: image/jpeg");
        echo $bin;
    }

    /**
     * 用户列表
     * @version $Id$
     * @return void
     */
    public function user_list()
    {
        /** 分页信息 */
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $user_count = $this->user_model->where(array("roleid" => array("neq", -100)))->count();
        $page_count = (int)((int)$user_count / (int)$this->user_per_page) + (($user_count % $this->user_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("User/user_list") . "?page=%s";
        $page_obj->per_page = $this->user_per_page;              ///< 每页数量
        $page_obj->item_count = $user_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_obj->id = "xpage";
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "用户排名 :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 用户列表 */
        $user_list = $this->user_model->get_user_by_page("", $page, $this->user_per_page, "solvednum DESC, submitnum DESC");
        for($i = 0; $i < count($user_list); $i++)
        {
            $user_list[$i]["rank"] = ($page - 1) * $this->user_per_page + $i + 1;
            $user_list[$i]["solvedarray"] = explode("|", $user_list[$i]["solvedlist"]);
            $user_list[$i]["submitarray"] = explode("|", $user_list[$i]["submitlist"]);
        }
        $this->assign("user_list", $user_list);

        $this->display();
    }

    /**
     * 浏览用户
     * @version $Id$
     * @return void
     */
    public function view_user()
    {
        $userid = $_GET["id"];
        $user_info = $this->user_model->get_user_by_id($userid);
        if(NULL === $user_info)
        {
            redirect(__ROOT__);
            die(0);
        }

        $this->web_config["title"] .= "用户信息 - {$user_info['username']}";
        $this->assign("HC", $this->web_config);

        $user_info["motto"] = $this->user_model->HtmlEncode($user_info["motto"]);

        /** AC列表 */
        $user_info["solvedarray"] = explode("|", $user_info["solvedlist"]);
        if($user_info["solvedarray"][count($user_info["solvedarray"]) - 1] == "") array_pop($user_info["solvedarray"]);
        sort($user_info["solvedarray"]);

        /** TODO列表 */
        $user_info["submitarray"] = explode("|", $user_info["submitlist"]);
        if($user_info["submitarray"][count($user_info["submitarray"]) - 1] == "") array_pop($user_info["submitarray"]);
        $user_info["todoarray"] = array_diff($user_info["submitarray"], $user_info["solvedarray"]);
        sort($user_info["submitarray"]);
        sort($user_info["todoarray"]);

        $this->assign("info", $user_info);

        $this->display();
    }

    /**
     * 保存头像栏状态
     * @vesion $Id$
     * @return void
     */
    public function save_avatar_bar()
    {
        /** 若未登录 */
        if(null == $this->get_current_user())
        {
            $this->error("未登录或者登陆已超时", true);
            die(0);
        }

        /** 头像栏状态 */
        $state = $_POST["state"];
        if($state != 1 && $state != 0) $state = 1;

        $user_info = $this->get_current_user();
        $result = $this->user_model->save_avatar_state($user_info["userid"], $state);

        /** 结果 */
        $this->success("保存成功。", true);
        die(0);
    }
}
