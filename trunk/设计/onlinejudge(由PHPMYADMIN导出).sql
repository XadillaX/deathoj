-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 11 月 24 日 04:27
-- 服务器版本: 5.1.41
-- PHP 版本: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `onlinejudge`
--

-- --------------------------------------------------------

--
-- 表的结构 `oj_code`
--

CREATE TABLE IF NOT EXISTS `oj_code` (
  `totsubmitid` int(11) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`totsubmitid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `oj_code`
--

INSERT INTO `oj_code` (`totsubmitid`, `code`) VALUES
(9, '#include <cstdio>\n\nint mon[2][12] = {\n    { 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 },\n    { 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 }\n};\n\nint IsBig(int y)\n{\n    if(y % 4 == 0)\n    {\n        if(y % 100 != 0) return 1;\n        if(y % 400 == 0) return 1;\n    }\n\n    return 0;\n}\n\nint main()\n{\n    int T;\n    scanf("%d", &T);\n    while(T--)\n    {\n        int y, m, d, big;\n        scanf("%d%d%d", &y, &m, &d);\n\n        int sum = 0;\n        for(int i = 1; i < y; i++)\n        {\n            big = IsBig(i);\n            sum += big ? 366 : 365;\n        }\n\n        big = IsBig(y);\n        for(int i = 1; i < m; i++)\n        {\n            sum += mon[big][i - 1];\n        }\n\n        sum += d;\n\n        printf("%d\\n", sum);\n    }\n\n    return 0;\n}\n'),
(7, '#include <iostream>\n#include <string>\n#include <cstdio>\nusing namespace std;\n\nint main()\n{\n    int n;\n    scanf("%d\\n", &n);\n\n    while(n--)\n    {\n        string line;\n\n        getline(cin, line);\n        if(line.find("0000000") != string::npos || line.find("1111111") != string::npos)\n            printf("YES\\n");\n        else printf("NO\\n");\n    }\n\n    return 0;\n}\n'),
(8, '#include <cstdio>\n#include <cstdlib>\n#include <iostream>\n#include <string>\n#include <vector>\n#include <algorithm>\n#include <map>\nusing namespace std;\n\nclass kill\n{\npublic:\n    kill(string source, string target, string t) : source(source), target(target)\n    {\n        int pos = t.find(":");\n        min = atoi(t.substr(0, pos).c_str());\n        sec = atoi(t.substr(pos + 1).c_str());\n    }\n    ~kill() {}\n    int GetTick()\n    {\n        return min * 60 + sec;\n    }\n    const char* GetSource() { return source.c_str(); }\n    const char* GetTarget() { return target.c_str(); }\n\nprivate:\n    int min;\n    int sec;\n    string source;\n    string target;\n};\n\n\nbool cmp(kill a, kill b)\n{\n    return a.GetTick() < b.GetTick();\n}\n\nstruct kE\n{\n    vector<kill> arr;\n    int kills;\n\n    kE()\n    {\n        arr.clear();\n        kills = 0;\n    }\n};\n\nvector<kill> arr;\nmap<string, kE> killEx;\n\nstring timeKill[] = {\n    "", "", "Double Kill", "Triple Kill", "Ultra Kill", "Rampage"\n};\nstring KillKill[] = {\n    "", "", "", "Killing Spree", "Dominating", "Mega Kill", "Unstoppable", "Wicked Sick",\n    "M-m-m-m...onster Kill", "Godlike", "Beyond Godlike"\n};\n\nint main()\n{\n    int T;\n    int n;\n    char s[128], t[128], tm[128];\n    scanf("%d", &T);\n    while(T--)\n    {\n        arr.clear();\n        killEx.clear();\n        scanf("%d", &n);\n        for(int i = 0; i < n; i++)\n        {\n            scanf("%s kill %s in %s", s, t, tm);\n            kill k(s, t, tm);\n            arr.push_back(k);\n        }\n        sort(arr.begin(), arr.end(), cmp);\n\n        bool fb = false;\n        for(int i = 0; i < n; i++)\n        {\n            if(killEx.find(arr[i].GetSource()) == killEx.end())\n            {\n                killEx[arr[i].GetSource()].arr.clear();\n                killEx[arr[i].GetSource()].kills = 0;\n            }\n\n            if(killEx.find(arr[i].GetTarget()) == killEx.end())\n            {\n                killEx[arr[i].GetTarget()].arr.clear();\n                killEx[arr[i].GetTarget()].kills = 0;\n            }\n\n            if(string(arr[i].GetSource()) == string(arr[i].GetTarget()))\n            {\n                killEx[arr[i].GetSource()].arr.clear();\n                killEx[arr[i].GetSource()].kills = 0;\n                continue;\n            }\n\n            if(!fb)\n            {\n                printf("%s has First Blood\\n", arr[i].GetSource());\n                killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n                killEx[arr[i].GetSource()].kills = 1;\n                fb = true;\n\n                continue;\n            }\n\n            killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n            killEx[arr[i].GetSource()].kills++;\n            killEx[arr[i].GetTarget()].arr.clear();\n            killEx[arr[i].GetTarget()].kills = 0;\n\n            string name = arr[i].GetSource();\n            if(killEx[name].arr.size() >= 2 && killEx[name].arr[killEx[name].arr.size() - 1].GetTick() - killEx[name].arr[killEx[name].arr.size() - 2].GetTick() > 10)\n            {\n                killEx[name].arr.clear();\n                killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n            }\n\n            if(killEx[name].arr.size() >= 2)\n            {\n                printf("%s has %s\\n", name.c_str(), killEx[name].arr.size() < 5 ? timeKill[killEx[name].arr.size()].c_str() : timeKill[5].c_str());\n            }\n\n            if(killEx[name].kills >= 3)\n            {\n                printf("%s is %s\\n", name.c_str(), killEx[name].kills < 10 ? KillKill[killEx[name].kills].c_str() : KillKill[10].c_str());\n            }\n        }\n    }\n\n    return 0;\n}\n'),
(6, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n    int n;\n    scanf("%d", &n);\n\n    while(n--)\n    {\n        char s;\n        char a[5], b[5];\n\n        int rate[128];\n        rate[''6''] = 1, rate[''7''] = 2, rate[''8''] = 3;\n        rate[''9''] = 4, rate[''T''] = 5, rate[''J''] = 6;\n        rate[''Q''] = 7, rate[''K''] = 8, rate[''A''] = 9;\n\n        scanf("\\n%c", &s);\n        scanf("%s%s", a, b);\n\n        //printf("%c\\n%s %s\\n", s, a, b);\n\n        if(a[1] == s && b[1] != s) printf("YES\\n");\n        else\n        if(a[1] != s && b[1] == s) printf("NO\\n");\n        else\n        {\n            printf("%s\\n", (rate[a[0]] > rate[b[0]]) ? "YES" : "NO");\n        }\n    }\n\n    return 0;\n}\n'),
(3, '#include <cstdio>\n#include <cstring>\n#include <algorithm>\n\nconst int INF = 2000000000;\nint ans[205][205];\n\nint calc(int a, int b, int c, int p[])\n{\n    return p[a] * p[b + 1] * p[c + 1];\n}\n\nint mul(int l, int r, int p[])\n{\n    if(l == r) return 0;\n    if(ans[l][r] > 0) return ans[l][r];\n\n    int mininum = INF;\n    for(int k = l; k < r; k++)\n    {\n        mininum = std::min(mininum, mul(l, k, p) + mul(k + 1, r, p) + calc(l, k, r, p));\n    }\n    ans[l][r] = mininum;\n\n    return mininum;\n}\n\nint main()\n{\n    int n;\n    int p[205];\n\n    while((scanf("%d", &n)) != EOF)\n    {\n        memset(ans, 0, sizeof(ans));\n\n        for(int i = 0; i <= n; i++) scanf("%d", p + i);\n        printf("%d\\n", mul(0, n - 1, p));\n    }\n\n    return 0;\n}\n'),
(4, '#include <iostream>\n#include <string>\n#include <cstdio>\nusing namespace std;\n\nint n;\nint m;\nchar enter;\nchar shield[105][30];\nchar rule[30];\nstring content;\n\nbool blank(char c)\n{\n    if(c == '' '') return true;\n    if(c == ''?'') return true;\n    if(c == ''!'') return true;\n    if(c == ''.'') return true;\n    if(c == '','') return true;\n    if(c == ''"'') return true;\n    if(c == ''\\'''') return true;\n    return false;\n}\n\nstring sh(string str, string rule)\n{\n    if(rule == "default")\n    {\n        if(str.length() == 1) return "*";\n        else\n        if(str.length() == 2) return "**";\n        else\n        {\n            string tmp = str.substr(0, 1);\n            for(int i = 0; i < str.length() - 2; i++) tmp += "*";\n            tmp += str.substr(str.length() - 1);\n\n            return tmp;\n        }\n    }\n    else return rule;\n}\n\nstring lowercase(string a)\n{\n    int len = a.length();\n    for(int i = 0; i < len; i++)\n    {\n        if(a[i] >= ''A'' && a[i] <= ''Z'') a[i] = a[i] - ''A'' + ''a'';\n    }\n    return a;\n}\n\nstring pro(string content, string shield, string rule)\n{    \n    shield = lowercase(shield);\n    string content2 = lowercase(content);\n\n    int def_pos = 0;\n    int pos = content2.find(shield, def_pos);\n\n    while(pos != string::npos)\n    {\n        bool flag1 = false, flag2 = false;\n        if(pos == 0 || blank(content[pos - 1])) flag1 = true;\n        if(pos + shield.length() == content.length() || blank(content[pos + shield.length()])) flag2 = true;\n\n        if(flag1 && flag2)\n        {\n            content2 = content2.substr(0, pos) + sh(content2.substr(pos, shield.length()), rule) + content2.substr(pos + shield.length());\n            content = content.substr(0, pos) + sh(content.substr(pos, shield.length()), rule) + content.substr(pos + shield.length());\n        }\n        else def_pos = pos + shield.length();\n\n        pos = content2.find(shield, def_pos);\n    }\n\n    return content;\n}\n\nint main()\n{\n    scanf("%d", &n);\n    while(n--)\n    {\n        scanf("%d", &m);\n        for(int i = 0; i < m; i++)\n        {\n            scanf("%s", shield[i]);\n        }\n        scanf("%s\\n", rule);\n        getline(cin, content);\n        for(int i = 0; i < m; i++)\n        {\n            content = pro(content, shield[i], rule);\n        }\n        printf("%s\\n", content.c_str());\n    }\n    return 0;\n}\n'),
(5, '#include <cstdio>\n#include <cstring>\n#include <vector>\nusing namespace std;\n\nconst int ROWCOUNT = 10;\nconst int COLCOUNT = 9;\nenum type{\n    KING,\n    WARRIOR,\n    ELEPHANT,\n    KNIGHT,\n    VEHICLE,\n    GUN,\n    MAN\n};\n\nclass chequer\n{\npublic:\n    chequer(int x, int y, int camp, type flag, chequer** board) : x(x), y(y), camp(camp), flag(flag), board(board) {};\n    ~chequer() {};\n    type GetFlag() { return flag; }\n    int GetCamp() { return camp; }\n\n    bool InBoard(int y, int x)\n    {\n        if(y < 0 || y >= ROWCOUNT || x < 0 || x >= COLCOUNT) return false;\n        else return true;\n    }\n\n    virtual bool ToWin() { return false; }\n\nprotected:\n    int x, y;\n    type flag;\n    int camp;\n    chequer** board;\n};\n\nclass king : public chequer\n{\npublic:\n    king(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~king() {};\n\n    virtual bool ToWin()\n    {\n        if(camp == 0)\n        {\n            for(int i = y + 1; i < ROWCOUNT; i++)\n            {\n                if(NULL != board[i * COLCOUNT + x])\n                {\n                    if(board[i * COLCOUNT + x]->GetFlag() == KING) return true;\n                    else return false;\n                }\n            }\n        }\n        else\n        {\n            for(int i = y - 1; i >= 0; i--)\n            {\n                if(NULL != board[i * COLCOUNT + x])\n                {\n                    if(board[i * COLCOUNT + x]->GetFlag() == KING) return true;\n                    else return false;\n                }\n            }\n        }\n\n        return false;\n    }\n};\n\nclass knight : public chequer\n{\npublic:\n    knight(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~knight() {};\n\n    virtual bool ToWin()\n    {\n        static int dx[8][2] = {\n            { 0, -1 }, { 0, 1 },\n            { 1, 2 }, { 1, 2 },\n            { 0, -1 }, { 0, 1 },\n            { -1, -2 }, { -1, -2 }\n        };\n        static int dy[8][2] = {\n            { -1, -2 }, { -1, -2 },\n            { 0, -1 }, { 0, 1 },\n            { 1, 2 }, { 1, 2 },\n            { 0, -1 }, { 0, 1 }\n        };\n\n        for(int i = 0; i < 8; i++)\n        {\n            int ny = y + dy[i][0], nx = x + dx[i][0];\n\n            if(!InBoard(ny, nx)) continue;\n            if(NULL != board[ny * COLCOUNT + nx]) continue;\n\n            ny = y + dy[i][1], nx = x + dx[i][1];\n            if(!InBoard(ny, nx)) continue;\n\n            if(NULL != board[ny * COLCOUNT + nx] && board[ny * COLCOUNT + nx]->GetFlag() == KING && board[ny * COLCOUNT + nx]->GetCamp() != camp) return true;\n        }\n\n        return false;\n    }\n};\n\nclass vehicle : public chequer\n{\npublic:\n    vehicle(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~vehicle() {};\n\n    virtual bool ToWin()\n    {\n        for(int i = y - 1; i >= 0; i--)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = y + 1; i < ROWCOUNT; i++)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = x - 1; i >= 0; i--)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = x + 1; i < COLCOUNT; i++)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n\n        return false;\n    }\n};\n\nclass gun : public chequer\n{\npublic:\n    gun(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~gun() {};\n\n    virtual bool ToWin()\n    {\n        bool foot = false;\n        for(int i = y - 1; i >= 0; i--)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = y + 1; i < ROWCOUNT; i++)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = x - 1; i >= 0; i--)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = x + 1; i < COLCOUNT; i++)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n\n        return false;\n    }\n};\nclass man : public chequer\n{\npublic:\n    man(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~man() {};\n\n    virtual bool ToWin()\n    {\n        /** 因为不过河反正不能将军，所以懒得判断了 */\n        if(camp == 0)\n        {\n            if(InBoard(y + 1, x) && NULL != board[(y + 1) * COLCOUNT + x] && board[(y + 1) * COLCOUNT + x]->GetCamp() != camp && board[(y + 1) * COLCOUNT + x]->GetFlag() == KING)\n            {\n                return true;\n            }\n        }\n        else\n        {\n            if(InBoard(y - 1, x) && NULL != board[(y - 1) * COLCOUNT + x] && board[(y - 1) * COLCOUNT + x]->GetCamp() != camp && board[(y - 1) * COLCOUNT + x]->GetFlag() == KING)\n            {\n                return true;\n            }\n        }\n\n        if(InBoard(y, x - 1) && NULL != board[y * COLCOUNT + x - 1] && board[y * COLCOUNT + x - 1]->GetCamp() != camp && board[y * COLCOUNT + x - 1]->GetFlag() == KING)\n        {\n            return true;\n        }\n\n        if(InBoard(y, x + 1) && NULL != board[y * COLCOUNT + x + 1] && board[y * COLCOUNT + x + 1]->GetCamp() != camp && board[y * COLCOUNT + x + 1]->GetFlag() == KING)\n        {\n            return true;\n        }\n\n        return false;\n    }\n};\n\nint count;\nchequer* che[200];\nchequer* board[ROWCOUNT * COLCOUNT];\n\nint main()\n{\n    \n    int T;\n    scanf("%d", &T);\n    while(T--)\n    {\n        count = 0;\n        memset(board, 0, sizeof(board));\n        for(int i = 0; i < ROWCOUNT; i++)\n        {\n            for(int j = 0; j < COLCOUNT; j++)\n            {\n                int flag;\n                type realflag;\n                chequer *tmp;\n                scanf("%d", &flag);\n                \n                int camp = flag >= 17 ? 1 : 0;\n                if(flag >= 17) flag -= 16;\n                if(0 == flag) continue;\n\n                switch(flag)\n                {\n                case 1: realflag = KING; break;\n                case 2: case 3: realflag = WARRIOR; break;\n                case 4: case 5: realflag = ELEPHANT; break;\n                case 6: case 7: realflag = KNIGHT; break;\n                case 8: case 9: realflag = VEHICLE; break;\n                case 10: case 11: realflag = GUN; break;\n                default:\n                    {\n                        realflag = MAN;\n                        break;\n                    }\n                }\n\n                switch(realflag)\n                {\n                case KING: tmp = new king(j, i, camp, realflag, board); break;\n                case WARRIOR: tmp = new chequer(j, i, camp, realflag, board); break;\n                case ELEPHANT: tmp = new chequer(j, i, camp, realflag, board); break;\n                case KNIGHT: tmp = new knight(j, i, camp, realflag, board); break;\n                case VEHICLE: tmp = new vehicle(j, i, camp, realflag, board); break;\n                case GUN: tmp = new gun(j, i, camp, realflag, board); break;\n                case MAN: tmp = new man(j, i, camp, realflag, board); break;\n                default: break;\n                }\n\n                board[i * COLCOUNT + j] = tmp;\n                che[count++] = tmp;\n            }\n        }\n\n        bool win = false;\n        for(int i = 0; i < count; i++)\n        {\n            if(che[i]->ToWin())\n            {\n                win = true;\n                break;\n            }\n        }\n        printf("%s\\n", win ? "yes" : "no");\n    }\n\n    return 0;\n}\n'),
(2, '#include <iostream>\n#include <string>\n#include <algorithm>\n#include <cstdio>\n#include <stdlib.h>\nusing namespace std;\n\nstring result[400000];\nint cnt = 0;\n\nvoid searchTot(string rst)\n{\n    if(rst.length() == 6)\n    {\n        result[cnt++] = rst;\n        return;\n    }\n\n    for(char i = ''0''; i < ''7''; i++)\n    {\n        if(rst.length() > 1)\n        {\n            if(rst[rst.length() - 1] == i && rst[rst.length() - 2] == i) continue;\n        }\n        if(rst.length() >= 1 && abs(rst[rst.length() - 1] - i) > 4) continue;\n\n        searchTot(rst + i);\n    }\n}\n\nint mid(string str)\n{\n    if(result[0] >= str) return 0;\n    if(result[cnt - 1] < str) return cnt;\n    int l = 0, r = cnt - 1, mid;\n    while(r - l > 1)\n    {\n        mid = (l + r) >> 1;\n        if(result[mid] == str) return mid;\n        else\n        if(result[mid] > str) r = mid;\n        else l = mid;\n    }\n    return r;\n}\n\nint main()\n{\n    searchTot("");\n    sort(result, result + cnt);\n\n    int n;\n    char left[10], right[10];\n    string ll, rr;\n    scanf("%d", &n);\n    while(n--)\n    {\n        scanf("%s%s", left, right);\n        ll = left, rr = right;\n        while(ll.length() < 6) ll = "0" + ll;\n        while(rr.length() < 6) rr = "0" + rr;\n        int start = mid(ll);\n\n        while(start < cnt && result[start] <= rr)\n        {\n            printf("%s\\n", result[start++].c_str());\n        }\n        if(0 != n) printf("\\n");\n    }\n\n    return 0;\n}\n');

-- --------------------------------------------------------

--
-- 表的结构 `oj_config`
--

CREATE TABLE IF NOT EXISTS `oj_config` (
  `configid` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`configid`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `oj_config`
--

INSERT INTO `oj_config` (`configid`, `key`, `value`) VALUES
(1, 'webname', 'Ningbo University of Technology Online Judge'),
(2, 'ojname', 'NBUTOJ');

-- --------------------------------------------------------

--
-- 表的结构 `oj_contest`
--

CREATE TABLE IF NOT EXISTS `oj_contest` (
  `contestid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  `language` text NOT NULL,
  `submit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contestid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `oj_contest`
--

INSERT INTO `oj_contest` (`contestid`, `title`, `description`, `private`, `starttime`, `endtime`, `addtime`, `language`, `submit`) VALUES
(1, 'Practice', 'The problems for practice.', 0, NULL, NULL, 0, '1|2', 8);

-- --------------------------------------------------------

--
-- 表的结构 `oj_contestproblem`
--

CREATE TABLE IF NOT EXISTS `oj_contestproblem` (
  `contestproblemid` int(11) NOT NULL AUTO_INCREMENT,
  `contestid` int(11) NOT NULL,
  `problemid` int(11) NOT NULL,
  `index` varchar(32) NOT NULL DEFAULT '',
  `submit` int(11) NOT NULL DEFAULT '0',
  `solved` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contestproblemid`),
  UNIQUE KEY `INDEX` (`contestid`,`index`),
  KEY `problemid` (`problemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `oj_contestproblem`
--

INSERT INTO `oj_contestproblem` (`contestproblemid`, `contestid`, `problemid`, `index`, `submit`, `solved`) VALUES
(1, 1, 1000, '1000', 1, 0),
(2, 1, 1001, '1001', 1, 0),
(3, 1, 1002, '1002', 1, 0),
(4, 1, 1003, '1003', 1, 0),
(5, 1, 1004, '1004', 1, 0),
(6, 1, 1005, '1005', 1, 0),
(7, 1, 1006, '1006', 1, 0),
(8, 1, 1007, '1007', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `oj_contestuser`
--

CREATE TABLE IF NOT EXISTS `oj_contestuser` (
  `contestuserid` int(11) NOT NULL AUTO_INCREMENT,
  `contestid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `teamname` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`contestuserid`),
  UNIQUE KEY `INDEX` (`contestid`,`userid`),
  UNIQUE KEY `teamname` (`teamname`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `oj_contestuser`
--


-- --------------------------------------------------------

--
-- 表的结构 `oj_language`
--

CREATE TABLE IF NOT EXISTS `oj_language` (
  `languageid` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`languageid`),
  UNIQUE KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `oj_language`
--

INSERT INTO `oj_language` (`languageid`, `language`) VALUES
(1, 'GCC'),
(2, 'G++');

-- --------------------------------------------------------

--
-- 表的结构 `oj_problem`
--

CREATE TABLE IF NOT EXISTS `oj_problem` (
  `problemid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `sampleinput` text NOT NULL,
  `sampleoutput` text NOT NULL,
  `hint` text NOT NULL,
  `source` text NOT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  `timelimit` int(11) NOT NULL DEFAULT '1000',
  `memorylimit` int(11) NOT NULL DEFAULT '65535',
  `inputmd5` varchar(32) NOT NULL DEFAULT '',
  `outputmd5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`problemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1008 ;

--
-- 转存表中的数据 `oj_problem`
--

INSERT INTO `oj_problem` (`problemid`, `title`, `description`, `input`, `output`, `sampleinput`, `sampleoutput`, `hint`, `source`, `addtime`, `timelimit`, `memorylimit`, `inputmd5`, `outputmd5`) VALUES
(1000, '纸牌游戏', '<p>玩家1和玩家2各出一张牌，看谁大。如果两张牌都不是王牌花色或则都是王牌花色，则牌面大的牌大，如果牌面一样大则一样大。若其中一张牌是王牌而另一张不是，则无论牌面如何都是王牌花色大。<br /></p>', '第一行一个数字n，代表数据组数(n <= 10)\n对于每组数据，首先输入一个字符(S\\H\\D\\C)，表示王牌花色。\n接下去一行有两张牌面，表示为牌面花色，如8D、9S等。', '对于每组数据，输出第一张牌是否比第二张牌大，若是则输出YES，否则输出NO', '1\nH\nQH 9S\n', 'YES\n', '', 'CodeForces', 1321290505, 1000, 65535, '935067f6bf0aae4856b293758f64ba92', '935067f6bf0aae4856b293758f64ba92'),
(1001, '和谐用语', '<p>在不管是在天朝还是在哪里，各大网站、电视都有一个习惯——那就是屏蔽不和谐用语。<br />现在要你来写这么一段程序来将一段内容过滤，把不和谐用于给屏蔽掉之后再输出。为了简单考虑，目前我们的待屏蔽内容只考虑英文单词、数字和标点符号(,.!?&quot;&#39;)。\n在过滤的时候规则如下：<br />1、如果是默认规则，那么将需要屏蔽的词留首末两个字母，中间用&quot;*&quot;代替。如果这个词语只有两位或者一位，那么直接变成*或者**。如damn屏蔽成d**n，F屏蔽成*这样。<br />2、如果不是默认规则，那么会给你一个将要转变的东西，如果这个词语要屏蔽，则直接变成要转变的东西。如替换词为[bi]，那么damn将被屏蔽成[bi]。<br />3、屏蔽的时候，屏蔽词匹配不区分大小写。<br />4、如果damn将被屏蔽，而内容中有形如adamn之类的词则不受过滤。即一个单词必须要原封不动地匹配。(不过要遵照第三条规则，不区分大消息。)<br /></p>', '第一行一个数字n，代表数据组数。(n <= 10)\n接下来每组数据如下：\n第一行一个数字m，代表需屏蔽的单词数量。(m <= 100)\n下面m行是m个单词（不包含空格,单词不超过26位）\n接下去一行是过滤规则，若为default则以第一种规则过滤，若为其它，则用第二种规则过滤。(不包含空格，不超过26位)\n再接下去一行是待过滤内容。(内容长度不超过5000)\n', '对于每组数据，输出过滤后的内容。', '1\n1\ndamn\n[bi]\nDamn it!\n', '[bi] it!\n', '', 'XadillaX', 1321290517, 1000, 65535, 'ec4183846b476364e8875304a8a42056', 'ec4183846b476364e8875304a8a42056'),
(1002, '将军问题', '<p>关于中国象棋，想必大家都很熟悉吧。我们知道，在走棋的时候，被对方將军的这种情形是很容易被人察觉的(不然，你也太粗心了)。但是我们的计算机是如何识别这种情形的呢？它显然没有人的这种“直觉”。这就是我们今天要解决的问题，你的任务就是写一段计算机代码，根据当前局面信息，判断是否存在一方正在被另一方將军的情形，并给出正确结果。</p><div class="figure" style="padding-top: 0.5em; padding-right: 0.5em; padding-bottom: 0.5em; padding-left: 0.5em; "><p style="text-align: center; "><img src="http://127.0.0.1/oj/ueditor/dialogs/image/uploadfiles/1321290173.png" align="center" alt="./1.png" width="371" height="409" /></p><p style="text-align: center; ">图片一</p></div><p>如图一，象棋棋盘由九条竖线和十条横线交叉组成。棋盘上共有九十个交叉点，象棋子就摆放在和活动在这些交叉点上。棋盘中间没有画通直线的地方，叫做“九宫”。棋子共有三十二个，分为红、黑两组，每组共十六个，各分七种，其名称和数目如下：</p><ul><li><p>红棋子：&nbsp;帅一个，车、马、炮、相、仕各两个，兵五个。</p></li><li><p>黑棋子：&nbsp;将一个，车、马、炮、象、士各两个，卒五个。</p></li></ul><p>各种棋子的走法如下：</p><ul><li><p>将（帅）每一步只许前进、后退、横走，但不能走出“九宫”。</p></li><li><p>士（仕）每一步只许沿“九宫”斜线走一格，可进可退。</p></li><li><p>象（相）不能越过“河界”，每一步斜走两格，可进可退，即俗称“象（相）走田字“。当田字中心有别的棋子时，俗称”塞象（相）眼“，则不许走过去。</p></li><li><p>马每步一直（或一横）一斜，可进可退，即俗称”马走日字“。如果在要去的方向有别的棋子挡住，俗称”蹩马腿”，则不许走过去。具体可参考图二。</p></li></ul><div class="figure" style="padding-top: 0.5em; padding-right: 0.5em; padding-bottom: 0.5em; padding-left: 0.5em; "><p style="text-align: center; "><img src="http://127.0.0.1/oj/ueditor/dialogs/image/uploadfiles/1321290193.png" align="center" alt="./2.png" width="328" height="328" /></p><p style="text-align: center; ">图片二</p></div><ul><li><p>车每一步可以直进、直退、横走，不限步数。</p></li><li><p>炮在不吃子的时候，走法跟车一样。在吃子时必须隔一个棋子（无论是哪一方的）跳吃，即俗称“炮打隔子”。</p></li><li><p>卒（兵）在没有过“河界”前，没步只许向前直走一格；过“河界”后，每步可向前直走或横走一格，但不能后退。</p></li></ul><p>另外，在一个局面中，如果一方棋子能够走到的位置有对方将（帅）的存在，那么该局面就称为將军局面，我们的任务就是找出这样的局面。根据上述规则，我们很容易就能推断出只有以下几种方式才会造成將军局面：</p><ol><li><p>将（帅）照面。即将和帅在同一直线上。</p></li><li><p>马对将（帅）的攻击。（注意马有蹩脚）</p></li><li><p>车对将（帅）的攻击。</p></li><li><p>炮对将（帅）的攻击。（注意炮要隔一子）</p></li><li><p>过河兵对将（帅）的攻击。</p></li></ol><p><br /></p>', '输入的第一行为一个正整数n(1<=n<=100)。表示有n个测试局面。\n接下来的n次测试，每次输入10行，每行输入9个特定正整数，用来表示一个局面（上黑下红）。其中数字0表示该处无棋子，其他数字具体表示如下：\n黑方：将(1)、士(2,3)、象(4,5)、马(6,7)、车(8,9)、炮(10,11)、卒(12,13,14,15,16)\n红方：帅(17)、仕(18,19)、相(20,21)、马(22,23)、车(24,25)、炮(26,27)、兵(28,29,30,31,32)\n提示：样例中的第一组数据表示的是初始局面，第二组数据表示的是图一的局面。', '如果存在将军局面，则输出"yes"。反之，输出"no"。', '2\n8 6 4 2 1 3 5 7 9\n0 0 0 0 0 0 0 0 0\n0 10 0 0 0 0 0 11 0\n12 0 13 0 14 0 15 0 16\n0 0 0 0 0 0 0 0 0 \n0 0 0 0 0 0 0 0 0\n28 0 29 0 30 0 31 0 32\n0 26 0 0 0 0 0 27 0\n0 0 0 0 0 0 0 0 0 \n24 22 20 18 17 19 21 23 25\n\n8 6 4 2 1 3 5 0 9\n0 0 0 0 0 0 0 0 0\n0 10 0 0 0 0 7 11 0\n12 0 13 0 14 0 15 0 16\n0 0 0 0 0 0 0 0 0 \n0 0 0 0 27 0 0 0 0\n28 0 29 0 30 0 31 0 32\n0 26 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0 \n24 22 20 18 17 19 21 23 25\n', 'no\nyes\n', '', 'Timebug', 1321290257, 1000, 65535, 'b598c4dc375cb3b2608629c221df8914', 'b598c4dc375cb3b2608629c221df8914'),
(1003, '矩阵链乘法', '<p>给定一个有n个矩阵的矩阵链A<sub>1</sub>A<sub>2</sub>A<sub>3</sub>…A<sub>n</sub>，其中矩阵A<sub>i</sub>(i=1,2,3…n)的维度为p<sub>i-1</sub>*p<sub>i</sub>。我们知道，两个维度分别为m*r和r*n的矩阵用一般的矩阵乘法相乘，所需的运算次数为m*r*n，最后得到一个维度为m*n的结果矩阵。对于矩阵链问题，因为矩阵乘法具有结合律，其运算顺序有很多中选择。换句话说，不论如何括号其乘积，最后结果都会是一样的。例如，若有四个矩阵A、B、C和D，将可以有：</p><pre class="example" style="border-top-width: 1pt; border-right-width: 1pt; border-bottom-width: 1pt; border-left-width: 1pt; border-top-color: rgb(174, 189, 204); border-right-color: rgb(174, 189, 204); border-bottom-color: rgb(174, 189, 204); border-left-color: rgb(174, 189, 204); background-color: rgb(243, 245, 247); padding-top: 5pt; padding-right: 5pt; padding-bottom: 5pt; padding-left: 5pt; font-family: courier, monospace; font-size: 14px; overflow-x: auto; overflow-y: auto; ">(ABC)D&nbsp;=&nbsp;(AB)(CD)&nbsp;=&nbsp;A(BCD)&nbsp;=&nbsp;A(BC)D&nbsp;=&nbsp;...\n</pre><p>但括号其乘积的顺序会影响到需要计算乘积所需简单算术运算的数目，即其效率。例如，设A为一10*30矩阵，B为30*5矩阵与C为5*60矩阵，则:</p><pre class="example" style="border-top-width: 1pt; border-right-width: 1pt; border-bottom-width: 1pt; border-left-width: 1pt; border-top-color: rgb(174, 189, 204); border-right-color: rgb(174, 189, 204); border-bottom-color: rgb(174, 189, 204); border-left-color: rgb(174, 189, 204); background-color: rgb(243, 245, 247); padding-top: 5pt; padding-right: 5pt; padding-bottom: 5pt; padding-left: 5pt; font-family: courier, monospace; font-size: 14px; overflow-x: auto; overflow-y: auto; ">(AB)C有(10*30*5)&nbsp;+&nbsp;(10*5*60)&nbsp;=&nbsp;1500&nbsp;+&nbsp;3000&nbsp;=&nbsp;4500&nbsp;个运算\nA(BC)有(30*5*60)&nbsp;+&nbsp;(10*30*60)&nbsp;=&nbsp;9000&nbsp;+&nbsp;18000&nbsp;=&nbsp;27000&nbsp;个运算\n...\n</pre><p>明显地，第一种方式要有效多了。所以，矩阵链乘法问题也就是如何对矩阵乘积加括号，使得它们的乘法次数达到最少。</p><p><br /></p>', '输入的第一行为一个正整数n(1<=n<=200)。表示矩阵的个数。\n输入的第二行包含n+1个整数，分别表示pi(0<=i<=n)，其中每个pi在[1,200]范围内。', '输出一个整数表示最少要进行的乘法次数。', '3\n1 2 3 4\n3\n10 30 5 60\n', '18\n4500\n', '', 'Timebug', 1321290492, 1000, 65535, '6723e81e0c33658c040f3814c3a4eb84', '6723e81e0c33658c040f3814c3a4eb84'),
(1004, '万圣节派对', '<p>万圣节有一个Party，XadillaX显然也要去凑热闹了。因为去凑热闹的人数非常庞大，几十W的数量级吧，自然要进场就需要有门票了。很幸运的，XadillaX竟然拿到了一张真·门票！这真·门票的排列规则有些奇怪：</p><ol style="list-style-type: decimal; list-style-position: inside;"><li><p>门票号是由0~6组成的六位数（0~6这几个数字可重用）</p></li><li><p>每一个门票号的每一位不能有三个连续相同的数字（如123335是不行的）</p></li><li><p>每一个门票号相邻的两位相差必须在四以下（≤4）（如016245是不行的）<br /></p></li></ol><p><br /></p>', '第一行一个n，代表数据组数\n接下去n行，每行两个数字x,y(x <= y)', '对于每一组数据，输出x到y之间的门票编号', '2\n001001 001002\n001011 001012\n', '001001\n001002\n\n001011\n001012\n', '', 'XadillaX', 1321291599, 1000, 65535, '3e00be5dd65fb64aa43ae8729b2a5236', '3e00be5dd65fb64aa43ae8729b2a5236'),
(1005, '足球赛', '<p>Petya&nbsp;loves&nbsp;football&nbsp;very&nbsp;much.&nbsp;One&nbsp;day,&nbsp;as&nbsp;he&nbsp;was&nbsp;watching&nbsp;a&nbsp;football&nbsp;match,&nbsp;he&nbsp;was&nbsp;writing&nbsp;the&nbsp;players&#39;&nbsp;current&nbsp;positions&nbsp;on&nbsp;a&nbsp;piece&nbsp;of&nbsp;paper.&nbsp;To&nbsp;simplify&nbsp;the&nbsp;situation&nbsp;he&nbsp;depicted&nbsp;it&nbsp;as&nbsp;a&nbsp;string&nbsp;consisting&nbsp;of&nbsp;zeroes&nbsp;and&nbsp;ones.&nbsp;A&nbsp;zero&nbsp;corresponds&nbsp;to&nbsp;players&nbsp;of&nbsp;one&nbsp;team;&nbsp;a&nbsp;one&nbsp;corresponds&nbsp;to&nbsp;players&nbsp;of&nbsp;another&nbsp;team.&nbsp;If&nbsp;there&nbsp;are&nbsp;at&nbsp;least&nbsp;7&nbsp;players&nbsp;of&nbsp;some&nbsp;team&nbsp;standing&nbsp;one&nbsp;after&nbsp;another,&nbsp;then&nbsp;the&nbsp;situation&nbsp;is&nbsp;considered&nbsp;dangerous.&nbsp;For&nbsp;example,&nbsp;the&nbsp;situation&nbsp;00100110111111101&nbsp;is&nbsp;dangerous&nbsp;and&nbsp;11110111011101&nbsp;is&nbsp;not.&nbsp;You&nbsp;are&nbsp;given&nbsp;the&nbsp;current&nbsp;situation.&nbsp;Determine&nbsp;whether&nbsp;it&nbsp;is&nbsp;dangerous&nbsp;or&nbsp;not.<br /></p>', 'The first input is an integer N, represents the number of cases.\nThen there are N cases:\nThe first input line contains a non-empty string consisting of characters "0" and "1", which represents players. The length of the string does not exceed 100 characters. There''s at least one player from each team present on the field.', 'Print "YES" if the situation is dangerous. Otherwise, print "NO".', '2\n001001\n1000000001\n', 'NO\nYES\n', '', 'CodeForces', 1321290723, 1000, 65535, 'b77679f92388704866e92a8f76839f8b', 'b77679f92388704866e92a8f76839f8b'),
(1006, 'DOTA', '<p>DOTA是一个基于魔兽争霸的5V5&nbsp;RPG地图。它风靡全世界，相信很多同学都玩过，当然没玩过也没有关系。首先简单介绍一下游戏，它的目的是守护自己的远古遗迹（近卫方的生命之树、天灾方的冰封王座），同时摧毁对方的远古遗迹。为了到达对方的远古遗迹，一方英雄必须战胜对方的部队、防御建筑和英雄。我们的问题来了~~~当一个英雄杀死敌方的时候，如果符合某种条件，该英雄就会获得一些称号。我们的要做的就是输出这些称号。</p><p>这里说明一些规则：</p><p><span style="color: rgb(255, 0, 0); ">不会杀死友军，但是可以自杀。若自杀了则所有称号中断重新累计。</span></p><p>第一个杀人的人将获得一个First&nbsp;Blood的称号.</p><p>连续杀人数3个(中间不被击杀即为连续击杀)将获得Killing&nbsp;Spree的称号。以此类推4个为Dominating，5个为Mega&nbsp;Kill，6个为Unstoppable，7个为Wicked&nbsp;Sick</p><p>8个为M-m-m-m...onsterKill，9个为Godlike，10个以上都是Beyond&nbsp;Godlike</p><p>如果每两次杀人间隔10秒包含10秒，连续杀人数为2人，获得称号Double&nbsp;Kill</p><p>以此类推Triple&nbsp;Kill，Ultra&nbsp;Kill，Rampage&nbsp;对应3,4,5或者5以上。</p><p><br /></p>', '输入数据按 击杀发生时间顺序先后 给出各队击杀 和 时间\n比如 a kill b in 00:33\n首先输入一个T，代表有几组测试数据\n然后输入一个N，代表击杀事件的个数\n然后输入击杀事件', '输入和输出规范详情参考 input和output', '1\n6\na kill f in 03:33 \na kill g in 03:40\na kill h in 03:50\nf kill a in 09:10\na kill f in 09:50\nf kill b in 11:50\n', 'a has First Blood\na has Double Kill\na has Triple Kill\na is Killing Spree\n', '', 'HJX', 1321290881, 1000, 65535, '0a5bfe5857dc1eea3804108b46e28008', '0a5bfe5857dc1eea3804108b46e28008'),
(1007, '第几天', '<p>有一本记录了从1年到9999年的日历，</p><p>假设1年1月1日为第一天，现在问第Y年的第M月的第D天是第几天。</p><p><br /></p>', '有一本记录了从1年到9999年的日历，\n假设1年1月1日为第一天，现在问第Y年的第M月的第D天是 第几天。\n', '对于每组数据，输出这是第几天。', '2\n1 1 1\n2 2 2\n', '1\n398\n', '', 'HJX', 1321290942, 1000, 65535, '49ffd34f8bd3624b4a7f97d566d02d51', '49ffd34f8bd3624b4a7f97d566d02d51');

-- --------------------------------------------------------

--
-- 表的结构 `oj_result`
--

CREATE TABLE IF NOT EXISTS `oj_result` (
  `resultid` int(11) NOT NULL,
  `result` varchar(32) NOT NULL,
  PRIMARY KEY (`resultid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `oj_result`
--

INSERT INTO `oj_result` (`resultid`, `result`) VALUES
(0, 'QUEUING'),
(1, 'COMPILING'),
(2, 'RUNNING'),
(3, 'ACCEPTED'),
(4, 'PRESENTATION_ERROR'),
(5, 'WRONG_ANSWER'),
(6, 'RUNTIME_ERROR'),
(7, 'TIME_LIMIT_EXCEEDED'),
(8, 'TIME_LIMIT_EXCEEDED'),
(9, 'MEMORY_LIMIT_EXCEEDED'),
(10, 'OUTPUT_LIMIT_EXCEEDED'),
(11, 'COMPILATION_ERROR'),
(12, 'COMPILATION_SUC'),
(13, 'SYSTEM_ERROR'),
(14, 'OUT_OF_CONTEST_TIME');

-- --------------------------------------------------------

--
-- 表的结构 `oj_role`
--

CREATE TABLE IF NOT EXISTS `oj_role` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(32) NOT NULL DEFAULT '',
  `privilege` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleid`),
  UNIQUE KEY `rolename` (`rolename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `oj_role`
--

INSERT INTO `oj_role` (`roleid`, `rolename`, `privilege`) VALUES
(1, 'USER', 0),
(2, 'EDITOR', 0),
(3, 'ADMIN', 0);

-- --------------------------------------------------------

--
-- 表的结构 `oj_submit`
--

CREATE TABLE IF NOT EXISTS `oj_submit` (
  `totsubmitid` int(11) NOT NULL AUTO_INCREMENT,
  `submitid` int(11) NOT NULL,
  `contestid` int(11) NOT NULL,
  `index` varchar(32) NOT NULL,
  `userid` int(11) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `memory` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '0',
  `submittime` int(11) NOT NULL DEFAULT '0',
  `languageid` int(11) NOT NULL DEFAULT '0',
  `resultid` int(11) DEFAULT NULL,
  PRIMARY KEY (`totsubmitid`),
  UNIQUE KEY `INDEX` (`submitid`,`contestid`),
  KEY `contestid` (`contestid`),
  KEY `problemid` (`index`),
  KEY `userid` (`userid`),
  KEY `resultid` (`resultid`),
  KEY `languageid` (`languageid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `oj_submit`
--

INSERT INTO `oj_submit` (`totsubmitid`, `submitid`, `contestid`, `index`, `userid`, `time`, `memory`, `length`, `submittime`, `languageid`, `resultid`) VALUES
(2, 0, 1, '1004', 1, 0, 0, 1421, 1321416749, 2, 0),
(3, 1, 1, '1003', 1, 0, 0, 735, 1321416899, 2, 0),
(4, 2, 1, '1001', 1, 0, 0, 2308, 1321416921, 2, 0),
(5, 3, 1, '1002', 1, 0, 0, 9413, 1321416943, 2, 0),
(6, 4, 1, '1000', 1, 0, 0, 676, 1321416960, 2, 0),
(7, 5, 1, '1005', 1, 0, 0, 367, 1321416982, 2, 0),
(8, 6, 1, '1006', 1, 0, 0, 3564, 1321417364, 2, 0),
(9, 7, 1, '1007', 1, 0, 0, 749, 1321417379, 2, 0);

-- --------------------------------------------------------

--
-- 表的结构 `oj_user`
--

CREATE TABLE IF NOT EXISTS `oj_user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL,
  `roleid` int(11) NOT NULL DEFAULT '1',
  `nickname` varchar(32) NOT NULL,
  `regtime` int(11) NOT NULL,
  `solved` int(11) NOT NULL DEFAULT '0',
  `submit` int(11) NOT NULL DEFAULT '0',
  `school` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `motto` text NOT NULL,
  `language` int(11) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `oj_user`
--

INSERT INTO `oj_user` (`userid`, `username`, `password`, `roleid`, `nickname`, `regtime`, `solved`, `submit`, `school`, `email`, `motto`, `language`) VALUES
(1, 'XadillaX', '045f382f08038084d9ef8d74a8402363', 3, '死月', 0, 0, 8, 'Ningbo University of Technology', 'admin@xcoder.in', '', 2),
(2, 'test', 'e10adc3949ba59abbe56e057f20f883e', 1, 'a', 0, 0, 0, '', 'a@b.c', '', 0),
(3, 'again', 'e10adc3949ba59abbe56e057f20f883e', 1, 'again', 0, 0, 0, 'SBUT', 'again@again.again', 'SB AGAIN.', 0),
(4, 'deathmoon', '045f382f08038084d9ef8d74a8402363', 1, 'canyouhelpme', 0, 0, 0, '', 'zukaidi@163.com', '', 0),
(5, 'mamama', '2a7d94e6d20ed9be4edca6f5ebe5e0ab', 1, 'adslfkj', 0, 0, 0, '', 'j@k.c', '', 0),
(6, 'konakona', '171f9f26441decbb9a1dac3e5b60f783', 1, 'konakona', 0, 0, 0, '', 'admin@crazyphper.com', '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
