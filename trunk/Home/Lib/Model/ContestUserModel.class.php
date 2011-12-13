<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-12-10 上午12:12
 */

/**
 * @brief ContestUserModel
 * TODO: 比赛用户模型
 */
class ContestUserModel extends CommonModel
{
    /**
     * 获取比赛用户列表
     * @version $Id$
     * @param $contestid
     * @return
     */
    public function get_user_list($contestid)
    {
        $PREFIX = C("DB_PREFIX");
        $condition = array("contestid" => $contestid);

        $data = $this->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}contestuser.userid")->where($condition)->order("{$PREFIX}contestuser.userid asc")->select();
        return $data;
    }

    /**
     * 添加用户数据
     * @param $contestid
     * @param $data
     * @return bool
     */
    public function add_user_list($contestid, $data)
    {
        for($i = 0; $i < count($data); $i++)
        {
            $data[$i]["contestid"] = $contestid;
        }

        $result = $this->addAll($data);
        return $result;
    }
}
