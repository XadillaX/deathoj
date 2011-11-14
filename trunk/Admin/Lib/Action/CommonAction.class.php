<?php
/**
 * NBUT Online Judge System
 *
 * 后台控制器基类
 * @author konakona
 * @version $Id$
 * @copyright konakona, 31 十月, 2011
 * @package Common
 **/
class CommonAction extends Action
{
    private $MaxLoginTime = 1800;

    protected $page_record_start;
    protected $page_per_num;
    protected $page_current;

    //存储管理员信息
    protected $admin_information = array();

    //项目路径
    protected $web_dir;
    protected $web_root;

    /** 各模型 */
    protected $MUser;
    protected $MConfig;

    protected $web_config;

    /**
     * @version $Id$
     */
    public function  __construct()
    {
        parent::__construct();
        if (!function_exists('get_client_ip')) {
            Load('extend');
        }

        /** 初始化各模型 */
        $this->MUser = D("User", "UserModel");
        $this->MConfig = D("Config", "ConfigModel");

        /** 初始化管理员信息,如果没有登录则滚去登录 */
        if ($this->getAdminInformation() === null && $this->getActionName() != 'Security' && strtolower(ACTION_NAME) != "upload_std_data") {
            redirect(U("Security/login"));
            die(0);
        }

        $this->web_dir = str_replace("\\", "/", substr(__FILE__, 0, -39));  //网站跟目录，绝对路径
        $this->web_root = __ROOT__;                                          ///< 网站根目录，相对路径

        /** 获取网站配置 */
        $this->getConfig();
    }

    /**
     * 初始化网站配置
     * 模板使用方法{$HC.webname}
     * @version $Id$
     * @version $Id$
     * @return void
     */
    private function getConfig()
    {
        if ($this->web_config == "") {
            $this->web_config["webname"] = $this->MConfig->get_value("webname");
            $this->web_config["title"] = $this->MConfig->get_value("webname") . " :: ";
            $this->web_config["ojname"] = $this->MConfig->get_value("ojname");

            $this->web_config["webdir"] = $this->web_dir;
            $this->web_config["webroot"] = $this->web_root;
            $this->web_config["adminroot"] = $this->web_root . "/Admin";
        }
    }

    /**
     * 获取已登录管理员id
     *
     * @author konakona
     * @version $Id$
     * @version $Id$
     * @copyright konakona, 31 十月, 2011
     * @package common
     **/
    protected function getAdminId()
    {
        return $this->admin_infomation['userid'];
    }

    /**
     * @brief 获取用户登录信息
     *
     * @return array|null 若未登录或权限不是管理员，则返回null。
     * @version $Id$
     * @package common
     */
    protected function getAdminInformation()
    {
        if(count($this->admin_information) != 0) return $this->admin_information;

        /** 获取Session信息 */
        $session_data = Session::get("user_data");

        /** 未登录 */
        if("" == $session_data || null == $session_data)
        {
            $this->admin_information = null;
            return;
        }

        /** 有信息 */
        {
            /** 自己写的蛋疼的加密类 */
            import("@.Plugin.XHaffmanSec");
            $encrypt = new XHaffman();

            /** 解密Session */
            $session_data = $encrypt->Decode($session_data, C("ENCRYPTION_KEY"));

            /** 信息数组 */
            $session_array = explode("|", $session_data);

            /** 若超时 */
            if(time() - $session_array["5"] > $this->MaxLoginTime)
            {
                Session::set("user_data", "");
                $this->admin_information = null;
                return null;
            }

            /** 得到信息 */
            $temp = $this->MUser->get_user_info("userid", $session_array[2]);

            /** 若无此用户 */
            if(false == $temp)
            {
                Session::set("user_data", "");
                $this->admin_information = null;
                return null;
            }

            /** 额外用户信息 */
            $temp = $temp[0];
            $temp["rolename"] = $session_array[1];                                               ///< 角色名
            $temp["avatar"] = $this->MUser->get_avatar_url($temp["email"], "");            ///< 头像地址
            $temp["logintime"] = $session_array[5];                                              ///< 活动时间戳
            $temp["logintime_formatted"] = date("Y-m-d H:i:s", $temp["logintime"]);         ///< 格式化活动时间

            /** 更新Session */
            $session_array[5] = time();
            $session_data = implode("|", $session_array);
            $session_data = $encrypt->Encode($session_data, C("ENCRYPTION_KEY"));
            Session::set("userdata", $session_data);

            /** 赋值 */
            $this->admin_information = $temp;
        }

        switch($this->admin_information["roleid"])
        {
            case 1: $this->admin_information = array(); return null; break;
            case 2: $this->admin_information["role"] = "完全体"; break;
            case 3: $this->admin_information["role"] = "究极体"; break;
            default: $this->admin_information["role"] = "丧尸进化"; break;
        }

        return $this->admin_information;
    }

    /**
     * 通用字符串验证正确性
     * @version $Id$
     *
     * @param string $string
     * @param string $min_length
     * @param string $max_length
     * @param bool $shied
     * @param bool $space
     * @param bool $required
     * @return bool
     */
    public function common_str_validate($string, $min_length, $max_length, $shied = true, $space = false, $required = true)
    {
        if(($string == "" || $string == null) && $required)
        {
            return false;
        }

        /** 只能是字母、下划线、数字 */
        if($shied)
        {
            $ok = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_0123456789";
            if($space) $ok .= " ";

            $ok_len = strlen($ok);
            for($i = 0; $i < strlen($string); $i++)
            {
                $flag = false;
                for($j = 0; $j < $ok_len; $j++)
                {
                    if($ok[$j] == $string[$i])
                    {
                        $flag = true;
                        break;
                    }
                }
                if(!$flag) return false;
            }
        }

        if(strlen($string) < $min_length || strlen($string) > $max_length)
        {
            return false;
        }

        return true;
    }
}
