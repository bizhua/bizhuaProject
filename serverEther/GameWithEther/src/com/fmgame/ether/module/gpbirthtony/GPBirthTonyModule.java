package com.fmgame.ether.module.gpbirthtony;


import com.fmgame.ether.module.gpbirthtony.dao.IBirthTonyDAO;
import com.fmgame.ether.module.gpbirthtony.message.BirthTonyMessage;
import com.fmgame.ether.module.gpbirthtony.service.GPBirthTonyService;
import com.fmgame.platform.module.ConfModule;
import com.fmgame.platform.module.IModuleEventContext;
import com.fmgame.platform.module.SimpleModule;
import com.fmgame.platform.service.GameServiceManager;

@ConfModule(name = "游戏玩法")
public class GPBirthTonyModule extends SimpleModule {

	@Override
	public void registerMessageProcessors(IModuleEventContext context) {
		// TODO Auto-generated method stub
		GameServiceManager.getMessageManager().register(BirthTonyMessage.class);
	}

	@Override
	public void registerDAO(IModuleEventContext context) {
		GameServiceManager.getDAOManager().register(IBirthTonyDAO.class);
	}

	@Override
	public void registerServices(IModuleEventContext context) {
		GameServiceManager.getComponentManager().register(GPBirthTonyService.class);
	}
}
