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
     * @param $username 用户名
     * @param $password 密码
     * @return null|string 是否登录成功。若为null则登录成功，否则返回错误信息。
     */
    public function login($username, $password)
    {
        if($username == "" || $password == "")
        {
            return "Username or password can't be blank.";
        }
        if(strlen($username) > 32 || strlen($password) > 16)
        {
            return "Username or password is too long.";
        }
        $password = md5($password);

        /** 从数据库验证数据 */
        $condition = array("username" => $username, "password" => $password);
        $result = $this->join("{$this->PREFIX}role ON {$this->PREFIX}user.roleid = {$this->PREFIX}role.roleid")->where($condition)->select();

        /** 若信息错误 */
        if(0 == count($result))
        {
            return "Username or password is wrong.";
        }

        /** 设置登录信息 */
        $session_data = $result[0]["roleid"];
        $session_data .= "|{$result[0]["rolename"]}";
        $session_data .= "|{$result[0]["userid"]}";
        $session_data .= "|{$result[0]["username"]}";
        $session_data .= "|{$result[0]["email"]}";
        $session_data .= ("|" . time());

        /** Session */
        Session::set("userdata", $this->Encryption->Encode($session_data, C("ENCRYPTION_KEY")));

        /** 返回 */
        return null;
    }

    /**
     * 检测是否用户已登录
     * @version $Id$
     * @return array|null 返回登录用户数据数组，若未登录则返回null
     */
    public function check_online()
    {
        /** 获取用户数据 */
        $user_data = Session::get("userdata");

        /** 未登录 */
        if(null == $user_data || "" == $user_data) return null;

        /** 获取SESSION的真实数据 */
        $user_data_array = explode("|", $this->Encryption->Decode($user_data, C("ENCRYPTION_KEY")));

        /** 获取时间 */
        $result["logintime"] = $user_data_array[5];

        /** 超时 */
        if(time() - $result["logintime"] > $this->MaxLoginTime)
        {
            Session::clear();
            return null;
        }

        /** 获取用户信息 */
        $result = $this->get_user_info("userid", $user_data_array[2]);
        if(false == $result)
        {
            Session::clear();
            return null;
        }

        /** 额外信息 */
        $result = $result[0];
        $result["rolename"] = $user_data_array[1];
        $result["avatar"] = $this->get_avatar_url($result["email"], "");
        $result["logintime"] = $user_data_array[5];
        $result["logintime_formatted"] = date("Y-m-d H:i:s", $result["logintime"]);

        /** 更新时间 */
        $user_data_array[5] = time();
        $session_data = implode("|", $user_data_array);
        Session::set("userdata", $this->Encryption->Encode($session_data, C("ENCRYPTION_KEY")));

        /** 返回结果 */
        return $result;
    }

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
     * 获取Gravatar的头像链接地址
     * @param $email
     * @param int $size
     * @return string 链接地址
     */
    public function get_avatar_url($email, $size = 64)
    {
        return "http://1.gravatar.com/avatar/" . md5(strtolower($email)) . ".jpg?d=mm&size=" . $size;
    }
}
