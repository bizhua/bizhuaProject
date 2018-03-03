package com.fmgame.ether.main;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;

/**
 * 停服服务
 */
public class StopServerService implements IService {
	
	private static final StopServerService instance = new StopServerService();
	
	private StopServerService() {}

	public static StopServerService getInstance() {
		return instance;
	}
	
	@Override
	public void onReady() throws IServiceException {
	}

	@Override
	public void onStart() throws IServiceException {
		
	}

	@Override
	public void onStop() throws IServiceException {
		
	}

}
