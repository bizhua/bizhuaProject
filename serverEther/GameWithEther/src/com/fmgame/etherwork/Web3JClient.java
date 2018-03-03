package com.fmgame.etherwork;

import org.web3j.protocol.Web3j;
import org.web3j.protocol.admin.Admin;
import org.web3j.protocol.http.HttpService;

public class Web3JClient {

    private static String ip = "http://localhost:8545/";

    private Web3JClient(){}

    private volatile static Web3j web3j;
    private volatile static Admin web3jAdmin;
    public static Web3j getClient(){
        if(web3j==null){
            synchronized (Web3JClient.class){
                if(web3j==null){
                    web3j = Web3j.build(new HttpService(ip));
                }
            }
        }
        return web3j;
    }
    
    public static Admin getClientAdmin(){
        if(web3jAdmin==null){
            synchronized (Web3JClient.class){
                if(web3jAdmin==null){
                	web3jAdmin = Admin.build(new HttpService(ip));
                }
            }
        }
        return web3jAdmin;
    }
}
