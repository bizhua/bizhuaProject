package com.fmgame.ether.net.parameterresolver;

import org.apache.mina.core.session.IoSession;

import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.IMessageActionParameterResolver;

/**
 * SessionMessageAction的参数解析助手
 */
public class SessionMessageActionParameterResolver implements IMessageActionParameterResolver<IoSession> {

	private final static SessionMessageActionParameterResolver instance = new SessionMessageActionParameterResolver();

	public static SessionMessageActionParameterResolver getInstance() {
		return instance;
	}

	private SessionMessageActionParameterResolver() {
	}

	@Override
	public IoSession resolveParameter(IoSession session, AbstractTCPMessage message) {
		return session;
	}

}
