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
    protected $user_information = null;
    protected $user_model = null;
    protected $config_model = null;

    protected $web_config;

    public function __construct()
    {
        parent::__construct();

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
    }

    /**
     * 检测登录信息
     * @return void
     */
    private function init_login_information()
    {
        $this->user_information = $this->user_model->check_online();
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
        $this->web_config["root"] = __ROOT__;
    }

    /**
     * 获取当前登录用户信息
     * @return array|null
     */
    public function get_current_user()
    {
        return $this->user_information;
    }
}
