/* 
 * More info at: http://phpjs.org
 * 
 * This is version: 3.01
 * php.js is copyright 2009 Kevin van Zonneveld.
 * 
 * Portions copyright Brett Zamir (http://brett-zamir.me), Kevin van Zonneveld
 * (http://kevin.vanzonneveld.net), Onno Marsman, Michael White
 * (http://getsprink.com), Waldo Malqui Silva, Paulo Ricardo F. Santos,
 * Theriault, Jack, Jonas Raoni Soares Silva (http://www.jsfromhell.com),
 * Philip Peterson, Ates Goral (http://magnetiq.com), Legaev Andrey, Ratheous,
 * Alex, Martijn Wieringa, Nate, lmeyrick
 * (https://sourceforge.net/projects/bcmath-js/), Enrique Gonzalez, Philippe
 * Baumann, Webtoolkit.info (http://www.webtoolkit.info/), Ole Vrijenhoek, Ash
 * Searle (http://hexmen.com/blog/), Jani Hartikainen, Carlos R. L. Rodrigues
 * (http://www.jsfromhell.com), travc, WebDevHobo
 * (http://webdevhobo.blogspot.com/), stag019, GeekFG
 * (http://geekfg.blogspot.com),
 * http://stackoverflow.com/questions/57803/how-to-convert-decimal-to-hex-in-javascript,
 * T.Wild, Erkekjetter, pilus, d3x, Johnny Mast (http://www.phpvrouwen.nl),
 * Michael Grier, marrtins, Andrea Giammarchi
 * (http://webreflection.blogspot.com), Felix Geisendoerfer
 * (http://www.debuggable.com/felix), Martin (http://www.erlenwiese.de/), Marc
 * Palau, Michael White, Kankrelune (http://www.webfaktory.info/), Public
 * Domain (http://www.json.org/json2.js), majak, gettimeofday, Steven Levithan
 * (http://blog.stevenlevithan.com), Pellentesque Malesuada, Josh Fraser
 * (http://onlineaspect.com/2007/06/08/auto-detect-a-time-zone-with-javascript/),
 * Lars Fischer, Joris, Arpad Ray (mailto:arpad@php.net), Breaking Par
 * Consulting Inc
 * (http://www.breakingpar.com/bkp/home.nsf/0/87256B280015193F87256CFB006C45F7),
 * KELAN, Mirek Slugen, AJ, Alfonso Jimenez (http://www.alfonsojimenez.com),
 * Caio Ariede (http://caioariede.com), Mailfaker (http://www.weedem.fr/),
 * Tyler Akins (http://rumkin.com), Aman Gupta, Thunder.m, mdsjack
 * (http://www.mdsjack.bo.it), Oleg Eremeev, Sakimori, Karol Kowalski,
 * gorthaur, Steve Hilder, David, Francois, David James, Steve Clay,
 * class_exists, Marco, noname, madipta, sankai, Slawomir Kaniecki, Frank
 * Forte, Nathan, T. Wild, ger, nobbler, marc andreu, john
 * (http://www.jd-tech.net), Arno, ReverseSyntax, Scott Cariss, Mateusz
 * "loonquawl" Zalega, Douglas Crockford (http://javascript.crockford.com),
 * Denny Wardhana, mktime, Marc Jansen, Ole Vrijenhoek
 * (http://www.nervous.nl/), T0bsn, Gilbert, Peter-Paul Koch
 * (http://www.quirksmode.org/js/beat.html), MeEtc (http://yass.meetcweb.com),
 * Bryan Elliott, Tim Wiel, Brad Touesnard, Soren Hansen, duncan, djmix,
 * Lincoln Ramsay, Bayron Guevara, lmeyrick
 * (https://sourceforge.net/projects/bcmath-js/this.), Linuxworld, Pyerre, Jon
 * Hohle, Thiago Mata (http://thiagomata.blog.com), David Randall, Subhasis
 * Deb, J A R, 0m3r, Francesco, Paul, Hyam Singer
 * (http://www.impact-computing.com/), Raphael (Ao RUDLER), Sanjoy Roy,
 * kenneth, Stoyan Kyosev (http://www.svest.org/), LH, Ozh, nord_ua, date,
 * XoraX (http://www.xorax.info), echo is bad, JB, Eugene Bulkin
 * (http://doubleaw.com/), Der Simon (http://innerdom.sourceforge.net/),
 * Manish, Itsacon (http://www.itsacon.net/), Pierre-Luc Paour, Martin Pool,
 * Kirk Strobeck, Rick Waldron, Kristof Coomans (SCK-CEN Belgian Nucleair
 * Research Centre), Saulo Vallory, Wagner B. Soares, Valentina De Rosa, Jason
 * Wong (http://carrot.org/), Christoph, Daniel Esteban, Mick@el, rezna, Simon
 * Willison (http://simonwillison.net), Gabriel Paderni, Marco van Oort,
 * penutbutterjelly, Philipp Lenssen, Anton Ongson, Blues
 * (http://tech.bluesmoon.info/), Tomasz Wesolowski, Eric Nagel, Bobby Drake,
 * Luke Godfrey, Pul, Artur Tchernychev, uestla, Yves Sucaet, sowberry,
 * hitwork, Orlando, Norman "zEh" Fuchs, Ulrich, johnrembo, Nick Callen,
 * ejsanders, Aidan Lister (http://aidanlister.com/), Brian Tafoya
 * (http://www.premasolutions.com/), Philippe Jausions
 * (http://pear.php.net/user/jausions), kilops, dptr1988, HKM, metjay,
 * strcasecmp, strcmp, Alan C, Taras Bogach, ChaosNo1, Alexander Ermolaev
 * (http://snippets.dzone.com/user/AlexanderErmolaev), Le Torbi, James, Chris,
 * DxGx, Pedro Tainha (http://www.pedrotainha.com), Christian Doebler,
 * setcookie, Greg Frazier, Tod Gentille, Alexander M Beedie, T.J. Leahy,
 * baris ozdil, FGFEmperor, daniel airton wermann (http://wermann.com.br),
 * 3D-GRAF, jakes, gabriel paderni, Yannoo, FremyCompany, Luis Salazar
 * (http://www.freaky-media.com/), Matteo, stensi, Billy, Jalal Berrami, vlado
 * houba, Victor, fearphage (http://http/my.opera.com/fearphage/), Tim de
 * Koning, taith, Robin, Cord, Matt Bradley, Atli Þór, Maximusya, Andrej
 * Pavlovic, Dino, rem, mk.keck, Greenseed, Garagoth, Russell Walker
 * (http://www.nbill.co.uk/), YUI Library:
 * http://developer.yahoo.com/yui/docs/YAHOO.util.DateLocale.html, Blues at
 * http://hacks.bluesmoon.info/strftime/strftime.js, Andreas, Jamie Beck
 * (http://www.terabit.ca/), DtTvB
 * (http://dt.in.th/2008-09-16.string-length-in-bytes.html), Leslie Hoare, Ben
 * Bryan, Diogo Resende, Howard Yeend, Allan Jensen (http://www.winternet.no),
 * davook, Benjamin Lupton, Rival, Luke Smith (http://lucassmith.name),
 * booeyOH, Cagri Ekin, Amir Habibi (http://www.residence-mixte.com/), Kheang
 * Hok Chin (http://www.distantia.ca/), Jay Klehr
 * 
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL KEVIN VAN ZONNEVELD BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */ 


function get_html_translation_table (table, quote_style) {
    // Returns the internal translation table used by htmlspecialchars and htmlentities  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/get_html_translation_table
    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname
    // +   bugfixed by: Alex
    // +   bugfixed by: Marco
    // +   bugfixed by: madipta
    // +   improved by: KELAN
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Frank Forte
    // +   bugfixed by: T.Wild
    // +      input by: Ratheous
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js, meaning the constants are not
    // %          note: real constants, but strings instead. Integers are also supported if someone
    // %          note: chooses to create the constants themselves.
    // *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    
    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: "+useTable+' not supported');
        // return false;
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';


    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }
    
    return hash_map;
}

function html_entity_decode (string, quote_style) {
    // Convert all HTML entities to their applicable characters  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/html_entity_decode
    // +   original by: john (http://www.jd-tech.net)
    // +      input by: ger
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +   improved by: marc andreu
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // -    depends on: get_html_translation_table
    // *     example 1: html_entity_decode('Kevin &amp; van Zonneveld');
    // *     returns 1: 'Kevin & van Zonneveld'
    // *     example 2: html_entity_decode('&amp;lt;');
    // *     returns 2: '&lt;'
    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }

    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }
    tmp_str = tmp_str.split('&#039;').join("'");
    
    return tmp_str;
}

function i18n_loc_get_default () {
    // !No description available for i18n_loc_get_default. @php.js developers: Please update the function summary text file.
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/i18n_loc_get_default
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: Renamed in PHP6 from locale_get_default(). Not listed yet at php.net
    // %          note 2: List of locales at http://demo.icu-project.org/icu-bin/locexp
    // %          note 3: To be usable with sort() if it is passed the SORT_LOCALE_STRING sorting flag: http://php.net/manual/en/function.sort.php
    // -    depends on: i18n_loc_set_default
    // *     example 1: i18n_loc_get_default();
    // *     returns 1: 'en_US_POSIX'

    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    // END REDUNDANT
    return this.php_js.i18nLocale || (i18n_loc_set_default('en_US_POSIX'), 'en_US_POSIX'); // Ensure defaults are set up
}

function i18n_loc_set_default (name) {
    // !No description available for i18n_loc_set_default. @php.js developers: Please update the function summary text file.
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/i18n_loc_set_default
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: Renamed in PHP6 from locale_set_default(). Not listed yet at php.net
    // %          note 2: List of locales at http://demo.icu-project.org/icu-bin/locexp (use for implementing other locales here)
    // %          note 3: To be usable with sort() if it is passed the SORT_LOCALE_STRING sorting flag: http://php.net/manual/en/function.sort.php
    // *     example 1: i18n_loc_set_default('pt_PT');
    // *     returns 1: true

    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    // END REDUNDANT

    this.php_js.i18nLocales = {
        en_US_POSIX : {
            sorting :
                function ( str1, str2 ) { // Fix: This one taken from strcmp, but need for other locales; we don't use localeCompare since its locale is not settable
                    return ( str1 == str2 ) ? 0 : ( ( str1 > str2 ) ? 1 : -1 );
                }
        }
    };

    this.php_js.i18nLocale = name;
    return true;
}

function ini_set (varname, newvalue) {
    // Set a configuration option, returns false on error and the old value of the configuration option on success  
    // 
    // version: 911.2923
    // discuss at: http://phpjs.org/functions/ini_set
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: This will not set a global_value or access level for the ini item
    // *     example 1: ini_set('date.timezone', 'America/Chicago');
    // *     returns 1: 'Asia/Hong_Kong'

    var oldval = '', that = this;
    this.php_js = this.php_js || {};
    this.php_js.ini = this.php_js.ini || {};
    this.php_js.ini[varname] = this.php_js.ini[varname] || {};
    oldval = this.php_js.ini[varname].local_value;
    
    var _setArr = function (oldval) { // Although these are set individually, they are all accumulated
        if (typeof oldval === 'undefined') {
            that.php_js.ini[varname].local_value = [];
        }
        that.php_js.ini[varname].local_value.push(newvalue);
    };

    switch (varname) {
        case 'extension':
            if (typeof this.dl === 'function') {
                this.dl(newvalue); // This function is only experimental in php.js
            }
            _setArr(oldval, newvalue);
            break;
        default:
            this.php_js.ini[varname].local_value = newvalue;
            break;
    }
    return oldval;
}

function krsort (inputArr, sort_flags) {
    // http://kevin.vanzonneveld.net
    // +   original by: GeekFG (http://geekfg.blogspot.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: The examples are correct, this is a new way
    // %        note 2: This function deviates from PHP in returning a copy of the array instead
    // %        note 2: of acting by reference and returning true; this was necessary because
    // %        note 2: IE does not allow deleting and re-adding of properties without caching
    // %        note 2: of property position; you can set the ini of "phpjs.strictForIn" to true to
    // %        note 2: get the PHP behavior, but use this only if you are in an environment
    // %        note 2: such as Firefox extensions where for-in iteration order is fixed and true
    // %        note 2: property deletion is supported. Note that we intend to implement the PHP
    // %        note 2: behavior by default if IE ever does allow it; only gives shallow copy since
    // %        note 2: is by reference in PHP anyways
    // -    depends on: i18n_loc_get_default
    // *     example 1: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
    // *     example 1: data = krsort(data);
    // *     results 1: {d: 'lemon', c: 'apple', b: 'banana', a: 'orange'}
    // *     example 2: ini_set('phpjs.strictForIn', true);
    // *     example 2: data = {2: 'van', 3: 'Zonneveld', 1: 'Kevin'};
    // *     example 2: krsort(data);
    // *     results 2: data == {3: 'Kevin', 2: 'van', 1: 'Zonneveld'}
    // *     returns 2: true

    var tmp_arr={}, keys=[], sorter, i, k, that=this, strictForIn = false, populateArr = [];

    switch (sort_flags) {
        case 'SORT_STRING': // compare items as strings
            sorter = function (a, b) {
                return that.strnatcmp(b, a);
            };
            break;
        case 'SORT_LOCALE_STRING': // compare items as strings, based on the current locale (set with  i18n_loc_set_default() as of PHP6)
            var loc = this.i18n_loc_get_default();
            sorter = this.php_js.i18nLocales[loc].sorting;
            break;
        case 'SORT_NUMERIC': // compare items numerically
            sorter = function (a, b) {
                return (b - a);
            };
            break;
        case 'SORT_REGULAR': // compare items normally (don't change types)
        default:
            sorter = function (a, b) {
                if (a < b) {
                    return 1;
                }
                if (a > b) {
                    return -1;
                }
                return 0;
            };
            break;
    }

    // Make a list of key names
    for (k in inputArr) {
        if (inputArr.hasOwnProperty) {
            keys.push(k);
        }
    }
    keys.sort(sorter);

    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    this.php_js.ini = this.php_js.ini || {};
    // END REDUNDANT

    strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value;
    populateArr = strictForIn ? inputArr : populateArr;


    // Rebuild array with sorted key names
    for (i = 0; i < keys.length; i++) {
        k = keys[i];
        tmp_arr[k] = inputArr[k];
        if (strictForIn) {
            delete inputArr[k];
        }
    }
    for (i in tmp_arr) {
        if (tmp_arr.hasOwnProperty) {
            populateArr[i] = tmp_arr[i];
        }
    }

    return strictForIn ? true : populateArr;
}

function strtr (str, from, to) {
    // http://kevin.vanzonneveld.net
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // +      input by: uestla
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Alan C
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Taras Bogach
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: jpfle
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // -   depends on: krsort
    // -   depends on: ini_set
    // *     example 1: $trans = {'hello' : 'hi', 'hi' : 'hello'};
    // *     example 1: strtr('hi all, I said hello', $trans)
    // *     returns 1: 'hello all, I said hi'
    // *     example 2: strtr('äaabaåccasdeöoo', 'äåö','aao');
    // *     returns 2: 'aaabaaccasdeooo'
    // *     example 3: strtr('ääääääää', 'ä', 'a');
    // *     returns 3: 'aaaaaaaa'
    // *     example 4: strtr('http', 'pthxyz','xyzpth');
    // *     returns 4: 'zyyx'
    // *     example 5: strtr('zyyx', 'pthxyz','xyzpth');
    // *     returns 5: 'http'
    // *     example 6: strtr('aa', {'a':1,'aa':2});
    // *     returns 6: '2'

    var fr = '', i = 0, j = 0, lenStr = 0, lenFrom = 0, tmpStrictForIn = false, fromTypeStr = '', toTypeStr = '', istr = '';
    var tmpFrom = [];
    var tmpTo = [];
    var ret = '';
    var match = false;

    // Received replace_pairs?
    // Convert to normal from->to chars
    if (typeof from === 'object') {
        tmpStrictForIn = this.ini_set('phpjs.strictForIn', false); // Not thread-safe; temporarily set to true
        from = this.krsort(from);
        this.ini_set('phpjs.strictForIn', tmpStrictForIn);

        for (fr in from) {
            if (from.hasOwnProperty(fr)) {
                tmpFrom.push(fr);
                tmpTo.push(from[fr]);
            }
        }

        from = tmpFrom;
        to = tmpTo;
    }
    
    // Walk through subject and replace chars when needed
    lenStr  = str.length;
    lenFrom = from.length;
    fromTypeStr = typeof from === 'string';
    toTypeStr = typeof to === 'string';

    for (i = 0; i < lenStr; i++) {
        match = false;
        if (fromTypeStr) {
            istr = str.charAt(i);
            for (j = 0; j < lenFrom; j++) {
                if (istr == from.charAt(j)) {
                    match = true;
                    break;
                }
            }
        }
        else {
            for (j = 0; j < lenFrom; j++) {
                if (str.substr(i, from[j].length) == from[j]) {
                    match = true;
                    // Fast forward
                    i = (i + from[j].length)-1;
                    break;
                }
            }
        }
        if (match) {
            ret += toTypeStr ? to.charAt(j) : to[j];
        } else {
            ret += str.charAt(i);
        }
    }

    return ret;
}

function ucfirst (str) {
    // Makes a string's first character uppercase  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/ucfirst
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: ucfirst('kevin van zonneveld');
    // *     returns 1: 'Kevin van zonneveld'
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}
