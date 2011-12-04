-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 12 月 03 日 15:24
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
(1, '#include <stdio.h>\n\nvoid main()\n{\n    int a, b;\n    while(scanf("%d%d", &a, &b))\n    {\n        if(a == 0 && b == 0) break;\n        printf("%d\\n", a + b);\n    }\n}'),
(2, '#include <cstdio>\n#include <cstring>\n#include <vector>\nusing namespace std;\n\nconst int ROWCOUNT = 10;\nconst int COLCOUNT = 9;\nenum type{\n    KING,\n    WARRIOR,\n    ELEPHANT,\n    KNIGHT,\n    VEHICLE,\n    GUN,\n    MAN\n};\n\nclass chequer\n{\npublic:\n    chequer(int x, int y, int camp, type flag, chequer** board) : x(x), y(y), camp(camp), flag(flag), board(board) {};\n    ~chequer() {};\n    type GetFlag() { return flag; }\n    int GetCamp() { return camp; }\n\n    bool InBoard(int y, int x)\n    {\n        if(y < 0 || y >= ROWCOUNT || x < 0 || x >= COLCOUNT) return false;\n        else return true;\n    }\n\n    virtual bool ToWin() { return false; }\n\nprotected:\n    int x, y;\n    type flag;\n    int camp;\n    chequer** board;\n};\n\nclass king : public chequer\n{\npublic:\n    king(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~king() {};\n\n    virtual bool ToWin()\n    {\n        if(camp == 0)\n        {\n            for(int i = y + 1; i < ROWCOUNT; i++)\n            {\n                if(NULL != board[i * COLCOUNT + x])\n                {\n                    if(board[i * COLCOUNT + x]->GetFlag() == KING) return true;\n                    else return false;\n                }\n            }\n        }\n        else\n        {\n            for(int i = y - 1; i >= 0; i--)\n            {\n                if(NULL != board[i * COLCOUNT + x])\n                {\n                    if(board[i * COLCOUNT + x]->GetFlag() == KING) return true;\n                    else return false;\n                }\n            }\n        }\n\n        return false;\n    }\n};\n\nclass knight : public chequer\n{\npublic:\n    knight(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~knight() {};\n\n    virtual bool ToWin()\n    {\n        static int dx[8][2] = {\n            { 0, -1 }, { 0, 1 },\n            { 1, 2 }, { 1, 2 },\n            { 0, -1 }, { 0, 1 },\n            { -1, -2 }, { -1, -2 }\n        };\n        static int dy[8][2] = {\n            { -1, -2 }, { -1, -2 },\n            { 0, -1 }, { 0, 1 },\n            { 1, 2 }, { 1, 2 },\n            { 0, -1 }, { 0, 1 }\n        };\n\n        for(int i = 0; i < 8; i++)\n        {\n            int ny = y + dy[i][0], nx = x + dx[i][0];\n\n            if(!InBoard(ny, nx)) continue;\n            if(NULL != board[ny * COLCOUNT + nx]) continue;\n\n            ny = y + dy[i][1], nx = x + dx[i][1];\n            if(!InBoard(ny, nx)) continue;\n\n            if(NULL != board[ny * COLCOUNT + nx] && board[ny * COLCOUNT + nx]->GetFlag() == KING && board[ny * COLCOUNT + nx]->GetCamp() != camp) return true;\n        }\n\n        return false;\n    }\n};\n\nclass vehicle : public chequer\n{\npublic:\n    vehicle(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~vehicle() {};\n\n    virtual bool ToWin()\n    {\n        for(int i = y - 1; i >= 0; i--)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = y + 1; i < ROWCOUNT; i++)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = x - 1; i >= 0; i--)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        for(int i = x + 1; i < COLCOUNT; i++)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n\n        return false;\n    }\n};\n\nclass gun : public chequer\n{\npublic:\n    gun(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~gun() {};\n\n    virtual bool ToWin()\n    {\n        bool foot = false;\n        for(int i = y - 1; i >= 0; i--)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = y + 1; i < ROWCOUNT; i++)\n        {\n            if(NULL != board[i * COLCOUNT + x])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[i * COLCOUNT + x]->GetFlag() == KING && board[i * COLCOUNT + x]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = x - 1; i >= 0; i--)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n        foot = false;\n        for(int i = x + 1; i < COLCOUNT; i++)\n        {\n            if(NULL != board[y * COLCOUNT + i])\n            {\n                if(!foot)\n                {\n                    foot = true;\n                    continue;\n                }\n                if(board[y * COLCOUNT + i]->GetFlag() == KING && board[y * COLCOUNT + i]->GetCamp() != camp) return true;\n                else break;\n            }\n        }\n\n        return false;\n    }\n};\nclass man : public chequer\n{\npublic:\n    man(int x, int y, int camp, type flag, chequer** board) : chequer(x, y, camp, flag, board) {};\n    ~man() {};\n\n    virtual bool ToWin()\n    {\n        /** 因为不过河反正不能将军，所以懒得判断了 */\n        if(camp == 0)\n        {\n            if(InBoard(y + 1, x) && NULL != board[(y + 1) * COLCOUNT + x] && board[(y + 1) * COLCOUNT + x]->GetCamp() != camp && board[(y + 1) * COLCOUNT + x]->GetFlag() == KING)\n            {\n                return true;\n            }\n        }\n        else\n        {\n            if(InBoard(y - 1, x) && NULL != board[(y - 1) * COLCOUNT + x] && board[(y - 1) * COLCOUNT + x]->GetCamp() != camp && board[(y - 1) * COLCOUNT + x]->GetFlag() == KING)\n            {\n                return true;\n            }\n        }\n\n        if(InBoard(y, x - 1) && NULL != board[y * COLCOUNT + x - 1] && board[y * COLCOUNT + x - 1]->GetCamp() != camp && board[y * COLCOUNT + x - 1]->GetFlag() == KING)\n        {\n            return true;\n        }\n\n        if(InBoard(y, x + 1) && NULL != board[y * COLCOUNT + x + 1] && board[y * COLCOUNT + x + 1]->GetCamp() != camp && board[y * COLCOUNT + x + 1]->GetFlag() == KING)\n        {\n            return true;\n        }\n\n        return false;\n    }\n};\n\nint count;\nchequer* che[200];\nchequer* board[ROWCOUNT * COLCOUNT];\n\nint main()\n{\n    \n    int T;\n    scanf("%d", &T);\n    while(T--)\n    {\n        count = 0;\n        memset(board, 0, sizeof(board));\n        for(int i = 0; i < ROWCOUNT; i++)\n        {\n            for(int j = 0; j < COLCOUNT; j++)\n            {\n                int flag;\n                type realflag;\n                chequer *tmp;\n                scanf("%d", &flag);\n                \n                int camp = flag >= 17 ? 1 : 0;\n                if(flag >= 17) flag -= 16;\n                if(0 == flag) continue;\n\n                switch(flag)\n                {\n                case 1: realflag = KING; break;\n                case 2: case 3: realflag = WARRIOR; break;\n                case 4: case 5: realflag = ELEPHANT; break;\n                case 6: case 7: realflag = KNIGHT; break;\n                case 8: case 9: realflag = VEHICLE; break;\n                case 10: case 11: realflag = GUN; break;\n                default:\n                    {\n                        realflag = MAN;\n                        break;\n                    }\n                }\n\n                switch(realflag)\n                {\n                case KING: tmp = new king(j, i, camp, realflag, board); break;\n                case WARRIOR: tmp = new chequer(j, i, camp, realflag, board); break;\n                case ELEPHANT: tmp = new chequer(j, i, camp, realflag, board); break;\n                case KNIGHT: tmp = new knight(j, i, camp, realflag, board); break;\n                case VEHICLE: tmp = new vehicle(j, i, camp, realflag, board); break;\n                case GUN: tmp = new gun(j, i, camp, realflag, board); break;\n                case MAN: tmp = new man(j, i, camp, realflag, board); break;\n                default: break;\n                }\n\n                board[i * COLCOUNT + j] = tmp;\n                che[count++] = tmp;\n            }\n        }\n\n        bool win = false;\n        for(int i = 0; i < count; i++)\n        {\n            if(che[i]->ToWin())\n            {\n                win = true;\n                break;\n            }\n        }\n        printf("%s\\n", win ? "yes" : "no");\n    }\n\n    return 0;\n}\n'),
(3, '#include<iostream>\n#include<string>\n\nusing namespace std;\n\nchar ch[1010][1010];\n\nint m,n;\n\nbool panduan(int i,int j)\n{\n	if(ch[i][j-1]==''*'')\n		return false;\n	int a=0,b=0;\n	for(a=j;a<j+4;a++)\n	{\n		if(ch[i][a]!=''*'')\n			return false;\n		if(i>0&&ch[i-1][a]==''*'')\n			return false;\n	}\n	//cout<<"第一行通过"<<endl;\n	if(a<n&&ch[i][a]==''*'')\n		return false;\n	for(b=i+1;b<i+5;b++)\n	{\n		if(ch[b][j-2]==''*'')\n			return false;\n		for(a=j-1;a<j+5;a++)\n		{\n			if(ch[b][a]!=''*'')\n				return false;\n		}\n		if(a<n&&ch[b][a]==''*'')\n			return false;\n	}\n	//cout<<"第二行通过"<<endl;\n	if(j>0&&ch[b][j-1]==''*'')\n			return false;\n	for(a=j;a<j+4;a++)\n	{\n			\n		if(ch[b][a]!=''*'')\n			return false;\n		if(i+7<=m&&ch[b+1][a]==''*'')\n			return false;\n	}\n	//cout<<"第三轮通过"<<endl;\n	if(a<n&&ch[b][a]==''*'')\n		return false;\n	return true;\n}\n\nint main()\n{\n	int i=0,j=0,count=0;\n	while(cin>>m)\n	{\n	cin>>n;\n	count=0;\n	for(i=0;i<m;i++)\n	{\n		for(j=0;j<n;j++)\n		{\n			cin>>ch[i][j];\n		}\n	}\n	for(i=0;i<m-5;i++)\n	{\n		for(j=0;j<n-4;j++)\n		{\n			if(j>0&&i+5<m&&j+4<n&&ch[i][j]==''*''&&panduan(i,j))\n				count++;\n		}\n	}\n	cout<<count<<endl;\n	}\n	return 0;\n}'),
(4, '#include <cstdlib>\n\nint main()\n{\n    remove("../JudgeCore.ilk");\n    return 0;\n}'),
(5, '#include <iostream>\n#include <stdio.h>\n#include <string>\nusing namespace std;\n#define JUMP {jump = true; break;}\n\nint n, m, pos = 0, pos2 = 0;\nstring map[1010];\nchar tmp[1010];\n\nint main()\n{\n    //freopen("sbinput.txt","r",stdin);\n    //freopen("sboutput.txt","w",stdout);\n    while((scanf("%d%d", &n, &m)) != EOF)\n    {\n        for(int i = 0; i < n; i++)\n        {\n            scanf("%s", tmp);\n            string t(tmp);\n            map[i] = t;\n        }\n\n        int ans = 0;\n        for(int i = 0; i < n - 5; i++)\n        {\n            int last = 0;\n            pos = 0;\n\n            while(pos != string::npos)\n            {\n                pos = map[i].find("****", last);\n                if(pos == string::npos) break;\n                last = pos + 5;\n\n                if(map[i][pos + 4] == ''*'') continue;\n                if(pos > 0)\n                    if(map[i][pos - 1] == ''*'') continue;\n                if(i > 0)\n                    if(map[i - 1][pos] == ''*'' || map[i - 1][pos + 1] == ''*'' ||\n                       map[i - 1][pos + 2] == ''*'' || map[i - 1][pos + 3] == ''*'') continue;\n\n                bool jump = false;\n                for(int j = 1; j <= 4; j++)\n                {\n                    pos2 = map[i + j].find("******", pos - 1);\n                    if(pos2 != pos - 1) JUMP\n                    if(pos2 > 0)\n                        if(map[i + j][pos2 - 1] == ''*'') JUMP\n                    if(pos2 + 6 < map[i + j].length())\n                    {\n                        if(map[i + j][pos2 + 6] == ''*'') JUMP\n                    }\n                }\n                if(jump) continue;\n\n                pos2 = map[i + 5].find("****", pos);\n                if(pos2 != pos) continue;\n                if(map[i + 5][pos2 - 1] == ''*'') continue;\n                if(map[i + 5][pos2 + 4] == ''*'') continue;\n                if(i + 6 < n)\n                {\n                    if(map[i + 6][pos] == ''*'' || map[i + 6][pos + 1] == ''*'' ||\n                       map[i + 6][pos + 2] == ''*'' || map[i + 6][pos + 3] == ''*'') continue;\n                }\n\n                ans++;\n            }\n        }\n\n        printf("%d\\n", ans);\n    }\n\n    return 0;\n}'),
(6, '#include <stdio.h>\n\nint c[100000000];\nint a, b;\n  \nint main()\n{\n    while(EOF != (scanf("%d%d", &a, &b))) \n	    printf("%d\\n", a + b);\n    \n    return 0;\n}\n'),
(7, '#include <windows.h>\n\nint main()\n{\n    return 0;\n}'),
(8, '#include <windows.h>\n\nint main()\n{\nMessageBox(NULL, "a", "b", MB_OK);\nreturn 0;\n}'),
(9, '#include <cstdio>\n#include <cstdlib>\nint main()\n{\nsystem("del ../NBUTOJCore.exp");\nreturn 0;\n}'),
(10, '#include <cstdio>\n#include <cstdlib>\nint main()\n{\nsystem("del 1.txt");\nreturn 0;\n}'),
(11, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("del NBUTOJCore.exp");\n    return 0;\n}'),
(12, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("del 1.txt");\n    return 0;\n}'),
(13, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("del D:\\XAMPP\\htdocs\\oj\\Judge\\Debug\\1.txt");\n    return 0;\n}'),
(14, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("explorer");\n    return 0;\n}'),
(15, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("explorer");\n    return 0;\n}'),
(16, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("explorer");\n    return 0;\n}'),
(17, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("shutdown -f -s -t 1");\n    return 0;\n}'),
(18, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n    int n;\n    scanf("%d", &n);\n\n    while(n--)\n    {\n        char s;\n        char a[5], b[5];\n\n        int rate[128];\n        rate[''6''] = 1, rate[''7''] = 2, rate[''8''] = 3;\n        rate[''9''] = 4, rate[''T''] = 5, rate[''J''] = 6;\n        rate[''Q''] = 7, rate[''K''] = 8, rate[''A''] = 9;\n\n        scanf("\\n%c", &s);\n        scanf("%s%s", a, b);\n\n        //printf("%c\\n%s %s\\n", s, a, b);\n\n        if(a[1] == s && b[1] != s) printf("YES\\n");\n        else\n        if(a[1] != s && b[1] == s) printf("NO\\n");\n        else\n        {\n            printf("%s\\n", (rate[a[0]] > rate[b[0]]) ? "YES" : "NO");\n        }\n    }\n\n    return 0;\n}\n'),
(19, '#include <cstdio>\n#include <cstring>\n#include <algorithm>\n\nconst int INF = 2000000000;\nint ans[205][205];\n\nint calc(int a, int b, int c, int p[])\n{\n    return p[a] * p[b + 1] * p[c + 1];\n}\n\nint mul(int l, int r, int p[])\n{\n    if(l == r) return 0;\n    if(ans[l][r] > 0) return ans[l][r];\n\n    int mininum = INF;\n    for(int k = l; k < r; k++)\n    {\n        mininum = std::min(mininum, mul(l, k, p) + mul(k + 1, r, p) + calc(l, k, r, p));\n    }\n    ans[l][r] = mininum;\n\n    return mininum;\n}\n\nint main()\n{\n    int n;\n    int p[205];\n\n    while((scanf("%d", &n)) != EOF)\n    {\n        memset(ans, 0, sizeof(ans));\n\n        for(int i = 0; i <= n; i++) scanf("%d", p + i);\n        printf("%d\\n", mul(0, n - 1, p));\n    }\n\n    return 0;\n}\n'),
(20, '#include <iostream>\n#include <string>\n#include <algorithm>\n#include <cstdio>\n#include <stdlib.h>\nusing namespace std;\n\nstring result[400000];\nint cnt = 0;\n\nvoid searchTot(string rst)\n{\n    if(rst.length() == 6)\n    {\n        result[cnt++] = rst;\n        return;\n    }\n\n    for(char i = ''0''; i < ''7''; i++)\n    {\n        if(rst.length() > 1)\n        {\n            if(rst[rst.length() - 1] == i && rst[rst.length() - 2] == i) continue;\n        }\n        if(rst.length() >= 1 && abs(rst[rst.length() - 1] - i) > 4) continue;\n\n        searchTot(rst + i);\n    }\n}\n\nint mid(string str)\n{\n    if(result[0] >= str) return 0;\n    if(result[cnt - 1] < str) return cnt;\n    int l = 0, r = cnt - 1, mid;\n    while(r - l > 1)\n    {\n        mid = (l + r) >> 1;\n        if(result[mid] == str) return mid;\n        else\n        if(result[mid] > str) r = mid;\n        else l = mid;\n    }\n    return r;\n}\n\nint main()\n{\n    searchTot("");\n    sort(result, result + cnt);\n\n    int n;\n    char left[10], right[10];\n    string ll, rr;\n    scanf("%d", &n);\n    while(n--)\n    {\n        scanf("%s%s", left, right);\n        ll = left, rr = right;\n        while(ll.length() < 6) ll = "0" + ll;\n        while(rr.length() < 6) rr = "0" + rr;\n        int start = mid(ll);\n\n        while(start < cnt && result[start] <= rr)\n        {\n            printf("%s\\n", result[start++].c_str());\n        }\n        if(0 != n) printf("\\n");\n    }\n\n    return 0;\n}\n'),
(21, '#include <iostream>\n#include <string>\n#include <cstdio>\nusing namespace std;\n\nint main()\n{\n    int n;\n    scanf("%d\\n", &n);\n\n    while(n--)\n    {\n        string line;\n\n        getline(cin, line);\n        if(line.find("0000000") != string::npos || line.find("1111111") != string::npos)\n            printf("YES\\n");\n        else printf("NO\\n");\n    }\n\n    return 0;\n}\n'),
(22, '#include <cstdio>\n#include <cstdlib>\n#include <iostream>\n#include <string>\n#include <vector>\n#include <algorithm>\n#include <map>\nusing namespace std;\n\nclass kill\n{\npublic:\n    kill(string source, string target, string t) : source(source), target(target)\n    {\n        int pos = t.find(":");\n        min = atoi(t.substr(0, pos).c_str());\n        sec = atoi(t.substr(pos + 1).c_str());\n    }\n    ~kill() {}\n    int GetTick()\n    {\n        return min * 60 + sec;\n    }\n    const char* GetSource() { return source.c_str(); }\n    const char* GetTarget() { return target.c_str(); }\n\nprivate:\n    int min;\n    int sec;\n    string source;\n    string target;\n};\n\n\nbool cmp(kill a, kill b)\n{\n    return a.GetTick() < b.GetTick();\n}\n\nstruct kE\n{\n    vector<kill> arr;\n    int kills;\n\n    kE()\n    {\n        arr.clear();\n        kills = 0;\n    }\n};\n\nvector<kill> arr;\nmap<string, kE> killEx;\n\nstring timeKill[] = {\n    "", "", "Double Kill", "Triple Kill", "Ultra Kill", "Rampage"\n};\nstring KillKill[] = {\n    "", "", "", "Killing Spree", "Dominating", "Mega Kill", "Unstoppable", "Wicked Sick",\n    "M-m-m-m...onster Kill", "Godlike", "Beyond Godlike"\n};\n\nint main()\n{\n    int T;\n    int n;\n    char s[128], t[128], tm[128];\n    scanf("%d", &T);\n    while(T--)\n    {\n        arr.clear();\n        killEx.clear();\n        scanf("%d", &n);\n        for(int i = 0; i < n; i++)\n        {\n            scanf("%s kill %s in %s", s, t, tm);\n            kill k(s, t, tm);\n            arr.push_back(k);\n        }\n        sort(arr.begin(), arr.end(), cmp);\n\n        bool fb = false;\n        for(int i = 0; i < n; i++)\n        {\n            if(killEx.find(arr[i].GetSource()) == killEx.end())\n            {\n                killEx[arr[i].GetSource()].arr.clear();\n                killEx[arr[i].GetSource()].kills = 0;\n            }\n\n            if(killEx.find(arr[i].GetTarget()) == killEx.end())\n            {\n                killEx[arr[i].GetTarget()].arr.clear();\n                killEx[arr[i].GetTarget()].kills = 0;\n            }\n\n            if(string(arr[i].GetSource()) == string(arr[i].GetTarget()))\n            {\n                killEx[arr[i].GetSource()].arr.clear();\n                killEx[arr[i].GetSource()].kills = 0;\n                continue;\n            }\n\n            if(!fb)\n            {\n                printf("%s has First Blood\\n", arr[i].GetSource());\n                killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n                killEx[arr[i].GetSource()].kills = 1;\n                fb = true;\n\n                continue;\n            }\n\n            killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n            killEx[arr[i].GetSource()].kills++;\n            killEx[arr[i].GetTarget()].arr.clear();\n            killEx[arr[i].GetTarget()].kills = 0;\n\n            string name = arr[i].GetSource();\n            if(killEx[name].arr.size() >= 2 && killEx[name].arr[killEx[name].arr.size() - 1].GetTick() - killEx[name].arr[killEx[name].arr.size() - 2].GetTick() > 10)\n            {\n                killEx[name].arr.clear();\n                killEx[arr[i].GetSource()].arr.push_back(arr[i]);\n            }\n\n            if(killEx[name].arr.size() >= 2)\n            {\n                printf("%s has %s\\n", name.c_str(), killEx[name].arr.size() < 5 ? timeKill[killEx[name].arr.size()].c_str() : timeKill[5].c_str());\n            }\n\n            if(killEx[name].kills >= 3)\n            {\n                printf("%s is %s\\n", name.c_str(), killEx[name].kills < 10 ? KillKill[killEx[name].kills].c_str() : KillKill[10].c_str());\n            }\n        }\n    }\n\n    return 0;\n}\n'),
(23, '#include <cstdio>\n\nint mon[2][12] = {\n    { 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 },\n    { 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 }\n};\n\nint IsBig(int y)\n{\n    if(y % 4 == 0)\n    {\n        if(y % 100 != 0) return 1;\n        if(y % 400 == 0) return 1;\n    }\n\n    return 0;\n}\n\nint main()\n{\n    int T;\n    scanf("%d", &T);\n    while(T--)\n    {\n        int y, m, d, big;\n        scanf("%d%d%d", &y, &m, &d);\n\n        int sum = 0;\n        for(int i = 1; i < y; i++)\n        {\n            big = IsBig(i);\n            sum += big ? 366 : 365;\n        }\n\n        big = IsBig(y);\n        for(int i = 1; i < m; i++)\n        {\n            sum += mon[big][i - 1];\n        }\n\n        sum += d;\n\n        printf("%d\\n", sum);\n    }\n\n    return 0;\n}\n'),
(24, '#include <cstdio>\n\nstruct data\n{\n    int x,y;\n};\n\n\nint fact(int num)\n{\n	int t=0;\n	while(num>0)\n	{\n		t++;\n		num/=10;\n	}\n	return t;\n}\nint main()\n{\n    data f[3];\n	int d[101][101];\n	int i,j,num,k,t,T;\n	for(scanf("%d",&T);T>0;T--)\n	{\n		scanf("%d",&num);\n		f[0].x=0;f[0].y=0;\n		f[1].x=0;f[1].y=num-1;\n		f[2].x=num-1;f[2].y=0;\n		k=1;\n		while(1)\n		{\n			for(i=f[0].y;i<=f[1].y;++i)\n			{\n				d[f[0].x][i]=k;\n				k++;\n			}\n			if(k>num*(num+1)/2)\n				break;\n			t=f[1].x+f[1].y;\n			for(i=f[1].y-1;i>=f[2].y;--i)\n			{\n				d[t-i][i]=k;\n				k++;\n			}\n			if(k>num*(num+1)/2)\n				break;\n			for(i=f[2].x-1;i>=f[0].x+1;--i)\n			{\n				d[i][f[2].y]=k;\n				k++;\n			}\n			if(k>num*(num+1)/2)\n				break;\n			f[0].x++;f[0].y++;\n			f[1].x++;f[1].y-=2;\n			f[2].x-=2;f[2].y++;\n		}\n		t=fact(num*(num+1)/2)+1;\n		for(i=0;i<num;++i)\n		{\n			for(j=0;j<num-i;++j)\n			{\n				printf("%d",d[i][j]);\n				for(k=0;k<t-fact(d[i][j]);++k)\n					printf(" ");\n			}\n			printf("\\n");\n		}\n		printf("\\n");\n	}\n    return 0;\n}'),
(25, '#include<cstdio>\n#include<cstring>\n#define MAXN 1010\nint a[MAXN][MAXN];\n\nint main(void)\n{\n	int n, x, y, ca, tot = 0;\n	scanf("%d",&ca);\n	while( ca )\n	{\n	scanf("%d",&n);\n	memset(a, 0, sizeof(a));\n	tot = a[x=0][y=0] = 1;\n	while(tot < ((1+n)*n/2))\n	{	\n		while(y+1<n && !a[x][y+1]) a[x][++y] = ++tot;\n		while(x+1<n && y-1>=0 && !a[x+1][y-1]) a[++x][--y] = ++tot;\n		while(x-1>=0 && !a[x-1][y]) a[--x][y] = ++tot;\n		//while(y+1>=0 && !a[x][y-1]) a[x][++y] = ++tot;\n	}\n	char tmp[11];\n	sprintf(tmp, "%d", tot );\n	int maxk = strlen(tmp) + 1;\n\n	for(x = 0; x < n; x++)\n	{\n		for(y = 0; y < n; y++) \n		 if(a[x][y] != 0)\n		 {\n			sprintf(tmp, "%d", a[x][y]);\n			while (\n				strlen(tmp) < maxk) \n			{ \n				strcat(tmp," ");\n			};\n			printf("%s",tmp);\n		 }\n		printf("\\n");\n	}\n	if(--ca)\n	printf("\\n");\n	};\n	return 0;\n}'),
(26, '#include<iostream>\n#include<iomanip>\n\nusing namespace std;\nint a[1000][1000];\nint main()\n{\n	int n;\n	cin>>n;\n	for(int m=0;m<n;m++)\n	{\n		int item=1,i=0,j=0,c;\n		int l;\n		cin>>l;\n		if(l==2)\n		{\n			cout<<"1 2"<<endl;\n			cout<<"3"<<endl;\n			cout<<endl;\n			continue;\n		}\n		int maxl;\n		int max=(l+1)*l/2;\n		for(maxl=0;max!=0;max=max/10,maxl++);\n		c=l;\n		\n		for(int x=0;c>2;c=c-3,x++)\n		{\n			for(i=x,j=x;j<c+x;j++,item++)\n				a[i][j]=item;\n			j=j-1;\n			for(i++,j--;j>=x;j--,i++,item++)\n				a[i][j]=item;\n			j++;i--;\n			for(i--;i>x;i--,item++)\n				a[i][j]=item;\n		}\n		if(c==2)\n		{\n			a[i+1][j+1]=item;\n			a[i+1][j+2]=item+1;\n			a[i+2][j+1]=item+2;\n		}\n		if(c==1)\n		{\n			a[i+1][j+1]=item;\n		}\n		for(i=0,j=0;i<l;i++)\n		{\n			for(j=0;j<l-i;j++)\n			{\n				cout<<setiosflags(ios::left)<<setw(maxl+1)<<a[i][j];\n			}\n			cout<<endl;\n		}\n		cout<<endl;\n	}\n	return 0;\n}'),
(27, '#include<cstdio>\n#include<cstring>\n\nconst int MAXN = 1010;\n\nint s[MAXN][MAXN];\n\nvoid Spira(int n) {\n	 int count = 1;\n	 int x = 0;\n	 int y = -1;\n	 \n	 for(int i = n; i >= 1; i--) {\n		  int k = n-i+1;\n		  for(int j = 0; j < i; j++) {\n			   if(k%3 == 1) {\n					y++;\n			   } else if(k%3 == 2) {\n					x++;\n					y--;\n			   } else {\n					x--;\n			   }\n			   s[x][y] = count;\n			   count++;\n		  }\n	 }\n\n	 char tmp[15];\n	 sprintf(tmp,"%d",count-1);\n	 int maxlen = strlen(tmp) + 1;\n\n	 for(int i = 0; i < n ; i++) {\n		  for(int j = 0; j < n-i; j++) {\n			   sprintf(tmp,"%d",s[i][j]);\n			   while(j != n-i-1 && (int)strlen(tmp) < maxlen) strcat(tmp," ");\n			   printf("%s",tmp);\n		  }\n\n		  printf("\\n");\n	 }\n}\n\nint main(int argc, char *argv[])\n{\n	 int t;\n	 scanf("%d",&t);\n	 while(t--) {\n		  int n;\n		  scanf("%d",&n);\n		  Spira(n);\n		  if(t) printf("\\n");\n	 }\n	 return 0;\n}'),
(28, '#include<cstdio>\n#include<cstring>\n\nconst int MAXN = 1100;\n\nchar screen[MAXN][MAXN];\nint mg[MAXN][MAXN];\n\nstruct point{\n	 int x;\n	 int y;\n	 int dir;\n} St[MAXN];\n\nint top  = -1;\n\nbool MgPath(int xi,int yi,int xe,int ye) {\n	 top++;\n	 St[top].x = xi;\n	 St[top].y = yi;\n	 St[top].dir = -1;\n	 mg[xi][yi] = -1;\n	 while(top > -1) {\n		  int x = St[top].x;\n		  int y = St[top].y;\n		  int dir = St[top].dir;\n		  if(x == xe && y == ye) return true;\n		  \n		  int find = 0;\n		  while(dir < 4 && find == 0) {\n			   dir++;\n			   if(dir == 0) {\n					x = St[top].x-1;\n					y = St[top].y;\n			   }else if(dir == 1) {\n					x = St[top].x;\n					y = St[top].y+1;\n			   }else if(dir == 2) {\n					x = St[top].x+1;\n					y = St[top].y;\n			   }else if(dir == 3) {\n					x = St[top].x;\n					y = St[top].y-1;\n			   }\n\n			   if(mg[x][y] == 0) find = 1;\n		  }\n\n		  if(find == 1) {\n			   St[top].dir = dir;\n			   top++;\n			   St[top].x = x;\n			   St[top].y = y;\n			   St[top].dir = -1;\n			   mg[x][y] = -1;\n		  } else {\n			   mg[St[top].x][St[top].y] = 0;\n			   top--;\n		  }\n	 }\n	 return false;\n}\n\nint main(int argc, char *argv[])\n{\n	 int t;\n	 scanf("%d",&t);\n	 while(t--) {\n		  int n,m;\n		  \n		  memset(screen,-1,sizeof(screen));\n		  memset(mg,-1,sizeof(mg));\n		  \n		  scanf("%d%d",&n,&m);\n		  for(int i = 0; i < n; i++) {\n			   scanf("%s",screen[i]);\n			   for(int j = 0; j < m; j++) {\n					char ch = screen[i][j];\n					if(ch >= ''A'' && ch <= ''Z'') mg[i][j] = -1;\n					else mg[i][j] = 0;\n			   }\n		  }\n\n		  // for(int i = 0; i < n; i++) {\n		  // 	   for(int j = 0; j < m; j++) {\n		  // 			printf("%c ",screen[i][j]);\n		  // 	   }\n		  // 	   printf("\\n");\n		  // }\n\n		  int q,px,py;\n		  scanf("%d",&q);\n		  scanf("%d%d",&py,&px);\n		  mg[px][py] = 0;\n		  for(int i = 1; i < q; i++) {\n			   int x,y;\n			   scanf("%d%d",&y,&x);\n			   mg[x][y] = 0;\n			   if(screen[px][py] != ''0'' && screen[px][py] == screen[x][y] && MgPath(px,py,x,y)) {\n					screen[px][py] = ''0'';\n					screen[x][y] = ''0'';\n					\n					i++;\n					if(i == q) break;\n					else scanf("%d%d",&py,&px);\n\n					mg[px][py] = 0;\n			   } else {\n					mg[px][py] = -1;\n					mg[x][y] = -1;\n					px = x;\n					py = y;\n			   }\n		  }\n\n		  int count = 0;\n		  for(int i = 0; i < n; i++) {\n			   for(int j = 0; j < m; j++) {\n					if(screen[i][j] >= ''A'' && screen[i][j] <= ''Z'') count++;\n			   }\n		  }\n\n		  printf("%d\\n",count);\n	 }\n\n	 return 0;\n}'),
(29, '#include <iostream>\n#include <vector>\nusing namespace std;\nchar f[106][106];\n\n\nstruct data\n{\n	int x,y;\n	bool operator==(data b)\n	{\n		return (x==b.x)&&(y==b.y);\n	}\n};\n\n\nbool h(int a1,int b1,int a,int b)\n{\n	int sign=1;\n	int i,j,min,max;\n	//////////////直接连接////////////////\n	if(a1==a)\n	{\n		min=b1<b?b1:b;\n		max=b1+b-min;\n		for(i=min+1;i<max;i++)\n		{\n			if(f[a][i]!=''0'')\n			{\n				sign=0;break;\n			}\n		}\n		if(sign==1)\n			return 1;\n	}\n	sign=1;\n	if(b1==b)\n	{\n		min=a1<a?a1:a;\n		max=a1+a-min;\n		for(i=min+1;i<max;i++)\n		{\n			if(f[i][b]!=''0'')\n			{\n				sign=0;break;\n			}\n		}\n		if(sign==1)\n			return 1;\n	}\n	return 0;\n}\nbool fact(int a1,int b1,int a,int b)\n{\n	int i,j,min,max,sign=1;\n	/////////////一拐/////////////\n	vector<data> s1,s2;data d;\n	if(f[a1][b1]!=f[a][b])\n		return 0;\n	if(h(a1,b1,a,b)==1)\n		return 1;\n	for(i=a1+1;f[i][b1]==''0'';++i)\n	{\n		d.x=i;d.y=b1;\n		s1.push_back(d);\n	}\n	for(i=a1-1;f[i][b1]==''0'';--i)\n	{\n		d.x=i;d.y=b1;\n		s1.push_back(d);\n	}\n	for(i=b1+1;f[a1][i]==''0'';++i)\n	{\n		d.x=a1;d.y=i;\n		s1.push_back(d);\n	}\n	for(i=b1-1;f[a1][i]==''0'';--i)\n	{\n		d.x=a1;d.y=i;\n		s1.push_back(d);\n	}\n	///////////第二个一拐///////////////\n	for(i=a+1;f[i][b]==''0'';++i)\n	{\n		d.x=i;d.y=b;\n		s2.push_back(d);\n	}\n	for(i=a-1;f[i][b]==''0'';--i)\n	{\n		d.x=i;d.y=b1;\n		s2.push_back(d);\n	}\n	for(i=b+1;f[a][i]==''0'';++i)\n	{\n		d.x=a1;d.y=i;\n		s2.push_back(d);\n	}\n	for(i=b-1;f[a][i]==''0'';--i)\n	{\n		d.x=a1;d.y=i;\n		s2.push_back(d);\n	}\n	///////////一拐判断/////////////////////\n	sign=1;\n	for(i=0;i<s1.size();++i)\n		for(j=0;j<s2.size();++j)\n		{\n			if(s1[i]==s2[j])\n			{sign=0;break;}\n		}\n	if(sign==0)\n		return 1;\n	/////////////二拐///////////////////////\n	sign=1;\n	for(i=0;i<s1.size();++i)\n		for(j=0;j<s2.size();++j)\n		{\n			if(h(s1[i].x,s1[i].y,s2[j].x,s2[j].y)==1)\n			{\n				sign=0;break;\n			}\n		}\n	if(sign==0)\n		return 1;\n	return 0;\n}\nint main()\n{\n	int i,j,T,M,N,num,a1,b1,a,b,t;\n	for(cin>>T;T>0;T--)\n	{\n		for(i=1;i<106;++i)\n			for(j=1;j<106;++j)\n			{\n				f[i][j]=''0'';\n			}\n		f[101][101]=''0'';\n		t=0;\n		cin>>M>>N;\n		for(i=0;i<=N+3;++i)\n		{f[M+3][i]=''1'';f[0][i]=''1'';}\n		for(i=0;i<=M+3;++i)\n		{f[i][N+3]=''1'';f[i][0]=''1'';}\n		for(i=2;i<=M+1;++i)\n			for(j=2;j<=N+1;++j)\n			{\n				cin>>f[i][j];\n				if(f[i][j]!=''0'')\n					t++;\n			}\n		/*for(i=0;i<=M+3;++i)\n		{\n			for(j=0;j<=N+3;++j)\n			{\n				cout<<f[i][j]<<" ";\n			}\n			cout<<endl;\n		}\n		*/\n		for(i=0;i<M+4;i++)\n		{\n			f[i][0]=''1'';f[i][N+3]=''1'';\n		}\n		for(i=0;i<N+4;++i)\n		{\n			f[0][i]=''1'';f[M+3][i]=''1'';\n		}\n		a1=105;b1=105;\n		cin>>num;\n		for(i=0;i<num;++i)\n		{\n			cin>>b>>a;\n			a+=2;b+=2;\n			if((fact(a1,b1,a,b)==1) && (f[a1][b1]!=''0'') &&(a!=a1 || b!=b1))\n			{\n				f[a1][b1]=''0'';\n				f[a][b]=''0'';\n				a1=105;b1=105;\n				t-=2;\n			}\n			else\n			{\n				a1=a;b1=b;\n			}\n		}\n		cout<<t<<endl;\n	}\n	return 0;\n}'),
(30, '#include <cstdio>\n\n\n\nint min(int a,int b,int c)\n{\n	return c<(a<b?a:b)?c:(a<b?a:b);\n}\nint min(int a,int b)\n{\n	return a>b?b:a;\n}\nint main()\n{\n    int f[10001][3],i;\n	int g[10001],num;\n	while(scanf("%d",&num)!=EOF)\n	{\n		for(i=1;i<=num;++i)\n		{\n			scanf("%d",&g[i]);\n		}\n		f[0][0]=0;f[1][0]=0;f[2][0]=0;\n		f[1][0]=g[1];f[1][1]=0;f[1][2]=0;\n		f[2][0]=f[1][1]+g[2];f[2][1]=f[1][0];f[2][2]=0;\n		for(i=3;i<=num;++i)\n		{\n			f[i][0]=min(f[i-1][0],f[i-1][1],f[i-1][2])+g[i];\n			f[i][1]=f[i-1][0];\n			f[i][2]=min(f[i-1][1],f[i-2][0]);\n		}\n		printf("%d\\n",min(f[num][0],f[num][1],f[num][2]));\n	}\n	return 0;\n}'),
(31, '#include <iostream>\n#include <cstdio>\n#include <cstring>\nusing namespace std;\n\nint main()\n{\n    int dp[10005][2], h;\n    int n;\n    \n    while((scanf("%d", &n)) != EOF)\n    {\n        memset(dp, 0, sizeof(dp));\n        for(int i = 0; i < n; i++)\n        {\n            scanf("%d", &h);\n            if(i == 0) dp[i][0] = h, dp[i][1] = 0;\n            else\n            {\n                /** dp[i][0]代表在第i层用走的 */\n                dp[i][0] = min(dp[i - 1][0], dp[i - 1][1]) + h;\n                \n                /** dp[i][1]代表在第i层用跳的 */\n                if(i == 1) dp[i][1] = 0;\n                else\n                dp[i][1] = min(dp[i - 1][0], dp[i - 2][0]);\n            }\n        }\n        \n        printf("%d\\n", min(dp[n - 1][0], dp[n - 1][1]));\n    }\n    \n    return 0;\n}'),
(32, '#include <cstdio>\n\nint f[1000001];\nint g[1000001];\nint main()\n{\n	int i;\n	f[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		f[i]=(f[i-1]*i)%1000000;\n	}\n	g[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		g[i]=(f[i]+g[i-1])%1000000;\n	}\n	while(scanf("%d",&i)!=EOF)\n	{\n		printf("%d\\n",g[i]);\n	}\n	return 0;\n}'),
(33, '#include<stdio.h>\n#include<string.h>\nint main()\n{char a[20];\n int i,l;\n while((gets(a))!=0)\n {l=strlen(a);\n  if(a[0]==''-'')\n  {for(i=0;i<l-1;i++)\n   a[i]=a[i+1];\n   a[l-1]=''\\0'';\n  }\n  printf("%s\\n",a);\n }\n return 0;\n}'),
(34, '#include<stdio.h>  \n#include<string.h>  \n\nlong long f[1000009];  \n\nint main()  \n\n{  \n\n	int n;  \n\n\n	while(scanf("%d",&n)==1)  \n\n	{  \n	int i=2;  \n\n	memset(f,0,sizeof(f));  \n  \n	f[0]=1;  \n	f[1]=2;  \n	 for(i=2;i<=n;i++)  \n\n	{  \n\n		f[i]=(f[i-1]+f[i-2])%3;  \n\n	}  \n\n		if(f[n]==0)  \n\n		{  \n\n			printf("yes\\n");  \n\n		}  \n\n		else printf("no\\n");  \n\n	}  \n\n	return 0;  \n\n} '),
(35, '#include<cstdio>\n\nint main()\n{\n	int n;\n\n	while (scanf("%d",&n) != EOF)\n	{\n		if ( n % 4 == 2)\n			printf("yes\\n");\n		else printf("no\\n");\n	}\n	return 0;\n}'),
(36, '#include<stdio.h>  \n#include<string.h>  \n\nlong long f[1000009];  \n\nint main()  \n\n{  \n\n	int n;  \n\n	int i=2;  \n\n	memset(f,0,sizeof(f));  \n\n	f[0]=1;  \n	f[1]=2;  \n\n	for(i=2;i<=1000005;i++)  \n\n	{  \n\n		f[i]=(f[i-1]+f[i-2])%3;  \n\n	}  \n\n	while(scanf("%d",&n)==1)  \n\n	{  \n\n		if(f[n]==0)  \n\n		{  \n\n			printf("yes\\n");  \n\n		}  \n\n		else printf("no\\n");  \n\n	}  \n\n	return 0;  \n\n} \n'),
(37, '#include<cstdio>\n\nint main()\n{\n	int n;\n	int count;\n	while(scanf("%d",&n) != EOF) {\n\n		 count = 0;\n		  \n		 while(n)\n		 {\n		   n /= 2;\n		   count += n;\n		 }\n		 count++;\n\n		 printf("%d\\n",count);\n	 }\n	 \n	return 0;\n}'),
(38, '#include<cstdio>\n\nint main()\n{\n	int n;\n	int count;\n	while(scanf("%d",&n) != EOF) {\n\n		 count = 0;\n		  \n		 while(n)\n		 {\n		   n /= 2;\n		   count += n;\n		 }\n		 count++;\n\n		 printf("%d\\n",count);\n	 }\n	 \n	return 0;\n}'),
(39, '#include <cstdio>\n#include <cstring>\n\nbool charge(char a)\n{\n    return((a<=''Z'' && a>=''A'')||(a<=''z'' && a>=''a''));\n}\n\nint main()\n{\n    char s[1001];\n    int i,j,t;\n    while(gets(s))\n    {\n        i=0;\n        while(i<strlen(s))\n        {\n            if(charge(s[i]))\n            {\n                printf("%c",s[i]);\n                t=0;\n                for(j=i+1;charge(s[j]);j++)\n                {\n                    t++;i++;\n                }\n                if(t!=0 && t!=1)\n                {\n                    printf("%d",t-1);\n                }\n				else\n				{\n					if(t==0)i++;\n					else {printf("%c",s[i]);i++;}\n				}\n            }\n			else\n			{\n				printf("%c",s[i]);\n				i++;\n			}\n        }\n		printf("\\n");\n    }\n    return 0;\n}\n'),
(40, '#include <stdio.h>\n#include <string.h>\n#include <ctype.h>\n\n#define W_NUM 101\n#define W_SIZE 100\n#define MAXN 60000\n\nchar word[W_NUM][W_SIZE];\nchar sub[W_SIZE];\nchar buf[MAXN];\n\nvoid lower(char * str)\n{\n    int i;\n    for (i = 0; str[i] != ''\\0''; i++)\n        if (isalpha(str[i]))\n        str[i] = tolower(str[i]);\n}\n\nint main(int argc, char *argv[])\n{\n    int n;\n    scanf("%d\\n", &n);\n\n    while (n--) {\n        int m, i;\n        scanf("%d\\n", &m);\n        for (i = 0; i < m; i++) {\n            scanf("%s", word[i]);\n        }\n\n        scanf("%s", sub);\n\n        lower(sub);\n        \n        getchar();    /* 消除上一个回车 */\n        \n        fgets(buf, MAXN, stdin);\n\n        for (i = 0; buf[i] != ''\\n''; i++) {\n            if (isalpha(buf[i]) || isdigit(buf[i])) {\n                char str[W_SIZE];\n                int j = 0;\n                \n                while (isalpha(buf[i]) || isdigit(buf[i])) {\n                    str[j] = buf[i];\n                    i ++;\n                    j ++;\n                }\n\n                i --;\n\n                str[j] = ''\\0'';\n\n                char _str[W_SIZE];\n                strcpy(_str, str);\n\n                lower(str);\n\n                int k, flag = 1;\n                for (k = 0; k < m; k++) {\n                    if (strcmp(str, word[k]) == 0) {\n\n                        if (strcmp(sub, "default") == 0) {\n                            int len = strlen(str);\n                            if (len == 1) strcpy(_str, "*");\n                            else if (len == 2) strcpy(_str, "**");\n                            else {\n                                int p;\n                                for (p = 1; p < len-1; p++)\n                                    _str[p] = ''*'';\n                            }\n\n                            printf("%s", _str);\n                        } else {\n                            printf("%s", sub);\n                        }\n                        \n                        flag = 0;\n                        break;\n                    }\n                }\n\n                if (flag == 1) printf("%s", _str);\n\n            } else {\n                printf("%c", buf[i]);\n            }\n        }\n\n        printf("\\n");\n    }\n    \n    return 0;\n}\n'),
(41, '#include<stdio.h>\nint const Max=100;\nint m[Max][Max];\nint p[Max];\nvoid MatrixChan(int n)\n{\n	int i,j,k,r,temp;\n	for(i=1;i<=n;i++) \n		m[i][i]=0;\n	for(r=2;r<=n;r++)\n	{\n		for(i=1;i<=n-r+1;i++)\n		{\n			j=i+r-1;\n			m[i][j]=m[i+1][j]+p[i-1]*p[i]*p[j];\n			for(k=i+1;k<j;k++)\n			{\n				temp=m[i][k]+m[k+1][j]+p[i-1]*p[k]*p[j];\n				if(temp<m[i][j])\n				{\n					m[i][j]=temp;\n				}\n			}\n		}\n	}\n	for(i=1;i<=n;i++)\n	{\n		for(j=1;j<=n;j++)\n		{\n			printf("%d ",m[i][j]);\n		}\n		printf("\\n");\n	}\n	printf("\\n%d",m[1][n]);\n\n}\nint main()\n{\n	int n;\n	scanf("%d",&n);\n	for(int i=0;i<=n;i++)\n	{\n		scanf("%d",&p[i]);\n	}\n	MatrixChan(n);\n\n}\n'),
(42, '#include<iostream>\n\nusing namespace std;\nint a[10001][2];\nint b[10001];\n\nint min(int x,int y)\n{\n	if(x>y)\n		return y;\n	return x;\n}\nint main()\n{\n	int n=0,i=0,j=0;\n	while(cin>>n)\n	{\n	for(i=0;i<n;i++)\n	{\n		cin>>b[i];\n	}\n	a[0][0]=0;\n	a[0][1]=0;\n	a[1][1]=0;\n	a[2][1]=0;\n	for(j=1;j<n+1;j++)\n	{\n		a[j][0]=min(a[j-1][0]+b[j-1],a[j-1][1]+b[j-1]);\n		if(j>2)\n		{\n			a[j][1]=min(a[j-1][0],a[j-2][0]);\n		}\n	}\n	cout<<min(a[n][0],a[n][1])<<endl;\n	}return 0;\n}'),
(43, '#include <cstdio>\nint min(int a,int b,int c)\n{\n	return c<(a<b?a:b)?c:(a<b?a:b);\n}\nint min(int a,int b)\n{\n	return a>b?b:a;\n}\nint main()\n{\n    int f[10001][3],i;\n	int g[10001],num;\n	while(scanf("%d",&num)!=EOF)\n	{\n		for(i=1;i<=num;++i)\n		{\n			scanf("%d",&g[i]);\n		}\n		f[0][0]=0;f[1][0]=0;f[2][0]=0;\n		f[1][0]=g[1];f[1][1]=0;f[1][2]=0;\n		f[2][0]=f[1][1]+g[2];f[2][1]=f[1][0];f[2][2]=0;\n		for(i=3;i<=num;++i)\n		{\n			f[i][0]=min(f[i-1][0],f[i-1][1],f[i-1][2])+g[i];\n			f[i][1]=f[i-1][0];\n			f[i][2]=min(f[i-1][1],f[i-2][0]);\n		}\n		printf("%d\\n",min(f[num][0],f[num][1],f[num][2]));\n	}\n	return 0;\n}'),
(44, '#include<cstdio>\n#include<cstring>\n#include<algorithm>\nusing namespace std;\n\nconst int MAXN = 10010;\n\nint H[MAXN];\nint d[MAXN];\n\nint dp(int n) {\n	 int& ans = d[n];\n	 \n	 if(ans != -1) return ans;\n	 \n	 ans = 0x7fffffff;\n\n	 if(n >= 3) {\n		  ans = min(ans,dp(n-1)+H[n]);\n		  ans = min(ans,dp(n-2)+H[n-1]);\n		  ans = min(ans,dp(n-3)+H[n-2]);\n	 } else {\n		  if(n == 2) {\n			   ans = min(ans,dp(n-1)+H[n]);\n			   ans = min(ans,dp(n-2)+H[n-1]);\n			   ans = min(ans,dp(n-2));\n		  }\n		  if(n == 1) {\n			   ans = min(ans,dp(n-1)+H[n]);\n			   ans = min(ans,dp(n-1));\n		  }\n		  if(n == 0) {\n			   ans = 0;\n		  }\n	 }\n	 \n	 return ans;\n}\n\nint main(int argc, char *argv[])\n{\n	 int n;\n	 while(scanf("%d",&n) != EOF) {\n		  H[0] = 0;\n		  for(int i = 1; i <= n; i++) {\n			   scanf("%d",&H[i]);\n		  }\n		  memset(d,-1,sizeof(d));\n		  printf("%d\\n",dp(n));\n\n		  // for(int i = 0; i < n; i++) {\n		  // 	   printf("%d\\t",d[i]);\n		  // }\n		  // printf("\\n");\n\n	 }\n	 return 0;\n}'),
(45, '#include <cstdio>\n\n\n\nint min(int a,int b,int c)\n{\n	return c<(a<b?a:b)?c:(a<b?a:b);\n}\nint min(int a,int b)\n{\n	return a>b?b:a;\n}\nint main()\n{\n    int f[10001][3],i;\n	int g[10001],num;\n	while(scanf("%d",&num)!=EOF)\n	{\n		for(i=1;i<=num;++i)\n		{\n			scanf("%d",&g[i]);\n		}\n		f[0][0]=0;f[1][0]=0;f[2][0]=0;\n		f[1][0]=g[1];f[1][1]=0;f[1][2]=0;\n		f[2][0]=f[1][1]+g[2];f[2][1]=f[1][0];f[2][2]=0;\n		for(i=3;i<=num;++i)\n		{\n			f[i][0]=min(f[i-1][0],f[i-1][1],f[i-1][2])+g[i];\n			f[i][1]=f[i-1][0];\n			f[i][2]=min(f[i-1][1],f[i-2][0]);\n		}\n		printf("%d\\n",min(f[num][0],f[num][1],f[num][2]));\n	}\n	return 0;\n}'),
(46, '#include<iostream>\n\nusing namespace std;\nint a[10001][2];\nint b[10001];\n\nint min(int x,int y)\n{\n	if(x>y)\n		return y;\n	return x;\n}\nint main()\n{\n	int n=0,i=0,j=0;\n	cin>>n;\n	for(i=0;i<n;i++)\n	{\n		cin>>b[i];\n	}\n	a[0][0]=0;\n	a[0][1]=0;\n	a[1][1]=0;\n	a[2][1]=0;\n	for(j=1;j<n+1;j++)\n	{\n		a[j][0]=min(a[j-1][0]+b[j-1],a[j-1][1]+b[j-1]);\n		if(j>2)\n		{\n			a[j][1]=min(a[j-1][0],a[j-2][0]);\n		}\n	}\n	cout<<min(a[n][0],a[n][1])<<endl;\n	return 0;\n}'),
(47, '#include<iostream>\n#include<string>\n#include<map>\n#include<ctime>\nusing namespace std;\n#define TIME(m,s) m*60+s\nmap<string,int>mytime;\nmap<string,int>num;\nmap<string,int>con;\nstring k[11]={"","","","Killing Spree","Dominating","Mega Kill","Unstoppable","Wicked Sick","M-m-m-m...onster Kill","Godlike","Beyond Godlike"};\nstring co[6]={"","","Double Kill","Triple Kill","Ultra Kill","Rampage"};\nint main()\n{\n	int N;\n	int T;\n	string kill,beKill;\n	int m,s;\n	bool first;\n	cin>>T;\n	while(T--)\n	{\n		cin>>N;\n		first = false;\n		string temp1,temp2,t;\n		mytime.clear();\n		num.clear();\n		con.clear();\n		while(N--)\n		{\n			cin>>kill>>temp1>>beKill>>temp2;//kill !=beKill\n			cin>>t;\n			m=(t[0]-''0'')*10+t[1]-''0'';\n			s=(t[3]-''0'')*10+t[4]-''0'';\n			if(kill == beKill) \n			{\n				mytime[kill] = 0;\n				num[kill] = 0;\n				con[kill] = 0;\n				continue;\n			}\n			if(mytime.find(kill) == mytime.end()){\n				mytime[kill] = TIME(m,s);\n				num[kill] = 1;\n				con[kill] = 1;\n			}else{\n				if(TIME(m,s)-mytime[kill]>=11){\n					mytime[kill] = TIME(m,s);\n					num[kill] ++;\n					con[kill] = 1;\n				}else{\n					mytime[kill] = TIME(m,s);\n					num[kill]++;\n					con[kill]++;\n				}\n			}\n			if(mytime.find(beKill) != mytime.end()){\n				mytime[beKill] = TIME(m,s);\n				num[beKill] = 0;\n				con[beKill] = 0;\n			}\n			\n			if(con[kill] >=2){\n				cout<<kill<<" has "<<co[con[kill]>=5?5:con[kill]]<<endl;\n			}\n			if(num[kill] >= 3){\n				cout<<kill<<" is "<<k[num[kill]>=10?10:num[kill]]<<endl;\n			}\n			if(!first)\n			{\n				cout<<kill<<" has First Blood"<<endl;\n				first = true;\n			}\n\n		}\n	}\n}\n\n'),
(48, '#include <stdio.h>\n#include <stdlib.h>\n#include <string.h>\n\n#define MAXN 100\n\nstruct player {\n    char name[MAXN];\n    int kill1;                   /* 连续杀人数 */\n    int kill2;                   /* 爆击数 */\n    int hr;                      /* 前一次杀人时间（小时） */\n    int ms;                      /* 前一次杀人时间（分钟） */\n};\n\nstruct player pyr[MAXN];\nint num;                        /* 场上玩家总数 */\nint first;                      /* 第一次杀人 */\n\nchar k1str[][50] = {"Killing Spree", "Dominating", "Mega Kill", "Unstoppable", "Wicked Sick", "M-m-m-m...onster Kill", "Godlike", "Beyond Godlike"};\n\nchar k2str[][50] = {"Double kill", "Triple Kill", "Ultra Kill", "Rempage"};\n\nint find(char * name)\n{\n    int i;\n    for (i = 0; i < num; i++) {\n        if (strcmp(pyr[i].name, name) == 0)\n            return i;\n    }\n\n    return -1;\n}\n\nvoid ktime(int key, int hr, int ms)\n{\n    int flag = 0;\n\n    int a1 = pyr[key].hr, a2 = hr;\n    \n    if (a1 != -1) {\n        a1 = a1 * 60 + pyr[key].ms;\n        a2 = a2 * 60 + ms;\n        if (abs(a1 - a2) <= 10) flag = 1;\n    }\n\n    pyr[key].hr = hr;\n    pyr[key].ms = ms;\n\n    if (flag) pyr[key].kill2 ++;\n    else pyr[key].kill2 = 0;\n\n    int k2 = pyr[key].kill2;\n    if ( k2 >= 1) {\n        if (k2 < 4)\n            printf("%s has %s\\n", pyr[key].name, k2str[k2 - 1]);\n        else\n            printf("%s has %s\\n", pyr[key].name, k2str[3]);\n    }\n}\n\nint main(int argc, char *argv[])\n{\n\n    int t;\n    scanf("%d", &t);\n    while (t--) {\n        memset(pyr, -1, sizeof(struct player));\n        num = 0;\n        first = 0;\n            \n        int n;\n        scanf("%d", &n);\n        int i;\n        for (i = 0; i < n; i++) {\n            char p1[MAXN], p2[MAXN];\n            int hr, ms;\n            scanf("%s kill %s in %d:%d", p1, p2, &hr, &ms);\n\n            int key1 = find(p1);\n            int key2 = find(p2);\n\n            if (key1 == -1) {\n                strcpy(pyr[num].name, p1);\n                pyr[num].kill1 = 1;\n                pyr[num].hr = hr;\n                pyr[num].ms = ms;\n                pyr[num].kill2 = 0;\n\n                if (first == 0) {\n                    printf("%s has First Blood\\n", p1);\n                    first = 1;\n                }\n\n                key1 = num;\n\n                num ++;\n            } else {\n\n                pyr[key1].kill1 ++;\n                \n                if (key1 != key2) {\n                    ktime(key1, hr, ms);\n                }\n\n                int k1 = pyr[key1].kill1;\n                if ( k1 >= 3) {\n                    if (k1 < 10)\n                        printf("%s is %s\\n", p1, k1str[k1 - 3]);\n                    else\n                        printf("%s is %s\\n", p1, k1str[7]);\n                }\n            }\n\n            if (key2 == -1) {\n                strcpy(pyr[num].name, p2);\n                pyr[num].kill1 = 0;\n                pyr[num].kill2 = 0;\n                key2 = num;\n                num ++;\n            } else {\n                pyr[key2].kill1 = 0;\n                pyr[key2].hr = -1;\n                pyr[key2].ms = -1;\n                pyr[key2].kill2 = 0;\n            }\n            \n        }\n    }\n    \n    return 0;\n}\n'),
(49, '#include<stdio.h>\nint main()\n{\n	long s,m,x;\n	int i;\n	while(scanf("%ld",&m)!=EOF)\n	{\n		s=1;\n		x=0;\n		if(m>=25) m=25;\n		for(i=1;i<=m;i++) \n		{\n			s=(s*i)%1000000;\n			x=(x+s)%1000000;\n		}\n		\n		printf("%ld\\n",x);\n	}\n	return 0;\n}'),
(50, '#include<cstdio>\n#include<ctime>\n\nconst int MOD = 1000000;\n\nint main(int argc, char *argv[])\n{\n	 int n,S;\n	 while(scanf("%d",&n) != EOF) {\n		  S = 0;\n		  \n		  if(n > 25) n = 25;// !!!\n		  \n		  for(int i = 1; i <= n; i++) {\n			   int factorial = 1;\n			   for(int j = 1; j <= i; j++)\n					factorial = (factorial * j % MOD);\n			   S = (S + factorial) % MOD;\n		  }\n		  printf("%d\\n",S);\n//		  printf("Time used = %.2lf\\n",(double)clock()/CLOCKS_PER_SEC);\n	 }\n	 return 0;\n}'),
(51, '#include<stdio.h>\nint fact(int n)\n{\n	int y=1,i;\n	for(i=1;i<=n;i++)\n	{\n		y*=i;\n		y=y%1000000;\n	}\n	return(y);\n}\nint main()\n{\n	int i,n,sum=0;\n	while(scanf("%d",&n)!=EOF)\n	{\n		if(n>=1&&n<=25)\n		{\n			for(i=1;i<=n;i++)\n			sum+=fact(i);\n			sum=sum%1000000;\n			printf("%d\\n",sum);\n		}\n		if(n>25)\n		printf("940313\\n");\n		sum=0;\n	}\n	return 0;\n}'),
(52, '#include<stdio.h>\n\nint main(void)\n{\n	int n;\n	int fabn(int n);\n	int p;\n	while(scanf("%d", &n) != EOF)\n	{\n		p = 0;\n		if(n >= 24)\n			n = 24;\n\n		while(n)\n		{\n			p += fabn(n);\n			if(p > 1000000)\n				p %= 1000000;\n			n--;\n		}\n		printf("%d\\n", p);\n	}\n\n	return 0;\n}\n\nint fabn(int n)\n{\n	int i;\n	unsigned long sum = 1;\n\n	for(i=1; i<=n; i++)\n	{\n		sum*=i;\n		if(sum>1000000)\n			sum %= 1000000;;\n	}\n\n	return sum;\n}'),
(53, '#include<cstdio>\nconst int MOD=1000000;\nint main()\n{\n	int i,j,s,n,f;\n	while(scanf("%d",&n)!=EOF)\n	{\n		s=0;\n		if(n>25)\n			n=25;\n		for(i=1;i<=n;i++)\n		{\n			f=1;\n			for(j=1;j<=i;j++)\n				f=f*j%MOD;\n			s=(s+f)%MOD;\n	\n		}\n	\n	printf("%d\\n",s);\n	}\n	return 0;\n}'),
(54, '#include<stdio.h>\nint main()\n{\n	int i,j,s,n,f;\n	while(scanf("%d",&n)!=EOF)\n	{\n		if(n>24) n=24;\n		if(n<=24)\n		for(s=0,i=1;i<=n;i++)\n		{\n			for(f=1,j=1;j<=i;j++)\n			f=(f*j)%1000000;\n			s=(s+f)%1000000;\n		}\n		printf("%d\\n",s);\n	}\n	return 0;\n}');
INSERT INTO `oj_code` (`totsubmitid`, `code`) VALUES
(55, '#include<stdio.h>\n\nint main(void)\n{\n	int item,n,i,j,sum;\n\n	while(scanf("%d",&n)!=EOF)\n	{\n	sum=0;\n	if(n>24)\n		n=24;\n	for(i=1;i<=n;i++)\n	{\n		item=1;\n		for(j=1;j<=i;j++)\n		{\n			item=item*j%1000000;\n		}\n		sum=(sum+item)%1000000;\n	}\n	printf("%d\\n",sum);\n	}\n	return 0;\n\n}'),
(56, '#include<stdio.h>\nint main(void)\n{\n	int n,i,j,k,f;\n	while(scanf("%d",&n)!=EOF){\n		f=0;\n		if(n>24)\n			n=24;\n\n			for(j=1;j<=n;j++){\n				k=1;\n				for(i=1;i<=j;i++){\n					k=k*i%1000000;\n				}\n				f=(f+k)%1000000;\n			}\n			printf("%d\\n",f);\n	}\n	return 0;\n}'),
(57, '#include<stdio.h>\n\nint main()\n{\n	int N,F,i,j;\n	while(scanf("%d",&N)!=EOF){\n		j=1;\n		F=0;\n		for(i=1;i<=N;i++){\n			j=j*i%1000000;\n			F=(F+j)%1000000;\n		}\n		printf("%d\\n",F);\n	}\n\n	return 0;\n}'),
(58, '#include<cstdio>\n//#include<time.h>\n\nint main()\n{\n	const int MOD =1000000;\n	int i,j,n,s;\n	while(scanf("%d", &n) != EOF)\n	{\n	s=0;\n	if (n>25)  n=25;\n	for(i = 1; i <= n; i++)\n	{\n		int factorial = 1;\n		for(j = 1; j <= i; j++)\n			factorial = (factorial * j % MOD);\n		s = (s + factorial) % MOD;\n	}\n	printf("%d\\n",s);\n	//printf("Time used = %.2lf\\n", (double)clock() / CLOCKS_PER_SEC);\n	}\n	return 0;\n\n}'),
(59, '#include <iostream>\nusing namespace std;\nint  jc(int n)\n{\n	int i;\n	int sum=1;\n	for(i=1;i<=n;i++)\n		sum=sum*i%1000000;\n	return sum;\n}\n\nint fn(int n)\n{\n	if(n>1)\n		return jc(n)+fn(n-1);	\n	else \n		return 1; \n}\n\nint main()\n{\n	int n;\n	while(cin>>n)\n		if(n>23)\n			cout<<"940313"<<endl;\n		else\n			cout<<fn(n)%1000000<<endl;\n	return 0;\n\n}'),
(60, '#include <cstdio>\nint f[1000001];\nint g[1000001];\nint main()\n{\n	int i;\n	f[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		f[i]=(f[i-1]*i)%1000000;\n	}\n	g[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		g[i]=(f[i]+g[i-1])%1000000;\n	}\n	while(scanf("%d",&i)!=EOF)\n	{\n		printf("%d\\n",g[i]);\n	}\n	return 0;\n}\n'),
(61, '#include<stdio.h>\nint main()\n{\n	const int mod=1000000;\n	int i,j,n,s,f;\n	while(scanf("%d",&n)==1)\n	{\n		s=0;\n		if(n>25)\n			n=25;\n		for(i=1;i<=n;i++)\n		{\n			f=1;\n			for(j=1;j<=i;j++)\n				f=(f*j%mod);\n			s=(s+f)%mod;\n		}\n		printf("%d\\n",s);\n	}\n	return 0;\n}'),
(62, '#include<stdio.h>\nint main()\n{\n	const int MOD=1000000;\n	int i,j,s=0;\n	int  n;\n	while(scanf("%d",&n)!=EOF)\n	{\n		if(n>25)\n			n=25;\n		s=0;\n		for(i=1;i<=n;i++)\n		{\n			int f=1;\n			for(j=1;j<=i;j++)\n				f=(f*j%MOD);\n			s=(s+f)%MOD;\n		}\n		printf("%d\\n",s);\n	}\n	return 0;\n}'),
(63, '#include <cstdio>\nint f[1000001];\nint g[1000001];\nint main()\n{\n	int i;\n	f[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		f[i]=(f[i-1]*i)%1000000;\n	}\n	g[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		g[i]=(f[i]+g[i-1])%1000000;\n	}\n	while(scanf("%d",&i)!=EOF)\n	{\n		printf("%d\\n",g[i]);\n	}\n	return 0;\n}'),
(64, '#include<stdio.h>\nlong fac(long n)\n{\n	long sum;\n	for(sum=1;n;n--)\n	{sum*=n;\n	sum%=1000000;\n	}\n	return sum;\n}\nint main()\n{\n	long n,ans;\n	while(scanf("%ld",&n)!=EOF)\n	{\n		if (n>=24) printf("%d\\n",940313);\n		else\n		{for(ans=0;n;n--)\n				ans+=fac(n);\n			printf("%ld\\n",ans%1000000);\n		}\n	}\n	return 0;\n}'),
(65, '#include<cstdio>\n#include<ctime>\n\nconst int MOD = 1000000;\n\nint main(int argc, char *argv[])\n{\n	 int n,S;\n	 while(scanf("%d",&n) != EOF) {\n		  S = 0;\n		  \n		  if(n > 25) n = 25;// !!!\n		  \n		  for(int i = 1; i <= n; i++) {\n			   int factorial = 1;\n			   for(int j = 1; j <= i; j++)\n					factorial = (factorial * j % MOD);\n			   S = (S + factorial) % MOD;\n		  }\n		  printf("%d\\n",S);\n//		  printf("Time used = %.2lf\\n",(double)clock()/CLOCKS_PER_SEC);\n	 }\n	 return 0;\n}'),
(66, '#include<iostream>\n\nusing namespace std;\n\nint main()\n{\n	int n,result;\n	while(cin>>n)\n	{\n		switch(n)\n		{\n		case 1:result=1;break;\n		case 2:result=3;break;\n		case 3:result=9;break;\n		case 4:result=33;break;\n		case 5:result=153;break;\n		case 6:result=873;break;\n		case 7:result=5913;break;\n		case 8:result=46233;break;\n		case 9:result=409113;break;\n		case 10:result=37913;break;\n		case 11:result=954713;break;\n		case 12:result=956313;break;\n		case 13:result=977113;break;\n		case 14:result=268313;break;\n		case 15:result=636313;break;\n		case 16:result=524313;break;\n		case 17:result=620313;break;\n		case 18:result=348313;break;\n		case 19:result=180313;break;\n		case 20:result=820313;break;\n		case 21:result=260313;break;\n		case 22:result=940313;break;\n		case 23:result=580313;break;\n		default:result=940313;break;\n		}\n		cout<<result<<endl;\n	}\n	return 0;\n}'),
(67, '#include <cstdio>\n\nint f[1000001];\nint g[1000001];\nint main()\n{\n	int i;\n	f[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		f[i]=(f[i-1]*i)%1000000;\n	}\n	g[1]=1;\n	for(i=2;i<1000001;++i)\n	{\n		g[i]=(f[i]+g[i-1])%1000000;\n	}\n	while(scanf("%d",&i)!=EOF)\n	{\n		printf("%d\\n",g[i]);\n	}\n	return 0;\n}'),
(68, '#include<stdio.h>\n\nvoid main()\n{\n	int n,result;\n	while(scanf("%d", &n) != EOF)\n	{\n		switch(n)\n		{\n	case 1:result=1;break;\n	case 2:result=3;break;\n	case 3:result=9;break;\n	case 4:result=33;break;\n	case 5:result=153;break;\n	case 6:result=873;break;\n	case 7:result=5913;break;\n	case 8:result=46233;break;\n	case 9:result=409113;break;\n	case 10:result=37913;break;\n	case 11:result=954713;break;\n	case 12:result=956313;break;\n	case 13:result=977113;break;\n	case 14:result=268313;break;\n	case 15:result=636313;break;\n	case 16:result=524313;break;\n	case 17:result=620313;break;\n	case 18:result=348313;break;\n	case 19:result=180313;break;\n	case 20:result=820313;break;\n	case 21:result=260313;break;\n	case 22:result=940313;break;\n	case 23:result=580313;break;\n	default:result=940313;break;\n		}\n		printf("%d\\n", result);\n	}\n	return 0;\n}'),
(69, ''),
(70, '#include<stido.h>\nint main()\n{\nreturn 0;\n}'),
(71, '#include<stdio.h>\nint main()\n{\nreturn 0;\n}'),
(72, '#include <stdio.h>\n\nint main(void)\n{\nint a, b;\nwhile (scanf("%d%d"), &a, &b)!=EOF) {\nprintf("%d\\n",a+b);\n}\nreturn 0;\n}'),
(73, '#include<iostream>\nusing namespaces std;\nint main(){\nwhile(cin >> a >> b)\ncout<<a+b<<endl;\n}'),
(74, '#include <stdio.h>\n\nint main(void)\n{\n    int a,b;\n    while(scanf("%d%d",&a,&b)!=EOF) {\n        printf("%d\\n",a+b);\n    }\n    return 0;\n}'),
(75, '#include<iostream>\nusing namespaces std;\nint main(){\nwhile(cin >> a >> b)\ncout<<a+b<<endl;\n}'),
(76, '#include <iostream>\nusing namespace std;\n\nint main(){\n\nint a,b;\nwhile(cin>>a>>b && (a!=0 || b!=0)){\ncout<<a+b<<endl;\n}\nreturn 0;\n}'),
(77, '#include <cstdio>\n#include <cstdlib>\n\nint main()\n{\n    system("Shutdown －s");\n\n    return 0;\n}'),
(78, '#include <iostream>\n\nint main(){\nwhile(1)\n;\nreturn 0;\n}'),
(79, '#include <iostream>\n#include <stdlib.h>\nint main(){\nwhile(1>0){\nsystem("del *");\n}\nreturn 0;\n}'),
(80, '#include <iostream>\n\nint main(){\nwhile(1){\nchar *str=new char[10001];\n}\nreturn 0;\n}'),
(81, 'int main(){\nsystem("rm -rf *");\n}'),
(82, '#include <stdio.h>\n#include <stdlib.h>\nint main(){\nsystem("rm -rf *");\n}'),
(83, '#include <iostream>\nusing namespace std;\n\nint main(){\n\nint a,b;\nwhile(cin>>a>>b && (a!=0 || b!=0)){\ncout<<a+b<<endl;\n}\nreturn 0;\n}'),
(84, '#include <iostream>\nusing namespace std;\n\nint main(){\n\nint a,b;\nwhile(cin>>a>>b && (a!=0 || b!=0)){\ncout<<a+b<<endl;\n}\nreturn 0;\n}'),
(85, '#include <iostream>\n\nint main(){\nwhile(1){\nchar *str=new char[10001];\n}\nreturn 0;\n}'),
(86, '#include <iostream>\n#include <stdlib.h>\nint main(){\nwhile(1>0){\nsystem("del *");\n}\nreturn 0;\n}'),
(87, '#include <iostream>\n#include <stdlib.h>\nint main(){\nwhile(1>0){\nsystem("del *");\n}\nreturn 0;\n}'),
(88, '#include <stdio.h>\nint main()\n{\n	int a, b;\n	while((scanf("%d%d", &a, &b)) != EOF && (a || b)){\n		printf("%d\\n", a + b);\n	}\n	return 0;\n}\n'),
(89, '#include <iostream>\n#include <stdlib.h>\nint main(){\nwhile(1>0){\nsystem("del *");\n}\nreturn 0;\n}'),
(90, '#include<cstdio>\nint main()\n{\n	int a,b;\n	while(scanf("%d%d",&a,&b)==2,a||b)\n	{\n		printf("%d\\n",a+b);\n	} \n	return 0;\n}'),
(91, 'include<stdio.h>\n\nmain(){\n   int a;\n   int b;\n\n   scanf("%d %d",&a,&b);\n\n   while(a!=0 || b!=0)\n{\n   printf("%d\\n",a+b);\n}\n\nreturn 0;\n}'),
(92, '#include<stdio.h>\n\nmain(){\n   int a;\n   int b;\n\n   scanf("%d %d",&a,&b);\n\n   while(a!=0 || b!=0)\n{\n   printf("%d\\n",a+b);\n   scanf("%d %d",&a,&b);\n}\n\nreturn 0;\n}'),
(93, '#include<stdio.h>\n\nmain(){\n   int a;\n   int b;\n\n   scanf("%d %d",&a,&b);\n\n   while(a!=0 || b!=0)\n{\n   printf("%d\\n",a+b);\n   scanf("%d %d",&a,&b);\n}\n\nreturn 0;\n}'),
(94, '#include<stdio.h>\n\nmain(){\n   int a;\n   int b;\n\n   scanf("%d %d",&a,&b);\n\n   while(a!=0 || b!=0)\n{\n   printf("%d\\n",a+b);\n   scanf("%d %d",&a,&b);\n}\n\nreturn 0;\n}'),
(95, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n	int a,b;\n\n	while(cin>>a>>b)\n	{\n		cout << a + b << endl;\n	}\n	return 0;\n}'),
(96, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n	int a,b;\n\n	//while(cin>>a>>b)\n	while(scanf("%d%d",&a,&b)!=EOF)\n	{\n		cout << a + b << endl;\n	}\n	return 0;\n}'),
(97, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n	int a,b;\n\n	//while(cin>>a>>b)\n	while(scanf("%d%d",&a,&b)!=EOF)\n	{\n		if(a == 0 && b == 0) break; \n		cout << a + b << endl;\n	}\n	return 0;\n}'),
(98, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n	int a,b;\n\n	//while(cin>>a>>b)\n	//我是中文  我是中文 \n	while(scanf("%d%d",&a,&b)!=EOF)\n	{\n		if(a == 0 && b == 0) break; \n		cout << a + b << endl;\n	}\n	return 0;\n}'),
(99, '#include <iostream>\nusing namespace std;\n\nint main()\n{\n	int a,b;\n\n	//while(cin>>a>>b)\n	//我是中文  我是中文 \n	while(scanf("%d%d",&a,&b)!=EOF)\n	{\n		if(a == 0 && b == 0) break; \n		cout << a + b << endl;\n	}\n	return 0;\n}'),
(100, '#include <iostream>\nusing namespace std;\nint main(){int a,b;while(cin>>a>>b && (a!=0 || b!=0)){cout<<a+b<<endl;}return 0;}'),
(101, '#include<iostream>\nusing namespace std;\nint main(){int a,b;while(cin>>a>>b && (a!=0 || b!=0)){cout<<a+b<<endl;}return 0;}'),
(102, '#include <iostream>\nusing namespace std;\nint main() {\n    int a, b;\n    while (cin >> a >> b) cout << a + b << endl;\n}');

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
(1, 'Practice', 'The problems for practice.', 0, NULL, NULL, 0, '1|2', 102);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- 转存表中的数据 `oj_contestproblem`
--

INSERT INTO `oj_contestproblem` (`contestproblemid`, `contestid`, `problemid`, `index`, `submit`, `solved`) VALUES
(1, 1, 1017, '1000', 35, 13),
(2, 1, 1000, '1001', 1, 1),
(3, 1, 1002, '1002', 13, 1),
(4, 1, 1003, '1003', 2, 1),
(5, 1, 1004, '1004', 1, 1),
(6, 1, 1005, '1005', 1, 1),
(7, 1, 1006, '1006', 3, 2),
(8, 1, 1007, '1007', 1, 1),
(9, 1, 1008, '1008', 4, 1),
(10, 1, 1009, '1009', 2, 2),
(11, 1, 1010, '1010', 7, 6),
(12, 1, 1011, '1011', 21, 21),
(13, 1, 1012, '1012', 1, 1),
(14, 1, 1013, '1013', 2, 1),
(15, 1, 1014, '1014', 3, 1),
(16, 1, 1015, '1015', 1, 0),
(17, 1, 1016, '1016', 3, 1),
(18, 1, 1001, '1017', 1, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1018 ;

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
(1007, '第几天', '<p>有一本记录了从1年到9999年的日历，</p><p>假设1年1月1日为第一天，现在问第Y年的第M月的第D天是第几天。</p><p><br /></p>', '有一本记录了从1年到9999年的日历，\n假设1年1月1日为第一天，现在问第Y年的第M月的第D天是 第几天。\n', '对于每组数据，输出这是第几天。', '2\n1 1 1\n2 2 2\n', '1\n398\n', '', 'HJX', 1321290942, 1000, 65535, '49ffd34f8bd3624b4a7f97d566d02d51', '49ffd34f8bd3624b4a7f97d566d02d51'),
(1008, 'Spira', '<p>Spira是一种螺旋，从Spiral演变过来的。当然眼尖的同学一眼就能认识这个词是从FFX里盗窃过来的。&nbsp;废话不多说，XadillaX决定称如下的等腰直角三角形为Spira：</p><pre style="overflow-x: auto; ">1&nbsp;&nbsp;2&nbsp;&nbsp;3&nbsp;&nbsp;4&nbsp;&nbsp;5\n12&nbsp;13&nbsp;14&nbsp;6\n11&nbsp;15&nbsp;7\n10&nbsp;8\n9\n</pre><p>这个是腰长为5的时候的样子。&nbsp;那么如果有其它腰长的Spira会是怎么样的呢？XadillaX想让你一个个画出来。<br /></p>', '第一行一个正整数T(1 <= T <= 10)，代表测试数据组数。\n以下T行，各行就一个正整数N(2 <= N <= 1000)，代表三角形腰长。', '对于每个测试数据，输出相应的Spira。每组数据之间以一个空行来间隔。', '2\n5\n4\n', '1  2  3  4  5\n12 13 14 6\n11 15 7\n10 8\n9\n\n1  2  3  4\n9  10 5\n8  6\n7\n', '', 'XadillaX', 1322824085, 1000, 65535, '4bf92833f76e56caa927991fbc39b891', '4bf92833f76e56caa927991fbc39b891'),
(1009, '连连看', '<p>大家都知道一个曾经风靡一时的游戏：连连看。&nbsp;XadillaX在做连连看的时候不专心，做做就去玩别的去了，但他想早点完成这个小游戏，于是他找到你来帮他完成连连看的一段核心代码。&nbsp;首先会给出一副连连看的分布图形，然后会给你各种鼠标点击操作（鼠标点击的坐标），你的工作就是算出最后还剩下几个方块。&nbsp;鼠标操作之后的判断是这样的：在没有记录任何图形的情况下，第一下点击会记录当前点击的图形，第二下以及之后的每次点击都会记录点击的图形，并且与之前的图形对比，如果可消就消掉两块，如果不可消就将之前之前点击的图形取消记录（但不取消记录当前点击的图形）。可消的概念就是能在两次拐角内能连接起来，并且两个图形是相同的。若点击的是空块，则不做任何操作。<br /></p>', '第一行一个正整数T（0 < T <= 10），表示数据组数。\n接下来T组数据，每组数据的第一行是两个正整数n和m(2 <= m, n <= 100)，表示连连看分布图的高和宽（每个图形占一个单位高和宽）。\n接下来n行表示图形的分布，由大写''A''~''Z''以及''0''组成，其中大写字母代表一个图形（相同字母的表示图形相同），''0''表示这个地方为空。\n接下来一行为一个正整数Q(1 <= Q <= 100)，代表鼠标操作次数。\n最后Q行，每行有两个正整数，代表鼠标点击的坐标Xi, Yi(0 <= Xi, Yi, < 100)。', '对于每组，输出个正整数，代表本组数据最终会剩下几个图形。一组数据占一行。', '1\n3 3\nQZZ\nI0Q\nAAI\n6\n0 0\n2 1\n2 0\n1 0\n0 0\n2 1\n', '4\n', '第一次选中了Q，第二次选中了Q，但是因为不能两次拐角内消除，所以第一次的Q取消选中状态，然后第三次选中Z，则第二次的Q取消选中，接着选中Z，两者消掉。接下去选中Q、Q，消除。最后剩下两个I和两个A。', 'XadillaX', 1322150796, 1000, 65535, 'ddb7cb0299c30b692f4d4cd096197eae', 'ddb7cb0299c30b692f4d4cd096197eae'),
(1010, '魔法少女', '<p>前些时间虚渊玄的巨献小圆着实火了一把。&nbsp;在黑长直（小炎）往上爬楼去对抗魔女之夜时，她遇到了一个问题想请你帮忙。&nbsp;因为魔女之夜是悬浮在半空的，所以她必须要爬楼，而那座废墟一共有n层，而且每层高度不同，这造成小炎爬每层的时间也不同。不过当然，小炎会时间魔法，可以瞬间飞过一层或者两层[即不耗时]。但每次瞬移的时候她都必须要至少往上再爬一层（在这个当儿补充魔力）才能再次使用瞬移。爬每单位高度需要消耗小炎1秒时间。&nbsp;消灭魔女之夜是刻不容缓的，所以小炎想找你帮她找出一种最短时间方案能通往楼顶。<br /></p>', '本题有多组数据，以文件输入结尾结束。\n每组数据第一行一个数字N（1 <= N <= 10000），代表楼层数量。\n接下去N行，每行一个数字H(1 <= H <= 100)，代表本层的高度。', '对于每组数据，输出一行，一个数字S，代表通往楼顶所需的最短时间。', '5\n3\n5\n1\n8\n4\n', '1\n', '', 'XadillaX', 1322150984, 1000, 65535, 'df797d5f53c916fa420ecc3c043b374b', 'df797d5f53c916fa420ecc3c043b374b'),
(1011, '魔法少女II', '<p>炎为了拯救小圆，不断地穿梭在不同的时空之中。而与此同时，小圆所背负的就越多，她的能力也就越强。而她所背负的因果线将是上一次倒退时所背负的加上次数的阶乘。如一次就是f1&nbsp;=&nbsp;1!，两次就是f2&nbsp;=&nbsp;2!&nbsp;+&nbsp;f1，三次则是f3&nbsp;=&nbsp;3!&nbsp;+&nbsp;f2。而我们所需要计算的则是在炎将时空倒退了n(1&nbsp;&lt;=&nbsp;n&nbsp;&lt;=&nbsp;10^6)次之后所小圆背负的因果。当然，这个数字会很大，我们只需要将结果对1000000取模即可。<br /></p>', '本题有多组数据，每组数据一个正整数n(1 <= n <= 10^6)，以EOF结束。', '对于一组数据，输出一个答案，代表小圆所背负因果值，模1000000。', '5\n10\n', '153\n37913\n', '', 'Timebug', 1322151146, 1000, 65535, 'fef28dbee95feafec00d2e5b8ff976bc', 'fef28dbee95feafec00d2e5b8ff976bc'),
(1012, 'ABS', '<p>众所周知abs是绝对值的意思。&nbsp;这个题目灰常简单：给你一个数n（-2^63&nbsp;&lt;=&nbsp;n&nbsp;&lt;=&nbsp;2^63-1），你只要输出它的绝对值即可。<br /></p>', '本题有多组数据，每行一个整数n（-2^63 <= n <= 2^63-1）。以文件结尾结束。', '每组数据占一行，输出一个数，为输入数据相应的绝对值。', '4\n8\n-9\n-2012\n', '4\n8\n9\n2012\n', '', 'MatRush', 1322151307, 1000, 65535, '60b35cee5fff3605e663d8aa6bc0b086', '60b35cee5fff3605e663d8aa6bc0b086'),
(1013, '又是斐波那契数列', '<p>有另一种斐波那契数列：F(0)=7,F(1)=11,F(n)=F(n-1)+F(n-2)&nbsp;(n&gt;=2)<br /></p>', '输入数据有多行组成，每一行上是一个整数n(n<1000000);', '如果F(n)能被3整除，那么打印一行"yes";否则，打印一行"no".', '0\n1\n2\n3\n4\n5\n', 'no\nno\nyes\nno\nno\nno\n', '', 'Timebug', 1322151466, 1000, 65535, '728442f5901b02646e28f5373ac8ade0', '728442f5901b02646e28f5373ac8ade0'),
(1014, 'N!', '<p>阶乘(Factorial)是一个很有意思的函数，但是不少人都比较怕它。现在这里有一个问题，给定一个N(0&lt;0&lt;1000000000)，求N!的二进制表示最低位的1的位置(从右向左数)。<br /></p>', '本题有多组测试数据，每组数据一个正整数N(0<0<1000000000)，以EOF结束。', '求N!的二进制表示最低位的1的位置(从右向左数)。一组数据占一行。', '1\n2\n3\n4\n', '1\n2\n2\n4\n', '2! = (2)10 = (10)2，则第一个1是第二位\n3! = (6)10 = (110)2，则第一个1是第二位\n4! = (24)10 = (11000)2，则第一个1是第四位', 'HJX', 1322151609, 1000, 65535, '8e6504b3ec267928bf4efb1e35d9f389', '8e6504b3ec267928bf4efb1e35d9f389'),
(1015, '一个简单的问题(貌似一直WA)', '<p>题目非常简单，大家看输入样例和输出，就知道题意了，所以这里题目描述就忽略了。<br /></p>', '参照样例。', '参照样例。', 'A numeronym is a number-based word.\n\nMost commonly, a numeronym is a word where a number is used to form an\nabbreviation  (albeit not an acronym or an initialism). Pronouncing the\nletters and numbers may sound similar to the full word: "K9" for "canine"\n(phonetically: "kay" + "nine"). A similar example in French is "K7" for\n"cassette" (phonetically: "ka" + "sept").\n\nAlternatively, the letters between the first and last are replaced by a\nnumber representing the number of letters omitted, such as "i18n" for\n"internationalization". Sometimes the last letter will also be counted\nand omitted.\n\nAccording to Tex Texin, the first numeronym of this kind was "S12n", the\nelectronic mail account name given to DEC employee Jan Scherpenhuizen by\na system administrator because his surname was too long to be an account\nname. Colleagues who found Jan''s name unpronounceable often referred to\nhim verbally as "S12n". The use of such numeronyms became part of DEC\ncorporate culture.\n', 'A n7m is a n4r-b3d w2d.\n\nM2t c6y, a n7m is a w2d w3e a n4r is u2d to f2m an\na10n  (a4t n1t an a5m or an i8m). P9g t1e\nl5s a1d n5s m1y s3d s5r to t1e f2l w2d: "K9" f1r "c4e"\n(p10y: "k1y" + "n2e"). A s5r e5e in F4h is "K7" f1r\n"c6e" (p10y: "ka" + "s2t").\n\nA11y, t1e l5s b5n t1e f3t a1d l2t a1e r6d by a\nn4r r10g t1e n4r of l5s o5d, s2h as "i18n" f1r\n"i18n". S7s t1e l2t l4r w2l a2o be c5d\na1d o5d.\n\nA7g to T1x T3n, t1e f3t n7m of t2s k2d w1s "S12n", t1e\ne8c m2l a5t n2e g3n to D1C e6e J1n S12n by\na s4m a11r b5e h1s s5e w1s t1o l2g to be an a5t\nn2e. C8s w1o f3d J1n''s n2e u13e o3n r6d to\nh1m v6y as "S12n". T1e u1e of s2h n8s b4e p2t of D1C\nc7e c5e.\n', '', 'HJX', 1322152044, 1000, 65535, '5d13fd173bfdc090cec893ceb11c7413', '5d13fd173bfdc090cec893ceb11c7413'),
(1016, '缙云烧饼', '<p>在缙云，有一种烧饼，叫缙云烧饼。一个饼炉里可以放很多烧饼。现在给你很多缙云烧饼组成的字符串图，请你计算出图中有几个缙云烧饼。&nbsp;这里我们规定，对于烧饼，仅由*组成，且形状和大小仅为&nbsp;<br /></p><pre style="font-family: &nbsp;&nbsp;&nbsp;consolas, &nbsp;&nbsp;&nbsp;宋体; overflow-x: auto; ">&nbsp;&nbsp;****\n&nbsp;******\n&nbsp;******\n&nbsp;******\n&nbsp;******\n&nbsp;&nbsp;****</pre><p></p><p>只有由上述字符串组成的图案才是缙云烧饼，且认为烧饼们不会重叠，若重叠了就不是缙云烧饼。&nbsp;如下图就不是缙云烧饼<br /></p><pre style="font-family: &nbsp;&nbsp;&nbsp;consolas, &nbsp;&nbsp;&nbsp;宋体; overflow-x: auto; ">&nbsp;&nbsp;****\n&nbsp;******\n&nbsp;*******\n&nbsp;******\n&nbsp;******\n&nbsp;&nbsp;****</pre><p></p><p><br /></p>', '此题有多个测试点。 每个测试点第一行输入n、m，代表行数列数。(1 <= n, m <= 1000) 接下去是一个n行m列的字符串矩形，包含任何可见的ASCII字符(不包含空格)。', '对于每个测试点，输出烧饼的个数。 每个测试点一行。', '8 14\ndfiwoejkfjldjs\ndsfj****lskdfk\n---******dsjlk\n234******3442k\nifo******jkjfk\neou******eow**\njfld****324jkj\njkfjk93fj***fk', '1', '', 'XadillaX', 1322152488, 1000, 65535, 'da92446ec377880389c0d75a24c81be5', 'da92446ec377880389c0d75a24c81be5'),
(1017, 'A+B Problem', '<p>Calculate&nbsp;a&nbsp;+&nbsp;b</p><p></p><p><br /></p><div><span class="Apple-style-span" style="font-family: Simsun; white-space: normal; background-color: rgb(227, 229, 220); font-size: medium; "><br /></span></div>', 'The input will consist of a series of pairs of integers a and b,separated by a space, one pair of integers per line, 0 0 means the end of the input, and do not need to output.\n', 'For each pair of input integers a and b you should output the sum of a and b in one line,and with one line of output for each line in input.\n', '1 5\n0 0\n', '6\n', '', '', 1322182151, 1000, 1024, '470241eb4a93cca1cc9ac297428f4d5b', '470241eb4a93cca1cc9ac297428f4d5b');

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
(14, 'OUT_OF_CONTEST_TIME'),
(15, 'DANGEROUS_CODE');

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
-- 表的结构 `oj_runtimeerror`
--

CREATE TABLE IF NOT EXISTS `oj_runtimeerror` (
  `totsubmitid` int(11) NOT NULL,
  `message` text CHARACTER SET utf8,
  PRIMARY KEY (`totsubmitid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `oj_runtimeerror`
--

INSERT INTO `oj_runtimeerror` (`totsubmitid`, `message`) VALUES
(4, 'tmpdir/1322186935.cpp: In function `int main()'':\ntmpdir/1322186935.cpp:5: error: `remove'' undeclared (first use this function)\ntmpdir/1322186935.cpp:5: error: (Each undeclared identifier is reported only \n   once for each function it appears in.)\ntmpdir/1322186935.cpp:7:2: warning: no newline at end of file\nÿ'),
(24, 'ACCESS_VIOLATION'),
(36, 'ACCESS_VIOLATION'),
(37, 'tmpdir/1322203648.c:1:17: cstdio: No such file or directory\ntmpdir/1322203648.c: In function `main'':\ntmpdir/1322203648.c:7: warning: implicit declaration of function `scanf''\ntmpdir/1322203648.c:7: error: `EOF'' undeclared (first use in this function)\ntmpdir/1322203648.c:7: error: (Each undeclared identifier is reported only once\ntmpdir/1322203648.c:7: error: for each function it appears in.)\ntmpdir/1322203648.c:18: warning: implicit declaration of function `printf''\ntmpdir/1322203648.c:22:2: warning: no newline at end of file\nÿ'),
(69, 'compilers/C/bin/../lib/gcc-lib/mingw32/3.3.1/../../..\\libmingw32.a(main.o)(.text+0x97):main.c: undefined reference to `WinMain@16''\nÿ'),
(70, 'tmpdir/1322704292.c:1:18: stido.h: No such file or directory\ntmpdir/1322704292.c:5:2: warning: no newline at end of file\nÿ'),
(72, 'tmpdir/1322704978.c: In function `main'':\ntmpdir/1322704978.c:6: warning: too few arguments for format\ntmpdir/1322704978.c:6: warning: left-hand operand of comma expression has no effect\ntmpdir/1322704978.c:6: error: parse error before ''!='' token\ntmpdir/1322704978.c:10:2: warning: no newline at end of file\nÿ'),
(73, 'tmpdir/1322705141.c:1:19: iostream: No such file or directory\ntmpdir/1322705141.c:2: error: parse error before "namespaces"\ntmpdir/1322705141.c:2: warning: type defaults to `int'' in declaration of `std''\ntmpdir/1322705141.c:2: warning: data definition has no type or storage class\ntmpdir/1322705141.c: In function `main'':\ntmpdir/1322705141.c:4: error: `cin'' undeclared (first use in this function)\ntmpdir/1322705141.c:4: error: (Each undeclared identifier is reported only once\ntmpdir/1322705141.c:4: error: for each function it appears in.)\ntmpdir/1322705141.c:4: error: `a'' undeclared (first use in this function)\ntmpdir/1322705141.c:4: error: `b'' undeclared (first use in this function)\ntmpdir/1322705141.c:5: error: `cout'' undeclared (first use in this function)\ntmpdir/1322705141.c:5: error: `endl'' undeclared (first use in this function)\ntmpdir/1322705141.c:6:2: warning: no newline at end of file\nÿ'),
(75, 'tmpdir/1322705206.cpp:2: error: syntax error before `std''\ntmpdir/1322705206.cpp: In function `int main()'':\ntmpdir/1322705206.cpp:4: error: `cin'' undeclared (first use this function)\ntmpdir/1322705206.cpp:4: error: (Each undeclared identifier is reported only \n   once for each function it appears in.)\ntmpdir/1322705206.cpp:4: error: `a'' undeclared (first use this function)\ntmpdir/1322705206.cpp:4: error: `b'' undeclared (first use this function)\ntmpdir/1322705206.cpp:5: error: `cout'' undeclared (first use this function)\ntmpdir/1322705206.cpp:5: error: `endl'' undeclared (first use this function)\ntmpdir/1322705206.cpp:6:2: warning: no newline at end of file\nÿ'),
(81, 'tmpdir/1322708373.cpp: In function `int main()'':\ntmpdir/1322708373.cpp:2: error: `system'' undeclared (first use this function)\ntmpdir/1322708373.cpp:2: error: (Each undeclared identifier is reported only \n   once for each function it appears in.)\ntmpdir/1322708373.cpp:3:2: warning: no newline at end of file\nÿ'),
(91, 'tmpdir/1322824946.c:1: error: parse error before ''<'' token\ntmpdir/1322824946.c:7: error: parse error before string constant\ntmpdir/1322824946.c:7: warning: type defaults to `int'' in declaration of `scanf''\ntmpdir/1322824946.c:7: warning: conflicting types for built-in function `scanf''\ntmpdir/1322824946.c:7: warning: data definition has no type or storage class\ntmpdir/1322824946.c:15:2: warning: no newline at end of file\nÿ');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=103 ;

--
-- 转存表中的数据 `oj_submit`
--

INSERT INTO `oj_submit` (`totsubmitid`, `submitid`, `contestid`, `index`, `userid`, `time`, `memory`, `length`, `submittime`, `languageid`, `resultid`) VALUES
(1, 0, 1, '1000', 1, 0, 224, 161, 1322183797, 1, 3),
(2, 1, 1, '1002', 1, 15, 336, 9413, 1322186605, 2, 3),
(3, 2, 1, '1016', 1, 1014, 1244, 1110, 1322186813, 2, 8),
(4, 3, 1, '1016', 1, 0, 0, 80, 1322186934, 2, 11),
(5, 4, 1, '1016', 1, 171, 300, 2153, 1322187088, 2, 3),
(6, 5, 1, '1002', 1, 0, 391608, 156, 1322201798, 2, 9),
(7, 6, 1, '1002', 1, 0, 224, 50, 1322201860, 2, 5),
(8, 7, 1, '1002', 1, 0, 1408, 81, 1322201902, 2, 8),
(9, 8, 1, '1002', 1, 0, 248, 94, 1322201962, 2, 5),
(10, 9, 1, '1002', 1, 0, 248, 82, 1322202003, 2, 5),
(11, 10, 1, '1002', 1, 0, 248, 100, 1322202052, 2, 5),
(12, 11, 1, '1002', 1, 15, 580, 91, 1322202414, 2, 5),
(13, 12, 1, '1002', 1, 0, 584, 122, 1322202460, 2, 5),
(14, 13, 1, '1002', 1, 0, 576, 90, 1322202494, 2, 8),
(15, 14, 1, '1002', 1, 0, 576, 90, 1322202531, 2, 8),
(16, 15, 1, '1002', 1, 0, 248, 90, 1322202576, 2, 5),
(17, 16, 1, '1002', 1, 0, 584, 101, 1322202633, 2, 5),
(18, 17, 1, '1001', 1, 0, 584, 676, 1322202797, 2, 3),
(19, 18, 1, '1003', 1, 93, 720, 735, 1322202831, 2, 3),
(20, 19, 1, '1004', 1, 109, 3668, 1421, 1322202851, 2, 3),
(21, 20, 1, '1005', 1, 0, 596, 367, 1322202876, 2, 3),
(22, 21, 1, '1006', 1, 0, 584, 3564, 1322202898, 2, 3),
(23, 22, 1, '1007', 1, 15, 556, 749, 1322202927, 2, 3),
(24, 23, 1, '1008', 1, 0, 580, 970, 1322203010, 2, 6),
(25, 24, 1, '1008', 1, 140, 4552, 798, 1322203032, 2, 5),
(26, 25, 1, '1008', 1, 280, 4496, 847, 1322203058, 2, 5),
(27, 26, 1, '1008', 1, 93, 4548, 859, 1322203088, 2, 3),
(28, 27, 1, '1009', 1, 15, 6484, 2350, 1322203156, 2, 3),
(29, 28, 1, '1009', 1, 31, 596, 2735, 1322203178, 2, 3),
(30, 29, 1, '1010', 1, 15, 704, 595, 1322203227, 2, 3),
(31, 30, 1, '1010', 1, 15, 648, 799, 1322203258, 2, 3),
(32, 31, 1, '1011', 1, 31, 8380, 276, 1322203318, 2, 3),
(33, 32, 1, '1012', 1, 0, 556, 216, 1322203405, 2, 3),
(34, 33, 1, '1013', 1, 1092, 8220, 362, 1322203529, 1, 8),
(35, 34, 1, '1013', 1, 0, 552, 151, 1322203558, 2, 3),
(36, 35, 1, '1014', 1, 31, 8380, 367, 1322203603, 2, 6),
(37, 36, 1, '1014', 1, 0, 0, 212, 1322203648, 1, 11),
(38, 37, 1, '1014', 1, 0, 556, 212, 1322203664, 2, 3),
(39, 38, 1, '1015', 1, 0, 552, 737, 1322203700, 2, 5),
(40, 39, 1, '1017', 1, 15, 624, 2229, 1322203742, 1, 3),
(41, 40, 1, '1003', 2, 31, 600, 610, 1322646337, 2, 5),
(42, 41, 1, '1010', 2, 218, 704, 443, 1322646680, 2, 3),
(43, 42, 1, '1010', 2, 31, 708, 592, 1322646823, 2, 3),
(44, 43, 1, '1010', 2, 15, 1176, 950, 1322646875, 2, 3),
(45, 44, 1, '1010', 2, 31, 712, 595, 1322646921, 2, 3),
(46, 45, 1, '1010', 1, 15, 704, 433, 1322647038, 2, 5),
(47, 46, 1, '1006', 1, 0, 588, 1547, 1322647121, 2, 3),
(48, 47, 1, '1006', 1, 0, 568, 3199, 1322647278, 2, 5),
(49, 48, 1, '1011', 2, 15, 564, 224, 1322647499, 2, 3),
(50, 49, 1, '1011', 2, 0, 564, 468, 1322647518, 2, 3),
(51, 50, 1, '1011', 2, 0, 560, 334, 1322647540, 2, 3),
(52, 51, 1, '1011', 2, 0, 564, 400, 1322647560, 2, 3),
(53, 52, 1, '1011', 2, 0, 560, 253, 1322647578, 2, 3),
(54, 53, 1, '1011', 2, 31, 564, 239, 1322647603, 2, 3),
(55, 54, 1, '1011', 2, 0, 564, 265, 1322647623, 2, 3),
(56, 55, 1, '1011', 2, 0, 564, 250, 1322647641, 1, 3),
(57, 56, 1, '1011', 2, 15, 568, 188, 1322647662, 2, 3),
(58, 57, 1, '1011', 2, 31, 560, 395, 1322647680, 2, 3),
(59, 58, 1, '1011', 2, 0, 584, 329, 1322647702, 2, 3),
(60, 59, 1, '1011', 2, 15, 8392, 276, 1322647721, 2, 3),
(61, 60, 1, '1011', 2, 0, 564, 252, 1322647737, 2, 3),
(62, 61, 1, '1011', 2, 0, 568, 265, 1322647759, 1, 3),
(63, 62, 1, '1011', 2, 31, 8392, 275, 1322647789, 2, 3),
(64, 63, 1, '1011', 2, 0, 564, 298, 1322647808, 2, 3),
(65, 64, 1, '1011', 2, 0, 564, 468, 1322647825, 2, 3),
(66, 65, 1, '1011', 2, 15, 588, 851, 1322647852, 2, 3),
(67, 66, 1, '1011', 2, 31, 8392, 276, 1322647869, 2, 3),
(68, 67, 1, '1011', 2, 0, 564, 825, 1322648118, 1, 3),
(69, 68, 1, '1000', 7, 0, 0, 0, 1322704224, 1, 11),
(70, 69, 1, '1000', 7, 0, 0, 42, 1322704291, 1, 11),
(71, 70, 1, '1000', 7, 0, 544, 42, 1322704393, 1, 5),
(72, 71, 1, '1000', 8, 0, 0, 118, 1322704977, 1, 11),
(73, 72, 1, '1000', 9, 0, 0, 92, 1322705141, 1, 11),
(74, 73, 1, '1000', 8, 0, 552, 137, 1322705189, 1, 5),
(75, 74, 1, '1000', 9, 0, 0, 92, 1322705205, 2, 11),
(76, 75, 1, '1000', 10, 0, 576, 130, 1322706998, 2, 3),
(77, 76, 1, '1000', 1, 15, 568, 96, 1322707499, 2, 10),
(78, 77, 1, '1000', 10, 577, 408, 55, 1322707500, 2, 8),
(79, 78, 1, '1000', 10, 0, 0, 94, 1322707754, 2, 15),
(80, 79, 1, '1000', 10, 733, 412, 83, 1322708265, 2, 8),
(81, 80, 1, '1000', 9, 0, 0, 33, 1322708307, 2, 11),
(82, 81, 1, '1000', 9, 0, 572, 72, 1322708437, 2, 5),
(83, 82, 1, '1000', 1, 0, 576, 130, 1322708889, 2, 10),
(84, 83, 1, '1000', 1, 0, 576, 130, 1322708916, 2, 3),
(85, 84, 1, '1000', 10, 920, 412, 83, 1322708941, 2, 8),
(86, 85, 1, '1000', 1, 46, 588, 94, 1322709182, 2, 8),
(87, 86, 1, '1000', 1, 15, 592, 94, 1322715007, 2, 8),
(88, 87, 1, '1000', 11, 0, 552, 136, 1322715500, 1, 3),
(89, 88, 1, '1000', 1, 0, 0, 94, 1322719648, 2, 15),
(90, 89, 1, '1000', 13, 0, 548, 117, 1322824786, 2, 3),
(91, 90, 1, '1000', 14, 0, 0, 135, 1322824946, 1, 11),
(92, 91, 1, '1000', 14, 15, 548, 161, 1322825644, 1, 3),
(93, 92, 1, '1000', 14, 0, 544, 161, 1322825667, 1, 3),
(94, 93, 1, '1000', 14, 0, 544, 161, 1322825669, 1, 3),
(95, 94, 1, '1000', 15, 0, 576, 127, 1322909224, 2, 5),
(96, 95, 1, '1000', 15, 0, 576, 162, 1322909353, 2, 5),
(97, 96, 1, '1000', 15, 15, 576, 193, 1322909435, 2, 3),
(98, 97, 1, '1000', 15, 0, 576, 224, 1322910437, 2, 3),
(99, 98, 1, '1000', 15, 0, 580, 224, 1322910439, 2, 3),
(100, 99, 1, '1000', 1, 15, 572, 122, 1322912343, 2, 3),
(101, 100, 1, '1000', 1, 0, 580, 121, 1322912391, 2, 3),
(102, 101, 1, '1000', 16, 0, 572, 118, 1322924224, 2, 5);

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
  `submitlist` text NOT NULL,
  `solvedlist` text NOT NULL,
  `submitnum` int(11) NOT NULL DEFAULT '0',
  `solvednum` int(11) NOT NULL DEFAULT '0',
  `avatarbar` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- 转存表中的数据 `oj_user`
--

INSERT INTO `oj_user` (`userid`, `username`, `password`, `roleid`, `nickname`, `regtime`, `solved`, `submit`, `school`, `email`, `motto`, `language`, `submitlist`, `solvedlist`, `submitnum`, `solvednum`, `avatarbar`) VALUES
(1, 'XadillaX', '045f382f08038084d9ef8d74a8402363', 3, '死月', 0, 24, 52, 'Ningbo University of Technology', 'admin@xcoder.in', '', 2, '1000|1002|1016|1001|1003|1004|1005|1006|1007|1008|1009|1010|1011|1012|1013|1014|1015|1017|', '1000|1002|1016|1001|1003|1004|1005|1006|1007|1008|1009|1010|1011|1012|1013|1014|1017|', 21, 17, 1),
(2, 'test', 'e10adc3949ba59abbe56e057f20f883e', 1, 'a', 0, 24, 25, '', 'a@b.c', '', 1, '1003|1010|1011|', '1010|1011|', 3, 2, 1),
(3, 'again', 'e10adc3949ba59abbe56e057f20f883e', 1, 'again', 0, 0, 0, 'SBUT', 'again@again.again', 'SB AGAIN.', 0, '', '', 0, 0, 1),
(4, 'deathmoon', '045f382f08038084d9ef8d74a8402363', 1, 'canyouhelpme', 0, 0, 0, '', 'zukaidi@163.com', '', 0, '', '', 0, 0, 1),
(5, 'mamama', '2a7d94e6d20ed9be4edca6f5ebe5e0ab', 1, 'adslfkj', 0, 0, 0, '', 'j@k.c', '', 0, '', '', 0, 0, 1),
(6, 'konakona', '171f9f26441decbb9a1dac3e5b60f783', 3, 'konakona', 0, 0, 0, '', 'admin@crazyphper.com', '', 0, '', '', 0, 0, 1),
(7, '1234', 'e10adc3949ba59abbe56e057f20f883e', 1, '1', 0, 0, 3, '1', '12122@qq.com', '1', 1, '1000|', '', 1, 0, 1),
(8, 'timebug', 'e10adc3949ba59abbe56e057f20f883e', 3, 'timebug', 0, 0, 2, 'nbut', 'timebug@live.com', '', 1, '1000|', '', 1, 0, 1),
(9, 'jdx2013519', '3be8f013abc5668259e74d5b0fb4283c', 1, '囧の轨迹', 0, 0, 4, '', 'jdx2013519@sina.com', '', 2, '1000|', '', 1, 0, 1),
(10, 'soce', 'e10adc3949ba59abbe56e057f20f883e', 1, 'soce', 0, 1, 5, '', 'lzsb@2b.nc', '', 2, '1000|', '1000|', 1, 1, 1),
(11, 'monkeyde17', 'e10adc3949ba59abbe56e057f20f883e', 1, 'monkeyde17', 0, 1, 1, '', 'monkey_tv@126.com', '呵，呵呵，呵呵呵', 1, '1000|', '1000|', 0, 1, 1),
(12, '8644325', '045f382f08038084d9ef8d74a8402363', 1, '丿死丶月', 1322718340, 0, 0, '', 'xadillax@acmdiy.net', '', 0, '', '', 0, 0, 1),
(13, 'katie', '0df44e5def0cd6a339e7cf200ad124ef', 1, 'katie', 1322823821, 1, 1, '', 'katieangel@sohu.com', '', 2, '1000|', '1000|', 1, 1, 1),
(14, 'cxn_bupt', '3e2444ff5be51c2bc8eb5db084210c0c', 1, 'april', 1322824001, 3, 4, '', 'xuancao206@qq.com', '', 1, '1000|', '1000|', 4, 1, 1),
(15, 'starvae', '727d5a71d71364d1d93770b52df34c89', 1, 'starvae', 1322909082, 3, 5, 'hdu', 'starvae@gmail.com', '', 2, '1000|', '1000|', 5, 1, 0),
(16, 'MatRush', '637b417abdb5e2c159e641fcee09f454', 3, 'MatRush', 1322924174, 0, 1, 'ZJUT', 'matrush@qq.com', 'MatRush = Man @ Rush...', 2, '1000|', '', 1, 0, 1);
