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

    public function is_user_joined($contestid, $userid)
    {
        $condition = array("contestid" => $contestid, "userid" => $userid);
        $result = $this->where($condition)->select();
        
        return $result;
    }

    public function del_all($contestid)
    {
        $condition = array("contestid" => $contestid);
        return $this->where($condition)->delete();
    }

    /**
     * 验证队名唯一性
     * @param $contestid
     * @param $teamname
     * @return bool
     */
    public function validate_unique($contestid, $teamname)
    {
        $condition = array("contestid" => $contestid, "teamname" => $teamname);
        $res = $this->where($condition)->select();

        if(count($res) == 0) return true;
        else return false;
    }

    public function signup($contestid, $userid, $teamname)
    {
        $data = array(
            "contestid" => $contestid,
            "userid" => $userid,
            "teamname" => $teamname
        );

        $result = $this->add($data);
        return $result;
    }

    /**
     * 获取队名
     * @param $contestid
     * @param $userid
     * @return string
     */
    public function get_teamname($contestid, $userid)
    {
        $condition = array(
            "contestid" => $contestid,
            "userid" => $userid
        );

        $res = $this->where($condition)->select();
        if(count($res) == 0) return "";
        else return $res[0]["teamname"];
    }
}
