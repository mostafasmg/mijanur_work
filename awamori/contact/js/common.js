// �K�w�����[�g�œ��삵�Ȃ��ꍇ�Ƀ��[�_���_�C�A���O�������Ȃ��̂�
// ���Ȃ�悭�Ȃ����A�����ŃO���[�o���ϐ��������Ďb��Ή��Ƃ���
// Root����̃p�X���L�q����
var dir_strings = "";

$(function() 
{
    // ENTER�C�x���g�Ŏ��̃e�L�X�g�G���A���t�H�[�J�X
    setInputKeyPress();
    
    // �^�C�g���߂�{�^��
    setTitleBackEvent();
    
    // ���b�Z�[�W�G���A�̏���
    setMessageClickHideEvent();
    
// ==============================================================
// 2019.05.28 S:DEL HEIJI �X�N���[�����X�g�����pJS�t�@�C���ֈړ�
// ==============================================================
//    // �X�N���[�����X�g�̏���
//    setScrollList();
// ==============================================================
// 2019.05.28 E:DEL HEIJI �X�N���[�����X�g�����pJS�t�@�C���ֈړ�
// ==============================================================
});

// ==============================================================
// 2019.05.28 S:DEL HEIJI �X�N���[�����X�g�����pJS�t�@�C���ֈړ�
// ==============================================================
//function setScrollList() 
//{
//    // �X�N���[�����X�g�̃}�E�X�I�[�o�[
//    $("#sl_main").find("#data").find("table tr").hover(
//        function()
//        {
//// 2019.05.28 S:Modify HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val == undefined)
//            {
//                $(this).attr("orgcolor", $(this).find("td").css("background-color"));
//                $(this).find("td").css("background-color", "#AFEEEE");
//            }
//// 2019.05.28 E:Modify HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//        },
//        function()
//        {
//// 2019.05.28 S:Modify HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val == undefined)
//            {
//                $(this).find("td").css("background-color", $(this).attr("orgcolor"));
//            }
//// 2019.05.28 E:Modify HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//        }
//    );
//    
//    // �X�N���[�����X�g�̃N���b�N
//    $("#sl_main").find("#data").find("table tr").click(function()
//    {
//// 2019.05.28 S:Delete HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//        // ���̍s�̔w�i�F��߂�
//        $("#sl_main").find("#data").find("table tr").each(function()
//        {
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val != undefined)
//            {
//                $(this).find("td").css("background-color", $(this).attr("selected_key"));
//            }
//        });
//        
//        // �S�Ă�selected_key���O��
//        $("#sl_main").find("#data").find("table tr").removeAttr("selected_key");
//        
//        // �I���s��selected_key������t�^
//        $(this).attr("selected_key", $(this).attr("orgcolor"));
//        
//        // �I���s�̐F��ς���
//        $(this).find("td").css("background-color", "#00BFFF");
//// 2019.05.28 E:Delete HEIJI rownum���g�p���Ȃ��Ă��ǂ��悤�ɉ���
//    });
//}
// ==============================================================
// 2019.05.28 E:DEL HEIJI �X�N���[�����X�g�����pJS�t�@�C���ֈړ�
// ==============================================================

// ���l���J���}��؂�ŕԂ�
function CurrencyFormat(num, opt_yenmk_flg)
{
    // �~�}�[�N�t���O�������Ă�����擪��\�����ĕԂ�
    var yenmk_flg = 0;
    if (yenmk_flg != undefined)
    {
        yenmk_flg = opt_yenmk_flg;
    }
    
    // �J���}����
    var afterStr = String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    
    if (yenmk_flg == 1)
    {
        // \�}�[�N���Ŗ߂�
        return AddYenMark(afterStr);
    }
    else
    {
        // \�}�[�N�����Ŗ߂�
        return afterStr;
    }
}

// ������̐擪��\�}�[�N�����ĕԂ�
function AddYenMark(str)
{
    return "\\" + str;
}

// ENTER�C�x���g�Ŏ��̃e�L�X�g�G���A���t�H�[�J�X
function setInputKeyPress()
{
    $("input").keypress(function(e)
    {
        if (e.keyCode === 13)
        {
            // �e�L�X�g�łȂ���Δ�����
            var type = $(this).attr("type");
            if (type != "text")
            {
                // SUBMIT�A�Ⴕ����BUTTON�Ȃ�ENTER��ʂ�
                if (type == "submit" || type == "button")
                {
                    return;
                }
                return false;
            }
            
            var nextIndex = $("input[type=text]").index(this) + 1;
            
            if ($("input[type=text]").eq(nextIndex).length === 0)
            {
                nextIndex = 0;
            }
            
            $("input[type=text]").eq(nextIndex).focus();
            return false;
        }
    });
}

// �^�C�g���߂�{�^���̎���
function setTitleBackEvent()
{
    $("#btnTitleBack").click(function()
    {
        var pathname = location.pathname;
        var params = pathname.split("/");
        
        var paramlen = params.length;
        var filename = params[paramlen-1];
        if (filename.length > 0 && filename.indexOf("index") < 0)
        {
            // �O�̉�ʂɖ߂�
            history.back();
        }
        else
        {
            // �z�[����ʂɖ߂�
            location.href = "../../";
        }
    });
}

// ���b�Z�[�W�G���A�̏���
function setMessageClickHideEvent()
{
    $("#MessageArea").click(function()
    {
        
        $(this).find("p").toggle("slow");
    });
}

// �X�N���[�����X�g�̍������g������
function setScrollListHeight(new_height)
{
    // Base�̍�����300px�A263px�A278px
    var sl_main_height = $("#sl_main").css("height").replace("px", "");
    var header_v_height = $("#header_v").css("height").replace("px", "");
    var data_height = $("#data").css("height").replace("px", "");
    
    var diff_height = new_height - sl_main_height;
    
    // �v�Z��̒l�ɕύX
    sl_main_height = parseInt(sl_main_height) + parseInt(diff_height);
    header_v_height = parseInt(header_v_height) + parseInt(diff_height);
    data_height = parseInt(data_height) + parseInt(diff_height);
    
    // �ăZ�b�g
    $("#sl_main").css("height", sl_main_height);
    $("#header_v").css("height", header_v_height);
    $("#data").css("height", data_height);
}
