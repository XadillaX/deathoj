var ukagaka_shown = true;
var ukagaka_bodys = [
    //"touhou-smile.png",
    //"touhou-fear.png",
    //"touhou-laugh.png",
    //"touhou-tear.png",
    //"touhou-han.png",
    //"touhou-chijing.png",
    //"touhou-heixian.png"
    
    //"touhou-han-w.png",
    //"touhou-laugh-w.png",
    //"touhou-smile-w.png",
    //"touhou-tear-w.png"
	
	"online.png",
	"online-angry.png",
	"online-black.png",
	"online-han.png",
	"online-smile.png"
];
var ukagaka_inited = false;

var ukagaka_words = [
    "那个，大家好，我是『艾丽叶·夜姬·安碧尤奇』，是NOJ的春菜喔~",
	"咱有版权的，喵~ 代表NBUT Online Judge System，作者是日本画师 須原くるり。",
    "咱OJ的地址是 <a href='http://acm.nbut.cn/' target='_blank'>http://acm.nbut.cn/</a> 喔",
    "你可以关注一下NOJ酱的微博呢，<a href='http://weibo.com/nbutoj/' target='_blank'>http://weibo.com/nbutoj/</a>",
    "做题要细心~ 题要细心~ 要细心~ 细心~ 心~",
    "A题才是王道的说~ A题什么的最喜欢啦~ 谁A得最多咱就喜欢谁呢~",
    // "啦啦啦~ 咱是二小姐~",
    // "赤い、赤い、赤い、赤い、甘い、甘い、甘い、甘い",
    "快做题吧~ 不做题什么的最讨厌了~",
    "唔，还有好多题没做呢。",
    "笨蛋，TLE什么的，本小姐怎么会TLE呢？一定是你太笨了！",
    "西奈~ 我才不会不做题目呢~",
    "你的光辉时刻是什么呢ACMer？World Final吗？而我的光辉时刻就是<span style='color: red'>ACCEPTED</span>的时刻~",
    "是的，只要5小时，就能让你爽到不能呼吸哟~ <(*ΦωΦ*)>",
    "笨蛋，让我AC行么？",
    "那个，你有看到过我的 AC 么？(」ﾟヘﾟ)」",
    "你也要来 A 么？很好 A 的哟 ~ <br />（〜^∇^)〜",
    "还用 VC6 的笨蛋们，赶紧转战 VS2008 或者 DEV-CPP 吧！<br />┗(｀ー´)┓",
    "那个，其实我挺喜欢你的。只要你再 A 一题也许我会答应你喔~ <br /> ＼(^ω^＼)",
    "想我说跟多话或者拥有跟多功能的话，感觉跟我主人酱说吧~"
];

var ukagaka_range = ukagaka_bodys.length;
var ukagaka_words_range = ukagaka_words.length;
var now_ukagaka_url = ukagaka_root + "/" + ukagaka_bodys[Math.floor(Math.random() * ukagaka_range)];
var now_ukagaka_word = ukagaka_words[Math.floor(Math.random() * ukagaka_words_range)];

function turn_ukagaka_img()
{
    now_ukagaka_url = ukagaka_root + "/" + ukagaka_bodys[Math.floor(Math.random() * ukagaka_range)];
    $("#ukagaka-img").attr("src", now_ukagaka_url);
}

function turn_ukagaka_word()
{
    now_ukagaka_word = ukagaka_words[Math.floor(Math.random() * ukagaka_words_range)];
    $("#ukagaka-msg").html(now_ukagaka_word);
}

$(function() {
	/** 初始化春菜状态 */
	$.get(ukagaka_ajax_root + "/Index/get_ukagaka.xhtml", {}, function(e){
		if("1" == e)
		{
			turn_ukagaka_img();
			turn_ukagaka_word();
			
			ukagaka_inited = true;
			ukagaka_shown = true;
			
			$("#ukagaka-wrapper").fadeToggle("normal");
		}
		else
		{
			$("#ukagaka-wrapper").css("display", "none");
			ukagaka_shown = false;
			ukagaka_inited = false;
			
			$("#ukagaka-shown-hidden").html("显示春菜");
		}
	});

    $("#ukagaka-shown-hidden").click(function(){
		if(!ukagaka_inited)
		{
			turn_ukagaka_img();
			turn_ukagaka_word();
			
			ukagaka_inited = true;
			
			$("#ukagaka-wrapper").fadeToggle("normal");
		}
		else $("#ukagaka-wrapper").fadeToggle("normal");
        
        $(this).html($(this).html() == "隐藏春菜" ? "显示春菜" : "隐藏春菜");
		ukagaka_shown = !ukagaka_shown;
		
		$.post(ukagaka_ajax_root + "/Index/turn_ukagaka.xhtml", { "status": ukagaka_shown ? "1" : "0" }, function(e){
			/** Nothing */
		});
    });
    
    $("#ukagaka-img").click(function(){
        turn_ukagaka_img();
        turn_ukagaka_word();
    });
});
