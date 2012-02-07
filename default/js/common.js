/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-8 上午1:14
 */
$(function(){
    $("#goprob-id").focus(function(){
        if($(this).val() == "题目编号") $(this).val("");
    });
    $("#goprob-id").blur(function(){
        if($(this).val() == "") $(this).val("题目编号");
    });

    $("a, blockquote").hover(
        function(){
            if(!$(this).is(":animated"))
                $(this).animate({ opacity : ".6" }, 210).animate({ opacity : "1" }, 180);
        }
    );
});
