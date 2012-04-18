<?php
$title = 'Glossary of Common Terms';
$hDescription = 'This glossary explains central terms of the Unicode standard and character encodings in general.';
$nav = array(
  'find' => '<a href="'.$router->getUrl('about').'#find">Finding Characters</a>',
  'unicode' => '<a href="'.$router->getUrl('about').'#unicode">About Unicode</a>',
  'main' => '<a href="'.$router->getUrl('about').'#main">About this site</a>',
  'glossary' => '<em class="active">Glossary of Common Terms</em>',
);
include 'header.php';
include 'nav.php';
?>
<div class="payload static glossary">
  <h1><?php e($title)?></h1>
  <dl>
    <dt id="kAccountingNumeric">kAccountingNumeric</dt>
    <dd>The value of the character when used in the writing of accounting numerals.<br/><br/>
    Accounting numerals are used in East Asia to prevent fraud. Because a number like ten (十) is easily turned into one thousand (千) with a stroke of a brush, monetary documents will often use an accounting form of the numeral ten (such as 拾) in their place.<br/><br/>
    The three numeric-value fields should have no overlap; that is, characters with a kAccountingNumeric value should not have a kPrimaryNumeric or kOtherNumeric value as well.<br/></dd>
    <dt id="kBigFive">kBigFive</dt>
    <dd>The Big Five mapping for this character in hex; note that this does not cover any of the Big Five extensions in common use, including the ETEN extensions.</dd>
    <dt id="kCangjie">kCangjie</dt>
    <dd>The cangjie input code for the character. This incorporates data from the file cangjie-table.b5 by Christian Wittern.</dd>
    <dt id="kCantonese">kCantonese</dt>
    <dd>The Cantonese pronunciation(s) for this character using the jyutping romanization.<br/><br/>
    A full description of jyutping can be found at &lt;http://www.lshk.org/cantonese.php&gt;. The main differences between jyutping and the Yale romanization previously used are:<br/><br/>
    1) Jyutping always uses tone numbers and does not distinguish the high falling and high level tones.<br/>
    2) Jyutping always writes a long a as “aa”.<br/>
    3) Jyutping uses “oe” and “eo” for the Yale “eu” vowel.<br/>
    4) Jyutping uses “c” instead of “ch”, “z” instead of “j”, and “j” instead of “y” as initials.<br/>
    5) A non-null initial is always explicitly written (thus “jyut” in jyutping instead of Yale’s “yut”).<br/><br/>
    Cantonese pronunciations are sorted alphabetically, not in order of frequency.</dd>
    <dt id="kCCCII">kCCCII</dt>
    <dd>The CCCII mapping for this character in hex.</dd>
    <dt id="kCheungBauer">kCheungBauer</dt>
    <dd>Data regarding the character in Cheung Kwan-hin and Robert S. Bauer, _The Representation of Cantonese with Chinese Characters_, Journal of Chinese Linguistics, Monograph Series Number 18, 2002. Each data value consists of three pieces, separated by semicolons: (1) the character’s radical-stroke index as a three-digit radical, slash, two-digit stroke count; (2) the character’s cangjie input code (if any); and (3) a comma-separated list of Cantonese readings using the jyutping romanization in alphabetical order.</dd>
    <dt id="kCheungBauerIndex">kCheungBauerIndex</dt>
    <dd>The position of the character in Cheung Kwan-hin and Robert S. Bauer, _The Representation of Cantonese with Chinese Characters_, Journal of Chinese Linguistics, Monograph Series Number 18, 2002. The format is a three-digit page number followed by a two-digit position number, separated by a period.</dd>
    <dt id="kCihaiT">kCihaiT</dt>
    <dd>The position of this character in the Cihai (辭海) dictionary, single volume edition, published in Hong Kong by the Zhonghua Bookstore, 1983 (reprint of the 1947 edition), ISBN 962-231-005-2.<br/><br/>
    The position is indicated by a decimal number. The digits to the left of the decimal are the page number. The first digit after the decimal is the row on the page, and the remaining two digits after the decimal are the position on the row.</dd>
    <dt id="kCNS1986">kCNS1986</dt>
    <dd>The CNS 11643-1986 mapping for this character in hex.</dd>
    <dt id="kCNS1992">kCNS1992</dt>
    <dd>The CNS 11643-1992 mapping for this character in hex.</dd>
    <dt id="kCompatibilityVariant">kCompatibilityVariant</dt>
    <dd>The compatibility decomposition for this ideograph, derived from the UnicodeData.txt file.</dd>
    <dt id="kCowles">kCowles</dt>
    <dd>The index or indices of this character in Roy T. Cowles, A Pocket Dictionary of Cantonese, Hong Kong: University Press, 1999.<br/><br/>
    Approximately 100 characters from Cowles which are not currently encoded are being submitted to the IRG by Unicode for inclusion in future versions of the standard.</dd>
    <dt id="kDaeJaweon">kDaeJaweon</dt>
    <dd>The position of this character in the Dae Jaweon (Korean) dictionary used in the four-dictionary sorting algorithm. The position is in the form “page.position” with the final digit in the position being “0” for characters actually in the dictionary and “1” for characters not found in the dictionary and assigned a “virtual” position in the dictionary.<br/><br/>
    Thus, “1187.060” indicates the sixth character on page 1187. A character not in this dictionary but assigned a position between the 6th and 7th characters on page 1187 for sorting purposes would have the code “1187.061”<br/><br/>
    The edition used is the first edition, published in Seoul by Samseong Publishing Co., Ltd., 1988.</dd>
    <dt id="kDefinition">kDefinition</dt>
    <dd>An English definition for this character. Definitions are for modern written Chinese and are usually (but not always) the same as the definition in other Chinese dialects or non-Chinese languages. In some cases, synonyms are indicated. Fuller variant information can be found using the various variant fields.<br/><br/>
    Definitions specific to non-Chinese languages or Chinese dialects other than modern Mandarin are marked, e.g., (Cant.) or (J).<br/><br/>
    Major definitions are separated by semicolons, and minor definitions by commas. Any valid Unicode character (except for tab, double-quote, and any line break character) may be used within the definition field.</dd>
    <dt id="kEACC">kEACC</dt>
    <dd>The EACC mapping for this character in hex.</dd>
    <dt id="kFenn">kFenn</dt>
    <dd>Data on the character from The Five Thousand Dictionary (aka Fenn’s Chinese-English Pocket Dictionary) by Courtenay H. Fenn, Cambridge, Mass.: Harvard University Press, 1979.<br/><br/>
    The data here consists of a decimal number followed by a letter A through K, the letter P, or an asterisk. The decimal number gives the Soothill number for the character’s phonetic, and the letter is a rough frequency indication, with A indicating the 500 most common ideographs, B the next five hundred, and so on.<br/><br/>
    P is used by Fenn to indicate a rare character included in the dictionary only because it is the phonetic element in other characters.<br/><br/>
    An asterisk is used instead of a letter in the final position to indicate a character which belongs to one of Soothill’s phonetic groups but is not found in Fenn’s dictionary.<br/><br/>
    Characters which have a frequency letter but no Soothill phonetic group are assigned group 0.</dd>
    <dt id="kFennIndex">kFennIndex</dt>
    <dd>The position of this character in _Fenn’s Chinese-English Pocket Dictionary_ by Courtenay H. Fenn, Cambridge, Mass.: Harvard University Press, 1942. The position is indicated by a three-digit page number followed by a period and a two-digit position on the page.</dd>
    <dt id="kFourCornerCode">kFourCornerCode</dt>
    <dd>The four-corner code(s) for the character. This data is derived from data provided in the public domain by Hartmut Bohn, Urs App, and Christian Wittern.<br/><br/>
    The four-corner system assigns each character a four-digit code from 0 through 9. The digit is derived from the “shape” of the four corners of the character (upper-left, upper-right, lower-left, lower-right). An optional fifth digit can be used to further distinguish characters; the fifth digit is derived from the shape in the character’s center or region immediately to the left of the fourth corner.<br/><br/>
    The four-corner system is now used only rarely. Full descriptions are available online, e.g., at &lt;http://en.wikipedia.org/wiki/Four_corner_input&gt;.<br/><br/>
    Values in this field consist of four decimal digits, optionally followed by a period and fifth digit for a five-digit form.</dd>
    <dt id="kFrequency">kFrequency</dt>
    <dd>A rough frequency measurement for the character based on analysis of traditional Chinese USENET postings; characters with a kFrequency of 1 are the most common, those with a kFrequency of 2 are less common, and so on, through a kFrequency of 5.</dd>
    <dt id="kGB0">kGB0</dt>
    <dd>The GB 2312-80 mapping for this character in ku/ten form.</dd>
    <dt id="kGB1">kGB1</dt>
    <dd>The GB 12345-90 mapping for this character in ku/ten form.</dd>
    <dt id="kGB3">kGB3</dt>
    <dd>The GB 7589-87 mapping for this character in ku/ten form.</dd>
    <dt id="kGB5">kGB5</dt>
    <dd>The GB 7590-87 mapping for this character in ku/ten form.</dd>
    <dt id="kGB7">kGB7</dt>
    <dd>The GB 8565-89 mapping for this character in ku/ten form.</dd>
    <dt id="kGB8">kGB8</dt>
    <dd>The GB 8565-89 mapping for this character in ku/ten form.</dd>
    <dt id="kGradeLevel">kGradeLevel</dt>
    <dd>The primary grade in the Hong Kong school system by which a student is expected to know the character; this data is derived from 朗文初級中文詞典, Hong Kong: Longman, 2001.</dd>
    <dt id="kGSR">kGSR</dt>
    <dd>The position of this character in Bernhard Karlgren’s Grammata Serica Recensa (1957).<br/><br/>
    This dataset contains a total of 7,405 records. References are given in the form DDDDa('), where “DDDD” is a set number in the range [0001..1260] zero-padded to 4-digits, “a” is a letter in the range [a..z] (excluding “w”), optionally followed by apostrophe ('). The data from which this mapping table is extracted contains a total of 10,023 references. References to inscriptional forms have been omitted.</dd>
    <dt id="kHangul">kHangul</dt>
    <dd>The modern Korean pronunciation(s) for this character in Hangul.</dd>
    <dt id="kHanYu">kHanYu</dt>
    <dd>The position of this character in the Hanyu Da Zidian (HDZ) Chinese character dictionary (bibliographic information below).<br/><br/>
    The first character assigned a given virtual position has an index ending in 1; the second assigned the same virtual position has an index ending in 2; and so on.</dd>
    <dt id="kHanyuPinlu">kHanyuPinlu</dt>
    <dd>The Pronunciations and Frequencies of this character, based in part on those appearing in 《現代漢語頻率詞典》 &lt;Xiandai Hanyu Pinlu Cidian&gt; (XDHYPLCD) [Modern Standard Beijing Chinese Frequency Dictionary].</dd>
    <dt id="kHanyuPinyin">kHanyuPinyin</dt>
    <dd>The 漢語拼音 Hànyǔ Pīnyīn reading(s) appearing in the edition of 《漢語大字典》 Hànyǔ Dà Zìdiǎn (HDZ) specified in the “kHanYu” property description (q.v.). Each location has the form “ABCDE.XYZ” (as in “kHanYu”); multiple locations for a given pīnyīn reading are separated by “,” (comma). The list of locations is followed by “:” (colon), followed by a comma-separated list of one or more pīnyīn readings. Where multiple pīnyīn readings are associated with a given mapping, these are ordered as in HDZ (for the most part reflecting relative commonality). The following are representative records.<br/><br/>
    | U+34CE | 㓎 | 10297.260: qīn,qìn,qǐn |<br/>
    | U+34D8 | 㓘 | 10278.080,10278.090: sù |<br/>
    | U+5364 | 卤 | 10093.130: xī,lǔ 74609.020: lǔ,xī |<br/>
    | U+5EFE | 廾 | 10513.110,10514.010,10514.020: gǒng |<br/><br/>
    For example, the “kHanyuPinyin” value for 卤 U+5364 is “10093.130: xī,lǔ 74609.020: lǔ,xī”. This means that 卤 U+5364 is found in “kHanYu” at entries 10093.130 and 74609.020. The former entry has the two pīnyīn readings xī and lǔ (in that order), whereas the latter entry has the readings lǔ and xī (reversing the order).<br/><br/>
    <em>Multiple Value Order:</em> Individual entries are in same order as they are found in the Hanyu Da Zidian. This is true both for the locations and the individual readings.  While this is generally in the order of utility for modern Chinese, such is not invariably the case, as the example above illustrates.<br/><br/>
    This data was originally input by 井作恆 Jǐng Zuòhéng, proofed by 聃媽歌 Dān Māgē (Magda Danish, using software donated by 文林 Wénlín Institute, Inc. and tables prepared by 曲理查 Qū Lǐchá), and proofed again and prepared for the Unicode Consortium by 曲理查 Qū Lǐchá (2008-01-14) </dd>
    <dt id="kHDZRadBreak">kHDZRadBreak</dt>
    <dd>Indicates that 《漢語大字典》 Hanyu Da Zidian has a radical break beginning at this character’s position. The field consists of the radical (with its Unicode code point), a colon, and then the Hanyu Da Zidian position as in the kHanyu field.</dd>
    <dt id="kHKGlyph">kHKGlyph</dt>
    <dd>The index of the character in 常用字字形表 (二零零零年修訂本),香港: 香港教育學院, 2000, ISBN 962-949-040-4. This publication gives the “proper” shapes for 4759 characters as used in the Hong Kong school system. The index is an integer, zero-padded to four digits.</dd>
    <dt id="kHKSCS">kHKSCS</dt>
    <dd>Mappings to the Big Five extended code points used for the Hong Kong Supplementary Character Set.</dd>
    <dt id="kIBMJapan">kIBMJapan</dt>
    <dd>The IBM Japanese mapping for this character in hexadecimal.</dd>
    <dt id="kIICore">kIICore</dt>
    <dd>A boolean indicating that a character is in IICore, the IRG-produced minimal set of required ideographs for East Asian use. A character is in IICore if and only if it has a value for the kIICore field.<br/><br/>
    The only value currently in this field is “2.1”, which is the identifier of the version of IICore used to populate this field.</dd>
    <dt id="kIRGDaeJaweon">kIRGDaeJaweon</dt>
    <dd>The position of this character in the Dae Jaweon (Korean) dictionary used in the four-dictionary sorting algorithm. The position is in the form “page.position” with the final digit in the position being “0” for characters actually in the dictionary and “1” for characters not found in the dictionary and assigned a “virtual” position in the dictionary.<br/><br/>
    Thus, “1187.060” indicates the sixth character on page 1187. A character not in this dictionary but assigned a position between the 6th and 7th characters on page 1187 for sorting purposes would have the code “1187.061”<br/><br/>
    This field represents the official position of the character within the Dae Jaweon dictionary as used by the IRG in the four-dictionary sorting algorithm.<br/><br/>
    The edition used is the first edition, published in Seoul by Samseong Publishing Co., Ltd., 1988.<br/></dd>
    <dt id="kIRGDaiKanwaZiten">kIRGDaiKanwaZiten</dt>
    <dd>The index of this character in the Dai Kanwa Ziten, aka Morohashi dictionary (Japanese) used in the four-dictionary sorting algorithm.<br/><br/>
    This field represents the official position of the character within the DaiKanwa dictionary as used by the IRG in the four-dictionary sorting algorithm. The edition used is the revised edition, published in Tokyo by Taishuukan Shoten, 1986.</dd>
    <dt id="kIRGHanyuDaZidian">kIRGHanyuDaZidian</dt>
    <dd>The position of this character in the Hanyu Da Zidian (PRC) dictionary used in the four-dictionary sorting algorithm. The position is in the form “volume page.position” with the final digit in the position being “0” for characters actually in the dictionary and “1” for characters not found in the dictionary and assigned a “virtual” position in the dictionary.<br/><br/>
    This field represents the official position of the character within the Hanyu Da Zidian dictionary as used by the IRG in the four-dictionary sorting algorithm.<br/><br/>
    The edition of the Hanyu Da Zidian used is the first edition, published in Chengdu by Sichuan Cishu Publishing, 1986.</dd>
    <dt id="kIRGKangXi">kIRGKangXi</dt>
    <dd>The official IRG position of this character in the 《康熙字典》 Kang Xi Dictionary used in the four-dictionary sorting algorithm. The position is in the form “page.position” with the final digit in the position being “0” for characters actually in the dictionary and “1” for characters not found in the dictionary but assigned a “virtual” position in the dictionary.<br/><br/>
    Thus, “1187.060” indicates the sixth character on page 1187. A character not in this dictionary but assigned a position between the 6th and 7th characters on page 1187 for sorting purposes would have the code “1187.061”.<br/><br/>
    The edition of the Kang Xi Dictionary used is the 7th edition published by Zhonghua Bookstore in Beijing, 1989.<br/></dd>
    <dt id="kIRG_GSource">kIRG_GSource</dt>
    <dd>The IRG “G” source mapping for this character in hex. The IRG G source consists of data from the following national standards, publications, and lists from the People’s Republic of China and Singapore. The versions of the standards used are those provided by the PRC to the IRG and may not always reflect published versions of the standards generally available.<br/><br/>
    G1 GB12345-90 with 58 Hong Kong and 92 Korean “Idu” characters<br/>
    G3 GB7589-87 unsimplified forms<br/>
    G5 GB7590-87 unsimplified forms<br/>
    G7 General Purpose Hanzi List for Modern Chinese Language, and General List of Simplified Hanzi<br/>
    GS Singapore Characters<br/>
    G8 GB8565-88<br/>
    G9 GB18030-2000<br/>
    GE GB16500-95<br/>
    G4K Siku Quanshu (四庫全書)<br/>
    GBK Chinese Encyclopedia (中國大百科全書)<br/>
    GCH Ci Hai (辞海)<br/>
    GCY Ci Yuan (辭源)<br/>
    GCYY Chinese Academy of Surveying and Mapping Ideographs (中国测绘科学院用字) GFZ Founder Press System (方正排版系统)<br/>
    GGH Gudai Hanyu Cidian (古代汉语词典)<br/>
    GHC Hanyu Dacidian (漢語大詞典)<br/>
    GHZ Hanyu Dazidian ideographs (漢語大字典)<br/>
    GIDC ID system of the Ministry of Public Security of China, 2009<br/>
    GJZ Commercial Press Ideographs (商务印书馆用字)<br/>
    GKX Kangxi Dictionary ideographs(康熙字典)9th edition (1958) including the addendum (康熙字典)補遺<br/>
    GXC Xiandai Hanyu Cidian (现代汉语词典)<br/>
    GZFY Hanyu Fangyan Dacidian (汉语方言大辞典)<br/>
    GZH ZhongHua ZiHai (中华字海)<br/>
    GZJW Yinzhou Jinwen Jicheng Yinde (殷周金文集成引得)<br/></dd>
    <dt id="kIRG_HSource">kIRG_HSource</dt>
    <dd>The IRG “H” source mapping for this character in hex. The IRG “H” source consists of data from the Hong Kong Supplementary Character Set – 2008.</dd>
    <dt id="kIRG_JSource">kIRG_JSource</dt>
    <dd>The IRG “J” source mapping for this character in hex. The IRG “J” source consists of data from the following national standards and lists from Japan.<br/><br/>
    J0 JIS X 0208-1990<br/>
    J1 JIS X 0212-1990<br/>
    JA Unified Japanese IT Vendors Contemporary Ideographs, 1993<br/>
    JH Hanyo-Denshi Program (汎用電子情報交換環境整備プログラム), 2002-2009<br/>
    JK Japanese KOKUJI Collection<br/>
    JARIB Association of Radio Industries and Businesses (ARIB) ARIB STD-B24 Version 5.1, March 14 2007</dd>
    <dt id="kIRG_KPSource">kIRG_KPSource</dt>
    <dd>The IRG “KP” source mapping for this character in hex. The IRG “KP” source consists of data from the following national standards and lists from the Democratic People’s Republic of Korea (North Korea).<br/><br/>
    KP0 KPS 9566-97<br/>
    KP1 KPS 10721-2000</dd>
    <dt id="kIRG_KSource">kIRG_KSource</dt>
    <dd>The IRG “K” source mapping for this character in hex. The IRG “K” source consists of data from the following national standards and lists from the Republic of Korea (South Korea).<br/><br/>
    K0 KS X 1001:2004 (formerly KS C 5601-1987)<br/>
    K1 KS X 1002:2001 (formerly KS C 5657-1991)<br/>
    K2 PKS C 5700-1 1994<br/>
    K3 PKS C 5700-2 1994<br/>
    K4 PKS 5700-3:1998<br/>
    K5 Korean IRG Hanja Character Set 5th Edition: 2001<br/><br/>
    Note that the K4 source is expressed in hexadecimal, but unlike the other sources, it is not organized in row/column. The content of the repertoire covered by the K2, K3, K4, and K5 sources is in the process of being reedited in new Korean standards.<br/></dd>
    <dt id="kIRG_MSource">kIRG_MSource</dt>
    <dd>The IRG “M” source mapping for this character. The IRG “M” source consists of data from the Macao Information System Character Set (澳門資訊系統字集).</dd>
    <dt id="kIRG_TSource">kIRG_TSource</dt>
    <dd>The IRG “T” source mapping for this character in hex. The IRG “T” source consists of data from the following national standards and lists from the Republic of China (Taiwan).<br/><br/>
    T1 TCA-CNS 11643-1992 1st plane<br/>
    T2 TCA-CNS 11643-1992 2nd plane<br/>
    T3 TCA-CNS 11643-1992 3rd plane with some additional characters<br/>
    T4 TCA-CNS 11643-1992 4th plane<br/>
    T5 TCA-CNS 11643-1992 5th plane<br/>
    T6 TCA-CNS 11643-1992 6th plane<br/>
    T7 TCA-CNS 11643-1992 7th plane<br/>
    TB TCA-CNS Ministry of Education, Hakka dialect, May 2007<br/>
    TC TCA-CNS 11643-1992 12th plane<br/>
    TD TCA-CNS 11643-1992 13th plane<br/>
    TE TCA-CNS 11643-1992 14th plane<br/>
    TF TCA-CNS 11643-1992 15th plane</dd>
    <dt id="kIRG_USource">kIRG_USource</dt>
    <dd>The IRG “U” source mapping for this character. U-source references are a reference into the U-source ideograph database; see UTR #45. These consist of “UTC” or “UCI” followed by a hyphen and a five-digit, zero-padded index into the database.</dd>
    <dt id="kIRG_VSource">kIRG_VSource</dt>
    <dd>The IRG “V” source mapping for this character in hex. The IRG “V” source consists of data from the following national standards and lists from Vietnam.<br/><br/>
    V0 TCVN 5773:1993<br/>
    V1 TCVN 6056:1995<br/>
    V2 VHN 01:1998<br/>
    V3 VHN 02: 1998<br/>
    V4 Dictionary on Nom 2006, Dictionary on Nom of Tay ethnic 2006, Lookup Table for Nom in the South 1994</dd>
    <dt id="kJapaneseKun">kJapaneseKun</dt>
    <dd>The Japanese pronunciation(s) of this character.</dd>
    <dt id="kJapaneseOn">kJapaneseOn</dt>
    <dd>The Sino-Japanese pronunciation(s) of this character.</dd>
    <dt id="kJis0">kJis0</dt>
    <dd>The JIS X 0208-1990 mapping for this character in ku/ten form.</dd>
    <dt id="kJIS0213">kJIS0213</dt>
    <dd>The JIS X 0213-2000 mapping for this character in min/ku/ten form.</dd>
    <dt id="kJis1">kJis1</dt>
    <dd>The JIS X 0212-1990 mapping for this character in ku/ten form.</dd>
    <dt id="kKangXi">kKangXi</dt>
    <dd>The position of this character in the 《康熙字典》 Kang Xi Dictionary used in the four-dictionary sorting algorithm. The position is in the form “page.position” with the final digit in the position being “0” for characters actually in the dictionary and “1” for characters not found in the dictionary but assigned a “virtual” position in the dictionary.<br/><br/>
    Thus, “1187.060” indicates the sixth character on page 1187. A character not in this dictionary but assigned a position between the 6th and 7th characters on page 1187 for sorting purposes would have the code “1187.061”.<br/><br/>
    The edition of the Kang Xi Dictionary used is the 7th edition published by Zhonghua Bookstore in Beijing, 1989.<br/></dd>
    <dt id="kKarlgren">kKarlgren</dt>
    <dd>The index of this character in _Analytic Dictionary of Chinese and Sino-Japanese_ by Bernhard Karlgren, New York: Dover Publications, Inc., 1974.<br/><br/>
    If the index is followed by an asterisk (*), then the index is an interpolated one, indicating where the character would be found if it were to have been included in the dictionary. Note that while the index itself is usually an integer, there are some cases where it is an integer followed by an “A”.</dd>
    <dt id="kKorean">kKorean</dt>
    <dd>The Korean pronunciation(s) of this character, using the Yale romanization system. (See &lt;http://en.wikipedia.org/wiki/Korean_romanization&gt; for a discussion of the various Korean romanization systems.)</dd>
    <dt id="kKPS0">kKPS0</dt>
    <dd>The KPS 9566-97 mapping for this character in hexadecimal form.</dd>
    <dt id="kKPS1">kKPS1</dt>
    <dd>The KPS 10721-2000 mapping for this character in hexadecimal form.</dd>
    <dt id="kKSC0">kKSC0</dt>
    <dd>The KS X 1001:1992 (KS C 5601-1989) mapping for this character in ku/ten form.</dd>
    <dt id="kKSC1">kKSC1</dt>
    <dd>The KS X 1002:1991 (KS C 5657-1991) mapping for this character in ku/ten form.</dd>
    <dt id="kLau">kLau</dt>
    <dd>The index of this character in A Practical Cantonese-English Dictionary by Sidney Lau, Hong Kong: The Government Printer, 1977.<br/><br/>
    The index consists of an integer. Missing indices indicate unencoded characters which are being submitted to the IRG for inclusion in future versions of the standard.</dd>
    <dt id="kMainlandTelegraph">kMainlandTelegraph</dt>
    <dd>The PRC telegraph code for this character, derived from “Kanzi denpou koudo henkan-hyou” (“Chinese character telegraph code conversion table”), Lin Jinyi, KDD Engineering and Consulting, Tokyo, 1984.</dd>
    <dt id="kMandarin">kMandarin</dt>
    <dd>The most customary pinyin reading for this character; that is, the reading most commonly used in modern text, with some preference given to readings most likely to be in sorted lists. <br/><br/>
    <em>Multiple Value Order:</em> When there are two values, then the first is preferred for zh-Hans (CN) and the second is preferred for zh-Hant (TW). When there is only one value, it is appropriate for both.</dd>
    <dt id="kMatthews">kMatthews</dt>
    <dd>The index of this character in Mathews’ Chinese-English Dictionary by Robert H. Mathews, Cambrige: Harvard University Press, 1975.<br/><br/>
    Note that the field name is kMatthews instead of kMathews to maintain compatibility with earlier versions of this file, where it was inadvertently misspelled.</dd>
    <dt id="kMeyerWempe">kMeyerWempe</dt>
    <dd>The index/indices of this character in the Student’s Cantonese-English Dictionary by Bernard F. Meyer and Theodore F. Wempe (3rd edition, 1947). The index is an integer, optionally followed by a lower-case Latin letter if the listing is in a subsidiary entry and not a main one. In some cases where the character is found in the radical-stroke index, but not in the main body of the dictionary, the integer is followed by an asterisk (e.g., U+50E5, which is listed as 736* as well as 1185a).</dd>
    <dt id="kMorohashi">kMorohashi</dt>
    <dd>The index/indices of this character in the Dae Kanwa Ziten, aka Morohashi dictionary (Japanese) used in the four-dictionary sorting algorithm.<br/><br/>
    The edition used is the revised edition, published in Tokyo by Taishuukan Shoten, 1986.</dd>
    <dt id="kNelson">kNelson</dt>
    <dd>The index of this character in The Modern Reader’s Japanese-English Character Dictionary by Andrew Nathaniel Nelson, Rutland, Vermont: Charles E. Tuttle Company, 1974.</dd>
    <dt id="kOtherNumeric">kOtherNumeric</dt>
    <dd>The numeric value for the character in certain unusual, specialized contexts.<br/><br/>
    The three numeric-value fields should have no overlap; that is, characters with a kOtherNumeric value should not have a kAccountingNumeric or kPrimaryNumeric value as well.<br/></dd>
    <dt id="kPhonetic">kPhonetic</dt>
    <dd>The phonetic index for the character from _Ten Thousand Characters: An Analytic Dictionary_, by G. Hugh Casey, S.J. Hong Kong: Kelley and Walsh, 1980.</dd>
    <dt id="kPrimaryNumeric">kPrimaryNumeric</dt>
    <dd>The value of the character when used in the writing of numbers in the standard fashion.<br/><br/>
    The three numeric-value fields should have no overlap; that is, characters with a kPrimaryNumeric value should not have a kAccountingNumeric or kOtherNumeric value as well.<br/></dd>
    <dt id="kPseudoGB1">kPseudoGB1</dt>
    <dd>A “GB 12345-90” code point assigned to this character for the purposes of including it within Unihan. Pseudo-GB1 codes were used to provide official code points for characters not already in national standards, such as characters used to write Cantonese, and so on.</dd>
    <dt id="kRSAdobe_Japan1_6">kRSAdobe_Japan1_6</dt>
    <dd>Information on the glyphs in Adobe-Japan1-6 as contributed by Adobe. The value consists of a number of space-separated entries. Each entry consists of three pieces of information separated by a plus sign:<br/><br/>
    1) C or V. “C” indicates that the Unicode code point maps directly to the Adobe-Japan1-6 CID that appears after it, and “V” indicates that it is considered a variant form, and thus not directly encoded.<br/><br/>
    2) The Adobe-Japan1-6 CID.<br/><br/>
    3) Radical-stroke data for the indicated Adobe-Japan1-6 CID. The radical-stroke data consists of three pieces separated by periods: the KangXi radical (1-214), the number of strokes in the form the radical takes in the glyph, and the number of strokes in the residue. The standard Unicode radical-stroke form can be obtained by omitting the second value, and the total strokes in the glyph from adding the second and third values.</dd>
    <dt id="kRSJapanese">kRSJapanese</dt>
    <dd>One or more Japanese radical/stroke counts for this character in the form “radical.additional strokes”.</dd>
    <dt id="kRSKangXi">kRSKangXi</dt>
    <dd>One or more KangXi radical/stroke counts for this character consistent with the value of the kKangXi field in the form “radical.additional strokes”.</dd>
    <dt id="kRSKanWa">kRSKanWa</dt>
    <dd>One or more Morohashi radical/stroke counts for this character in the form “radical.additional strokes”.</dd>
    <dt id="kRSKorean">kRSKorean</dt>
    <dd>One or more Korean radical/stroke counts for this character in the form “radical.additional strokes”.</dd>
    <dt id="kRSUnicode">kRSUnicode</dt>
    <dd>One or more standard radical/stroke counts for this character in the form “radical.additional strokes”. The radical is indicated by a number in the range (1..214) inclusive. An apostrophe (') after the radical indicates a simplified version of the given radical. The “additional strokes” value is the residual stroke-count, the count of all strokes remaining after eliminating all strokes associated with the radical.<br/><br/>
    This field is also used for additional radical-stroke indices where either a character may be reasonably classified under more than one radical, or alternate stroke count algorithms may provide different stroke counts.<br/><br/>
    The first value is equal to the normative radical-stroke value defined in ISO/IEC 10646.</dd>
    <dt id="kSBGY">kSBGY</dt>
    <dd>The position of this character in the Song Ben Guang Yun (SBGY) Medieval Chinese character dictionary (bibliographic and general information below).<br/><br/>
    The 25334 character references are given in the form “ABC.XY”, in which: “ABC” is the zero-padded page number [004..546]; “XY” is the zero-padded number of the character on the page [01..73]. For example, 364.38 indicates the 38th character on Page 364 (i.e. 澍). Where a given Unicode Scalar Value (USV) has more than one reference, these are space-delimited.</dd>
    <dt id="kSemanticVariant">kSemanticVariant</dt>
    <dd>The Unicode value for a semantic variant for this character. A semantic variant is an x- or y-variant with similar or identical meaning which can generally be used in place of the indicated character.<br/><br/>
    The basic syntax is a Unicode scalar value. It may optionally be followed by additional data. The additional data is separated from the Unicode scalar value by a less-than sign (&lt;), and may be subdivided itself into substrings by commas, each of which may be divided into two pieces by a colon. The additional data consists of a series of field tags for another field in the Unihan database indicating the source of the information. If subdivided, the final piece is a string consisting of the letters T (for tòng, U+540C 同) B (for bù, U+4E0D 不), Z (for zhèng, U+6B63 正), F (for fán, U+7E41 繁), or J (for jiǎn U+7C21 簡/U+7B80 简).<br/><br/>
    T is used if the indicated source explicitly indicates the two are the same (e.g., by saying that the one character is “the same as” the other).<br/><br/>
    B is used if the source explicitly indicates that the two are used improperly one for the other.<br/><br/>
    Z is used if the source explicitly indicates that the given character is the preferred form. Thus, kHanYu indicates that U+5231 刱 and U+5275 創 are semantic variants and that U+5275 創 is the preferred form.<br/><br/>
    F is used if the source explicitly indicates that the given character is the traditional form.<br/><br/>
    J is used if the source explicitly indicates that the given character is the simplified form.<br/><br/>
    Data on simplified and traditional variations can be included in this field to document cases where different sources disagree on the nature of the relationship between two characters.  The kSemanticVariant and kSpecializedSemanticVariant fields need not be consulted when interconverting between traditional and simplified Chinese.</dd>
    <dt id="kSimplifiedVariant">kSimplifiedVariant</dt>
    <dd>The Unicode value(s) for the simplified Chinese variant(s) for this character. A full discussion of the kSimplifiedVariant and kTraditionalVariant fields is found in section 3.7.1 above.<br/><br/>
    Much of the of the data on simplified and traditional variants was graciously supplied by Wenlin Institute, Inc.  &lt;http://www.wenlin.com&gt;.</dd>
    <dt id="kSpecializedSemanticVariant">kSpecializedSemanticVariant</dt>
    <dd>The Unicode value for a specialized semantic variant for this character. The syntax is the same as for the kSemanticVariant field.<br/><br/>
    A specialized semantic variant is an x- or y-variant with similar or identical meaning only in certain contexts (such as accountants’ numerals).<br/></dd>
    <dt id="kTaiwanTelegraph">kTaiwanTelegraph</dt>
    <dd>The Taiwanese telegraph code for this character, derived from “Kanzi denpou koudo henkan-hyou” (“Chinese character telegraph code conversion table”), Lin Jinyi, KDD Engineering and Consulting, Tokyo, 1984.<br/></dd>
    <dt id="kTang">kTang</dt>
    <dd>The Tang dynasty pronunciation(s) of this character, derived from or consistent with _T’ang Poetic Vocabulary_ by Hugh M. Stimson, Far Eastern Publications, Yale Univ. 1976. An asterisk indicates that the word or morpheme represented in toto or in part by the given character with the given reading occurs more than four times in the seven hundred poems covered.</dd>
    <dt id="kTotalStrokes">kTotalStrokes</dt>
    <dd>The total number of strokes in the character (including the radical), that is, the stroke count most commonly associated with the character in modern text using customary fonts.<br/><br/>
    <em>Multiple Value Order:</em> When there are two values, then the first is preferred for zh-Hans (CN) and the second is preferred for zh-Hant (TW). When there is only one value, it is appropriate for both.</dd>
    <dt id="kTraditionalVariant">kTraditionalVariant</dt>
    <dd>The Unicode value(s) for the traditional Chinese variant(s) for this character. A full discussion of the kSimplifiedVariant and kTraditionalVariant fields is found in section 3.7.1 above.<br/><br/>
    Much of the of the data on simplified and traditional variants was graciously supplied by Wenlin Institute, Inc. &lt;http://www.wenlin.com&gt;.</dd>
    <dt id="kVietnamese">kVietnamese</dt>
    <dd>The character’s pronunciation(s) in Quốc ngữ.</dd>
    <dt id="kXerox">kXerox</dt>
    <dd>The Xerox code for this character.</dd>
    <dt id="kXHC1983">kXHC1983</dt>
    <dd>One or more Hànyǔ Pīnyīn readings as given in the Xiàndài Hànyǔ Cídiǎn.<br/><br/>
    Each pīnyīn reading is preceded by the character’s location(s) in the dictionary, separated from the reading by “:” (colon); multiple locations for a given reading are separated by “,” (comma); multiple “location: reading” values are separated by “ ” (space). Each location reference is of the form /[0-9]{4}\.[0-9]{3}\*?/ . The number preceding the period is the page number, zero-padded to four digits. The first two digits of the number following the period are the entry’s position on the page, zero-padded. The third digit is 0 for a main entry and greater than 0 for a parenthesized variant of the main entry. A trailing “*” (asterisk) on the location indicates an encoded variant substituted for an unencoded character (see below).</dd>
    <dt id="kZVariant">kZVariant</dt>
    <dd>The Unicode value(s) for known z-variants of this character.<br/><br/>
    The basic syntax is a Unicode scalar value. It may optionally be followed by additional data. The additional data is separated from the Unicode scalar value by a less-than sign (&lt;), and may be subdivided itself into substrings by commas. The additional data consists of a series of field tags for another field in the Unihan database indicating the source of the information.</dd>
    <dt>Unicode</dt>
    <dd>Standard</dd>
  </dl>
</div>
<?php include 'footer.php'?>
