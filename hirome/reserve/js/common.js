// �K�w�����[�g�œ��삵�Ȃ��ꍇ�Ƀ��[�_���_�C�A���O�������Ȃ��̂�
// ���Ȃ�悭�Ȃ����A�����ŃO���[�o���ϐ��������Ďb��Ή��Ƃ���
// Root����̃p�X���L�q����
var dir_strings = "";

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

$(function() 
{
    // ENTER�C�x���g�Ŏ��̃e�L�X�g�G���A���t�H�[�J�X
    setInputKeyPress();
    
    // �^�C�g���߂�{�^��
    setTitleBackEvent();
    
    // ���b�Z�[�W�G���A�̏���
    //setMessageClickHideEvent();
});
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
        if (filename.length > 0 && filename.indexOf("index") < 0) // index�ȊO�����HistoryBack
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