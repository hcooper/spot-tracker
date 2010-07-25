<?php

// Functions related to data caching with memcached

// Connect to memcached
class clsMem extends Memcache {
	static private $m_objMem = NULL;
	static function getMem() {
		if (self::$m_objMem == NULL) {
			self::$m_objMem = new Memcache;
			self::$m_objMem->connect('localhost', '11211') 
                        or die (showniceerror("We failed to contact the memcache server! Please check the configuration."));
		}
		return self::$m_objMem;
	}
}


// Returned cached data, or cache non-cached data
function cachedSQL($sSQL)  {
  if($objResultset = clsMem::getMem()->get(MD5($sSQL)))  {
    // return the cached resultset
    return $objResultSet;	
    }
  else  {
    $objResultSet = mysql_query($sSQL);
    // Store the resultset in the cache (30 mins with zlib compression)
    clsMem::getMem()->set(MD5($sSQL), $objResultSet, true, 1800);
    // return it 
    return $objResultSet;
    }
}
?>