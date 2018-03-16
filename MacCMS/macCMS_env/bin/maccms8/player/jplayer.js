
document.write('<link href="'+SitePath+'player/jplayer/skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />');
document.write('<script type="text/javascript" src="'+SitePath+'player/jplayer/js/jquery.jplayer.min.js"></script>');

MacPlayer.Html = '<div id="jquery_jplayer_1" class="jp-jplayer"></div>		<div id="jp_container_1" class="jp-audio">			<div class="jp-type-single">				<div class="jp-gui jp-interface">					<ul class="jp-controls">						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>					</ul>					<div class="jp-progress">						<div class="jp-seek-bar">							<div class="jp-play-bar"></div>						</div>					</div>					<div class="jp-volume-bar">						<div class="jp-volume-bar-value"></div>					</div>					<div class="jp-time-holder">						<div class="jp-current-time"></div>						<div class="jp-duration"></div>						<ul class="jp-toggles">							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>						</ul>					</div>				</div>				<div class="jp-title">					<ul>						<li>Cro Magnon Man</li>					</ul>				</div>				<div class="jp-no-solution">					<span>Update Required</span>					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.				</div>			</div>		</div>';


MacPlayer.Show();


$(document).ready(function(){

	$("#jquery_jplayer_1").jPlayer({
		ready: function (event) {
			$(this).jPlayer("setMedia", {
				mp3:MacPlayer.PlayServer+MacPlayer.PlayUrl
			}).jPlayer("play");
		},
		swfPath: SitePath+'player/jplayer/js',
		solution: "flash, html",
		supplied: 'mp3',
		wmode: "window",
		smoothPlayBar: true,
		keyEnabled: true
	});
});
