<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午6:13
 * Config Model
 */
 
class ConfigModel extends Model {
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

        if(0 == count($result)) return "";
        else
        {
            return $result[0]["value"];
        }
    }
}
