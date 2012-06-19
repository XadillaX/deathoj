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

    /**
     * 构造函数
     * @version $Id$
     * @param string $name
     * @param string $connection
     */
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
     * ↑这货被删掉了 - -|||
     * @return array|null 返回登录用户数据数组，若未登录则返回null
     */

    /**
     * 新建用户
     * @version $Id$
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
            "motto" => $motto,
            "regtime" => time()
        );

        $result = $this->add($condition);
        return $result;
    }

    /**
     * 根据键值对获取用户信息
     * @version $Id$
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
        //$condition["password"] = ($already_md5) ? $password : md5($password);

        /** XadillaX+ $Id$ */
        if($already_md5) $condition["password"] = $password;
        else
        {
            $md5_p = md5($password);
            $condition["password"] = array("exp", "IN('{$password}', '{$md5_p}')");
        }

        /** 搜索数据库 */
        $result = $this->join("{$this->PREFIX}role ON {$this->PREFIX}user.roleid = {$this->PREFIX}role.roleid")->where($condition)->select();

        /** 返回结果 */
        if(false != $result) return $result[0];
        else return false;
    }

    /**
     * 获取Gravatar的头像链接地址
     * @version $Id$
     * @param $email
     * @param int $size
     * @return string 链接地址
     */
    public function get_avatar_url($email, $size = 64)
    {
        return "http://1.gravatar.com/avatar/" . md5(strtolower($email)) . ".jpg?d=identicon&size=" . $size . "&seed=" . time();
    }

    /**
     * 新增submit
     * @version $Id$
     * @param $userid
     * @return bool
     */
    public function add_submit($userid)
    {
        $condition = array("userid" => $userid);
        $data["submit"] = array("exp", "submit + 1");
        $data["submitnum"] = array("exp", "submitnum + 1");

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

    /**
     * 获取提交列表
     * @version $Id$
     * @param $userid
     * @return string
     */
    public function get_submit_list($userid)
    {
        $condition = array("userid" => $userid);

        return $this->where($condition)->getField("submitlist");
    }

    /**
     * 修改提交列表
     * @verson $Id$
     * @param $userid
     * @param $list
     * @return bool
     */
    public function modify_submit_list($userid, $list)
    {
        $condition = array("userid" => $userid);
        $data["submitlist"] = $list;

        return $this->where($condition)->save($data);
    }

    /**
     * 根据条件获取用户信息（分页）
     * @version $Id$
     * @param $condition
     * @param $page
     * @param $per_page
     * @param string $order
     * @return array|bool
     */
    public function get_user_by_page($condition, $page, $per_page, $order = "userid ASC")
    {
        $condition["roleid"] = array("neq", -100);
        $data = $this->where($condition)
                ->limit((($page - 1) * $per_page) . ", " . $per_page)
                ->order($order)
                ->select();

        return $data;
    }

    /**
     * 根据用户id获取用户信息
     * @version $Id$
     * @param $userid
     * @return array|NULL
     */
    public function get_user_by_id($userid)
    {
        $condition = array("userid" => $userid);
        return $this->where($condition)->find();
    }

    /**
     * 保存头像栏状态
     * @param $userid
     * @param $state
     * @return bool
     */
    public function save_avatar_state($userid, $state)
    {
        $condition = array("userid" => $userid);
        return $this->where($condition)->save(array("avatarbar" => $state));
    }

    /**
     * 新建比赛的“临时队伍用户”
     * @param $username
     * @param $password
     * @return int|bool
     */
    public function create_team_user($username, $password, $teamname)
    {
        $data = array(
            "username" => $username,
            "password" => $password,
            "nickname" => $teamname,
            "regtime" => time(),
            "roleid" => -100                    ///< CONTESTUSER比赛用户
        );

        $this->create();
        return $this->add($data);
    }
}
