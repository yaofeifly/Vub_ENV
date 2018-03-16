var bstartnextplay = false;
function BaiduAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height ( MacPlayer.Height - 62 );
        $('#buffer').show();
    }
}
function BaiduStatus() {
    if (Player.IsPlaying()) {
        MacPlayer.AdsEnd()
    } else {
        BaiduAdsStart()
    }
}
function BdhdUrl(url){
	if(url==null || url==undefined) return "";
	url = url.split("|");
	return url[0]+"|"+url[1]+"|["+document.domain+"]"+url[2]+"|";
}


MacPlayer.Html='<object id="Player" classid="clsid:02E2D748-67F8-48B4-8AB4-0A085374BB99" width="100%" height="'+MacPlayer.Height+'" onError="MacPlayer.Install();"><param name="URL" value="'+ BdhdUrl(MacPlayer.PlayUrl) +'"><param name="NextWebPage" value="'+ MacPlayer.NextUrl +'"><param name="NextCacheUrl" value="'+ BdhdUrl(MacPlayer.PlayUrl1) +'"><param name="Autoplay" value="1"></object>';
var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'BaiduPlayer Browser Plugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
	MacPlayer.Html = '<object id="Player" name="Player" type="application/player-activex" width="100%" height="'+MacPlayer.Height+'" progid="Xbdyy.PlayCtrl.1" param_URL="'+MacPlayer.PlayUrl+'"param_NextCacheUrl="'+MacPlayer.PlayUrl1+'" param_LastWebPage="" param_NextWebPage="'+MacPlayer.NextUrl+'" param_OnPlay="onPlay" param_OnPause="onPause" param_OnFirstBufferingStart="onFirstBufferingStart" param_OnFirstBufferingEnd="onFirstBufferingEnd" param_OnPlayBufferingStart="onPlayBufferingStart" param_OnPlayBufferingEnd="onPlayBufferingEnd" param_OnComplete="onComplete" param_Autoplay="1"></object>'
	}
	else{
		MacPlayer.Install();
	}
}
MacPlayer.Show();
setTimeout(function(){
	if (MacPlayer.Status == true && MacPlayer.Flag==1){
		setInterval("BaiduStatus()", 1000);
		if (MacPlayer.NextUrl) {
			Player.NextWebPage = MacPlayer.NextUrl
		}
	}
},
MacPlayer.Second * 1000 + 1000);