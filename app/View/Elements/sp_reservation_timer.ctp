<!-- step1, step2の予約確保タイマー -->

<div id="js_reserve_timer" class="notes_reserve_box" style="display: none;">
	<p class="secure_timer_area">このページを確保しておける時間はあと&nbsp;<span><span id="timer_min">0</span>分<span id="timer_sec">00</span>秒</span></p>
</div>

<script>
$(function() {
	// タイマー処理
	$("#js_reserve_timer").show();
	var count = 600;
	var min=10;
	var sec=00;
	var countdown = function(){
		count--;
		min = Math.floor(count/60);
		sec = count%60;
		$("#timer_min").text(min);
		$("#timer_sec").text( ("00"+sec).slice(-2) );
	};
	var timer = setInterval(function(){
		countdown();
		if(count < 1){
			clearInterval(timer);
		}
	}, 1000);
});
</script>
