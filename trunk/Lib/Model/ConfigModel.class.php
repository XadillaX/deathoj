<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:32
 */

/**
 * @brief ConfigModel
 * 配置信息模型
 */
class ConfigModel extends Model
{
    public function __construct($name = "", $connection = "")
    {
        parent::__construct();
    }

    /**
     * @param $key
     * @return string 返回相应配置值
     */
    public function get_value($key)
    {
        $condition = array("key" => $key);
        $result = $this->where($condition)->select();

        if (0 == count($result)) return "";
        else
        {
            return $result[0]["value"];
        }
    }

    public function set_value($key)
    {

    }
}
