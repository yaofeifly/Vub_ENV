MacPlayer.Html = '<OBJECT id="Player" height="'+MacPlayer.Height+'" width="100%" data=data:application/x-oleobject;base64,Q5uJqr0ka0u70EVVfY0R4AADAAB7TAAATygAAAEAAAA= classid=CLSID:AA899B43-24BD-4B6B-BBD0-45557D8D11E0 VIEWASTEXT onError="MacPlayer.Install();"></OBJECT>';
MacPlayer.Show();

setTimeout(function(){
	if (MacPlayer.status == true && MacPlayer.Flag==1) {
		Player.ServerMode = 2;
		Player.PlayModeValue = MacPlayer.PlayUrl;
		Player.ChannelID = MacPlayer.PlayUrl;
		Player.AuthenHost = MacPlayer.PlayFrom;
		Player.ServerHost = MacPlayer.PlayFrom;
		Player.ContorlWidth = MacPlayer.Width;
		Player.ContorlHeight = MacPlayer.Height;
		Player.UserName = "";
		Player.UserID = "";
		Player.PlayMode = 1;
		Player.Session = "";
		Player.ProtocolType = 1;
		Player.EmbedMode = 2;
		Player.ProgName = "";
		Player.VODVersion = 6000;
		Player.Start()
	}
},
MacPlayer.Second + 1000);