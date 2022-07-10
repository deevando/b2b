<?php
/**
 * Numbers_Words
 *
 * PHP version 4
 *
 * Copyright (c) 1997-2006 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available at through the world-wide-web at
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @category Numbers
 * @package  Numbers_Words
 * @author   Kouber Saparev <kouber@php.net>
 * @author   Philippe Bajoit <phil@lebutch.org
 * @license  PHP 3.0 http://www.php.net/license/3_0.txt
 * @version  SVN: $Id: lang.fr_BE.php 302816 2010-08-26 16:02:29Z ifeghali $
 * @link     http://pear.php.net/package/Numbers_Words
 */

/**
 * Include needed files
 */
require_once "Numbers/Words.php";

/**
 * Class for translating numbers into French (Belgium).
 *
 * @category Numbers
 * @package  Numbers_Words
 * @author   Kouber Saparev <kouber@php.net>
 * @author   Philippe Bajoit <phil@lebutch.org
 * @license  PHP 3.0 http://www.php.net/license/3_0.txt
 * @link     http://pear.php.net/package/Numbers_Words
 */
class Numbers_Words_fr_BE extends Numbers_Words
{

    // {{{ properties

    /**
     * Locale name.
     * @var string
     * @access public
     */
    var $locale = 'fr';

    /**
     * Language name in English.
     * @var string
     * @access public
     */
    var $lang = 'French';

    /**
     * Native language name.
     * @var string
     * @access public
     */
    var $lang_native = 'Français';

    /**
     * The words for some numbers.
     * @var string
     * @access private
     */
    var $_misc_numbers = array(
        10=>'dix',      // 10
            'onze',     // 11
            'douze',    // 12
            'treize',   // 13
            'quatorze', // 14
            'quinze',   // 15
            'seize',    // 16
        20=>'vingt',    // 20
        30=>'trente',   // 30
        40=>'quarante', // 40
        50=>'cinquante',// 50
        60=>'soixante', // 60
        70=>'septante', // 70
        90=>'nonante',  // 90
       100=>'cent'      // 100
    );


    /**
     * The words for digits (except zero).
     * @var string
     * @access private
     */
    var $_digits = array(1=>"un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf");

    /**
     * The word for zero.
     * @var string
     * @access private
     */
    var $_zero = 'zéro';

    /**
     * The word for infinity.
     * @var string
     * @access private
     */
    var $_infinity = 'infini';

    /**
     * The word for the "and" language construct.
     * @var string
     * @access private
     */
    var $_and = 'et';

    /**
     * The word separator.
     * @var string
     * @access private
     */
    var $_sep = ' ';

    /**
     * The dash liaison.
     * @var string
     * @access private
     */
    var $_dash = '-';

    /**
     * The word for the minus sign.
     * @var string
     * @access private
     */
    var $_minus = 'moins'; // minus sign

    /**
     * The plural suffix (except for hundred).
     * @var string
     * @access private
     */
    var $_plural = 's'; // plural suffix

    /**
     * The suffixes for exponents (singular).
     * @var array
     * @access private
     */
    var $_exponent = array(
        0 => '',
        3 => 'mille',
        6 => 'million',
        9 => 'milliard',
       12 => 'trillion',
       15 => 'quadrillion',
       18 => 'quintillion',
       21 => 'sextillion',
       24 => 'septillion',
       27 => 'octillion',
       30 => 'nonillion',
       33 => 'decillion',
       36 => 'undecillion',
       39 => 'duodecillion',
       42 => 'tredecillion',
       45 => 'quattuordecillion',
       48 => 'quindecillion',
       51 => 'sexdecillion',
       54 => 'septendecillion',
       57 => 'octodecillion',
       60 => 'novemdecillion',
       63 => 'vigintillion',
       66 => 'unvigintillion',
       69 => 'duovigintillion',
       72 => 'trevigintillion',
       75 => 'quattuorvigintillion',
       78 => 'quinvigintillion',
       81 => 'sexvigintillion',
       84 => 'septenvigintillion',
       87 => 'octovigintillion',
       90 => 'novemvigintillion',
       93 => 'trigintillion',
       96 => 'untrigintillion',
       99 => 'duotrigintillion',
      102 => 'trestrigintillion',
      105 => 'quattuortrigintillion',
      108 => 'quintrigintillion',
      111 => 'sextrigintillion',
      114 => 'septentrigintillion',
      117 => 'octotrigintillion',
      120 => 'novemtrigintillion',
      123 => 'quadragintillion',
      126 => 'unquadragintillion',
      129 => 'duoquadragintillion',
      132 => 'trequadragintillion',
      135 => 'quattuorquadragintillion',
      138 => 'quinquadragintillion',
      141 => 'sexquadragintillion',
      144 => 'septenquadragintillion',
      147 => 'octoquadragintillion',
      150 => 'novemquadragintillion',
      153 => 'quinquagintillion',
      156 => 'unquinquagintillion',
      159 => 'duoquinquagintillion',
      162 => 'trequinquagintillion',
      165 => 'quattuorquinquagintillion',
      168 => 'quinquinquagintillion',
      171 => 'sexquinquagintillion',
      174 => 'septenquinquagintillion',
      177 => 'octoquinquagintillion',
      180 => 'novemquinquagintillion',
      183 => 'sexagintillion',
      186 => 'unsexagintillion',
      189 => 'duosexagintillion',
      192 => 'tresexagintillion',
      195 => 'quattuorsexagintillion',
      198 => 'quinsexagintillion',
      201 => 'sexsexagintillion',
      204 => 'septensexagintillion',
      207 => 'octosexagintillion',
      210 => 'novemsexagintillion',
      213 => 'septuagintillion',
      216 => 'unseptuagintillion',
      219 => 'duoseptuagintillion',
      222 => 'treseptuagintillion',
      225 => 'quattuorseptuagintillion',
      228 => 'quinseptuagintillion',
      231 => 'sexseptuagintillion',
      234 => 'septenseptuagintillion',
      237 => 'octoseptuagintillion',
      240 => 'novemseptuagintillion',
      243 => 'octogintillion',
      246 => 'unoctogintillion',
      249 => 'duooctogintillion',
      252 => 'treoctogintillion',
      255 => 'quattuoroctogintillion',
      258 => 'quinoctogintillion',
      261 => 'sexoctogintillion',
      264 => 'septoctogintillion',
      267 => 'octooctogintillion',
      270 => 'novemoctogintillion',
      273 => 'nonagintillion',
      276 => 'unnonagintillion',
      279 => 'duononagintillion',
      282 => 'trenonagintillion',
      285 => 'quattuornonagintillion',
      288 => 'quinnonagintillion',
      291 => 'sexnonagintillion',
      294 => 'septennonagintillion',
      297 => 'octononagintillion',
      300 => 'novemnonagintillion',
      303 => 'centillion'
        );
    // }}}

    // {{{ _splitNumber()

    /**
     * Split a number to groups of three-digit numbers.
     *
     * @param mixed $num An integer or its string representation
     *                   that need to be split
     *
     * @return array  Groups of three-digit numbers.
     * @access private
     * @author Kouber Saparev <kouber@php.net>
     * @since  PHP 4.2.3
     */
    function _splitNumber($num)
    {
        if (is_string($num)) {
            $ret    = array();
            $strlen = strlen($num);
            $first  = substr($num, 0, $strlen%3);

            preg_match_all('/\d{3}/', substr($num, $strlen%3, $strlen), $m);
            $ret =& $m[0];

            if ($first) {
                array_unshift($ret, $first);
            }

            return $ret;
        }
        return explode(' ', number_format($num, 0, '', ' ')); // a faster version for integers
    }
    // }}}

    // {{{ _showDigitsGroup()

    /**
     * Converts a three-digit number to its word representation
     * in French language.
     *
     * @param integer $num  An integer between 1 and 999 inclusive.
     * @param boolean $last A flag, that determines if it is the last group of digits -
     *                      this is used to accord the plural suffix of the "hundreds".
     *                      Example: 200 = "deux cents", but 200000 = "deux cent mille".
     *
     * @return string   The words for the given number.
     * @access private
     * @author Kouber Saparev <kouber@php.net>
     */
    function _showDigitsGroup($num, $last = false)
    {
        $ret = '';

        // extract the value of each digit from the three-digit number
        $e = $num%10;                  // ones
        $d = ($num-$e)%100/10;         // tens
        $s = ($num-$d*10-$e)%1000/100; // hundreds

        // process the "hundreds" digit.
        if ($s) {
            if ($s>1) {
                $ret .= $this->_digits[$s].$this->_sep.$this->_misc_numbers[100];
                if ($last && !$e && !$d) {
                    $ret .= $this->_plural;
                }
            } else {
                $ret .= $this->_misc_numbers[100];
            }
            $ret .= $this->_sep;
        }

        // process the "tens" digit, and optionally the "ones" digit.
        if ($d) {
            // in the case of 1, the "ones" digit also must be processed
            if ($d==1) {
                if ($e<=6) {
                    $ret .= $this->_misc_numbers[10+$e];
                } else {
                    $ret .= $this->_misc_numbers[10].'-'.$this->_digits[$e];
                }
                $e = 0;
            } elseif ($d==8) {
                $ret .= $this->_digits[4].$this->_dash.$this->_misc_numbers[20];
                $resto = $d*10+$e-80;
                if ($resto) {
                    $ret .= $this->_dash;
                    $ret .= $this->_showDigitsGroup($resto);
                    $e = 0;
                } else {
                    $ret .= $this->_plural;
                }
            } else {
                $ret .= $this->_misc_numbers[$d*10];
            }
        }

        // process the "ones" digit
        if ($e) {
            if ($d) {
                if ($e==1) {
                    $ret .= $this->_sep.$this->_and.$this->_sep;
                } else {
                    $ret .= $this->_dash;
                }
            }
            $ret .= $this->_digits[$e];
        }

        // strip excessive separators
        $ret = rtrim($ret, $this->_sep);

        return $ret;
    }
    // }}}

    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in French language.
     *
     * @param integer $num An integer (or its string representation) between 9.99*-10^302
     *                        and 9.99*10^302 (999 centillions) that need to be converted to words
     *
     * @return string  The corresponding word representation
     * @access protected
     * @author Kouber Saparev <kouber@php.net>
     * @since  Numbers_Words 0.16.3
     */
    function _toWords($num = 0)
    {
        $ret = '';

        // check if $num is a valid non-zero number
        if (!$num || preg_match('/^-?0+$/', $num) || !preg_match('/^-?\d+$/', $num)) {
            return $this->_zero;
        }

        // add a minus sign
        if (substr($num, 0, 1) == '-') {
            $ret = $this->_minus . $this->_sep;
            $num = substr($num, 1);
        }

        // if the absolute value is greater than 9.99*10^302, return infinity
        if (strlen($num)>306) {
            return $ret . $this->_infinity;
        }

        // strip excessive zero signs
        $num = ltrim($num, '0');

        // split $num to groups of three-digit numbers
        $num_groups = $this->_splitNumber($num);

        $sizeof_numgroups = count($num_groups);

        foreach ($num_groups as $i=>$number) {
            // what is the corresponding exponent for the current group
            $pow = $sizeof_numgroups-$i;

            // skip processment for empty groups
            if ($number!='000') {
                if ($number!=1 || $pow!=2) {
                    $ret .= $this->_showDigitsGroup($number, $i+1==$sizeof_numgroups).$this->_sep;
                }
                $ret .= $this->_exponent[($pow-1)*3];
                if ($pow>2 && $number>1) {
                    $ret .= $this->_plural;
                }
                $ret .= $this->_sep;
            }
        }

        return rtrim($ret, $this->_sep);
    }
    // }}}


    // @CHANGE. Add the toCurrencyWords found in lang.en_US

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in English language
     *
     * @param integer $int_curr         An international currency symbol
     *                                  as defined by the ISO 4217 standard (three characters)
     * @param integer $decimal          A money total amount without fraction part (e.g. amount of dollars)
     * @param integer $fraction         Fractional part of the money amount (e.g. amount of cents)
     *                                  Optional. Defaults to false.
     * @param integer $convert_fraction Convert fraction to words (left as numeric if set to false).
     *                                  Optional. Defaults to true.
     *
     * @return string  The corresponding word representation for the currency
     *
     * @access public
     * @author Piotr Klaban <makler@man.torun.pl>
     * @since  Numbers_Words 0.4
     */
    function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
    {
        $int_curr = strtoupper($int_curr);
        if (!isset($this->_currency_names[$int_curr])) {
            $int_curr = $this->def_currency;
        }
        $curr_names = $this->_currency_names[$int_curr];

        $ret = trim($this->_toWords($decimal));
        $lev = ($decimal == 1) ? 0 : 1;
        if ($lev > 0) {
            if (count($curr_names[0]) > 1) {
                $ret .= $this->_sep . $curr_names[0][$lev];
            } else {
                $ret .= $this->_sep . $curr_names[0][0] . 's';
            }
        } else {
            $ret .= $this->_sep . $curr_names[0][0];
        }

        if ($fraction !== false) {
            if ($convert_fraction) {
                $ret .= $this->_sep . trim($this->_toWords($fraction));
            } else {
                $ret .= $this->_sep . $fraction;
            }
            $lev = ($fraction == 1) ? 0 : 1;
            if ($lev > 0) {
                if (count($curr_names[1]) > 1) {
                    $ret .= $this->_sep . $curr_names[1][$lev];
                } else {
                    $ret .= $this->_sep . $curr_names[1][0] . 's';
                }
            } else {
                $ret .= $this->_sep . $curr_names[1][0];
            }
        }
        return $ret;
    }
    // }}}

}
