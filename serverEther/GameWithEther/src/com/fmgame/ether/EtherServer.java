package com.fmgame.ether;

import java.util.Date;

import org.apache.mina.core.session.IoSession;

import com.fmgame.ether.main.ServiceTemplate;
import com.fmgame.ether.main.user.User;
import com.fmgame.ether.main.user.UserCacheManagerService;
import com.fmgame.ether.module.gpbirthtony.dao.BirthTonyData;
import com.fmgame.ether.module.gpbirthtony.dao.IBirthTonyDAO;
import com.fmgame.platform.service.GameServiceManager;
import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.GameLog;

public class EtherServer {
	public static final EtherServer instance = new EtherServer();

	/**
	 * 服务器启动时的时间戳
	 */
	public final long timestamp = System.currentTimeMillis();

	private EtherServer() {

	}

	public static EtherServer getInstance() {
		return instance;
	}

	public boolean initialize() {
		try {
			// RoleDataProvider.getInstance().clearAllRoleIsOnline();
			GameLog.INIT.info("清除所有在线状态 ok");

			return true;
		} catch (Exception e) {
			ExceptionLogger.printLogger("初始化游戏服务报错：", e);
		}
		return false;
	}

	public boolean startService() {
		try {// 准备游戏数据
			GameLog.INIT.info("============当前版本启动时间：" + new Date() + "============");
			// 只有在服务器是关闭的时候才可以启动服务器
			// 准备起服的动作
			ServiceTemplate.getInstance().onReady();
		} catch (Exception e) {
			ExceptionLogger.printLogger("服务初始失败!", e);
			return false;
		}

		try {
			ServiceTemplate.getInstance().onStart();
		} catch (Exception e) {
			ExceptionLogger.printLogger("启动服务器失败了：", e);
			return false;
		}
		GameLog.INIT.info("游戏服务启动成功");
		return true;
	}


	/**
	 * 关闭游戏服务器
	 */
	public boolean stopService() {
		ServiceTemplate.getInstance().onStop();

		System.gc();

		return true;
	}

	public static void main(String[] args) {
		EtherServer.getInstance().startService();
	}

	/**
	 * 响应会话关闭事件
	 * @param session
	 */
	public static final void onSessionClosed(IoSession session) {
		// TODO 当一个会话上绑定多个用户时,对应的用户列表全部离线处理
		// GameLog.COMMON.info("GameServer.onSessionClosed()");
		boolean quitType = (Boolean) session.getAttribute("QUIT_TYPE", false);
		// GameLog.COMMON.info("GameServer.onSessionClosed():"+quitType);

		User user = UserCacheManagerService.getUserBySession(session);
		if (user != null)
			UserCacheManagerService.userLeave(user, quitType);

		// GameLog.COMMON.info("onSessionClosed(IoSession) - session=" + session + ",
		// user=" + user + ", quitType=" + quitType); //$NON-NLS-1$
	}
}
