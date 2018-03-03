package com.fmgame.etherwork;

import java.io.IOException;
import java.math.BigInteger;
import java.util.HashMap;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.web3j.crypto.CipherException;
import org.web3j.crypto.Credentials;
import org.web3j.crypto.WalletUtils;
import org.web3j.protocol.core.DefaultBlockParameterName;
import org.web3j.protocol.core.methods.response.EthGetCode;
import org.web3j.utils.Numeric;

import com.fmgame.etherwork.contract.CoinPawsGame;
import com.fmgame.etherwork.contract.HelloWorld;

public class ContractManager {

	private static final Logger logger = LoggerFactory
			.getLogger(ContractManager.class.getName());

	private static ContractManager _manager;

	public static ContractManager getInstance() {
		if (_manager == null) {
			_manager = new ContractManager();
		}
		return _manager;
	}

	// /robot 账户的合约
	private CoinPawsGame _robotContract;

	public CoinPawsGame getRobotContract() {
		return _robotContract;
	}

	public void init(String robotPass, String robotKeyPath,
			String contractAddress, long gasPrice, long gasLimit) {
		try {
			Credentials credentials = WalletUtils.loadCredentials(robotPass,
					robotKeyPath);

			logger.error("getCredentialsAddress : " + credentials.getAddress());

			// 加载合约
			_robotContract = CoinPawsGame.load(contractAddress,
					Web3JClient.getClient(), credentials,
					BigInteger.valueOf(gasPrice), BigInteger.valueOf(gasLimit));
			// _robotContract = HelloWorld.load(contractAddress, web3j,
			// credentials,
			// BigInteger.valueOf(20_000_000_000L),
			// BigInteger.valueOf(4_300_000L));
			logger.error("getContractAddress : "
					+ _robotContract.getContractAddress());

			logger.error("contract is valid : " + _robotContract.isValid());

			EthGetCode ethGetCode = Web3JClient
					.getClient()
					.ethGetCode(contractAddress,
							DefaultBlockParameterName.LATEST).send();
			if (ethGetCode.hasError()) {
				logger.error("ethGetCode.hasError() : " + ethGetCode.hasError());
			}

			String code = Numeric.cleanHexPrefix(ethGetCode.getCode());
			logger.error("contract Code is: " + code);
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (CipherException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}
