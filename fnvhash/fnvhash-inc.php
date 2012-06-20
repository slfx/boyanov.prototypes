<?php

/**
 *	FNV Hash
 *
 *  Author: Neven Boyanov
 *  Copyright (c) 2009 by Neven Boyanov (Boyanov.Org)
 *  Licensed under GNU/GPLv2 - http://www.gnu.org/licenses/
 *
 *  This program is distributed under the terms of the License,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty
 *  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 *  the License for more details.
 *
 **/

	/*
	*	Constants
	*
	*	FNV_PRIME:
	*	32 bit FNV_prime = 2^24 + 2^8 + 0x93 = 16777619	... 1000000000000000110010011
	*	64 bit FNV_prime = 2^40 + 2^8 + 0xb3 = 1099511628211	... 10000000000000000000000000000000110110011
	*	128 bit FNV_prime = 2^88 + 2^8 + 0x3b = 309485009821345068724781371	...
	*	OFFSET_BASIS:
	*	32 bit offset_basis = 2166136261
	*	64 bit offset_basis = 14695981039346656037
	*	128 bit offset_basis = 144066263297769815596495629667062367629	
	*
	*	Source: http://www.isthe.com/chongo/tech/comp/fnv/
	*/

define ("FNV_prime_32", 16777619);
define ("FNV_prime_64", 1099511628211);
define ("FNV_prime_128", 309485009821345068724781371);

define ("FNV_offset_basis_32", 2166136261);
define ("FNV_offset_basis_64", 14695981039346656037);
define ("FNV_offset_basis_128", 144066263297769815596495629667062367629);

	/*
	*	The core of the FNV-1 hash algorithm is as follows:
	*
	*	    hash = offset_basis
	*	    for each octet_of_data to be hashed
	*	    	hash = hash * FNV_prime
	*	    	hash = hash xor octet_of_data
	*	    return hash
	*
	*	Source: http://www.isthe.com/chongo/tech/comp/fnv/
	*/

	/*
	*	Example Java implementation:
	*
	*	long fnv(byte[] buf, int offset, int len, long seed)
	*	{
	*		for (int i = offset; i < offset + len; i++)
	*		{
	*			seed += (seed << 1) + (seed << 4) + (seed << 7) + (seed << 8) + (seed << 24);
	*			seed ^= buf[i];
	*		}
	*		return seed;
	*	}
	*
	*	`Source: http://www.getopt.org/ - FNV1 Hash
	*/

function fnvhash_fnv1($txt)
{
	$buf = str_split($txt);
	$hash = FNV_offset_basis_32;
	foreach ($buf as $chr)
	{
		$hash += ($hash << 1) + ($hash << 4) + ($hash << 7) + ($hash << 8) + ($hash << 24);
		$hash = $hash ^ ord($chr);
	}
	$hash = $hash & 0x0ffffffff;
	return $hash;
}

?>
