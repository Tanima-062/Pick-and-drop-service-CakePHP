<footer id="footer" class="footer_reservations">
    <div class="footer_about_wrapper">
        <div class="footer_about">
            <div class="about_logos">
<?php
    echo $this->Html->image("/img/icon_jpx.png", [
        "width" => "30",
        "height" => "34",
        "alt" => "icon_jpx"
    ]);
    echo $this->Html->image("/img/icon_jata.png", [
        "width" => "30",
        "height" => "31",
        "alt" => "icon_jata"
    ]);
?>
            </div>
            <div class="about_text">
                <span>株式会社アドベンチャー（東証グロース上場）</span></br>
                一般社団法人日本旅行協会(JATA)正会員&nbsp;/&nbsp;観光庁長官登録旅行業第2035号
            </div>
        </div>
    </div>
    <div class="footer_logo">
        <div class="logo">
<?php
    echo $this->Html->image("/img/logo.png", ["alt" => "skyticket"]);
?>
        </div>
    </div>
    <div class="footer_bottom">
        <p class="copyright">&copy;<?php echo date('Y');?> ADVENTURE inc. All rights reserved.</p>
    </div><!-- wrap clearfix End -->
</footer><!-- footer End -->
