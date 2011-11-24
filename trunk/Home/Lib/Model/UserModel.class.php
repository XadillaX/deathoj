<?php
/**
 * NBUT Online Judge System
 *
 * 用户模型
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package User
 */
class UserModel extends CommonModel {
    private $Encryption;
    private $MaxLoginTime = 1800;
    private $PREFIX;

    public function __construct($name = "", $connection = "")
    {
        parent::__construct($name, $connection);
        $this->PREFIX = C("DB_PREFIX");

        /** 导入加密类 */
        import("@.Plugin.XHaffmanSec");

        $this->Encryption = new XHaffman();
    }

    /**
     * 用户登录
     * @version $Id$
     * 被删掉了果 - -|||
     */

    /**
     * 检测是否用户已登录
     * @version $Id$
     * @version $Id$
     * ↑这货被删掉了 - -|||
     * @return array|null 返回登录用户数据数组，若未登录则返回null
     */

    /**
     * 新建用户
     * @param $username
     * @param $password
     * @param $nickname
     * @param $email
     * @param $school
     * @param $motto
     * @return bool|int false或者用户id
     */
    public function create_user($username, $password, $nickname, $email, $school, $motto)
    {
        $condition = array(
            "username" => $username,
            "password" => $password,
            "nickname" => $nickname,
            "email" => $email,
            "school" => $school,
            "motto" => $motto
        );

        $result = $this->add($condition);
        return $result;
    }

    /**
     * 根据键值对获取用户信息
     * @param $key
     * @param $value
     * @return array
     */
    public function get_user_info($key, $value)
    {
        $condition[$key] = $value;
        return $this->where($condition)->select();
    }

    /**
     * 根据用户名密码搜索用户
     * @version $Id$
     * @param $username
     * @param $password
     * @param bool $already_md5
     * @return bool|array 若条件匹配，则返回用户信息，否则返回false
     */
    public function check_username_and_password($username, $password, $already_md5 = false)
    {
        /** 条件数组 */
        $condition["username"] = $username;
        $condition["password"] = ($already_md5) ? $password : md5($password);

        /** 搜索数据库 */
        $result = $this->join("{$this->PREFIX}role ON {$this->PREFIX}user.roleid = {$this->PREFIX}role.roleid")->where($condition)->select();

        /** 返回结果 */
        if(false != $result) return $result[0];
        else return false;
    }

    /**
     * 获取Gravatar的头像链接地址
     * @param $email
     * @param int $size
     * @return string 链接地址
     */
    public function get_avatar_url($email, $size = 64)
    {
        return "http://1.gravatar.com/avatar/" . md5(strtolower($email)) . ".jpg?d=mm&size=" . $size;
    }

    /**
     * 新增submit
     * @param $userid
     * @return bool
     */
    public function add_submit($userid)
    {
        $condition = array("userid" => $userid);
        $data["submit"] = array("exp", "submit + 1");

        return $this->where($condition)->save($data);
    }

    /**
     * 修改用户默认语言
     * @version $Id$
     * @param $userid
     * @param $languageid
     * @return bool
     */
    public function change_default_language($userid, $languageid)
    {
        $condition = array("userid" => $userid);
        $data["language"] = $languageid;

        return $this->where($condition)->save($data);
    }
}
