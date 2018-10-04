<?php
namespace Pbc\Model;

class AccountPbc
{
	public static function computeCompleteness($account)
	{
		$nbSkills = 0;
		if ($account->profile_tiny_2) $nbSkills += count(explode(',', $account->profile_tiny_2));
		if ($account->profile_tiny_3) $nbSkills += count(explode(',', $account->profile_tiny_3));
		if ($nbSkills > 2 && $account->profile_tiny_1 && $account->profile_tiny_4 && $account->profile_tiny_5) return '3_completed';
		if ($nbSkills > 2) return '2_intermediary';
		if ($nbSkills > 0) return '1_minimum';
		return null;
	}
}