function CoolAdsStart() {
    if (document.documentElement.clientHeight > 0) {
        $('#buffer').height (MacPlayer.Height - 45 );
        $('#buffer').show();
    }
}
function CoolStatus() {
    if (Player.Full == 0) {
        if (Player.PlayState == 3) {
            MacPlayer.AdsEnd()
        } else {
            CoolAdsStart()
        }
    }
}
function CoolNextDown() {
    if (Player.get_CurTaskProcess() > 900 && !bstartnextplay){
        Player.StartNextDown( CoolUrl(MacPlayer.PlayUrl1) );
        bstartnextplay = true
    }
}
function CoolUrl(url){
    if(url==null || url==undefined) return "";
	url = url.split("|");
	return url[0]+"|"+url[1]+"|["+document.domain+"]"+url[2]+"|";
}

MacPlayer.Html ='<object id="Player" width="100%" height="'+MacPlayer.Height+'" classid="clsid:73BAB958-AC02-5108-B2B8-665834A9C63A" onError="MacPlayer.Install();"><param name="URL" VALUE="'+CoolUrl(MacPlayer.PlayUrl)+'"><param name="Autoplay" VALUE="1"><param name="CoolAdUrl" VALUE="'+MacPlayer.Buffer+'"><param name="NextWebPage" VALUE="'+ MacPlayer.NextUrl +'"><PARAM NAME="Showcontrol" VALUE="1"></object>';
var rMsie = /(msie\s|trident.*rv:)([\w.]+)/;
var match = rMsie.exec(navigator.userAgent.toLowerCase());
if(match == null){
	if (navigator.plugins){
		var ll = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'CoolPlugin'){
				ll = true;
				break;
			}
		}
	}
	if(ll){
		MacPlayer.Html ='<embed URL="'+MacPlayer.PlayUrl+'" type="application/cool-plugin" autoplay="1" showcontrol="1" width="100%" height="'+MacPlayer.Height+'"></embed>';
	}
	else{
		MacPlayer.Install();
	}
}
MacPlayer.Show();
setTimeout(function(){
	if (MacPlayer.Status == true && MacPlayer.Flag==1) {
		setInterval("CoolStatus()", 1000);
		if (MacPlayer.NextUrl) {
			Player.NextWebPage = MacPlayer.NextUrl;
			setInterval("CoolNextDown()", 9333)
		}
	}
},
MacPlayer.Second *1000 + 1000);