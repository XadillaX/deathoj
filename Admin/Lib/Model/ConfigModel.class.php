<?php
/**
 * 获取网站配置
 * User: konakona
 * Date: 11-10-31
 * Time: 下午2:08
 * $Id$
 */

class ConfigModel extends CommonModel{

    /**
     * @param $key
     * @return string 返回相应配置值
     */
    public function get_value($key){
        if($key == "") return false;

        $result = $this->where(array("key" => $key))->find();

        if(is_null($result)) return $result["value"];
    }
}
