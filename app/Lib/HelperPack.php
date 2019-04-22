<?php

namespace App\Lib;

/**
 * Miscellaneous helper functions
 * Should contain only methods which are independent of a single project.
 */
class HelperPack
{
    
    /**
     * @param string $var      | variable to test
     * @param string $varName  | name of the variable for exception string
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    private static function throwErrorIfNotString($var, $varName)
    {
        if (! is_string($var)) {
            throw new \InvalidArgumentException("String is required as \$$varName parameter");
        }
    }
    
    
    /**
     * Validator method for generateEmailStr() method arguments.
     *
     * @params same as in generateEmailStr()
     *
     * @param mixed $domain
     * @param mixed $sep
     * @param mixed $userData
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    private static function validateGenerateEmailStrMethodArgs($domain, $sep, $userData)
    {
        self::throwErrorIfNotString($domain, 'domain');
        self::throwErrorIfNotString($sep, 'domain');
        foreach ($userData as $key => $val) {
            self::throwErrorIfNotString($val, '$userData variable arguments ' . $key);
        }
    }
    
    
    /**
     * Generate email string from domain, separator and user data.
     *
     * @param string $domain  | email domain (e.g. example.com)
     * @param string $sep     | user data separator on 'implode'
     * @param Array $userData | user data in varible number of arguments
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function generateEmailStr($domain, $sep, ...$userData)
    {
        // allow array to be used instead of variable func arguments
        if (count($userData) == 1 && is_array($userData[0])) {
            $userData = $userData[0];
        }
        
        // validate input arguments
        self::validateGenerateEmailStrMethodArgs($domain, $sep, $userData);
        
        $preparedUserData = array_map('strtolower', $userData);
        $preDomain = implode($sep, $preparedUserData);
        
        return sprintf('%s@%s', $preDomain, $domain);
    }
}
