// JavaScript Document
$(function () {

    

    //推荐
    $('.recTab a').click(function () {
        $(this).addClass('active').siblings('a').removeClass('active');
        var index = $(this).index();
        $(this).closest('.recTab').siblings('.recCon').eq(index).show().siblings('.recCon').hide();
    })
    //招生院校
    $('.item3Tab a').click(function () {
        $(this).addClass('active').siblings('a').removeClass('active');
        var index = $(this).index();
        var $item3Con = $(this).closest('.item3Tab').parent().siblings('.item3Con').eq(index);
        $item3Con.show().siblings('.item3Con').hide();
        $item3Con.find('li a img').each(function (i, item) {
            if (!$(item).attr('src')) {
                $(item).attr('src', $(item).data('src'));
            }
        })
    })
    //招生专业
    var li = $('.profession li');
    for (var i = 0; i < li.length; i++) {
        if (li.eq(i).hasClass('active')) {
            li.closest('.profession').siblings('.zyCon').eq(i).show();
        }
    }
    li.click(function () {
        $(this).addClass('active').siblings('li').removeClass('active');
        var index = $(this).index();
        var $zyCon = $(this).closest('.profession').siblings('.zyCon').eq(index);
        $zyCon.show().siblings('.zyCon').hide();
        $zyCon.find('li a img').each(function (i, item) {
            if (!$(item).attr('src')) {
                $(item).attr('src', $(item).data('src'));
            }
        })
    })
    //习题练习
    var a = $('.item5Tab a');
    for (var j = 0; j < a.length; j++) {
        if (a.eq(j).hasClass('active')) {
            a.closest('.item5Tab').siblings('.item5more').find('a').eq(j).show();
            a.closest('.item5Tab').siblings('.item5Con').eq(j).show();
        }
    }
    a.hover(function () {
        $(this).addClass('active').siblings('a').removeClass('active');
        var index = $(this).index();
        $(this).closest('.item5Tab').siblings('.item5more').find('a').eq(index).show().siblings('a').hide();
        $(this).closest('.item5Tab').siblings('.item5Con').eq(index).show().siblings('.item5Con').hide();
    })
    //远程教育-招生专业
    for (var e = 0; e < $('.zsyx li').length; e++) {
        if (e > 2) {
            $('.zsyx li').eq(e).css({ 'border-bottom': 'none' })
        }
        if ((e + 1) % 3 == 0) {
            $('.zsyx li').eq(e).css({ 'border-right': 'none' })
        }
    }

    $('.zymore1').click(function () {
        $('.item4Con .active').next().click();
        $('.profession>ul').scrollTop($('.profession>ul').scrollTop() + 60)
    })
    $('.zymore').click(function () {
        $('.item4Con .active').prev().click();
        $('.profession>ul').scrollTop($('.profession>ul').scrollTop() - 60)
    })
    var itlength = $('.stf>a').length * 80;
    var perlengh = itlength - 14 * 80;
    $('.stf').css('width', itlength + 'px')
    $('.xy').click(function () {
        var mar = parseInt($('.stf').css('margin-left'));
        if (mar > -perlengh) {
            $(".stf").css('margin-left', (-1120 + mar) + 'px')
        }

        if (-1120 + mar < -perlengh) {
            $(this).hide();
        }
    })

    $('.xz').click(function () {
        var mar = parseInt($('.stf').css('margin-left'));
        console.log("mar:" + mar);
        if (mar < 0) {
            $(".stf").css('margin-left', (1120 + mar) + 'px')
        }
        if (1120 + mar < 0) {
            $('.xy').show();
        }
    })
})

function addFavorite2() {
        var url = window.location;
        var title = document.title;
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf("360se") > -1) {
            alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
        }
        else if (ua.indexOf("msie 8") > -1) {
            window.external.AddToFavoritesBar(url, title); //IE8
        }
        else if (document.all) {
            try{
                window.external.addFavorite(url, title);
            }catch(e){
                alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
            }
        }
        else if (window.sidebar) {
            window.sidebar.addPanel(title, url, "");
        }
        else {
            alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
        }
    }

