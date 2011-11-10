<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-10 下午5:05
 */

/**
 * @brief ProblemAction
 * 题库相关
 */
class ProblemAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新增题目
     * @return void
     */
    public function add()
    {
        $this->web_config["title"] .= " 添加题目";

        /** 所属分类 */
        $this->web_config["action_class"] = "problem";
        $this->web_config["sub_action"] = "new";

        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->display();
    }

    /**
     * 检验新增题目
     * @return void
     */
    public function chkadd()
    {
        /** TODO: 小熊帮忙放到oj_problem去吧 - -，我真不知道用ajax好呢还是什么好。 */
        /** addtime就是time()就好了。 */
    }
}
