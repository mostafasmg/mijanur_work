<div class="modaldisplay_video">
	<div class="fix">
		<p class="f-right"><input type="image" src="../../++[lcl_path]image/close.png" class="close" /></p>
	</div>
    <video id="streem_video" src="../../++[lcl_video_path]" width="500" height="360" controls autoplay />
<!--   
-->
</div>


<script type="text/javascript">
$(document).ready(function()
{
    // スクロール幅の自動調整
    var count = $(".video_scroll").children(".pics").length;
    var base_width = $(".pic_thum").width();
    var scroll_width = (base_width + 40 ) * count + 10;
    $(".video_scroll").width(scroll_width);
    
    // 動画差し替え
    $(".click_video").click(function(e)
    {
        var video_path = "../../../video/" +  $(this).attr("video");
        var video_elm = document.getElementById('streem_video');
        video_elm.src = video_path;
        video_elm.load();
        video_elm.play();
    });
    
    // 画像の透過率を変更する
    $(".mouseover_class").mouseover(function(e)
    {   
        $(this).parent().find(".video_start_dialog").attr("src", "../../../image/ready.png");
        $(this).parent().find(".video_start_dialog").animate({ 
            width: "48px",
            height: "48px",
            marginLeft: "-6px",
            marginTop: "-6px",
        }, 150 );
    });
    
    $(".mouseover_class").mouseleave(function(e)
    {
        $(this).parent().find(".video_start_dialog").attr("src", "../../../image/movie64.png");
        $(this).parent().find(".video_start_dialog").animate({ 
            width: "32px",
            height: "32px",
            marginLeft: "0px",
            marginTop: "0px",
      }, 150 );
    });
});
</script>
