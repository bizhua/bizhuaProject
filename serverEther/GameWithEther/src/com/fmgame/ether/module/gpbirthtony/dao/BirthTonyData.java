package com.fmgame.ether.module.gpbirthtony.dao;

import com.fmgame.platform.dao.IDB;

public class BirthTonyData implements IDB{
	  private Long pid;

	    private String addressid;

	    private String gens;

	    private Short charact;

	    private Boolean repflag;

	    public Long getPid() {
	        return pid;
	    }

	    public void setPid(Long pid) {
	        this.pid = pid;
	    }

	    public String getAddressid() {
	        return addressid;
	    }

	    public void setAddressid(String addressid) {
	        this.addressid = addressid == null ? null : addressid.trim();
	    }

	    public String getGens() {
	        return gens;
	    }

	    public void setGens(String gens) {
	        this.gens = gens == null ? null : gens.trim();
	    }

	    public Short getCharact() {
	        return charact;
	    }

	    public void setCharact(Short charact) {
	        this.charact = charact;
	    }

	    public Boolean getRepflag() {
	        return repflag;
	    }

	    public void setRepflag(Boolean repflag) {
	        this.repflag = repflag;
	    }
}