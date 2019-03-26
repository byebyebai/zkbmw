


function setTab(name,cursel,n)
{
    for(i=1;i<=n;i++){
        var menu=document.getElementById(name+i);
        var con=document.getElementById(name+"_"+i);
        menu.className=i==cursel?"curr":"";
        con.style.display=i==cursel?"block":"none";
    }
}

$(".zsyx ul li").click(function(){
	$(this).addClass("curr").siblings().removeClass("curr");
	var i = $(this).index();
	$(this).parents(".zsyx").find(".dd div:eq("+i+")").show().siblings().hide();
});
 $(function () {
        $("#enroll_readType").val("0");

        var areaID = parseInt("0");
        if (areaID > 0) {
            $("#enroll_readArea").val(areaID);
        }
    })

    //提交报名
    function save_enroll111() {
        var name = $("#enroll_name").val();
        if (!name) {
            alert("请输入姓名！")
            $("#enroll_name").focus();
            return;
        }

        var phone = $("#enroll_phone").val();
        if (!phone || !(/^1\d{10}$/.test(phone))) {
            alert("请输入正确的手机号！")
            $("#enroll_phone").focus();
            return;
        }

        var verifycode = $("#enroll_verifycode").val();
        if (!verifycode) {
            alert("请输入验证码！")
            $("#enroll_verifycode").focus();
            return;
        }

        $.ajax({
            type: "post",
            url: "/ajax/enroll.ashx",
            dataType: "text",
            data: {
                name: name,
                phone: phone,
                readTypeID: $("#enroll_readType").val(),
                readAreaID: $("#enroll_readArea").val(),
                verifycode: verifycode,
                fromurl: encodeURI(location.href)
            },
            success: function (result) {
                if (result) {
                    var data = JSON.parse(result);
                    if (data.CodeID == 1) {
                        $("#enroll_name").val("");
                        $("#enroll_phone").val("");
                        $("#enroll_verifycode").val("");
                        $("#enroll_readType").val("0");
                        $("#enroll_readArea").val("0");
                    }
                    alert(data.msg);
                    $("#enroll_imgVerify").click();
                }
                else {
                    alert("报名失败！");
                }
            },
            error: function () { },
            cache: false
        });
    }
	$(function () {
        $('.search .p_search').mouseover(function () {
            $('.search .p_search>div').addClass('c_search');
            $('.search .p_search>div').show();
        })
        $('.search .p_search').mouseout(function () {
            $('.search .p_search>div').removeClass('c_search');
            $('.search .p_search>div').hide();
        })
        $('.search .p_search>div>p').click(function () {
            $('.search .p_search>div').removeClass('c_search');
            $('.search .p_search>div').hide();
        })
        $('.head>p>span').click(function () {
            $('.popDiv').toggle();
        })
        $('.popDiv>span').click(function () {
            $('.popDiv').toggle();
        })
        $('.search .p_search>div>p').click(function () {
            $('.search .p_search>p').text($(this).text());
            $('#hidSearchID').val($(this).index() + 1);
        })
    })

    //搜索
    function find() {
        // var readTypePath = $('#hidReadTypePath').val();
        // if (readTypePath) {
        //     readTypePath = '/' + readTypePath;
        // }
        var hidSearchID = parseInt($('#hidSearchID').val());
        var search_content = $('#search_content').val();
        if (search_content) {
            var newWindow = window.open();
            switch (hidSearchID) {
                case 1: //院校
                    newWindow.location.href = "/school/index.html?keywords=" + search_content;
                    break;
                case 2: //专业
                    newWindow.location.href = "/subject/index.html?keywords=" + search_content;
                    break;
                case 3:  //信息
                    newWindow.location.href = "/index/search.html?keywords=" + search_content;
                    break;
            }
        }
    }

$.post("/index/Api/getTags",{},function(res){
    var html = '';
    data = res.data;
    for(i=0;i<data.length;i++){
        
        html += '<a target="_blank" href="/tags/'+data[i].id+'.html" class="tagc'+data[i].color+'">'+data[i].tag_name+'</a>';
    } 
    $("#tagscloud").append(html);
    tagsaction();
},'JSON');

//tag标签设置
var radius = 90;
var d = 200;
var dtr = Math.PI / 180;
var mcList = [];
var lasta = 1;
var lastb = 1;
var distr = true;
var tspeed = 11;
var size = 200;
var mouseX = 0;
var mouseY = 10;
var howElliptical = 1;
var aA = null;
var oDiv = null;
function tagsaction()
{
	var i=0;
	var oTag=null;
	oDiv=document.getElementById('tagscloud');
	aA=oDiv.getElementsByTagName('a');
	for(i=0;i<aA.length;i++)
	{
		oTag={};		
		aA[i].onmouseover = (function (obj) {
			return function () {
				obj.on = true;
				this.style.zIndex = 9999;
				this.style.color = '#fff';
				this.style.padding = '5px 5px';
				this.style.filter = "alpha(opacity=100)";
				this.style.opacity = 1;
			}
		})(oTag)
		aA[i].onmouseout = (function (obj) {
			return function () {
				obj.on = false;
				this.style.zIndex = obj.zIndex;
				this.style.color = '#fff';
				this.style.padding = '5px';
				this.style.filter = "alpha(opacity=" + 100 * obj.alpha + ")";
				this.style.opacity = obj.alpha;
				this.style.zIndex = obj.zIndex;
			}
		})(oTag)
		oTag.offsetWidth = aA[i].offsetWidth;
		oTag.offsetHeight = aA[i].offsetHeight;
		mcList.push(oTag);
	}
	sineCosine( 0,0,0 );
	positionAll();
	(function () {
            update();
            setTimeout(arguments.callee, 40);
        })();
};
function update()
{
	var a, b, c = 0;
        a = (Math.min(Math.max(-mouseY, -size), size) / radius) * tspeed;
        b = (-Math.min(Math.max(-mouseX, -size), size) / radius) * tspeed;
        lasta = a;
        lastb = b;
        if (Math.abs(a) <= 0.01 && Math.abs(b) <= 0.01) {
            return;
        }
        sineCosine(a, b, c);
        for (var i = 0; i < mcList.length; i++) {
            if (mcList[i].on) {
                continue;
            }
            var rx1 = mcList[i].cx;
            var ry1 = mcList[i].cy * ca + mcList[i].cz * (-sa);
            var rz1 = mcList[i].cy * sa + mcList[i].cz * ca;

            var rx2 = rx1 * cb + rz1 * sb;
            var ry2 = ry1;
            var rz2 = rx1 * (-sb) + rz1 * cb;

            var rx3 = rx2 * cc + ry2 * (-sc);
            var ry3 = rx2 * sc + ry2 * cc;
            var rz3 = rz2;

            mcList[i].cx = rx3;
            mcList[i].cy = ry3;
            mcList[i].cz = rz3;

            per = d / (d + rz3);

            mcList[i].x = (howElliptical * rx3 * per) - (howElliptical * 2);
            mcList[i].y = ry3 * per;
            mcList[i].scale = per;
            var alpha = per;
            alpha = (alpha - 0.6) * (10 / 6);
            mcList[i].alpha = alpha * alpha * alpha - 0.2;
            mcList[i].zIndex = Math.ceil(100 - Math.floor(mcList[i].cz));
        }
        doPosition();
}
function positionAll()
{
	var phi = 0;
    var theta = 0;
    var max = mcList.length;
    for (var i = 0; i < max; i++) {
        if (distr) {
            phi = Math.acos(-1 + (2 * (i + 1) - 1) / max);
            theta = Math.sqrt(max * Math.PI) * phi;
        } else {
            phi = Math.random() * (Math.PI);
            theta = Math.random() * (2 * Math.PI);
        }
        //坐标变换
        mcList[i].cx = radius * Math.cos(theta) * Math.sin(phi);
        mcList[i].cy = radius * Math.sin(theta) * Math.sin(phi);
        mcList[i].cz = radius * Math.cos(phi);

        aA[i].style.left = mcList[i].cx + oDiv.offsetWidth / 2 - mcList[i].offsetWidth / 2 + 'px';
        aA[i].style.top = mcList[i].cy + oDiv.offsetHeight / 2 - mcList[i].offsetHeight / 2 + 'px';
    }
}
function doPosition()
{
	var l = oDiv.offsetWidth / 2;
        var t = oDiv.offsetHeight / 2;
        for (var i = 0; i < mcList.length; i++) {
            if (mcList[i].on) {
                continue;
            }
            var aAs = aA[i].style;
            if (mcList[i].alpha > 0.1) {
                if (aAs.display != '')
                    aAs.display = '';
            } else {
                if (aAs.display != 'none')
                    aAs.display = 'none';
                continue;
            }
            aAs.left = mcList[i].cx + l - mcList[i].offsetWidth / 2 + 'px';
            aAs.top = mcList[i].cy + t - mcList[i].offsetHeight / 2 + 'px';
            //aAs.fontSize=Math.ceil(12*mcList[i].scale/2)+8+'px';
            //aAs.filter="progid:DXImageTransform.Microsoft.Alpha(opacity="+100*mcList[i].alpha+")";
            aAs.filter = "alpha(opacity=" + 100 * mcList[i].alpha + ")";
            aAs.zIndex = mcList[i].zIndex;
            aAs.opacity = mcList[i].alpha;
        }
}
function sineCosine( a, b, c)
{
	sa = Math.sin(a * dtr);
    ca = Math.cos(a * dtr);
    sb = Math.sin(b * dtr);
    cb = Math.cos(b * dtr);
	sc = Math.sin(c * dtr);
	cc = Math.cos(c * dtr);
}