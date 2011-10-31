<?php
/**
 * NBUT Online Judge System
 *
 * 后台主控制器
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package Do
 */

/**
 * @brief DoAction
 * 后台主控制器，包括基本上的后台操作。
 */
class DoAction extends CommonAction
{
    private function init()
    {
        /** TODO: 一些初始化操作。 */
    }

    public function __construct()
    {
        /** 基类构造函数 */
        parent::__construct();
    }

    /**
     * 后台首页
     * @return void
     */
    public function index()
    {
        dump($this->admin_infomation);
    }
}
