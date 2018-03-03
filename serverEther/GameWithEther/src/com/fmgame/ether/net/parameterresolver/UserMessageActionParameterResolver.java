package com.fmgame.ether.net.parameterresolver;

import org.apache.mina.core.session.IoSession;

import com.fmgame.ether.main.user.User;
import com.fmgame.ether.main.user.UserCacheManagerService;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.IMessageActionParameterResolver;

/**
 * UserMessageAction参数解析助手
 */
public class UserMessageActionParameterResolver implements IMessageActionParameterResolver<User> {
	
	private final static UserMessageActionParameterResolver instance = new UserMessageActionParameterResolver();

	public static UserMessageActionParameterResolver getInstance() {
		return instance;
	}

	private UserMessageActionParameterResolver() {
	}

	@Override
	public User resolveParameter(IoSession session, AbstractTCPMessage message) {
		return UserCacheManagerService.getUserBySession(session);
	}

}
