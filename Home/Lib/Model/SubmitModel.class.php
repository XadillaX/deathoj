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
     * 获取提交信息
     * @version $Id$
     * @param $contestid
     * @param $submitid
     * @param $userid
     * @return bool|array
     */
    public function get_submit_info($contestid, $submitid)
    {
        $PREFIX = C("DB_PREFIX");
        $condition = array("contestid" => $contestid, "submitid" => $submitid);

        $data = $this->where($condition)
                ->join("{$PREFIX}result ON {$PREFIX}result.resultid = {$PREFIX}submit.resultid")
                ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
                ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
                ->join("{$PREFIX}runtimeerror ON {$PREFIX}runtimeerror.totsubmitid = {$PREFIX}submit.totsubmitid")
                ->join("{$PREFIX}code ON {$PREFIX}code.totsubmitid = {$PREFIX}submit.totsubmitid")
                ->select();
        
        if(false == $data) return false;
        else return $data[0];
    }

    public function get_best_solution($contestid, $page, $per_page, $index)
    {
        $PREFIX = C("DB_PREFIX");
        
        $condition["contestid"] = $contestid;
        $condition["index"] = $index;
        $condition["{$PREFIX}submit.resultid"] = 3;

        $user = $PREFIX . "user";
        $result = $PREFIX . "result";
        $language = $PREFIX . "language";
        $runtimeerror = $PREFIX . "runtimeerror";

        //$data = $this->where($condition)
        //        ->join("{$PREFIX}result ON {$PREFIX}result.resultid = {$PREFIX}submit.resultid")
        //        ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
        //        ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
        //        ->join("{$PREFIX}runtimeerror ON {$PREFIX}runtimeerror.totsubmitid = {$PREFIX}submit.totsubmitid")
        //        ->limit((($page - 1) * $per_page) . ", " . $per_page)
        //        ->order("`time` ASC, `memory` ASC, `length` ASC")
        //        ->group("{$PREFIX}submit.userid")
        //        ->select();

        $SQL = "SELECT * FROM ((SELECT * FROM `{$PREFIX}submit` WHERE `contestid` = '{$condition['contestid']}' AND `index` = '{$condition["index"]}' AND `resultid` = 3 ORDER BY `time` ASC, `memory` ASC, `length` ASC) AS temp_table)"
               . " LEFT JOIN {$result} ON {$result}.resultid = temp_table.resultid"
               . " LEFT JOIN {$user} ON {$user}.userid = temp_table.userid"
               . " LEFT JOIN {$language} ON {$language}.languageid = temp_table.languageid"
               . " LEFT JOIN {$runtimeerror} ON {$runtimeerror}.totsubmitid = temp_table.totsubmitid"
               . " GROUP BY temp_table.`userid` ORDER BY `time` ASC, `memory` ASC, `length` ASC LIMIT " . ($page - 1) * $per_page . ", {$per_page}";

        $data = $this->query($SQL);

        //echo $this->getLastSql();
        return $data;
    }

    /**
     * 根据分页信息获取运行结果
     * @version $Id$
     * @param int $contestid
     * @param int $page
     * @param int $per_page
     * @param string $index
     * @param bool $is_ac 是否是ACCEPTED的
     * @param string $order
     * @return array|bool
     */
    public function get_submit_by_page($condition, $contestid, $page, $per_page, $index = "", $is_ac = false, $order = "")
    {
        $PREFIX = C("DB_PREFIX");
        $condition["contestid"] = $contestid;
        if($index != "") $condition["index"] = $index;
        if($is_ac === true) $condition["{$PREFIX}submit.resultid"] = 3;

        /** 消除前缀影响 */
        if(isset($condition["resultid"]))
        {
            $condition["{$PREFIX}submit.resultid"] = $condition["resultid"];
            unset($condition["resultid"]);
        }
        if(isset($condition["languageid"]))
        {
            $condition["{$PREFIX}submit.languageid"] = $condition["languageid"];
            unset($condition["languageid"]);
        }

        $data = array();
        if(!is_ac)
        {
            $data = $this->where($condition)
                    ->join("{$PREFIX}result ON {$PREFIX}result.resultid = {$PREFIX}submit.resultid")
                    ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
                    ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
                    ->join("{$PREFIX}runtimeerror ON {$PREFIX}runtimeerror.totsubmitid = {$PREFIX}submit.totsubmitid")
                    ->limit((($page - 1) * $per_page) . ", " . $per_page)
                    ->order((($order != "") ? "{$order}, " : "") . "`submitid` desc")
                    ->select();
        }
        else
        {
            $data = $this->where($condition)
                    ->join("{$PREFIX}result ON {$PREFIX}result.resultid = {$PREFIX}submit.resultid")
                    ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
                    ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
                    ->join("{$PREFIX}runtimeerror ON {$PREFIX}runtimeerror.totsubmitid = {$PREFIX}submit.totsubmitid")
                    ->limit((($page - 1) * $per_page) . ", " . $per_page)
                    ->order((($order != "") ? "{$order}, " : "") . "`submitid` desc")
                    ->select();
        }

        return $data;
    }

    /**
     * 获取某场比赛的总提交数
     * @version $Id$
     * @param $contestid
     * @return int
     */
    public function get_count($contestid, $condition = null)
    {
        $PREFIX = C("DB_PREFIX");
        if(null == $condition) $condition = array();
        $condition["contestid"] = $contestid;

        /** 消除前缀影响 */
        if(isset($condition["resultid"]))
        {
            $condition["{$PREFIX}submit.resultid"] = $condition["resultid"];
            unset($condition["resultid"]);
        }
        if(isset($condition["languageid"]))
        {
            $condition["{$PREFIX}submit.languageid"] = $condition["languageid"];
            unset($condition["languageid"]);
        }

        return $this->where($condition)
                ->join("{$PREFIX}user ON {$PREFIX}user.userid = {$PREFIX}submit.userid")
                ->join("{$PREFIX}language ON {$PREFIX}language.languageid = {$PREFIX}submit.languageid")
                ->count();
    }

    /**
     * 获取问题的提交状态统计
     * @param $index
     * @version $Id$
     * @param int $contestid
     * @return
     */
    public function get_statistic($index, $contestid = 1)
    {
        $PREFIX = C("DB_PREFIX");
        $submit = $PREFIX . "submit";
        $result = $PREFIX . "result";

        $SQL = "SELECT count(*) AS count, {$result}.resultid, {$result}.result FROM `{$result}` LEFT JOIN `$submit` ON {$submit}.resultid = {$result}.resultid WHERE {$submit}.index = '{$index}' AND" .
               " {$submit}.contestid = {$contestid} GROUP BY {$result}.resultid";
        $data = $this->query($SQL);

        return $data;
    }

    public function del_all($contestid)
    {
        $condition = array("contestid" => $contestid);
        return $this->where($condition)->delete();
    }
}
