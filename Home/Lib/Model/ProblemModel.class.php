<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-13 下午7:44
 */

/**
 * @brief ProblemModel
 * 题库模型
 */
class ProblemModel extends CommonModel
{
    /**
     * 添加题目
     * @param $data
     * @return bool|int 若添加失败则返回false，否则返回主键id
     */
    public function add_problem($data)
    {
        $data["addtime"] = time();
        $data["inputmd5"] = md5($data["addtime"]);
        $data["outputmd5"] = md5($data["addtime"]);

        return $this->add($data);
    }

    public function edit_problem($id, $data)
    {
        $data["addtime"] = time();
        return $this->where(array("problemid" => $id))->save($data);
    }

    /**
     * 获取当前分页的数据
     * @param $page_num
     * @param $per_page
     * @return array 分页数据
     */
    public function get_catalog($page_num, $per_page)
    {
        $tot_num = $this->count();
        $first_row = $per_page * ($page_num - 1);

        return $this->limit($first_row . ", " . $per_page)->order("problemid desc")->select();
    }

    /**
     * 根据题目ID获取题目信息
     * @version $Id$
     * @param $problemid
     * @return bool|array
     */
    public function get_problem_by_id($problemid)
    {
        $condition = array("problemid" => $problemid);
        $result = $this->where($condition)->select();
        if(false == $result) return false;
        else return $result[0];
    }
}
