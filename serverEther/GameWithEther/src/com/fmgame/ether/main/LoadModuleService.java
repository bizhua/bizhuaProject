package com.fmgame.ether.main;

import com.fmgame.ether.module.gpbirthtony.GPBirthTonyModule;
import com.fmgame.ether.module.login.LoginModule;
import com.fmgame.ether.net.NetModule;
import com.fmgame.platform.module.IModuleManager;
import com.fmgame.platform.service.GameServiceManager;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;

/**
 * 模块加载
 */
public class LoadModuleService implements IService {
	
	private static final LoadModuleService instance = new LoadModuleService();
	
	public static LoadModuleService getInstance() {
		return instance;
	}

	@Override
	public void onReady() throws IServiceException {
		IModuleManager manager = GameServiceManager.getModuleManager();
		// 引擎层模块
		loadEngineModule(manager);
	}
	/**
	 * 加载引擎层模块
	 * @param manager
	 */
	private static void loadEngineModule(IModuleManager manager) {
		/******************************** 基础服务模块 ***************************/

		// 网络模块
		manager.registerModule(new NetModule());
		manager.registerModule(new LoginModule());
		manager.registerModule(new GPBirthTonyModule());
	}

	@Override
	public void onStart() throws IServiceException {
		// 模块管理器
		IModuleManager manager = GameServiceManager.getModuleManager();
        //启动模块管理器
		manager.startup();
	}

	@Override
	public void onStop() throws IServiceException {
		// 模块管理器
		IModuleManager manager = GameServiceManager.getModuleManager();
		manager.shutdown();
	}

}
