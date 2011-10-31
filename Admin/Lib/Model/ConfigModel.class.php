<?php
/**
 * NBUT Online Judge System
 *
 * 网站配置模型
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package Config
 */
class ConfigModel extends CommonModel
{
    /**
     * @param $key
     * @return string 返回相应配置值
     */
    public function get_value($key)
    {
        if ($key == "") return false;

        $result = $this->where(array("key" => $key))->find();

        if (!is_null($result)) return $result["value"];
        else return "";
    }
}
