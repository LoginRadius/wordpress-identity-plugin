<?php

namespace LRLivefyre;


use LRLivefyre\Core\Network;

class Livefyre { 
	public static function getNetwork($networkName, $networkKey) { 
		return Network::init($networkName, $networkKey);
	}
}
