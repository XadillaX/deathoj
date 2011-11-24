<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-15 下午3:57
 */

/**
 * @brief SubmitModel
 * 运行时信息
 */
class SubmitModel extends CommonModel
{
    /**
     * 新运行状态
     * @param $submitid
     * @param $contestid
     * @param $index
     * @param $userid
     * @param $code
     * @param $language_id
     * @return bool|int 若插入成功，则返回主键id，否则返回false
     */
    public function new_submit($submitid, $contestid, $index, $userid, $code, $language_id)
    {
        $data = array(
            "submitid" => $submitid,
            "contestid" => $contestid,
            "index" => $index,
            "userid" => $userid,
            "length" => strlen($code),
            "languageid" => $language_id,

            "submittime" => time(),
            "resultid" => 0                 ///< QUEUING...
        );

        /** 插入新运行状态 */
        $result = $this->add($data);
        if(false != $result)
        {
            /** 插入新代码 */
            $code_model = new Model("code");
            $code_data = array(
                "totsubmitid" => $result,
                "code" => $code
            );
            $code_model->add($code_data);

            return $result;
        }
        else return false;
    }

    /**
     * 根据分页信息获取运行结果
     * @param $contestid
     * @param $page
     * @param $per_page
     * @return array|bool
     */
    public function get_submit_by_page($contestid, $page, $per_page)
    {
        $PREFIX = C("DB_PREFIX");
        $condition = array("contestid" => $contestid);

        return $this->where($condition)
                ->join("{$PREFIX}result ON {$PREFIX}result.resultid = {$PREFIX}submit.resultid")
                ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
                ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
                ->limit((($page - 1) * $per_page) . ", " . $per_page)
                ->order("`submitid` desc")
                ->select();
    }

    /**
     * 获取某场比赛的总提交数
     * @param $contestid
     * @return int
     */
    public function get_count($contestid)
    {
        $condition = array("contestid" => $contestid);

        return $this->where($condition)->count();
    }
}
