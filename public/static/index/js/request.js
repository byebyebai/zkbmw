

	$.post("{$Request.domain}/index/Api/getTags",{},function(res){
            
        var html = '';
        data = res.data;
        for(i=0;i<data.length;i++){
            
            html += '<a target="_blank" href="/tags/'+data[i].id+'.html" class="tagc'+data[i].color+'">'+data[i].tag_name+'</a>';
        } 
        $("#tagscloud").append(html);
    },'JSON');
	$.post("{$Request.domain}/index/Api/getSubjects",{},function(res){
            
        var html = '';
        data = res.data;
        for(i=0;i<data.length;i++){
            
            html += '<dd><a href="/major/'+data[i].subjectId+'.html" target="_blank" class="hotTop" title="'+data[i].subjectName+'">'+data[i].subjectName+'</a></dd>';
        } 
        $(".major").append(html);
    },'JSON');
