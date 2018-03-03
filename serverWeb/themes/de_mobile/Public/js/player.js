$(function() {
    var canvas = document.getElementById('video-canvas');
    var apiEnterRoom = "/Api/SiSi/enterDeviceRoom";
    var apiEnterRoomParams = {};
    apiEnterRoomParams.token = TOKEN;
    apiEnterRoomParams.deviceid = deviceid;
    doPost(apiEnterRoom, apiEnterRoomParams, function(data) {
        if (data.code == 200) {
            $(".price.fr").text(data.data.price + "金币/次");
            price = data.data.price;
            new JSMpeg.Player(data.data.h5_video_url, { canvas: canvas });
            playerUserName = data.data.user_nicename;
            playerUid = data.data.uid;
            playerAvatar = data.data.avatar;
            matchID = data.data.match_id;
            changeChannelStatus(data.data.channel_status);
        } else {
            alert(data.descrp);
        }

    });
	

    var NORMAL = 1;
	var GAME_UP = 2;
	var GAME_DOWN = 3;
	var GAME_LEFT = 4;
	var GAME_RIGHT = 5;
	var GAME_OK = 9;
	var GAME_GO = 11;
	var GAME_CONNECT = 13;
	var GAME_CONNECTTED = 14;
	var GAME_NO = 10;
	var GAME_CAMERA = 12;
	var GAME_STOP_UP = 16;
	var GAME_STATUS_BUSY = 8;

	var playerUserName = "";
	var playerUid = "";
	var matchID = "";
	var price = 0;
	var playerAvatar = "";

	//var uid = "101238";
	//var userName = "USERNAME";
	//var userAvatar = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1513244465922&di=7f3edcb7ba78ae5cbb97e40f292717de&imgtype=0&src=http%3A%2F%2Fwww.qqzhi.com%2Fuploadpic%2F2015-02-02%2F211841154.jpg";
	//var balance = 73100;
    var first = true;

    var onlineNum = 0;
	var remoteUid = deviceid;
	var channel = deviceid;

	$(".pd.fl").text("余额："+balance);

	var socket = io(h5_node_server, { 'transports': ['websocket', 'polling'] }); //信令
	socket.emit('user joined', {
	    username: userName,
	    uid: uid,
        useravatar:userAvatar,
	    channel: channel
	});

    

    socket.on('getUsers', function(data) {
        console.log(data);
        onlineNum = data.length;
        $("#online_num").text("旁观："+onlineNum+"人");
        var temp = "";
        $.each( data, function(i, item){
            temp += "<img uid = '"+item.uid+"' class='txd' src='"+item.useravatar+"' />";
        });
        $("#userContainer").append(temp);
    });

	socket.on('user joined', function(data) {
	    console.log(data);
        if(first){
            socket.emit('getUsers', {
                username: userName,
                uid: uid,
                channel: channel
            });
            first = !first;
        }else{
            onlineNum++;
            $("#userContainer").prepend("<img uid = '"+data.uid+"' class='txd' src='"+data.useravatar+"' />");
            $("#online_num").text("旁观："+onlineNum+"人");
        }
        

	});

	socket.on('user left', function(data) {
	    console.log(data);
        onlineNum--;
        $("#userContainer[uid='"+data.uid+"']").remove();
        $("#online_num").text("旁观："+onlineNum+"人");
	});

	socket.on('new message', function(data) {
	    console.log(data);
	    var jsonData = $.parseJSON(data);
	    switch (jsonData.messageType) {
	        case GAME_OK:
	            zhuazhu(jsonData);
	            break;
	        case GAME_NO:
	            meizhuazhu(jsonData);
	            break;
	        case GAME_STATUS_BUSY:
                changeChannelStatus(3,jsonData);
                break;
	    }

	});
	socket.on('privateReceive', function(data) {
	    console.log("=================");
	    console.log(data);
	    switch (data.messageType) {
	        case GAME_CONNECTTED:
	            //$('#player').css("visibility", "visible");
	            changeChannelStatus(5);
	            break;
	    }

	});

	$(".zhua_ing .dtop").on("touchstart", function(e) {
	    sendCaozuo(GAME_UP);
	});
	$(".zhua_ing .ddown").on("touchstart", function(e) {
	    sendCaozuo(GAME_DOWN);
	});
	$(".zhua_ing .dleft").on("touchstart", function(e) {
	    sendCaozuo(GAME_LEFT);
	});
	$(".zhua_ing .dright").on("touchstart", function(e) {
	    sendCaozuo(GAME_RIGHT);
	});
	$(".zhua_ing .go").on("click", function() {
	    sendCaozuo(GAME_GO);
	    clearInterval(playerTimer);

	});
	$(".aside .xj").click(function() {
	    sendCaozuo(GAME_CAMERA);
	});
	$(".zhua_ing .dtop,.zhua_ing .ddown,.zhua_ing .dleft,.zhua_ing .dright").on("touchend", function() {
	    sendCaozuo(GAME_STOP_UP);
	});
	$("#sendMessage").click(function() {
	    var mes = $("#messageContent").val();
	    sendMessage(mes);
	    $("#container").append("<span style='display:block'>" + mes + "</span>");
	});

	function sendCaozuo(messageType) {
	    socket.emit('privateSend', {
	        uid: uid,
	        remoteUid: remoteUid,
	        playerUid: playerUid,
	        playerUserName: playerUserName,
	        playerAvatar: playerAvatar,
	        matchID: matchID,
	        messageType: messageType

	    });
	}

	function sendMessage(message) {
	    socket.emit('new message', {
	        uid: uid,
	        remoteUid: remoteUid,
	        messageContent: message,
	        userName: userName,
	        playerAvatar: playerAvatar,
	        userAvatar: userAvatar,
	        messageType: NORMAL

	    });
	}
	function GetRandomNum(Min, Max) {
        var Range = Max - Min;
        var Rand = Math.random();
        return (Min + Math.round(Rand * Range)) + "";
    }

    function changeChannelStatus(status,data){
    	$(".aside a").first().hide();
    	if(status == 2 || status == 4){
        	$(".user-zt").hide();
        	$(".aside a").first().hide();
        	$(".zhua_zb").show();
        	$(".zhua_ing").hide();
        	$(".djs").hide();
        	var t = $(".zhua_zb .btnb");
    		$(t).attr({
    			src : $(t).attr("src_on"),
    			disabled : false
    		});
        }else if(status == 3){
        	if(data != undefined){
        		playerUid = data.playerUid;
        		playerAvatar = data.playerAvatar;
        		playerUserName = data.playerUserName;
        	}
        	$(".user-zt").show();
        	$(".user-zt .tx").attr("src",playerAvatar);
        	$(".user-zt span").text(playerUserName+"游戏中");
        	$(".djs").hide();
        	if(playerUid == uid){
        		$(".zhua_zb").hide();
        		$(".zhua_ing").show();
        		$(".djs").show();
        		$(".aside a").first().show();
        	}else{
        		$(".zhua_zb").show();
        		//disbale
        		var t = $(".zhua_zb .btnb");
        		$(t).attr({
        			src : $(t).attr("src_of"),
        			disabled : true
        		});
        		$(".zhua_ing").hide();
        	}
        }else if(status == 5) {
        	$(".zhua_zb").hide();
        	$(".zhua_ing").show();
        	$(".aside a").first().show();
        	$(".user-zt span").text(playerUserName+"游戏中");
        	//修改余额   开始倒计时
        	balance -= price;
        	$(".pd.fl").text("余额："+balance);
        	$(".djs").show();
        	startCountTimer();
        	//我成功上机

        }else{
        	alert("设备维护中");
        }
    }

    $(".sy.sy1").click(function(){
    	var audio = $("audio")[0];
    	if(audio.paused){
    		audio.play();
    	}else{
    		audio.pause();
    	}
    });

    $(".zhua_zb .btnb").click(function(){
        var t = $(".zhua_zb .btnb");
        if($(t).attr("disabled")=="disabled"){return;}
    	var apiConnectDevice = "/Api/SiSi/connDeviceControl";
    	var apiConnectDeviceParams = {};
    	apiConnectDeviceParams.token = TOKEN;
    	apiConnectDeviceParams.deviceid = deviceid;
    	matchID = "";
        $(t).attr({
                    src : $(t).attr("src_of"),
                    disabled : true
                });
    	doPost(apiConnectDevice,apiConnectDeviceParams,function(data){
    		if(data.code == 200){
    			var status = data.status;
    			if(status == 0){
    				//有人占用
    				playerUid = data.data.uid;
    				playerAvatar = data.data.avatar;
    				playerUserName = data.data.user_nicename;
    				changeChannelStatus(3);
    			}
    			if(status == 1){
    				matchID = data.data.match_id;
    				playerUid = uid
    				playerAvatar = userAvatar;
    				playerUserName = userName;
    				balance = data.data.balance;

    				sendCaozuo(GAME_CONNECT);
    				$(".user-zt").show();
        			$(".user-zt .tx").attr("src",playerAvatar);
        			$(".user-zt span").text(playerUserName+"连接中");
        			
		    		$(t).attr({
		    			src : $(t).attr("src_of"),
		    			disabled : true
		    		});
    				//修改余额
    				$(".pd.fl").text("余额："+balance);
    			}
    		}else{
    			alert(data.descrp);
                $(t).attr({
                    src : $(t).attr("src_on"),
                    disabled : false
                });
    		}
    	});
    });

	var playerTimer ;
	var countPlayer = 30;
    function startCountTimer(){	
    	countPlayer = 30;
    	playerTimer = setInterval(function(){
    		countPlayer--;
    		$(".djs").text(countPlayer);
    		if(countPlayer <= 0){
    			sendCaozuo(GAME_GO);
    			clearInterval(playerTimer);
    		}
    	},1000); 

    }

	var playerTimerAgain ;
	var countPlayerAgain = 3;
    function startTimerTryAgain(){
    	countPlayerAgain = 3;
    	playerTimerAgain = setInterval(function(){
    		countPlayerAgain--;
    		if(countPlayerAgain <= 0){
    			clearInterval(playerTimerAgain);
    			changeChannelStatus(2);
    		}
    	},1000); 
    }

    function zhuazhu(data){
    	playerUid = "";
    	console.log(data);
    	//danmu;
    	showDanmu(data);
    	changeChannelStatus(2);
    	if(uid == data.playerUid){
    		//如果是自己
    		//显示效果
    	}

    }
    function showDanmu(data){
    	if(data.messageType == GAME_OK){
    		$("#danmu").text(data.playerUserName+"抓住了！");
    	}else{
    		$("#danmu").text(data.playerUserName+"没抓住...");
    	}
    	$("#danmu").css("left","-200px");
    	$("#danmu").show();
    	$("#danmu").animate({ 
			left :"400px"
		  }, 10000 );
    }
    function meizhuazhu(data){
    	playerUid = "";
    	console.log(data);
    	//danmu;
    	if(uid == data.playerUid){
    		//如果是自己
    		//显示效果
    		showDanmu(data);
    		startTimerTryAgain();
    	}else{
    		setTimeout(function(){
    			if(playerUid == ""){
					changeChannelStatus(2);
    			}else{
    				changeChannelStatus(3);
    			}
    		},1000);
    	}
    }

    $(".back.fl").click(function(){
        history.back();
    });
});

