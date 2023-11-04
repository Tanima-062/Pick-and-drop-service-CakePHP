<!-- schema.org -->
<script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "AutoRental",
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "<?= $reviewAvg ?>",
          "reviewCount": "<?= $reviewCount ?>"
        },
        "name":"<?=$clientInfo['name']?> <?=$officeInfo['name']?>",
        "image":"https://skyticket.jp/rentacar/img/logo/square/<?php echo $clientInfo['id']; ?>/<?php echo $clientInfo['sp_logo_image']; ?>",
        "address":"<?=$officeInfo['address']?>",
        "description":"<?=$officeInfo['precautions']?>",
        "currenciesAccepted": "JPY",
        <?php if(!empty($officeInfo['latitude']) && !empty($officeInfo['longitude'])){ ?>
        "geo": {
          "@type":"GeoCoordinates",
          "latitude":<?=$officeInfo['latitude']?>,
          "longitude":<?=$officeInfo['longitude']?>
        },
        <?php } ?>
        "telephone":"<?=$officeInfo['tel']?>",
        "openingHours": [
          "Mo <?=$officeInfo['mon_hours_from']?>-<?=$officeInfo['mon_hours_to']?>",
          "Tu <?=$officeInfo['tue_hours_from']?>-<?=$officeInfo['tue_hours_to']?>",
          "We <?=$officeInfo['wed_hours_from']?>-<?=$officeInfo['wed_hours_to']?>",
          "Th <?=$officeInfo['thu_hours_from']?>-<?=$officeInfo['thu_hours_to']?>",
          "Fr <?=$officeInfo['fri_hours_from']?>-<?=$officeInfo['fri_hours_to']?>",
          "Sa <?=$officeInfo['sat_hours_from']?>-<?=$officeInfo['sat_hours_to']?>",
          "Su <?=$officeInfo['sun_hours_from']?>-<?=$officeInfo['sun_hours_to']?>"
        ]
      }
</script>
<!-- end schema.org -->	