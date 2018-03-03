package com.fmgame.ether.module.login.dao;

import com.fmgame.platform.dao.ConfDBOperation;
import com.fmgame.platform.dao.GlobalDBOperationImplAdapter;

@ConfDBOperation(daoClass = IUserDAO.class, dataClass = UserDBData.class)
public class UserDBImpl extends GlobalDBOperationImplAdapter<UserDBData> {

}