package com.fmgame.ether.module.gpbirthtony.dao;

import com.fmgame.platform.dao.IDAO;

public interface IBirthTonyDAO extends IDAO {

    int insert(BirthTonyData record);

    BirthTonyData selectByPrimaryKey(Long pid);

    int updateByPrimaryKey(BirthTonyData record);
}
