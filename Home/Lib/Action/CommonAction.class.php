<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-8 上午10:33
 */

/**
 * @brief CommonAction
 * 前台控制器基类
 */
class CommonAction extends Action
{
    private $MaxLoginTime = 1800;

    protected $user_information = null;
    protected $user_model = null;
    protected $config_model = null;

    protected $web_config;

    protected $script_start_time = 0;
    protected $script_end_time = 0;

    /**
     * 弹出窗口并转向
     * @param $text
     * @param $url
     * @return void
     */
    protected function alert_redirect($text, $url = -1, $needalert = true)
    {
        $javascript = "<script type='text/javascript'>";
        if($needalert) $javascript .= "alert('{$text}');";

        if(!is_numeric($url))
        {
            $javascript .= "window.location.href = '{$url}';";
        }
        else
        {
            $javascript .= "window.history.go({$url});";
        }
        $javascript .= "</script>";

        $this->assign("javascript", $javascript);
        $this->assign("msg", $text);

        $this->display("Public:alert_redirect");

        die(0);
    }

    public function __construct()
    {
        parent::__construct();

        /** 开始计时 */
        $this->script_start_time = microtime(true);

        /** 排除此页面 */
        if("__construct" == ACTION_NAME)
        {
            $this->error("非法操作。");
            die(0);
        }

        /** 初始化各模型 */
        $this->user_model = new UserModel("user");
        $this->config_model = new ConfigModel("config");

        /** 检测登录信息 */
        $this->init_login_information();

        /** 获取系统标记 */
        $this->get_system_tag_info();
        $this->assign("current_user", $this->get_current_user());
    }

    /**
     * 检测登录信息
     * @version $Id$
     * @return void
     */
    private function init_login_information()
    {
        /** 获取Session信息 */
        $session_data = Session::get("user_data");

        /** 未登录 */
        if("" == $session_data || null == $session_data)
        {
            $this->user_information = null;
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
            if(time() - $session_array[5] > $this->MaxLoginTime)
            {
                Session::set("user_data", "");
                $this->user_information = null;
                return;
            }

            /** 得到信息 */
            $temp = $this->user_model->get_user_info("userid", $session_array[2]);

            /** 若无此用户 */
            if(false == $temp)
            {
                Session::set("user_data", "");
                $this->user_information = null;
                return;
            }

            /** 额外用户信息 */
            $temp = $temp[0];
            $temp["rolename"] = $session_array[1];                                               ///< 角色名
            $temp["avatar"] = $this->user_model->get_avatar_url($temp["email"], "");            ///< 头像地址
            $temp["logintime"] = $session_array[5];                                              ///< 活动时间戳
            $temp["logintime_formatted"] = date("Y-m-d H:i:s", $temp["logintime"]);         ///< 格式化活动时间

            /** 更新Session */
            $session_array[5] = time();
            $session_data = implode("|", $session_array);
            $session_data = $encrypt->Encode($session_data, C("ENCRYPTION_KEY"));
            Session::set("userdata", $session_data);

            /** 赋值 */
            $this->user_information = $temp;
            if($this->user_information["language"] == 0)
            {
                $this->user_information["language"] = 1;
            }

            /** 已做题目 */
            $this->user_information["solvedarray"] = explode("|", $this->user_information["solvedlist"]);
            if($this->user_information["solvedarray"][count($this->user_information["solvedarray"]) - 1] == "") array_pop($this->user_information["solvedarray"]);
            sort($this->user_information["solvedarray"]);

            $this->user_information["submitarray"] = explode("|", $this->user_information["submitlist"]);
            if($this->user_information["submitarray"][count($this->user_information["submitarray"]) - 1] == "") array_pop($this->user_information["submitarray"]);
            $this->user_information["todoarray"] = array_diff($this->user_information["submitarray"], $this->user_information["solvedarray"]);
            sort($this->user_information["submitarray"]);
            sort($this->user_information["todoarray"]);
        }
    }

    /**
     * 获取系统标记
     * @return void
     */
    private function get_system_tag_info()
    {
        $this->web_config["webname"] = $this->config_model->get_value("webname");
        $this->web_config["ojname"] = $this->config_model->get_value("ojname");
        $this->web_config["title"] = $this->web_config["webname"] . " :: ";
        $this->web_config["root"] = __ROOT__ . "/";
    }

    /**
     * 获取当前登录用户信息
     * @return array|null
     */
    public function get_current_user()
    {
        return $this->user_information;
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

    protected function display($templateFile = '', $charset = '', $contentType = 'text/html')
    {
        if (extension_loaded('zlib') && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ini_set('zlib.output_compression', 'On');
            ini_set('zlib.output_compression_level', 3);
            ob_start('ob_gzip');

            $this->script_end_time = microtime(true);
            $this->assign("script_time", $this->script_end_time - $this->script_start_time);

            parent::display($templateFile, $charset, $contentType);
            ob_end_flush();
        } else {

            $this->script_end_time = microtime(true);
            $this->assign("script_time", $this->script_end_time - $this->script_start_time);

            parent::display($templateFile, $charset, $contentType);
        }
    }
}
