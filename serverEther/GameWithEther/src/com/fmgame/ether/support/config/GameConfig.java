package com.fmgame.ether.support.config;

import java.sql.Timestamp;

import org.jdom2.Element;

import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.xml.XmlUtils;

/**
 * 配置
 */
public class GameConfig {
	
    /**
     * 服务器配置信息文件
     */
    private static final String SERVER_CONFIG_FILE = "game_config.xml";

	// -------------------------------------------------------- 公共参数


	/**
	 * 服务器TCP端口
	 */
	public static int SERVER_PORT = 2888;


	/**
	 * 服务器开服时间
	 */
	public static Timestamp GAMEAREA_OPEN_TIME = null;
	
    /**
     * 初始化系统相关的配置信息
     */
    public static void initConfig() {
		try {
			Element root = XmlUtils.getElement(SERVER_CONFIG_FILE);

	        GameConfig.SERVER_PORT = Integer.valueOf(root.getChildText("server_port"));

		} catch (Exception e) {
			ExceptionLogger.printLogger("初始化服务器相关配置参数失败，错误信息：", e);
		}
    }

	@Override
	public String toString() {
		StringBuilder builder = new StringBuilder();
		builder.append("[");
		builder.append("SERVER_PORT=");
		builder.append(SERVER_PORT);
		return builder.toString();
	}

}
