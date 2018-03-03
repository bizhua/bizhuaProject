package com.fmgame.ether.module.login.dao;

import com.fmgame.platform.dao.IDB;

public class UserDBData implements IDB {

	private int userId;
	private String password;
	private short powerType;
	public int getUserId() {
		return userId;
	}
	public void setUserId(int userId) {
		this.userId = userId;
	}
	public String getPassword() {
		return password;
	}
	public void setPassword(String password) {
		this.password = password;
	}
	public short getPowerType() {
		return powerType;
	}
	public void setPowerType(short powerType) {
		this.powerType = powerType;
	}
	
}
