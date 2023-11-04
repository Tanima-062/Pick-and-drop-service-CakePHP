<div class="wrap contents clearfix">
<?php
    echo $this->element('progress_bar'); 
?>
        <div class="page_title">
            <h1 class="page_title_h2">フェリーターミナル一覧からレンタカーを探す</h1>
        </div>
<?php   if(!empty($ferryTerminalDataArrayTop)){  ?>
    <?php   foreach($ferryTerminalDataArrayTop as $value){ ?>
               <section class="from_about rent-margin-bottom">
                    <h2 class="rentacar_text_ttl2"><?= $value['head']; ?></h2>
                    <p class="from_about_contents">
                        <?= $value['text']; ?>
                    </p>
                </section>
    <?php   } ?>
<?php }  ?>
<?php   foreach($landmarkList['airportArray'] as $prefectures => $terminal_prefectures){ ?>
                <h3 class="hd-left-bordered"><?= $prefectures; ?>の港から探す</h3>
        <div class="search_rentacar">
        <ul>
<?php       foreach($terminal_prefectures as $airport_id => $terminal){ ?>
                <?php if(!empty($terminalLinkCdList[$airport_id])){ ?>
                    <li><a href="/rentacar/<?= $base_url_arr[$prefectures] . $terminalLinkCdList[$airport_id]?>/"><span><?= $terminal ?></span></a></li>
                <?php }else{ ?>
                    <li><a href="/rentacar/searches?place=3&airport_id=<?=$airport_id;?>&_submit=&year=<?=$link_date_arr['year'];?>&month=<?=$link_date_arr['month'];?>&day=<?=$link_date_arr['day'];?>&time=11-00&return_way=0&_submit=&return_year=<?=$link_date_arr['year'];?>&return_month=<?=$link_date_arr['month'];?>&return_day=<?=$link_date_arr['day'];?>&return_time=17-00&adults_count=2&children_count=&infants_count="><span><?= $terminal ?></span></a></li>
                <?php } ?>
<?php       } ?>
        </ul>
        </div>
<?php   } ?>
<?php   if(!empty($ferryTerminalDataArrayBottom)){  ?>
    <?php   foreach($ferryTerminalDataArrayBottom as $value){ ?>
               <section class="from_about rent-margin-bottom">
                    <h2 class="rentacar_text_ttl2"><?= $value['head']; ?></h2>
                    <p class="from_about_contents">
                        <?= $value['text']; ?>
                    </p>
                </section>
    <?php   } ?>
<?php }  ?>
</div>
<style>
.airport_rentacar_info{
    height: auto;
    background-color: #ebf6ff;
    padding: 10px 20px;
    margin-bottom: 15px;
}
</style>