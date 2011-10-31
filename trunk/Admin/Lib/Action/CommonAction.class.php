<?php
/**
 * 啊哈哈哈
 *
 * @author konakona
 * @version $Id$
 * @copyright konakona, 31 十月, 2011
 * @package Common
 **/

class CommonAction extends Action{

    protected $page_record_start;
    protected $page_per_num;
    protected $page_current;
    //存储管理员信息
    protected $admin_infomation = array();

    //项目路径
    protected $web_dir;

    protected $web_config;

    public function  __construct(){
        parent::__construct ();
        if(!function_exists('get_client_ip')){
            Load('extend');
        }

        $this->getConfig();

        //初始化管理员信息,如果没有登录则滚去登录
        if($this->getAdminInfomation() === false && $this->getActionName()!='Security'){
            //redirect(U("Security/login"));
        }
        $this->web_dir = str_replace("\\", "/", substr(__FILE__, 0, - 39)); //网站跟目录，绝对路径

    }

    /**
     * 初始化网站配置
     * 模板使用方法{$HC.webname}
     * @return void
     */
    private function getConfig(){
        if($this->web_config=="")
            $this->assign('HC',$this->web_config = D('Config')->get_value("webname"));
    }


    /**
     * 获取已登录管理员id
     *
     * @author konakona
     * @version $Id$
     * @copyright konakona, 31 十月, 2011
     * @package common
     **/
    protected function getAdminId(){
        return $this->admin_infomation['admin_id'];
    }

    /**
     * 获取用户的登录信息
     * 如果没有登录则返回false
     * @return array
     */
    protected function getAdminInfomation(){
        $user_data = Session::get("userdata");
        var_dump($user_data);
        if(is_null($user_data)) return false;
        $this->admin_infomation = $user_data;
    }



}
