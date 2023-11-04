<!-- キャンペーンページ（wpcampaign_pageとは別）　→→長いこと使ってない -->
<link rel="stylesheet" type="text/css" href="/rentacar/css/campaign.css">
<div class="campaign-area">
    <section>
        <div class="client-name">
            <h1><?=$clientInfo['Client']['name']?>のキャンペーン</h1>
        </div>
        <?php /*
                <img src="<?=$campaignData['ClientCampaign']['thumbnail_image'];?>" height="69px">
        */ ?>
        <?php
            if(!empty($campaignData['ClientCampaign']['thumbnail_image'])) {
                $strPath = '/client/img/../../img/ClientCampaigns/' . $campaignData['ClientCampaign']['id'] . DS . $campaignData['ClientCampaign']['thumbnail_image'];
                echo $this->Html->image($strPath,array('width'=>'95','height'=>'69'));
            }
        ?>
        <img src="/rentacar/img/logo/oblong/<?php echo $clientInfo['Client']['id']; ?>/<?php echo $clientInfo['Client']['logo_image']; ?>" height="69px">
    </section>
    <div class="campaign-title">
        <h3><?=$campaignData['ClientCampaign']['title'];?></h3>
    </div>

    <?php if(isset($campaignData['ClientCampaign']['overview']) && !empty($campaignData['ClientCampaign']['overview'])){ ?>
        <div class="campaign-contents">
            ≪キャンペーン概要≫<br>
            <?=nl2br($campaignData['ClientCampaign']['overview']);?>
        </div>
    <?php } ?>

    <?php if((isset($campaignData['ClientCampaign']['period_start']) && !empty($campaignData['ClientCampaign']['period_start']))
        && (isset($campaignData['ClientCampaign']['period_end']) && !empty($campaignData['ClientCampaign']['period_end']))){ ?>
        <div class="campaign-contents">
            ≪キャンペーン対象期間≫<br>
            <?=date('Y年n月j日', strtotime($campaignData['ClientCampaign']['period_start']));?>～ <?=date('Y年n月j日', strtotime($campaignData['ClientCampaign']['period_end']));?>
        </div>
    <?php } ?>

    <?php if((isset($campaignData['ClientCampaign']['booking_start']) && !empty($campaignData['ClientCampaign']['booking_start']))
        && (isset($campaignData['ClientCampaign']['booking_end']) && !empty($campaignData['ClientCampaign']['booking_end']))){ ?>
        <div class="campaign-contents">
            ≪ご予約可能期間≫<br>
            <?=date('Y年n月j日', strtotime($campaignData['ClientCampaign']['booking_start']));?>～ <?=date('Y年n月j日', strtotime($campaignData['ClientCampaign']['booking_end']));?>
        </div>
    <?php } ?>

    <?php if(isset($campaignData['ClientCampaign']['vehicle_fee_example']) && !empty($campaignData['ClientCampaign']['vehicle_fee_example'])){ ?>
        <div class="campaign-contents">
            ≪車両クラス・料金例≫<br>
            <?=nl2br($campaignData['ClientCampaign']['vehicle_fee_example']);?>
        </div>
    <?php } ?>

<?php /*
    <?php if(isset($campaignData['ClientCampaign']['image1']) && !empty($campaignData['ClientCampaign']['image1'])){ ?>
        <div class="campaign-contents">
            <img src="<?=$campaignData['ClientCampaign']['image1'];?>">
        </div>
    <?php } ?>

    <?php if(isset($campaignData['ClientCampaign']['image2']) && !empty($campaignData['ClientCampaign']['image2'])){ ?>
        <div class="campaign-contents">
            <img src="<?=$campaignData['ClientCampaign']['image2'];?>">
        </div>
    <?php } ?>

    <?php if(isset($campaignData['ClientCampaign']['image3']) && !empty($campaignData['ClientCampaign']['image3'])){ ?>
        <div class="campaign-contents">
            <img src="<?=$campaignData['ClientCampaign']['image3'];?>">
        </div>
    <?php } ?>
*/ ?>

    <?php
        if(!empty($campaignData['ClientCampaign']['image1'])) {
            $strPath = '/client/img/../../img/ClientCampaigns/' . $campaignData['ClientCampaign']['id'] . DS . $campaignData['ClientCampaign']['image1'];
            echo '<div class="campaign-contents">'.$this->Html->image($strPath).'</div>';
        }
    ?>

    <?php
        if(!empty($campaignData['ClientCampaign']['image2'])) {
            $strPath = '/client/img/../../img/ClientCampaigns/' . $campaignData['ClientCampaign']['id'] . DS . $campaignData['ClientCampaign']['image2'];
            echo '<div class="campaign-contents">'.$this->Html->image($strPath).'</div>';
        }
    ?>

    <?php
        if(!empty($campaignData['ClientCampaign']['image3'])) {
            $strPath = '/client/img/../../img/ClientCampaigns/' . $campaignData['ClientCampaign']['id'] . DS . $campaignData['ClientCampaign']['image3'];
            echo '<div class="campaign-contents">'.$this->Html->image($strPath).'</div>';
        }
    ?>
</div>
