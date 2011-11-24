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
        else return $result[0];
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
}
