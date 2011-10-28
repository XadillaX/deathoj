<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午5:02
 * Do Action of Administrator
 */

class DoAction extends Action
{
    private $MUser;
    private $admin_data;

    private function init()
    {

    }

    private function check_online()
    {
        $this->admin_data = $this->MUser->check_online();

        return null != $this->admin_data;
    }

    public function __construct()
    {
        parent::__construct();

        $this->MUser = new UserModel("user");

        if(ACTION_NAME != "login" && ACTION_NAME != "chklogin")
        {
            if(!$this->check_online())
            {
                redirect(U("security/login"));
                die();
            }
        }
    }

    /**
     * 后台首页
     * @return void
     */
    public function index()
    {
        dump($this->admin_data);
    }
}
