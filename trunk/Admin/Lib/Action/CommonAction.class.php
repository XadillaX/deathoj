﻿<?php
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
    protected $page_record_start;
    protected $page_per_num;
    protected $page_current;

    //存储管理员信息
    protected $admin_infomation = array();

    //项目路径
    protected $web_dir;

    /** 各模型 */
    protected $MUser;
    protected $MConfig;

    protected $web_config;

    public function  __construct()
    {
        parent::__construct();
        if (!function_exists('get_client_ip')) {
            Load('extend');
        }

        /** 初始化各模型 */
        $this->MUser = D("User", "UserModel");
        $this->MConfig = D("Config", "ConfigModel");

        /** 获取网站配置 */
        $this->getConfig();

        /** 初始化管理员信息,如果没有登录则滚去登录 */
        if ($this->getAdminInfomation() === null && $this->getActionName() != 'Security') {
            redirect(U("Security/login"));
            die(0);
        }
        
        $this->web_dir = str_replace("\\", "/", substr(__FILE__, 0, -39)); //网站跟目录，绝对路径
    }

    /**
     * 初始化网站配置
     * 模板使用方法{$HC.webname}
     * @version $Id$
     * @return void
     */
    private function getConfig()
    {
        if ($this->web_config == "")
        {
            $this->web_config["webname"] = $this->MConfig->get_value("webname");
        }
    }

    /**
     * 获取已登录管理员id
     *
     * @author konakona
     * @version $Id$
     * @copyright konakona, 31 十月, 2011
     * @package common
     **/
    protected function getAdminId()
    {
        return $this->admin_infomation['admin_id'];
    }

    /**
     * @brief 获取用户登录信息
     *
     * @return array|null 若未登录或权限不是管理员，则返回null。
     * @version $Id$
     * @package common
     */
    protected function getAdminInfomation()
    {
        $MUser = new UserModel("user");
        $this->admin_infomation = $MUser->check_online();

        /** roleid为1的时候为普通用户，则这时也不算管理员登录 */
        if ($this->admin_infomation["roleid"] == 1) {
            $this->admin_infomation = null;
        }

        return $this->admin_infomation;
    }
}
