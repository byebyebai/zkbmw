$(function(){

            $(".btn-confirm").click(function(){
            var form = $("#register").serializeArray();
            console.log(form)
            var jsonData = {}
            $.each(form,function(i,o){
                jsonData[form[i]['name']] = form[i]['value'];
            })
            if(jsonData.name.length == 0){
            $("input[name='name']").css('border','1px solid red');
                return false;
            }else{
                $("input[name='name']").css('border','1px solid #e0e0e0');
            }
            if(!/^1[34578]\d{9}$/.test(jsonData.tel)){
                $("input[name='tel']").css('border','1px solid red');
                return false;
            }else{
                $("input[name='tel']").css('border','1px solid #e0e0e0');
            }

            $.getJSON("{:getDoman()}/doRegister",{jsonData},function(result){
    
                if(result.code ==1){
                    alert(result.message);
                    document.getElementById("register").reset();
                }
            });
        })
        


});