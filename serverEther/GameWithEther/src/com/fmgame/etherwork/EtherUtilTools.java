package com.fmgame.etherwork;

import java.math.BigInteger;

import org.web3j.abi.datatypes.generated.Uint256;

public class EtherUtilTools {

	public static int POS_NUM = 14;
	//每个部位 18位 bit 最大值不能超过 262143
	public static final int MAX_POS_INT = 262143;
	
	public static BigInteger base = new BigInteger("262144");
	
	public static BigInteger baseBybit = new BigInteger("18");
	
	public static BigInteger baseBit18 = new BigInteger("262143");
	
	public static Uint256 getGensUint256(int[] gen)
	{	
		BigInteger num256 = BigInteger.ZERO;
		BigInteger temp;
		for(int i = 0 ;i < gen.length;i++)
		{
			temp = new BigInteger(String.valueOf(gen[i]));
			num256.add(base.pow(i).multiply(temp));
		}
		Uint256 gens = new Uint256(num256);
		return gens;
	}
	
	public static int[] getGensint(Uint256 gen)
	{
		int[] nums = new int[POS_NUM];
		BigInteger genB = gen.getValue();
		BigInteger temp;
		for(int i = POS_NUM-1;i > 0; i--)
		{
			temp = genB.divide(base.pow(i));
			nums[i] = temp.intValue();
			genB.subtract(temp.multiply(base.pow(i)));
		}		
		return nums;
	}
	
	public static Uint256 getGensUint256Fast(int[] gen)
	{
		BigInteger num256 = BigInteger.ZERO;
		for(int i = 0 ;i < gen.length;i++)
		{
			num256 = num256.or(new BigInteger(String.valueOf(gen[i])).shiftLeft(i*18));
		}
		Uint256 gens = new Uint256(num256);
		return gens;
	}
	
	public static int[] getGensintFast(Uint256 gen)
	{
		int[] nums = new int[POS_NUM];
		BigInteger temp;
		BigInteger genB = gen.getValue();
		for(int i = 0 ;i < POS_NUM;i++)
		{
			temp = genB.shiftRight(i*18).and(baseBit18);
			nums[i] = temp.intValue();
		}
		return nums;
	}
	/**
	 * 检测部位数据是否合法
	 * @param gen
	 * @return
	 */
	public static boolean testPosListIsValidate(int[] gen)
	{
		boolean flag = true;
		for(int pos : gen)
		{
			if(pos > MAX_POS_INT)
			{
				return false;
			}
		}
		return flag;
	}
	
	public static BigInteger getGensUint256BigInteger(int[] gen)
	{	
		BigInteger num256 = BigInteger.ZERO;
		BigInteger temp;
		for(int i = 0 ;i < gen.length;i++)
		{
			temp = new BigInteger(String.valueOf(gen[i]));
			num256.add(base.pow(i).multiply(temp));
		}
		return num256;
	}
	
	public static int[] getGensint(BigInteger gen)
	{
		int[] nums = new int[POS_NUM];
		BigInteger temp;
		for(int i = POS_NUM-1;i > 0; i--)
		{
			temp = gen.divide(base.pow(i));
			nums[i] = temp.intValue();
			gen.subtract(temp.multiply(base.pow(i)));
		}		
		return nums;
	}
	
	public static BigInteger getGensUint256BigIntegerFast(int[] gen)
	{
		BigInteger num256 = BigInteger.ZERO;
		for(int i = 0 ;i < gen.length;i++)
		{
			num256 = num256.or(new BigInteger(String.valueOf(gen[i])).shiftLeft(i*18));
		}
		return num256;
	}
	
	public static int[] getGensintFast(BigInteger gen)
	{
		int[] nums = new int[POS_NUM];
		BigInteger temp;
		for(int i = 0 ;i < POS_NUM;i++)
		{
			temp = gen.shiftRight(i*18).and(baseBit18);
			nums[i] = temp.intValue();
		}
		return nums;
	}
}
