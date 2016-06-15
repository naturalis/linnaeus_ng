<div id="countdown-container">
	{t}Remaining time until reset:{/t}
	<span id="countdown"></span>
</div>

<script>
var targetDate = new Date('{$cronNextRun}').getTime();

{literal}
var days, hours, minutes, seconds;
var countdown = document.getElementById('countdown');
var countdownTimer = setInterval(function() {
	var currentDate = new Date().getTime();
	var secondsLeft = (targetDate - currentDate) / 1000;

	days = parseInt(secondsLeft / 86400);
	secondsLeft = secondsLeft % 86400;

	hours = parseInt(secondsLeft / 3600);
	secondsLeft = secondsLeft % 3600;
	minutes = parseInt(secondsLeft / 60);
	seconds = parseInt(secondsLeft % 60);

	if (days <= 0 && hours <= 0 && minutes <= 0 && seconds <= 0 ) {
	   document.getElementById('countdown').innerHTML = '';
	   clearInterval(countdownTimer);
	} else {
		if (days > 0) {
			 days = days + 'd, ';
		} else {
			 days = '';
		}
		countdown.innerHTML = days + checkTime(hours) + ':'+ checkTime(minutes) + ':' + checkTime(seconds);
	}
}, 1000);

function checkTime(i) {
	if (i < 10) {i = '0' + i};
	return i;
}
</script>
{/literal}