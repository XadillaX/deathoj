<?php
/**
 * NBUT Online Judge System
 *
 * 网站配置模型
 * @author XadillaX(admin@xcoder.in)
 * @version $Id: ConfigModel.class.php 17 2011-10-31 10:34:13Z zukaidi@163.com $
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
