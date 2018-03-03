package com.fmgame.ether.module.login;

import com.fmgame.ether.module.login.dao.IUserDAO;
import com.fmgame.ether.module.login.dao.UserDBImpl;
import com.fmgame.ether.module.login.message.LoginGameMessage;
import com.fmgame.ether.module.login.service.LoginMessageService;
import com.fmgame.platform.dao.GlobalDBOperation;
import com.fmgame.platform.module.ConfModule;
import com.fmgame.platform.module.IModuleEventContext;
import com.fmgame.platform.module.SimpleModule;
import com.fmgame.platform.service.GameServiceManager;
@ConfModule(name = "登录")
public class LoginModule extends SimpleModule {

	@Override
	public void registerDAO(IModuleEventContext context) {
		// TODO Auto-generated method stub
		GameServiceManager.getDAOManager().register(IUserDAO.class);
		GlobalDBOperation.register(UserDBImpl.class);
	}

	@Override
	public void registerMessageProcessors(IModuleEventContext context) {
		// TODO Auto-generated method stub
		GameServiceManager.getMessageManager().register(LoginGameMessage.class);
	}

	@Override
	public void registerServices(IModuleEventContext context) {
		// TODO Auto-generated method stub
		GameServiceManager.getComponentManager().register(LoginMessageService.class);
	}

}
