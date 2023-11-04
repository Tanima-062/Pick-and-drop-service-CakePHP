<script type="text/javascript">
  const yotpoReviews = <?= json_encode($yotpoReviews); ?>;
  let editYotpoReviews = <?= json_encode($yotpoReviews); ?>;
<?php
  if(isset($yotpoReviewOnlyScore)){
?>
  const yotpoReviewOnlyScore = <?= json_encode($yotpoReviewOnlyScore); ?>;
<?php 
  }else{
?>
const yotpoReviewOnlyScore = <?= json_encode($yotpoReviews); ?>;
<?php 
  }
?>
  const reviewCount = <?= json_encode($reviewCount); ?>;
  const perPage = typeof $_perPage !== 'undefined' ? $_perPage : 10
  const isExistedReviewPage = typeof $_isExistedReviewPage !== 'undefined' ? $_isExistedReviewPage : false
</script>

<?php 
  if(count($yotpoReviews)>0){
?>
  <div class="reveiw_overview_wrap">
    <div class="reveiw_overview_total_score">
      <div class="reveiw_overview_total_score_title">総合得点</div>
      <div class="reveiw_overview_total_score_contents">
        <span class="average_score"></span>
        <span class="full_score">/ 5</span>
      </div>
      <div class="reveiw_overview_total_score_stars"></div>
    </div>
    <div class="reveiw_overview_distribution_score">
      <ul class="reveiw_overview_distribution_score_list"></ul>
      <a class="reveiw_overview_show_all hide">全てのレビューを表示</a>
    </div>
  </div>

  <div class="review_container">
    <div class="review_container_header">
      <?php if($reviewCount >= $yotpoReviewLimit){ ?>
        <div class="review_count">全<?=$reviewCount;?>件<span style="font-weight:400;">(最新の<?=$yotpoReviewLimit;?>件を表示しています)</span></div>
      <?php }else{ ?>
        <div class="review_count">全<?=$reviewCount;?>件</div>
      <?php } ?>
      <div class="review_sort">
        <div class="review_sort_button">
          <span>並べ替え:</span>
          <span id="selected-sort">
            選択
          </span>
          <span class="yotpo-icon yotpo-icon-down-triangle"></span> 
        </div>
        <ul class="review_sort_list hide">
          <li class="list-category" data-name="date-desc">
            <i class="icm-clock"></i>
            <span>新しい順</span>
          </li>  
          <!-- <li class="list-category" data-name="images-desc">
            <i class="icm-picture"></i>
            <span>写真付き</span>
          </li> -->
          <li class="list-category" data-name="rating-desc">
            <i class="icm-star-full"></i>
            <span>評価の高い順</span>
          </li>
          <li class="list-category" data-name="rating-asc">
            <i class="icm-star-empty"></i>
            <span>評価の低い順</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="review_content_wrap">
      <ul id="review_content">
<?php
    foreach($yotpoReviews as $yotpoReview){
?>
        <li class="review_wrap">
            <div class="review_left"></div>
            <div class="review_right">
                <div class="review_title"><?=$yotpoReview['title']?></div>
                <div class="review_content"><?=$yotpoReview['content']?></div>
                <div class="link_wrap">
                    <?=$yotpoReview['client_name']?> 
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <?=$yotpoReview['office_name']?> 
                </div>
                <div class="posted_date">
                    投稿日：<?=$yotpoReview['created_at']?> 
                </div> 
            </div>
        </li>
<?php
    } 
?>
      </ul>
    </div>

    <div class="review_paging_wrap hide">
      <div id="review_paging"></div>
    </div>

    <div class="linkto_review_page hide">
      <a class="review_btn" href="/rentacar/company/<?=$clientInfo['url'];?>/<?=$officeInfo['url'];?>/review/">
        <span>レビューをもっと読む</span>
      </a>          
    </div>
    
    <div class="yotpo">
      <a class="yotpo-link" href="https://www.yotpo.com/powered-by-yotpo/?utm_campaign=branding_link_reviews_widget_v2&utm_medium=widget&utm_source=skyticket.jp">
        <span class="yotpo-logo-title">Reviews by</span>
        <span class="yotpo-icon yotpo-icon-yotpo-logo"></span>
      </a>
    </div>
  </div>
<?php
  }
?>

<script type="text/javascript">
  $(function(){        
    /** Review OverView Rendering Start*/
    if(yotpoReviewOnlyScore.length > 0) {
      const sumScore = (yotpoReviewOnlyScore.reduce((a, b) => a + Number(b.score), 0))
      const avgScore = (sumScore / reviewCount).toFixed(1)
      $(".average_score").text(avgScore)
      
      let avgStars = ''
      for(let i = 1; i < 6; i++) {
        if(avgScore > i) avgStars += '<i class="icm-star-full"></i>'
        else {
          if((i - avgScore) < 1) { 
            avgStars += `<div class="rate-score">
                          <i class="icm-star-full" style="width: ${(avgScore - (i - 1)) * 20}px"></i>
                          <i class="icm-star-empty"></i>
                        </div>`
          }
          else avgStars += '<i class="icm-star-empty"></i>'
        }
      }
      $(".reveiw_overview_total_score_stars").html(avgStars)
      
      const distributionScoreBoard = {
        '1': 0,
        '2': 0,
        '3': 0,
        '4': 0,
        '5': 0
      }
      yotpoReviewOnlyScore.forEach((item) => {
        distributionScoreBoard[item.score] = distributionScoreBoard[item.score] + 1
      })
      let distributionScoreList = ''
      for(let i = 5; i > 0; i--) {
        distributionScoreList +=
            `<li data-name="${i}">
              <div class="distributions_star_num">${i}</div>
              <div class="distributions_graph_wrap">
                <div class="distributions_graph_score" style="width: ${distributionScoreBoard[i.toString()] / reviewCount * 100}%"></div>
              </div>
            </li>`
      }
      $(".reveiw_overview_distribution_score_list").html(distributionScoreList)
      /** Review OverView Rendering End*/
      
      /** Review list Rendering Start */ 
      paging(yotpoReviews, perPage, 1);
      /** Review list Rendering End */ 
      
      if(isExistedReviewPage && yotpoReviews.length > 10) {
        $(".linkto_review_page").removeClass("hide")
      } else if(yotpoReviews.length > 10) {
        $(".review_paging_wrap").removeClass("hide")
      }
      
      $(".reveiw_overview_distribution_score_list li").click(function() {
        const score = $(this).data('name')
        editYotpoReviews = yotpoReviews.filter((item) => item.score === score.toString())
        if(editYotpoReviews.length > 0) {
          $(".reveiw_overview_show_all").removeClass("hide")
          paging(editYotpoReviews, perPage, 1)
          $("html,body").animate({ scrollTop: $('.reveiw_overview_wrap').offset().top - 10});
        }
      })
      
      $(".reveiw_overview_show_all").click(function() {
        editYotpoReviews = <?= json_encode($yotpoReviews); ?>;
        $(".reveiw_overview_show_all").addClass("hide")
        $("#selected-sort").text("選択 ")
        paging(editYotpoReviews, perPage, 1)
        $("html,body").animate({ scrollTop: $('.reveiw_overview_wrap').offset().top - 10});
      })
      
      $("#selected-sort").click(function(e) {
        $(".review_sort_list").toggleClass('hide')
      })
      
      $(".review_sort_list li").click(function() {
        const sort_name = $(this).data('name')
        switch(sort_name) {
          case 'date-desc':
            editYotpoReviews.sort((a, b) => Date.parse(b.created_at) - Date.parse(a.created_at))
            break;
          case 'images-desc':
            // TODO: 写真付き順番
            break;
          case 'rating-desc':
            editYotpoReviews.sort((a, b) => Number(b.score) - Number(a.score))
            break;
          case 'rating-asc':
            editYotpoReviews.sort((a, b) => Number(a.score) - Number(b.score))
            break;
        }
        paging(editYotpoReviews, perPage, 1);
        $("html,body").animate({ scrollTop: $('.reveiw_overview_wrap').offset().top - 10});
        $("#selected-sort").text($(this).text())
        $(".review_sort_list").addClass('hide')
      })
      
      $("body").click(function (e){
        var hideReviewSortlist = $(".review_sort_list");
        var target = $(e.target)
        if(hideReviewSortlist && !target.is("#selected-sort") && !target.is(".list-category")) {
          $(".review_sort_list").addClass('hide')
        }
      });
      // TODO: レビュー続きを読む
      // $(".review_read_more").on("click", function(){
      //   var review_id = $(this).parents("li").attr("id");
      //   var objReviewAll = $("#"+review_id+" .review_cont_all");
      //   var objReviewOmmit = $("#"+review_id+" .review_cont_ommit");

      //   $(this).hide().attr("aria-expanded", false);
      //   objReviewOmmit.hide().attr("aria-hidden", true);
      //   objReviewAll.show().attr("aria-hidden", false);
      // });
    }
  });
</script>

<script type="text/javascript">
  function paging(totalData, perPage, curPage) {
    let totalPage = Math.ceil(totalData.length / perPage)
    let startNum = curPage * perPage - (perPage - 1)
    let endNum = curPage === totalPage ? totalData.length : curPage * perPage
    
    pageContentsRender(totalData, startNum, endNum);
    pageNavigationRender(totalData, totalPage, curPage);
  }
  
  function pageContentsRender(totalData, startNum, endNum) {
    let pageContents = ''
    for(let i = startNum-1; i < endNum; i++ ) {
      pageContents += `<li class="review_wrap">
                        <div class="review_left">
                          <div class="icon">
                            <i class="icm-user-shape" />
                          </div>
                          <div class="stars">`
      for(let s = 1; s < 6; s++) {
        if(s <= Number(totalData[i].score))
          pageContents +=   '<i class="icm-star-full"></i>'
        else 
          pageContents +=   '<i class="icm-star-empty"></i>'
      }
      pageContents +=    `</div>
                        </div>
                        <div class="review_right">
                        ${totalData[i].content.replace(/\n/g, " ") === totalData[i].title ? 
                         `<div class="review_content">${totalData[i].content}</div>` :
                         `<div class="review_title">${totalData[i].title}</div>
                          <div class="review_content">${totalData[i].content}</div>` }
                          <div class="link_wrap">
                            ${totalData[i].client_name}
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            ${totalData[i].office_name}
                          </div>
                          <div class="posted_date">
                            投稿日：${totalData[i].created_at}
                          </div> 
                        </div>
                      </li>`
    }
    $('#review_content').html(pageContents)
  }
  
  function pageNavigationRender(totalData, totalPage, curPage) {
    let pageNav = "";
    pageNav += `<a class="${curPage > 1 ? 'btn_review_prev' : 'btn_review_prev disabled'}"><i class="icm-right-arrow icon-rotate-change-left"></i></a>`;
    pageNav += "<div>" + curPage + " / " + totalPage + "</div>";
    pageNav += `<a class="${curPage < totalPage ? 'btn_review_next' : 'btn_review_next disabled'}"><i class="icm-right-arrow"></i></a>`;
    $("#review_paging").html(pageNav);
    
    $("#review_paging a").click(function() {
      var $item = $(this);
      var $class = $item.attr("class");
      
      if($class == "btn_review_next") {
        $("html,body").animate({ scrollTop: $('.reveiw_overview_wrap').offset().top - 10});
        paging(totalData, perPage, curPage+1);
      }
      else if($class == "btn_review_prev") {
        $("html,body").animate({ scrollTop: $('.reveiw_overview_wrap').offset().top - 10});
        paging(totalData, perPage, curPage-1);
      }
    });
  }
</script>
