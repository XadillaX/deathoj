<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-14 上午9:22
 */

/**
 * @brief ContestProblem
 * 赛题模型
 */
class ContestProblemModel extends CommonModel
{
    /**
     * 获取某场比赛（习题）的所有题目资料
     * @version $Id$
     * @param $contestid
     * @return bool|array
     */
    public function get_all_problems($contestid, $order = null)
    {
        $condition = array("contestid" => $contestid);
        $result = $this->where($condition)->order($order == null ? "`index` asc" : $order)->select();

        return $result;
    }

    /**
     * 根据分页信息获取某场比赛题目信息
     * @version $Id$
     * @param $contestid
     * @param $pagenum
     * @param $perpage
     * @param null $order
     * @return bool|array
     */
    public function get_problems_by_page($contestid, $pagenum, $perpage, $order = null)
    {
        $PREFIX = C("DB_PREFIX");
        $condition = array("contestid" => $contestid);
        $result = $this->join("{$PREFIX}problem ON {$PREFIX}problem.problemid = {$PREFIX}contestproblem.problemid")->where($condition)->limit(($pagenum - 1) * $perpage . ", " . $perpage)->order($order == null ? "`index` asc" : $order)->select();

        return $result;
    }

    /**
     * 根据主键获取题目信息
     * @version $Id$
     * @param $contestproblemid
     * @return bool|array
     */
    public function get_problem_by_id($contestproblemid)
    {
        $condition = array("contestproblemid" => $contestproblemid);
        $result = $this->where($condition)->select();

        if(false == $result) return $result;
        else return $result[0];
    }

    /**
     * 根据index获取信息
     * @param $contestid
     * @param $index
     * @return bool|array
     */
    public function get_problem_by_index($contestid, $index)
    {
        $PREFIX = C("DB_PREFIX");
        $condition = array("contestid" => $contestid, "index" => $index);
        $result = $this->join("{$PREFIX}problem ON {$PREFIX}problem.problemid = {$PREFIX}contestproblem.problemid")->where($condition)->select();

        if($result == false) return false;
        else
        {
            $result[0]["input"] = $this->HtmlEncode($result[0]["input"]);
            $result[0]["output"] = $this->HtmlEncode($result[0]["output"]);
            $result[0]["description"] = str_replace('&nbsp;', chr(32), $result[0]["description"]);

            return $result[0];
        }
    }

    /**
     * 添加题目到题库中
     * @version $Id$
     * @param $contestid
     * @param $index
     * @param $problemid
     * @return bool|int
     */
    public function add_problem($contestid, $index, $problemid)
    {
        $data = array(
            "contestid" => $contestid,
            "index" => $index,
            "problemid" => $problemid,
            "submit" => 0,
            "solved" => 0
        );

        return $this->add($data);
    }

    /**
     * 修改题目信息
     * @version $Id$
     * @param $contestid
     * @param $contestproblemid
     * @param $index
     * @param $problemid
     * @return bool
     */
    public function edit_problem($contestid, $contestproblemid, $index, $problemid)
    {
        $condition["contestproblemid"] = $contestproblemid;
        $data = array(
            "index" => $index,
            "problemid" => $problemid
        );
        return $this->where($condition)->save($data);
    }

    /**
     * 新增一个submit
     * @param $contestid
     * @param $index
     * @return bool
     */
    public function add_submit($contestid, $index)
    {
        $condition["contestid"] = $contestid;
        $condition["index"] = $index;
        $data["submit"] = array("exp", "`submit` + 1");

        return $this->where($condition)->save($data);
    }
}
