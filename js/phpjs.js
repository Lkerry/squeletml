/* 
 * More info at: http://phpjs.org
 * 
 * This is version: 2.94
 * php.js is copyright 2009 Kevin van Zonneveld.
 * 
 * Portions copyright Brett Zamir (http://brett-zamir.me), Kevin van Zonneveld
 * (http://kevin.vanzonneveld.net), Onno Marsman, Michael White
 * (http://getsprink.com), Waldo Malqui Silva, Paulo Ricardo F. Santos, Jack,
 * Jonas Raoni Soares Silva (http://www.jsfromhell.com), Philip Peterson, Ates
 * Goral (http://magnetiq.com), Legaev Andrey, Ratheous, Alex, Martijn
 * Wieringa, Nate, lmeyrick (https://sourceforge.net/projects/bcmath-js/),
 * Philippe Baumann, Enrique Gonzalez, Webtoolkit.info
 * (http://www.webtoolkit.info/), Theriault, Ash Searle
 * (http://hexmen.com/blog/), Jani Hartikainen, travc, Ole Vrijenhoek, Carlos
 * R. L. Rodrigues (http://www.jsfromhell.com), stag019, pilus,
 * http://stackoverflow.com/questions/57803/how-to-convert-decimal-to-hex-in-javascript,
 * Michael Grier, marrtins, d3x, Andrea Giammarchi
 * (http://webreflection.blogspot.com), GeekFG (http://geekfg.blogspot.com),
 * Erkekjetter, Johnny Mast (http://www.phpvrouwen.nl), T.Wild, majak, David,
 * Oleg Eremeev, mdsjack (http://www.mdsjack.bo.it), Breaking Par Consulting
 * Inc
 * (http://www.breakingpar.com/bkp/home.nsf/0/87256B280015193F87256CFB006C45F7),
 * Mirek Slugen, Martin (http://www.erlenwiese.de/), Public Domain
 * (http://www.json.org/json2.js), Joris, Steven Levithan
 * (http://blog.stevenlevithan.com), Steve Hilder, KELAN, Arpad Ray
 * (mailto:arpad@php.net), T.J. Leahy, Marc Palau, Josh Fraser
 * (http://onlineaspect.com/2007/06/08/auto-detect-a-time-zone-with-javascript/),
 * gettimeofday, AJ, Aman Gupta, Felix Geisendoerfer
 * (http://www.debuggable.com/felix), Sakimori, Lars Fischer, Caio Ariede
 * (http://caioariede.com), Alfonso Jimenez (http://www.alfonsojimenez.com),
 * Pellentesque Malesuada, Tyler Akins (http://rumkin.com), gorthaur,
 * Thunder.m, Karol Kowalski, Kankrelune (http://www.webfaktory.info/), Frank
 * Forte, Subhasis Deb, duncan, Gilbert, class_exists, noname, Marco, madipta,
 * 0m3r, David James, Arno, Nathan, Mateusz "loonquawl" Zalega, ReverseSyntax,
 * Scott Cariss, Slawomir Kaniecki, Denny Wardhana, nobbler, sankai, Sanjoy
 * Roy, Douglas Crockford (http://javascript.crockford.com), mktime, marc
 * andreu, ger, john (http://www.jd-tech.net), Ole Vrijenhoek
 * (http://www.nervous.nl/), Steve Clay, Thiago Mata
 * (http://thiagomata.blog.com), Jon Hohle, Linuxworld, lmeyrick
 * (https://sourceforge.net/projects/bcmath-js/this.), Ozh, nord_ua, Pyerre,
 * Soren Hansen, Peter-Paul Koch (http://www.quirksmode.org/js/beat.html),
 * T0bsn, MeEtc (http://yass.meetcweb.com), Brad Touesnard, David Randall,
 * Bryan Elliott, Tim Wiel, XoraX (http://www.xorax.info), djmix, Paul, J A R,
 * Hyam Singer (http://www.impact-computing.com/), kenneth, T. Wild, Raphael
 * (Ao RUDLER), Marc Jansen, Francesco, Lincoln Ramsay, echo is bad, Der Simon
 * (http://innerdom.sourceforge.net/), Eugene Bulkin (http://doubleaw.com/),
 * LH, JB, Bayron Guevara, Cord, Francois, Kristof Coomans (SCK-CEN Belgian
 * Nucleair Research Centre), Pierre-Luc Paour, Martin Pool, Kirk Strobeck,
 * Saulo Vallory, Christoph, Artur Tchernychev, Wagner B. Soares, Valentina De
 * Rosa, Daniel Esteban, Jason Wong (http://carrot.org/), Rick Waldron,
 * Mick@el, Anton Ongson, Simon Willison (http://simonwillison.net), Gabriel
 * Paderni, Marco van Oort, Blues (http://tech.bluesmoon.info/), Luke Godfrey,
 * rezna, Tomasz Wesolowski, Eric Nagel, Pul, Bobby Drake, uestla, Alan C,
 * Yves Sucaet, sowberry, hitwork, Norman "zEh" Fuchs, Ulrich, johnrembo, Nick
 * Callen, ejsanders, Aidan Lister (http://aidanlister.com/), Brian Tafoya
 * (http://www.premasolutions.com/), Philippe Jausions
 * (http://pear.php.net/user/jausions), Orlando, dptr1988, HKM, metjay,
 * strcasecmp, strcmp, Taras Bogach, ChaosNo1, Alexander Ermolaev
 * (http://snippets.dzone.com/user/AlexanderErmolaev), Le Torbi, James, Chris,
 * DxGx, Pedro Tainha (http://www.pedrotainha.com), Philipp Lenssen,
 * penutbutterjelly, Greg Frazier, Tod Gentille, Alexander M Beedie,
 * FremyCompany, baris ozdil, FGFEmperor, Atli Þór, 3D-GRAF, jakes, gabriel
 * paderni, Yannoo, Luis Salazar (http://www.freaky-media.com/), Tim de
 * Koning, stensi, vlado houba, Jalal Berrami, date, Matteo, Victor, taith,
 * Robin, Matt Bradley, fearphage (http://http/my.opera.com/fearphage/),
 * Manish, davook, Benjamin Lupton, Russell Walker (http://www.nbill.co.uk/),
 * Garagoth, Andrej Pavlovic, Dino, Jamie Beck (http://www.terabit.ca/), DtTvB
 * (http://dt.in.th/2008-09-16.string-length-in-bytes.html), Christian
 * Doebler, setcookie, YUI Library:
 * http://developer.yahoo.com/yui/docs/YAHOO.util.DateLocale.html, Andreas,
 * Blues at http://hacks.bluesmoon.info/strftime/strftime.js, Greenseed,
 * mk.keck, Luke Smith (http://lucassmith.name), Rival, Diogo Resende, Allan
 * Jensen (http://www.winternet.no), Howard Yeend, Kheang Hok Chin
 * (http://www.distantia.ca/), Jay Klehr, Leslie Hoare, Ben Bryan, booeyOH,
 * Amir Habibi (http://www.residence-mixte.com/), Cagri Ekin
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

