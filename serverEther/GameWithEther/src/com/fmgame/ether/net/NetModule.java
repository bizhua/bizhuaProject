package com.fmgame.ether.net;

import com.fmgame.ether.net.service.NetService;
import com.fmgame.platform.module.ConfModule;
import com.fmgame.platform.module.IModuleEventContext;
import com.fmgame.platform.module.SimpleModule;
import com.fmgame.platform.net.current.INetService;
import com.fmgame.platform.service.GameServiceManager;

/**
 * 网络模块
 */
@ConfModule(name = "网络")
public class NetModule extends SimpleModule {

	@Override
	public void registerServices(IModuleEventContext context) {
		GameServiceManager.getComponentManager().register(INetService.class, NetService.class);
	}
	
}
