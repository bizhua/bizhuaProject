package com.fmgame.ether.module.login.message;

import java.net.InetSocketAddress;

import org.apache.mina.core.session.IoSession;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.fmgame.ether.main.user.User;
import com.fmgame.ether.main.user.UserCacheManagerService;
import com.fmgame.ether.module.login.service.LoginMessageService;
import com.fmgame.ether.net.messageaction.ISessionMessageAction;
import com.fmgame.ether.util.SessionKey;
import com.fmgame.platform.net.current.MessageProcessException;
import com.fmgame.platform.service.GameServiceManager;

public class LoginGameMessageAction implements ISessionMessageAction<LoginGameMessage> {
	private static final Logger logger = LoggerFactory.getLogger(LoginGameMessageAction.class);
	@Override
	public void processMessage(LoginGameMessage message, IoSession session)
			throws MessageProcessException {
		// TODO Auto-generated method stub
		  LoginMessageService service = GameServiceManager.getComponentManager().getService(LoginMessageService.class);
		  if(service.validate(message.userId, message.pass))
		   {
	           // 设置session到map中
	           InetSocketAddress add = (InetSocketAddress) session.getRemoteAddress();
	           String ip = add.getAddress().getHostAddress();
	           session.setAttribute(SessionKey.CUR_IP, ip);
	           UserCacheManagerService.putUserSessionMap(message.userId, session);
		   		// 创建用户
		   		User user = new User(message.userId);
		   		// 用户加入游戏
		   		user = UserCacheManagerService.userJoin(session, user);
		   		if(user != null)
		   		{
		   			user.activeFlag = true;
		   			message.setReturnFlag(true);
		   			session.write(message.encodeIoBuffer());
		   		}
	           // 登录成功打印
	           if (logger.isInfoEnabled())
	           	logger.info("【login】用户" + message.userId + "加入");
		   }else
		   {
	  			message.setReturnFlag(false);
	  			session.write(message.encodeIoBuffer());
		   }
	}

}
