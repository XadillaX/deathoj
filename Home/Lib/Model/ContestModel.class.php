<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-14 上午9:21
 */

/**
 * @brief ContestModel
 * 比赛模型
 */
class ContestModel extends CommonModel
{
    /**
     * 获取比赛详细信息
     * @param $contestid
     * @return bool|array
     */
    public function get_contest_info($contestid)
    {
        $condition = array("contestid" => $contestid);
        $result = $this->where($condition)->select();

        if(false == $result) return false;
        else
        {
            $result[0]["title"] = $this->HtmlEncode($result[0]["title"]);
            return $result[0];
        }
    }

    /**
     * 获取下一个submitid
     * @param $contestid
     * @return bool|int
     */
    public function get_next_submitid($contestid)
    {
        $condition = array("contestid" => $contestid);
        $result = $this->where($condition)->select();

        if(false == $result)
        {
            return false;
        }
        else
        {
            $data["submit"] = array("exp", "`submit` + 1");
            $this->where($condition)->save($data);
            
            return $result[0]["submit"];
        }
    }

    /**
     * 添加比赛
     * @version $Id$
     * @param array $data
     * @return bool|int
     */
    public function add_contest($data)
    {
        $result = $this->add($data);
        return $result;
    }

    /**
     * 根据分页获取比赛目录
     * @version $Id$
     * @param $page_num
     * @param $per_page
     * @return
     */
    public function get_catalog($page_num, $per_page)
    {
        $tot_num = $this->count();
        $first_row = $per_page * ($page_num - 1);

        return $this->limit($first_row . ", " . $per_page)->order("contestid desc")->select();
    }

    /**
     * 编辑比赛
     * @param $id
     * @param $data
     * @return bool
     */
    public function edit_contest($id, $data)
    {
        $data["addtime"] = time();
        return $this->where(array("contestid" => $id))->save($data);
    }
}
