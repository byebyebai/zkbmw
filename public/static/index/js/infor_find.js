//进入专业
function goProfession() {
    var professionID = parseInt($('#profession').val());
    if (professionID > 0) {
        var newWindow = window.open();
        newWindow.location.href = "/major/" + professionID + ".html";
    }
}

//进入学校
function goSchool() {
    var school = $('#school').val();
    if (school) {
        var newWindow = window.open();
        newWindow.location.href = "/" + school + "/";
    }
}

//切换专业类别
function changeProfessionType(readTypeID) {
    var professionTypeID = parseInt($('#professionType').val());
    var hidProfessionTypeID = parseInt($('#hidProfessionTypeID').val())
    if (professionTypeID != hidProfessionTypeID) {
        $.get('/ajax/getProfessions.ashx', {
            readTypeID: readTypeID,
            professionTypeID: professionTypeID,
            readLevelID: parseInt($('#readLevel').val())
        }, function (result) {
            if (result) {
                var data = JSON.parse(result);
                if (data && data.length) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].ProfessionID + '">' + data[i].Name + '</option>';
                    }
                    $('#profession').html(html);
                    $('#hidProfessionTypeID').val(professionTypeID);
                }
                else {
                    $('#profession').html('<option value="0">请选择</option>');
                }
            }
            else {
                $('#profession').html('<option value="0">请选择</option>');
            }
        })
    }
}

//切换报读层次
function changeReadLevel(readTypeID) {
    var readLevelID = parseInt($('#readLevel').val());
    var hidReadLevelID = parseInt($('#hidReadLevelID').val())
    if (readLevelID != hidReadLevelID) {
        $.get('/ajax/getProfessions.ashx', {
            readTypeID: readTypeID,
            professionTypeID: parseInt($('#professionType').val()),
            readLevelID: readLevelID
        }, function (result) {
            if (result) {
                var data = JSON.parse(result);
                if (data && data.length) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].ProfessionID + '">' + data[i].Name + '</option>';
                    }
                    $('#profession').html(html);
                    $('#hidReadLevelID').val(readLevelID);
                }
                else {
                    $('#profession').html('<option value="0">请选择</option>');
                }
            }
            else {
                $('#profession').html('<option value="0">请选择</option>');
            }
        })
    }
}


//切换院校层次
function changeSchoolLevel(readTypeID) {
    var schoolLevelID = parseInt($('#schoolLevel').val());
    var hidSchoolLevelID = parseInt($('#hidSchoolLevelID').val())
    if (schoolLevelID != hidSchoolLevelID) {
        $.get('/ajax/getSchools.ashx', {
            readTypeID: readTypeID,
            schoolLevelID: schoolLevelID,
            readAreaID: parseInt($('#readArea').val())
        }, function (result) {
            if (result) {
                var data = JSON.parse(result);
                if (data && data.length) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].Abbreviate + '">' + data[i].Name + '</option>';
                    }
                    $('#school').html(html);
                    $('#hidSchoolLevelID').val(schoolLevelID);
                }
                else {
                    $('#school').html('<option value="0">请选择</option>');
                }
            }
            else {
                $('#school').html('<option value="0">请选择</option>');
            }
        })
    }
}

//切换报读地区
function changeReadArea(readTypeID) {
    var readAreaID = parseInt($('#readArea').val());
    var hidReadAreaID = parseInt($('#hidReadAreaID').val())
    if (readAreaID != hidReadAreaID) {
        $.get('/ajax/getSchools.ashx', {
            readTypeID: readTypeID,
            readAreaID: readAreaID,
            schoolLevelID: parseInt($('#schoolLevel').val())
        }, function (result) {
            if (result) {
                var data = JSON.parse(result);
                if (data && data.length) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].Abbreviate + '">' + data[i].Name + '</option>';
                    }
                    $('#school').html(html);
                    $('#hidReadAreaID').val(readAreaID);
                }
                else {
                    $('#school').html('<option value="0">请选择</option>');
                }
            }
            else {
                $('#school').html('<option value="0">请选择</option>');
            }
        })
    }
}