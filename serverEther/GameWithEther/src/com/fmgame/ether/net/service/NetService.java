package com.fmgame.ether.net.service;
import com.fmgame.ether.net.messageaction.ISessionMessageAction;
import com.fmgame.ether.net.parameterresolver.SessionMessageActionParameterResolver;
import com.fmgame.ether.net.parameterresolver.UserMessageActionParameterResolver;
import com.fmgame.platform.component.ComponentServiceAdapter;
import com.fmgame.platform.net.current.IMessageAction;
import com.fmgame.platform.net.current.IMessageActionParameterResolver;
import com.fmgame.platform.net.current.INetService;

public class NetService extends ComponentServiceAdapter implements INetService {


	@SuppressWarnings("rawtypes")
	@Override
	public IMessageActionParameterResolver getParameterResolver(IMessageAction action) {
		if (action instanceof ISessionMessageAction)
			return SessionMessageActionParameterResolver.getInstance();
		else
			return UserMessageActionParameterResolver.getInstance();
	}

	@SuppressWarnings("rawtypes")
	@Override
	public int getParaType(IMessageAction action) {
		// TODO Auto-generated method stub
		if (action instanceof ISessionMessageAction)
			return 2;
		else
			return 3;
	}

}
