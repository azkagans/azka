<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.2
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.1.2";error_reporting(24575);set_error_handler(function($ud,$wd){return!!preg_match('~^Undefined (array key|offset|index)~',$wd);},E_WARNING|E_NOTICE);$Zd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Zd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$ln=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($ln)$$W=$ln;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($h=null){return($h?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$hc=adminer()->credentials();$H=Driver::connect($hc[0],$hc[1],$hc[2]);return(is_object($H)?$H:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$pg=substr($u,-1);return
str_replace($pg.$pg,$pg,substr($u,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($Ga,$x,$l=null){return($Ga&&array_key_exists($x,$Ga)?$Ga[$x]:$l);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($T){return
in_array($T,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$n){$T=$n["type"];return(is_user_type($T)?idf_escape($T).substr($n["full_type"],strlen($T)):$n["full_type"]);}function
is_searchable(array$n,array$W){if(!isset($n["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$W["op"]))return
true;$T=$n["type"];$Mk=$W["val"];$Za='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Za~",$T))return
false;if(preg_match(number_type(),$T)){$Xh='-?\d+(\.\d+)?';$th='';if(preg_match('~IN$~',$W["op"])){$Mk=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Mk);$th="( *, *$Xh)*";}elseif($W["op"]=="BETWEEN")$th="\\s+AND\\s+$Xh";return(bool)preg_match('~^'.$Xh.$th.'$~i',$Mk);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$Mk);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$Mk);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Mk);return
true;}function
remove_slashes(array$Y,$Zd=false){$H=array();foreach($Y
as$x=>$W)$H[stripslashes($x)]=(is_array($W)?remove_slashes($W,$Zd):($Zd?$W:stripslashes($W)));return$H;}function
bracket_escape($u,$Sa=false){static$Lm=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($u,($Sa?array_flip($Lm):$Lm));}function
url_escape($P){static$Lm=array();if(!$Lm){$Lm=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$nb)$Lm[$nb]=sprintf('%%%02X',ord($nb));for($s=0;$s<256;$s++){if($s<32||$s>126)$Lm[chr($s)]=sprintf('%%%02X',$s);}}return
strtr((string)$P,$Lm);}function
min_version($Kn,$Kg="",$h=null){$h=connection($h);$jl=$h->server_info;if($Kg&&preg_match('~([\d.]+)-MariaDB~',$jl,$A)){$jl=$A[1];$Kn=$Kg;}return$Kn&&version_compare($jl,$Kn)>=0;}function
charset(Db$g){return(min_version("5.5.3",0,$g)?"utf8mb4":"utf8");}function
ini_set($xi,$X){return(function_exists('ini_set')?\ini_set($xi,$X):false);}function
ini_bool($Ef){$W=ini_get($Ef);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($Ef){$W=ini_get($Ef);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($I,$Ni){$Og=(int)ini_get("max_input_vars");return($Og?(int)floor(($Og-$Ni)/$I):0);}function
max_input_vars_error(){$Ef="max_input_vars";return
lang(1,"<b>$Ef = ".ini_get($Ef)."</b>");}function
sid(){static$H;if($H===null)$H=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$H;}function
set_password($Jn,$M,$U,$D){$_SESSION["pwds"][$Jn][$M][$U]=($_COOKIE["adminer_key"]&&is_string($D)?array(encrypt_string($D,$_COOKIE["adminer_key"])):$D);}function
get_password(){$H=get_session("pwds");if(is_array($H))$H=($_COOKIE["adminer_key"]?decrypt_string($H[0],$_COOKIE["adminer_key"]):false);return$H;}function
get_val($F,$n=0,$Pb=null){$Pb=connection($Pb);$G=$Pb->query($F);if(!is_object($G))return
false;$I=$G->fetch_row();return($I?$I[$n]:false);}function
get_vals($F,$d=0){$H=array();$G=connection()->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[]=$I[$d];}return$H;}function
get_key_vals($F,$h=null,$ml=true){$h=connection($h);$H=array();$G=$h->query($F);if(is_object($G)){while($I=$G->fetch_row()){if($ml)$H[$I[0]]=$I[1];else$H[]=$I[0];}}return$H;}function
get_rows($F,$h=null,$m="<p class='error'>"){$Pb=connection($h);$H=array();$G=$Pb->query($F);if(is_object($G)){while($I=$G->fetch_assoc())$H[]=$I;}elseif(!$G&&!$h&&$m&&(defined('Adminer\PAGE_HEADER')||$m=="-- "))echo$m.adminer()->error()."\n";return$H;}function
unique_array($I,array$w){foreach($w
as$v){if(preg_match("~^(PRIMARY|UNIQUE)$~",$v["type"])&&!$v["partial"]){$H=array();foreach($v["columns"]as$x){if(!isset($I[$x]))continue
2;$H[$x]=$I[$x];}return$H;}}}function
where_function($xe,$d,array$n){if($xe=="md5")return
driver()->md5($d,$n)?:$d;return(in_array($xe,driver()->functions)||in_array($xe,driver()->grouping)?apply_sql_function($xe,$d):$d);}function
where(array$Z,array$o=array()){$H=array();foreach((array)$Z["where"]as$x=>$W){$x=bracket_escape($x,true);$d=idf_escape($x);$n=idx($o,$x,array());$Td=$n["type"];$Rf=$n&&(is_blob($n)||preg_match('~binary~',$Td));$H[]=$d.($Rf&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$Td=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$n["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Td,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($n,q($W)))))));if(JUSH=="sql"&&preg_match('~char|text~',$Td)&&preg_match("~[^ -@]~",$W))$H[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$H[]=idf_escape($x)." IS NULL";foreach((array)$Z["col"]as$s=>$Bb){$W=idx($Z["val"],$s);$H[]=where_function(idx($Z["fun"],$s),idf_escape($Bb),idx($o,$Bb,array())).($W!==null?" = ".q($W):" IS NULL");}return
implode(" AND ",$H);}function
where_columns(array$o){$H=array();foreach((array)$_GET["null"]as$x)$H[$x]=true;foreach(array_keys((array)$_GET["where"])as$x)$H[bracket_escape($x,true)]=true;foreach((array)$_GET["col"]as$Bb)$H[$Bb]=true;return
array_intersect_key($H,$o);}function
where_check($W,array$o=array()){parse_str($W,$qb);remove_slashes(array(&$qb));return
where($qb,$o);}function
where_link($s,$d,$X,$ui="="){$ri=($X!==null?$ui:"IS NULL");return"&where[$s][col]=".url_escape($d).($ri!=first(adminer()->operators())?"&where[$s][op]=".url_escape($ri):"")."&where[$s][val]=".url_escape($X);}function
convert_fields(array$e,array$o,array$L=array()){$H="";foreach($e
as$x=>$W){if($L&&!in_array(idf_escape($x),$L))continue;$Ha=convert_field($o[$x]);if($Ha)$H
.=", $Ha AS ".idf_escape($x);}return$H;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$X,$zg=2592000){header("Set-Cookie: $B=".rawurlencode($X).($zg?"; expires=".gmdate("D, d M Y H:i:s",time()+$zg)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($un,$Yb){$http_response_header=null;$vd=array();set_error_handler(function($ud,$m)use(&$vd){$vd[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$m);return
true;});$H=file_get_contents($un,false,$Yb);restore_error_handler();$Ve=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($H,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Ve,0,''),$A)?$A[1]:''),(array)$Ve,($H===false?implode("\n",$vd):''),);}function
json_decode_exact($ag){$ag=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$ag);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$ag));}function
json_scalar($W){return(is_string($W)&&substr($W,0,1)=="\1"?substr($W,1):$W);}function
json_encode_exact($W,$fe=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($W,$fe));}function
get_settings($cc){parse_str($_COOKIE[$cc],$nl);return$nl;}function
get_setting($x,$cc="adminer_settings",$l=null){return
idx(get_settings($cc),$x,$l);}function
save_settings(array$nl,$cc="adminer_settings"){$X=http_build_query($nl+get_settings($cc));cookie($cc,$X);$_COOKIE[$cc]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($ie=false){$xn=ini_bool("session.use_cookies");if(!$xn||$ie){session_write_close();if($xn&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$W){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Jn,$M,$U,$k=null){$tn=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($k!==null?"db|":"").($Jn=='mssql'||$Jn=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$tn,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Jn!="server"||$M!=""?url_escape($Jn)."=".url_escape($M)."&":"")."username=".url_escape($U).($k!=""?"&db=".url_escape($k):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Gg,$eh=null){if($eh!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Gg!==null?$Gg:$_SERVER["REQUEST_URI"]))][]=$eh;}if($Gg!==null){if($Gg=="")$Gg=".";header("Location: $Gg");exit;}}function
query_redirect($F,$Gg,$eh,$bk=true,$Cd=true,$Nd=false,$zm=""){if($Cd){$Il=microtime(true);$Nd=!connection()->query($F);$zm=format_time($Il);}$Bl=($F?adminer()->messageQuery($F,$zm,$Nd):"");if($Nd){adminer()->error
.=adminer()->error().$Bl.script("messagesPrint();")."<br>";return
false;}if($bk)redirect($Gg,$eh.$Bl);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($F){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$F:(preg_match('~;$~',$F)?"DELIMITER ;;\n$F;\nDELIMITER ":$F).";");}function
queries($F){remember_query($F);return
connection()->query($F);}function
apply_queries($F,array$S,$xd='Adminer\table'){foreach($S
as$Q){if(!queries("$F ".$xd($Q)))return
false;}return
true;}function
queries_redirect($Gg,$eh,$bk){$Uj=implode("\n",Queries::$queries);$zm=format_time(Queries::$start);return
query_redirect($Uj,$Gg,$eh,$bk,false,!$bk,$zm);}function
format_time($Il){return
lang(2,max(0,microtime(true)-$Il));}function
relative_uri($tn=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($tn?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Ui=""){return
substr(preg_replace("~(?<=[?&])($Ui".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$wc=false){$Vd=$_FILES[$B];if(!$Vd)return
null;foreach($Vd
as$x=>$W)$Vd[$x]=(array)$W;$H=array();foreach($Vd["error"]as$x=>$m){if($m)return$m;$p=$Vd["name"][$x];$Gm=$Vd["tmp_name"][$x];$Wb=file_get_contents($wc&&preg_match('~\.gz$~',$p)?"compress.zlib://$Gm":$Gm);if($wc){$Il=substr($Wb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Il))$Wb=iconv("utf-16","utf-8",$Wb);elseif($Il=="\xEF\xBB\xBF")$Wb=substr($Wb,3);}$H[]=array($p,$Wb);}return$H;}function
get_file($x,$wc=false,$Cc=""){$Yd=get_files($x,$wc);if(!is_array($Yd))return$Yd;$H='';foreach($Yd
as$Vd){$Wb=$Vd[1];$H
.=$Wb;if($Cc)$H
.=(preg_match("($Cc\\s*\$)",$Wb)?"":$Cc)."\n\n";}return$H;}function
upload_error($m){$Wg=($m==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($m?lang(3).($Wg?" ".lang(4,$Wg):""):lang(5));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
utf8_length($W){return
strlen(preg_replace('~[\x80-\xBF]~','',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$A);$rl=strlen($A[3]);$H=number_format($W,0,".","");$H=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$rl).'})*\d{'.$rl.'}$)~',$A[1],$H);return
strtr($H,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$x){$W=idx($R,$x,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$Ca=($x=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$W=in_array($x,array("Data_length","Index_length","Data_free"))?adminer()->formatSizeValue($W):format_number($W);return($Ca?"~ ":"").$W;}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Od=false){$H=table_status($Q,$Od);return($H?reset($H):array("Name"=>$Q));}function
column_foreign_keys($Q){$H=array();foreach(adminer()->foreignKeys($Q)as$q){foreach($q["source"]as$W)$H[$W][]=$q;}return$H;}function
fields_from_edit(){$H=array();foreach((array)$_POST["field_keys"]as$x=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$x];$_POST["fields"][$W]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$W){$B=bracket_escape($x,true);$H[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$H;}function
dump_headers($jf,$xh=false){$H=adminer()->dumpHeaders($jf,$xh);$Pi=$_POST["output"];if($Pi!="text"||$H=="tar"){$Mb=($Pi!="text"&&$Pi!="file"&&preg_match('~^[0-9a-z]+$~',$Pi)?".$Pi":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($jf).".$H$Mb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$H;}function
dump_csv(array$I){$Ym=$_POST["format"]=="tsv";foreach($I
as$x=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Ym?'\t':'[,;]|^$').'~',$W))$I[$x]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($Ym?"\t":";")),$I)."\r\n";}function
parse_csv($kc,$Xk){$H=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$kc,$Mg);foreach($Mg[0]as$I){preg_match_all("~((?>\"[^\"]*\")+|[^$Xk]*)$Xk~",$I.$Xk,$Ng);$H[]=$Ng[1];}return$H;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?str_replace('""','"',substr($W,1,-1)):$W);}function
apply_sql_function($ye,$d){return($ye?($ye=="unixepoch"?"DATETIME($d, '$ye')":($ye=="count distinct"?"COUNT(DISTINCT ":strtoupper("$ye("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($p){if(is_link($p))return;$pe=@fopen($p,"c+");if(!$pe)return;@chmod($p,0660);if(!flock($pe,LOCK_EX)){fclose($pe);return;}return$pe;}function
file_write_unlock($pe,$oc){rewind($pe);fwrite($pe,$oc);ftruncate($pe,strlen($oc));file_unlock($pe);}function
file_unlock($pe){flock($pe,LOCK_UN);fclose($pe);}function
first(array$Ga){return
reset($Ga);}function
password_file($i){$p=get_temp_dir()."/adminer.key";if(!$i&&!file_exists($p))return'';$pe=file_open_lock($p);if(!$pe)return'';$H=stream_get_contents($pe);if(!$H){$H=rand_string();file_write_unlock($pe,$H);}else
file_unlock($pe);return$H;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$_,array$n,$xm,array$oj=array()){if(is_array($W)){$H="";if(array_filter($W,'is_array')==array_values($W)){$fg=array();foreach($W
as$V)$fg+=array_fill_keys(array_keys($V),null);foreach(array_keys($fg)as$cg)$H
.="<th>".h($cg);foreach($W
as$V){$H
.="<tr>";foreach(array_merge($fg,$V)as$Dn)$H
.="<td>".select_value($Dn,$_,$n,$xm,$oj);}}else{foreach($W
as$cg=>$V)$H
.="<tr>".($W!=array_values($W)?"<th>".h($cg):"")."<td>".select_value($V,$_,$n,$xm,$oj);}return"<table>$H</table>";}if(!$_)$_=adminer()->selectLink($W,$n);if($_===null){if(is_mail($W))$_="mailto:$W";if(is_url($W))$_=$W;}$W=driver()->value($W,$n);$H=adminer()->editVal($W,$n);if($H!==null){if(!is_utf8($H))$H="\0";elseif($xm!=""&&is_shortable($n))$H=shorten_utf8($H,max(0,+$xm),"",$oj);else$H=highlight_matches($H,$oj);}return
adminer()->selectVal($H,$_,$n,$W);}function
is_blob(array$n){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$n["type"])&&!in_array($n["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$n){return$n["auto_increment"]&&(JUSH=="mssql"||$n["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($kd){$Ja='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Uc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$nj="$Ja+(\\.$Ja+)*@($Uc?\\.)+$Uc";return
is_string($kd)&&preg_match("(^$nj(,\\s*$nj)*\$)i",$kd);}function
is_url($P){$Uc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Uc?\\.)+$Uc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P);}function
is_ipv6($pa){$r='[\da-f]{1,4}';$Qf='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($r:){7}$r|($r:){6}$Qf|(($r:)*$r)?::(($r:)*($r|$Qf))?)$~iD",$pa);}function
is_shortable(array$n){return!preg_match('~'.number_type().'|date|time|year~',$n["type"]);}function
url_host($ff){return(strpos($ff,":")!==false?"[$ff]":$ff);}function
server_parts(array$hj){return
array("scheme"=>(string)$hj["scheme"],"host"=>(string)$hj["host"],"port"=>(string)$hj["port"],"socket"=>(string)$hj["socket"],"path"=>(string)$hj["path"],);}function
parse_server($M){if($M=="")return
server_parts(array());if($M[0]==":"&&!is_ipv6($M)){$pk=substr($M,1);if(preg_match('~^\d+$~D',$pk))return
server_parts(array("port"=>$pk));return(preg_match('~^/[-\w.:/]*$~D',$pk)?server_parts(array("socket"=>$pk)):null);}$Kk="";if(preg_match('~^([-+.\w]+)://~',$M,$A)){$Kk=strtolower($A[1]);$M=substr($M,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$M,$A))return(is_ipv6($A[1])?server_parts(array("scheme"=>$Kk,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);if(is_ipv6($M))return
server_parts(array("scheme"=>$Kk,"host"=>$M));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$M,$A))return
server_parts(array("scheme"=>$Kk,"host"=>$A[1],"port"=>$A[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$M,$A)?server_parts(array("scheme"=>$Kk,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);}function
count_rows($Q,array$Z,$Sf,array$r){$F=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($Sf&&(JUSH=="sql"||count($r)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$r).")$F":"SELECT COUNT(*)".($Sf?" FROM (SELECT 1$F GROUP BY ".implode(", ",$r).") x":$F));}function
slow_query($F){$k=adminer()->database();$_m=adminer()->queryTimeout();$tl=driver()->slowQuery($F,$_m);$h=null;if(!$tl&&support("kill")){$h=connect();if($h&&($k==""||$h->select_db($k))){$gg=number(get_val(connection_id(),0,$h));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$gg&token=".get_token()."'); }, 1000 * $_m);");}}ob_flush();flush();$H=@get_key_vals(($tl?:$F),$h,false);if($h){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$H;}function
get_token(){$Yj=rand(1,1e6);return($Yj^$_SESSION["token"]).":$Yj";}function
verify_token(){list($Hm,$Yj)=explode(":",$_POST["token"]);return($Yj^$_SESSION["token"])==$Hm&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$Ic=""){$_a=array_flip(str_split(compress_alphabet()));$y=strlen($P);$Gn=($y?13*($y-1)/2-$_a[$P[0]]:0);$Za="";$pk=0;$qk=0;for($s=1;$s<$y;$s+=2){$pk=($pk<<13)+$_a[$P[$s]]*93+$_a[$P[$s+1]];$qk+=13;while($qk>=8&&$Gn>=8){$qk-=8;$Gn-=8;$Za
.=chr($pk>>$qk);$pk&=(1<<$qk)-1;}}if($Za=="")return"";if($Ic!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$Ic)),$Za,ZLIB_FINISH);return($Ic==""&&function_exists('gzinflate')?gzinflate($Za):inflate($Za,$Ic));}function
inflate($Za,$Ic=""){$wg=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$xg=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Mc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Oc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$H=$Ic;$E=0;do{$ae=inflate_bits($Za,$E,1);$T=inflate_bits($Za,$E,2);if(!$T){$E=($E+7)&~7;$y=inflate_bits($Za,$E,16);$E+=16;$H
.=substr($Za,$E>>3,$y);$E+=$y<<3;}else{if($T==1){$Eg=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Pc=array_fill(0,30,5);}else{$Dg=inflate_bits($Za,$E,5)+257;$Nc=inflate_bits($Za,$E,5)+1;$Ai=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$kh=array_fill(0,19,0);$jh=inflate_bits($Za,$E,4)+4;for($s=0;$s<$jh;$s++)$kh[$Ai[$s]]=inflate_bits($Za,$E,3);$lh=inflate_table($kh);$yg=array();while(count($yg)<$Dg+$Nc){$Vl=inflate_symbol($Za,$E,$lh);if($Vl==16)$yg=array_merge($yg,array_fill(0,inflate_bits($Za,$E,2)+3,end($yg)));elseif($Vl==17)$yg=array_merge($yg,array_fill(0,inflate_bits($Za,$E,3)+3,0));elseif($Vl==18)$yg=array_merge($yg,array_fill(0,inflate_bits($Za,$E,7)+11,0));else$yg[]=$Vl;}$Eg=array_slice($yg,0,$Dg);$Pc=array_slice($yg,$Dg);}$Fg=inflate_table($Eg);$Rc=inflate_table($Pc);while(($Vl=inflate_symbol($Za,$E,$Fg))!=256){if($Vl<256)$H
.=chr($Vl);else{$y=$wg[$Vl-257]+inflate_bits($Za,$E,$xg[$Vl-257]);$Qc=inflate_symbol($Za,$E,$Rc);$fi=strlen($H)-$Mc[$Qc]-inflate_bits($Za,$E,$Oc[$Qc]);for($s=0;$s<$y;$s++)$H
.=$H[$fi+$s];}}}}while(!$ae);return($Ic==""?$H:substr($H,strlen($Ic)));}function
inflate_bits($Za,&$E,$ec){$H=0;for($s=0;$s<$ec;$s++){$H+=((ord($Za[$E>>3])>>($E&7))&1)<<$s;$E++;}return$H;}function
inflate_table(array$yg){$Q=array();$Ab=0;for($ab=1;$ab<=max($yg);$ab++){foreach($yg
as$Vl=>$y){if($y==$ab){$Q[$ab][$Ab]=$Vl;$Ab++;}}$Ab<<=1;}return$Q;}function
inflate_symbol($Za,&$E,array$Q){$Ab=0;$ab=0;do{$Ab=($Ab<<1)+inflate_bits($Za,$E,1);$ab++;}while(!isset($Q[$ab][$Ab]));return$Q[$ab][$Ab];}function
script($yl,$Km="\n"){return"<script".nonce().">$yl</script>$Km";}function
script_src($un,$zc=false){return"<script src='".h($un)."'".nonce().($zc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($yd,$Me,$Da=null){$Fa=array();foreach(array_slice(func_get_args(),2)as$W)$Fa[]=json_encode($W,256);return" data-on$yd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Me(".implode(", ",$Fa).")")."'";}function
input_hidden($B,$X=""){return"<input type='hidden' name='".h($B)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($B,$X,$tb,$kg="",$c="",$zb="",$mg=""){$H="<input type='checkbox' name='$B' value='".h($X)."'".($tb?" checked":"").($kg==""&&$zb?" class='$zb'":"").($mg?" aria-labelledby='$mg'":"").$c.">";return($kg!=""?"<label".($zb?" class='$zb'":"").">$H".h($kg)."</label>":$H);}function
optionlist($yi,$Tk=null,$yn=false){$H="";foreach($yi
as$cg=>$V){$_i=array($cg=>$V);if(is_array($V)){$H
.='<optgroup label="'.h($cg).'">';$_i=$V;}foreach($_i
as$x=>$W)$H
.='<option'.($yn||is_string($x)?' value="'.h($x).'"':'').($Tk!==null&&($yn||is_string($x)?(string)$x:$W)===$Tk?' selected':'').'>'.h($W);if(is_array($V))$H
.='</optgroup>';}return$H;}function
group_system(array$Eh,$Jk=false){$H=array();$Wl=array();foreach($Eh
as$B){if($Jk?driver()->isSystem(DB,$B):driver()->isSystem($B))$Wl[]=$B;else$H[]=$B;}if($Wl)$H[lang(8,'')]=$Wl;return$H;}function
html_select($B,array$yi,$X="",$c="",$mg=""){static$kg=0;$lg="";if(!$mg&&substr($yi[""],0,1)=="("){$kg++;$mg="label-$kg";$lg="<option value='' id='$mg'>".h($yi[""]);unset($yi[""]);}return"<select name='".h($B)."'".($mg?" aria-labelledby='$mg'":"")."$c>".$lg.optionlist($yi,$X)."</select>";}function
html_radios($B,array$yi,$X="",$Xk=""){$H="";foreach($yi
as$x=>$W)$H
.="<label><input type='radio' name='".h($B)."' value='".h($x)."'".($x==$X?" checked":"").">".h($W)."</label>$Xk";return$H;}function
confirm($eh=""){return
on('click','confirmClick',$eh?:lang(9));}function
print_fieldset($t,$vg,$Nn=false){echo"<fieldset><legend>","<a href='#fieldset-$t' class='toggle'>$vg</a>","</legend>","<div id='fieldset-$t'".($Nn?"":" class='hidden'").">\n";}function
bold($bb,$zb=""){return($bb?" class='active $zb'":($zb?" class='$zb'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($C){return
remove_from_uri("page|next").($C?"&page=$C".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($C,$lc){return" ".($C==$lc?($C?"<b>".($C+1)."</b>":$C+1):'<a href="'.h(pagination_href($C)).'">'.($C+1)."</a>");}function
hidden_fields(array$Oj,array$of=array(),$Dj=''){$H=false;foreach($Oj
as$x=>$W){if(!in_array($x,$of)){if(is_array($W))hidden_fields($W,array(),$x);else{$H=true;echo
input_hidden(($Dj?$Dj."[$x]":$x),$W);}}}return$H;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$sn){$sn=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($sn?on('submit','uploadProgress',ME."upload=$sn",SESSION_NAME."=$sn"):"");}function
file_input($c,$pk=""){$Qg="max_file_uploads";$Rg=ini_get($Qg);$Wg="upload_max_filesize";$Xg=ini_bytes($Wg);$_j=ini_bytes("post_max_size");if($_j&&$_j<$Xg){$Wg="post_max_size";$Xg=$_j;}$Yg=ini_get($Wg);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$Rg,lang(10,"$Qg = $Rg"),$Xg,lang(10,"$Wg = $Yg")).">$pk":lang(11));}function
enum_input($T,$c,array$n,$X,$nd=""){preg_match_all("~".driver()->enumLength."~",$n["length"],$Mg);$Dj=($n["type"]=="enum"?"val-":"");$tb=(is_array($X)?in_array("null",$X):$X===null);$H=($n["null"]&&$Dj?"<label><input type='$T'$c value='null'".($tb?" checked":"")."><i>$nd</i></label>":"");foreach($Mg[0]as$W){$W=stripcslashes(idf_unescape($W));$tb=(is_array($X)?in_array($Dj.$W,$X):$X===$W);$H
.=" <label><input type='$T'$c value='".h($Dj.$W)."'".($tb?' checked':'').'>'.h(adminer()->editVal($W,$n)).'</label>';}return$H;}function
input(array$n,$X,$ye,$Qa=false,$pn=false){$B=h(bracket_escape($n["field"]));echo"<td class='function'>";$td=driver()->enumLength($n);if($td){$n["type"]="enum";$n["length"]=$td;}$yi=($n["type"]=="enum"||$n["type"]=="set");if(is_array($X)&&!$ye&&!$yi)$ye="json";$ag=($ye=="json"||preg_match('~^jsonb?$~',$n["full_type"]));if($ag&&$X!=''&&(JUSH!="pgsql"||$n["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=(is_array($X)?json_encode($X,128|64|256):json_encode_exact(json_decode_exact($X),128|64|256));$ok=($pn&&is_identity_always($n));if($ok&&!$_POST["save"])$ye=null;$ze=(isset($_GET["select"])||$ok?array("orig"=>lang(12)):array())+adminer()->editFunctions($n);$c=" name='fields[$B]".($yi?"[]":"")."'".($Qa?" autofocus":"");echo
driver()->unconvertFunction($n)." ";$Q=$_GET["edit"]?:$_GET["select"];if($n["type"]=="enum")echo
h($ze[""])."<td>".adminer()->editInput($Q,$n,$c,$X);else{$Oe=(in_array($ye,$ze)||isset($ze[$ye]));$be=0;foreach($ze
as$x=>$W){if($x===""||!$W)break;$be++;}echo(count($ze)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($ze,$ye===null||$Oe?$ye:"")."</select>":h(reset($ze)))."<td".($be&&count($ze)>1?on('input','skipOriginal',$be):"").">";$Gf=adminer()->editInput($Q,$n,$c,$X);if($Gf!="")echo$Gf;elseif(preg_match('~bool~',$n["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($n["type"]=="set")echo
enum_input("checkbox",$c,$n,(is_string($X)?explode(",",$X):$X));elseif(is_blob($n)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($ag)echo"<textarea$c cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($wm=preg_match('~text|lob|memo~i',$n["type"]))||preg_match("~\n~",$X)){if($wm&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$J=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$J'";}echo"<textarea$c>".h($X).'</textarea>';}else{$dn=driver()->types();$bn=$dn[$n["type"]];$Ga=preg_match('~\[]~',$n["full_type"]);if($Ga)$Zg=0;elseif(preg_match('~date|time|year~',$n["type"])){$Cj=($n["length"]==""&&JUSH=="pgsql"?6:$n["length"]);$qe=(preg_match('~time~',$n["type"])&&preg_match('~^[1-9]\d*$~',$Cj)?$Cj+1:0);$Zg=($bn?$bn+$qe:0);}elseif(!preg_match('~int|vector~',$n["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$n["length"],$A))$Zg=(preg_match("~binary~",$n["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$n["unsigned"]?1:0);else$Zg=($bn?$bn+($n["unsigned"]?0:1):0);echo"<input".((!$Oe||$ye==="")&&preg_match('~^'.int_type().'$~',$n["type"])&&!$Ga?" type='number'":"")." value='".h($X)."'".($Zg?" data-maxlength='$Zg'":"").(preg_match('~char|binary~',$n["type"])&&$Zg>20?" size='".($Zg>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$n,$X),(count($ze)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$n){$u=bracket_escape($n["field"]);$ye=idx($_POST["function"],$u);if($ye=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?idf_escape($n["field"]):false);if($ye=="NULL")return"NULL";if(is_blob($n)&&ini_bool("file_uploads")){$Vd=get_file("fields-$u");if(!is_string($Vd))return
false;return
driver()->quoteBinary($Vd);}$X=idx($_POST["fields"],$u);if($X===null)return
false;if($n["type"]=="enum"||driver()->enumLength($n)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($n["auto_increment"]&&$X=="")return
null;if($n["type"]=="set")$X=implode(",",(array)$X);if($ye=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($n,$X,$ye);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Wk="<ul>\n";foreach(table_status('',true)as$Q=>$R){$B=adminer()->tableName($R);if(isset($R["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$G=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$G||$G->fetch_row()){$Kj="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$Wk<li>".($G?$Kj:"<p class='error'>$Kj: ".adminer()->error())."\n";$Wk="";}}}echo($Wk?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($wm,$ql=0){return
on('mouseover','helpMouseover',$wm,$ql).on('mouseout','helpMouseout');}function
on_help_value($ik="",$nk=""){return
on('mouseover','helpValueMouseover',$ik,$nk).on('mouseout','helpMouseout');}function
edit_form($Q,array$o,$I,$pn,$m='',$F='',$zm=''){$em=adminer()->tableName(table_status1($Q,true));page_header(($pn?lang(14):lang(15)),$m,array("select"=>array($Q,$em)),$em);adminer()->editRowPrint($Q,$o,$I,$pn,$F,$zm);if($I===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$id=false;$Un=($pn&&!isset($_GET["select"])?where_columns($o):array());$Zb=(count($Un)!=count($o));if(!$Zb)$Un=array();if(!$o)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Qa=!$_POST;foreach($o
as$B=>$n){echo"<tr".($Un[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($n);$l=idx($_GET["set"],bracket_escape($B));if($l===null){$l=$n["default"];if($n["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$l,$kk))$l=$kk[1];if(JUSH=="sql"&&preg_match('~binary~',$n["type"]))$l=bin2hex($l);}$X=($I!==null?($n["type"]=="set"&&is_array($I[$B])?implode(",",$I[$B]):(is_bool($I[$B])?+$I[$B]:$I[$B])):(!$pn&&$n["auto_increment"]?"":(isset($_GET["select"])?false:$l)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$n);if(($pn&&!isset($n["privileges"]["update"]))||$n["generated"])echo"<td class='function'><td>".select_value($X,'',$n,null);else{$id=true;$ye=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($pn&&preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$pn&&$X==$n["default"]&&preg_match('~^[\w.]+\(~',$X))$ye="SQL";if(preg_match("~time~",$n["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$ye="now";}if($n["type"]=="uuid"&&$X=="uuid()"){$X="";$ye="uuid";}if($Qa!==false)$Qa=($n["auto_increment"]||$ye=="now"||$ye=="uuid"?null:true);input($n,$X,$ye,$Qa,$pn);if($Qa)$Qa=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($id){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$Zb){$Jc=($Un&&($m!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($pn?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$Jc".($pn?on('click','ajaxForm',lang(21)):"").">\n";}}echo($pn?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($nj,$y){return
str_repeat("$nj{0,65535}",$y/65535)."$nj{0,".($y%65535)."}";}function
shorten_utf8($P,$y=80,$Rl="",array$oj=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$P,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$P,$A);$y=strlen(isset($A[2])?$A[1]:preg_replace('~\n[^\n]*\z~',"\n",$A[1]));return
highlight_matches($P,$oj,$y).$Rl.(isset($A[2])?"":"<i>…</i>");}function
highlight_matches($P,array$oj,$y=null){if($y===null)$y=strlen($P);$H="";$E=0;if($oj&&@preg_match_all("((?|".implode("|",$oj)."))su",$P,$Mg,PREG_OFFSET_CAPTURE)){foreach($Mg[0]as$A){list($wm,$Il)=$A;if($wm!=""&&$Il<$y){$od=min($Il+strlen($wm),$y);$H
.=h(substr($P,$E,$Il-$E))."<mark>".h(substr($P,$Il,$od-$Il))."</mark>";$E=$od;}}}return$H.h(substr($P,$E,$y-$E));}function
icon($if,$B,$hf,$Bm,$c=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($Bm)."' class='icon icon-$if".($B?"":" jsonly")."'$c><span>$hf</span></button>";}function
copy_icon(){$dc=lang(23);return"<a href='' class='jsonly icon-copy' title='$dc'><span>$dc</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('*c(<]iDp;+<8]XG-X#ETBP{IAOo`HAD0Z,$t2FfTr3g#Vd(TV$)k.Y.Mza.nf=/#EsSP{RNA{M`c@a0tg&9-nc;72Wy&XvI>dmHD*qF#w3sU4N@=|&JHw3xBmwvnkq<ydKh3e?rW3UW8c6iorbru~-xFWKSUq)z05WLa*7(Z6q_k
>fA~ZVU{Y8%+yFYE(^i;T$03;v)}SxAXNo<2/|a&hF.tk8g=mh`&V[Y+Jg@kV{Sqq*QTq`Pdg~jopx-(Vm;9h^8LO"b79T*xOZYLrw`W*by*"9E8;y3*Q"%:"cM#I?Nm)g"HjH]t<XaA1o(r3Jbt:+q>5y
hEWT0t;QeU+eKbfA)dYUoNbi2I$`BJhLgr:&V-v0b"$EN89Id8>
n)ARt5TpU8NE}!G^/#!6z%BI)xMGGS)M8f+*2HyhUQ+_oMQF2EQi"&/g>W2yT(IE96sGrq+Z`yDc96$8=2UJ+S`$0tt,suyEps:Z-C1+P,@IL[3H3poVy;_1=sg><#+v{"+lM&vrPd{fIErRru1$V5gIRRet>oSi83]?]VZp0C-`G"*EJ`bK6rcS#pSTs^selWAVk+["M;ix#il8A]a52R7R@rGN9BM=C%<x4>Vi4<y<#Xf`)Q382VesHPT84b5.c%I!6vZ+qZK4Yc+3jfA"DS%8zxXru
,Efd5hE+Q&`koWtBokKfC*o<l)_3Mcd!D9H1!M&*SqAXaT]@9yHV!&Lv?owU9lu%,nFFj0q@wfQ!,ovAoG&y+Y$0`?^M,(Z"WKChD*!<qxg5_tzOJ?Y`dNzA:j~qOS7C"AzCc"PX"-xjlFJM5Su[vo/JKyP..nNNxa`-54e<&OCM3<0N~]U"h*kA$HI;N#fiySXADi9muuVRtrYcEs~-YZy"CTBm/dk&lEz2G`mx:X@ii%=3AL"4lv{tN[X)VsoN?:`MMRKfZPX=&q/(0[F%lKJHrT-53p.r{l<1ua
.%*Q/uEk]Z.lo@dy?Rk6Z9y%sk/zDlAqC?s=f2cHx0hd6gEo<))#>1vfZ;xf+!QU<9ls1m4<HR.4.nSkVahA3iWCMDnnDrf{;"&Va_kK-d1_]_<c<w3a*<K]m_OKTH-n0~:}fI>btB8ZG,Vwo*urg6f3OP&-`d*M0ktSkc%{1Nxi)_v#(

V;_RNE>UWBOhds*U0e8t{ZnGaYDJ3>
"e`mW+vNx3)]lct,8m^Mbg9g;pF
6X]}*;u.Gjf*L0ye9GxZfsQ/%)ovdoEhI?)=GvdLMIh:nYyv2+Br9"i|Qc))DM)0ORVRd,ShD#kZk%oa1|y]G/84-$z"q8gn&N-<MjLPUAYyl{o)NKedIl:1_{l}+-Cno1O}"@Er=&<7_XE82CJw$yZnti$xV[8ndG-4@76_KYxqE(Gj:3_i8h_*
n0f:7ki(??jX^=bX(
Nqcn/403Q`G[8()ZxS{"MD/D"RWvrmtX{3uHa]S`8=PqZ"G,0)[G=Kj.n
]r:G+CeI`YBi_58@k.^<(7;<Q#YqF/wN@lFh?8tDtk0HL2gZP_kl+M9ipj>;^xMkz]OO[k3.Z,K_I=f:C61UU
RgNd%Hb:p:,^}u{nEcO6_Nf;-Z/$Pj}_9Yk
9qWn_hqI%K}W(FuK<"T_KQi.Jp]KkYyY|GRy>_Hq.42Vwc.t(4h1;`#-_*6H!D*<Eedoly,F
y"jys2p}"0R$;wx=ls6e,sh<db
uBY9(o*Fdg3Ke0vOUD/)_?!K?4I!#^+<h3J#"&y)-Ul3H%HqQ1]Wj&NJTS2%*VU2EDT+?,]NgNVE<^`aKZ$Rs3P0,qzR.v)4GILUC<|Ud:p[2Q7i:Xl.5%uA3&=La!];v8G_>_B]wfZoU/!lB;cNp_`DJqA[3JubAy:.MMDd>+^a-`-SnxP#EbXIrFth5YEd$/:a_Pr+e$%DC1?<P!5XQ]lb?#??)St=Ztc__DzH{2kDpc_eQH=eNqC-xh[53f$pq<7N2Jn?jLw?/@g=nfRGXV@/~$&Iz%xt.0!*s)naS(fkLwr,)#5.Pixx?K$FstcJFT->afBGQSBu94W`%*Fr4G?lZBU[M0ekQ*M6{E0,D-WLLkL(7bxoyNCI{0_)u(76[ZgDvbeBxvQ*pG3@BT`,NbVey"-Hq-++QiE&v2TW2D~;dX.I7s}Q)_M2d%<2#_mwza(g}ptHZY*%2t9N-G#pibhL0fJ6YmkqW.88^?
On;%1sB%>6YZ//nv81N|?JS=<bNnkVE?<?R/aIeo7Ju9oxbFMl#$+GVX*hpdVvYe5^Aops?Q5ib2WBE+D*T8s#+fk15|m.FEH<2]$r$bBB6$sZu~t/fs^ciBGt0_]3N,D"M{P6iRA[H?t!iFC+p_fa&3NT2&u(R_b}M/5haEWeF@t+n$MHjAY"D~>jy?r|D,"w11?7:Df-I@"MCWry3NxD<G/lT_kUuz4dSAPJ:DL=+nfPkyx6ptdo!BBB0s:?!UV,g<egPzl^Z@40U~n1(7(H8%%H-S
Csxg;]5f>XPi5JE(oV!t"%KpQ?0!?!u+jev]~!T,fN}V(5+m20qhqIiRV,I
HGRt5A.
X#G_})=,Pjv&`Aa`4g(?:l{yPit95/V?`:%,C]2
a]gxe[=F.s3
aU9Pd-$0%tlHg
?sR>~Br$^80iPo2Z$Z=_qJaBu?_^Qy}ChV61:nhk{bm,}7hk,)XDJXv;L/fp"/Lx=cXV/%KkY@sR1gPcJR-KTR0v{P(M76[xTMr1rxcK[cp]NphX7Y~FQHsb}l7l<U55jMp,:cD)Zw95rWHu!H9?2v/?Lu:aVW@I&5*/Jr.6+r
j]xaW<URw9vVx078rA//Y~H1Bm!Pyb*yN0l/4bud;<G0gn*yNNGm(EP*xFJ(5!;S/VDKMBL!uU#wDKvOy.,DxRMqLOXkl7v!#$M.rsHNvZBuR_?J(jhkKsSr`:*iTO^F9CI?6m[gq2ig<5F=!)@Ug}phXQEGGaVe0l8rfcn.SMg)<[rDxKPW%0Hs80yN^>w}O(O;e3Kgw^y.Ud+MpQl_c(2F@-L<XNfSTrL"A,a&O]#(_mC!0q(-`m9uX/.7PW9)&u*~[GuO<jHHh0<P27wI2Aqm]kfqI)KKb_IBE%Y^9kD"]9NO$^<drZ&R#4_fCW8^s9U,uC/Q>+L%Q>k&q1ULx^pctS-T_!%z,Xx5z)t=LiJ<0JIKN}yF+f`SZRBzA39JF`64g"M3#|u*P
$54:q+;xZ@3|*.!]Rrl!QukvN`,=ntjA^8t}tnnDJVBbV0IE=Id>bbn!d!nkq,
MpzBIrIhoo(swnSZg)stY)"l6KR1`bbgRrr_#cp>GHIISPwA.Q)dp%1
~([OkfpfSImq!0>Ph$P%/g
od={Z5;Ob#A3Y~GT&&wElNH"BC.f
&,O@<T7OU@>wlQt:Re78?]/3e-}4XAHR=ea/~;|c/(3&yxeHLKAr=h+H|sclPSCUz1~-!ZOD|oQw</L<
7"J/G)1I
_Wu]5(?:uHtSqJO6-<;0Ctkx.ufZ5NMtw-dY2)#k+xCb-:(Me=1h+Qi21Inqty>RV
YbAKfX7rL4yL;1jlbPb$*8~@=YBQykY,LIXcW#>E]vR-=$
tmyBFQ3"9%ai:W@1<@+-6+;"L]_aD;
C[<w]veXu0z+"[YRq"R$9[(7;m2TH$?;ZbPuV6+hU!b<?0e;8gRJ@dcC^,r8tljexN#K^0}:WDFcfon7#P|!@rJn#BE!Zw&qg08AIw#l9uVpEgW@}hlY`BO!L6?l-bCt$4284Ao@r$4klc3`!y3
F%:HS$,7;B2+rGmC=UcW[&<hh%hBPe%l^mN(>rdbT0e+lu1f=SKEquxI-hg#5rTKn.=*:^;a<ni8.qg9E@"UG
_KGb<@-+6C)cPwS<QaUh(bJF9TaWOF%ZE6ei83sUyt[auyW
%x5JBoBh5:PB5sG#Zn5;mh?@HH**FCNQ
F!+m?p/qpS/wTIq6<J5EChnxwq-vUr<Ng;yG%(#,J.F?AI]X&ImB>/t)@+V@S&ZYbA0UT{2kT_VCZM?7C8GK#Gp]=mWdSy4.*zJUqFVv%?lK$1EK&yB-O)wi%yKZ"%:5ZVg>3[9/F745SQ?3.10DI`t0Ip-zN"kA#f%64{ybdLZ&MAf*ZbQ<fqP6:SWg3^K{ih$gLIT{y@"
v5<(7hjguju)hrhotTyAx3n%_%nxOg@N`{euSPt/e`4L!gq0];4h9r`MhuR"3~XEH^Jq*@<i2_rgX%^?a%W2=xM;r+!h`:"&wfZ-d)$o$8X/1`KtUaZ9p"m{(M<6cplN2O,]Y4cUG*^PZ$nS7:`!I8RA2zk9t4<M$QurHC4v-pges|H1/6gi8;w/Mwd.s8b1eZf=2=1O@-$Q/ZEv-kun+zX]MfJ9GpF8Z1d-/nIQl#Yw&:@I#rj?cRdNBz_&M-

I$#,IpFTje_drQ*0+3DbuW!t@bJ#s_/93e
u@1>G>_.8rYPaNP,;+G2OC;Z"(Y"8R+=<+U](g3i;G$M3y2j}qfwi3"FgQC(<@(Ex3]u0eqmJlGIlHt,%r@HJ7|:2nQ;k?UAKv]LQ_f2G1,&)Mts#<90u"YMcMTK=');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$Osc2b7V>fj0U7tCw8TNfbT`5e<0!4x49EeL1n%e,E<_WZ?>@wWMzpUD:UubAB^u7,;ZJ.!U!ODBJ$p,I?B8.F[0L@ZP|U.08;>OMB~NvqOKctbeXn{?PG0IyC%OS?)r=jqOH2M?hb}k!R/`..+tcti.<c<RYfU_-K2A
vCxFn<6{L^r&8pu@R.[?Y;Q%DJhl3UE#:f_^Gy.{7^;|fNj6ajaX/-y3:kA
^e)ZU{TubDn#S5p^*>QuANxT;&o~2R.180pfCDp{Af#.9i9,-(*Y&"#eJu7R(uH>BnL38{cb_!.Xt`+GTj["w]mNugo|W%7muWs%-r2M[*vyw`Oi6fN"fuoOa;Cx<jivJ:1;k/yuSo;e7`7Ib+sTqL`5Z!<_Msdu^T1o?NxnfVqUcZ(31mO1mW&?l3hyqkSs<KDz)JOYNX.qcbw.a-hF9!u8),&16yAnEfk#`",*pieSR"^
:QnVS6*:q5["XTfNZdqS`4.wq&WqR$ff;}))8p/))?(NoZ>?`%8E(L)Qbr/mvjYq3odVYIZmt];gErx>%}pw8jHUEMo^U6FzrG6!>r%.Fl,i8GQ7.)96y=aANQAV
I&mM@=8CtkDDN#eQ><"PT&0&"
MqJq.V[OT-W:;AFl}kzYka3jtFJR2
a(@dy4=2dZ`=+P>(`R1E_t2_SsXn^QTobmely7V_<d>rQWl@v,kyf0vur:xH._r2kS"fa0O7^l]f4?(
>1U6U+VK-57o7x8oQWpsfb%dt4*EQPSa_B
R5pEdml@-Xb&>I"-^*scQ1=E/ctCOk5vsYn%D90P*?o@rb?
W?IHCMCI&bJI9sK8]T5bt76}rq_*)G[9K2;FAd)taZI^BtuBX+sK60V2H]NeE|*vCruwDpOK^kn6m6f6&1s^Ucs*]9vP^6+%wutWM/66l4Fj)WO0?_+RGg2|jW.*v?W?ZXyua"T+&(XWj;>M!:kHFApzq<`|]GHW(vkK32!q%[A`AHO1`}O*ZI?@w}[)');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(',]bcjnsZ323o!;f/.e+fx8n;[f71r0pc]giW`oV!D5mSIO;.F)#>?N&/uKRvUtUkWix^XgjX;sS9{(3
eB-
eJD[z_=IA3FpiUH:=MZ3k@qANy
,pQ[T_3Z)Wvk_gT[Jd2khOG[lbtSJi%Xc~x,$3rQ`e7zs`9hczx~5/)NGI&}cu=mR0U@T!doS{^#,i$18TPKy|Ut2;;<LDrZ*rx?t3j]uV0wBhgRMVF+5rB"o&w`p]@K$P3@jPh+.?b}q$QhMV-w*DRoP)N{L2JTta*c.*>bWJuFy>FjBaYa^O=]J40HjoD4_^T?rw%#V3SaEJ,@haT<%p+hZz%2WiK*x/Ujaht!%qA^VCZ%24?IYc7SxrR/#P0:US@|Fn%2c_mX-0TL`9<sF_kip3+A]LVm4i_B)GKtx"@.D*hh<7$~]-CYZwFt@Oa4x]vbQ|`u+7g+B6IsXQ3H9^K]$9EnvPx3cO;lKlZbjaFHFIy,ncrDLW[Tn<w@a^MaXrb^LUyM7x^;r12)nqu8iDuIM(&I)|_X"Ta}5J+vE8Uf0-m`=4yNV{@ctgiirE-<
?TDx$]G!:iP](g0Z*9Tox-iN*VRy5Z>W|@*CdPh,qgPeFeMj{gObW[)Bvq#<xTo_E9Ma8.QeBgQwGwbq:@%v*h-K?(EG6roY+s6+fSd4qJe8ihH3!oX$R%<=8sjBnS#^E#;AmlT"iBvkm.HK$!dmmiw(<TJiS*Fu)T`tfj)T
@N%}vxH6(]h%)|`r.SEbR&c(LU&vpH"+5`VMV)-ZbIT#<fD?n>8n2J%va!Mv$m.^hRe%-X-j8i^ltxz"+|(/%&CpSH2U#0L{*B3d4
_yY.,rLR)Vu[*cHPfLnsT%2)YL*l"Qrl)l]e?}*q</a6L:)^n}&4kh
$76?e_vQYFJn_$lIesca`%Ewhj%@RVQhWxqp+.jV5;r-HtRBw:=<Sk)v"OJJtc%O%sO:
ZZ&+Q1[:U&fw2;M5@lX{[^,8rZPTbc9_#(EBnTtG;ck;)R4J&Zs.JeIACX?%N(wf_FTvYw0/xQqtaLx0CKViZm]I1+UWX/z$S9P&84YP;E=~j^9RosV!7zfSE#83?W&67RHN5rXJPDCS2Gyv;d!O@S#*/Uo2_|#Mu=br..K!3_K;71(Uy~vxanxZS)w%e;THdO(#U)!#!GJB^&t[rKBocXn!w<n)YgNp!K#5tt=Q,r#DG:?&t8?)!?"%DU_7IMM7ZkymqVXhSO*|CkdoTA3Uncu[!&/UNEkv$d-~Q*Y<41A./WpZv1ekD>a6K,o&o{FA<,hX="<85cJg1"q`ACg$xfbTN^G(18VLq|V^0zEub|d9kf63V>g9m>$"Zen)a{$+a[j><8U7*GC%x99n>"T[6}?,bk*,q-nZ9yH=^Sqtfqs+*[fgu)b>FMLo[`a<HAX%w/b<L.xjMB]jfV,Ar6qJ-IvMed8h$K;tjyg2&/*Iq[?l-Gu<2MghB>KoD{5rWJrFV]bxI9H:=zbb7Td4o7a.Ga2ZLG`$+KgEw@fGI~tV,k-Nd=YwM{)h61ry(c(L/g5$-cWtXw=Q4,CNs`:|aRsFGM+*-V_`%rI/YL@:?n(>E
/n2exjX%P[Dq]yI,HU[R3mauTku
0AA+R`a:Co6f388ML#DT)"c(m-FoQw:%p0
PG&r"LiSMarC.JWx4eaW//o9%OUE[0S6z&C/p4(Bg2oXd8!##.{#w]{VW@hQ7fU7o:tbCrEEJbz:6(cZrU0Xda`4xVx!+Sr@c2&GQ]F):Yb?,oH$K=2j2W%07YqKDDoSheV3Kw.3@aW+w*B#4Z36q<!qp1PyQkpGS9Q<-V(^VFyG;w.H_s5LVHIY&/6]::OcSEM!@>EdFKirB$BjXBihOE=7tX"fY"#JZ?@Rym;[F@lmHfkJOU*h/!MXOdxLq`d<FUof^^.We>/tP!
oZuZPZ8^WdOIo#ty^2XU`|0/.trk[&21Vcp<:iH=NZ0ja;jtf&gI8o]iT]vsA^G)4V5//@E%tfMjh6j$+8wQs:Pc&6-_w^U&]In#`fvwt6#4:&L0:}G^TL=USoe%m{FH;,kcYR@`?q:)8pQ7H?c^Ua;GAk9K%p"fbcHuJ?<=6AT2(R*xu?bcvJKK1j@|g0`v(s$PNT=Z#L,%Q_C/a)6W+41|JQs:,1-J=t%2n.5cAt6l1AiKxX5xDG-iTXMpZBT=_X:=C4WdkI&-`(.GobZFC9!yC%C%/Q(V*k-!uQ?f5>Nt#36)EAcL2%Sn0e]E^+Gd]E4%Ci@ycI9C7j"@kRwz%J5W;$ioW;Co2:4g`/lcCnj@1G*py6%(m?I
>n6Y!F]B-&^2@,;/Y(Rnu%
]vb+wc+CKNq3w+w0D(]GoV^?fNN?,[+?;,"XGP<!b!&aS!S>`Ef:XW`PJXr>)1K&i;)lNuL!uR7/(<lxsGy137GOLb]Fnb{Q0;?J!6cGsEYr"Y?Ub0,cG9p8(2YP]j]KZ&QBDC/"2WjK>"/S&o8b2Fb6wZ]L)+<w7iPj#)yu#L"[sr`cp/__ynn$Cp^DHqvP(DXD^N?;~EeOS.aI-,bRWxnj54PhTJ)tt)hTKpj85%TlGg/JD"Br_)C3*Niig;Pp+XAIn(ilG0HITlWiG-!Giqi#F0nRZt&"Cm:`qUNeim?P-gE#PI/
a"/;T^g$lh(6(/gH;^mRQ/GJh
?ypWph-5Q:YV0biLHp}BJA[;=f#6io7b2@)3<Q8:h[ha$a3u%YhD2pVtC+)Sxldu`/!Dco{+*5mmGR^S[<=QShX]M[{NRMC$p3YYLT:T7)%uAc/"dRz"d%df`,&kOKUrG"u%=Q8G4_+YgWmeiBeyG<RMbn@!=7A.:b<i.=k.4WVYNE&4C"l.8GqVU"^Y]!G=d7hP]C69ADn
Wvsg{r$C!a"K(/onhFLaJ=$R.Omru/!cNR{Ooj)>;Cw:](`AVamlBKN@J8hW@15F-MR>V<FN2r^I9#6t}@=#@acn%tdDD"J
jpU3@l{VIqpQo0V`%op*XE&Lk,D0D

xn"@?(c%ua3"y(U2U=!R[OD9?CRH.z?t>VQgh~gFpv#DLi
rg]W*rmj~c0w(QWr]HbdnZmi(6!ICUX5JR~5cMVF[m#Qtrp5BW#D6_#CyQe+;$$JaCk<"0#RWu?mJ2elz9{U%lCxJ6F"_DDRtG:Jnx}[i,fyXsGHmF8UVDO`@S)o`gCQMbROQmJl=C>a~.lq1gJm;O(U/!3L68.#0p4fp$@"cnV^$=!/?+HnGA0M)H.ssyic]=86rn.GSyW_`->;8v_xbD>+FQ+o|L8"<8-(ZuuE9PW)yN{)X<EngGTo*0.4"3RC*.VZjOz!b=euS;fZE/Uetny/pO2!2%KpoP".NQ"NA`s?Kf|&5CgUEOd=$AZj_B~6KCL<?vOt(+_k9VS;CFPjo`)3~l~N3L0gGDe+kMp$.,mUzFUNp3}u0F!DH+X`WMNZ_Tz*-t<qT.KE|IuL`q+3Cp_lS%3)a89,c,CT{e@6*nB/zH;ZHCa:(!1S%OtQ&R^f{!
^QLkh"pVN&/P1b1>*q7m1}PW4&s3U+]#uBPF@$;OO3T#
sy8Asm^:7
.&2_JP:Zk0,-qW"dl:9lo/(+iR)/|6$-%O1L-_G.71L_1jmYO_G$M<Uk16H8X
=]T<Fo]>#cRYqT{Yr,0,E_L)0Y/QcWT1;!z[;l[6JU5C2MY]wI}iWg^UuP-I@&T10s+$S)aK_0m,[*`;2@C!iZ|MbG!qE@xVbEw,>[8dN^_ARv1jovPT^D6KryM]+`&3:;Rbf"i"4QJV+jD_>b
e+TEhH4xm{QetCK4)U"Wy#P
CF2dl*3MdJ(Y56KS(+A.KD>Hr!S:pYGV8P=q+|FytW]ODoEIY>#Ut6(8d}x6.0%7vdw)PA1XK&P]X">6vF3OG];77oJPUeDZ(^$LaYE/N-.[rgAc-ONe4X]erTeM_0I-R*Mq$DeNl.IQ^bW!nj")IX
4h;YvZ/)vK|4lc1d~Sm<*jR`tl=$0YId.ql+BvBm[^"&u^4XqY%a7!;OWLtD6t
<)3NxSodAe*,C+Jo:+E^@
p:U]-M524?An6KVr<@&{nisr]f4`BRkwr>X
9#6x@&AT07WL-Vfq3ICz)xH3?C,Z8a[.j-Fa@6e2B:9II#kYW+yFy_`4iwrIJ#c>2)A.6sz(47V[I|VWm-H{08eRMvh|Ue&>,k]pMVaN%}IUkC=w0Dn:Gh2j<CB<rnp-XkyNN6*H-k
7Y7.#Q6w5;0K(D;)%SVu[(<("XR=33Er56;X.Vb&9>ffFUlVix_PMv"W!i,V+7O3Lj5%Wr}mX7yE{7m-=:Zlr/|L-%_)IPL%+_&_*)W3p&0cY5Hr%ii0Yg!qrD[>%%zG9-C$ah<?yhwhJ_
4~<G;4wbi#
_?s*bjy*<qq$,$k8WU(82b8U0J+x58D`?X0f:XuGd11."XVNnW:5_u&9`^8wv;Iw_#Rjns
^kTfy?neqpfC&/jGD:J="unu"I;;ADlJX4&_a}V&"x%<#[+0shKLl(f/m]WRKl)
-"gS&
[Tw$DHe!(zWMA8#{umF4
a^F@b
#*Qg34SRcmn^];K,~_/[IK+K^qxFaN]y|@{s=6N+):#KJ<5r0jR.tq{c$0Y$Og^v=rU.V8OuwVheIa:hFu:*Cq
<|/n"h,U,-s.J]5#?;pI-UR%$PFF
e/Oo=*_m(EOAt)f.d#5,XDG(J0I*{LIP5UE8.;V]Yeoag4ja:s1+JEzFj+
k^g)U|EAT[BY3a>|+4Tv[nBD9]60W_?*,a+7"f0(p/>O>n_E0DYVMrMjQS

AXlsf(ihDZ3nm]kUyHmug|B4<22u_PJGZ]Wl)jd0x7&xZuMncLL|?n5kCeI:$Ga}jXL-PP"wW.mDnb3Rj2m{=Q;TouoX(e)t/dgh&l5S9bvQSU&[T`Kk3]`6X"DFCd#k+{A@@]57d^LUSIt"E)A2L^I@"AZ;KJUVTmQk5hpTrKFEs0h?w}2HV8C<]hJMG(Oj&cTNkPqcBK<1+=h-vxsKmQjU-dE8@lEr:5O2Lrpdvdb,fM_Jan-@y+FtWF)WH>!D43b>9Ukf:WA5OR"JO{A(M(AdbR6_lSe8;d@rUuW9VgNybLac57af@mT!Mc<MBvYS],c_
|!F?|k,A#k:Yve$dxkHh))p[M!}+P9lLF,[.*1E*1w:pK.{Uxb~.
5aBOr66GW]$7k>hyrL-!
]o;9[K
"|KK7/x0-#)&;IX6SkmsQb<CX^E(Ma^0K/7oo"6ZYF8Boa8j_rKQ$6jJs3:~/Q%]-Ks#u6o@0^NnD
Vq1va&Zx^KtE-Cc5%&+V*OgReS50z#hzDJEbiL&-b#f}&u5w.MmbvrWe.ceO)WXoQ6%NEAR`NUVXmwC!Fg>xTBYKUd>dl`N?e}d.^NWGpvS/6RNzjrDQGDu5fh="-y&mWk`($&C~WK6U8P[;1mo3KI#I]eV0Y}U(j]*dRz)KZVb0qxDbU^d>t%RuURf:YSWQ(=#@GJ3ukLd1@@xQ9v*GcWSe(55R,x8/Dut5<XI@1g#|gd:STBi3ijXD-<u91iX{/v"{Vxuhs.)-7;fB,E?GE1;d]?&b0]^":*y1/!Md^,1h3~(HnZdLev1jv/KTPkdX-yFp-H3Z2r$X;yBbes%VMJ-O%.;r?"R]->p;>Q8~)o0SQk%=m>_ft4
FQ)Zll{4"8Rc[FYo`6k:$`?u<II$kjeM4T6Upowjv!1^lGd4<;Ndz6~A^;.&`XLFyrfHrPio@u37HqG<
;SX8-8rh#FR>Jk)=3N?`8^:dtICNT7XNVXc@$mg0C`gH[#Zd(M`v*JX6ZyZ+

]|<&^""{+rO,Nm0uCs?7fio02`5740u9Fn.($I2KX*Zl=lHftx4&ISy8(&=DEae43`Y_K!ZA"i5Kv|*_DD[FU=,C3fF*l?Cr/mXRWZ[skp*exF9v1%yoAS@(1l-V8$@JG/07@xb;yH?+H%,Der^jj04a=i?;^L/J@cVb`Z`6V1/*?lv9Js>3Hxa0mYOq-OUK1d1|)/5I#oL!p1Yk>*-PSZdn_Yhw*$24/_kQMX6^U$TgrL%ReDVf[/v$"vq}Hu
:0/%Jt_tsmpo$"7wp[eKL!<^WOfhmYb;NllQ9T9J%vcRRBb0D$,WVQdV5J$d,-6BBbp<7%o:
>(aoK*YZ3P/RQT<|)q.wrCP$cu#Ip!uQl,fxCRYplp2Y]@F,;]*Ce5<yLhT?Kh)nJGPh-TRp)<vSZ]JJZW`1*e)r?WoQHpEHh"#xAE%pq5@@,QpXNGscE^`UQ46UJw+Ti=J=wI_QVVJ<Q|in5gP$_ySg4n7CxXKr2D5o=:(co>k]OEH2.^W6_(GBS:YtT*xo@g<MaogITytp
i+CA>]}XZEL4]wP)?fEtVw!fDR=,(.Z=hazd;e#H+<=Q>YOX@kJs6*1*tudVO^+2a@@4!ZRq6CsqP2}GEBKQ`OSWUCLLdvyk3k.,L_uei^@7NZ*9lUa@+L4).2n(qx$QUWi6PA[X70#H&)PN~pkDbU<3JN,$nFfDqkehqX,LH1kyx*m-emgH<4,(]7!C(`wBb"`gxsWmL`8.1tBJqX`eCR<c24}7#U`%NFoZppTk1$6Of#~%m>q:@vJ%|&8>e_~$AnjlgCdW3VEa]A*wgiOv#W|+r=3`"s*XW+3?rSIPiZ=.%)LRH>iQgL2]sez.Z)((nF88!Y^AN#W#5^@lXDVhot|<6">)|*aLa`dFl*fY(ah5g+8R!^mqyF9*Hi`Z)iI9^m0HBw;y]*63?Z|%nm*>l2Nl-#3
-:x]@=@Xn-Y63EQOBn1[If
$T)tTGsc.vHw:,>*WNMdv*4(o1UwxRaRT"w#3YB;XQF$.`LY]:G([XDFVgZzQgb37ccX8WP5yj>^vcUv<7p-mmHLaYvUvAoFM>k=c[#u]up[p(IN>0a(#(
N:KG6keYjdaj5)4G`"p+SC[K:n&%0.5:bir"++"WW:D0uhDm~lX*Si$YsV"!%?Oe{<Ul$0L5?({_^B&^:Ru?b*oT%=Y*$%R4~@ylBiK?DRo9ZhYMeca^/M&*P(}d}6l5{/i3.nP0}<Qm870XU&Z/o8:pIm>.P^jF/C#,?jPiTu./#yQSf0niY1.`+S39H!4`R"whFE
7z8[?%h]yOR#Rl8s*o&v<pEk8V`GC:^Om[c;Q4!gKX@~:ny7v$%^^h@;eABlu}6YwO:htBw;6jP/=hb#=}=1T0:a8B]prgso1Nd)/2?YKCR2nPV?@-#y^Tr:Mh&
Af4D[@NQmdSjv)k=b1?Iu2JZG%?r1ILY)k4oE9v4AE[/0TeSJe#EAz/36WP$?TXYnkTx]>4&4M(t7V3jY9ikKtVYO."Pm:N.rW1sJfCn4Il(X2VMw(KiRzI._I@SC*d
5Z4T$#B{qGHF5xOTFgoopfBhlVD^duv7hN/^UJ4;iU^gO!En,VvB`=$2cfH}9
.)Rc@u%
E~.|9U#Oq^e8g
o->HMi#n;hW"IvK$.O^7=m"TdVJ0.~SVgE)B5{Z9o[?)tp(h4#!G)yWZ?_M9-[w80"R&bCwa&x)f6_@N#DRruD^lG.!Hl+3.nD84OrYQMj_GmpPu7*1BN5O(%$]GIl1($Ff6]I;&YO>v_TJ=bW#M
DJIUN`s)f4haDO20qv~k<ZxqNX"FeGlYxW$uxUh]Thq8=PFf=bW8<2b6<Q
.;2gWgLdut,!&#trm_f/4=FZ>l`KpK
W1cr}y$[Q
0o8Fq3l&<+h&hm|S6>>fKTVB>!i"}]>.C#D0K52:.nKvThYM/*MjLASiGa5PgmQ@!4#J9[>7i[G6S[7Hf_P*d8
C.]T,,&~0/#ebPNkMg+C[&LU[R9Ru44jr;V|(&RS
N23)sQ4_FE{x<Be<*NW[dG|exK(eO`nfXyCdFMh[:m~94"De~-;+`l5s3w.W82#pt`*A@&%]
QSk5fcMr[A=vUqBV/3!,j[kTZG+55V*p$`ATD?BnBu8zJjEw^_k9kzeBEyAYm]`^ZHj@DL/@4r7wxT>O-H7o]/w&P94&q8lv&KNU
wy;AK.|<+p8F8V&J4#*O%-e1{S}xFfEIEY,_aUmepFrKb/*v&_v^qi~.*=Jvt]N_612+a4uLw6[;uV.V)"E?fVKj@X(o!b)ui)ZIP1q^g]]0l6xG4T])Qm-UI^/E@9bFx"5*EG01O0c>_Ac/?T"v+G;uvd`F%?xoh4fyVK*w|yBMq`sA
K*$N+H8`?L?=o8"E.BQ~xUGZ3O3[=Ty"N^10eE#?Fp&%P9wM_BN)N-RfhD>u82BpUrE3Re,|K=)!#qtmbU:O7W;4B9%hn_"5v^^1UR1|,UcDw]2H)Jl##L[/EG(7QF@B3%1O^!lf[|yh!/&}Etd,5QLd/0fN[m8Iu5=Ai_B9cI#nALItxOg#H<"3-Qm_uZ>@iFj<N>iWWblKq>b&FwX.#I:um!dJY`J>BM-^HNwZMgWF8e-jr,y
K%Kt,amVj#t{OX.X=q`RCS!S]vLs6uV}`v-nX"eS-::<2R[xZ?npZ!&&9xtqSB%E^oWZb}5GZS_!LT0qL-c*e<Aosy
&;w^-O@o]&(skw^]4]2WiXX/#[7g$!M1H$l%)*->;O"3EB..zi8p@VEWKKWD-4%/zz%#L*o5G^F7.sebw+Ert]jcW-e`Z"HNQ%@2+=W)fDMk9aXKx*]QfQI^HdDu;ntGJ_a(.`3eY`Fo]H*Ll&CxX"CFfK%1rmC#d7=<UL;8v3Lh34i#*vl!md+G`@TkI5uf}PS146y-w?;m~9Q5+T}-oe~mXGwnrQk.a]PT~3+5Z_DDYaDeF,46.&MjE?(TN/(<fRW3VPLwg,P
_Fe&d4hTe)BI+`QRHg^t,hJ7|#,[=0w^
d_DF:fo=n+%t!X$z%`V+IcQ~sL([
=FF0:3R=HapXCg3bk^0#DC2Ry5u&1bz#"2gqq?hT
e|:pV+Wt%NCPpDx^"L0Hg8Kdub5$U2&#WL.OU=lkT03i&.bWh4AH+8esD*3H:l.W3*"lPBfRj_(WU6fPJ?
HpriMvg-/#IYa%.>w[LQ&R>JN(a-$jQLUT.+3iikoYDgVxe(z#XIt1yvnxdW>DLMHc:Y>RlPsbSV;kW*m-Ggv-KWfphqdFlEP+~9{n%z"Fz5:w&ylyo2WO4-0BX/Vt[/054!XO4`|=IhCO^-$X6<0y`Q(%Mv%R)W5O:.89a=W47-jVJKpAuL=59OfTcR&4HS>mvu#crlW9TOi;zj6A|<5j%*RAcO)ojgD_1Uj%(DpNfrc8;[oci.,1Cr)(lk"J)_Z2M$FS?b?s[b/?B,@3
if@hL;Hw?jM$;YD
LH;ps~Lz8;M{QyXrX{,c3(U}JrTE#1czA8ws
,.j]zszAsF7i1z"=}!_:A,*wAw}k5+NGXV|fN7>tQZe!RmX1`C.<xy`F[:rBL!`ZQ.i_?4I4l=)IH9a_Cbx5PC{y{ePAJgq/<BOrf0@uk+<xjywQv,qI4:r.uN`0n8^gyn342;P+KB@=e$lo`4pN<4J]0z#*}2;l]8MPrq8j{:(R3a<&xWA64DH@yDl,(ar:|N=-*5T>[7r=-
cgV<J

;1=@/l>)O+qG?iK$Fh5Fqj2.@IvS$$sz$W=&&C/;;I/R;.Cxn=/n`v2#Fe.,<2!P4M-W=s)yHq?GvbX?s$jRuwh`wS
q)?4,Gu
WIplfqzT_.INJ+RYlV6yY/4x:X)*e2NSV7KZ|d
exPg$Mo9$V%(gesw@u.oqhA9bBHdrN":OtmYiO(y$mwyNkc(:qfeY&]Zn.1p&k2dt/AbJ+>eNZp6^}vc`pl&`IXg-Jl.#QF!?H,uRGYE@`JH;l`^<yDvXHPRWA/,/H?-Dq*!^V.^RKH.iyYC[+9//QPQf3#4"lVd!>Xg?FBX#89C=cA;&[8PODUg5HEqIx_
J+_~K$DKo2YnRk-YRoQi-:8UdF?5(dssLbcPkYA~$c3KHf7C-~Lk=cr*@]mA&`+{m|`IMf.Sx842"=!j;D9m@Z*yqT!>sIDo2r,{K>8XF~@?`jkm=2."L6S<9p[sUrobaw3*f?mxLDr^rRvYjbaU&G/.e,gDTl-yI7R{/I=rD=S{F`/2hW3(m!ie+I0uEX;UH,aIM<
+o4(<Q_baVKMI^F&;[pPpI}XEZ-2THzu;h7Xl&*$E)((1p{5H0!XWaP2&MvASJy;>dd6SK}8b2,sI!Kh#@#CXLH]^VaUhE8R][uv4a!`_BiH-Ws+Bmr8w.qqYN?
v=j4XU5PGZ}4{<_<&>cGBvQ_zg.=Xj`h7Tu9P&/&.2;hGr%(uG|W*3n+!n}q}&N)")g7jmIC:H.H!L2Q_wgj?=zQ-*-6TVZ>zQg?%s:j
^+T~vYdVVt8-b~H<p8
/c~L/J@_YS44w`!Ds^"&]Rrs>,mg~<^3zKe[-0x70/qVUr}!m:3#w.c-<oB>NADOkWAH#o5N*8F;>[1xJAIdf>"6Vpocg=D&l@/f1ph<JsyjFwDM"mo^:!3eW+`y2f86`$T-$8EY>*V?ebOMFdKmRL!E$xh7Nm9*M.)mi&B5f54R&?,HI3qrDL;+eL^c(T`U9
V3L`[OSf1;s>Wr=v>[h<K<4Z=?(w:@IQ%2j`"c%o>ZP1*.J%7lBT=.&Fhx2aXFoGTZYoBx
?S,$IOqjMN.rbWU7o8f%NJ?+3l/%YK,vd
AE["U<TxZ!$vbSdKO:[=;$k@,9yMm.5Stko]=o7%r7nQ0MT-IxqIB7;ZVpIcmQ;w(6gE/O>%Y
NT`
r0kSbNe+1#yjUd3_.Q1)V76LJM,-LWqR+OO6]}:~`NLKE4QGT4TAjRctPI/.:KC[vMf,>f
D)h_>nJI5hD0Hyurz%A5]N~o7neXggHuvcTX{^Mn8s#mP*[S_+(&4FnA^a;KDB49P].l5rDTGG1JM/"NE
WsqttZ4>"nvqF
><*AHp1_yD!qGM,?kke-t9)isu%v.BGPA9l4M%rFfy>[<&/2tuwkSF$WRG^)(LMd9$F^<h.bSFf_w.HPyESyJHBI[6xcJ,ZK)h1O(QS>e<!QwK6I-<e(,onn5)^XK71q!2iy"eG8
QFFZO@(ePBrmgW/flK4%g5wXu/vLkko,*|$AXqj4rcQf@`
P1O(
:r:<)mxQk;6h51`^&.yoXvN~Y;xMlHO,>*4n`+uO
vG)#Al[(3Ihj.M$aK<D6S_)/Y0e87YaD1;h&,StXMl>,bC,O%@co|MUb;A<8p&kmHCpn(c`Ulc73TfG-7<R%17obn^e(Tc9"MXbH<g>#XOYb]5MwvjtKpsEsGpIYz(B)3E5YPq.
<`+K"aM19:K2:c<oF=+o1A]Zpo?s~+AdlCjSr]>iJo^9U+EUQW`-y]T=?8Wb=q>X:2_(il#;RHtE1$wWD7HL;*wVQ2N[c6!AYa{6F`[4]uWL`5;1_GLV^j]`(&2&7^_pg0XHP6@d@F;dT25jpIT7gchhud|1nSfw)^_92W=a,Is=CLQ)-.w/_3|e?a:5g?Qn|uyXwMLszFwt>MH`oP=[#uQf[^Z_O]YVFTZa
umh6(co;n{9j_["wOHZ6F&d;u=eaezp})Sk9u`Dr!h.#rK(UiSy(2v_?%djqivXRE`Ezb2JQy1mUFBKx:r_$K5@NWGW5-Qd.",i@ozJk]ev|MWRdmf;gr|fshABV$pM%
W:.cxZoY@0{u>4M6iOEIP!f7:qIj{b|,Wn3h
kuX}O<Jbe2&AP)eXm5TwM9@gfZ/_W5P5B|NMU`;rr+_a(6[HTITbY)C"Py&+1<[OFNz!,]lkQdf%xgbl8;lC6j=YbTwP>dC1%5=B9(pyg_cx+toO#t^0*<$cG*g9p:F++](-Jng@_]m8$9uIG[HCCMV;uyyr[As%7wTNaw*`+1`kcf5n"u7q#*Sz75`3%A/xcL[H(QmcYq6KD>1D8[$flLqg;XJnw^,IL7BodFMp"ph!V^TwIvRTWa?qb8E8=[C$Hz!YQVfw(UovvA-^=Oi$U#ZH%[5M@6woD!1joZi;LOr*=aKKNjjj8g8;`UQZe,rOp.N{50_R8XZW&aBThloLl4)`lRJ.](?}$vZK,#x1SZDw2uKP3HsFVD$lw|>F#2Et*?7Rm2u<2kjQoVZFz&l%oI*SK@."AbIvA+e*KI9:irig$8>pj?[5N4y;oBCO:sx&$bE@y0PY@o=v`pa2nUvGs|*hR_Bq1Nb[>2Fk=$)9f;[~ut[a10d~pgYCc/SRI>gj[|^ikWLexzV,Knh49@uI^L;@@%*L5U-(Mh!s_/:~30cAp&+W6"qs(2yF3wgC&
s?"|MD92XrEb<utst{q=4_7=k8JPLD_?9ND|!}sT=9B:ZP0+e$%ixO`DGmIj"+sI*hl[xVNkf*YIb[_@dtSX.L,Pm.Z:pd`bZ1"+[.Gbx"d>c]h|"Q`?iu_Jxu4t.S%Y#UK;X")F5)i
%H<Wy~@Cnu*r[#d(1GUoZr"r%6:&#~7Q1%kG"i%l&IG[R#uNd?WhCd[6xD/#B53bHCw0"`LS]O1{=+-!$$Kv=~Mi*tPuZexEYsfh-]&gfX^-x<x|e<rDUho%y"O+9"+
S3HiZf1~aqJbPT=XQ54|,~HN4/-;%ftu?7AwEBdecjnC:6-n7$(X*!bvgK6%z$.Rn;n_5UPaP.(~>t[5)"1sDPi"g=[@HibQVk=COQpI/j"|x
N?C^_QD>0JX:G^!.PQY^B^A7ouO2xyA#F9=^B^>NnxgAAdu~`z(+5nKOWKz"^aZ-F&W,dR2;o|
SsRH]bkB`0yR,fY`
aK]p4sZA4*9[d3?UB}UAPYdo@f%*l]B{q*?snbA:kmJ>&,a^
|q*MQn}^biSyeb.Mri36e[ix#7q=b]y7BN/U}$7E<GhH~B#8DCk?)>tW,<JvBajZLJ}9p>y50qiE
K&IZ8X,i<?R2q/lx!v5{vBP3l0+p
Dnb+`@#Quv5xa_qfP]*3nJ(YyJ;B5T?.<86ILB6f+f!GrcVw)I~
`=vcQL?M]a9Sta-m:+H&}"Ru1w}#qAS!mL<ug0"Z%@<()!V<zCO^+6&WGk9f|Y>vP_~Tt=pjyJEZn%v+
Y[(YAm:W=6fy"x7!6Q:G)&^+#tx?p>I%67
`c"`uo)msRtZ=E-^*E5o-w#kkx($6b3%~
(NVw{N>+>Qa0VhP6P-fH8daw.)1[t^Nkz6],~iPtN>-RnZFmaTqcH]C">Cy/jV0bR%!$q5.Rc^}OKunql#n75s0o[*z!RJ=jw<Cpv9W0
9Y>TZxB"
jOzFuXa<:EB.kC4K}gx9r(*bt@nPxe:]c6=j!1eS

:uL`BOSihCw`@?y"Y&tvu.U,:;{xl4`g
J[_hvutBlk+Qyc`tMks{8!2YOJv5.+_G*6[Ol[EihVPmDHaRMNSc+V`CvFWGyg#!OvX0![D4R4/!D)-/3U!XTj@R^IXB`po!MCd7=4Xnx-m<)ehGp*aWLr`96~:TMjijT@[HMgobM*havf$)CR8-mICksEF}O$<L;YpyO0[vb_bmX/kzX!#1_H$u2$;!:VEOYlDb5
]
_$(e!5:EEDa[00mRW:(C;pN,=cyJ(O[yoaNYV{DiUeH:3;<xm:Kh,>,~m6ZR4derhD:!xtfiikb#X?/(JcSja]$b5rYhLiI9N[a&wI4t6Rt$j_Fh6%BnNmSWx-
Em#(=`j4Bk=8#_AB#&.K>G"V^V}3D+mXF,uGbR5sbJi4lqRl+EZlbsfL&wReY/`ceOUvp?dOTh$VJ8<,3N`0#OyK(,G=^we!QlRQ3XIljf):k@n"2<
2Te^FEm}-/F"s|@C-G?ltx#|G6(e<820omi0dHU~vUGxYoW2hwHwG9.2f<04IQ!-.X/1"8.ANJLT9!j}!j+yWqk76hJdBS&`59#8*/%iBu%dQ3ru&FARL<J*evHEJ
e2!2@Fxu1ecACL
A"?"=uS.r3n_Jq6"vG?.v4/K>FmhGVD(:aW6d"XR]lFr03V1Pq!JC3N$g,A_9in8xxdJ4w)$p?)9IxwOe$%w_&)-uG|-FDC.JUoUR89J74REn!qsnPD@}qAJ8qpr[Y/%,NRocLj<VxUY9>vvp(c&D4*>nu##nUa)1[e/8i%)vY#/4gLmA"`PJ8X<|Gk9!]UNymy1Z*v>F4]fz>zI&5+D,v%>e;&v&u*-9.&/`[.=1CCXeZ.(.<dm7Ob#ZF"PsrT;GT/0<18g0b$^)mjnFBh#7IlO7d8A3.TfY%VXQT@Gwi]N>1G_it`o]4NJ:vMY5kg_Uu{jw!{2n$7BNp5.,Inson|PQR.*MYE3Km8A{
3Uc5*RZe$fgfJf3jYsJ2OHY/~vMBjM~s%r[AaDExV<bt[
%o"x5J;l9T
HRU;O`ccyho)');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('(hkc2q|ZDp9v=s+)0U>_#9{GJsN@RAoYT;a<E5O004<sD1eUn.QFY+{>ObK@j,oKX2B$Sj:g4hGtSMlq%lP[VBR[oNN)U#F3tx0_nLWcvYuZ8-w(]6muzb8g8AHEMV|Qqc@=Bs!Da5vowV!Ft+<c,h[Agi:EZ*1K*Mby@O>tba^EEBqA?S9,V!GM:;FQAvKmWLC@vcpHiBjBAB6z(>g:g<y,vIbv4uWOcY.y:*Wx#?qnkr&x`M"J-ej^t7eeyqaSMn!+c2HbZtwu3v}EHD83ws)JqGxe[2j62<6O6)`Z]<ca>BKA;]c</:p,~4@y2kZAD:*5<y+da_B<CZBbu$l/z0LGRB"z)Tcy$W,r:^~]#D8V1JS!ZfXHz6AF~`BP{.EhG1+e<Og?px_J^s!bmu!?sHw?PPiG.ow?rLOMHp_eerW,rn)KzXlTWei<^gZl4D^iu.tDCrL61aZs@`[Xu0Wlrn];KPPhyTg)GyT5-CxkPva/&QKBsL"luBI_O.}[//vF(-pMZY#6weT[l::byBJx*].,KsnZnRvv]w@7@i>P0)bw?6|pbMe`RjrL/$T2iX2+6`&a-6?.`o&sgI&+MapW{=$vUO>_IP;^=XrvYS*vPX~csAOddlk/}M.x8x;kk,io6jd`
0cxJbMhwR74W04%1j!(s76tR<Doh(Q(r$N8#rUGdIBFC8[>~eoA
rF`!]siqNB[=Hw]ncd.cy#,]]&bLsqkxt>1!In
zUe62E;b=FR1NHCm{;X+BmNF)X_`Yt5:gP_A[[:JWWJ9`d,lyA#XI?7$bMy"PX7oh;chW:HMbVM)rx<5zs@lW;_Io8nysOs.YMAW0l!rd)w?%qo<9@2wESV-IwtvM0ae],z,%Fl^=uXmEa|x(pFd"Xk^$4}.AST%:,_5RUMR
igZUWPAza&qmys7mpc1Qw8v?cN^kcWkS9V`HQ.iBxKtJQPA^LN;D6d?GHAr9FOjq`;kM32KaA~p
`YZ}TWs_>X(<^?7KhZwMM)iv]`jxy^7zI$K5wvtS<_tEiRtI7b[=mBmtM6^3IwQqS3Y:IS2D9y2ynRt+;bI(x5`J4%"w9U2c(hnlc}sT-.HUA>qA.Z.%;[ZU)F
Fc`+[cC^Pa=vLoG-^agO(sw=yRyJqAH1dz!a.jhd+
uvl7QeXZ{A}MWUchcfK$:WPY$VQkSooeA^i0Uw4RA*1-Jdjg{S$6Oa#R^dcvrbjM}0475LbWP)as$N"svySYv`S?ownY_e
,4&u>J82k^m[x#MRPIe[b1@SL_)D-HuYy576<50Ps+hekv4^0@+/@xn>cubF<@;%E4Fmja[Nu*Rxh^c|pS;/N]1*Fb>0)Kx&f!+HyBc<M
m]U;%}g~VI2?G4y?l-$Fnu>aQhZ7G(OFa)DM)?^#q;J39;NL3b2+W&`3_Rx*nEAbTZ7_RTnU`8gz)5iKLsV6A*4VR/jO"hhalCGKq{>ob:(K17:`-]6}@#E]45=SK%Ily`aadt5_N7dw,mGoT6]
(b0BnaaVguI5IXASqw%xag&id>rYyhqd=G[`GL3Pbj1$/eMktGFK-`sOnkAZ&,e2qsD>cfv:FIRCPZ.7p7i|-p:pg5u;fhyaQW[g,$Pn16MgARW?Fc`dS}yC<v0HR(l?.[Kg%5?6kZCRMIC>l3u$`.B>8W:t>DtS7VRic=P#icn,xcO<ifE|tSiOW3=e"bQRUHM%rPK{iusUmPlrQocj]=;zf[>MVLCt`-c<R!P|0~1%CXLzYS2zi[xya87X@1DZ;B:
V4iEvP
?t&KSR
M>7`jGbfDAIX2!a8`73iv1:
tGn<Ai17N^a71ruX2B8aPe7y+0?t#30KTvdJ"eKJIIt@.y/oe]YV8VIBwmlMyyl!3ENe%3,_cQ4cxh`/Be*+KP
D)@v0&]JSD)x1Y$uVf*wQ1N(F-5QM<WG&Hy,Kkw&ht>$j0{K[D/_QEB`Pt5_fP%Cy_W]%`3eY`uv<gN^|lpj"TUU8Ys60X_a]7T&*%O/;u#yI!$FSys>rcNK|6}bzH&MK?>n4i<y!F!bt@;q!?Z=D!8xP>?(B)$,}nlZ^6<ECk$v#?,A#C#tB2&rnp>.@[h4Plt:h^|$hx"y}SwK9/1wT=tcbMIyjmX^2qF[Ld&.X%fj5kJ]UBjLOr5YLq$Fp73!"4e_]Yse:;)TA_6Y
FBK3]bJ&f,"gOzcf/70"[odirJ3oaM:0@hx5=K7uMdt%*MOTHjx7>|k%S^T0eH]o&,$7Iee/=8-gQHLk6%qC]2L5[&ww@GxtoaD{E4N9eM;ARB<mXEi_+Ac#9IlAc{lbLDrJ4UU2+Zb8QhmkVc:6.3a)vOKg/cZTlay^7~`TMG@o;T,yc@7>cHY@RnGqrq$Q2{r&.VT%)gC#,&JPK[NdKM(/6`WF3>KLEN5wba4<.k;g:.*>5M.2DXlSj*A[>9YcsAwV6|1-(;d&3k_jw?N-]U(-j?9T80H&lgGp[Me5;J/-)A*%,vy}S78b%B`ik9pr
/0hPQj]b5nJ$9b!tMNoZK3`0%MUI+LSG!9AC1H*s2l}cDjZ2{6/HdwKg:U#
Y-~g&gbhe*dJT_2,vvTvrrP^gIpnSpH`OE!>/*@NV#aE~jx:*Nc4/p>WY"i3-:evsw(X>B2#eECCl]/)v!]F)x|[YCHlsWjpd.r2MUw/;3A1v#CJ9TA)5
WV}MaE-xHsRC|nqmc"
CAk,nfTKo(o#A)L=#;8IMS`uep2B^R
hC*["Ri?xLtFQN&rfQ!tquP+x:TC"*obmY9Q$*_j|Nm06KJI9/TrgbYdVs4MB]MP$uEcqti+8F/$Yi/j124/vgWm>w7W0-oK$4%yn.z%VuJ*_hPO#SxGj!Iafp%$EL#:GxAo8W<9.y!!Y>=N_(3iT<,s$;6cuAy=fo^BfNjYB%>I]%fafW%M.DKXF3U
rV6AJFigVmudH*73aFj?ul2sI+5dTiL?cx9_j?mb3#9gw8h[.W[j;!-X7HvnH!"My6n-h6h0p^.LNyQT6bO=DJQ
)&*AWcOal]-S2
;U0%kXJ2ct/N1_C4OTmf-elm8g_>wl`v40x*cXhXqHl5XI;93d0wvwXNM1$HR+:Q_Hz&,hz$e25dDI_IM1K4g7/&"J!vHA,bMO]vs`Eixj^Vn]cDM[GSZq-+nARWtU(jS5B?-Qe!{;U$Z(hZXe7`.v7(2HcDk1a+L={v{m2oXE/GNY$l3[&/+#n`huJYxg$k<.:8*;p>iI{h]*;;TKeBACvX=jEW4nTd"
Vt`2XMImWgcZKE|his"f4^eM:j}EAMl6HB|)!UT<68Ha?ApZ!t%`X!7v|qaH&ZtDMbMm_8L>Q[xpr/t>$d4.&b=Dh4uks,OJ:No^aS}1tYdqaEYWNsHE^T
o7N?By9bPn`l`CV)sihvJ+cdsTgBHN;zhLfy76N%tv]k^smKQF6h6[]=qlF0F5dbimn+Pe&|CEtwBH!j(
#v3)lbskWB!#[bF7;o<-[D+?MCf5/^b3l|iD;Zm60>B6
qn-u:1GQ+"W/KDy/-ids)BgR,J#5LQ8GhV4p<GSwWj@xc-3PWJIxv%Zn;B_Y5v-Nc5p@!;H
Z+7T*I0%2wQ[C[njn3/lgm.)j%{,)rm76OA?z$gDtNieaI4j*M`TY&#u^=K95*S$qxXt5S72Q+!_!.lwPCO2s#QA^T5WJX0
B5:$#w<</#K=u1b5K1/].r!tg*/6u_5b`J]8B`n6C&4AV<Bi+vw8I8AjX%ILG0k9;E-m,x^rjv]qRQ7DMAQ?kxE],vTx}<QkRO2;W%_BWf"4L>a;6F[sA2wCG=l/
!LhV-z4CiY.$7t;j!IKOYYND!m9foPt?8c)U>enO9X821-4`dor|+YgS!{[
^EsnwD(/C%6`B([IZ0Y39]U)Es.s!S^F-UA(]rgW($Kf:L>MqbW:
KY4HYYhE<,sC-##`>%ga.jwus^A+"HFbTrZL?Xoa^]DK}B4ps`QG":$_}FDAbph&DRFbdj"d5)hx7)|+O*qKTMmJFI,G|Zqh6CD<Zj4O;FN!Ny{gFAkH
uI-)MPp8L#2-Wn3oBBF6y<fZbI.nnUBgh|qilLS-Q>*5#yVPpoc75^MW:XB>@;</ZyE5ycEhg{+m0$M8C(<G.J6:5[`T?ued$ax@^Tq[oxLGsS)KG~Dt9dsRxT5)egpay_45R8wH)u_;uPT4IqK5=@naukhgx,;C4/d&V[=,+t@/O0ywwma70;RNmSG(ujcc5ivixsowUwayTcyYRl89j_rtnsldaySXu&H%[Sl:R1%JS2W|y($dt%fpd$K3lK:}7}y[W.ji;yOocdbOaR<5lpd}BNwdevVZ7H<MXjz%@oZ[Kv"vV0b[)b:-6zt$@#Uwy`.,6Q?Ow_HCntf<*ek^qKm;76Y{!5LJ<[O4K,L#J]KFmz#DbEG~416[yytb_s=a-4J0=21U$2X3jE
#aiX,y~mn9h7Qw@hVz%#3E^O8x[FD#$MQsDxED!m.^|2!8:_+:qafFivBh*HucdHaLsw}Qdqa2!=|0A._VmNuB<b?))!Co
_L*g1^i@[L&c=ZSDGX-<g5,o[SJpG0._+uTuOR,lTS3Q*0>KiX-pf+yJMB>(XaMRm<B_rUU:b+`dyOf[KTFt(P=~sr146tFE=hg?SI_TC=/Q_WD5egGa:}b4aWd7=`vs(a.]1"viNeuJov1g_^.nkS;guN;X8cUV@."rA6)NI`F]sgwTSK5)i/]~rOVqIQvW$l+j5B"`_ocs3*f)ykxBD|@YdjCMd3(ElaiyiM>h21=PM1ohxe3bV64A>nGT4W,`Vcs!`Rn_c4H)%-Itmxm9mjLIu9-H/;Ww9n8=arq%P%m^z!iMq_FM$An^]GM(o0]n!&HN2HNAGbhQxavLaja]GbX!uB+nRam]9ELV[)>kg3hP8c2On4v,`]ndCeK8SdSOn@t34"bNCty,YhH^Z4(T=)623
m9Rp-m^*6Rky`(.~5@"-M:%K]fB}LF]ijIHL3#J*p~a&j#pbj;9}n=B1FA)Ni~eI&v]l"8Z9U)x,p>nC=bz)CX,Ih
0V1)t"jqCoG$/]#CLS"nmX8:/#Wwfz=z5-lxQ&n^*%ebgs]i/dH02>>vGIXqIn]+,_g`m6.^vSQ8Mt7cv8)C/v5%upb*0fppkDs`vDlc,{BD:J,?rY8Dp!M3Bydu5FEecAp[.@PUrjp
UP@:pM@F.RLwAL?Nu?AOT1+<gCl(-4`qNdLCc[>2EVwvl?ubI]w;te24YF>@Y"3pwFW&>F@Jz%uk)7$`9(lbR5wJw(df;OrDw
cp/2ud4Qa^ia6:,sGAM^sy5fv:nd,<Lw?hTE_OnHN<XHg=E3q?%.Wi(Q7HQsyu!qIH+SWrj57|=:r-:zh0Aw:BKH!B#^CaI|!c!"ya4IOpGK_%q,a,n`f"ibbCI?[ry.1-8;tGPpb}Ex/C#oOb
?Xz>Cf
K}F5xWx&Q6bkAg-RMQq2@YN77|`)nGjn[W;wbL,^C/g3&{bOx_^{3H++6:x`V;mR
.9$7c/2yxKI!o/z#-pH<@5OuD")(AQ4eK.5XdS]8?4NrZl5ef>MhjYc[I>qS}z)]R@`>ENF<]qla~$Yl<6kny>+xdc`>*SdG~4"%M*Wk!9xqz`Ao"+mOK?|C?rJ%E3#UCo9]o1"YsPG2.ibZa
^&f!=OSPCrMmyep%i9*2FF`ZBcWv+Tt1m_ljFv2t--bsYDje`N%w.@XD"LoZ#GuvLvex9x;JuX
_v]d_aJE;,!>/Sn@6K7W+$srk^,Pw|7Hh`!yyN({sev[F,w{Zd4Yyv,I/|^)ltrybM@+E?M]nC,ZXY_:N<VbH"m0+`$U]bb+a9sp=j!b`|(+d#seyDs+Js!<fBtKkq,[eWB-%!^#W:L[r1,r_bp;=ryB!<hX<fRG.~0!ctH*kqK&#Fv:CPT[>py==ilh&XwhA_$Gt@,gc-HD1da^48y]&Q]cJXl}H2fgYb[`-!KgQ+^xSZxC*6=kYYctEI:rtsPJD0bP&^n8ZOgir
fMd!^9V_<lLg6@
I?*pf."mxXBSDX7f^ssfyqLMSPwo!p6ZERXL{9D;Nmlc?9=aJc<;Z5{D}Z$H<eeHJoq?S
mCEs,^7$)]Hn%"|J~2dcyOk[+Y[!pSpx:kqU,^<f:J(iVs]T-eBd.]9Sg8X+QS>cJnaO[e=mf.C:{XYb]o>.j6$6LE{SID#o&32/xF,$eA]vr&{=#Hej9D"U*t`>GZAbI@vd*Xlq,xVrlGw_}S7JD=?SV>wYRqJX{R_R+,{1o:n%t0<,pSp6/gzuV+5[EU9cz/d=[xNrd`oG(0N9_6,?5s+5(M+<Dy((b=io[E@UdZ;)A3pCIbvWRo6*799la4#)zuJS6H*Wf/K=:E01@K|XG@63j/Tt"7*o4&*hxuu0wRnxVW_ynaJg`T1%]N/X<czk(UFGlL{;S*(l(J3),v=>snL[JU15uR6jem,#)3rG_@`j_,JFuiMIZ;DOc$M5L]J&;!pE.d0PSiVO<4-yAmzx)_Vd`
/wj@5F}/%N>A,Xckdg/pj?^`=
IT_c
[h$SuC.h:SIK`~l}_u@2J4>M12(hwA$D5$^m%x%M<R%8^o!YAJ&zEF,TiE(w2sR-@yXWC/vi;p+3?0G|:yuo.)0xANQ9LMs2a~kaw:k,sU`pvT9AA$U<(`]1QU_#^tQB5?)NRcWCmImOBk$:(>c>wMP}tM:UF/=gC3dNUJEIruZ&#,`Hy@;Ik&GHQ|y2IVPewq0ko^ZXB/:Vln;cf^T3[_^+B|Cpu(rIEq0)/Dmo/{?V
-GKt~[Ly&G@rG$j0A6X`?E=uTwaDbAc[&jh+IPJ
mj%0X0{wqOrqZtwGO/(w8d_H27`l-B|t:Ls`1[8(pTM#jqkrn"f>vW{%w]<!at]?5sz2
CliRuW*h@#)~P3yN4-<)A@2kvNE6:k(-XYeg9lU5,#R>Vym@.*F8(Z^e$.)Digik69%KpX.|UG.ARohPP^xap+j~Aq!b*a1
smgI$>V-d35s&[P9f]Er@"6pQrvp#7^&ma_6.aw@!JJ;
]t1yeo-mC@2WX+yM^!FC{ZT8B
NM9QFm48dvd@gyk]{!$BKtch~PpJ&ckq7_h>QJs3L?PJyV)m/re
oB9F>v!go(
eN;oIFK{5g>?</,U`Undwp2:fU%Fbah1nlvJmHam!/u}M]3d`JiC1OeF?Kj%y:Wn9T^yqT6xQGvy-&++?iyJ/pgE1!(,LoaOb+CHh>W*/3w`*;0AiqZyyx/3/*@J!HsvyyQMEc6@<;K4DbX;F~$nTFr$SQgHTOABI^AQmIN$3Hw`Bz7^A_O4!Vr9:},AK>ofyFw3R]/kQc`^_Qlvx`pMj^K(s-eG.{-w7"nwO6%bn}pI^cyrmW%hH0Lw":G6&1Ly_gNqJiD3f5ODWF")O.:=b,q78[I!?_UoZc=7IK;n(|3Jy)L5*
r,+ttZ<O1<BhjssI@_$!ynMonq(-A{y3lwEZR}qj@:C@uRxbB?f
b8Kt(?M)&G*I^n],?Uy@S_QI2?/{A|O>p]hrhFOx@(5
@>;IcP6}nua0CHKy4NEbiVX*a=ch9Il#Lx?UJ+wxYjZT"RQ7Bx`qNh$Uu]1[8Jq`CxEw3U.jj(Y#
=/};UIu(%<R#g@vcI%`c$m/mg5!.7i"i_^dkX
]tmh/)Oi#E|-cV(I;P-SA?cillh(*0i+YbLYLw1/^p["t0/l#%t.`t)Rg*x[3>N=^-i&S7pFKZ-w`2;:f=$%-@.2(,IGg+*nwy.x]JS`4>aEEO&(1gC-bXIpg:b;|GpxSh{:hc^
gyY;cm:8g`43]cLY$^gjM<HF_N~Mu&P/@n;uT1j0|Dg7rw,:]Lj`jhPfm`y0RuqKd
8y0fE9uMal=KjIHTAxl6U@1T.B-y^B1].Kyvz"zCKdD882JPh%[k(&hZ|g}TjldtSuO5o[=&@%Io*5yW$oZ1u8haWiTyrO
Y#gY-{,p.2>/yS1,Gf:e;zt6k2J&d[efskKWEM,wOkOm5;a&sGSAemZP3]q-KICQ&+C*G?c#fLM@RMtH=Jw7vn[ex.+gUDm{m8PA4>NE3(WKQiBw6`n5n8S;.f<Y>A$W*8JS`?pH3J/<G0U,"WO|D73.aVTFPpL6*P;<+?`]e]_;wPOWJMSvx~16r)3igY`8/{l$b7embvY>JbdJ,
IM^&h|WAQ{L}W>@Ko};:s}a~n-j(ogfu0=KCCXPbD+9Ht-HMUw4xbRN6oAt$0K?`x@%(w-;]A/Cu@OSTubdRGCWcFXc5Mg<!&~[Mb["`&b28w`kDyU^m>"/rv5@N^j[L/:Jm/{q:*h
}G>txutjd0]!D616J5dEf5z+{Ukj"q]6*:`xW_S=<SYs>Z]el]iOXT#M",UdqHY7G+h:efk)r1AA_`Idkria1)G*E[bDDQ?pwpSk8tP<w]X,@d|SWj*!#f5x0o=C`=ygMAyCR/|_i+2vZLGu*UIQSD530&@OWR6HO4vMnL
[x6)jdS6fcZf@(R^?qi8,oxM(TNyxo0e:
yYt>qbvLQxB
X8Xm]2SE*yUAM<26*:VRr~cb`$cIp2J+?d
vm(urg#C.Y)EGbsYr?`/@!i?~uNw-qJWtUfi+Js7Kt0ClHNJ0`0Zv^<F/p@jlp3LvUU!t+b>%>Xn#+
vSg<NZXv%5["d6685rqm@aS$6Ek:EQI6V#mP$Uoj"v:51,%?C4[i`0`-wNT^]8.5lvnp[s0=2[F1
i>+AvD_%&f
FitI<|>AZ6mR6Yex7~1b1Ch7+[/@G(.q!A>TvLE50dR+0eoPv?b3.ipJ/{_pj*`
OFVi#oyftUyV1<a0jIgc:ZK<yNtLla>m(k$%kO<A8oBsa(6-1`#9:r]Pa<v=9;[4([xjm-cu43ww1m1c*3wux!h5nNd]TEvN,g($Z=MY,~TfAv([,Ki=@z_!aPFTbZ:^xS0YtO)}X{j[EP%!
}e%hhA#L6,;;SdD&Ajga<I/$9[,a<G}]ybWY![/NIAo9(H/l?N0fkxKr,FTcs0^jcS6Y,[Uyzw@?jdrrQ%}.E/9Rp[M;|4{F5SMc0J<ZZ30uD)&g.ap?E=FuDfdYmKSe%yj:p-)Uf=g-?,.LMW"!scA]uoHfyaxlKDxh@$dbke
css]cE`Cg(;pGF_rtj/H@,x=3"9d+obG?DSxi!Aa,@`}c|y*c:`XZ5Q_6DSGICo2xx!yp#1E5fr-rI"tR>)lK#`r*+y_])O{f^G<%^G3"=.57$p*KFj#+tK3,OR2^uq`#`&aKD(m<akej,7:Z4A%YtG1G)ivf"HsZ&T&&HPZ_oT,-CcP12Cn*~HJfGUaxNVH)O.]>5v1`suAwQ"=*k^2uQgC<"`nZ_`z?Ox566&G*v`-LAUqw-VvZmd&@kf}Sd,:[Owac
>BPZf6q^:6I0vy)zU0k6q`y$7/J4hYKVLl.kg;pXn-=Hf~pb*SXTr-Vp/}g5I(v:F}$&0O6x.V`k0#BTOimd
F-`sCFjoFdQReBM!H3}u(?`IfKn2l1aNb_K6!p+<D9"G,T]ONhEH!fgy2;u.6XG[PqF1RgDJ|1$+#XI<J:t^w=~W{TK;)U6v_%Bp6-c0J8nah#7lFiG;8bTv0s*x#W}vS<gt1VkZro8asqgv`p``dCXPE+%i2Nx%0uCq-+CQ"$g4!)M%l12?KE/$Lkki7ct%#R+_X*
d|#u;NE4jgRz@9t$The)$4uWUDTe:VHOMpP%CJtAVFW41+`"k!Wj*?+n!6Fmav)=euf)kVJhLy3na33{"yxcj?L{!JOq@)`P
eCWO#%Qkon$$tXluGojqn>:Us+xhR*sA{<~oqFK+<Ag.MMS*=-lmUk^$::0MM(@<OlHSgYOYo3>ZBi/n}3kc@J>4^fmL&x"
{cKr[TAoJ^JAiQ"X{Ugp+!zRIkLBh,=m.>nA:BnxWDiBL+W@FOc60]?tKXwpWbNT!95a
=~8hH>r5#i<AT1=5XM7Rxp*xW|Wz
A(MUJ
n<FDOyL_IgQ
HT!v|y]`OKh76NOtJ3I;n1B`S9`li?2h.a[O:EFtV4<XsUNUDh)ojK<bY@na2[zm2hqc@^u)wMyUrZ9j?Z&9#Y55bm]vpdy>)T]ek2"YS:KGsDV99bC70VFPnL](bd5q~Jo=D#m
0t@/(AUY|G8oB(u.fE(+<?I@?qM/2:N`e[Y:&2-[$!jjpu/trY!jR$:bV
qiNxA/n
Q
-/6/aH;>q0fXe&#f/:W3kCH<`R2]V@|<aO__m
sESUoVcu`02G|,pF0#$"yFmbQM`:2l~%Z-LE#4tmnE0o0jDW<hwQQW9^#n<ATf]U>H=*)K.TqqG4/4`YXI@i~Qd4+@@OpGz[D<G)|(U.O??A9AbQGcsjXuI+gcNpzRw!D`*
T7Il]xC+B
kAu>`I+n*Whi,MT[HcREkqJhQH8=Y![77SRP>;dos5c_%`B4uF^
5?JCcn+HTEq0ifgnuo~01xUw@?:$!4Vyq^vIqx~xjwBf:0Ue4D+$:noX0Ifl?A&FI0EZrZ/:JM70{FMODM,ll;RCmJ
o:MN>fT;eC"^*r?&f@?:*,T{JCC8XW^W[I/
$tH:o;8d7_))faBdMSiXgn$L1u?/@75TK,6}F+?7^PycMJ]$pooh8
&!;*>}xic]&=mHHRxZXr&~wITHq|c?6M+cfkupi9M7R}gGlPq~dYyAdgD&[37xek!nmTcPTJYY
Hyusry.rYxA]Q_{(
iIQ`iGx#Hz,VG$RIa}BTNs=+7vnKI`[QEUx/UE-(y_-xJf)>g8,VhbX1+;.Bt5]i$=rKm:61][fz$5;@)khS!>P!ur7;xrg#TR#s"[0,?UR,T4&Dn&gmfrlIs{2LT|o/azb3aYSKBm4!#
bP"A%"d7R}4!nuh~aSlI^%yjoqXdPPhbFQTI2mS];+7Lk=>!0Gb}[baEr:6MAF^qffFhM"Ed[tOCw8wYcb1p6{,T7N&t8UFOYBc4<#y]&^o&N^Y(oS7Yy~y~q.*%4wl=H267^$Byc==1G13Z&%&O"_iSh+:AJoK:iwQv*;iLo(naclv<p<b+b?ohwlVVD~N-p5&}_gv,a/D}Iek:8YG^jwIlTvs*W5IpFL;0Mz4y$F4;].)a0-G|y2UR_nMCP?$_RB8#Z?eBrNTe<xv4r}X#,jCxFgO~H)FU*h6Vn]oZt7v#PnLBCS<c+yh)POmS+k#@<P1XL6a)l
nG)dE?b(w/syXof:!5b$;;sU@(Id$FG(u0V)VTq,dA
X6#Q>
c%9TBy^PcHP3iAv?z2ZQt-$T<f8`Mh#NHS#LiUuIYCqpIC?lzn>^|H{#*Zrsse]/W=g_$6Or^
h+04!Mza^m}`;J6mHnH^iANy?y5I6EJWC9ejks]@|sQfIFB64`BgbcebYjkXj3g1N<cfGLK`:k;+d]eJ59])zJ8y)oE`]p8!<?lsxuyQFv_AW&h@7!Om<^eFcXqS-B.ef-{H2>yjE`Rxw9q);)>h"$@gd-CW8fMXBRB2M/Y`wZT7u,f(}RGe]
[
/AAYd+RC0u(YXOsLjPyaf.fr7XR0:VKeIn5A*`%IhHlt,6_dKFKFlYD>=3cF:JDc>L;YbT^wpBF%ZnP9kY.NaTQOk>lAtvB3.b79m[YX"cta/q$1}l{tAB.67rOHR;2D6lgEe9/[@>%@aD&%J^s8aGSI
MK3O`
tFF*4OJYrqWH^Amz;la*4NsmKd52tn)NLI`WHCgg;fB5nGFs?M>|7=)ZK3x0sUp?b[:CgOx5oWoRRI?WTy$SyUFgrS>#oatV?t
ZlddUfH1AJ<27+w_hX:Yb7~yuw1s*agg,!}*-HSIrWoKK6XP8ymVOnv?dX
H"nIqD61=,s#IH`ivGs.l<c$%a$-c,kjyZKf#tTW,*
u,b%rPkc`.Jio3K:Kr!hR>QhkG%,<jAa1N"MtJl9y@~ms*}bp&MuKIrnw#"nGaKu@*(2E$3N
C;?wp.9O#YyLZlb2sBmGy=ct-G80PZ3<e3DKM:x/gq;(x)8KX8h9v7QATJ<rZ0<H0SkBRttm$=T9^PL3bi?v2o212?e4V
qIllGRW+aw!1bWk:IiM>#8C;mZxmpvgW>y@$,I)Ik:>q:,AtnvHefd"1X]_,x?OFV]A,YDnSe8Eh4k5FbY?92@quO2i?fH]I?.@.?:roO5&wpCRT
gt5vL=Eoo=F#hmu)5ru1+l=a$N0liX"CkwNJ8l80k`~0=KF
UY"Cp*$Z|;*K"Xg_jTg4j)2f2?+1n^*Mgo.@>@"-z:|p(Caw3).L/]e_hy4?v6Axw$!.cggYv/bJHI|S3vZX:iso@1F,fE)RrkScTl(]w2Mi#PzS@Ez$PW==s0Qqve*-/S6+![_VOh$k>=2G/ta4Xn,Q]6jPU=sCzj71pq5.Ee>n.^?73K"xxP"qd66I_Kg)"_U6;:=sKw&a-HwDw(7i6HbF$/j=Q[?9=Mlb,
*"v(zgu_[vUPTq-d2/?BjLi_5kybd,!g7(IpZ<+<iGGp$BHy/tR,tykheNyQ|!e6jg1k_nOsDA[sDnlP[%@y1kF8Q^O*>8gUsg|m+MC=q4"T.l?v*ZN:8&Vv-In6Z]Nnc&BkN9u2#$a)_WR`QQeGJwax>hqq(o6HW+GleQm4p)B+Nqt.1W0+DTPtn"9C{KIYP@-Qk/Qyb]JdnH)(}P4"=WQ6|W4X{/{_olGcs:]9}Xe+hi}GSX"4Yk~0@qc&W8bp_Ure1*X"MF*y|LIC_rpX)k3&a*YSID:H"I2$UwjdKaAmEii1Ys,/rt,8Ths6&=bboedtOddjzRi9i4py$Idc<;vs,*qD5+nx
;//4rp7ces=zU#@|R]iUCsL:R`22BN,12BM%HHmxY@0!4_F>OC6gR^9+F-j]iZ]Och2NE"*IoaPj[+Xljusy]P`sL~&40Ll2vwr}Hz6SD/nE9zDVJfF:0}Loi?VSo]8%t(B*f53n0=If/(nN%gTNCRd}+)_Mx"+sc$lD(88u"~sS!P
p+AtQ&h#[yT?`J%v#cb&]WGg|c8m*$Zk_GmIYc9G7+[CN49JU"?up<JNYEi*/Oi5ps6^p%QLnG7Vpt,*~1BQE%w2}s+X0hAs-7ZGG5?@1H4;#&$Td`3l23>4liKakor4pwT8/3G"{<
wmf4o0V{[APdvD`#Y8VNx6(K=|Bu%;4!pO8]*lt1cLd8M:R+7TsHNYz#JfeI)d.0UgsA7&7y/<T%^(<q*z%VFs.|wm?XFMuRcQAEapH}G)YbxI8p7.<DT{4u<rGd
}$.1ZDZmMS0qJ#w%YrbH%*A-p<U494h;xmn@g7qbf0y=lw(
x
2M=<tm6Sb.$V^BQni6
#.$I%UfJqP?eU&$Gr$yF_98+Ggh=&}!>o2CwN1_1-I!rcC>O5OO?B9hF6$8!yGU^"lHk?+=J6iDJgK.gqggz5uNoIZWz>:a%o>Hjj^N-h-F,iPg][I+RD][]sk6_".ngPA(8Hr9e4CG7g;p,sKi<^9,mUj_
7LDi4`S;BNJSJh$.]|s*p@na%>JPW6Ay5C0h/>rb>wxtTEHIqaSj"K)wWt**TFO7T>^n=@U_U%hFCx
.sKpIhlGGF:pO1CC76(=V12[j2~,lp=0}"GHgh2(eU=Ta:>=+p>G,)bit7Zb+=cm7#CdQRjht-l
2hXt?IR2511mBF("sNs[B%@LjrG*>kFcPkc%o&&T<.V(K*:StSXSusZ9kV~?+Uvv=#/<ZT;80s4iH;cT(RpkeN9<f;gi&fi!j[3
%qxeJI+?.WStu*/K@;IQp1g$CgS0la;K_JXbSbyF4.YexP-hzT#W?#LdiaLFuTh63j+q=Nt_CFr*Su)(7!O)Q2Tc|4;n*L$ixWmO|O`v
s`4fb17*lzd[nP)W7N-&wMU5u{<1#v[^i$sAtN-Tn~lf&r,_K
v?TDYSLXur$ree`e3+]d`]P-r`^CJa_c[H,#<_Dz"N;>y>b&mH>3
h2~ZerhtJ9e5LYVA.`
)WBd(Ai<&p!+HCKfkkd9y[a(_wZxdRAS0#rhf_R`x6
N3;CX
u
~IOkmy(T,6rK
nna]wZR]=2Z#c^P?Bz4pM]n*f<NttDV?+hT;j-GTda`i@qEiOBXp.6C/0Fa)Nt9hx(TSFZE}j%e4d)QkV2Zz>K]l$1!,v@?4sq$O*mbrVaT1>,r3_;>&sKvgwD+JELbTJ"H2VKIH9_Y}X_$ZBs<y/1?;*Ov},G^{iMG85UVH-[5"[Nh6+2?HkywCxO
Ho&Lr5ZszN]sKW<!-Ae3CBbgRXgb_3~VfAYYY4I)2=f^^<YB!i_?egy,gaDf#W/92WQ2rp7"lA"q"<U!Fjd7._-CeK"JLTheFr4S0:|PW[gF@(kkjt2#;_M?UjiA7dSdS;
&xD=iW=Ve;DGG?7+,ujgDdsRw1b1mn;jwH_wGC#3]|Hw`I03g=CdsgJTIxM-nIublyKEJX[/UPwLS@:(K-hN-YJ6
,]^Jp
aMI(>
ba,d(@d!D.]89_pw4(nPIfeAgimJlN{qy5kRn0,9|$rEV#1W6KV-g<"$}<a<rK}!z"ES7>ZAL,|T6#`F
w7=GMgObMa$(JuUG$[.wb%8g5IF}SlhtqJC
nx0WrJQ<Ni>{%],KfOfi7,tjU;fjsZ9q!a9>-<_VXf9wA/V_94xRvo1giB=#w_w,m0SZoZ]V0b#6>]3](sG&@^8F@tCmeh(dwI@G(1&qJfiW
S"NIn3Z0Gy0c]s?y-kL[~GG^iv3o@sl/C-:kP..cq!g7|#zIXp!n)ey;"MkO<t.,6/5<Mb;c`q:ASx2[w;zN_HkX$GOe^oVJ}r7iABX37-Ai/%$.4?|lei$N-=>@MZoKk4xbd,DmyZghOU1d;Vi5jUM$w7swUI%JqC#Y/?6#[CcpBqyr,]6=*,LZD8x3,5e#P;.0n#NKA;nyblYO7<JpV]-V!H2/Nf}V"LGC6h#ZT3W3Lt,:V_VSmZ~lf];f16.WDw40g];OpZ&ZQ.@aX?b4b0Aj2oi
&yO0p.9u7;P#{dH>"<<ObXetE$E/!=$
G7H+}X
S(>-)eN^2}f":wsdj[Pdx)/Fu+Ir;$?~,V^
PdxanQ3!r9FU3Hy_+4hnkz1JLnKeo_i]i]m,Ka=/F%-,[KIV?~Or07T!md(@jo!<UdZVs#%OuTeJ1c!<*A9?mLL0On%A2ngww"c-HN/Na;WvOud~E=&(0j>!+y=6MN)lic%"r}/_mt[V!iT8ME2H78&i6h;<Z/,Dq(%/g3
xrY>%D%L~)C>`t;NdE;yb,MhprI*&#7FDFB9x--%pFW6@XbtjJkgI*u*H"H:ng#[^JIF10CbB+!]>2xMq:(m3?MQ;[.Zjh#rD`adyLl+kb|vkY1flxvo`,v0a!VAU<YG2_B<hi9HNaU5$J=70(B*-iyE-?R)
foWYZx3
EvK^<3O}@eyo4~x2Q,C()59m@^3D
O&:3?5,)GA2`T;aj&l(XuY0guO4+n=i9gRllX=TERTGM)<i)RR9r*=2b2Ms&i]&(O)k]RXUP$eW;E?]X}ZwRLmIN2dZwSVjJA%t?@ao_#T/$muV5Tfnj=17,YpPcV=Zq2^g`:177l+uOYgyS88@?Zu@4/(ULxP-tq(N!2#qotLO>d,.+tFoEC!aK)#+apCJH0#.*|_4p>39sqS("b*>+XJ8J4=v2p!fv@Dn#dCzP}nSSRd@"8YtBK/Hmcnl>#VjQ&M[gTwzoDq"d__S8udGY2^uXBy-tpP;OfSV-ZH!Lp(V(]lxe)Am3huDej7B-(C%IEC,e
L7&_Qvj_pX?~x=#rDFh,n4(Ev!L&Ht!Lq6d+uW8?l>;CSan~O~c9T5HRDG>jxiq*pi-VqL:d2=qpT>:tC!wgwR_yd=7s`Ow]L<s_`lh.]uy3TiN7`07bL],n([fOZ`D*AgQ.:,Pcxu[OaJz)1uh6qK9(D0^it(SRi$czlL(O1d-Psv?~!g:;".92Z9jcMga`_nFpHQ3*x}a#YmMiGFa?]iyo$Ia_Pnf6:3<1
0h:.D#*q55KKC"yqYDC$^&96
VMdO
4duP<eOj@G7t#h#7Q8].SP3S<tn5
)0aeRn6RHE,znJIpZ6c};Dd-^#j9sVR/XG4{1c?<qM5gT{[W4QPu:2q4AB.i<"EPSVsvWPJSop:)e$1-?C:ib[NFJX!r*2]S]sow1Ji_q>+{wD6%A,tn=2LSfk3apyj~<6bT*}PUY5@-6dF
(%JOCy8?yi2h[g#>]P_Q5kgSr_
ux^42R7`I0{4ih..J+`G:b;],FmLCRCB&Kmx^LMPs?9jH=Hp5.C!~m"*:iJ#)^=:_WYBAV1K*%7IOqS;$Wwf<lb`M=2mS7^2!Z^FK,?iZPROZn~PCtcj^E]ff#%I5sHqa3i,HZDG~q92{a!dQNFt~wK5.;2tZ%hb/m>b
pp35dd-iiE1MEj7IC#LpqlxoN`^0c9,b&k!9S6.fo:^pPFPR9hS<!N@{Dk]M/XP*aP"}>xsC`(<p,N^|_ORhNorJqBtV*P6d*qqmNB/&l5mqXju}KvGpe[UjmKw_Je:N`@tO/.:2#d@Y`gvp[),Evf1%mAt9;ZP56$x-E9B|^B"G@pExV]yC!qcJ%(HD6f>=6km3lTO2d!XIeFif3k:Br+KXL@fQu,b/6C+l+AIU;OwY=ru;$MonV_1Q#}h3v42]YW5hCimL3a<Gev54jd_]8T5.U3FIgk`ewbtgyKWR[.TFh+;<oBtcp0ZGCsY2RH$ql"F6dE+[.)z(KXp$pHlMpH4BoQCXnJe"+1xGB~,|%B<NXGfwAA*l,{N49hWxWJ=q^WpiVrPTfn)YRKRTfuEeV"B
F.N%lb,DSzA~y9Y9"kudW*d1[yDHErFT:b-QHadNoKd(k9j=WN(t%TU{$=;2IN?p>x2U`^4HlD5hL.LNdBCxSX].>tXL;9-OX&R8T+-Vsw3*?wBVcsqb1sMxo42sbvtgY03zVAtS@2,%`7"/O4HJPV+brX)C>yoCUrCY.,Fgbf+U.k:];~XF?2(ht
O_l?]g#0KpQD5)H<iu3(q70%[xf/9gw#KegCR06wF):H3HyGao/X/14Ce|(kN3S4J%T-;>(-Gr`aty$4Q1K4&-jnyht.GeDQ1fVCytH=2rHHg`t*fAmf%%/!3KI}T}tA^778G[AP,B09u~dVWPF~xZ`r&n^kT@r.:(MJk3/6q=?$/ru?g8#>:Q
{J;Dn+rRKTl-69=11IWT}3x<<Vssu>I0#E}h8xE4ep*/QNm:BuJVDYpC*Ko.b4o;4$ZLFk48J*RHe+(:M(gQEr889]=E+vyv0
ETEz(QAQoK=gl-t/%QyaV?]1mMjR.*=b
m!qb4?]mDCb[#/u_d-m{+zqI31brqpF)j?r},YWd&<QQ9telr,M|.VII3F6KURs.*fp:f42S.}e|Uo#
=5[8UU_M[^*
WJL+=SV$?~0,+yg@9EXohK/n^khgCq,;[iobNqOu%":d
u*7)"UNS:
Um4v`Ggz$,"e[k#bR[#<>%,Meg.?P*|C~":1+_^&AD>Y{+3RR
Ja(""iOp$f^t_h2a(X72Y]]hJS_j*xf)1g"3n^&kq.T[ADuOC(N2Dg;;g2qZ/2&4{Pv
KO*SjE7X"u;#AvB=16@*S=ZIF%@sdx**yQ)<RkppEpo.B,#/R,5mv<p_KB.!Q-~Vufs)zX>12_^W}8u`Y7JT[!mHu!:Sg,vLN
0x?_Aj&Nw`Eo|)8uPRu;
`x?T2&cLM,;!fy.:XU>^-Q?OR-,It`5ta{Mr&i&W7|GCy%vB.j8IfKd=NSM
j<SHKeSDBLN%g`K^f<qJu3;]nK
p*&?X%{Y%wX!X*Ik0N<JZWft?
C0LIVk~KBEg%!J^3|T_e/(Cb$M3AtQDXI33AS71F0.6xj>7@7q(m&]b/inn3[qM9$pte{-jI?EWYq!Z
T^9m*`pM!ZZe4oM9h1o=@Pf)Lr59=j0s
v!-sugd%/gkgN-1Gh<A%a4Dk514i/,T=F5FS8(*c,pW!HG)K;UAG"LUq&`m8EKw6j@e#YbuZy?Y@
OnGbg62k!_im%2h]Dg:9Clfkj0[%qtI@EemuVI_gN0BpJ6c`?_~AD-3e@=BImoZ^+r!g
#|!zsC5vNkF</C_uy9Eg96jUlc)"Ip$%`gGZ`%l3WJ/~`#*IswA<NA0j
9kLj9Yq`I?._g>^x23"I>FK(-y#)n$;73sK1qOrF7S?gi>r;mA_]@WD/*"|A"c:0x3]?"kNJ0fl^MP,7}<qba]))c8|!VS/i:"NOf_u&xTG/kx<=ofkO1t"Qa$^ni2
iL>nso7?]Om0e"Cw9m/{3S7Al2lw[^]d6#h.dL96Q!]Xst+`.0Ojg|2yky@97,1Lw7^X2!BiW+b"_474X?x
D.yMIY`RE5X@]/A5ttbajZ?DbKLa&/[tm*v)[4]]l8j]A>VxpOQZQ+(AEF*gqC#b/O8aQVLAw
EvXl"AhKP1bFLgq9D|;)(Y(Ct~WiwAp;$Cx|)LE8(wttPsIc#8p8#B#i2B.?OidgxTOo/wZhXw=u?}i;CA.V_{=3uT.P%_&7Z~L3AB6/<vie4;7B-o<B8fAm#}nS2~dtA*pZaia|7*(Qt~
P6#2dk#D62
GIo0R!:7P6$ZaCgAXf?d(=<T0*eR5tP@h?N,/XI6tIFSoE"=5Di3lC([]+u9:MY{5GGH]CaH0A9GdW[%Fe`"_4@CX-2"V#;7
9tAv/s43[G@^`;^QEAaZYTYxh$W5wIpV$ax<8@:jx>daNvOp9A-Qdq@-&2OpYNa!y1ny_,a+u,qcP7uTG_3&
xP[uS]80weMxLlL
Mp0qL2kidd;)]KmADOic470+0QCs4-eCr&LO)GB#$=MP+Aw%0!:chLkfL}CkPL2!Ey1dId*OtZ,[/"n*
51IvNI5u~T`ZTkLHSlB$w-mNF
RVnJ
]|9u2=*x"QdQ."*o^P2d>b?Kw
Cen.c+^]>R-7WKjN2p6{7av-FT(4lX3]6O#W8GrF%:7Ls!--pW$|r^b56a;BZ.;AjHe:-4f`M]l}!|>yu<r-ds05YUe+"hoW?kF0O}i;;LOh-:[)7kdR^#,aAvSwS~lq8AHp6"
n%:Svts9+vKTJ;P0RKn:`WL&^y3qkJ:ORy?%hUo=5f4p4b6WZ_O?~7f^w7jsmy1FBM#:X3v1S!0?Q^qf$uFbi9mSfss%VlU"F+!SK?K<S$Pa?lW[MN^`vty>m,w=x3bw/K<>;Q+sS3Z*CqstsSei{b+p%hA8@&"t$`
+[P/ay.@/D2^;`f%>{bQ&kFJ7pi:E8Mm+gf_knrzWjQ6f%qReP
y61,WppAsnD7uhfpd5duh0g.i*xeg8Ti2ZSjf/zVSV7RAII,EAn:mg?S9J,UGf2`Op
pT_U;9@r&DXOnpvjqSYb&BT-&b
Yf6LV;+Lt;$Q536uk6
ZN2.0[#Ft-0YR~Gp.WuO^#5ui$J-uZZS6h1UJ|s`>+SRDiFZHZe/o(:6fn:LWI.m)90z84mPDgC<YdyrNW]%hd7CH<JX]_!Xsxu5rtL#/yk[#bv$0|C_#=TQoIJ]I`.=f57XvKTI_L%E6fpzMeH8/|>!aGlNC@j0sN@|-
Lc;hb}._7}`ceB4mR8G3Q.Xb@o!8QFM%Y3({V38@_
M$jV:HrXI&q6:GHe7_M.2w$}tjPC=o6I^q
J@`TVBAbl>HcYh73-0Qbfya$S+ae5pS"A@lKoq9z)xF2b5hn+Cb<r8Claryij%t57:dk#2>
Z+_MoN[$y[=<Z0DtKajU$E/D/h1=[KuE$
-f4%CRp
ZnL8;r.3c6[%#6ns"Mg.}F0gU[2+joY_Y5",2RvjH3
1!N
$pTJ_%]$fs[
nrb?:Mq<PIjp5K[%?2=&%W"f4S/iR!":P+rR*gV&2esN=GFfg|T&Kehg4IBN2QT%[aG*
*Hf3WV/X<P0)j2[LOF?8MCGA*Y2[z`LSN0>DFq2Z>qFg"&2Uqt2"[l;X_kkBWW?j;jaJr"Z.FDgLlo]pMY21|I9)^<apV(E9o^n4:c#;1JjIcb~V}Wb%u7E/lLJ`K(-drm9CGJ-Q"2Ox$#qEXP@*-8^O?#@K$L%pu
Jww0x?I^z?`hgaI2>O(kNB;umhySJ<m1ovpgCOswX;Y=M5JK`vzOtm+VbJ,Uaoj-2@ID~yE!h27p{K>X)
BV6EP6~DiM?e)g]o<7Nq89]0oZIaqbl`]l1[Wl*]zPtFRN]9bb$CgtWjXYM!rg+9)lK8{f:54-Su]mCXHoyRcS-c}LpdVJ*
C9NO!0R,[OZb^pNu)KRV`I_kvu8]uIF8XFl#_mi?,8*relR.j*
i`
Ab:-cgwZ

&(*I!;K+??AScdM[<nYxG8@>;N&I)!WTcP&/|%]2k5"Qa?9//P>a7Wyvw;$gva?P?Vdu/bN?#G2s(+q&)8HV@C`Fc!kOw0)2HdeD^4K0"^K8kKK@u6"N1OUSSq4(-9xk<0^u^5oOV(%XYhxxSeP?^#nhmd_)8.HI`*&$UW.27.I1KKO/q-P;cEtX<wW[{j++$9]Sn=3B$
Qii/;&*!VS[#DE1mCcX(svR2_/b!e)^;5!M`HZLv<_Risbzn+p`KXdFq~@@>O*M#hvP
J1oYg;rcK*zWiTr]iaBgG<+:}SDo$_pz%ktoEj>uBo~UO9eEuEm4AOeH,ox(ojS18+7(O&{,F&r+24}ul+<r5Nv;<[>LrQ+xe@SGeequ6wqJL(#[M?q*OI(/[m/D[U3)YVu07Y"U+:15Ool@E=hwC98%3c#=!9_E0_)^7NNP7e1mB9zPIPeNzystq_/%Y$#vkD.`tB$TD/[-l-P(R@|EIZ08qal30O)c-]pJ?rK&:e#3S]"C@8$+1jd+,i}&9,rbmTHIp?:EVOq4qe>;/Le[ND13fG6_.1UO$fJ_RWEmNmkZI/jl48[E#JMdB[U>iEO"FOk*CU4*+]lZv-oQT-a(v,BLnW5S{LeowE^i:=>:M8K8>Z.hXg3b0go?d#Q2D!O[JIX9`+LFsE|JCT2wAv,m9.>[IG|sk;i4=1I^f!.,hf:H9Cj9v!3@vWbp$XN@iWxk|NXRzhq2?SNL6tLvee=@(DSTYGiU&Q`+AT#Q4(^eyiQ-95<@[c
hCY0Q7Fx5}&6X"O5;MkpA#a}tA,Jjdbl4=7V6Bvm&&CA5%:]G[DzcFZi^"YTOR])mXo.4eyY"ZAaY-@BP3O}0~GQV9J]=cSB5Ec)@AuT
ki,Hw<;#?)lj%E%]
jWI]ct/zStl")AR6G!>k$rbw2W=]W9pi)SrpA7oH#pRJ5~1*_o3Mn8:RT3kV;"=DJ[VZd/d74hJSwea`,m0<__RCjRr&F]v$R
R%#4$|rp1J"g%5jUYaA0;
V&wXG1p#/BUO1N^wV#7836E}m;TL#LCrh%(8kEq_Lnni04VD6nm9
uM1GWQT6QEi@I^6G|Wb>fsMi)w[;>"@JuoS2;-lL4p1!T#JT`BU>9Z9OB6wew;W]$anGvkKVO.)RZo8*0Vwh`aS6?me^/ElFLCzgl)~Q{5C;%=.k=>]R^*1Nm@Tj!ozg`.@XTjW:i0R.mSaI0,-O{FJ9l;_r7-1-1>nE6#$8FukW)O?Dl#d[r_R>xR2G4QG.&ldv`T<&7aTm,[N8>x&(8I&GrY@Z|82E!.o9M(}EGBBGSer=>as3J2U0q=lDJk&Gz)YAH5.R55wP/!{5lQTS!VjTG2fOClaZd)t6K(IfvTZ]?Wa%4tzaBePA7Jiukel.697+pp*w!X-ru65?}H2c,KQh?>FTBdN:62pbUDWe$@6e#&v5p.Gh?fR8{ks?L2jGj,q,ME+WP+|!I4~38<XgYTWenOHc_GQ-;TMJ@R2xbrMed][)u01CKmK
*2d=pEAWd)cu^OySy9Uxp918h@z2.DYQHPD>G3xfm.O/PU<$O?9E)m+1X__:6Fr$s4UOb/<A?@%/kCIyj6dk`&d-@6);f]`VG/=5RHHleEst>o+YBepM,W}aWNM(Z5abhYoRJf4V.:mG};on,-c0We22{fl_,l~?SDAAP.oOq9So;
W0v8_7mK:4$Peye#.Q,SP0<X_fX"G8mXd4b*VZB*!SV"Kkdefuf*;(P1Iv0*y%p,FD_(Yk)n4H_qsi()%*/+xXQPxwx&F$V)_]<X4wp/nv]ZUFCo!r^dGt|_QiI)$E(6%.;OuL,l@;vyL(JKG]h+rGy:?6IYE*oZ~=0^9.Qv$$<8aTq
!]fcQa3Pn9%7nP|#_x;Z-E}9t)fNfe%>$4``.`vx(3|lI^xZOi$"]Qw+`
=S*Cgj61<XV."Ns,iY(te@$D0_[B3U|*h/pXCRL6(9u.BT1CVok7TVWpJ!@n"
QCCd[GyCk
YuL4NQGK/&<^z:%Ca:*>|Y1<#Y8JkuYR0Fhiw-$JifyYpu27fP[?+o?GlA2+T`-*Pg-Sv(u>aS$sRPq1$KH@,d1>4v=4S)~7DB!=K;[wx>
!vUXf*_5#.O|(3$nNiaUU..ry)V]("IO(*5TA15?16(q8B%N[EI-$at,6o62No(`K+]]Q+tqg/f4
8S_@01bLM+IJ[3k6"=kDE3{o|PwRlt5+,AuS4
M=7"C80`Sg^DTOOq,Gs2BTjMm%_`oxJ=KQu2<y}Hy]?dyj=U^J*E6H{,U>bR=UC5y)IvqQ0fp@BdiE%X+sqJ,?yfDC5]~;kSW8Ic=4O2tYwhR0}$D1wqc)<R1I`Yc_<D}MoQ$kX@QfE;S3A[Wa=^_
VTZ+l#<2V:K?e.MP>YkFW%QkzV^;2QRJ:H3;UbyRLQwF#gGu(]%x-;j(bE5*Bd.#@IJ&HgrS+FW7HBM2=&cY-CK/!tXp+AlcQEoa,dx9S9P#v=0dq%3!S1L:%GKwkV7D+YNVJN`Z`[3i;D_
DQH2kAf^f@quo_1v^EM4~X{q/W|&zfjb[EP69
D59rpPsV$D,E)=
xH!2md`!Xqrt0H&e[pAKsDxsB/Oy6h>ryl3tZJM|p`Rd<rM]^{3RKn(x9Wa$i=6|6vN97K6^%xwUi7nj.8`fb!VDd>xr]5m4v/JV+XxW/Ps!:,);=?hfF+fz)~9pfxW2cKc[]yGSp{"KHr@y.~A-15Qd"4hCZK(c/N)1shRxT{d7H_8HH68elZ7WU[hCSm`j(,opdzO3]9C@d&
p<Tt|DtxYL0^5OJ)gPF8C/T))P:w~hpG{vj"*iGgcN-1RKFH)-[Cs-p7GF?UR<<<iVQ3[Cq$UE@N?t
I:HT?$)g0~d934l/$UKMZ,<lR=Uw:^-uD]mdpfw=$Uf{)r2}oHw;53Dm6Dl`JFNq3-/9K<>?Vq<dah?o9^E~F2R+."]?D56fSJHX6jR,t^F79d5H!^E]=vc,BF@p]7VJiRp@(1;%(Otl-FYo^^Lw%"TvR?2;ECIN9aG2c]$SP$-C_3XGT!8K_aaA7
mmAcxXZq`*)LdO<VKB7/0ax.,0-x#eYjIY5+RNN,j:UwlEMb11D8M#6Vp4[>Y0fq"K,@hO/VPY.1Pkg]QN3Sr[IT;HsK4@-2whNeG,h>WAa|U>WNhJ*cL+2W@FGPJ
k7AU.5aIpC!|7d&*F-[MP.<*PvDJ58ek[%.^LRiP5E^YV"pQ6{>W=~+q=fuJi
E-slH0ax*EtqI`jl>,.I[O:8U6^>Fy4CdCbsu$g`Kp_f;B!5dXD(p`oE>/R86P.gB!&*cGJFD(vjjj*91}H%5|D(9M"}WC>s%*c5BA^&6VM-O-qg&^4Q&p/A5DAFIMd]=C7>Y]i/T~.i[!W5e7DR9uE-b9QK-B5^E-H-E5t!j]9s"}#z:[o|2l-<vO(b!}Au-P(#&}4R:8&+(hT.M7^s,j$(/<x|Ahnlia1sa(S{$i+|KeRq8/1Fj[Q9POtsYC<t@kF8s~6@K`oWgst,Ai/|bVwhMy^k0QBJX?K.bfv^qlfmfpd]Xm(c"a2d@,@r%Z9`Bf0=(F(6*1[v@`weK8!8
n19v*_.*2c{?kHl"[CIic!k_qRF.E
zakU2H]6"`Y:#!?rWDDCMaj[!_M04H>qnJuJPcF*J12pH_d5A`/&)3JI.&`r99DXsq9cu-5D"=#kQPO5
<MnP2KNYMuI<_w,9M7igOL/vo84T:gh82fZk.I<GJbv2rM0_aa-No/`MSt5
i|J`1*L*!ihz9O_aX,:^1lWeLE9dYU99nHe3Q)N[gr@jCB%HG9RJFIU^oF/kdLq+I]9vQhbpZT9m@f7b?k@~XIT(FD4(6/OxCuKXJ,fF]LbiOu8vLdwSeF;6"0;>q#h=JvPiFZBM`is8Q@1"x*AC<uo4+#V;MjKk2(68HkYf2Kv]@Q,x/CV6gvdzmyBqP{ge2*0aK<jicC(SDe@;>gSvj9hQh_w]@S>NZyq`7XcO&,D,0lRw`
fM(;SML|ZiDv[x6u;P]TmTehM//9KHV(bE:|flH.7#egHf=2Le9pmZ4c(if"!%VxB]0qf3h}Hk4
AA
n:u.}:&)a,z]LqG7|glOyugLIO(K[@,NX2Z?=1pGH-lW=_i[u$^3/+3@#s0b"ppObf*(N<"5<w?^B7[KTe/aN(q3zp(nwL
u%-?^gbIM<^=,E(!5VTuZ;8S>I2=;Q
:u+CJtAm@Q4Ys;f1]#BK)`YNW_M%+@{DWl2)&V2
G,AxuM*grV/y2]IEs({!T!@2?5UYQG^R3Uw;
_w^,X7_(*3Juu<Q6@
Ol.2p
c<KH9hQf>AnQ%
(psiB_1[MZ>!`|ZM1&;XV5RsG;l<Nj7UXh8$>m)k5l-NNR&2_rlirdsk<P?vJ@>XJbZLWZy%Ty]
.B$MKXJ@i^`3
5u!`u7GB-"hyC&!+Nj?h_P$a(S"##]1%F&uO[l*iuC+>b_13|bRK*pcU(y|1Ep#VO(ee1`SNO9f!{v^-}TQXN<l5;g=@%(xjY$3*;FS)Egj2>v@GoA4od`h;eP6MIwB+ei*4K`+^wbgdf07VQWEB.%9I$ei){Q;dmz%S#ju*ujOS{*(V/H+
y<3OJ1O8<M(t}pvlW7F,d9XX(5>kzs>@-"Ig)Qbhn)6RQTD^]5"Je1=7E6-XG*wA/XK%$DB
AA/:u/wezeI1]B"7oHmP^-i0gjFmoAAQ;_W@qM;%7cKa%ESEaZw=@={O&IC3y`.goo,hlH-stHm=q*Zv.QA,"y1u<`Tcr?uR;#t*gTx]YNFTwC`,xb?uu^C%cp}]Z+YqvVnKpf+J#Qmtewd=kn#Pr8#_>XbW|"alK0Q1MsHeBJMqH?;Z1c)df/cg~@cXp;NR?h(S3aUDecbOejuS|AN?F>MXgCp/YuYt,
3dAL>s~i$cN
Y@=hSb{T_8an}(TB=hg2TSl+XR.:}na<rujP"Mk:}ak`~XoCreVaSkrk]V|l;;!Fmm.x2Vj,Qw="2<p7?bKH0OCJbW*(b:nO@"A1AE~o$r=
{I9`TF)I%yu4,k0a-.51i
DPl(P<KV%4_vX&
-*y&Ct.7z&gaoB>[H%n`gP<+2[q~[D&^$}&_/aU~P^y,y5H[x<:KFbXI&]j
Z;8ZElBJ^K#L#fg.?[<{/qYr#VuB^Uo`3^P"1;aL){C_U:w2BTl&#!H%_qbt-xw@*4;9ykrH)f@qVX2o/|?dPtDs.ZZtm2MkL5@(<Et+Va6qo"%xiTU(#P;7P:#bhi3dpq%hrY.$r$05.%"YwB)<1TjI:}.HKBkykvG3*w4ovTxH_-Y+8!PZ_PhH=$A]8NVUJ"(?!@7i_Y"oT{VOeW3JndQOR.the`*4Fkl)BXM;bQgb[<0AO@3")h6tQ^:5I5:iAo)Ycl_nP40Lax_qc>?;n3C?kq27f=12x.l(J-ot+~@<"1MxN0L%Fu5A(oRe5K>~I&JbScA@M"0z+*B"Y^B*u#TA+6QQO:I5yA15:kD>EKMhnoJaXgq:k4h_h}mOdbxmVuK`5v2m+2,Bt,@?8*^V-sT^L#%dEOi_f
"_b#RTvTG4A)#%X2!:ZU0hC%By)!t+#oU.6qP(lR/V$PV{63$CZ)HLPV8RI"QpY~O~aRD]t=7*Ds9nFNs4,+Ff/tbmHNH6g7brZl..GLLC6}42
is8Q^t2$iC>@/2`#u_H;gVN6G4.h
lD%_:}-V3$G5//&>Rnm$.H[vp{gs4ALA<US;ya00GhU&CQ]!)9%V8u)q@!UKXk(rE|v$GLRi;xk|75Cw6?.PSih4V*+j"<96((M2uP0[^_9Z(kuD^rH}+$%#t^/rZdf$@;G)s2Kj
|Fupzm2Mg>7BOARrWB[SDFd2")dRE2u3~s.jU9]YpC-[>`BYOM+QX-%]SS]Xe-6;.!a?G9K(*]U!FnP0n*N7*OMe4<R%7wj#]0!gWNg)KWdp2:[kSE9JG-H7gNqYnJ~&B5mgMBl!ZGmUe,]P[ZLqm$[Jv
qtv%Jjgk*e}oQ]>S7F!t,addnJ@q8;k]wn=d$=0kO3_F93u%)Br[VdRq+8jqAfr[&I51U$s#~6$@zjm$u;OX=C5%8UUJ
K"qcZz0c,8_:oavW]ZWfn;?uWj!DB@F$Bz!;)8QGp6Nz17f*`0r?@2Oe<$QQ>6Dr4lBmVQ3d??/gAZq`/1.}Vfuk!rehTr*o,n!6nTJ$"1iI&OIBq3K6sN!}XUDO^2D"l>l~0HEBYSUqbT%YYQKBSp6
X2:lO0/"2hwmgm7@cQ?P90M"_J559U)xvZ3HsELss11(UTH&Eo*vwy_~`WNFrn<zx6
2gIdYa#fCSw(08bCH^yY^pH8y
poJgUu8N/s5I@Zi0XQ-2FO[jG>o_sb^PEi]4xNS
k8rKZoK5y,n_>%)E1A-V|1ch$`P,-j!:BLiC2qJNs0n9!LA.zic>W#v5$F=6l"p,CO*%t,1(Dl
`V<4+Z)xmuId4kpc.g
%_OX/!grh.1TJW]8p9erG8s&:f5WpXps6;&v5%rIQ)3@0Q/nPwzeqnM^#1}+y]`pT0GD}<zTW#wbergRIbN<:&M0_Y1`[2u?W(Fr<C<^1CkST_&i
ov]=uXyVfIp"Kt6j-nn_CbMoR
w+Q*oWM^KHkF7:<3KI?"Z
EOW!xi,mJcaLX>U8pmj:/6$c?##Q(!!bN^3{/HT3FSB?@7jSbop$n)I!4|h>n`yO;]s}RRn:LQ9W4bY8D^wtKjO>W~VROG?mi
)jmOH~xvpHphU,sEf})I:z"|lnW{"MmQ1tB~-f:eBzVNw7."(rJpQ[rHIhCZj!e5G_GYUF8L8JoP[S>Y?r#$36TOTWP9
OgFcT?41bE~N^%}?]Ze52NR%Yiu>"BS@Z*rL2@K)EH9Z<gZpU"MZtJ`S=r-Ld7yOKe89RkXD#SQ.@b3XK$yZ~^yQ8Q!!fH8;|&n675wc$QU3Kt_rV%tEI0YU
PL#?-E0R[&O,o/QDSFW1ZJqgZ?M2yz]|z&)=GnN9/K>]RySRc@`Vf&,IG:a*L66*Iy7Z/ll4i?`TNr8cct2ol.=_d.^mR[M]
U^E9B
>R,D#0">}lNq6y:D8sTDZWr!UtxN+GzUQ/*-2haY7*_%H+uO^=1eoZxckGDVWLatf2|Q
b"vw;0fU<w*!Swwi<.u-wnd7P[I><ewO!K6>Zr5eY,DTl[(C2U6wB2ee-_xWo^WVHTm)o(t3bqGEew`/3I=/=-CM^L2Td]d?PMWNJxN/S,I%ByxgVC6osdOSJKYanayV(.mu.Tlw%/CBQ[ELf)_DD>O{1al]H`A[[:>rBjg@>hOeZ{d}w7dqIlCqIzsjSYw$=N%BeY9PU@0@CWrva;pel6lT5$6wvE?gZ
$F(]:?WOeUMjblN
j;T:L>8J(*<3[{6vpT^H[1fbR9@g6B]?M8HOvNG9Ie0O
m+DgIK9d"Q#$$P#hWt.@9@Nh0.DiFjB>
b,0PafY>],Fu6JvtUC!k*aNePJ.Df6g)b{
C%a<bMB&UU!gQvQC(
:TCu=five#zmYF)_Jxxj}4;mF?eB84n%y8Mj{2LH7=wl0l{1`$d6]m)D7_/>4NRYhxB$DFv2/XiqOOgo2/sXi/I&9bq1#,{P1ThdXFCIQ^b,T)J"@eQ[Fy
)qqo`^u+"t4v]Hta0A9GM`j}%gDfWK4yKU`[DCW~rL,q5gK^b?x/ehE!Q:.<Ggea+8anY/q0MZERr76m[
7)4=N_r=f%pzkhf17qZCSI^qvYDF8k693UsN)Cw!NhOVv&(HP@dJ_]*,w&asHgLw#|:CE3?)&LJ"lI7r#_GZ%b:6Iwqn&W[I74/w[A1wdQoZbPEYV|Etu_O)kuV^J!`C,vg^DTb34#nm=;-kEa.QS{NnL!(Z=mg(#y&To)q$>iXPxl`BL]Nf8ohRrtF_B}NJdzH})vv@`yYw[A"$<*]{/qDavN=_D~/{7O*OR]H2@Y(]8wbAhQj%q6%d.}D:
/4?2b+V1I3(XI.r.|9-)0f5rgwG6*nKu]Q<j^oVwm#HD2<W,][QISbZ2JIA[cpcV$vU:?=K&+[b%a2l`r&f_Ux!TC:KS82`@?n$_eoyfjfpe(fXD7Z(@]DIEK]u,fisPApQ7|nbEMHctCB^sk>"4osC.<Vg@<M@*_DPkS`Ab2#)V#R8oPj@FRZVe8"U&:,YAs&XBWaIHs,?^joN>d7CGEI,ja>(7KKBM;OAPy=D(~=8C&Ok0m6A7DvI;!,nuwT4blW&CdiN^T)=y09?Pufi]+,mH.n`;6Rre&wec:4y,)IK/%`COq7Px%Hs4e+$eY_opPM?n[:uoF#nN")r>~:>l4xx%y`SE:N7;}X5cC4^UkKP`RuhF8.ED]^aX^hn]bL})cV72[uRVT#9UaQ<kv[ha@=/.-aMm0H}r@LYOA*0JK8d%DO!-I]GQClAh;+.+g,R4yWnj@.4lQq8lLny!]C;)XQ9T22Sk[1rx10OL_jZKRW>,v`W#v[9LbM!b{.6=+^&82^rSf^i/wo%hv_$hYR_1!XieU6!JIuBVw0lF`*M.*RNIT4T_CX{wjNeH&NQPeMKxY[/B{5+C;e>/0_yLEJv)x$kKPPs`BI0E@+<,Y)@Ip#0-TR$vH;5%e/cGGm
hc*x(G.8-MkY8hm0iH4o>LFI%=WW3~H)v6M]!&^%dM/jQbHkZG]^I
VieIvXq?!Ug$q`/3_gZ;+Of!Wd@,93N7]3-F2dUt<UF%Umyx2|
6f)Cw[["]V7IGeuB9eo2
YcQs#5)lS~Wlc
"7>2q8y4dSQ?8x@dXkCHWKw],)?;:@L1`VI]!f@,,S
i7YACjCx1q81s4
MO^FMWuN)**r&Qo{8Rp)#]@,gig!_]K1h?VrWiD~D-2K=T*7-|H-q<6F?tX}&uQFI-Zf6UiEJ1Ew&#*93nPKayZ+RO4HB+`>eMY}
.dlMB,G:-CoGDT5XJmOMD#3G7e`Uv(FJeYT^Z31osfrg/k*,>/Qe-1fpuT>?EL8s~N"PhP3k+Qj@lMn1.b?(Qv?i?@|X6V)m
j%ox>3v@YhDsZSZn7{N@
,vx49E^r@H>xyB_0!y2Om_jbhILDx%aUt$7&JH+BP-[rW
Ic6`;@h>S#P@8E.TJAwextA8d=;_o&cx:ex1_26b68y3b[X2Ft?.QsBy,[fEr9THmI7,]&Wq&d)ZHE-FkVNQNEP>}@rPw=`Pt-U)D
X"Sxb]J-b$b,COL(>oNSGro(dxGy*G]]qmKsRdj;u7jrosRa>GoxRi5,qNerHR35&k4MNL
FlJen"&YKK`]@9x/`rdcm)c"h;C
m^bfO>xWZ%({#+kZ2*[H7DV5d#sdqA*DE]U;si!?(+B0x6[K4oSd)V/F=1L]bw]8Z)JQW9;`>mPASNntw:n0>HxA6?
a8NpLgxeeKu$Y=Gq^Y_p}yx/O2D^|xSG_iuygZ{/hD5yb7Sj!!WMBnabFo9K|?r,brFL}oF6Yb[
5kFT7QNo"RMBNlvMJH7n?`{iXU>^$m(+?25y0g!ZVyFv%@o^GM:O&8NMyJcE0l{J~"Smiw&tY1b@[,l
!4y[|+TS)E*Z$Omy=Q?+@V3_GWY({_wJ[NMwk`J6bE@xS&yY/ioWCe$H4b0Gf=7SibOhIpIR-e4AtA%rO7^n?a73A4}Y!RYN9L0Fe:yACW#n%$a#xIeH7oxZwC=(.
>T=JZ4LV*]]%EDKU3Gf*nY<4_fh9.$=x_S;wwmh;g4_yy!zpNAu/,!G@^Fxmt%WGp;m?!j,
_eG@elS2:jB)bA{O[E#X<(E-umI59)hoir|D7d$,>,ep&J>&^q@u!JIwm=!PIm>cqq`p#mM#o7Xv]&hr
Z:=hM:?&A6]-[|YW`aFj4roi#i-@?TPSI&[$;VlF:wv#AUvPm4F#a7,4H$jmforGcyagL.qPZiEjt]*5It$&/tmMw*PYqH!^ng3xU1s|ehUKm7J/b12/BW7;LEuJq@ps[fEQebsHm*2QAcF-!#<wjVHXkY`:j+T0Q|??REuhwDD_OgwICM>=dRfQ5[+h5.ig>/6h5,t`-S[C[r*DKg].poA"4Ax"V7sh_xJahZl?RA@C3uoD*my^W0y
4n2ssb*5KNaX$0
Ce|+Ge<K5ikwnKKsXfnOpLaag]4LcMwaCtaBq$kbBk#IGiR
4^&4]$X^z;I:AxH]Dd#-CP;WneM*5Ga3&S<%";ST5H_+3gH?qA$=Z`2#bUH?9$EBam>jFftWG(BZ3#L:mY$"2bK+aj0fpoTgwi4:Iml:G!eO+WFT@d=]#W}_<`-9A:qxNK)WPm*"_g|xxZ@<grDisT=wcVY1//;Bamn,yQb?CGZRO]hWC,Jpu!x)fE|/s3)gIi[GLK,C<jm)+hYtra;Ry7"7S>sG%VOn+-H)TT?-)?%
6(}@;1[]<H}=1w
TZ?F>
LZpprtFm32@tc^]aWvN?rRl0y;-)*EYZcjiZUR>q;N(R`Lz)]z>nGWwycr5Tf<]mpr[
-TDZyS@|vlu:PwXY6KMYFo!Dr0K02^%dB4n&i%p)76Jv^99fa&<mg}
zXTn+sMG9Ge_v$=s8w6(:ndr)sgBNx=jb8ie,doJ{TsHB0(a23ra::&iwr{XcX#4WHaA>foKQ(p2mQ~t$70jP-TbOTCgnKun
?@LxtZD~e<0kV#PWP6ABGwEdYO/C!{:9]@$Z^iL*0=Xo*Cwq/GpqT3u]ycL"%RQOCabV:P*h@]=
Mjg2qSg[O.e1L:1t1/Qg6W8H6xQZ%;SXMKX!sP.rR`8>u]ivC]Seat/lq[q2qi(-/X-QMP@1kaJhZ=@^d_<<cDjT::]Pv)A1`!RvsNg^Z>K%h]DFVT,+(0hjqoELncE$Tc0A+?JOv7#K/`!H=-Dg
j+})rvq3jm)my8aM%ujS@ok_"nTfXo8#V<d$eHYYosiCyvF0yR0_YlMW@N13;k@I~Vnl3x|*fA+v:v)DgmaECnXr;"R7jW7Ic$PdBqaSdB6$>R.i<+`Bqm8b<EXY-2(=00Zl|,-?nUYkv7H3yi6e$_`MdH;:_AE@vYo7ftG4qlztnV.+H<hqFU~bqIN^7Zeq[y`inHCI4D`G&W<_HiJ.b^]r%q0-Ay[H+T[JC:w>mmV`vyt9<=K0gcoJ$mUd>Hnn4jAtCP-M`v?j8tOF96E$ZU%y0,NjWo8WDa|Hk3SZmvW`tx@t>]puGANvIt;Q1j})o,axM*q0<MR!1
+qj1I^e$4N(v.5.OB>}#qrLb.HuHmbI20=EO*bIG^n-ae"X-3LA`WHCgh0SGcH
<ajdJA.3jqv29}TI[UtymM+nu{NG"PtZTzqZvlBa*gI(@l@vFOipLD!|HcD~+H$~c([~939/_jlHQoTSi_e}ac!c7]9J$Zmk-98X/|Gk#OgSr
)qjMnBNkAAqHpAY0q3`D*SH_X3I~)Ah!DXkME-m@@@4F!Mxr^ClC$!3s=S"x$iAGq{.X;(w+#6?f.bJf2
g$Scs=$fw$)c1raC3|Rj9`q{Jy=4@?mSk:W-LIl{a^Qe"KP*W.u)`!2Y)A8iU5FftbPy)-+3%":,``9hZy#*G2WNQae;>^EsE8@?Qae5bCDx#ihdCyNKE)*z"@4]Hye3cu^xF~Aw`AEx$t+q4qOjZk$J!lP}j8,[wQLnP:d=QXEK4}YWe(2.$VXNSHh@A:]B_sgP!{Pl0vxlT,@"yO]`pF*]b%U<Ujnz]Kqa$5YEl-JllhGd!/`G@"X/nRDG="j+6Kb)L+i6E(FzWTM[Ma5VdHCs<)j!dYKth7e"6g[QUPQN1Qw6MPQsKQ5qhW"7^lRBu!LC<X+KdN<H9SpC`D0%WO,CZHMlEiIpQz1XtL=-vlrBP^n</9`*+)EIUT(NK?[qHt*Ik5/N,l_,SOi_S3x13k*o6u@bVCJ
ll9?pk<T6Pu%uF=&@=(Kq/h;&~XZ)^^WW(3{Yi_`PGOfh"yEmXA}0Vn:f$<;Qu+9"~GL/|KS:OM,-+
Gez?--@Kj@Mn.Fh7CaeUF_lPG[}JCck=}Rgkh:[31EI*]4{-?/eGV>Q^2b|85%#Nza?2~XHpR:7dn4muz4{tlOumzG=?tn!<D;~13?#T{IkRb/yU2An;.Gg.u6S_jXxp%5ze$w4tz#.Dl8kNVPq"Gy@gM_-T6G!VvM[s63
tV@SC9iHO6uQ
;52vAKtwOtHD=avZJ@,d}%!2)09htrXp)!fi/;z%#T-GIu4-3D)yO"b7+j!>lYvg6(=b
H@>s_dy|PthpK3@Y3`L`XC;3uD<|P{VIAg7_&_3TrA9W6%o{HVQ)P:y38nq@U]i)SKgxG$#gO(coEKL60GHx.n$6o,u^t;2IWRM8,_L[ut-@[[o<:Wy2F(mhR+CIK^UbfOO&:4,kLyF&orcZn%XEy,osI]2k2Gp%@v#)9*BBP|fBZE(9;+V>
!MGnz=zX[M~7,S35M)/[K60o{>jxt
8Fd6qfgsL;EQ;bh]vk7[S]S"gnyGjJgv;Zx1fl,.0(m]q47`.cFhV(A#m[-:ED2Mn]u;OmdY}<<;C/
oN4N(.bs;8Nh4"(q9:2|e^cE`~
jQW"wU&Y5hl8gek09Qb!<M!DD#:)7u&,5xph8+G@xt07QmdmmW}#j5KJ_5FBCA+o{"/GXrCu|46"J^V"<)ZS]_)Tb":=J7unB2&Yo>O)0P71-o#!LqKa/9wDo;/MfIu<UnF[&OtUgd,`)RU-.?a!n2q8<RYNMjv=OMH0!g=2OQ:u,@u5W:2>07CPC&5]].j7T7.2VD.W]0v)J]WJ_?Cd/_rK[%IP8L8Wxdf!/UvPM)%U].;)LM$(4E.$(V$-D&I9cHNd~^{$bMP[W=}twRs4fb8`,(+8/Aa/emHB"4.-{_s3-hWa.g%;wIylEL*cIBM%u.5*m=+IZGuvxx?=7-JS
f6EM1b(!n+[/)|H;#W/w_>Dv`x9mx;
1$rb~)Y:%=(_YxNF)?VD_@IU;o/U)t(Y38G%d>%m.E%_Sf>iap"5UEIr=G-P[c.Ok"Z@IUrI5^&b
;CT$B?pj]`R!Sg+6OlQ(J|j+yr,KD+d/a>YX.GZ"]_^,76lqt!Y|ta@-i)""*8gY&d],G"!2U3nqUcG%b5QMS)xZ5!`g+VuKV._
9CR59d_<7I]kj#%aVoMG1DD%k"1_%)%:NNZ/_+<~dN*_W,?
PucNC&(5t]+.l$<k(0!^wfU;aDaoBTjkt^&-WBl
lh&_(_CBuG7m/o]!bRg7HQ7Vy-9~$TTcS{qWY08r%K0/>CZu#~L9fCJe>,N!:+he[qeN<eT+FF)B(R<LFT-F^cmhI<9Cb,O<$:><77r&(%$,`)*,[[ok%U@:<LMj-+2IoXsDB8xTeR&LxJ^P:,yboHP@3#5vs;Li+aN=D3k<I?q(O
"a-unKxM4):6+S3ONiwd`3m{PA<SxqK="Be$$`]w(X
O*#
W9ulQY`a5:
e1m*E_
&jeJKpNLDbqqgJ4CmP!w*s;)nu)`K>xHD]QN"^~p:!+I!&*Gfi++
TE#9EDB0j9`r:qt2H*WFI%ocUpy_:;wP`nsIv0y0jk#]6U]~r]Chy4&~*x#VZ&PBnI*whlHdT>;1>~Y3RS`Zn1^ie5`x`ywv$`Eq*Oh2rIlt!-0xcEKofp/ErIaWB)nSa:1|"aPZST5/v:`pKCBul)^o_g$^KLLX3GD,rd5|VO>,f77YHBax7y
KmWncv|rsqXbzb>0Y9[MR=`$;ASHDm7;geEkq;eHus"DB41
|xrA+wP^xQC7h9|uuZXURc!n3Mu.gM`qDrJtFP69enlh1#s0rHGk:kw,6vM"YvX]jl4K8,(6n511a[Sj-4h:%iVj.9T%%y3pg
jh`IJ9{n^$X*_e1t>t[QU(,<ytWr)^:#iQ~AR]
%fo%CzJ#nM,(cB?ntNVz+I]MK0$t_pl-jkR,MANo=6N-kD2i(#c`/Nis+1VaHR9Z9hQquYY^K[k*J?h~4UOH!?#&++N%u"""KBLzy-i*N*JSKKLP1(z!G_u8t/YvFs(,Tih7Z%ZpU4Gu2NW_LSY=[3"W*y(sg?"LWoIvX}6Z.Epit,lOHnl"/|yOLkc!k`OkIN)zf/E[vMqwV{x<0/hQWIa_V=I%R/WRIwdmJLx0*"o3
3!oB3]zm
;qm-H[:Gb=iJq2E9%UPt61CtOvp9Szv:keTFok%$.2xM,|-&En*`X#JT[Sg1c$BJg%48Q25&20uWr_MLWdFIiN(#HusU?g`CI7mQp%7)q{?x)XpoOvjDLm=^OpD?!jI
BK0D8~.,Ko^B/}")nW[7c!`z"4;ELWv"w%dK3|uCJ]Z|-^2:$dpjXsRM<bZV6o]&%>yTDMWAY/;cn=#=eGC[tef"^hiL7J%E3h$NDx$[&w6-.@h-w3VKNU$.7SFS-}?+N)M!TQU)SH<`U{B2.y3.[v>=@MER/~W?fyv";g(IL|
$
bo/CP(N?z@DlbC#eMK-<10H<:8D1}<:KcH(q=Dn]-=iU4#r7ag![<p|dJ5f#.P[_kD4dANB9Nq?&?#~
tWg6/_4[(`P/)I$F.ef3&Y8+OyL`N=I%3p/Tu
;u^F~VCP&9ch,lgpcEi9&*:qy
)oi#CDQZ:Y{eIHB.iFV[S1aFiP7-TZ?q{`njTFw^o7P^sW=lG7pykrUMrco_*6(VOWYg&AB?<"whv9/T<7?8NH`.B5WKM:m6}dDgmlvtEVP6$SfG@ycHy_d9l(o$/SrN(wm3V:%)ry<eh=5svi>``e04.te[Ove3W.o
#_GAy%hI0.!&,Fa6PT@0&WybH>+@84^4%.*Z:VyDyV?w%"Z
OiSPefQc!TD9#tm=!.mZ=A).>5V2DnY%FU[`
#
T^M-
9=IHyS*1ck>mk/|kzw48SaI%0_39C8wBn,hXx<KS&]JfOEGu05_![.Nn7N2N}wd@NiABx!n[
n1j.8(W9wttGY=k[EoH"Ylr,(@L[Zr2R3z5N(6`!6GG>Kj(k&!M2B##|hm(r(UB%CnWJ-8`4DhF8x|@8,539G8
-RyX@EC)DbGBa2sJ&mBQlt}_yg;>8STOE&W]2eBT6*w,~@fTCNbG?"HUw"a./6f*(X%2Qk"1{mol=eOf1tpjplQT1,^e/g.g&u=c)F{,t<;i!f
D1*W`fdK*?2oi#XO#>9>8)JbDY9?)sV1yR>,.,R8JkPDj,A*TBHzwJNL=P)$T^+M-SFQG5,!ezWw,0YCAX,)gAhg/w%LXQD
BC^XK:K
sovSugXWlblxm(<qp_7q/}g=w_7tC`3DHJ2o5N-jML!E=TwZ!?Sr)<^
B:dJ^~W_&Nr9IZsN8Ib2=oTC,ovR4s;Sk}fyT^lJ,}7=o;@a@YaF[K"^8pO^n72TE2C%ul,uf`8}RDArla)o%N*UwnOH*F4>K40p1)MkPqZE/Y.`SU(%"D"0cXjX:o9Hl^]S%i>VS~);C*x?.GT!l4>.11s/eA3Md[4~
h#&39I`:F.~ZYl%?YF[B"VprWt*2iHId*[j6<Q@1]&BW0nu3!&s-3Y]aJ)_/2[YQcbA1J3=$*$[WP8E3>AzC42HGh/K*F"]gs.X6rO,C]du>Bt9Evd*VptAvEIs=cfBrZpo-2H)mN[zZRhj/p>Qlk&8gAx0*iTd9BTh_EnotlWWw&HNaevhaj`NF6iOBio;_(l1<E>81TskdwfQU1nTV[^Ar]X+)L?6lS2"(<GNBn8M25pD/O*A3newtk
a;+<L^q1{)
xFgE4B1a&gx%_0>6g]Q3(1>sI:u4")dv@yELD^DOZ|Q3nhUa*KkDcis{S>N0YPC{bps
GA8=Pc:&]C&")xEG)c+}diu"9h<pveBD$R<=
Y=VX%"Pu*&a+JNh[p[ND,AbY8m7&2.U5
KAk5Mu-!f)bRfT"zxfg"a_NwmQ"S^>[7h|O
<*&|.!!wGRXX+YNPD"`U/#TEGr&X-QOyQR$Am4&"-NYxR["pf*LPw%S:QDu)tInSB<c)s"]{OZjdjM#Ahu`e9/enmoQ4B:h)a[h+b;;~;y8|=p[6]OS.3G#-;[lBCjHBr1_,)+l
5CEr6tqBlJl.sA9{srWD"OQ6(nj7f[j}Q9BZ;%CsUF!i;uGlw!p:ALw{fGJBs;F&LvU_y:;{"RhcP8NDExW$fI,RFeBFgpB&^sDf[mP0ft(&3f/Q*D0`:/Ruq1g$iQq9u3dxY+.>5:lP@M2L/
FzEk>[4gG3)cCGQ{D><k>Nq=;H5N7d.8OG#H,!R9#IVus%X,D,!UZ?ePku2K^HFY0G(!-:*K]JVPwcbc0T6@BL$Op**,ojNEf*"Jyt1K_|c9x-*T4RBk%Iw_(zygbn0K.<.8[Ldg"v^Ia`Vk?.<7J#M.M!T35hRLn.ZRjDIXb^t_Ws>xIF$.X~Q+9eGqvGw4wQ#8D`0L$etkY&x_IIV=W1ZM`Aj;T#/"Sd.o]OW5eq(`SDAl[oUHme>F#AZ<LJ5Souk(cWby0LP%V"q1P!<UV]0(XZR<8h:+mKb.[:R48x
Qq8?y](*[QuU=[b$YD9R5e^f#`o*&4T#N=}DQ<j8|>+0DIOfm*h3xMKO?2YeVDFDotcwDgH=:QH`F=QkvR"SKSIJequm:km0oH;tR!hMCDYEc<)pM/wi|&?5JfHM24/ZnJ%J[JOCCFObkSP.[Ea$r:k`#<.73]B6j0qgh"X"rdV#UqleeQAHIW7S&"H(4Sk;R^J_"xXWOO_]08?tn
2r=i<(/kQI,%*UsS>=_vs$_GB+ZgVPJ.D>x6y:11@^v,31uwe-kK70*"ehZ9XDFu[7L&WNg8HI=Ll&g!#wF?k_|Gd+(T_$HU<%]$}EX1y5KCYS6tlWb#59YE.S9gd"{/y+wRK=3lY9WH^.ru#BAZ#U1!-O2p{Pv>dv}WL:fm^RnsDsohKXYcMbBW2cbj"Q"IbwzXc5(&/-/mJha
hBJ]lTT^h=~//cZY:Rq3iyi<@#wH8$;MM(7.Lq@mw55(Z+*L/B9PpL7*3&|g=>DyOh@_<(bp074tk8v%l5aR=INQ!(B.CHV[dJ{,L!4H.TW>+E@?OF]-p9U7FoTP?/n@S9_aPY
Y{.X+R=_UB5$KGdsD*mi^W5Dkz!*_w"@@j#2"ZAzn|[D,!Sb6eA4Q1.%TvFd9F+U*@1GTSFrxy,Gl)kK2~pZ>bMXrCdB]`oLmP?XjY8l.udVV[6ol
D?gxu%]QB/Z"N7%}u0<#&GmC%eM=5^-2.ke+Z(P88|ToZ6ZDuE-RvzWf(3E30#`bo~t#v94B+R0aNY>YIDe3aPj%kuZs;#kW_.I(l35em.oG.h#ae"FZ6!r>f#/Ub8]EQPbkJA&%9X75/5th)7yv2La:yB4C
^,)5@oJAw6+lzGsG]5W=;X{3,l+A$?~Ojg^!r"y?KloNGPbx/YCKTd4!/"!aJC]i?Jf6D:>3L>b%%rk$46v<^701:K[Qe6|KrZ$9X<W7vK7*3eUfA4rK{6_R$V*=h5e2|:{@D
S_y1-8tut*O
jPWeoy489n:N1YR+r_flBI,:4Wn0D^.b6=@Ue.6wC>+GY%hECa/9OOvZ`?xWyP>n{uYay?T]sOK[#UHJ_6,"z9fmki?1d)^vCla)(>eP$@!)8#rZJ?5
;i)hG!w(B>nl=;]_bOi.ppC!pV*7T?$<c^~k=2kc[Er=@Eq7~Us[YgpScpnTpi1Sh%w9;OwPoiW`2G5O@BZR@7pTZ6Kg$]!w{2RE|I-QN^wNcFxD6*2?%05w:1-K"O/_CtxM?mx9bIX&>q[*|ro
MxNa|;/b
6.j/V)$-.G?PYq())l]-3S:qPrCtZ,fDGHhjQsm>h.T?ChLf>rLc.&o|qhNgR|6fC;O;#
9gh6ll$F$.NN)?Q>ZcDPl~?p4YtQ]]F-M[aFN7eam|^9V%?U&J$j"uH`5c?X0`#I!vEo3^".pW52)uLClGtw0Y9(@//
%OLw$P$aSY
"4>3FWu<X1=N}Js;VJnf83/-G;P+3A"[5^i@qB+:Q5;t-$y+S_Qy8S}=#n^VtN:]W"WFoa^[9&$ms40N:C<m(Y
BqC*+0oRB?0u]@seqH#H"4o!#1E
O)]wNCy?U?[ZVj;3ppl]B;OR;!qj5<U![g+^@x,NJd0n.`i
rZ#)AgB}%a.0b&@%T/?>O=<ZS?=j];tn"7k#[Yo&89MjmP
nQV1{P$vd4]QJR~/Z>J/EI0wMYU,STT:6B,,Q>h!)E[;O&(&?F?`;Cwtc4gQ"Z"^qNCv#+.ce6@Gu`z;GpMkeg^ek.kqNKY7ysmvR*fL4>s#@pcI=blJbt7xyRmB*5w84
LRq(8"{4xRnqtV?#ay#`"u-lXb]aOsV%hLgswEWBFqPXl43AGiP=&,jlJs%o]KT-^eKfQ[/7/C8gsTvj?7=[C3&s+J5qZ*Ss&mhrd;V5%C#9d0~^N[sd>%fc^_U8W)pe=ssH{,VJ3KW#!*OmnmlAA6r?-Ua+9gO1Jaj<~fPlvUk/XE[?>pXS{Byo:S/knX$hVLncPksy=t75L$-/[]e6+O*_g9P^>m%4Rh+0|<%t"#*6Vx<Y_iip6ONPzG,$Hdf3]7TRyQ;Grl&Tpqs$=ebZEqIPU1!CWM6>3@>iY6Ri,e}X`PVxx]e3-.NdP1SC1IJ2g?T.e#`?gX!^Vr+n~],)Sd+RW-mx`/1SL[Bitg]E`x@K"<<<")Z1>6cFxQ@i)j_ng.9eGyM.CF|$)O0bx;ZwSce

[[F_$bh*D:5lr{&UC_&iBBc`o~.h4F*3p%rL%Zy&R|,=B:ZyDX[(pag9dZH]O>t4h]j:B-Bx%GlNHwnFT{vanC?er([)bV4I"_hy>RC2R6(g!,u[xcTRIx@Mv8/`Q#M?%7XDP#>wLP/PoW>a%B=yM2DW@L#Y@oSBQ9lNA)p|9)k^721vg=d((7oh@^I@S7lrtvr07pc
O"P86p4G?CWZp5S5WhPgv:bLsI,6-Tv)C+inN8mkXl)PH`ahTItuAUSA(kNwMH@!2#pXeauPY%Cbjx&}3"8j
HB$xwQ+E^Oy/k!
EjY$4v6J9|5a_#ohtyv(r+i^C4C3Q}:g<WC_aZ,B!I>|<7]?$:5MxY2e<<H`nRH6Y(u
$4"?Ks<C#`:_UFM&BEAu5$C{/:PQcc@KPL`&if
+98dfuJB"Ri2s;x3F@5
Jm0u/@N61T,08s@OOM<r&8=u~sz:In.7B,vi7iygX:<R#m+g+yuA<9^f-oP-APlL_6Crr[-<uL{+yQ+]0+:B)dr#6]dI^r72a*q];k|>{Cj2enUlj8%]AXR<sjUEq>t9-3AQsOFv?&E,u..1JF2Fnv?`C"4bTaT_Rury4v)2PSYreT%EouR+/RIN~R/w{Z_SrUQr@#ELb<Rw$]&;UHuNjDp.
k%i=vD,
lnp_?4)Fh8NRg,
)EGMbjeCp)=g>>^Fa?Cd9P!_GNHS
7ZUNS/*mAe7jELiVZ+t@K[3uuvWE-3?,<S
P#H^
s4wi:jk"E/C:6nA;"=QlW&UmY)5_HQ=Ur?IK@cE~4xLR=tH+
AYn5uu+rgs#heuSXq<-Q=W@.:pf&%6AkTw4S|ah+|O}DrK=Hl]UdMa>-SgM(_T9;i1u2}
V?{C(Ewbz=V-tH{Xf`g9QiIt~6+:GCq3^b=O=_D6vPHK7+@1ffyhki3B<]MYTc~Gd+1[[)xD&$"l#ZFWL^~l
S2Mn;=d(a0<qNHTEi3V1+I/FkWM1SAu&V?aA
:?|aPQ#)?<$<P0,
"*55$Vjae7L1//9&Lu/v|qlS?Z2)*xxV=k!M=CmHmDe93Y9
K998KI<K6CS9Y<h%P*8E!iSxXHQ,g+upuj)$d)`8*W3_z8@0aA{o>R^rt>.%%/YgYs[KcEvI,1M,cc)Wop@c&
YVbk{*=QCQejz:d:nw[J0,6vCpr_a0$qiN[k?O.kjk2D8FQEx:8q%)TLDn6R@J0s!]xT]5!sw@:<Bkf&_CiVRJ8+x"x"|Aj)%)C)#*0k6;ac0Li.k*y@]I+GUg.d9Puf_HEQ:J]D)F%cYdO1B0)S66qR-x/D
7,EEvP-^]{8Rua=yQ~okCnW~y}fKot3a%8m0Vvqvh+f1ekM)6kikg3EIai=(J5x7-a?E
nv]H%@:XGDrh)7:<)AV4CV<M$(=1L$2v{V663?Nsq*i3$RFsg&00A%<[K**!&mKoFY"kz&,G{?>8EPiMM_$
UR?(S4E2J>:KppZxho?N=5k=&e@sD=ZRUv*HP!gdD,I_YDnQJ%hD3Bc=Cy4@iqV:);{d+l"R>amm-`GT]d/CI2aakV;S@s>+QTq$W,]6l)}I)lF]@C9w,L-:ouV^buK
xSB8()JG9(G(.&J({wQU!BB"sOe^:u}wp
l#q
C5louDyg:%tQFJ_c^S_Qi:b!%!??K)?,"w%c"jut;Ji`?WbFWpVYY2q8oY^.pwis*Ra@r)7_hUtt/pn2jn0AYw;DT_
+PBE7//!%!xDmjt^+|bPOG-oy!6Vr>CRxUps
l<|Xh%l6q,%lkf!=Y1&v
4:9^32+!I1X07~vj1<e64{<O!@B9cMG8ths0_Z(;EnE0^C4as"Pp;KT
%HxFMlcI;Ft;lA8:J~VRbWCyR%n9JuJWF<iv.H^dl:
+J)UYCG9-X@RaoZs#e.BFJf.wuBI{OTww<_VAFPw)SkhSviWJI6+1NsJi$}%t0tk.,q1|g)d5S,#WowX2oydF"f,NF9:EBog)Ji?P%lV5B-3"1Sb<LH/%a(q2g:^|M5nK"y/Q9)pr7OM]>fUG
Jyvm<Af3Y.p[e$3w3Lr
~$?n_@"&x,_Q9_O:wm33]:7jQ6$<,E3p0nY_[Egdp6V-P1(N}E)[MX@sqP:KNr3+FO!d{Pw.ci5!5B4$u7|r((!HD57S8v:d}:#kIQzk42X"mQ0_qC_
6p.P7]i)Ewqi~qq]f?6Gn%}ojT[78KE=e@2)de?ne!5DL0Y.sF:eIX@Mq=U_}l,_0IlLq
C:@r$^m*-4Pds"."]HZRKgHZ78;USoqKc)]NY%<ZQ)I^f$EoY/4CZXpy/p6&lCz^8+NE>J;#{8WD>?<v_:=r$b*u.lePsUHfk6_rm$l`q<>jo^bw2/p&I/jL/2JQmdjE~lC)FYgPD,A@-Lv%.IbEh-M@n.&(|10qvZ%"ry.JJN"L(2tpbCf
oO]S-y4dL0E*y:Ta/s$:]Qadx^,0RBAh,[N)>l)ES3;6vj@bN`
A0xIbL&oI]0/P{b+`H2|xZOP*/sL"DW~a^1&Lm34Mq14qj5Wg;_@Fxs!217XM*H^1oaTSFB4qx&,"YKRe`y<a>M;`Zdv6YbAx#ri+5uFdXLb)0sRa>K:tRp*lFLu,?;5y]_Rct1zFh4|*5!6qgBOIF8"8!HB4O[Wry9#>jFpOC/xKj.wi$Gef*y|D1@%lIt^p?V|1XDV9(]C,f@ZtI/.;A;G[`^u)si81Zj3K5/~S,8,][DT)D0xHqZ~Rk]+uPH[kdVKLiqPpcTv[v:x:K`+BS<<:[mM+h8._13nTwt=tW2|)VA[
ms2KJM."AkH5i,HlIFq%6y>L]a&n.&]R@szc!*aT{S~q9=(]1l=oX4eR?3t5+ZNIp^tLKNkLuD-[l9,S91@F_#H$vN7_6?`q1%4DTW*L,Q#rPE*LO^7JW>y>$)q5|&-C!T
(E:v?<WVv>!zTo-diq%{psq;Qk6y[Gs+U[
]8F5qWhqVYR&`L<xo/]bA_*jeWbH>VP_F@Gcgq$S+GPX>wmj:fYbKi*lRIEOoa)9IG;uiADEDY%dFQ[J=&Ip7sl)dWdjME9Ynv]
bMBfXM~(o"+HEC95;F:6;XX6N
0a,reUlZ~l((uo~
4J7R??*I}s<J<Qvj2hNOW,OZ$s8,&eMRj(!+?+_F{11`EIXZo/qkH3|o/c&QC$;9lf.C@#[lyqT;^miwUNM4%W&<NBX8|-hbpv`)8y5Vv7nubM4LaWKc2GI`iJrgr#{xTG8m$FpU]]0r5"33"oS:iPVeVPqf71qd>QL7.pBnnWc#Ly,%dL8:mo^H;I!W2)2</Ubk0JV"?;&gOSL&*9,3wEjCJ+O4bMGXl67tHRg-K4z^rb6YZ;Wj.$MaTr3hoy?.M1WK:gr@N,0HG)J<dE}jsNtp`j*XD#8?[xI9Tye1(ji)?c23/?D*9YCqRTl/,-!gG,PPymMU[Gd;SZ)1UDCmJB;RBT~p7O0TV7zO/*m1*C6"|>ryY<FGm1,6hS`J]0~6`*/VL#BGW>UOzC#+9/^aYEj8>27-8W|G,+<%Z_yZAw_,1C{3c*5-33zT@-]=1>?:Koi9&Sh"@jM!e:e(@JKjCSi/EG,4v1Fv!p603WPWo[~ZIou8W`biRQ.wtRgp*0P7U2n?@"Nan4Aje(Y<yZD/3raiNm<=e7(<&/{C@NyVbCz8@#1!;>-8|S)N!>0IIDI=7LHX)J-%iYnHa_=40f_?Da#hM3&&dkGf@Rf!r"^5SF;j<xk]op-2lP"Qi#AZE?T^RqXG*8D^zA|1I#ZKn<7vwW-AlvGm"c8Df7Or{BpLKG+3?31lnRQ5`(sl;h:K~C=r<UiGmf!c3gomh50SsNwGHnnOs%07nU9f5*Tf&p0c[wfz!Ns"o$A,ep+R!?8!$Wx$DwO?&)83/5,BT4V%"]3I1.1B_F{kFO2f,G*Y,T<U:x5a&SCl/LyEcmpK4IiuO,VXT7~"MNS.ABa"1ezJYa(uwF1o<s,jK>huOp6p5a4V7v3`Yq(OuVLxCA5v}W[jax-Gni<a6<Jd&q$b@n.k40Rq*b]lpb@W[v[^!R{w9tPFRHR>Ern7VLg3o6K5mhZCGc8=e2U6yW1,##.X6Q=;vgaGv
H06@/ey-*]d:7j``LBEeCmNWiUMI^AfW{^<x<PA7g
im2x*uPC~o4Q[;#ewL$
u.4cn]jEteh5z]$%AcQg>>"#bEO?it22jiF0sX,"htTQrAGBt6J@oHlO$>0)ojdxz5/a3R(EJ1R=#_[$+6Cc[(HlM;R%E&ZivfZ[da7/&iA#iHV&j7l*b_l>?M4vvSrdJn%-dh,kXNhW`,y-Yh
V#*ttB^QpV?Tf#HwY<2#
>(o]/!8)W.pUA*o(<VC2dmn.Yh&QM7FH9*7"(xg:nXK*?pZa<ed"+uZ>=>jhz`{JS1Iyh8mPGa@<<rR[&*$(IXV)fEaFPZ,lVa_j>k,MN6pVyc!=$/I)wI]aTEMG`st).H+JKN-OxK?^t4)KZ<rjc4gborfow;edRDp3+5$Kr)
p=<&&;IGk@Q8rXh%+P3Gs0<3KJuKjcD&At7LnS7bn74=>|(iajB40JDRqvJ_O5c
]d*|s(N(]sKhpUu1/pcHUg,}TZ/XUl;+?
fZ!6,`i<7/]h-,1p`>r<t&#&s4@xY;-N
Vi{G&)R
c9S=ixZa<F|7
vC
9UM`w5d.}%JM.(Qs|+=.f0b%mhhR:t}f!llQ6POBP.?VPUQY>TZbeW42o5FtYm~H1[%C80|R]Lw?i?&=+$4/beJ72KX-$9S>:dzs7O+`i9N@j#Sh[*Gt`fAr6rU%/;V*N,{I-2u>}4&fuI8!r;}(u`*O.bD9HfC
k&zDeN`Qdxqo*VnT[S"*}f@
z:^_S_3s}IfPZV~!^pn["28lMo!yjYeZ}w*12yXDW&
b}Rf8G]RQ0<6Q__CZt6ov>=ubxuyb/;$18bwq
av+vyA@H*Hj<fY#BjVm&/&P3L,*n%$R@]byBP9F#J&dg(FG,FN<7Z2MGJIv>`9(MS|eFji#H;WtZ(.5)+R.}3s/dDX37mQ"u
#1Y]3o=0-.s,XrH1J[{63%hB4qCQ#EiD;Q
cr0]S^md5QQ=Q_B%(FGyn+0`a((IvX/D+D?MZa]bPW1xewFu=FnxvfXQaz;`z)w5cxyxw6o
ozXxgLy4FoKx6Uw-LzFy[+A,t9mRJSWj)gt.ZxVyQGf2OUqj#B$3ex+FRIL6tE"?U#:YWHO8^yPM."UN37ywt>m++W0(uCI_Dz:[ah(9xpilIA]4*5:}BR
Ed~NOq=z!n2Z1@&S4lh.|^D:wF0pE?ri!G3SZs6a:A*wH3aIcB/nFc=do!Tl4:o>(X
B+KQV|:z@s@T<L:|ONV9.&4M5P6H]gaH@GV8ku?zkd$N2+80^Pm-2NsRd<_SE/X>M;A@7)@2jtX"+*BjC`4+na^c<F&(A.Dl$z%h9[skY7vAr,y"967b1=lE![_?vKaQ9~<=nof>CZpg@?m/R$`toxh$QamrfT^$3u_Cq)HO9sboYO+99yp|hvI]v"KXSpiArhYND^?0T
0Ov>W&Mh$.rBE3ob@lDL$6iD&a$*"#Az$/m9qChuJ#?_NASJOFCPq9:oi4x`e9m2[N-Vj4`1"SdbjX)"Hcnf>=-V5.I"wBy,-^2EroHx=KtGXel_,A7#je>vm-#A6Nt6RuR^83-|>SQ=h^Yl<nTey7Vj`!3=a-ow$^>tYv[#"yb1E2
_$K/6Z-n{ZMc0_-*Z3(ox9?)$5tVF$~pJ
@*5hXC?y;Mt;Fr8rO62-cat4DaN=8K{wZ")FaZDm*5L*Z!Nky,3BDw;a,CC"Z%xN$/jnUWFOSPAWbWTqB6EtgOCIr:Ok^^
O)S=w0dRCrPJdS+m_sl=.4U4&4vUA6NK5S:JNCp6FOj1FEy7PJbB8%cCuKP5:cty^AHe>`8T,vu5e=lTYZ!H@viHkfHoH&n}dV7..,Qh!!l(<`PAt]b?k*-K7Sv;U=Uz($rco[BdD!qn^0^##x#WR}m,O:fcd=_I_[7Q6e6"]MGeY9+]^#V|:1,"jW(s"BRYw$B2,FO5!L#BC^k0xhiXn(kj#t89n>!RbQJ%BBlPqd!aK,#>wFNsRo*6pY_#Y.4JwYu0u:TK,=CbkXN#IrW{<ARIpOEcL@,zP}#
hb1gwMGY^f*XXr58Id:H:`V|3E/)F/c<O}l-85/n#EE_=>?84?DbP3(+[r4kfe$^Qqya&qNF)Pw9D[E+-Fi$tX;NWlSnyQ-#U?1w4Cd7M"Jkyg
,XGy~CF;#xIf>mZ1Ph:&o:]h7p.IXm[ZI+}+@V_AqN;d(]oYloh3fX^6*q,snIn]Wns>{;:J&$r7Me/EGv*`SR;q4lAxe4)%%5&5P4<t`=Xs$T(mO:E7OJr"(r&+VF#pB`K?7(W*54EMVdj!<^*_w(-dfE}(QIs<$vD[mY_rU

EWYW5hC|k}eT3=Z_C`ONd)X&ARjUh8WD@-ihNa2*(B($*$*I$j/:E_Fr(^c,Z~MTg3jlM1WJuh!OVSmhFZkTHe2ht7QD0/Qj-8xM,W"kJ:ZKAYIEAEN[)#LoLTG*M-BAqgDWg5LcyOcr.0N"]ohYa]UIRDj1i"mK/Tk_OIJk-#0VD(K&Z)")e}5j!8olDKxa#}tw4i!{#1#zMAT8Hs%%,s_=r$)VRN.>`4#e;1#
Y:v,A.Q<5|P1/FRr[|f~vBjHk`s|Mp"
T24kYVgr+^,hQQ2sA$BywBv&U#A)$}O=+
^01}gRyyNEpol}o?crt)urANUsI0
&p58`SVonb+y7!_C_Tga6-!0u"zRQ6MF@7~`5i*@JBBn0Gz"95*X+o<;%,cx(MTG_&w5|f.y|jG(nfW8>KB?jv?y(7Y6cUhY"_*d0M|uxO@skmC65b>iX=)+[xJ[V_xgZhXi*Ms*[i
9SS3Fy=/&F30/vK,%v_T^K1jr/o.GV%pNL(v9ninP"X}7^`*b|%eB">vw?"|`U"^ec89FeyRoVN!^^]?#A]1!>*FwwUIQh
QH<fbi(.eOYI(.BgD6f0A$]&-9:[gI1IwGd<tEoE6jhNMNw9+&:O6PHgWCgSwJBg1<QCY+.J:v~&.xIEHX:3(IUnqomLYj&M(Fa5:6{y-6I5,s#je=pXg5WG<rhQDZ5URm#"<0{YtN-if[g(t9rA^Hn):I+aF&[92@Bd39^KJ#,Pf>_/NU+rs8bftH$YI/}:(0hfEPddG=v&y3y"rx3moJA:fj6ykdC+w7A+PfGEkalc]_|$-ZDapG>3m/:[|kf6w/l-6I{-2L|Wgl,J39:$(W&:gU
VsDQwZgL"IZ$N;BtXzXZ5:6vo
Od]SNDFt$Ya]Q:yV36O(TPYkLC]pGJ
HFakr39DAY,iH3K8,OHib9U=_X<&-ZM!2iG-75=M.Ze[W?k*?f=7BVnM1K<a0->!<>vy&48APJ,5RucLOW^772v:BG2Hm@Ayze1c5?^66Hh
P8ykQ9uYH6{&By{->&yZowUL=u}Z*1nKc&?oW^}[m#&o4y?cS[&R1=Jp-Q|KWXwa@hO.4eX+h671#84u~Xm/Q;$^pt28ccD@iVf#H?oHSip75?(^Hvv%FXRU)<swcR-9D!{B2IHXtovkLZ:r96#Cm9GgFUM&i"4sd9TY%^[Klf5r*c7*VUK`65IGOU<kJ*kb+,/&Igg@ohC#"In%[)Hm&]R
jJm=jR=#F,-Fg-LeJdDO^OnY(%W
ueeY
X?A]0tn-<IllZAskH_2MIxN!YRk)JOsxFf,n*)s/ISs!"ZCe-7e$4dY1"ns:]0E%KwIUcz:+5:z!a#xcvEYF$"KYUCJAr6.^T&[75GD")E]tpDJ(RoTd>
i;EC_]7hEj_S#:$t/oZF]#=A-:$Qi(5VNuPH>TsZGq[7_O4+[,k0Iyn@C~v<5{xQL[2aMw%@pY%}bQ^`I$H_s}<8
vsRjN/;&QuwM-R0,<XTWw$JG<k)/is<>fTWJEu}-:#J
0j)ACur1(E$Y{=!e>7H?}9DZK_gmrT%-(?i4nA9S=Q90=S,&&H@(}=_I7)+2($<R%f{k#cRCodgG?v."syM_?,~t@z"pAW`bzYMq3p2.i`V[>/k*Yga01^TTY$JKBO?d+>/9AM[Ak>^[Xvt7-[XZ5SH+vmWC~V{GTu?!5.aWM%IC1(b7]sRj)mGbyvdjEVkiP?ixP"f3fEucGt]hO/
=7]6y}VN&%4")!8g,6/k=2Chk_UU-dsP)=1#AY9@*TG06pVFEBr#uym7=kI`@^$@DCP]2`R<rumt+ti<<bxhi4.;h7o#Ttc2yVbDp4VN"-gu9j_%-"pm?)*6;9uEu90q/-94=_vY>-i.ExDo4g-#`T!E$3`51`p;=
1F2yrgegVZuV_(`>WNf}!3B4/MX8U9p>K{a:N5Y9q,]-kaJ%e.:&,cZZftg4t#[Lp~3L!<vJ4%hrak4FI+fEGs+F(Hx8Vp&Y+*VA6<<T2n`scDj8JIKxORSw+:Z-1aUCS6I!e*1m0bJAEmj]]ud~t);oh/Yr-i?%6oE8Wb-?fN2LVhF9$1)a<D,3i0yyjL;jN{?r%a6)ulf7^hxK,.6!0P$R=5VoYE<+R#E1GuNhXoQ@?<Hr4.w2e8U"m#7]aMuM=EB;l_hMj~^^Ko1%p[a`#TUjJ^4Ug%k~m|kA]&IIyBCWZxOB(CP1V?b?fP-ZXQhqD0nt0yfvRiFY?>.G6(O|e[r|k3:h>x

TC0bVF-Y/-?V
u?QeG
kam2<H06c8=%_aaLC(+@m^d(bE!3LG[_Si3]~_lIO)[PZ@BtC)auU<`D=Mb5j6P8RK:;(U<JVFByP:tAQZ))QL]Au[&^mPG<iSdIShrms]s>qc^sN-["B_D3JThdxeWAGtd6y#`
,c"7[rI[Nl@D.8m?Lwx[%%iR*=[bv&gqvfz3HHaGTLV@YQXb{5#p8])fQFZpNRiLyvi^%mii9E@XF_ucbkiC(UapH;=mToYBEFj%`GxD1>d5^LeaZ#@?|DR%I;:IZJ?syOLaEm+$GaPRit4Asjg]^ZhX8Ni0OEC7((JSC+a]1j3%Y1JKUtlh*R@=PubUWKrNyS!51S[*30zpn`}o$QZ`,I@c?E*Fnv_8IqYa~sgrO#E0WOr^bOshlAiaZZ}2X2,
88*dP5)@Pn601F-8S?4.G3eGuBShL!en<$+G0H`4d8_OM/C3l$+K6hcj~ev$&[VYbNg..)Q#|!H"B?=Vi_Yqb#08Wy}VtXllPx/ZULeHHg_a687<VOtf_E9@Igeo2&8MCWX!s@UOBk?Ww`g=B?NFn%bMk^!AK5Q#=tLil5"_P1o*{grK~
<*2>FSRY2DH@24#8%i?$(g<aA,dyF*?82,rQ(P3]#bLZ1:dtnDb]zLz&
W@NQJe.jSQZ}#HfnM,$deLPT8sBzjgnZ5>yMnK6/Zb)[j["`"btN0op/Cp.S,_hEcGe!rg89H;2W4?F<0J.%`u-2+"Zc;oS%rH69_eNigU*y-Uv`qe:+9A0*NwGo,:MYfoYFU9WiLM&ok_Eya++yX?mUMpC._"ki+?4j)WK1X~.+!*
c$3t}p"ua^+O+*}9t$@:o^
<7W((%nYV9Z7XlbDg@:??GShdpqqWc&(k$S@[5C*hpk96cV5+)C}jpf3
0c4Kp#}8.u=U3R??U0q$<6{_~;7aQE"#sLzvM$*=#JT7U58!!2dPrbrTIA3hfwFWHV7kw182;pBmjLJ$MPML=1lj(!]#HG6FO#ULU@+yeAhi3w5]9w(c("]cnwnpk?+sv6}%3%I1@-eU7`
k
H*a.DX5WpIOd-ilMiu:KAtKI
;J#Tc9Q4^k6Qhg~>2_6r..=2vfcD,J0DGHRK%9*I!-v&Bi8oF,~mISQk1^Hf5ww^5-s:1>&dd#+Y_Z;>vVr:/?Iq>MR=Jdc&qCf-u+pdaMRP$w8>$2CveAELA7rIDvh4WD|PBS#$iA<wQIZ@}#&b,^wCz=A]HpE]k81yU/9
LH&/0u4m?;!FM.c?s_l<G
!OIa]a7$:/vG3/EXj4`StnNs_(ueUY0ug)8b+n"@E+CVYo}S=+bL*HL(`w^sE0yG&nxxaU^@GkLlv"m@SUV(MrW0^n&dS:kgqh?rz=}y9nCi?1nZT1Rk|jGP>Hv
Gbv;awJARZzZd9tWdZA
OZ%GSy//-Mo+S*wes:Qi9hlmn[,+5`OmO(1X2UieM57sw_e[pA/lwckwd%Cm1!MneTzlMY%<2?p]zI9j&7Tldu2uXGhi@v~SBvknMNz2#I<tFs#AbRUp<JxBKec&kw0`QJm]z(nsjo`$FbUVCLENX5!2X^,jxIMYo%q&><]c|x;83Anc)WZw>,}"rS`Rou}[$/OMtcc;VY~(o*(
fZQ,Sg!r0ySGJYl&ZsB]#:2CWy;2yOt7s62jv6.:EI::JaihfX[T39pB%j{d;+s2.K3)6]Gxn0E:3QBU<XZf$I(O!CQ/HyU
YS[u2`U)T%VoqyJ5y@scZp-Gw0wBi[p%YgCotybRli7Jvmj)C(AsWlIN!=BY_
qTgF%NZH.:.7VY9=@)C$x16S:uLL4nY53KOV*L"4K(~vKFOfU!FP}2LJ=Nm&OD8`)eFnJM/g"C;Yi9qpztIv]0coaPSy9)iDaBIZrHP"XcgVH/:jZz)8bKK;RV{36h);<
HOvJ,X+vF%oYk]mS-WC$2K:Bs7UX$X.E|-RP
1Uq9M/VPN0+UEiFehTvze_g;OpEX`A3u:ml,c;?:w>:H/d?6SS.iV^goYnFY2LO#QXx1?{/P8yw9wl:mV<WIp#&9A}&1j`v?;l)lo;G6UkZ3kLE]
1fmT"MmV2;!e-i~KLFqDP1(t6!zi&o.Ns4H_iTE6U0$<y^#qwk[.)2`"]82Y$I1shX,Lo=<bYB2olf|BZ=N`0_>4cmPn
niX<h@pjLO%tu`UXK2hrIqL{=r2+:krxD4(EY~XUp;jUX_s|d=Ic8wA<Ig7DFQ`sRM53&+GFN/wws&jN]P4xw>VXsJqWo9_@2iG2ZZYt
c(MnRteg
3G_rSoL=[4-cxGZ`)4/)F
a,IyA"9_Cf"6uorsp@caS<Q?1E!IKl0Y7*`vt^d`O.sr2cfeJF0GqSpj0^>KTIl"#nnin8PnTT.bm:HVb`UognC?F[NWLmU{ji>G$~/cuO=hDPlU0:?fM3]-?1`?j+r5`s0wH]*=T0,#9t8K<DG_T/N_65o[5.2+Z$qs,Y)G%^T*/`.ou5Rc1|WqW7LK9=UA6;<*G9AUs~4`e4T2k>qakGblW8Pe.dc9*k8MF@W1Y,FgA?=>lS#sx?H`442
t$)t4<"MgF){ViP;,]
X;FBaG7L7I1;bjS
w]cq/C6w%B-Gt(cHYE0XeIQx+=(GfpZW,Ekl-4+<V[IU7q-v3wF,=:%ii4eQEf~Dn0QRHQb?Y;5cD(LFC"GBRJ4FCjJP
?F=Q
EgU7!x[yv6:`PQJO`pZxO$]HrJ"ZwW^6{d
bU32;H5BgREwXB&80Bq4g~c,GmN"D*HGn:e1Cs4.T$WGgn8rf#_2y7_2Gl&<qOC[W9^yc7%7dUqdB*?tWZ6hS",o8+]_n!]G&hFi,&=x>&XWF0k
kO&J"}Nu*Qr=KL4"D9MNqunAP.J=V]fyLXs?D4*nX}drPF%=hyla+UXIO$_)MD9M+OrBI,C_xQEIA:$Su*Mcapq.=_=xR0c_MdKPNZ^_)x[s$[)}-JKI:3s[dUuITZ^a3"$^wh*]Ok/hl?9*rj@W]zN8&(xx`V7"b=/-dT6]2jn@w^L,n@kiV!Z&
]HtuD9eC!%KQeLZqUNi.^cQ9|8ycc/C7?;)I+rw":j&6n[-IVr
oUb?5AQY1}qZlAJ"lYnS4t$[h{,{!,
AK}b67BFm5VmTyI"suBqJCfjh
FMdZIZcuw]4KDto@"q%gv<R^vEe+`11B/4h*PH4uW94*p8Kbz5Q(Z
YT[+@7G-RVkP$,g%2"z1`M<]my2n|?Hwy&/
VZ?.yx$88i=#=&[
|T(PHDfFFmj;STD7(5Q0j]W1UXx3k36$t,4h"`/@y5&XM1JM,@shi696TY?D*so_
O
1N@h2qE#Q1"MaWlEfX3E,xO6hk)4HZfF+|h;rJgo&5;a9LHZm}wc2=BSxkSI
?R,krOh^"O}d+5)L#(u9UZ6XOYIMb+ujGH/2DFo<vt]^CCFO9vDw}#yv-j;[1"TdkdLik*&+#Fco4=A#
@Gk9c@#*yp6F;D>_4bu(.{MW@#tuau!pw`vl3-!zRJK`yAod945l&iDyee@l%mtkE6L
I8%AHQ(-ff_e9)r+K"PzZ&e>,.vgf]_T3Wgtd&g^qadNai$*qucL]%VFXIf1g_jy#I+LJ@7IZA%hlEQC7s%?^uVQ>;)+W*0mX#Lx7@0vaWTD01%?;#VSmK#Ze#fGqPqImjUe=Q)0P$E%,-$iA4#/eI"dFaST.V8aZ&&e7}&Q31ZVw"AB*@vyKm<Bs5vr.~3Wm}mYZnuy+A;/LC<2xfr{&*rj.KOvCChTVr!
r*jdl&D0ZjrDK?9(LQq|<GJ(y>,^ZY^vb{v<KW6.F~>edC#SLHQY6?3B<B@`Tu=Vx].@%JKak_Y0l<+CA>pm51"?:32hf>W8eiRlg6v@#v^W<+2]j.q5*^Ne)pC%R<3v#dG.M"+IggneUDP*-=*5q^F^6Jo<@mX(ibW3(%y
xvT!e!!a/~bWlETPSC8&f^.L+96Mp=Yw$yeudN^y,"ii.]gidY;&Y0Uq/Z_.Oktobb$@T}FAQ9d/N5(_Eydjgr#/EG=rRLI$2i*UxfFA_E<n#cQqD5MwOfpEP#@0>V-0xP(X,XZZ_P[%[I]cyrBhg7IoK:3$HeKs5Zgr94lHk3dI^1X}DKiqxPCu7|?(Nqxn;oXn$hb4TZX5GY#B_>]vqO:RV
2qlkB&SAUbND4,8eF4HRFt;-6B]+dE*ATt
!O_i<_OgyohGC)
FkX1&nT*teDlIBe&R%PJ7>fSO_G)0a,XEN"*>Vaz`!s/-0fQXc6e20U&<ekAuHCgC^M49GO7[D
T7z+$oP[t6=i*fc?vw:WHu.^dqYouZ8]&PbHRutU?C~rtEDp#D$"Fo[7.l=oHav"Of?oP[;Tx7/;XcHa/./G9Eh:7/+p[tNHh>sC{!w:F2Xj?JK!blQn3jqj;Z,K{RJ47<sY86@B>/x6fPm=C;mPz@%ED;qp!rK>NfSP%p`M{IjJ.MGOvyLtE]&GA4UCNSLV|2*%-NID.E$(L-$hhOZj
No=N;X&GV&+}yNAO8]E.V
g2_{bCXYI,eL!Sc#beBJ&$30ESe{#S)uF4HcP2RWD4Umx/`i/Zn?i}Q])3r!O@:;EL1"$?E=Q@0i>bkmVLM6yVIny%wgo#8M1%C3kyRl7#6)0v,7Q!KIE%7*RWRZyiT@C)9/ogb3NwR:n:(wL9(,I2<jL!hWkcUolw,4x=F&?!6`p3HvjMq6Z(h_$uYsllGso@EfQoAg?_=F$B%bsfZN!HRHdL]OVj&v$`V;(m"m^
Ub]`DaQjjhWdTZKC6yxfcG4c&>e)JmX*N@4rV+@n@Dtax(n?dZG}5d9}(Qc
d
)b08q&)OJ6U{C*]1S32rkkk_umCU_+w"XCk|botx-@n=u)GKT#D5lwxy*.e:R4A4DOaEZ.mQ7/YeeoIf]Z?wIl+
Gg&;WPB8hs1LS//SF^P;EuCMI_JD@zi.1#i.JBd.GN^:^o[
*U4e4&pQ>w)r1!&(J.kVsVWBAv3,3?kYO4^
m;s@"7HY`eG;w-v26gE&B.@bO<c?d~"o=y?!lYj4,h7Mh|i#hH^}f5.2:$x-+,16nkx~Pvf;lWR(iW/Utag`,L&gXV7O`3m,>~,NK(K~4Eh[I6f9T[aFrxvU0K1e0[vzyviU%K');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Qj=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Qj=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Qj["bytes_processed"])?array($Qj["bytes_processed"],$Qj["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Zd);$_POST=remove_slashes($_POST,$Zd);$_COOKIE=remove_slashes($_COOKIE,$Zd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($u,$Xh=null){$Fa=func_get_args();$Fa[0]=Lang::$translations[$u]?:$u;return
call_user_func_array('Adminer\lang_format',$Fa);}function
lang_format($Nm,$Xh=null){if(is_array($Nm)){$E=($Xh==1?0:(LANG=='cs'||LANG=='sk'?($Xh&&$Xh<5?1:2):(LANG=='fr'?(!$Xh?0:1):(LANG=='pl'?($Xh%10>1&&$Xh%10<5&&$Xh/10%10!=1?1:2):(LANG=='sl'?($Xh%100==1?0:($Xh%100==2?1:($Xh%100==3||$Xh%100==4?2:3))):(LANG=='lt'?($Xh%10==1&&$Xh%100!=11?0:($Xh%10>1&&$Xh/10%10!=1?1:2)):(LANG=='lv'?($Xh%10==1&&$Xh%100!=11?0:($Xh?1:2)):(LANG=='ro'?(!$Xh||($Xh%100>0&&$Xh%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($Xh%10==1&&$Xh%100!=11?0:($Xh%10>1&&$Xh%10<5&&$Xh/10%10!=1?1:2)):1)))))))));$Nm=$Nm[$E];}$Nm=str_replace("'",'’',$Nm);$Fa=func_get_args();array_shift($Fa);$me=str_replace("%d","%s",$Nm);if($me!=$Nm)$Fa[0]=format_number($Xh);return
vsprintf($me,$Fa);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ja=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Mg,PREG_SET_ORDER);foreach($Mg
as$A)$ja[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ja);foreach($ja
as$x=>$Tj){if(idx(langs(),$x)){$ba=$x;break;}$x=preg_replace('~-.*~','',$x);if(!isset($ja[$x])&&idx(langs(),$x)){$ba=$x;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($ng){switch($ng){case"en":return'.X/+K5IAP*41^o=NV84<aq}X5Ji99)^MH(KW;azZ+/m8!qRJhJ&w,.aDesho}tI2qU`wgZ[M6-RP<N(l{DZ5O
;FhWhy?1[WBl-:!wiE`B(G%X*E}Tfe`?GsojWg;`/^7Zylzk46!+p!!QlLEDv@Xy:,a6Eljp{[OO35hFs^
B1]HOsTYLU`i$fj{<Fw~<NZB0+$@=.W&4ImLG9&U?@hGQysO`Jcg&-w1pvtWudiVylriu&qVO6hMKk,"l/GUtKX~+[1h[r`T^$w+G6JB6q0aTYB([!3JD!b8$;@Kg^iwZk"Ldi[Zs7jGyJvC.@j@%%m:7)6rFa@R5E)31;/J]78qhJI>x,DiI
EtUX4k={1xXn=}MCO2v,Ix/MMrcdyi+7tCY?_N`Wt|rDr{vPjvtw+Z_?H%
c6Jh(+f=H?Q/mS)u0f8m?E9r2P5]K@e%es%J7t*9{dW7c71rj2Glark(i8=@?O9?UwckxQJu&iu`VRq*uGT9;eCOQ5]5EK]tVa!gZm%
66^e,!!l8gtHLf]XdHt$/0@c:`f"@5UGQB:$$to(Q]BH7rr1T4aQvH<X)v6)<-"H|6Yu$]UIVKp<ikmkD"l.IuNm;A@RP.c9uVT&i+edE?7BdgEUC0D7euValE}Zy7[T[[/XxJwex94$T.x<o:8e_L;RrShHRJaOo/c_W-tg:_<;kJ>a;Rg4K
wdKAKd2QsRV`69mpTIpoG@Nvx]qVCkYY0r)UiS
W4[#y+WtS_0y%XV$u"pmSrh_DV>Si
x1L!_SR!S7"M@YcHCO%gJs#3aM!6t/1)b3#VD{
IA#GmojQS)jRkBIK2@V8)%~KB+`FiFi$?+weO2Haeo:V<^#;N<5T@FgFdC_TT_;k?^K`!=<IW[}yv=Xo`%5OV`p:9.pV2]jyGjt6xT.;xU"5|w5DNtALf(D]/Cf"Ds8l[xN404))}gPfWf%Ho${5$tJXXP/Mux/E17N7P:ihMJ9g1BwI
yxW+/japC#JCnCH#k0%=eMAJ
a
R9UDh%P)`=WW~"eyd-Dpj0W0T,$BAoR,"Ucp-DHkIFg/_)g&)0<:4UsAgp
R;qp(h9"JdMOL{($E<"*[u:kGzU8d[;j$@lA+)`1C{gY:oVu+_Ndad)qg[K43;K6SFG@@&@Dc~2S$b9{?w!gtm(8i,#xV=rPbP$FZ;H;<-0j(Os5kRB[CBU4?.*n[p4z4Eo<s]W=pA_Tu?Vt.s%L!{Tco;._p2amn+Z<*2T-lUe.R+xJj<3;D!9LVZFo5@]d59Ff6[qGl7&u;?.].*aY/|qt)feO@
3u/UQ5sN9RBl
p)1wk@c@3tEDk/t]Jq[xS`wFDT*s/n%F
&+EKrytSo<Mv=*3[Wy+%QVnYIXh]""8Te2-7A|BDLE6%$*_x<{5J@KB~;Y84682vhtdzSI.@>Qif[B
aO
n<7:j2W
;)gZHI0N!dNk@TN&,C"2EFI3Y[P~Fve+&|>Rs5iUsWm,*?U[@n`F:GtXVET>#&Gb4x%5[EfGk@x}aryRO}ut=}ZGsUtU!?[x8nX/Kq;!mx(MqYDq3l.|^4-fm=V;Zra$%P^?>4+4x]QrAL[6pcQ{%U6oARbCxRs?F8S
@*FI9Y%[?f;~QtGOGP>2q7bxqm8sQ}eCd#Hks1dV%@VAik%c5TLzQ%B%o^Q#%2>9*DFad*=,fcD8eX(mii2`NF:MldsG*zjH61ZvX;Q_wc[yr$No_/&7Z+HV#J(N+T6"sLadc4D(K*x{,ckUbmW`=*vWbR4WCvII.s+lgT@t5Er~D&owT(Q>KCQ|n
Y4y>4qpoWr^ui]phKiIL/?u4mIjL3>fZtsAl:q!MC:WuQ!.VMeZ88xh^aB=}J-g};M!es%dU;iq
kySSwa-7OIld%g];*[]!qe.m3_aog":q&rgl8%hM/I4C"y5}PqDkZVh_i8!XTq8QM3lbd~D5om[QZI[{
XN2Rw)B"V(v>*.EfU4^9r4+AN6PRS^w]w*ir_]Kk?/>
>I.cWmsk&#r,rFr4e;}U$*i:O=tX^SZKGJ+r0qLel<w18U6I"o?oP]H0]NVCD?Sl0ufgs)!Ry8kSI3H]]nw9sP&;ARpZLT0=l#~<(kx-e+f2;!?=9.NkzX*:#ZYyK0HQ.xes-64ylJL]p#$/yU4Y-"eF_]afNs>Y$/VQ&wys.K
y}E1GcJgy?P=wr*3(L-/W]0tJ"qJu5m|uvnm/GLcA=#I;mv*BJ[vmX;ZCv2{7><AT_^#Kh9.jL9JCp20C1Y`2hcYb),_Q%px1tZu_}KZ7]M)ihtVk&>nJJ
-m6u5b>W4nk9OHJRjDn){G;xA:`CPW?7fZ)3jgDh:I/"E",7wVtn#&otEoJk>Cy#U3kOHp0XZ^H3b
5?4/TRn0QTQ7zUyp63d/|Hean(8HQ7V-$"7]F6>l`R=Vo]M<9)G8"o"D:W>PTZW5))GF$$@iNQAC])vmi3fgsOZ"Syx.(i4+b!tMoWsinT`i"%t;J12acv4!7P|MH/P17N+g#!y`_oL
p.A
/[1q@X"#XXa.f<pgo[w]:rU*!V>do&EBJF#=u]p6m1P0<Xp&i(gLCG#@6)o*nc:3Q#DeFc|Z::iL-S9!}EO!I8bk&]SyV&ROH5BX>-l3^^=od8EJ8@K]|^TE/c)4h!>uI[BYnNr?!3b$Keo*gP]5Mye`Pk7NSrtxXnc8>p4@Zp3IF%)BGpk0}pW:6
4L!NFc)m1%{nciv(a(d%fndSw*7rBKyEJMqAEB|V17u+nNe+l:+yW7itm5gbLNX6,Dhpf,O*H%j:y*Vp=rJXkG:u7,6/avRcoa7(LZsT=L|d|ivv@CEs/s(GTLxKZ2t44:$Gc78%JQI,{eK2Yqv6J=Y^8L)1>!GtX+#,bv|xcR>g&aDMor;YC6Iov.*[`dtRu<B<GnU!gT8PGvmN,@R4^-AVP"#g/nzNmiw6EcfZ+uaR2i5eV[2SH<Kr3Z]gtP}ya"
OS^([}XmFv=&6u9n-?J.QGsMX_NN`%.tLE9RV}4KmMn#)4Ui"vdkpRE|-S5TCJlFF5tTc)/[M!/]/?-)F4V6heI+o)1f->V^C9LzS:AyyGS!2^6}uZW+]px71mpv%P3cIijjX?@db^?l^C3Vn3u!,8QqeA9E*vO36u%>tkcH.@a03,f=wDN&';case"id":return'.Zu;BcvpM+XqDeQ:d(AN6T[]{/Sh`I(eiI1f=C^UY9XmLV`(Xe9!Jw076?3YoPZD*!=I>K3JH
m?.l?X57+>YN[<(x&vPTffEB9I+oR,g:ZIP@|SGuC0#w
Wa]{q+HPO78A=9,6?=M[.Jl77nNkvb.Jkl=U>Du>7m"/ANm-"Ms)mDmjUR1#=(Y(+RH+d]^Fdm&PIf
7hrb^Nq,l+@(
#t*+Xu"!6,BVq%f^&mDj355]S?2hNkEMp98e4hKnW2rqP3-angb]CvE=5-+.mFsO#>Pw
H<L:s3JqZl*!^%v&|.%@nJ%8g<TB%!H11B,,{b.8Pw`(ekIa<7>[f5>DXyz,Iog#wi6LZ8|koB?(9"A!gTn]`h{:z
[pM:#7ix[n#,G,6Q00PK$`(+P+G
uOrsu/K8>^~<dE&mwkO`m#@XsjJ$od[B5*,4LP1tGoFYDDFP3d.2)X@8tq[QXp,F
JhVSVRR1:UWnxxU3=")C(5Q4"3s9Ct1K2V8`%v3o>y-1a:O[4jsJBGLQNDcj
.XHou"8e@f0d{;ccSJhy01u[AL%MKv8X*K)39-6q5+.xD*S#?*l3j_3*7dp^WfRMA2bjP?7yno:d1VY%"DJ;N.z":<BZUo+;lMkd$OE:vZPh]%fm;ChC
8,U:](QzIj&>fk&#pma!/Y8LQ`e1BKYLPLJ=Wi-AC";]ulMOd>F6Q"N+bFG1e=ba,KsVCFx4VfTr&Y$%*F5HOdowYuFTxkNurRrM,{%#vLx3KiPv%w9,NivN.aty5TV?CuR-"!2i?+;I>wDRKfoSvvXR.&Y&yyc:h^*y$N/+IoV/K17EH,&eWCv-*Lz#qji&"
,h^TX/1%/>-N&1J=?CMlo*y*C!ldm$eZ>kEgumV46E)*@KAJ%tG%W.g@#{H[=%G~*j6-$tLdan54Ol%9a|8+bCg5.$o.gOfc
t@&pK,+IzYr(vHO
NJ][_u
NrBsX@4.IW*mD.O/HNchsXpOboDXD`-+s=8HA"mQ*vb1QWx4."K8`5R-;[+u"/HmgdI=^YJ9#H]<=N^VI59RIqh,9J>b[6c_&($SSnPR_twxXyi?1Jc|k`=Zxr!zOZrAqR5dCdp#6ls#b<MEqRv8%dSMW1bbu(SkiyLv.08h-ayHod]#XPEU/%A~+[yNd4:^c=hwME,M(KX)N%M`w0<UJPyd[v<2A^"]Ceuw[CPH9O5c`T"DA/MZ"rg=
_k)DHOW#-LQe7^3t6lS!Sh0+9L2H$YTmo#3:#0Gk_JE(W8/]QN2G96Zs3<
pRthw);&rJ6Dv8Ik!|foxxB2Ky
(N|
&D-3i0XJ@,F#:kyUxn?Gh1#sXrJZfZmlD:ZYy:,SXwPs!3,Opn"PW"clraNL"72%m@6;<l`*}@HIx:SN>a6R>66?>K-M#V9+*`
B1ShOA?vlLAR";6Bff3a6gw`ZFO[L6p=5bCgq4[vl_Dy,LY+tPNcq:ooT"GK5>N_T
>+qU,ZoSRMx~_Y_BlQD(8b(;s6?Yt](3$kZ,/mb$<.e3YqX=k=])%/7`Q8L2!)L?%X^`FfP,lHwu=LJ16hpF!ooL1_x1T5@0_W6-4rifo{BS=Z
hw<iges2x-1BrZW-GVe<8
#hAw=.[V)rYTzC_?Q+4S2u*t!mv6Q>lH*%CHV*iQ}#PTXmtgS+=_CIXN{Gmp5oBbg9/N5B3&X(r-6MB09:<^R=|/(/2?CrWT6rI2ja5Pzv+$b`0(-QgRj>;M+wR
K&OQa]49$%C4;t@3&`>HDM3Fw?/I|Y/6VxQKv7~BE2R*|vv*=aQqK=C!]!Ude2`m(StRn$1wThi)v1COpM;1vhu1M:Mql-J:wjVOKNGkz5CopnP1?Bhe]Ih[A%NjF%gx@
(CaTj(!4bXud:Ml(k97ind0G?2:MIMIRkwu)4TnO9;^P|5@:B]WUId,kx;klt1Q@q>tG[B@&q6$M4592}%I@40RVA<jY5f5t@iIh|q:^TID(fq>EY;N9xCd4kJCKm
)E#DCp#cJ9VD@/`&g)}F/U8_s1+P=+Lhr[elc<Oy3">238b5F"-I)B%qYEF%I@f?z4@f~AZQ]"OQAbr]|^
-ui!Km#]rA94jaA;x4"-F8,QEZ(UAC2=n1jRIc%_%fTnp@64jj6YP1x-R/pU.t!XPq!#K|,|p&XtXJ3=^/(4
g8"95CtaxDCh;rujRcit!Gar/W=dITB#LNh!14wjsV$1{2f*5u[iztAO1t56l<9v]qP=b*MS9ip;_5w;Zl.C^_N!Hl=%34*.;H&W_?Z+jeR3%_<cL%}FOPS4*q|Vuuj9Do|>%EOhACLAzO$`xa"5q`hjgBcmPyr=WJ>!)9m+&rODK84sh"arDxP=KqH:p-%Egj.wx]^&`mo6LCRf:iN.SW^Q6U]dZT_i?[V1BdNL.?gQ}6K[E`cd=#6_BIm`$xQb0qtPHI=/at&%X/:a7uaX29m,.i2MvafqYl#f[vE`TVQKbMJ276-A^>n0;k]0/vSb:<z^L@UmCbM/hQv]zAV<sce&,;(QD:343KbJ(^1y3RMfAHm(zgsJm&I2Z!&$q0Ed/CKF"-
Sy^K(J@iu>%J_LUMY":5W((1Fxhl$5,7TQEDLMoo8+qjU`(Bt8dyAnwD[~kNa^[s0fAZB*!V^dv{K?r"ko6qsWa8anP#KBQDl1XA&Cr;;xXJ`Zef9th5`VbaPJFi@Lb%//&#8ib:y:A;c(fcyZTJuJAav&c<*(@55{`-5(Q2Or+{[_Kx;S?1)"qc!t!2e{&N!4o0-"p?C}-.(J_EP"Sb1Cu{VsH6({,C6P[J/(wM*Z)nM,NBYNxeN&';case"ms":return'-Zu;C7nWB&)krx*PH(4M9f1Q|W-j&Z"G=6&a|jLG48oBJ>Hf&c|nE3{(HZwZS!?.w;:/KI^WT51clr
D8J=*tb>-bl4Lh5D$OpBHqwie7-Xstb#&?
gR:Mesvs&8&pB03BG6C9K@(I{LlXDp(!<Wn!MOa?{uK*(kM[*o(9I
_`>R3e()<
sNeAON?y_x]=jgMWqn01xd_3?+BK4wK!Dvdjj>U.US?mS=j+Ntue}6u1YN)d&kX.9DA!1GlF|S@8UTjNtA.A3?Ep{i;>rQs-^sT,ULMdGaP!dFk&Mn84/E77Hsq&i_@q&uD%.KjtwI6tZX7vXe-"=T!C=-tsB+TU?y!sbwN>{5w1V)edc1&eNgpNtn;PHWv!?cC5<[ZpQ3Jgc
FYnGy.hIN9U[u5S0a:A@}-V0ORG!nOGC3">5Gf&vDhm8XdYMFP($YatKmE*aBlE@LIk4N#FvAQTo<:{N{QGgHBrK@&oq%2":3:>W/i,tSOucJp9(<$6.*jf/?uG+;+Ee2&N*_aXMCKalD/`PFJhdj1ernBj+I^f,cv"6E!My(;IHpNjEQ%ziDneAb._i2#u7^Sh/f^CCXF"Dt$[AwZ]Ejm,>dG<tw7!PY:lED;-oTqW2Oe"x$i]z$CvJ~B},8oi#E-fiW1]6f2NY-vPS`$IwX]^+Cxp-fdLONeM^F+ro}43MCnPHyQmq/"IcnkK6l-ddl2G=+rV"/nDihH8@wbLywYb-ThP%M2bvl;$"v-Dp.Hn^u:Y$gPpwWs@C>!>#Nj7uZK+(4hq[9@8!|Yn>|JsuawsUkDd)RP-[as&jy1RSH1=S0C~8|L`!0T7O{k`n|D1Wj!m^Rr6H|Z0(
BrQP:oCuc9rGeRS
bRrhf08b[JQzVxh8x4Z>Yj]ucbY!Qi@oL#`_ek_f:l>UWOK;=AaqFw<4E;YPqLkUhyG)Ms[^!HXzV6](f>bTM}f#T|ig)Kch%t.j:Fji!Gu{0H&COLH>R{VEj*Ff!lc(omawY*2G;i;^i>Dk.!Tt"lt_:?]4Q[lS2_tbD,__)?7pYjx|Z
TAyf$t^}PzheV5v
r[v[lWi!%K.5^
gvLsn~$=3>T0`D>jdc/JS(:hC&2V6NB{.VU`u]r$M<C1yETIaok7>Adh1jH]jm6QW"d#4^n~6vVi4di=j`Q9qtLk<3PC2T&M^w3WRdK>lXA7l:l6"5p<2Eja<!WQpVNSlVbR+n=5&MEB"Tv*rK4#yc>PGO4Q=3vJ/FtN7=CkhXVZ0*oNV$&6$)$_CC
CeBT<C5JZ1[W#e4iJfNPj)3<Lp`KE(u8N.lKq:mP2.cB3Q9`pPmI@Q6sxWV-a#ej0HZs~$c6YA"jK#IH|A,BH@olm022^Ke53B{Zow*"X/,nn*#c?SkABO+vpY;V&1GlPRz$Afw^l*krWi
$&ko`/p]K`f+#Yd^9n_nIiietQC6t$+OwUn:Be$tr1s`NXP{y5);.Wslk)`t=$WJ>n#oSJmQ>}x8Gn%pK~-GE^4x,#B#Xy-?GubBm
q3[p(62]8V08HVbsQ9.!4];41:Nve+Zl"ehSpDN"CnOK89.ue3sjdy#H<FGG]p6v[GkfOS?wjXT33D,!#?LvS]xVV7(p%|:`0z))9zjPIS]>4qOS$Ogx164Tlg:{[{dyoeCOV%)dG?jcSGEW$dpcQH8
o{/"rOW6LQ]&_j$la.+n
thondu|Lq#l&g#:)tYiw;Z9&1d&*
^oB5EaAh"4[bc*l]IX*
/i@9G@!|wj-3&~(Fc4xc/8M{%SIghXZ"g7N`wI[~"!oJC~TS0"hh8L3I7n.FhN%IV4U!J*N;h
&enQ)$>Vm@!oeC[~fwEJ)GL}vl]C;4AU"]h_m3;}-EK2gI[@kY%!HXQ7uQ4B+V2L22ey<<wj$xL}#rv6O6F18KrW")Xl/j]>4q%>3*w(*L8IURuI4@FrNkM}B>(Jp:<*5
,MnWC{d&d7P:yd2wbv!|NdP5MgX;K%snA)eL;uk^2vXyc:dkfPDJnT$=o><gTd(#ebAJP!A@hX0ZjlXV=$;?HF<SJ7Ks/pMa1LeQ.Lp5bhQk-ldqnJs%H6Z$R<k$16YzTP`S[@e
RQ@r9MI~aqI.;M-nB{(J<1,ID&o=3rNOWD1IEnU0j!Xx$4$i7tEixI,*)`y:[sm%<"L]!Bw7sMXt#&Q30WE+vhs*#:TL_GB=Fe=>xQ9V-pf9
?j`
~PtP1&H0DyQ;GOZ<<YCU0)75gmr$E"k?dCIJ>
Tqa#O!#&>!dI!q`8q6{]J>/d>@K<^T`GZN/yEp:E;nGLhsra[[Ac^C{U~o&EDocu!]/.{[Pal6cmaXQf"&6MoLvYs:BZ*]=&7:}j4AFG0oV&aV#qcGP6I(iyRkk!GH$kWDlp`CUUqjKPfN+.[>+KY_4b#&y?E
^6U?k0:r7X{9(Fp13U`EHFtU}>_>Sy:9^DrZ!t4<EK4JfU"4/
Z?.MUBoO`RwuLUdV9ZEH5<W_!`nuz9ni-wZ=FDoZa2tj=Eo*{F/mq2`cAOcd;jWIJ5U:].uU0Dm#F:>/t>~lAq::hSY
3Y]`)V#-[LTJ%/}_>4cNxu<>1*$#rpr^+Wk2*v_;%f)kNuBw9bQ#j!M*j@qKBxFJETY&48UldH9wYK&[2Huvsu;_(,/Q--i;/-8,&bSD1LS/>F2PC(MU2dSscs=Uy0kXyhK^nm2UH;lJ
>).5H%pes$2Anj^Sb|8=EJF>rSwmn/xr6{Y+3by%-Kl->,$yD&k](hE{/DNZ"Fu7UxnG>+O/vUW>0$#=qoN!t:s7;jowarJOiMO8<UR8M_[Fg(d(';case"bs":return'!ZuKrbP+^.BS(fy"Id-j;ZzLSPz%|bqD!Jf9U5yh5`v>9Q<
8:~8_u|+{>Vnr,tRNsSufcZSyA4[3@]`3I}r79i
}?GcZ<}MC-7m[x#4Zll*U]=8u!a(pLX/57%mqvktAL+LZ65))a-^m4V[*M.odoZ6LA~B.yeT!vZCD=9<is7
iyY3Is1e5E)a0`An.LPqW[z6pEE(sPKb29}m~v!Q
]<tmd6*nROoBv4)]tI/dcK<PVk<)FkL|Sr*F/M9H&>Mp1
(WGu6{(;O7yCK
rmnK*#Z/r8FSIn=VJ;H@+_XBe%nn1q2pQ0wy#Rs}1"^D!)&*1YnPt[
I
^Xc]X+I2+Al6G2)SNi
v;D!;ND@p2_d(?9xKZKrX]#aw0f5I{Zi0WN<<1x&@hq8tUrMtwFvXgelHKi:sheZy5&dO~!7lH52$w72X=y]=4vS.$-&i/gFu_kTJ0("-(
jf}NfA2;NtYU&1Yb?&|3p-=l2LA*$3A93AZA5hyX8-2;luA<!p1Cf@Dm6bh6-N^9=ZSAAR|Xo)PJ;?u$H6XMjhE+}+/D-H[j/U
*?7G^rSe-U5j8<@xa<G3oOj/A?sBq=9]5}9$Wj1`6nN}>6d#]z8!Dn5AK~]q#}c++F-GLz:7rCxG.p#}`iKTDS<9kiLt>zl%c5Yhkv+KkdE)M>;jp:fomZLWrTPGmJ6ONW_20;N@1D7ogRs"o=-|j[+sGc.mj:LHh:5SdLF93{d>P8.ZnnNPnwe|wFteUfZXaqN
.X7+;;-F.$(QAw@L$
,[<F6_EZC}wgcE1ZQyiwW8E+s%fxG-E=a>!LO4%V3Gxp<0,#k]P#%U8OS-c2,:`XZ2c-[J(k<K:X<l0hf=aSOlY#.rn6**G.B6?gv7p!WoIcMmCHP"l"O]l%Y77h1Y[slhuY?p#%EnypWhV@p1)2#k+H^{j/;KA!pF*=KbS!2Iux!L30KQQI7O9|2Ut=>Sq%$`BLqLWTd8a]mk7]#DtM`PLsFceJ(UakGH#Y@gt.Qw)Fs5Y^#171Paa:d<g_Nw]b<Qf-az"~LFoEOREFrDmQe.r+]TgO3"pkYX]EN[9u#1U{xDXg_2vcQf%
"Ni3m/.p
/Haa*.V]7xem`!b2-Z6Q`rLsC+9*LL<
17X<*@H?#</Nam;CC$+sB0&HWe]J|V-OXw$@=X%So"2WTRvrm*Ye(_vyku:AKK@VIvSNj,?y=tLw=*X>~^Uv%)a9bWBOUZtOd?o7:J!m]sION&?XN-KnJ6wvd2A`5RT^s;>>wRB(4!Q.G%3_9H/ld<_
`[)tpx]4_Nrr!R^#ne;)XoBpRh3qb,{pVB2+1r_e$sd8,4$8eSi$e$Xu>&q,2aUQj,#dZQ.>(v=yFc!s%w+s(U^HU]WX88^:q<gB3Y/^wOuZwVFUVp?ulET-yBYMm7]Vv6BJ

YSGC8Ml;EB%vwT)[$V.e=EQvHZw$
N@ql!bd7cB_+Y!yIDiEp.cR6DTc&$LdSBjg.YHk<)Cbt%a.De=Z=/XUE0<.Otv[u"D%ESuXPC4jYt}iY%(Bk3_dtpQ3@^rhmC5^2ly#F(Qjam&`=h#&>fK-Ju+b0lL<BL=1$sGj/6"h8k:VAO/EV:i&Fc`ebK&&6(3X!P$8eebk
W^NN!qTi1Hw$2J8gig8,2v#%FFHd"Bp/kq[UEKOR+,a$Q)0_Cn.ld95?!._Yt=j[HJCpM
R4^<tJ4cQsdiMJX@$[:I4=bIVl_xbyXwMK9Yp5[vr8d,p-63I;vtB68X,p2M(~lAwe(yj_F!uTx&xS#O/6<YE"2U3d5j
<_C_yi`fYsB:0TK0Z4?!(YD_jf4ugQT$mdj^{XYS94W:jE*Gx>E5t!
!s+]I,T5%=uE+$xA=;U?l}&D@^+X,}kM+r`@IxV95D"rt&Bor~u?59C3Q{^Ao%o;Eq]zT/`>U%G7^p/T_q2.kW4.xNu"-IE4j;k/1>GqKB$@>g@g=Sk6
e:}J_Ep_)LhR&^oiv,!CalZ1@Ncs,
SnFZ&jCU"veBi`jDm%8a,PL0pE?dr5VQavwZ3IKZ{;D)Rd~.gBVo8hl."BX?`%IxcXB@93<[NH~]|!-)8"Wx+@iZTE5>1$k8ltV%O<j%d`"20[gb2)0#x?aEm7kOZ)J(?7&S{I?r:ZcC1qP5%44/F6z4)MuO/?
.YWa6nkiYZP6Kpy]1$JCV;31(C>"B6D#Kea*O+pB+0JdwQjo02m]j{AutbeVo:(vq-B8JYp-4dqDLz[:2XfPqs!|gwkGifPtdHGf,}V,uR]%BJPRfN37`8n~L&N"%^i86odo?krZw(M9GA6Z^7>F8MJA*|78s/U1=52b?:C(]0=O%W_bj=S#cE4]c{rL
Y[+p;]xeK:jUDCZU-DQ-;C]Ob#s+!PB_Jke5
Zl2:de*n!X,m_=741i<srA*MK-1^$(gP+]rW<u%;`r$Z:_FPi/WcPJVCJ
TxU>o7R=(#Y
:cc~_Z9s0n/{iG)S^~"R/7lC2a3:VDONC5#{#}M2H}8&ZLZ68_`0aBx[(!a{4H:d8a.bXMe(heG/d|G#v{u&+DJngGccTAywde22m|*M8bC=baHob?n|K!jue7/?&LX`Wr=qr*RBp7[U2YlqD{aG!`ZAS2>?U+6C=I!tt#3W(*@hfvgb]MQlZ
fw"78{)[cIre]mG78.l9]a>7y{`[JLaU8Y.]x;mWX
@ZQ^Km
;Oj_<eGraPAKfRg
ne.+}mAD8w:vU,)cD.)s&n,_cR7ssDK`}jJ3X&UePHqRVwVUi3KLpecOJ;85;V@OIx?r)f^G*hXdT50D6(w10FrMFwT=$ZvJPQZg4=)7wfF:mtVV"jBHHqcrmk9h8nw.C,Es/,q&D8^IS0l[Wct]PGS
?2TpZtDEYIe^9sW4c@"?]Qma^>)UX`hCL:rl~-Qmt+xiid_+&p}c`s
JtSITNjgM2aixlBR8o#H-RilO&>}-}LKNp7.gtLERWu2QeQrnme(&=$On!jpenHS^W9FBD
lnaDf(e8dDFrBZW%T(-n@1q;6o1J>-k<8C>X:KsqqtsE?!SGkw:UujQ9*?))2
|QgM3tit*4zrYT?-uOQDnpM*NJ#$aw(Fh8o-DIarrrF4*z)(uctr6*bn9HreQTH?dL]@z;J5Bnj6<ND^uKm86$LrrGzuyZ/c.KW:
bP<Al@xI314*-[+6W+Q)&3[5+B*{r5WB^kCGR2?Y7aB+KDCAeO?J
1O1*k@Gy
ajFmej4t={n+G#.x)R<Nx(,vL)%T]{Fn<v!W^ec_A~p
8nU!,CA]VwAxDI/2W#W"h,P?=vc0LLGPkz?-Gf//^S$(';case"ca":return',]^Kk6LD)?T)ni`"XL$E?8;ltO)n)rv1;*X${-~91:6U#/EF05n6nM|$c5RDL"4eV"20kP3uTfel6A@1ep5`NZ_F|3rXsFIWD=LC5T"x_`f`0.@[Lsrd_0Q46gTQ:Nc=pH!3(A2Z=`|gio_X8=N
&=,Nlh58{
HsNvy^_EN[Jsju_<^mNZNbNFmhm(iMie0&JBlJWyRy7PNo0>2M&[C@tx[?NybXn8BH`$(,Mr_G+Hf&Q2iWXkW]~+jbJ`T.!qk_p[I]xwp^[a<"BCnG5$QTQ3VBZ>1dZ;H9=rC
X0/+mB;;d5Pj5AwS)%eSkWXQFS>rX66yU%h4Go@Q!
08[CaJWd*x}y<UXG>lxTJ7{`"1fuO;oQ>@KTVt3ZNpETus,C7VEnck^x#lkPFl1tbZIVSZ=`1QBEU-zLP_b_P"AGx("KLr|Gkj(J,G[-S-TN9fET^UO(7eSxKE8.79/;RvbE(r/WaG^
-+3aH#1vFxpKc^X$XCrSEN:@Pv&=tUq
=iTKTL92:rW@+[DWWQu]1Q?nY5ke*9)S.vB:e<5nL?Sjx!8AbD)U!7p=]CChxl=vB/ME9$KrXk}-sL>grz&It55>E"/xj!78[U|N]Y*5&$e]0OnKQ6II9hDMMAe;4_{jRO`Xhnug&d_wwF6U`SN:AD,A4pby]0>)1O4jLg)U)T8mL05v}uj&e_jy?!;c|#NW"@AnJ*JDdLSK%u
h#/lSe*o*Yxebr*RHc[/.t>&+2]gRc6f$yU_<;cMIg-w]PaW?$f7oqFa;>R:<qo9Gv,dFG`g6b>I3P2p@DiK_7jHN5^/BRX!hZO1
-%m!$]`3?[!=2S>cZs8e~@=pD7+y5,i!3v-Q,7P;2?b]e9uW4.iG+9>#/N<r!TYX?(//^`Q
R5r&dZPYP)J*s"qG)>6OcHS,mHSvNuMMBRj2l)RI;Ou7vXCfl5t17t}Q/ti^5WvBrfpY/FH>
t#YW.zDVf2<,3x,chme^#w#^E|:Ldb7-%oJ92M!{.|#`J+/D9{p03[kZLJ<rn;,9-sLaTUY.s%l>lpT1r|6*"`aPf#.eV
GImRhiQ_0u#PpULI-5u:+rYkwsyXL~(hDLs&:6igeO%k+b[,yS<!z)9}
7,93L#52)1ty@,#[P#5Qnb*8R$U<LQJmJcs!lTi@}[Zq|uNlKiKmb+/Tx2Ox*KMDUbz`o<s:ikUIlP|:ffR]LdBbDNh)pW0S9nC?O3!OZW{gZJjaL;4rxPL68A$o|1h_Dx5x?unIus8DI:<Q4&!KALPW5e
n;6jRmHj(sv)^T.LNba$Z+y=OT,U+.VxJrq6uU]lyX@(X.9K1|L)tE#BMz(e3sm1n8[{3"vDA=fW[~wK_%GIk4#jx|,r8*0;cWbypYe?rWMs/d^W5Za;Y7SGv_<`yZtv#eQW66J9LAXru*4Tio7SFf:y16
NXtSu+@YB;n>MJ&9TKEDRH`#YoFVpu>YY8s$DwssOZg`@?@W=%gQSRR7~8W>k)dht7(6=OzQQ#&)o$*Jykn/_3DCRw+G_T`7,J<k$`"LU5+&77_@2?4SV%K?p4?h?#Y&=S2`
@lN,g})X5cthvP8-Z`,vxCKN^5+`vQ,vixa_,gK%W~"#LPgT5B
,bdif^DMaj<Hgp>SE&I3=p}0[7VYe!{fzM1(hu)sO?jy)A1J{nIlgU?pn>lXKO}Jc1
5)"m"uD_NBQ3gUg2yBIX6oHYw%)8yl-;KM=5A#T^^mcmPZRaBvap(i<h-I@l)=>jt7"-/&<.Q["d:W?^YHI2jN;{,l$w:XScu9r3Jn6K22K^n.9Ud0B1Mi9-Cl)`@uXPTb;^/8S70a9"d2_CWc(Aw]U[U=;!UwT[)16/-!_fD-Z3%oC:KU[(F-,rBmyVNNy7V^;WPG<D^`
-FP.?#0$od)oR&kb&-D/$O?&@)=ZOdN:6Mj9L%Q45Q6ArRF8-(6V{((u-#iCN$Bx,/P5e@-#Lg}JIjQ?ue2pAH[LpLf0nv,
aS<v&Ip7q4N-_k$7R0qC_h]G9dw7#>A<297pO3@J:-MC`i}1MIAZv];>,)dl2r1LS;*@fwHIk0$5`h
j-Gw6;uiB}d;qg:~6_W]y0gLZ2H>J3V716[/=QGW)@XIGmexz"3j>A9Io^4F4k/5V.uJ7Z7uy1V9-bm(0UVfO$9jgM,Er&XN=OGBB)ZlW)VZ?po$M`yYA]lyylbG!HZ{,1q@,GW`icX:WGG0npe4ac`b*]SaUz0U4TG]4E5d1N1,U%SuOGn2^}0^ufK
@04Gx-xYa++X?Z?#mk^?X$7:0H0LsQ^2T0Y{:{,R$.9|b`iEEboyb&*LLYWk`/;_1O@BpwF4f`lYkCR=[h$
7
E(ldEOiElPUpS.C&@UFy$*6c&SYqW2/`e2sl#cIieEm!S|3CLk3{E$+3[ljno$Dv&-Jw,/L$/|M7^E`y^Gj"NLt}DkcM)at!_P.Og]03LAGz*H)|kY5.QT-x#c9K?jLu^Lt}Z(6QqzT`_+c{OJBa;uskBulwpMFLWaD!MDiS>Wi#`MqAFIv}],L=Ka?a`]2I^*sCuP9_jV)7
b;Fm]fGV$-gM8=ucX2RT
s-
X@are0!eiYWn;Xp;>&Cyw-5&tEG#e8c]"r6PI`,X7G|p^F,2FT$KKdu$vq18*/
**>Za5>97hNo,Oe_)ju,[U]g0B`zI8%,^iX6u($@t#6Bj><z=]EjD"=se@kP%xZmR^T9lNTLw37$Llb@/unYOMp(=Kf4(_OM/hhf95vg`{q`E<&*Mo]+L>Y~sQhs6L[^+9@$mhJd/#D/i9lKsjx]h"@BJ`vIhXd3q)dbL_n}r}s(D-jWJ#hkut/W0hoIEONCW]$yKHP3!JAe*K^t*vC>PIHr<gnLn"Z]B9#uCi>f>yNSCvsJk1GLHg6f)vEv5^V>s^=y$$.q!eRLd>04SY1[e~wSLs]K)e5KgG_;G*s>a[LM-[,3
]!zZ9_RT0ilC/9QE5Af0apf(54j2[2d#55?!CGvpriy;Hrs0U,LM?QTvL=XAuNerZ/{wes`7yk6j]/`T{PLX85,*;)^Og!a?v)cOF,Yy^P2BA:|9c
&/:J$b006J?C:hs:U%0Gw@>SlcD(:>@AOKQ?/a*="40HZR@D|v"BmM|rrnQ:sz#uHb0w_g]uD]pu

qOc3=F8;(uq;4ktIk#]XO)k`C([M%8w;G4T9*Ewq(Ce#+!^:vsbKu<vbqyg-#';case"cs":return'.]^:_cvs6A:!@o;#;6&K?Vd%IhL0.fY.2]9DsI_Su4;;J"DQs8nnj#byZZq>H/w(d_4Y0lawYyP2M&}b_TJhPAC0vGm<!u6tOpGSDu3bnY]VTUM1.ZOJy:7mKh#PC?PE?r4C|/h7huKHSBoo~&chK<OtxT|0F
gEZEi0C<7#6nW?XXttJ$+vh`A;K(PB
?U"[g|^R"J0{>Pa6o"lsxD0a8pR,
uBwLi?l?1?pt{9YE~A6^bl>v5tTyU=d2F:5/jwCJ%hmn*gNEj+X&#ho!G>4V]aE+XUW-&h7M8%qieS6
:eV$8w<#(j4ukn(f#HVrN2!i@C9V5MTbh6%cK3|&2xRhzp&>c=FqTqUEL:T7|`i2!jTT[7!3$xHDqCwp>xHX@n>yyZIm&o|/Z;Y>q;_&ny:gB
&/C>"3L9$l=J+!9at6.mBtW*~ixr9AjF)h.%T2r30EnId(V4nu"n$"~MiyB9rs:+XJD?r<R
H4X?hPJm8"GP
y[iA<wy^n0U2kV.Vxs;`>>aN7myv<"P8XXWDpo[BtFtHz)F{!sJ)r!<@/3?_
Hm(?_
:;st/IYh:0n:v86H3HB`c^|CVboFx5*[lxLCASf0F#EdyAIE6+6F=j
(_`G7)16HfM6rhV!EtJA)~Z|OGUQL,%>Z$ftA:3dO9Y698M[il4XyB)3wCAZfxq*?LSYXIh1QU3SI%2q>+&IViVjr!J*UHHfaM>{0jF,wBWjT#O]ofVl?4`oFw1=3AbzI//f"IiZSB`e8Tsmj0"b6Qo9e&"8L2opa!iv_`?3AC[Y*:C=U?gA(Ld=Dz;(+Ydc(.eVD~^OTwA^E+tzG+A4HJC9"EL5xEXDn4r^0=^9x@19^s`3tq#V%d^3-T%f+*n!2p$+b~WyN:F2g{"g92aHu=<x_cfK-Y2Jl#wm%W,?_(t4EX5tdzQod4mwRGna2i3=73
4AscbELubkG5"c)mS)Z3*?^(RlT52Rj@3pTv]kLa)*R[xUB;s!}182)
at>J$fF%~,|6nUAW*qO7`eVj"W@38k*?l1Gx4.lW}H"rTF0K_m#KLg=2;n`]x$M7DBHjupk%3DIh{U^i%bNy~9&dx*x<;Lg[QS,lM?WH<ui.de<`1a
^dt*8/hk/B_3+fmk-bK2<?NGX&.Q`F<unh@"9abfm,V).bj:*6itZh>Z3SZB/@`nx{x"!rBOm9;rQf:uI"QCL[!_do`.EBI,[u6/1=o^1IbafuX8Lz$`..Lrf]LP%m[j#ri))AIyI
-x2(!Q4mAiuyySr%3SN%I-e(;1[n7b
LF&*BF|G)Rl"0lP6pmiC9]$rQ2!ml(=sMO.KZa"VGB6R}E$MzyyMlk74
"j]{NA<6#>N7Az`I8#ci&Md$6
_0.=IdlJ`6U2e>[xX)yLfoO!cn,c7qJ{Z&["Yyw@+)d"rp[6Yh<TG~-^>pxQ^Kdit5pHH.wa875YoaGFgA[1L9+D,K[Vf.J&Ol3pC*5.<`M6(dU(Xp,ZspLP0of^NTUT;l]g;9#EDPtqj%Q4VcVB*O[G7QC-OW-$d[/|V:ACt"2UI?[W(a!tEw:oa
l"G0P45Z2}Y9AYYW,.2:1cSqL,j&uN5Z^!R>5,#SJ+05#",9/GFzATuUa@XTst1vti%A9J
,H%fLW-tu$2H#Xytp*m2s^R9;SEo]E0^2],9;!l^0Rj(WdGYW&Ie}hTieGAYVn/
@f[npQ0JG1+hfdO&12g4(]30zc>Rvd_;u"84=g0K`I9dXi{bSSJP{k]&L)`,R5IYG*=?:y_H)Y;H%WQACy7rm#U@l]H/PLp/G?3?Q,nKf,PN=;EC3Ujmuo^Fh0y;Sp

;Jo72)s^me@+XXF#I+bt"(},9_*Shs>7w1@WA"1U="k+;fu[I#_aC.O$-WTr{4$N6$9L$Ia[5/No2E
iZK::@5!Qzf@NHv]OH=gn335*KJuIP_O0.The2mV5BR2xK#XaBx3qV5{^V^.!jo.sEUQcH"wmr=
_YS@a<>($9&YY<5
?[PuNTS91tP~9.["a7HRVqH<3A&U>58!jmbky=$k!T*)?lo8IP?"`c?Wi_aA9eb#00Tn-4pWUb^6WaE:BlH]MdhzX9u^EkZx:_di]jj%LA&X3+@~?>^^]vtYgjtRWE+V-KvHGfv@kV`$dRkTs?G9n)Fh<7$s[G6aJ=yKU=97%^Ve-4cF0*]7j]Vf61?oj%-uG>F(Y1T`%$X(g4t!,_
VhLT#F.FvZjuc8x/,#!8cN^s+
yF.Rn10s.+UGf,X;@07vJv63J7c?WU`^T!qehJDN)]4/aZoo"7POr8U%VP!>O]5t)DA^(E3N1<"/o*x8.bRMZ*2tcz(aySIpuql>r^QJ/:LAh)dEIZ.h]F:f@Q0_dc[V;bV+uyb@79&L`ry^XL71:_3e![@:]?}(bA~4U)%G|X6hNVDux(sor$D2c[PZU&10zW&C,Iai8iqSiv."b;;^fM#[&GCiqb!eg4gXr>/Pi^Q!W[Lbv
/4;IY#2:4Gw(nNmG0pI2*]l)-E3
&/n"ic:%}0b6:o7a:;
TM%?9"d%^T?b$?Sgi>JNZw!=PwG/j}^FtbIG"tOOQ&-Wvwe_y<`+ckD+`PWD)/l(6Km9BZ"_7#[xbt,*IH>p;]`$k@$H=3F3f+4tE4SP`AeR<BP89_d:dIw,&#7c=kCr9W0Y_5)JyM^*eg:][F4yEmdQY0(7j@)-`:oAqU!(oy21L,WweC(NyD+]/t^f1n1=LhsZW}(+5J7;AH2+.72SM(6a>7Oq*,qinV;hV|BQ1?o$"IrK`?dk7vEZ-EAHv_xWLyioVd5}
6oHWcoJBTm?DPFJg
KQ1Js`kd.+ac2)0v_"hIF)X:9_[qO=6(%{Y)Is3;P/$c*frCsK#sfT@>+KizPG@,=&2Ea!$g2b3b8W!RR~nrE0(opTr#v^b`dq./f?X*1-yR9M:U"D%qs^c%lh:UC8x#Nj7qXG9}BKWRGe/71vZ(,4f@BB`{b<?W(AaC5Skue(NIogbPf^K%6Z94Pa"OF."JoFjvAA>,6Pe;=*F].AcNLbtt/
"Vv*w.svPPuy;rGK21-rkx-OH}I*,ae$0WB)CnV[(,2N-Umwj)QU,[mgS{`cAK@TY4O_S`@aUc%s:#>"DItAJn9|cwkxY/Uu5Zpu`MZJ.!$e4F5~x-1VpM3g/0E3)d"W3
d.djK~mK**4@;Hp^G~2:FP[tbEQ8"X!c%(csS*`)T;nCa_bLeFw47w2+NT+#"ej_(=sDh4mu61"i3b(s@1e#jytbuRi`*NsR%<7Qlqig%ZgQ2fJ!^Jls:nfDj*5HsdNGU(^,:NtF)HY=Cy5EX=%%l31314YwGC#_VT+OrMB.oo1A"jH)ykwR;X@R7XbaDL-!p
>@EC7%jU>P7Ggq@]phd&[P:,;PQhwu&S>4BM5f3JZxIE[Js94QghI6cW
67&p{V]`)q=.tjLgF.C,>SOV|xdN&';case"da":return',Zu@ibTDI(l,oo=#<HNC0@OoZJ`!?q?gyc%m|kPde>Q?O6>%d<X$EMy,y+:M=_jyxV%#MiLo?qA7x^q#_9m?FCoRCVq8qwY=4d*#>$eya;Gwf!$srIfZ,FZNy$~.yjK`pWP=:F_ZkXxmfz#@@NyL."UXBuA27$_TM4[Lbr8Rh:Z)Iv7f(,Cmr+3%3"CecB2m;<h+.@6jyz!q>h-:o7}Al7{oiLoMO:%WNkqu6U-Q*)U6e%i9mSV6VSFpcTk%A_:_VmE.+dOFM-Tm"]tqA5D*t//lGn%M(8|&CakM_*)
SL"biYc_7sL"7VGQnP"tP2QkUEhd^j?mY>3sIbL`C3olOgUK;2#O,9+J):um759Q`8VCQG,e!kIo|v4XpM=e(iVMrLC#w^G)tSYt=Z$Bd"SrQiXsL(f<>eKP|&{Pr$zac*|l*:[e=NlpB@V
3lqp+p
Kws6rKZ]?*[7_o;J-i(G?X]?99hq-bV[Yw!ER(!bmVpOSJw0$ILuSAm{Kocz#,*6Sek17!nrA|FHk_5"C<?INs@WF13|!gvC[hP18Um,$i*mn0;79ryFOp&gQ|.mJ9+QJb$dAhb>%_(vhZMJ:UL+9@
3G^Dcx{bM#p#@c8>m5H]wh|*(ABcoxGtY4b@ed8<%?~o%>%%hJeK-gNLyt&q5(7XjqBS?`ZG!13WMFM`@rj8Z
Tk#mqq`8/D3EnE1V!m$m@%mY>7"jE_ew^JV-dDN@MUUX>6BJaWX$z8PN:uV4W#8_%g2Bz0JNHV
e8q1&XQYY=A>^g4B%nE!*0@Y^RG.C=.`M@Et&KS@iMy&o-$tJaJodf))bBSd_<_?re^tn_W@"?.1IjD"#$iHYzg"M+<k
~[r^vp[O8^].GY7`e"AE^3m/.mX)O2Vi,#fITk5G%Hx0xR)D}m7*1RX2-Y)RewKB!,xB4*RSFN]7^D{aCkl7TQ9/r,7)W
Ca+Q@JlsdqSlCV3XEl6R{OB>/"D[~9f%,k^!a%78,ZDFEtvHJgLR8[pR48{NBmy[d7;-|p@2v-NV5t7^6eUNf%&H+X{i~&oFDynHKnoUX^ChP!v:AQ+A@7~`hcgr<hDkASx]b?T&kN-
5S41Xt4+Vxr9>R(+Z
SS5,Ufy7I#E/_&iL=KbSTw7Z2
~&b9+dVZRLC@rMSxo>uZAh3bT_%)[>ony3g#zYXe6H5>6TDX*
Fh=mR#w&|G}_?/Ut?5zb-*#hJo{#h#tYByf0u1+Sg1SecR6.];.=k+UG9u!nU@Jfq"|qa<Rp-l]rp[E=Py(K~ufrRiiBeAH+G8KUAP{rB^LfVCF-kV{.s4^^c%.UPlI$YIFQzMjonr`yN@?`H5@_d+`6VZS
radx-
,"L+V<{lb3MK7rtAy._
%
o=QY)d](HN^)_CEX[]e$X
?F2pOPXwea7D~$aEyp&aeZ6XH&9,#Ceobp"hpNW>/Yi9zSZdm<hA;ThHGFTjA2T+jB3y!^Qa!rw&7<7lc>cSPh/Sq3vjTfqignlMu4y#D)(DmLU(008%+&{mqdholsf?-()e)fb;-({wjl}k/%l,a5D->R!1L&[4QwZ&N*24*9mlAua9$7z!Z4dqSNg2H-Kg5$pUTx=cdb*Nhu/1b,$J)C#7KW>nBA.]vi~k!$nYYDirEI
Hu]C"H:F9Q?^WF*k*B.h(LkewQ8?%i]:UfZYhYv_/qN+:AN-Kn/zKctPDJOE=55uX@d)yC+YIA$?TVnrf:W&!rqKu1(I0z3Ikf.g2$T=Stw@AzL]OZ6WuCbnMqXHFXXBjR,oK*Telra]-
eqX:myfbL}RjR_lcAA->(dYlynn^RqW>G?_q^!=E9(n1X$QG
(]hg2ldE`l<]:ab8S_O4^Z#RNltiyh3:.Zh]e@)+=8L+mXvU&."9pJOBvFxS,(oi5JS*>VjlQAmL$(}"d/;C+D_oJED?oz(F
[S/q%)1#Z41l%cL%HM_|*p5jw[OCm&))jx2:8j^<8fT=8guz1y`NPO=uhMJM_;FEbv;|(usYuu`;V2Pp`FU#
W;~*B4EHYni:N/r@`D+DVS7_Ao4VRouFtu($V^U6a:a;,wk$n:K*KmpDlY1E]>UaFBt):le0P&=YKch=`/QT:k(&~PottAg`vf3e!1MFKI6Ow,8VHSTtjPiH|kpi]a};CXL=)BH#"9cbX`KqBbD4KN~^Po6X&0$utsL<j;[`**RYncBY9z)oyDwB1IOiBY~w3>AbyQe_C@GE.AmN,kY,0jBFBiiTR9E0if?fEFi3{#.!vg>18Q2e8Fx@.#n95xDgu:c;2U,WI
d190r#XD}7GKO5-GgCE$wi3I3U1FrFs=Oka`@SZ=cl7J~KdF{Lg_`%zwwnLe.6IUAw:lSic?
rSGu%MlZ"G[e`Q&V2=Ze4Df8BL0df*BJF^Z`Yaj]S{E>3KOKKdad
*/d3)X.p31inWN<sclQl#&@Ah-f?|V%o91I0VTsA9eYB%Fn7ThQG72C_xXwlt@g:HLg2:pz>C+E_prsRw
S,4WiuFHng7g!C)6Ks``wThrSp)Of$jI-WxRPe
*z*Nn~SX.n?]@m
hm!!lrSE
7<U]ANQ$j5FPHSDJ@>k
!Uo|#1GY^P]LZ&9|cE::JdsayR0eCY]G[%U}!6T_=Zc_>`cq&i*K*Dek_XO>h`0h9QJ=JFB6)A.{CyL}#oI:,_acQkO<e+97Rc)H[&lo2KFu?gc<K<SJpHl)Ejp=K$Sactg1><.[ykE
?UT(@S)=-k7?7,cNG*X>ee`D-?^Kl]5Vt|7!4Gqd7TBPYYJpy.<~VpK=]X&K.a/p.M@~A
/rH.]%
eIeq0Q@M@O4=iW~a9R?CCtAi,0&2sp;o)';case"de":return'&]^@iaQD9(lnej"ms"#0l;e:]se3%Zm.9BdhHbB1kv;W4+8jdP{e~`8v<&LCM#$M=sf4wwJc}T>DtfdshU(5=Xrk7
lS*UN.*$-ukN#>,`JmzM_l4;$"8g_<WB=Mxyd=T09JCP0l(>t]irE/BoLL?d7YpJ~wZ70hw)
M`2uV=Qm074oHLF?J4N6cLMtC@DK^Nu?Mu.8vI[&JuGLQ,6DlzH":luPP9bQe4y"5`rc)5R@.}W6<l.242]+K6w=uMUXe1Yd5%VZ"A6FyH!od%;1G^r?,jdZRfeBiQV4:x$hFuN]0-Qm7)"4svp"sDY1wYL+#DBmW:w_Xr.FGS&Mh"Lp)K$XW:*%TUh(t76[O>P+
k7anBpz7"]An|"<?b30e9i05vw>2QU[*wl!W&^Fk4Y%nuf^oZDT"YD>lJ.)R1P>w=(.KZuYcV;
s#PK]3hoxb152KN!r@n[XL_$HsRW]wRKchDB+0,o_}W~iempCJh;cq4~$i=kq;V;rE)o1_`Jx;`~`N!bPC_+uvIgW};fn{6FyeQ]*Mr{/upCamr54n!M]dUN=Sv3Z(Lj`V9wewT_VG&%L/M$%huS3`x=w|O.79fbcQ>PR:y0:gT0qF3ZEi<<r2MMy[=(edY5^hAjm+pHW%txK(0#H7cUm&S5.tXwq[SDafn@,6hhvM<%A@B&iu<NS0HD3nsHcG6G:vWJr$MIt{.!aJK$V&sOrjvz%Q#*,`b#$=d2WbK{#PFZW6<^x$k:Vpg}2aL0iW&pw`3tI}OqguaW,0_KZ4+;8s!H+#xE_v@:
/9i5>Ms*RLN(8SvCj)nK<OyTPaEU=Q3(2Sj5jE:MOkL/Oidk`%)qj5!b5mq!,/7[Y!Qv@9=&<M&0N%9qn8:P*gssgnV+9@R4}FIBYh/OP@YohCvv(C?+]P?GD-b1J$.l_D`A`iU%O2=BIfTa54_2|L/-?X91(xX2t+*76rs.?>sX#bmY-[g9#TM)&C2eCZ<r/%W(4_d2dD<B(]p-"&0_V4!0}M8;7R/-&jP#$=~""ZAj[a-244ds</3F^q9uEeL@q$"LxbVb!*!TqnkiTit
sUr57-XOKF!M_NZ!2sTRTEZnRNb6u"O8-9HMn;[LUk^SuaV]c?Qp;<tPGZ/Sai6Q$<fHk5g>.950!R9!SkKiH_4)HmiIBs&6@%Q-;s%MP:;?_$iYq*WAdP}@Dp2B9-]jes-1lF=ZR^}P5R&Lxd![V,qT#1^M@oC+1TCHP2+M=7xQ;ryh/z%9eow:NTs`zk_$T1U6j>u1MK/SB0j:0!lCC<,3jZ|hAP6a19{&"lYpNN^g*@w".AfU_]?(od?O5Wh;i(.&MU<xTyez)1e5KH<VGjGS*x|S$4B.=z&s
S#XWAV5m8AA8ww[1>=[K.kdqxlYgA5Jp<9qwP@<(qSVs;H0~et1K8sS
Qt[X!=GzR(
MY
s"?[4o-t%IG`Vmh(Y@Y?$R^8byd/c
UP:@J|
I(?:}qlc>/MU7[A?f<yBSB]TZG@!E>E4&Tr3Y%I<atq+k>:5Ui_y[rBEK"l#e((dmA(!*?mOyQTpmIz[823e1U]>{%=!/.Gj~?E8{"4<vfS^df7DZSV]"Sp)oHa7.MiR:0/1)@y(Nj19C7wmH0LU6&%C&oW9}?Rbzt!QhC`p.
C,k0hxNRj*P@
;tDO*0Gi"G^Hna>0+gB;AVomP-0^@}FnwiC14Vlv"$58QocsNz]^9Np1%1+4xt4@A7ogJ8usJKCX$|IH=1>u<{WPV2U7d3@
X}qiqB4TYBn
VHC*"Wbn+)LC=HBdJ-ND&eUmNIxUxyVbf)q:Mec?GNGFQ*mw;YI_,D,Y@nJ1(H
@DZt-+%C`IWoqQiu|OeHgt8DvYR7tO$1DxFT0oH(+><Tv?<0XKo>USNR
OMU0<T<
0wpqT<6}j2NZU7;!g0<YQ:gu,yjf/E>[;SdL9hpm3e6TVx0Fq_`@e##u@L:1fH0?*$[5M-Vr`g>`Y{moa%]|n+P
c#Lt+<8+-7U)F$Kl>JD]3<:tb>1<Ayn@,"d*tR4p1MjH8"kv7uo:Tn%L7uJ3kj^cs~.lWnq:J_/d1DY.kE%k7s5ZP2t/+&QZHgheMMLMX?S*W9Sok03@<Zjr1=AL)q?_r?&Nu-Ysa^<04dsL*p0GNj<o?Fcy=umO_&-&l)RDlM#qKSuNRc]+9gBzs.gWa61R(;=W:n(MgTHn]OaP@7U)Dv&m.dCUcJ[Y<,U]UqC5BzOG<l?Fl,;ivnR*Y%H)de,eN#rtugaa^/-<e8`yD&y&18GK,RC&HWWZv"n#ZKos<l3t+ApKTD&3[IAKmA(oQ2TItkigO9rZ:*>oUx=#tOEWxfZdv~Ef^kUMwi-_K:%WGiz$&wLF^Q
"2,.mg:Cx-xdHu|k.YW3qVl`>tK1~()lEHF.+s.h=M4XE$K=N4c]e!K
[8[;&T@f?`bJGe=mu9^.x.wwC`(R:MF:h*x`V&WP2s:P|=$O/:`04aOhgA4,x4h]J@?@MMtnv]9-mez&]h#:ha|V:,+L}K^o#^@@yqV`A%t^Dg7scqbpW<O`kSNvJ^Y6:`x2K:*s%1IymI^qTq@5p@wa9M"t087jZNuv8o0yV40!go`b1]HLC4R:(rCQr.UM:y?(rnX:t0iQ[dPUNvP*[#~FAPPA:N,6383,UYIWs(ngDRYmTyK$}hG9W.($RLSES!~C&6}!d1EbFgHP1F1RW0n!jCqWpENWFHM/W7,C-fO8[Dv$#Yk/cYAtk;|Ls]6MdZw3N(`r>AGJlSKc2CbGzZm5@"#
|1?y+C+H}O$97RJf)cWh]0,>5*iD/^d
x:U=Rwac.B0--TW_tbRg^(ZR`7H>B%qD~fvMJxpe>O)d_qLFD*]ZeBmW}Tj(6Zaa<(.8_="k`h0*[GBnd?gU@(1us]KK)`K47egRMSF>OAse37@o,=kQ-P8:pA-K<5BL.rB:<r|1de3.cSwY+,nm9xg+pEi,]5Ej[:ZaVJJgSAK/TJ?Cl,t!|W=4R>t3}.X+mS]LfIL-/-H;[wV1V180z
<1h>/;K:><5Vcp]Koj
Ir,<Q<@2%~Th!5`WFwPHQP[,igZ5,v&;gMS1o0!XsV<Q&J(%n?Yz@4-|uxBJl3>(?-&3U]I6mt,!;
6NA=>%
)iPv;-$?,jgu"m~kAmHN7rBo<W(2H9X]p
efvcg"2d?U0Z_+uax?>Hdt7_,g:abp)1S>`Z|kd1Z#fO+SrY1Uo=f1T2P2p7-X%TBQdy$7=^Jh>:4FQ1X]"60+pb*l`+
NV';case"et":return'%s`;;6KZ+$#5$fnN>SU(3cuCw;oFkN]u#rtmE@i>:9JluS)cM&Zjy@23aKtfw,MM}919_ku_)IY-;&.UdE:vH^KB`
_%e[O
kE&Hslp_I[NysR;n+0hV+<m])k?;-5!w3v^4<=}OitkCH;s9Xiud=m9*+7z!U`^:M[7A@VI</V@v|A6MTI#ajXEY:t<&+al(N-*]U<[s
%|laiRz%D19v#C5r:&8<VDyqAdh5BNN#V&k-Pnt/JQ8)$mc5b`SBJzo$`JHi$re*%tq%K}U6iN8I7o.W@zA))87`@;(=c}s2sh.h6MDVOu7}E1($l
l3^?5i11-"D%`
*6!8[^GaZcXCW|qpKZaH@aePIgy`6-Wl?>:`U>k:c?sJSEXM8YN[q.HQ_2ROt/Wmh/g9.QjO(7aQ&S<BfZi7n+$tWb0;QoYth*w{kN>=qU"MB^M#hWe<HJfWUE<rjWAAe/7&:e($sQ._Tk63l+bD"6%
o)6B^L<./K=/P)D&r/rH-O)9CfQ|@Jnd`*oCAi((
&Sb52GB]u7qm2Upv[yUOHakOAXWHCEo)-qf63%SYXSI;,$E0&M3Tte0!/%l9[u}YO?="cC5f^j,Q6_p]=#]>+]U`5%nk,%Fp/RDRYpNAZ01`4/!CUYqg1dr#n?HfMEAr,IaaD*3dZU0]kA<CswdYg_Pa0$<[{c;wl
t8_L!jfQE2w.[V40W/}U>OlHKH>65M5qaH6$jm/c<c8JkOtHJ$sqaX(=AT}82-|pyVrv[FqyG
=uf7Xh1EJctq:n.YM6Cjgxfu6UIu"DYg4R]QW%znCs8S4:F({QI!()l.@gDyrwHoB"x3G]
nN0smRK=2r?HWP$k"#mrj8WL`*Q=(vYH`4%RH{XL5?yJu=jF.e&
"rpN*&ND[oseS*vPZX.=pr9B-%+/Kc<}%#./Oz`Xyi$yNbNrSuEa`F1@w`J31fU4^U$RruB9Y1RL/Q8?I4C`"WI=!*c!H(4wdwq(^~FYQ4TNxt:6#ukU3;IJ(DD_%/Q
C<"&
RoirDne`
pz+&`/u0F.C5/s#k4Jn$%rcs3^p~X6a~dY
@En^ODN%4i.KKj]nx9:H^S@ImeM>XsH6)lL<EaLrUj.=]a`fdik]gkz/OYC?p`wNR2
SL:JLS&bIg;vHTi
3j[s=4wZ
T&&(0QtNjr$T{hH4
:R-PR$tgPjA{!BmK&{:ndq5Bu<R`NnYVu!0<W4/dX>/t"LO}$n*Q.Xy(KSgq)**M,KL,Ex>~Ur9
Ju>!/z!cBZ6%:^myiO8%,R_*p@`AMTkbv}78w.h!tRrQ,;x52?#AE|
8W)nnFf_DUYV*c~[flo"jl}M^x1bFAo7yD}oS%KXL({L,34oY%BBW>nj+^oIFg01^u?=h!~O+2<F2-D&_ph
>jVpA5YE"ME]?R-Z0xxTRdL4,bN0s.>q11(3<$c/UEbpPxYhlL+!~lSh$2|fw+sH=?hBlVBh*0gD,wIX]]om?O7]bUQo`9sPKtd)/L="qq@YG)`bXlebPZ5%8rAU4%AU_[Tl]N5RhK,2)!&7Ncigxde%M-BXu=+M/"22G+I*d9LZGrp3cw&k/UU^8D#0QN/]>PMM|82$"Tzflq-0lgow:_sfx.mkAG~lq&gV&i|Uuy1Lu?_!VHtn}7v)YUUWgA
YhxH*78VJ}et@bR@yU.H?!_+yPvEA+&W%
PG]-5Z:K6pY{.sRbBucMu?1%P$&![rNa.LICp58`2iy,[AuH@QX@"!Fsw#dP^c(2!v"n#z[VSApfN;)+ni=Li9C:eNKoh-JKrZ[LBBXRml^O7JunE`Si:Yf=R^5YI>f=D?1&EL9tE#u>"}8e5=j@/IN+&l1Bq2)99y)T,mjI`&trR`AtF"Tg)<L704+ZDb5HeyP.R}7W+Yo{FN0sQ)5WTt0ArQ-0kgBwx)kB=z1"
-uF%7o!ezb&[j?|j
gMD~+2Wx;T;jB(PI-Qs_*HAtC>bF-^dN2X^]hga]S<w8F%ppd?:Z/1V37V-F!M:lXkO?a
dplX.[bsGEZ"T8W
T}+.UzcP%KXxk|i-,40pbks/H[Q9L-/PV5X@^_dtmbfM#1[a_^qd4UygP4^styuMH3l=-DBc467&b}7Pfy^=kA$-j6*IC4i,J%0"4$35oWeS[a
4e)3J;K=ZVP&j+#jZ%f%d!!5W?l6IYv30g21>(LRt[{K&&a4*pjU!nc"p`Ri))@E;lwe+`6
6ocpZj~6~dR0V)&be
UnnBEckWHU?FH><,J@!:^F(kgX|uzV[sQT.0:0VnQ
;qF)HrN40Zk^AX?Y/NyKudRD
Z(Oj$PdvDpipCH?~:fqrRa8P<6@~e)SJ,_ll>58F9)tfz"W;TUZ*]>,]VNAB,2w1vDJg"}[##RJ&K=rDn$v0d7)=Iqh,';case"es":return'%`G;BcsDI(o5*o;$>5#/ngw(M&y_
820B)4pEh)RMFhb~4XXzR>Au=rfox!:-j/P
hfo,OhsJxIn[?Dx4m+_B4Q;)Ns_@vH^+tR_envPm5

Fh8/j?yx>K`C3-JHfHt7IAK.G)(_LcI.|scq.!8)#,IF1ma=*8b_9$dy|^PKJGleveiUO(60.a[ZJ^?ErIh"IpF2mOd("mJ&
,>AVj*i{IZ!@4N-h&c<f<Z-_qkF`xaz$w9xn%ZUdnF=Bz#R{PXb]wGfO[k4oM&VQ_U3c*,gN<IS5lfn20!$t:Pw#l$=]7V1]n+IE+3)2a`DrV%vbP?NW:O3-9l32]Bd1l=@7NTd8n8Uy3~mtIP-e##iVK{dyj-YQMrMr_<@Cmk5B&@^]XT,;aHWdNfUV@Jt{C2aXoV8sroYjhucgf|/&eGSwnHm~&~5$s{,6XMM7KHV|K8K4.DS]P}7XOPULY6-D;+brl<M$7=,_,pFOq{rp2%ycL+J%tAJ`od^2ef-*KCd1Y{]c6kJ+<9`ACD,-(Io:LmFAi|FH<L!kf-jfGw:D9Dh!f6@2&QKoIN$og8TX[9of9&7js9Bg>
&m_n
*hW?j*AKTQvW9hA+q6$!nS+K5K
-[1]680`Ri]]tKBGeU/io|`G[]+7qeFSdyHXK"UTIUcJETY9e39z)5x5:P-2OOjyYML9<
D{nzbvQS!Rd>chKIN.9n3"+
:glNeG(^=,E*4YWxd5huoc^w>haB9P-raMi#c,H?jse<6CKj?e!^G:AET!aRx4vLD)<>/*Nw["K/$^f4j`.P4&@VA!]W>|Ez1Nm&n{Rv=i.?sCePrw=Y;WEkmS;1+ss`VkfVB$Yg.]=_%4>ZyCGWO?fe#U@T0JxS[~,bZ1??b}d]+l=_;gl<rmi0,Sc[FPcx]J`6m`drj]jc5.HHysItvLawkJ9H_]pbYnM@5n40(~D*g_C,tU[w,hn]^BK`v:^:f?=tVXc)dT]kS9=*>ABIchLb7F)=sRl"?D[qrJ1DfaY/d6dq.xhA^h%p-p&?a
t~`9U+iM7n)LdZ%m3/j$]=yd6y]G<Z]
deb&n<H0ff.]Tq.[5lI
8Y5u<.@"tR@yj9TIXg"[]5[?ZM#)RU+bp7=[]IkeWt@{^fT(
qde-rW6B36w8#Z;gCWAthb9yEZ&
XuyQlLx?*8o$cntB4!@YOEg^:D@W+`O"F?^]9>ewuTqg%2bJZtd>gfjOV7B2p+26zk1+d_:)`YbE8&KfMx{X@[D.Ir<4U1h6!#1ZQy~EnxK!<6D9z$_el0O<sk|(<.GC,Kxf*Mr#Cd~I7trKYvVP]Luw{H?u:kDE$8QmA/A0#.iZ)@]>~R4J)RX.+B3&a&b7.s/F56h4{@6!eu`Ttcswt
8gc&;PKg)/~Dj`9v@!r8KV33/e`-6Ri8"QppdyO"PlZ0UC-".3VFmB!p"b0>zG"TgS3R,(B!~o`Xpsju3,=RdLi2veT56"updOZ10+KuXh5-bYpw?"bE.ToZ<t&=WOQd$5kS!-LfsE5"*=Rnk]9kx"i.ID.FRj5_B#ge?Hp)?D+OV
f1_(]sr%#+oDqBi:J9K#w>:
reP3{n%<m5zBH>guu9~837%Sa10c/E{quR{WY;_"4,kguu0(~D9d>vQQ;C|n(U?CD)9u51Mo<0D<B-c++A^^Ed9dD<xyr/OaR"Ysf^Ve{O~Z.Z5p`Et:gF66(rGI(0l.c;-qp&tY:,g(vK0:XZ=J?>to}+%`R2Xqtmn#NZ^R?k(]2mngws.M1BaXKQ
6ZvZb9w`>[mpsScN
Beo`sS2-le%rsd(xqikgD2i/O%c_(60CXqe"67*]tikAqe$ZGOJ,z"[9Z/L&ll*$m6$K]9gPt%.
bszK};5Dvv}0O3NQYvM%kMa]9L7/"Y_$1:$>~bfb$3pCBHDOLP;`_!hIEnXc8%#?NQQs!Z[j]bx<~,1#>Txs9j2oLYkVchx;`#[G[FTs$l]]9XrVmLdI.g(44.y:=1)$O7RRLf/(wC?ViFW(]&v;O6D1sxcZ`d#G4%L$"mod*>zrYw8?XcQY8#UoNS7
U0!]KF"P9L9^UC0UJ3tZtgJ8&WYO0]VguTH2aJaRQLEFAGXppF7`0pPb84cQSG)qsueTpN5>WdHTiD]5[L_4):C$7IqjePFEvy-.&8VAW].3joV=u#"Cv
MF|1!s*%r(Sby0tC^VbN^b6T}[{F[I:08=xVq
Y?7Z}-Ws".e_$XhhJ^Riub}Gg_xw!^HS_SWc2$A/IrkZy
GhN;9N=:0jhmbEbsKu-P[c{S*+<u5Pv-jT=gf#*^&bTIGSBb
?3IT67*t$"Cr.!8b3Kq:Ats7@y^CtCh&mH$b"kF>4OQsW47L&jVI;nYYS1k=_[1XmUG-]Q^|kmQC07FqQ#,)YrLqTUh+
M7oN|f9vtH=-1k{("pQp~&!CJDQ]gLynZho3)st`{uC8M
)=S">V~Fl[HW-n{OD;E=Rf7vh[Xkwk/EZW8al51Q}ALj>7;cu&g0=yNml7(/[2^RY54G+y;%~,}.Kyt0m.|jY<U:Pt7u4&HAB7{.G9N*J
R`A%2^|=9QaSM,>uOy^hMA#Fu9@12b^emgo?g2e.LB,:ZXlmy!;F]
MaC;^B^usv;0+.)-GC5rsR94nTP<sJcJ4Ggc?PrMV@;`H`sw.;TL_Ww:deRJxG:Pv63wMTb&m)7(y&IwB$oSuSn?BW9&diR;34gnGp%#<G$WKEOtRbQk2+s!;WY"GVmMlu[.Rl0`pf;@GFyH?x~=La@Tu
>-gJx1z9-Fhq.nJ;T06&UZl)x>iF%wv2oB/WVQ}^2
67afYFM<]EZ:=M",h`4#Vq=0jIj%2<_qU[cnQJe7+CP^-?16AGB=;k;O/CfGM0~lXw+-Q(:#GM}PBZf>T=:yLDF7?y1-pSvTa
x;2
[X7(4yOp(q&;1Q[#RA%Fs[H^F#r-kR
OD1I^6":(-qNPV-p?~75>)ECm%undbg*:U<r(9BP+UOz<S3&=^aplkv))H".a3MuTAi"dPdUF)7d1bwZSA@buY3Kh=hwa8Pq3K?>K>T@YM)d$l<Ls!%X<KN^..0sAwA#]mVuhT4.SKV2Wpb=0zq8uRa5X8kWH[jv;k@-B=r^
}UI<%)x&]1n38KI$Ys;]<7)D|^,!~!m@;2Cp~snh"VmpkRD
]4IpY<Q-7&<Oe%*X^Q;)6.q(<CDn^=LHxF_t!QeHbWby.=&0;v#;lN&';case"fr":return'#Zu;C7oD)(nv}NR+5*=FN*^:Uo1pU)o3+8H#kGJp*Vy;}:x7mrB4lX3gFwdfQOG<u*-yl7z/(/%
msNfR:{=G8~C[tD^+?VriSLWwx$FF0,dc3y*J"[,;LyN"<5y,`aqK,`"5P$ss)06l[NcJ]TMV<siwh+LD)X@T^XdG
7<-
m:Oy11rg_=(MLoFj;cj:L&U0g-q]wvu(i<v^hG/=6mB`$t&QXXBJyz(tko=e{boeAd[aKx+q^t7[n(vXd7k6i7ouw>4]fa
!pE%/Ff2fxk;hdqhot&bmf_?F="|
xIY_=X&Rk8
&JpEy+7>,Fg_+i;ALyF$j/WnC,TWS_bdmU0@Iw%`N6IoaC6])RyPXFiNXt2-L[_Aq2O*y>Oq!R?*y-0|R~$$l*aP!uc@]>;@"8wx2Mu)ro@N3`dML*ZmIl59-<5dL_$xpj]*HMH8Y)clT8GsQ/bF-m-BYLM&d<06)oH9B3w>O*Xh7ZEnC2,7U%Mu.b1@8}EcnO--v5eBxKE}vK(ZxICXPQ@K[aHqyf&:"#e&Jd?Y4gyImq;R_mftmJGp
)3*f2ZZ$iMcFcevG^eZxCxQR*aCY9t@7
K?jeti3|Xq?6:%Vr-tR`,

Wt7QtBEUxs.I"YSl;vk0N<I<*cTP,AA1|DC`H;2/7`AkF
LL62^@PJcDAHA&l@Ls.^.wNrnURc4O|@l-H.L-/J202E8Ze<N)Y@Z$J4=T^
tKHQ6>`d>_eT#c>p/%4jrI
BgZ~dq/RW#UrikhDJ33F#W-)]?ER&00
pLd74Jf:BLN2TX$e!PtjMU(8)VBG)9ivr#xN0(:at$"__AZ"nzMh=?!T5>d([`5k2F_ld%@Wj|NZ:TgVKY53tNZCDccIhl#~q0>8aM#@Z*eNC/ByTNCZRjYnL.NoJI@dLLfwd0r14qlkiOt},ViaPV.Uk9%>`tpC?kwuQ
ia5$gu,&BOf{ut1^yV4joF8v9*F|Z}+_L:hr30<(GED~x(e])kGEQS@//%b}2E`0Clb=#N]fJ*G4E`%9@VDS6!aN;E(;28i_$)$5;5Fu+_5Y+q&s<=(>_mBqs([_i62$"dYY:8J|%65d/]YN-9P<c[
l:.fG0J2vdmovm/GKR~NXEtbl=)_Fw$0q$bLsN+OGYD.pJ2X@Jon@sU;@eU1kwjll"74/V4T|3m&Vy6q9(),mY5IZqb.eyJYIy.XkvqOM5&]Dx[!fT)+To#wKQ]AaOMVJvR"X%)fT&%Ww_~cyw1u[IiXOQ[<|CNkw;LS2Q#2OF?u]l?d^W8q2puke]"?QFG+)cp`8JXq/[jg]2"Z7b`%&(
Y(W;PT7iFjZ_s]3B5q3RqvwDueg0r^t53)<y@Z1d"AdQusA2gZ2
oN<|KNZ6V|9U*gUH3VcJ1QTPn!p0Q(=c1*S/&=9v0i2$;Q<Xn$m"]X(,VfK!S#:Tch#T!y=~wIf!=nNR"g^u*}iGX3Al*L^Pl@se/+
vS)5Zw%k:521-G9fS_r4pK]19Y(-ggj:,A4/9rmHA!<
|][B&=pu>W[`{4PPVHn$6UCykO$t@CT#.l`T2xn-aef"2#m?w/2:.7)4rTq@~DOPLfoi660vcbmy^t@EzMCCR=!2SdR>D<e%ih[v4^,+]DqR-GL$/yuC<!BvHja
MWv]X=vZE"]idpGII")]m%.>Re4GfPhG~V>f?<p<%i+ptW/A*.O@9_=2au|&Mow0zc7@*xI&rJ"@eNe>OS1y,%qo,-H:X3n3w0b=N%>^{dhq@i|y1g&k2y
29>O=j!_
A.pol4|;I"-1-wASKg^&6>T(6*O6*U-%m5AVF3~-LN|d5S"d`dHZ%P9^ZWWjQx[h)3e
"1kC!B|^|wk0<@aTD)VU4_3/NZ4aQr#O6QhSI9,clQ?bKCx1B!Q1WA8%5m20h!PC,0To}dDLZm&MGP7(&c&_(KJ
/wzxBc?vQQsMm])5%A|xg7+rco9d,9W=EgonZGU7Ata-Q!H-hLbRFlKyQf$^(Y328nSEr/
727_xv2LG"kk#V]VkRbl@1;ok5^*xPQw!y,4:4AwXL*LB]lN(T7!K2>7(/>s`]@]#c)1h~#,qJ#Ke!TTuJ3dwXai-@4o!Ud0r-j>3C[KZbC?#jbT_g^w>Ww@/aG?_!4y@uLf-EFznJy1C&-C`-:L%9BbTvVN[`oR8_vV[yp5.GFAUAZY3(P}TcVH>VP]@
Yx06O9TJdb#@KSyzovHN=L.S=Q%FtxP$)ao9wY@UHoq_D-ql_|MLC3+V2~rGXN/rqqv&"G6sjsLcC"?GD$&EpRh:N%bQ:R5q4Lb4i6Ia7rN+9_TiZ,L/#x1l3-ddMV9jCRhU3r>)%{0Dx/[F9|Ibl.ER4I`!<k3,`1
$M[]uZ/[4]ujhIbS_V2]@4kye:Fe4
dVU-zf>SH5T[v2!<{&V1W
5Ood1W^9X/aqs7u2cZ,1{u`
"`H*%A;gi?*20G[wv0*&3E<//aG0jDF+m<-p!]<V`4S=j
3tX-a7Y7<ppHELs`T6w)
w,)uFeR=L4@z5G6aPq`Hd*^M>C@bcNV+PLYeuM]X($GOomFY7`)lBLJxuh4kf+hxgaS)o3Z/k^,;L9Q<9Qs*`A!M0!P|-+Nwl-Z8U/Y6(yDxU-3NHwF<[C)%s&jE<94S$VB%Ge`<u"?CAaxpuFe~.[?Dy+VuDeJriJY@N"1P6Ns_,31aEymiLp$A?*`?+ffx_N[u4Uq14@x#Gz-L_;`4MGezH!qmX,LBjULpmWY9QaQdtAtYI:b#7u##
S?2EcxBSIc2)}z(g-iP;w6.;&<0;#Mg";e.+{VQp71S-hOzeb=KZy]xRQL@#>K0@2FPp:tE%cl_nc$]Cl)p5gjM/:[:<KOY.asjEhZN;v4T`zbw5Fl+seiK@NVZQg
}dd_PwWb+YdX
DA6DX,F|iv/EQ:J!8XIUnAayB4c3
?`PS!pc=4e><xC^
:U[=3V7SNeR`B&upw-$ic^w;FCIoN5gx%^cd<9m<vn0O-nRcUs6h[d3XjOQRL9cRL_Z4@7H^jW9eo2:E2VD6GACS~cg3kiF*kAn&Y5Sr,kw6G1R0s*b4*ore*q!0=yAUcSKjl:DWcn|x.S_E?YoL%TS>Rk"r-rP$*ga7Q]BM]tP@ly,i*D]J1v:,Jv]I`<w&AL_gqL%5P3Lc_1Y:>U~Q:uq5,-eY@;a;Oxm4kUo-xrKQ]@W"b.BS|huA"_tey1K)s,Q
j%A40Q41{Xna*qcQO?eS^Wt0g=-iA)US!_y-7Gcwi)x)bWqV)[,gwwI4DTAIOs%j/AGciHct^';case"gl":return'!ZuKk6LD)?T)ni`"XC%/q,dVg`
RGPL,2:T+n#)@%]{iYb%3*@U
5!&yK#AepUkC!8KGhp_x}0Dqjrz6Y::hAb|H0?]L[c#pGA,ywOk![UqN$P`wOCGo+0?
RC([1G
p[rgeABiy=*>GVt4Xc$_S[(QIr_R
p<OP^[<Y=8
vtt;Bfo<;R-7(Mb-PiNhvD[;k:WL?r4P]vwKW=dP!?nD<vkEc*g~eYcr$Ldr^:wf(d2d,Mc:o(ydEjWc"[y_666"AWA|h7Uo/U5z4zx9EE82TmM":IB?/}H8%}y@eHK4dnuk>md,^$p-]~n?AOk9_:qq`},4$DwpXlvkUe)p^xQSKp-@M5J8I_R%0+D4mW)oi[##Uza|2p<->t@b
)hnX|W.P1ifPK^!Y<+:7^"WJ?2>-^K3VbdO]Q97yGXvuu=UGzCU5+/Dcqe)<wYE/eV:L/TY)nm_]nXmsS4YI})^0/?%&JXv-mO&gk#cG@FU2zsmno_YIW1zukgZVA5j$gn!KR@/)PK/#7$~^v?U>{T$(6efV-HY8}0Q=~<D)*gwnF@U/1]
)FE$eN98el(D%Xu&kuq;r~.ay{OvVSJs%yNrPbI%;S3e
6N/`}>p-[
4n<D5b"e?Uz=g>-QAL0%hc)t7JEE&*A:O#hG70+Us#ZlWm0vHAxJ>YyG%r;%{q@2gEXBicBG&I.1N>abQ/Xq=c.<P2[p}gEL].Kj>&trvUrbD+1LJ[(&bmrFH:Qd1B0cZEb&q4}*8k%eNU[]Q$}QpW9enlXG1*}Kis-&>aBo5Q1Q.[5m$7_&y&:oje_/c^.!!3TV_DHsehPh~5W
X3z(7X/XPkWN`I-cUFTP(n#er8V)>,I,lt[]3HGniafFr9/jB4tPL[HOkpc/]kKfiglS0:*H3
4p&:@)aKW+a-tN|Zy^DP+gE@j2)ZlOL0O9N)/WH9Oy~f/tk(</vy&sEJScxnee8mK%o:rSD_2wAF7$$xgk"WD<Wz&%b3:%g%FbRTM>@j$r6G:rcA,?bUJ1JYI<71P2
iJVb8rW2:t;s?pl2-+7EER-0bji`Y=N5(e@^II6rhrL
;4"qUtyJG(.&0jA@7h_2!~`5UO>J>>/R`Xvw^hK-kbgRE7;(/`s{#V
x/3tIs3`X;gz)o.8>-I,|<1`)sU]8k$<djADrv^/Vf9SM,rfY"{5N&Z"}/n5_v;X{h<OQX).{0.]9EBvZk^Kqx!7g&5y{G,pdD)P.8zKp5Y[f(Kt?RaA37Adg,#6O+7!BPSaSSIwDwE"C"Z")PKbG*RoBy0>SJV<F-"<)R$s
MK(&B07?dx6~@4hom1l)+vsJBC1CdVB88|+ZLh&?Rnyw%IJ1Kf_)4an83Yvz.)rZ%gaFnV[xC}1gS&CkR|5k%ErGH5[f+pU58(.E)5<U$-9E+e?Y>>P&[6N";)NpJm@>ZHCvOqD+1pDKowo6?J&Ow{#2)4D(t3@mfrRBSEa_C!&}I4hJ!LkEMvuZl-5+j5HDxb9*)E/gUH(x
$@~=6Eva-7?DkBx<%/De;$~#AVJjhX.$]-}a;S1>*Dbcc_~yJf-DV*<"s+nxz.%/8cA+kE>%{w^G(d.rXK6*<#o3~*P_$DL]F0|NV5xW*84y$v<v`wZ-5V;G,k$%M@586vYuLe3
Pv?JM7Zk!H]-JY+&g)Td`G=b
[p_[OUAa+XO
Aa6O;-I??~mO#$g+%Fv`htB&mMKI8pv`6CFld@f%Q_CQj$L{R!M^R.hmuWc$d$Lkl=[KDfg3Kk^Al>n.3>0IOwr<
vM)GPL#x]9N*CC-F&/jOlZ?C:[qq$Wc<r6XS=)%h:#+QWK[?uOU.Q[#:?sa>1#(TC6Iu4/Vu0JrF/?bNu/y^{(F!&;g(1"J0?azLt%e]ZQ(aD=RTlfdl3Nu[x#f%`ARuZq|o:^x
]0P`Yn7iqHKFl9GlK-G
g>Q%eq0tjE$SeKod^KtLSeZOcbREN`jvkBrr8%=VuiNlOM|TJ--nQc&]%P?_)u9gAP2@@(aDdIw*lj;
9s8]ZC8FV7zTYg*-oIM])$yLLrD1(-7O}o"n_Vm9i`loPF206hzo%R;`
hi)QQK9ai[q)I<;S"1%8UzalA1Fi81$?WQU7*nT[1TA?/Yj:EE<`d#_Db4dVS:lx_ss0BAH]y<^xaU*PA>J0">=i*.cpIyxwlw@`[sAN._w}V!.ci$Tkua?0tZd+aG7d^`cJ
GDmgH<?IxjWGdQx)4-3qg[<[P#1W;L9vIMZ=)$U+w&S>KY4B|;>2%wjkX/g+sS!Qg`sgL?f-DP&.?OqQs
nu*.YLjm`_uW,"[Jw!.p5sX"fxZYPeao|%Q%Ag7eI4=P"6PcHuWJ8gxJ/.n?~4$e-ly!yOtKseR.>OP?fm!;-k@rv3<I5dx*?9}E.,SVLyL"
JCU1?sW<cJ(_$wgLqL;:05Z7.P1ZrZewsfl
h[2a<%B%i)F0<F)G0>v
(MUH&_`_4_%|qg
9Wh
t1)vl)L5Qsvq?E.FGH>YJC8NJysNmdOTC?`:ecD2+
~s#.v;`46o%n7P4oFS
(F0
He5/H$][;$,4-<Znxe%O:|#LBUs[Z^qA$r2;Vma3Fnb/%lY:!cVrksF>]8ko[NLs
Z=cJS@^3"`{_S>UgPYWkqII.}-oHZgjS~#<4Vg<N$m*L{0(^Hl>J
DC#+f9w=SBR)86#/UW5-qSv^m{N|wrC^e`]h>)Ad&h^]AFpt,,fgMJ
Hn;:UJRh!4<*$f`Q}Og71Ug
Avf1Xs_d^ccA.[3<QXH>QLE1=(cL,"i;^b9HNb?2_1tY6e<sa7V!m=Y,VSRCar,4tKM;Z>Yva!wJG[|g[bZp[e1H`xGg,`Q+2@g-
ne/JuT3n/o1_;IR:FXYqPLlaA#m,C(6}DAK]EK.@@_nL,48]-NOtL,A"-}w:V1?2bu@Dkj#.w|ACTzwr^-GCE,5P.gTLpbIZ,ze#=~yK%c3T=WKIE+4SM0u)e[Ri^4;#jkuNr);j"rl48[]X^if$?VsyZ1-Fs9d5)#!C5w;iR#vD75<bl~t)tS6MnQ(#If?`%men0^JkLI+F&&wH%<Q?l^V]({Xj.~^~uvDKxQ$I2p^"`]0FUGhs$h8GV*
eOs$Z=lo9TW`F"J*4?~Tl:4hxP~1lF0?b5~DUDb_F`3?.ymHW$t(Q?L^+SXSA/:o3p(Wk*js1d"cm5ZW!+i1i2,%D
2*!Mc';case"hr":return'"]^@ibTD)?T)ni`"0SU+i+&3FC!KBOJeU:Kan;nB@[IRFg5n[-.-V8Cta>%q1Ly,3TWloXr1axPF1aZd[FXj??tw079J6d@-T&ln,XkH}@b`g)gY;oEu:o|IT?uHJF-:qA
r-Bm#<JVavhh+ZBuyJ&dJ@vXnie=L+uyU([`QebB6)@/^iOwS#!H7m""deuDjX1?W64G&0%|jBDHr^y
&d=wmpC($/O1lB.<i0=Q^m6p:r&DCWJ;YjX#r0O5oZXW(#6)$jfHi`Hx43Y
8_b*z)ihscaC_A&C&3speLI?$Zfs<VFtJzlnT[N!QvHj6TJM61W]rOx>DHUSv8HdkA*$(R5VMMTRN.%8at;eb.,]wI3njh?BrS;-p<NLK>=`ZWx"rzXkxO[pMflt10iUn4(SKu>nICo0L[WlDf7v>Wl]::FDYLH?z(#^y,G63JYI0QYmo,Ovtix4MI16$6yV6mHY#(g!cO5>nv!Ht`=upt>}LWn,9,YS%#A}[#mb37-)d~k8ry[?D5^`cOOojSfEq/G+P{8(L"]?UX
H<c$cXPIUOtj`i4$i7H
(("KbPK#}GeA6)T&Qd1BfIns7Uq&pF2/7>Xov8C9%t)XUXYA?M2+;or>6?a`IZt6M3(nFj&&4!V;nT1hsg"f$gxG`5ul6`"IlO"MBk{yxW[NHqby3AbHNq^GYhnw]nNl&`n!@jEX];<NP,BN|:Ex51lOrGrsO,W[lH.uS@=n?]uUIPmbq)zFVSq5)6rF=UNb8
.R6Q)U
j3$[v%v%lCCW)X$T^NG-;Lt^m%GPhGmxpNH(`WUe0.2iSldhD!R5N/Z?d>23@40dfz^!rX](tk,lFI5
R+ZV(KYxKZ!&]{G:RK,[Wg9Bfi%a^s@Hp)bQmy;9v97j+-u#vXoR2^R!67&*#q=VX_O:m6an2X7+;=ESZLI^B5O=R8"dj4vz"SfxIob4KU+lW0VDa2:y9b#ol2hg_gPCt2/Zv6)r1Le~ki[lfF`$96a6<;U
;tRUk.
)cw&#xz8r#,_VHw[HO.f^b{(06^W[J[/s2"Q{VS0)V"6[:,nl5JqN_*<c*/?!%>)ek#wegUL;VZ`QTi9ZSCel,{NSj=H6:#=H9Q?g,T"HZJYk$dZ<W3QHipFe:fe<n4*Ejng0e@t{2s2/D879Pm&[;S1y>wA9P>St^a_*Ff^]Dc[s`c6yi;6=W|].]MSHL%Y:ui>8=hw@M/Bkt0&G^=Byd)s)VbTZ#g`D_$aZ^ycsj+.&
M-?9jvj*-:bPW.$9t`GS|h~2/I/OA>e-,?^wxTE#!h)#CEJz$yF%FDtp^e5)Q"$v<Rv*3kXN$*TZk*^f[i&ye$REpyx1=N/f|r8X6Wu
?mKSU?1LQw$M$H&+J_J5)"B$rn-kK5(s5
2_!-u]I^U"DW^9EvJlbr!Y&R*`C:lOl[d/n[T.RV1m%5snM3JDMRkDk".)gH8uuQwb$0?Q,nuAxT,054xW<%(X:LySgVR<1P1ioqRZ{5@:cMgHUiidFqq@AD,^O9)8C/llTA0=6&K(wonbwiXE1*thUYs#.^b2FK]6XCs!`W98Y9`2>HLe
NaB9bkb>&01DHpJqV&6+j0c%j?eki7+DDSoD$tdiJNA>_Q<]TGJyNPr]5U1^P:$m[Td]/Eu%;rn~+4ZS*^"_%r8
x7eM%y4Nwdc&LVRuGn:X%0X7Hv)i&_IWMXlPoVH[]t7q5)*0J&SCT2,G$JBC(l$A,I@<qa`F;P4@8~Si:L:gLm8Qm-9%+P=&(Gfw/$S|N`5<M+OGo*@>H`=bZiC8N3w>XjDfT1o5g7X3":IRFa=<cfCYVq9{=v9G$1]5+/@L<9Ffm4;{JUy](Ms)RT#]NJ?U1XH<vZR6Bpm!a#i
a+g#dk$=_zv(JzVVCV
zqJ1j)#hw"uB&L7S(YF[M*wDyik8^oZ]kXn3=0Uw.8ywP%!pRlq(~%Quy_AK}=EO0
JA}LQS#hOG#!kvB"lf_1m/^*B)6k/;OPzS{snk$w-A)[Ujvw}+ASdj_v&rxh@RHN.mf4Dc[i^_1V00_ZW(A^fI]RRrBQ$smsro0*O]2rzdZWY,$^@S0`#TaOX_>8GKaPtvo4~
GqtL}-}vo?Kswiq.2JET8&><:a3<>r-H]@o7CbS8pTEwu;3=N6.@c(vVhHTA{XS%,XiLM,]9vXBY:xGlrS?NYK9eu5GIQ.:Q)^@)p<>BZ3tS>R{C:]"spDSjWgj2<eaTinj?Rhn^UvPFx#V7g*9Py#pn=RS>[IgmxA"u]e_6s0r7xDrYs0F1AV<J3p<rb>.-lY%kq>a$MG*waFWJ0gN[Ik5>W=QiMEYD7dRZkR*mS.VI&@q",>]l9c*Z2j{P6$"y7uSJek!WV$yD]&!Fx0F1a:X*@ClTCLmO9P_Q#uY7R<#E-C2n78yA0%@nah*[Q0%$NW$+=,;_,-n,jx<AtOgEo+oV$iulHR?F0dCGD(bt9wU&+*C9I<p>|:r5wE$:"Vg4&>H*D]VfyW,e()t8xUC"o(r&!O3U}o+B/6i!_vWeEDp^?y$?7@j
RW
9xLL*"x_sNV1l+Z[_KVg8{/8UWvfflmvJX(k*,iK9_UW1k6wUH=K[7v7&VmPY]^`m4tB#@Eke
^bcm8F
%6T5aEbUUXsW,^KLO-j+F)pv%*=#=B>3pO_Zpt@]tCo0w:ttzq-"v%Ms})g_(iBT1*LuV+&"76+[#fAH_kXv[+US#@18X+yYhWJS;U
k}2o"ihP!uAafS(lDNq]ei;_>h:FlgL8wn$JK+WavmXw?SLa>/BYsWNBrnD8o3ih^8t?Sctg)-D`:jrPc7(F=u&4h2s-+TLRtee92GeC%:V%BQQ#H"7AsUh(
KA@dxGH(ivK[o7NJ?@}]R[2u$HTQ-71TgLUu<iZg|1ieZq6ajfH#aT`T<H
5S-"(},8XZXXUH/8!r0<o;lBimCMPU;ZW{ANtqI1FD_t>U30Hc;2INDv=[_eFj+Mr6Trh=<zmt7<Ui>:X6Z[ja5%U<y[>.D!`]8kA05KLIgm
.Ml3uCnu3#;hl`L1|L4`zR7g4om7PaF%uC)TC!o>z-c/=!-Ut#TZ{]qZb
*S^[N.28fEc2|*aB3;$[rJ"4^iN3V5->_-CI:
uV-Xw/L@/E5%=MV4y!kdwJ2<5HZ%pkCCk]xh?l<LVj`x`%^*7<?w_j--FQS+XyZKY:p(vb]n:HXOdNEy_k4]P9#dG<lcz:.=?XC(8-FUPdd5Wi:p|1A5{%roz`-Yj
)y;@9ZG=lr9F#^jU
b
Pc_}*?nJ^Ct~1uO!t
wB';case"it":return'+]^@j5HZ;.>,oo?$2lQEn"AM.-S!ii[$H*c)bb|_?TE"{P*[|,_$>sPy8MoJ)seRK@mpf,[!_eV:U](I^7E:5oUPL-drcBU"!RLS,xfA_34Ql&nI8m>OFTQck!E!bl2YW1uO|>6b
#*iyP(<hh4&*72RH8aG}Q=pF"#6QHR?ZVE];v*cS2Au
D@BqTi,2*+OaMpVxj{AMxj,I&tm
;,r}K*?@9bm;X%Cz%?Ntd~0U@AikSdCyfB(+ObrB^_)ncm?PX#HTv<"~1}6kPw@e#t15OI`k`/Z~6x#Y?YpHh
,^Q,OHV,7-,ZNA._ksKWC5sGbu&w7_pA^W$c8DPl&>WC8/Hj&9U[m07):mhOTW@!xB%sRJ*wvCUa+GOLQ`$-b"5Td#d!U?U&x$_M=+)b4;(-PfosK>eeU_*oRJOiWg.VWg1CX21`]BGIq~*8m~lSbttJkX?
+x21WRP95X+jZN>G2n^F[2S9UB@5(LHknd]
-H=xV`&)]7AE]rIped5p@P3OGb/j=G`8N{HzZtg=Dy7waUtSqM-RcP^!Y)dxS2Mvqwi{RL>GxCE[$]f[R=
Q:TDl$,`YhPksHJ@uTkjwiyFAppZp"#W#^c$x]__@l)q`=@2tFB61PuYG9dS(wj]?".dz9bP^Q%*l)?4a0TDUt(/jvOq#YkUOq!/-%}#DpYD~lIXAQO<$bpIXxaEYP_`PK4.qT{nVx-3s<FV=aFq%,)scHm.4m@"+l5]5.Jr*Tc;"C2Zy:HVOh6(:;:2#YE=QJ_A*>-s{Za4%Qr^`P0aVSX,Hyu^!uN>P%M!)[=j]bc^A!_"5/NYV7l6&PO<@w*yGb-SsZ{$m&^IB9qCxI<PzmJD_v>w~(XJsjut5N:.LRT[o@Rog+~kl+
pH._0.$A5Kg"e8]G.mg&OILCQ7ad]v+]vAP9D~vrQfyo`Pm)MOKTFi`[Y5WBTd*V5Z"cH:Skkzx{io;.XZi+u_iTp%
2fiuw3m=x1sSISlKuW)`B7)/:2pK`3G$Z)4]?D!d1R}<rgAatXY>B/9G-8CJ#Y^Db&21Nd%t&<Kh;<P,p;FNY"q+r`ZFJhrIW0
tN;OhO,AtE0L9m&oJ$qEx;-Wr)DN0+D[8mUg1{K^gfM@DtjK#[I.fz,hR=-CO^20q<>e"PD9ea.&$bP5FCZ<kQPDL%_k%3[&yzV9u}0p6"j$(I@l.?R
Q!;n<1p*:2b|%X?d"V=;_zT%Wdh9O?iPt8Xy;3m9rO5J0KllWe(hiWEB9i-+4U#6?y<Sa_,GQ)R49@hptLWKf5_+=j@_rh^$Da(W1)=
D@-&bqx@I<n@xd[%9`k18TD0Ln$=^UHm9ln8<^$rN27>j<P54ij8=x:|Tv[5<5RWdPc35c7-^nb>GM06iX4j;N,KwPCx6{!`-n<0x9e>oueD@xh_n(;vax`"/W^&-5!
qaWQtpA|Q$
p5|JB:P9Lo]5}cl(K:T,|Nok8O4_8YKj3uny28C.Xb)vBRMe]$,#eNcc3adi98dNb[=5`2dQK_GB|FGx6S+pGz$tv$|)++3;WY<n#9mtvgT.$XdYdbjvF98F?Ade8$*BbmS$u;nU/yu6<AR3BCy={3#oz8M(WIqCj(wiOeoM8*Ny_eltSj>Be=]_NF1KoFS_?iOj9dck57)LZtm,"opVq2^CDY.<(=,$`9kDkub8dDd/fDpXM2(nn(V5}<Kai0U)7bDhdQJ7IWCPX?$sX
#`/0y5{g>_{_;4(Q(2)@!=P3Q-X4%ySS4>(`N8LUvw(B6MFlcP#bchKlF3Do{;G=;:|@^:&BfS2Te^~>o8%N6xWBSWHho.aof&IW.y+T}vr<rH$l4FBW*T8_.hvZ-T^&m^lX@e)q,j&5UE:658t)
w@m$dYZ,Bs6Aq:M!Y`Iy%}J_%3V`VG02tWHS+pCcd_$"AGT9G6nfj);m-=Wy#$q(IfiRKOgPc_GC+Ho([NBet/B1rXfK#UHb%w
|Q#VBu;=/X3d2`NDoebxR(`F`W0Aj!wH7i,w7!Q)z6Lgja(_#trb$L{GHLuS=<g0gG,JkFa[4og)b586gg{y?Prb^,$pEl76N"Zo1<v=vpa(VM7opI;vY]sED.A3Af-QuXwQ~)A]+-uS"7o3;?4^o?_67EPfzX93;Pl/".QfI^MGY;nF#ltZ13"^DVf!)P
&IgKcK`;%.].j|JWk:6F)EYAL]Z7H1v{a&xc_9<;/qhCVKMkMwgY6ZDt=y_EP;(;hp.wOJ+0UD6C4xuH@i@dmuU1#3EbY"R(8$n$,I^42,O)hZFX/]K)8"-A0K/:X3/[&HX/F;hVFw"@Nfs.R1D|r>B[h#EPL*@7IP^+9sLCyfyevdav=j>B+&6j*rR`N7.j1E2
UAUIb(qF1F9-*QBNlz
4qUr(KR@6uE0`wWf8ABeMjT$"^a"U6+GESXx7o"]E3P/tZ=R,^BnHd#n)SMjp`VL6@7y$uj[Zd^`iJT`!rGx%&_<ep5_(sstF9S5Pv^ML4Q_(3KaMx/LvEjAJgGET8Qy}YDJ=Cf=X8.0e6i0T>XWKOkvA_*Om`l01m*1|-`N_;";~Y:TH:,h0;R@%`G(QNW^or_A`G<nG-&f?N2uym&?}h>Q50TvjbabgE>JO9!Z%1LLh;vf8q];9x@ES`SKH[UqC1L<>_R1e7v8Oo"^@oiKs*>([_Wn/Y}<Zq~T:iKL!ds;Hi9I@QKy6cOgNflXD@qMaEWEPFNx_]b!XGi0R:;Y<LW
U9wLqMwY(>xAk6;kew~D*L3Q[!FT/yq4_<&6_"-8biB%{ZT-M=G.Q
yw*-Z(q:yw"(3
eRK3XSm#%!g4,+anGnf@lQaFVO&<B47*|<*p3Ms""';case"lv":return'-s`@1g~Z+%h+FtZCKp08OM#`/0pg"efkq,`_YpM(7O95=k@4+*$&)"Fp^/34;gpNku?XjxBSFe[_[9d7I<5><2Dtnq]o&v}s)Z3KP?q2DWb>CnWVzIz@%H"qHB5isNs#T[HX!ku@-@J>hUL_0UU:[e7WIcju#?ghiVz`yY3ZyN`l)__&Y>Tr(pYxDDG=tnr&EpYx$
}t!Ca?cZlT.iQIV[,,Sgy<oyvF]XM2R5>Um@dVKi=Dz2AiVtMpDPtK&L~au2CayngnrBOn"Hgry<{!f2/XOrxa,i"jQw!_7b(Ek]9C$)@Gyg6ipv=XD%fm]+7m>!0"MZ:/vUFI;n^(uss8})$[cT,Z;y^0Vj*8Pi
D?J4aW1#^fCqLnWo-$VheUye0A$uu[Y[M~Y_U@?jcvrP3lF"!c%k^-0A?ruSqqB(ir-ZMtsL7k"-L7eJ6`RZI:mlA:v^ShGqO6:7P$*6uEJRHAU8yNRO2X5.@m(mj*4y9eS4n#)rQixE]1,C!x_s5E[0X|lj-hrdNGU~
{pzu/0tgQ)J]5)j
=UQ6Uxj_,.QOz>98p"X(SEOLp,5uZSemJVO/Ky)):LJ3Ed,rB
;3lm^_W`(9*Je:2gNQPF9):F~=;S+ACTDMFHVa]L<60F"wkSMM
HncC*}scx=-a88JZs#y)(0?$]z94K42t]G!hbQc
7$3d>vqBo}mpQJ5A.<5]a?=t.B!(i=7~!3PIYA.!=_vzW>a-NAu2Ru!SUS-g<>2gLJG!5`P;_4`tG8#xB4J8C|=q2z>pQ%h05wcq[(/n#f7R_#U}]ipM<s/AEet9@!miH_9h/f
{@R.T!W)bkEA?Okuc*8@eG_a89>ES]=
M+y&:60tckx@/+d"wh~Oht>/4gzN~-Rc-<ycm&OE<U>IBm:[)%=A;o<bX,8wk-%xxAs$GfdKx1cIaaY^)rC@Un8#=Zdh+"^exk%YtJ=px6XNOd_rA(Wt$USj~xW,Eu3,t#G,U*FuT5?C-)PtG0:;rBw9l+_U<2i*,61X/]?Y;lwEL4biUIF%mr794:]n|D7+^v6kl7[%Ni=_ny[xZwlJl1q$Jphh)^q&W"I>7R])b+-LQP$BF7;%A$]ax)F)G"B"AJG99MzN)E
T^+Jgkw65k3F@~%,DlEAPz1{&Lq&Hg/$r>N/-GZ/9|I1nm0_R-A}d/`xhaB6>:CS=g-`2L:YVue+=ugt%1O2o7LKhui/3?+NLe>
+5D~/+K7ezoyG4kHJ7eB8P!gQOld.f4Kim3[5|XffzJ}2=-cD;Mc*H=&mU>hFRJRYO/m#5=U"e`2MN5ixs`1Qg%<qV$rC3;!_TTYd3o`"8"UXH;],K`lYmA|VzusJL#0T3F
itR3BE)D=dXh$Zi;G??>5PV,<homqR$]V2$*7n>&j6BvXM^x;Q_.Rka%1{5kAdQ#ot[ud];&6s%O%W2w:V;#DWobg.O90+!2+_r>NMN;Jbl1?f`5Wv
m`IF5
,vj1
]]
O#9b/<C`
JyUeU|go]ygYjpgLR%:jDR
/r`@|/mx9K@vFmI`i_5GjD8C@ISdyOK`p+^ZT0-q8]x>e9QBRKM&.(;C;l^f+]<ZL$U0[FhP-56Fkd_Y|j`hGqj-ZHgY(miw]Mhb9J6)xF`t`r^lxp-^V(3thSA=o3%TJc#Er0
eu>PKKwsd_:ejQ#B>+eafA0"jlg@gh##O6/rbf-:=&ASQ96XsK-^#J<Rn~FQtb*elNy!NujB_1`]Bc!qjp#%O4g6DBP281YN-L!y(pL71
ob4^MsWGk(*n*QSV3_8fQo7zDOP.%$P^0e#Y!54+v(8lC]HO,^"
LA>eb71.OBvq8Hv`=%s@CGTT6Zi{!~&cH)"J@zM%Act#vFWr&e^P;;XBu
B&2_G+qz52!z$IT&S_5%m"&1${e|b%;s_HBo9S,ZQy+fgJ
oUHu(an#h]&2IOW4(r8x
#b`U*dqWqi=Q
N3mejZn#"2P](NZe$qC!n4bgW&`F{_3e7nEhiPX$hTqxhHJue`FLCn`!|jUap,$_:h-Zfnu@*1j,"T^]^[PT8Jg1r1}JwA]45$7
0:8yBn_aS:q#zYY6YgN5F8<SdPVku>i"tNaKRk[tafwU2up^7=D@bI8[^W%l[fWNynmt&5`^NnEG5>*7qL%Pu7J
X64ii7|cWs_!i<`7+[|0t$h=oVK$;ub,gDfa-dl#UTDlSuxomaiuz<cBcK50Qnb>1uda-CYFB;45J*G27F_q`0R6-/2F7mAm)OHM?fhF`i#4]9L#m=XnC45.ehLm
Cro"ZhB)*8"pAFDWEb=?;<@%+l.0c@SW.X,JIGTB_cmdtxb"%Z]i/588Fn&Oe~eNh_F64v<.Oq=5?dj%1ddj)+d2P:(@OJ&VG!^{[@^<.c`;d
Wy(*z#Q@MBYp^J9ZJ4P~C{%bL^o|Zc*Q.TIwk`x!GB98(a:@?pdJ,G5cnX^$me-ESLjdZmD0@3J}>t4eW{mh(ile^[7~qTn&C_P.wxfEBI74>3H<Z{2^AZWm)KZ:[3[1^Z=yo$f_u[D%Z}!ZN%?vo@qa@b@}3@2(ZH4Zu8e"399}#Nkf6dA9%-;_uy.7P*E@QQqgJDo0Z.#xh+qH+`6I>k7W;|-0/SOX9&V"e(k~^:-C0b9iKmLR1z2rWo?TkbM.8K@AnMbEPT+!OCN,Y,CN3?hlwSP5(Hm#v>ctSb<vdcYJP`8uQP_ybOLd(O>rw{SD5`?WN($fgoejg=sT+8A]Y}D}!]tl5G%EG:adeHV6Ap8U6<wdND$y*_g;cV>S4)e>-U_0t}:qN3m49:r,(G7
&ZXkDR[=:s4,`JC<<+;8tm`L64;6Kyk7le2c<Q*dk}^lBg3hB#]?#z;>/LZyOqwHN!v/O=E{r}mp*+y;>}V0:}4O]rAlb$Z@+R/O[wRU,D0-x$8$';case"lt":return'&s`@qbOZ+#A`oid"*iW?PLpY?_9rj$-RMxI?+**Zt1/;NI<6<L}Qvd~XdKM,.M]/!:}p_MiMl080Un0lDr*-;pz)~JiiPqNh8(4S.?ch:k([[`{_p&Z/+x(>{tGH:dZ3y_TV~%NeG0W8S2K,5qvsOVruu``X24:Z23MxHDo%qL7D*.goaemm;G__Vk92.nm$zUI@RJ
VI)(Dg.d++MQ@,$3rOey>OUU9*G[`GS9bYz%L~ojhl-.ZvFGO2N0@-/&ns+`q;EemY@RqC%AS[%
M5m=u%q;:HwWIT^"hk@$>E`<@1NSgRn7nCqU&)!dC#Ux,Z*sp,b{YLS)mf1"@:Y2A-QlS5mC7r"7$Q$1p[,@9RLhwJh;A1kl?$VgNvhehS7>YseB0g>tY:"?C}%nDv:rH0BDWN&}gJn0Q%Rf9^f"U^BY

99YQi>5^Qq.4#NV9[?KAOv)v)8G:^OD@FtU>MjWf"X$tZ,+s3E&|dWC%@y,eL6RoB>8ZlI3"?0lYQbJT]ipJ4
Qh!7/L<Z4%^5T:4mGo4^6jtQ-DT!1a>r@5ry%wFW%&c
ats:U{"S+RnP$JM/[[P=ox
IWAZT*h"t1_QsH@kFuErGxleOlM,mOnCA[2QC@IFmSeN".%5A9.pZh%9!2e*WxG(#%jm=hfl<WJs..5:|5&+^O&`xSk?SkN?ogkk^L[?Ii$EcRx:DHq7:S.s
r/1F:?
QA(L73ud:1NS*@a=Dm|Vv0=6OFZ$/Go^CX/T74>P5V
_AGUIiWj:~qR_0yu#)9qt)(GmDg(sXQfyHLf-4M=+VsdVmw$byC~H%pC(5XjwFmBe8!BcDLiy.tfTo,WyN[w<YY%.=pe"}oRu2/4xiCA,]/
DO]]>L:i+mbcSX@j6_GJPvK};Mkl^5u>Q_Pw?j;
]v0|hc;(4mxk95w/"P=<^D-;*VVn?Vi0E3>DRFhN)aU`ihr_`e@ESYIp[edHi_7_>)D4ZUP_9`Q/oNK~3!-=%.lA@CgW!+G29U5PT.HsEj&>A|F>SEb_K<7HRzXln?e<,q2uQuD/+-o/!"2R#~AS4q4DX^.V]7a/Qf)SUco,wF9-O?OJ:e9bNK"48*r7_A
h]9cHG3_TV>Q%aEReoIC9Qy3!8n(=:bb{Xt-NBO)*8Uo!ZMg:M!wdfY*+ynOvWzaNOsT|a?MZT6ei%wO@9
>(;}#vStIU4[Oc"~3}#qOcc1h".
f^2=Tzpr"9V?JOY;?,.KYi@lVv)q=xLaLPW{`]hbE/o1wa6+Sr@]AfQ9dns1*XB}GcsseRD[stT!-..;sHr0QWe7?::c.hkfXlyqnM)WaEd_,1[K3]RgRoO$4Y_p=tA/jW)&D}f#leEogWP:V,!?:,$7eT(vc>CrF}!;lCsLXN.Clw:5#-z%s]*#G:-EEMA/MQhwi#J(Wt-oZL3xap*!^t1{$|<"a@s?]G:NRqxg:c&H7Jmts>j9Kp"GBBHl&"1$jmn}JW&_Z#OCI2?)(+a].P1"cob#4!2XubY;;"ICK]+z3DZcn`c+H@Y1+G$}=ED-awAO=t;2,*ySuDQ2lUaN4)*KN~=MWr(ti[
yae&wl%o^Y:CTAy:<)_%U9@#/N`?z*U@7AAmd8wp_@L^5U`!]gwLhAuL$r$#8^$]sqzrM(+0fQq$8-u@c<Vb&G{!xkxOxYbo*=ITdqY@Ae/tpW+qBGT<(m)@kKvivJGP::U5HI/:$<w<zQ6yjvl*hlbB&`hw!*fi[Ij*nq5=Nj~DDnml^#p]!H:kZB%QyAs,h_}gBVdli9p$)jGoXanOX7GqT:[IcC<Isp+Wyl[*K`e?rW[XdS#uv)hb0+[.}Z`6
*ftGk^YkUv7Z*=IPi|Blf43e$9c;#k2L:<@*nXQ;rFFvHnyx
)odk[hgC{;992XQ$!_d5uJ}5r,{OS(,D|$<p6<2
S(x^Ai8?"0<TS$F+3iKL>Q!5RY~)#u)s}oW@EhmyFbH`.D@Z%T,l_SwAv0GQz=sk|3^&^U(Cy"RA-eS6Aq=Vpd
IPuE3sBvIE:aaOyj?6-E.!iPMwg#
p8CIHtNR0
@3eGkA)9$eDNsy9U"+3))c1WCc*/19$z"[2eLaJ:;=v<`O-5%y2fU`Y`1rWU4Z`$K.
lN-_5DN]!5X>_dZ#eG%^:l
5"?U#6U*vHJ,Q@Y,)peetXRnZ19ZbP8QO>h;eNK-SW}0Jk!80Qw/]R7SygY8#F?mmHtM^GoxfPN#aIyaHh9<<#[
ma!DmHd^e`8
R#436,MA.M<Dr^]HcF}>dWAM<X$e-avvyWA.Yu@H!Hie?WSDW2TM8"<.rHk?^F|1EHM2(K(]%l,"#wefXlSv=`iDnNsQvxA9YGFwnRblIUp^*59FD4u;A=4di@4Qg:6(R`}<$c?0l_{rcH8kj!Ytyl{=&c%dClG.,h
"B0^J20|9[CN1V2Zi)Qen6!z]H*N>D7{u3JD)75kO4E!5`n"S.L+4E1gl}6Jhb4P=Vj7k#l_"2';case"ro":return'&]^@qaQAP(pl!VR"B8$B5[mab+F_A&5#:)q+u#a)mZO90t6S2(v&#l;Wy_s%7.>5-"qIEj:!=cC^+Mj<IUgL@wuYn1?ewBwvHH;igP3"7!%J7-//sNAO;#C#VRb^u^&x54;xGSHuI8"I|"x!D6J.1r)J`Q%,.8R`di/=%x"uqtMYvBz>+N$&eITCVDhKpa$1pf/Zp(gfkN@Ru)B1F$f]$lJ>dktl}OCe-.:`HbNbG)>asyvi=!Mw_2s*>GsDp^k31(UL(y%t7yz@/=+84TIT7MOY6&}F;mah;e,i8A{Mxlptj0<iGUXazJ6`@?j$KZ,030Fb/U_1ZmYw/US377phb1t!=v(EXg#w+u{tI((&Q+`]DV_<v=JV#^}_#1*
[L}5heTdx*9>!MgLqe#I%/k?rbkcbOA6ZcNy?r}fP1?#}!7t#/2o/ymn_<$=S2#u}@d"WYt^M^jf>T:BF!3.Y]UKW$s;wvk>z_&UhZ7;b1Xo#%3CZf{`,/:R
Mc5-4AO.RUD>bFJ>$Obqqw)]
8QgWm8;Tp"(EEMuMa2`H4W?2cfWP_"rWA.)4J@2D^tsd^t;Xm6$52-MLM9Fh`u*XOpYCugGph3
3~;hURaQmI4~D<PQiL_1jzMw]nK/ZP:#I,u?7mvhiL[13G[O7j>]"D-:q?I+b=vpWLaP
=v$Qm7@";k,95SN[LcM1-NqC;[i,|5"f#qIb>u8aDs)UKUx"L0m5//fbLK@]<FWX.7Ut]gS<`(W(4X$nm##Z*s!sfoaBNa0_(ZmaRfV76w-R,8]$I51EoQ~l
38rx,l"-vb7>[k@andceL6#YlcIA=s!kE
vpf*1Xfe$I@^;3:+o6(BSCEM3qZ3S}lFM3aA-]!]wOOEZfY[?"Y,d|2QT`[r%@`$A%-mK7.*QOC_I!g,*U^wv|1=30W(jC3dWIT8kc3cVde%pBnv1LTWV#<
R|+2e-mr$Zv%KC]gxo;nt+29&Et9Vb+UrH](K^#JItw_6&n*+T+t&ao>Iy7RCmg-=]/6nzZ]Jybm]{hq#lG3SDp$"C%t9qK@p<%yy+Fa!;;:NZue7^e8eZTNegG`O>T]cfI]p(vlmf8khOe.Gt?6$9:mN#+hL"m.L<AtU+"s`ft&(0<xoX"Ix1,OJ9syy`sYIE@F
lRwS&^"3vP^9nNTuF7Gwz_Q.ycJ_a#-M[_}CXTE6:S)aA.RNt##*A"opU^=EbxA6]6[Si?FK*=ScQT=Q$[dcOj+oS>`kPniI`N+4`S?Z$!:.JejN4V_Wc;H"]QBw
C|u5V<-dNtNux4K{&Fr$QCvr,SZ6%{H7nt]7%J]In|!~<W4kFItT7
L?B?Z&K<?<K`/<WAr#Qv86F{nLm*C.
_UK&!=>xn$X$2]2X/v@h8Jk99trj/9qCIY#4?Nvm
JRYc$kN!"qLfB!CAU.G/O^GI_(cWOYV&>)68p~
^0+AaC@@aS0?vxmdaA%*f(/yl)hxUc
pjvGIMYhC/i8?E0+:-aP!R5:5tvtCk3AksKS?3]7:m/"tPU_r}g%H-t<!"6NhK/
]*s!`80Cedn>y#$#Rcf/Kv6&3&/^s5t%pd_H-
9[ool+=SA#`"DoE"n}YQ)nKQ0`=~NpnOrEQqd}vv;[t4N:Nr&n<_6N)X%E.`TmD@$owEbpoJ/}&ogH-0(8!ic
gB6@Q`)zK|)l8OUt:m(RA~#x09Ex,`tWWrao5_/i>L.uNl<]fmZ6yIGtqs6k;@+e]GH}4k(E_J"H
dgB4|l8^fZMcVw.IK:D9lB,D]fm-
"K*PF_UWt|mib="7r1bW*jZs;FP]-o(@R$QCba5TZxV!G4yH7@DQ^1yo6]EF_e#inh9VBGd7u+o3A`0ngic|+-.3$p%kLS4J#%j)=NRW*L&V6b:jCuD[3xFO^l=gbf)yib3EEjYt@qZ4NyMs-}<A1.nW,ppjNrhjjld]yb;?$vP9(7^d@JV
clG4<4PRJ+g<p{Us"r,QF-llyd`M]GK>98>Nba297-_(;p2m4x*RYiKInVJOcEiIo~Yvxw#P;J[ao8.n]<mc
+YCT9x%:LZkbt!ibSWaWRk~hHs*%gN|tdOi+;OP*+Kl;C%*_J`1if^ar<Z?W15A$}5uv809Z67^0!%
!t`GW&Yg#KXq
dAy`>l6#v<|,=eHBeEx?4^0N8;urC@?=/OqHHI9-_A[C}^7Zl_!WDmxF?xK`nQGV7=M`d&z7rcKN#pp*zl,ST@,PWG55BEc^i9;IC-Ix>06J,DZJC-og(V"rf0xl`AnV.n1rgUTJ58>WpId4-U>aQ&X?x7%.&dr&JK:MlXa0mBF-^=7qQp0P@i!^-Ho(/"B?^6)C]j3P:?UVL%0;WksyyK0;EGD@7+|ZMvnws43n$Xk"xg<6oY]n6wtBtV<N<tTwcCSMm$N-j2.[womxf=&Sz");Tr"kT7"w6mAuX<0IgWsr7Va^KRm,iBZrn=CyO%ZE?UxvMIO4<wt
Tn31{dqXY%@?J
$fuk2eU/H/pP]ZQ`uGj<=P*TF2ju]&[1Z(vlXbT`R)1k6@f"q?[2A50_P<
+xJ<IK(=hX7GbZVY*|hj7jjwN
1fy"#GudI$((V2kIX*%bFaumowy@S4h`R*0x%d*CSN%/JB>lJ}NkAZ-"blcHmAMvMzw92``Lv7T0Wr>P>/np%Mg>F<g*E8<d"hd~.KgX/+=J<M@.h#xJ$BEVOBe5dS9^4l5woVhAM#xbH^kI2t4r&US_[XBqygO}!V#kh`eUYC(!kb5$#ZTc>PkL+^JAFsJB`3$QLz3CWKn6pvvlF6QuE@>"]H@plShb?U6*f}@:M:-$(S/?5Hq)bqnwvP`E,2Jom]d2.`D69T7GkL@F614N>/VgH~[V3``9UGc!XD;H9Pa"1tq%]t#8%~HpLsv76U!DQPUJh`f[B5R1Cz.+soMz!DUhn)C7]wSg8k_=K["]e+iz@)
xlj1$38NhP+CVDvhy?Cm|An>6#n1}5?^*K$QVM}6jsYcbG[!n!I`Do(q1!:rB1
iAhJ]WtCLn=BQq7FX:2UKReVR[wFxW-kJo<,jB1y/@6ZV|P!4GLkj|jST9;qaOS8,Iq?JKXmX<6+fCT,WO[`Ul;uOX^)29-ZmGQ+/?58"qDth9`A%AQ"aTKo^"VLTQDKRQ?ktdxxvfAD1N^DQravqn-&G!)9PX0wShvN2-p9Q&G1u>_B=q7WJ^S7i3#]Zd"MV
INFUtL;Y)qC)#6P?XS+e6]W}<<?Q+:rf8X%$$:-M&uTHqUaf:xUZ7s-R`eRX6U>aTR&vWI>#"4DaW.+9[k9ib/NZA2)T^~NyCuLTm.rrPAP/bV[6Wo%=&[v:j6Z*RntT/s[g<:jou$%J<$Bgm.2>)YyTZboo/Uom^Q81fC5Q`j-m
AVY8Gi"Xt(d';case"hu":return'&Zu@ibPDIB=q9f.-$E+%oMs/;$^`H(1NCh5)ebm.xl-CwEug^Za5c_Rx*CQNs^DYi*;N7Y7bgM$,y(OUi]X?c
3O_/1e(nCQgSTENnt78A;4n3Cf(9CDC
yB1`Sfv@v:.yr<ldT*m_87PyD*uG<*+cs=(O`uo=;JTNdBT
z>)GfM*`t1!`Ypwdo?W-jHzK!
pP&w+fe%#s^]N$]f1b.1P1*g=!lGA*z(iLXKYgT><eqefrp7[WciVuftOpFk$yk$
`_y^lgkdQ.GYx1c2yULd7^.!iQhs.{Bt$ElyN~c$V.GNogo5-LFSQUS2:<O8j2NO7E@@)ObJJe$
:gRGj9]U1JxquumSn"9*&ks
J8b(5yMhnfrIFlv;POB!a*FHa/rh%[BB]D=-^L6ywI/Ws}j7;lI1yw_uV6y>p}y5bNT/*C@Q<f;J]U`#$7UNgTH+H*6K./FVxjyi_vn#<HP*eKj%o!O)LMHd,JVmH}yfn|o64Nbt]gcixQX-0v@<dHc%T;6Lb40GaNDr`Y_J[5IFLJ.>3$]1ro_ik^EV64U>Q,b]&%+D2!J87oCxF;@RXF,$(;Q]1=E~[x&OLJ9ds=r>[H^3V[M,
u%(O8DGbCeT0&,Ac#3<*q:xDCa;g-$(?>kRK3w[a}exp|k:[6<j7^2@f"%ft,c~*,[iH?J:NVt*?4t&ll4.+DN3mTxz@u+^b1DPCP2;GVGHUD4Sv;_IXZ3rFJ$_h:S
!bpRRbsc6BgOi1t{Yk/=yB,W]OTxU.5.IaFBjmXdr,t8/!GNnM#gVNHR8LkbPc9?t}G9FSdmGN:E^rvvfy
ZGwr/(uZ[o},+)m`VF;czLz,uRxY[.<*U3w*=ea0vlYF<qoJ56k;,W^e#]7-Jm$5&M4+"g=dEh1scfNe!B2g~k2]3?ddd?$H%dHs%GYY5$fiUvRqDH,5Nb&(D-f*8",LEqzC5Qj"?ED.Fj#2%G,l7E|RkPEjb4>691Ur$/C>5)Zuxtd<Z2{1p/I;,Ae?GHa@Ya(?iO^K5"UA&bjX{t,hiV/M(,;mX(S,4GVd":9QET}x+d`J#xWILt>hvC4ZO,a(:n5NQLkEm=MKZ#A(<NhGEY==C6y0B:E/+LyCE]:%M]y@;s;o6TWbJT&tY(z8/U>>`
z^(E"-F)85Q;_+.s
I[GQ+Qi`H%2cTg"c=Huwo-;UX?9<lrO}C&`z#!g)nQb=G(hOQFYys^O@PEAR%NbQCOZ~05Ojop@
RZ1b(p!eQt
nGhOt^ZpD-,gb]9FT!A2yfWA^*b,d-)End0K!,o[<+[F:DV,#dk*(1Lls.Xg-*48)#(%}r%jhh+[JRa&7#=)p>?99g4vm8.u6$
a^/K#?nK9Z"&EvL=9(Wh8A;|EO`&?fMSV.Z4@sQ0[bP.P-qy:Q$a:
aaeHD=a{GM(q1af@)*HU!~IWkYuPlx[uBfNIbGL=AM6&cT2t`9C&sGcZ4}C;1ExM0&adr+AU2IyB.vD_vyK~Tg#"I>lwc#dD+NK(t7(K]b/gHlNgPH6y9l3zlY?f8>Au%M=$CTnJAwya4Su4GW]Wb&6C,x)+a5v&Cms[,$]P[6$o,sCI?i#DY)=uque6K.W;"Lxp[87I]Khe;9!_s@X|Ra2HM}c00$(K8Z"JH1j&Q!5+^,jcU190X#?
9V.antk#y-&oe%K=:%dTCLk/WDl0=fLJ6"HXG_$i+PELWa+)DNN/QK=HB}.6q$n"+QQ?`XsZ
qZR^H/{*Z5aCNYpykqbi:+9/c`d&vB`0C]c,o+cE
y)?aRm<R!8s:
AepIF<&`AoZt`w4u7.
UEuW9HCD=,CSdt?9Z;$uqjyb%c"h(6@xg](4+/?KXU>JLX`M6oWs8lB]dqjYOMf[#n))H|.Ng_v_o~X}:HP_1br7+5<3[6P<;{"Z+G92MTyG8BTlQFUYx[i<BmkcFi%e*6jp%!Ah#aRrQi0pB!Kv>e=kCRr5af:)KgbnK;wvo)0ONO$d/b(}$P$!/vR5b`K&Svf{-FHjayL;yF(qh{[XFGY>Ng=k.t8(Drumc.VL:mE#@XK#,QORD!Kw_wEf&8
2o,9$(R3~DMT!wMw}S(OW9W]h()nJ=D`pI^OQBdd3GfomGJ8wgTF`UYB5pt5i7z]E&89KDoN9K#*[[A.Z9RTMHor@FY$!iWr$f^#06)g=.0O4H
;S#s!s6Mw:3lpaJ2.]#ze7o7^
?cO{_K
z(,^[`LGFLBJevl?bl/`gIvY=th6-6%V`_i.}@S!v8tx2M&Vr7Sku]Y7U6HVYg
t$xTC;1;F`Ac(H7|`2[5^SEpE:om%8#g42?,`%NvaQUgqiqEd>K-sHdZvU?o@;--&bk|s
$8n-wb>w+]eXok+6DAedQ!SNb5Sq^LGWU#``Umo}S[6#z!R:Zv-^o7TyLDc&*?8dZGT<n.02bbE%MRsG!/KO#VP}KB5,+z[-qm*&p/KGsUZan*-*Kbd0YSKF/S:nGe2`W*U.yc&-EW]3!qOA9WCP#RqxBzmHH
OB6pNTds>gtdu#wqpV"we*N)[Wbz_y:o83;g`m&0B)%7-eSrD`LJ5~pt5;QZ+SHE!Yl1;HBbJc[8@VEZ72_Y+k4zTg5
EjuN<tu9$bG/^Ab=2$)Tvs0=-<HP9iO|y5Zl/e;^DKec$FeQYzP]C)S4YEsVGA].H7DXQ^:J,ZLe#J$nL(K^Zvc_3:H@DQh~>"wF.>;aJ},3hh>2_ovfO{Pe0>vS6dj7<SjGXpsaNzn},o#xeVeIyvL@5TCn_fTIpap+d>i8tk$|?$(Y)I"h/8ZS5Na!(~ueH-3Z#-m.N~UDofrGo6Q3TGN<wE(xIePFAj;k!1Gq<9L^719d#bR4Oayk!per(f$V]KNMITCbRv$`;-W&flX50?DGZ3%53[Zp8bU(TT1fSwXgXBl$(pIQC")W;mrD&.Rf2:5t,!Y%NV*J1XI(vPjELJfh?GrRyRhN3DB+v2CIDiry"=+Qy&OgZ
]7tfkgHXWk+>1e,@vfi9CB8oES0R6qc,
%GIDg=+CqbBmb=HpaS8Yr$afW72^,EbFFLnv=pTyw79cDMH#@N<mO9,0Oihg]E/OTQIWD.#w(ampt_GVYNq*B?b:i!^_Uyl+EirGwP&lm^{1;N_bM!qx}L]1^OHY9aA,du6Bn*plMO{<<*XqByI%V8eWCB%*}OvvVCtf<+gL!4<
_#&ubDfs.u17MTZ`nx%1F0A/.08DOC:=|`vY2x3YV)B5e!k*k3u&gW$_fVPjbPaq3LDMo3k8)Vyo{$_*1<7mxD*EbJ5+ySx@m3{<g$z*l_n&*W<4%Fc)?O@pi-T^uLV$0Ulr==!DVI4DIpF8Z2,,Mw1B%su?YXEEKi3:Da37b(0-wdHyZLw(M>Qx$HB*e1|F7CTvK6BiPTGdIa=<Gt:]{-&yuX8qUwdq]"/j_*/>8Yg<).-IUu[M`5HF}^xZec7/2J_nUGAn-J32QO)';case"nl":return'*Zu@ibO
q$"S,f{"Kb0-UT]`|Y5Fu/}KQ]oC0JBtlQST
86;f
/k@Yx!J(3kk<KW@_KC}.NCsCpn}4Vo
Egng*eI1)jp,-E)m<kw4y4p1]P;-e"ISJC%CrD@yNOQ3B7uWe$!)C@Asi8<ge3noW@D"M)lpF
N)
:LJdOV`+L>4H=Jt<HJ]6bxKNpxbu1%5O8II^8l:og.iOjh,,>&EPhZ%yVNncysYIuPY;0v=&~PXm
.@K*_c_0#ERv6&_0r~+.1gQV^BM5NhNA2T0BvyxZ5"XimXN/OnIrY~c@Kx4k2:-8^2O^XF<lR*e0_rujw+=mX|QLKlT?TQlj+fKjB#e?$ZCA"9xzk~*bv9xWBGz"NWlyMz)G
gYGu,h`^jJjd+D.*"%*&%JP0/oC5%j.B>8jg{4D._"^4,FLd7OJASlnk>omG^HgTfDLlO<.&<L6A|l9LN[%]iVAC<1]&etplg?=DZZRfD$gjO2!KwpH
?"DmIpk"SDt:J+z:Z?"%$(i[6)LSj<Y(Sd!c4vMXI7U2M@eAb4OXnO`@dh(Q}%IimkY<~w#>3-pB,o&76D&8S6<uq.h/#6+8ujiP~31v
NmaFbLvE(=OiGXpu54G@I(T)O<TfR{jBi?L
KqgU-EU9X9lre*H-u|oGKHLPvNDC_
g8XXD>9JNE6!"c<vVh!%L$v4EngD2l52H#7gt"-x-vAdy?FPw6@SMdP`9,j<bJ-@;$y!5yRw4[e6FCj9u"$x7"[b"ys!gg-JFM$G?vP|Gzrw%^cp?v>%`?Oa7<mbr#peI;ZkJqkIB#SBs!!;XqNtn|mF@pS,rw"Jcuk*W)hihSj@3mkY%
`;oqZ:O>=l6MQWh2X3_hJRFQ4MB"@jYhAoQR*[&~7/e
O?Und2Ia+#IqZW^(Oj[wLDhX^}qJkTJRoT19dc"I7F6<w[Y0fVRq"E>]X<nj9y+^vE/nIrQEB/rjr|WC&D7~4+F-U#d./w:E
[
l]rYbLA8z*fY
u?*X)ry8EiCv)R-@0t84eBgnbdwo*c$J".#Avq._Vf[08t$G*ByFMNekOxnmdD9{>aS4aI5l`6Jvs=-/.c>X/&#a5FoWal&!H|rBeW@o-_.jhVI!mQqh<lwY[+3l&1E~3oM}HSiu:@(racutw>jDe|oS^lV&Aj6]iFpK@[*]D3s?^.rp-,-l&-iwJ>KmlL3AL5>eESNypA0mmwX%lI3KdZ[y/d8`McTnY}fcKLC7%2KCU!Ua)?f(Azmfe?/U)n5@//x*
D]%>1yWg{;d7"kpACRcd21f8h$mkWsy2RZ8f6ZrctKq!q"hW3Kab;,H(&?;v1W?>N0L+-p6K%.N=fTtJPsnJ?Wa?d8)XMJ!@|
Bd{o.Fr`J(T`YW/g!la&@H"Faf;uAL5S!#X>ik<*,)Tkv#tjwf2Tu]_JReaGX
]<v>*dl/m9.@oCy>XR65WI_NAVeo,h*f[W/=r
.Se,/pt
m(D-^C~5My)+o1Y#d8&6/=&$Ob|:RRYV`;SFncyl|r[mL4#;|$H`b`6232WEzRs,.i?Z2P2`xTeIn<R3>/?)2(@d[doGLjbC{x8n~T8Z1V>pvgWMkjkp&XPR4+jb1Ks"zsXhyq>#e
H(AZa;B!M3CH}_L8s,{9z-p^3.(*h?j)|+)t"4Vu&Fco<v[kL"c9^8y
H/<?+^bAi4DI9JU5-d,ZUWC^/E[pp@A"jcU_B6b2n^A9SN`y-I+7&WmPEB3y4Ce:g,MQHyPSx4mg01]"I`UQ<^E%$`a-CoVrjW9Z<WfLv>^q*=5a3NAl0R?0gQUu^HwtWpGB
g9b+k?3>jiWT1pS2/KH-N9:|m2q?KGS+&u5kED;J^;NofnezFB<!G)X`ej>D_dshxL7wN|FME{AA*S(5QXUrTyg@3m8_r6"W(R^"M*xH7?_5CP=2lV/s-xrGhA8k=@dx?(3=VQO~,`uwyZjwlN4Ic0i.Np%1bYS[!h#lvOe]P,(/S
C5]]8]F7jcDcR?GxJu[Xp4ZDbKSeS;/k&TBw(sZJjyhf%S7ea>nii?8&A-uE#[2|5u-}J+E{s2C&c_io@^U(K7enaQ7^[jFy
/Or<6Lm(8ZJ[qFd@Trp(`1CvZ3.(ci0Qk#_9}thb{W.n"54o~%U;L1aTt)Y[_-c90$c1F57R^bo4]0IkV5xrz.8ocTCGvMh;s8]s)#%"itVlxw:;F2_u&i|(s=_`TBRLzyF84wLq#kt-27Yv%1wPT.&Iv;X?&_IB0UdA")[9)WN];@A&]UHkp&rgnq00kt<Lv-W`aU@`KNP?#wzWV*>[E]e01c6`(c[M:G?>9Zvy4O1M<fJXBtd^t+>[Z4
+u`}Z@;`?hRek{sW4Lu",3ZAy1me"6BqO~G3ksRZk!#fwt%s*T_AGc-]G=w_Vzz$6oA79/
7<E#dcK9aXVRf0cPdnxj&<=wMYL_M1P9{J5v2`N&M%z&7&.Z0g"hq%SBM:%p.PMua=mmnGepQr^8L[+.>hM5+^xTS2dhFAi>M6tR-(gR_EyX0n_)k@ja*8+[wXJCG=Vcr4G1:)7K8aq%~r~B1NCZ4nO8"l$)1m;:P:ljv?p2jdfUU<&_T0"]]Oi(diC4j]N!8/{fLPf#,yM4PIX?q("d"T_UrK`bX
saspv
t#Nc%d*BN!
_J#u@G5m/qkIV8h/@.-Yp(RC5bb&Wf<k@/;30e
ysmge3,>++fgung$
P_=&"|D{-Wq[
uNkm__,`/Ytn]N6wxY{h5!L@t;*x
m+8;B3C(KWg@k*aM,S@frCC9I,S0-TfcZ_B:,CO~%(Nes&$HJZlj.IOE0+dN+(o=hKZ;`V=x6RPE*92s@+Ph)-Pms-/)70149riJK0Iy[C
iXcPOWC>DxuK=';case"no":return'%Zu@B6KWB#?S,lM$51Zjzv(nW87Ze8y5tDZLRku?_Nx[.S}nv]fx(Mo"EnqJ7z$<oZgwu2fpypV[."gT#;Q9n6D<cMjsJJ[ms-sRr#;b
5(p`L6$FWUj8w`Hc`O/w)LTi^~SlR/l(Ba6%U|:z]QeCD4Wv=4Es2+azTO`I[^G<(m(hU{w$r62dW$qb:-ZL=S=D2qC[^iVKUFs(=Pz)y9QjdAXkxb33@Cet6=bg;ZRZa%2.&Jlg1Zek)vu%F?@LGQ1%rljn=^[C8+^Ld@&#(Ql;L9QlHAlW5C!%q;(sp=;$>H.armsOjaY0eqeAq_6kS0mYJKC5OHV6F;XuZRBcO}"NB=ZG^[)K<>jrC=+MNvp"ZT=DT+cfJ7ptFfy5S22^<hF]xpHC^n_Sr<M`2:mQZpu0R~%SA`gB6^LkQ{H>nua,Vl%(KaIYa3k09?--TK5zO,Ctf_"8gfEy+mC[C/oTd@w3qT)nF~8G$
/g.(R3n%`/
h)3)k1W-.Z]oIk)Imd;]lfk$"yG5ZD_[^(NMu+4!eGJZjS.nb@t^khjih@2ML&,QeCjQM[CJl@N4:7v!{uuKG8[2Kc@7pEKyXGMHLt.o.j&V},c;YIa/i$7d?wwfAcMGb``x^6Uk67#GYtgps[Zo}y&"8A/=a1/G#Y96@A]?DvQKKv;2CHdf
3Ss0w!TP&UY{YQQ
Cy>^!eGS0|%h5&"(?~l.
qSBvRq[NzHbQoJ3jec;vLOS>6koZi!6,:+S_nOH)CY-6Ndyda:XWc9b;?eC#Efw8=Ril@&(G[e)fm3N-P2%rpel6YX@utO)42MP*O(RRs32^
dEe-s)VeK"q{QWDT]QuF+aNvq`R{Y3N+xnS{#F`};78wA46)X,6|&FYm:j*|p8&z,Vf`Ak
tUg!g@xPTKUMKqCP2W-$HpIW1EY)/i->>6zZFqs0!LaN5&>*i0qZ~4<J75=Y{SzF:#A7/QXa,/?yL@LA~
:70$vNmk@N(0")[.`u3K@Lm7n:k[VYz"Hg::IlcBMjHcA-hJ0Cgf/f02=d*8K$`J%^MR@mQ_kdjcT7`=|$wdYx3t7Go<;F$N-v
B@wcizoik1r_j5ZNXzvt"m*F@o2NL(U27:YFo>`SKX?m/jeZ26YRjg#i44&R8(2Px`fdh:%Busf#y&Bd+B$a8_=f]5yFx`C`/&HOprKoBU72A|%R#cEqVS9&1QK_7Og6S&T)ngB+5Yug3qYSeLz(c{;#R;GrAEKiG2w~sFT5CpJ*mq1}gx#T<<9pJ00@:{*,p&wfBM(6;?WH)GUpsRO%0se[@P8dfJvxG{"d@pD+():eY]a._LP31ymTd1Co%Kx3aU*1)yPM+.)dv:#9BM=ST>`Z3UIAQ{kc$Kn<]hQ%YVgz(S#FZ#00b)Datf544)%fuZOd]qe6GZNs[2K-q$Gqt&0)uSwI4<=(n_mljiZ+]8&[4,[{D}(fdB$F59=k>eu4UfODeT"2e)/H-ygt[3d?6Amo5>pd]?/RgCysw/%`vDHx%?I9y7r7LMO]&^y}Tb0[I,4Ts`p-+&#ywY]BLgh<&:Z2-*`&809W$D0YuTDfVT-c)mEWC3p0)GQ9IIS$TMu]4%d+voR"2qwNN&[k;MKoD/k7&Z25I#f|Yiq,kLXq[04dZEB`iyTmM3pj^Ad]3@%BhNQ+4Y8&E)pIe`#wR0Dt9e<s0U>G/nVLk-90_;8o6(:m`BOg$)VmhY=ugC+f2"8StXxTVQL"4?9c:R8+p36AQ;U:[wPW,kq%?%kR6NSuBSo8Dz488`/VGkxr(M+?/hnRee6vy2s_sRrW20I?9=(4b0V%;APw$
=:u^4J.Q#~_Cg8K9:#L~[R;*RKS!4R-gTAAi5Y>5<qaFA=b%g?F4YOLk&IAjUCS}6_aUTL[m`<5iSbOH[1LtVz$@D|V.8IE!&:
,]%l5%C8M3JNSO[2@IJ
VAhrp(_!ZMHq"gbCu7_dS0gQ!_wE.MwQ7x|s```gIZvpq&^J$WRta)[gB&pf{!Kj0fd]Q%$iB%PQ%$u@.`xB+D,<<N"h@@sog)v>?u?
;-&]Zfb^_aR>{0bYkC<Tb[M@pwd1@h=JNT,&6T26h]`UWga/.y"R{9NLffHYl[I2V7wf|2sh5C0q~_rnn,K9.epq-cXsI5rmH1zC(o|GyYDRBc{gG%%q|UEoL`9qYZY+{=fd<9jya-u@(T2O4Mq<~t;/>5M%HUQy&X
Rfu1LRML>2o7BBf1f"X29e>70n)N=vi5ua+|MPB=TcVw#8Ej@UcX)Zw.B9;gQ{g=N;XmT6x4Qp5nsdRFYnr<Hf"o2
?(!^,Db9/mg&tdW)rXU>0lt|8=Q]LqxL
h15I[#X?F4bK@57q@CG"IfI-=Vx;qKR,ice>-R=;)EWr|3Gpdb$eUa,mM*[5X1au5DVCmwj"D#O!W%7@DWtoVS^;lL);w[y<]$*4Fk`veZaPf@hv1h"Vaom_Z;]gl/bd~FUQc]RR./t?8^,k2%yka;!>:_VCKlvHb0"g0h}LJ7yCp]qA^7DFB,gZ,voI(t[>d?jc#i"f!nkQQ_NW^CvEd`EZyB6p3`u<4cN!k0AIur@@Z7aM#(M[y8&PO/7^_=YE1%T/?A4b3[@/ZL6Po*2R#-ef+k$L;&{T#
V;IHmTzAv/s=!>F.
r?V^O|CE(qAx:e0ZA=rY-G(i(K_!m#O_>+Tn]enkjTExV#bKLt@q%8@|=zY0L`kGVj2P]x%]_y3+;3=egFW%O[3AQ5(idf7o0JGNq*-XHO
c;LH1lyVWgk:HY]9[:3BN&n^dWE%Mek
9I?EZs+t]/6k)^{=,kRkV$EL!7@*!:yFL&=L;T}2$_nIWs2;_dQKe?B(m:I^*^c"X[@%I?^;Pq*#D^F(#YJmhO_Cw.<W(pfP{YqV>badP';case"uz":return'*s`;:h"+>$#5$lB
&_k2&,FS1LDlJGD-z`4SxRBoix}cz+_`U>3jL=E61Iw#K3JQbBVetT~wWTav
fBrIEDQa[b2qwrkb17<eV>gAso#qW(^O9=h[>BC>jM$m`5A3v=1)O-W*Y$,KjRO?B%7}"&<~a+=^8?c/1=/a5=U=3^fCVh0q(oKG9{px)lOuy|$%9NreZwcC#$)R@:4DOo""?WP)z)y$K$A?jXYVvBEamX3*
@U@pxIR$pT;E_wn;e7#@5l96h1%EI0=VW07S)jO9F@uHBr7[]6o0kS8Us+/u-,Z9fK_s);I
c7Ldt"`a,vD"0C1hn*jHK0X2[m:nF1{f1Y3Q[sU3{o!f^6dh&)z["=/FcOuHsWVD{;?9jt4Q9CA=>Y.eoxY^8$2`o6jDrixiKoFIIr^?k!I]J@1E|k4W,"SBR>}@]b`@o]h83-,jSK`rdQ$km3kG&&=hbo2>A`/AaixZ!!^8&RI9A)&g_ZNVWUI"bkX*Bh^h`"^GJ(o-.8)n^&`2+p}x4%"+<,~XcigYQx6d9NT
]Q.>9DN#W<k={b;]M#MXoH4b&G])Fv~Xd@7C5RVkV0&-KQ}5!+T"9K._<7ZswW?:KlHxm2sw`T2MkjI#bQ_HA?^OYeWj^GrE8
`cp[<1.W3k#<m?{SJ9!BbXBHuOC8AdobTO$#7__EQ!g5q8o
DY.IVlWqBjNvVI,!AoMHZIRZYbRyQvrgEdcaX
$u/L4n1xSVmP7>:V01_Z&f{o5x-]lij:8=gr.7S.1PD)vRDNzSvE00658VCI&&P4!EFRl+]qZ>Si,h4Hxnc8d5%i&=*Ex#3u8ANnz_ZRhe8v2<1y3)IhC7{]S^fX<5?aw[t(?0t<,d-PYG12y?x)uc}J^JZ[VDM
u]&
"`]`XT/m[G&=./>a$jHSFBL3fmO1gpk5CGz_vX>y(uhY,$}I,7n(NYAP#o[-ldTf#-g&kYk@[K"wmv{77M,,@!Kf`Gr&E*7wo!kV]<HA>wIB%o|G^FRs($^q@M(+)18dXsiqb1)oYG-d>a_;myHr};-NrU0,:EotY%VX5VJ418.D{
v#]QxO":&z%Luxvrm.I[kGAY{,TWRjAuxH5-,*ZDJK01=,$of:rTvJGgS$;5zm+/OYoV_J_,1!o^k]D,A4xwX$7J_Q!^7*v#)PAS+&L_cV*tk3Z#vD%Iwg=Ux.E`8[`oI<T7*(ZA^pS*:KFW<ulf?R:JB/Y<_KzZLr?;RmD!XCUQ)CL,InyvO8@>IFF6;P;-
-w$UgX[za}HB!o%Og+BI0iU9%XhAVS4^.B=]dduHL#QB$F$MXv$j.TI%)fFd6eU`q&85nE+"8*7G,$1JSXyO-Yng]S,BlYS3cH?C"_Un.VlrWN
7eA,Kmy]#BNkr6_eKRwH=)W&
XgBx@H*p>xCOax%;<11zp/2$Ipy"Y$9U+`rw;O=T=ZwngZp7YNXG._j``#N
3XFgkd2&Xi2<vyWwVCo~nf*1E+)|Yn1R6*m##~G[$YD(AK-BL@L&s?]$5?vWt$007z02SpIX3>es6is0vYM4iOc
^;o*Oxnan~u9@1tp"kOK32+Wl8x
YIoq+Qc=V$q?%}8k?mPN-A0B/N>KH}6$GPh1X+)M:z>f/+3XXSe3I%@fR)wG5c.pZv.iH}F9eQ*L>=:O[^j:.!&n6r@K.cV=e5$*qnN7cFC7!!o.x*%`,w)b-]UAKbG{0[8}n3N95Csf8EL>aK&b@rD6=X.^Q|uR:vWFOW#<TEoBuuh
<ZZ>&46PbnG8elrcaMy->i3K,NwQkg1giE:N(_Tjye%j1+P`e;//Pc9eQCqbK1T=`K7)fw>KI(y`4}A((s,
=t>$*~X&$h%z$XmG=8x|ED9~%/*J@L.aCHT9]xxbw1(4Y0H8q4g^&Pv>ct$F251;+f5IVGJ~4rVGt$Ahf>2@S$%Z6k=t
^2ixAj.2M^2_M51f8#@.lEDmRTdsz5D78o[LR%j+HdixQ%P#;w$N;Ys:Rg=y>W3v
<R/r*sVMWK8emdm?<y,t"Kv.)Hfc9)$DAY.HV}(}eq*4N.Ak[_p1b6Sp>Zg/,/jo3(fR/FVMRh[L*r^)-V+3QrLOX4ibOLi
x#dv/DGRTwmP*lgzJs6y%?Ysi<c2.O43>q9R+qcD&G$&P(n;($#uu[[9iZIJLT6.jftope
wxOYSUDAY6RWW*@R<vj-GXW.%ZMAH,7VMvBCR1MDjyl2]kwZ&fv5vq!&jtHBtf*-@9R>s=GH{W|"s.HTay;xnYnK}2+WMFn2Z]N7a635sRX6oEHXI,6%l&J$tr5_,4Q)W>/HCU1;e^VfB+2Y&W#chd|`s@l23.)f>e%<0^e7w-Oif60<
,DQR"Ly"$S3wO:RUXZZ..]g[eRtN``9;7nRd:0G6cE!i]5PLP91jw"@vlQ.>5n3OW_i9HwvORU1|5y_H:x&Nm*fW-]wRp:PJMSUZgICWO<nQVqIx,Y1qEJG^$uc21Pv-KDVBV_X4=>OSXwPT(*3e0/=G6a#"A,CI,(#^xx([YZcD=}k?d4OG<WNnIRdr/
fQgyg=OCmC[6L9CCc`d~UvW1Af%T!grI(rNg*vh=6PKb@0`@`N>cDNR5ngqZ=c7TC[C>0_/K&D0o^UUB`l
N;(hmVxT&.EY"-
CcmiY-rd-
If:+Ogs>h,*ule/vUFcEWB9Kwby|!c"fx?>_W(FO3:eSRSPsT{Z4,92dFOu4c,]JT
;$/q0Y$Hp.TC:"Ulg%Ja*$yR<77pE]yUS/cx-K?aG$@b>E!;Ocr-tuHqhr(@rM=tj%.)hatdKEOeU7_4Vu3CIo,(W+ZR&[UVkJ@.x*Ljf~n#+{F1*l?o7VO)S@yykps%E"P@M
0_%na8wA';case"pl":return'$]^@j6LB#)Q`sfw"YbJ-2._9w;m=#*:7w.TI+*mDAETS:)mFT;}:laHC]G/eC.D:,]&H7X{!MVmg8$y<a[OchFr`A[fEA,/>JkU*c`;<sslLlKWI.hJK#
PZm
w/G27X1m@j5c}i_B85Bx{sL=NFcw
vL3|[[GS.vcUxM^6H)D6$J3l<]gO6TFwuuj7;b#I_LQ!)D`M[Nw_7qWc=#6i]Go%Hvd15JMyZbvsHSy}SMbl#QkN#ZiJN;sVEnydVEsJWdoQS/I^L.B)3*e.op+zcgKhow
GUTG!
zV%[cYj*/PF_G]:rb.DOv@2X4pw$^$KB.y%v^xx9Qe@LRjWPUj<kt6[kLC|ilB77wb#G`HP>U6kl^^.mim<qSxJ2$@+PCH+w9^gt!tZ_{gpK5M$Ir"BaZ/8!BwymO]at@rn)aB_5^`ik*^6kvcZi8"Pty?%UmSFTb7`_mBYTy0E4ATu=32+j3"ls[#3)x9xgr?&6OZ%!{Q=!qOF_i>,2!KYFd>t]6I>jRwi80rIb9Bz-]e-k~)Xc8C>LNj20.623
^KGzBphqd*^m5vN/Q)>RDj#;
(V#5=Hq+bm~SoWQ]29E+sYxUq<e86-obB>y0mi#_VP,b,!,G7h_S3C+8(St(M>r
cC%Ggv8LWQmY%_3T$Ma
pMSqNKS!`[cD52aV?[R1,qP9/,DTpBGLPG(>+H&H7rP2rz)U|
<6=!(e
B5&.ax?@d*3E#v,t9wP<!)lh+
*!obVAc`e.+=T=RM)!tp*d-c?tW
UqQsOJAUwv7$cj?HTb4xu_c
-PYFFcl>@0y(:s;5;bK&wJMPcy#+8OKaWkv.:fU`NTkgx$VGe`;Im5vr.2u0S0f{%?]Zcy!x3<_kEmeyF_#.p_;m^QJwio$;c*jq+%A[00Qq#nN2-Lrg$N/SOwaS
;w#X]$P;&b5Un"b]j9dr`H<Z?eX)Ea?AL2,hpF[tD9}Jwz&5aMzp>QIsxQ+0(ptTyuJ6Z+Bx]"CcMi*`}t!r<*51]"v1rEmgCWj4(:kDUt,eQ0ykNRuqkA:Cp$rTw$s-5*>MPV-g%a0l{pF,.w~p7y9m;)7c,/ysWM
a]7PUCiYO)ID*2tvSf5haHVuT~6xfs&Is6*/=X`ZG%FXUvL#KCd2Y)j8pyqGs)+qpfH#rZ4ZS>u}R!02)wS?xV1*B2Dg(b%LTHf^J
ul#U%~CA?rgI6*G_S|ND`Fjrr.kfA2jaoX-Jf3Jjh6FKeQ6eakJV?DbtC~3aRcEPI[qA=sf^x:=RcVByy]*^0!e=,Q>@8/TH({*TC^,IP+#)h`ai=jI_d#sVx[d&UQ^UOxyS&|U""1y:LO$QbnwvdSVqk8Qxd=yQk5wKz#aO#h6^XFZUNda,b.vHJ+S^BD<Fdnv8E5u,H/uFA{Vki)^-Tr8{6+hq8q1P8~j8=TExf%U@*Q]WDCdBF+%Bh2`hJeMR
vV2Q1<J_aFa3#yQHivaA5m`7Vep4f<2A5`[tA1Y$x^kqTtS9oqObQt*T3Uh^eZ*QDDPq1kPCoXHEcRb%B!noeab545rkSS?i[,PRNX&:h[mS|&i?Z/DVE^:!YHjVFMjsawf&e-c)]&NnNP}M3(.L+r<R`)d:!k513x&mm,;7&Le3C;F))4kqwpVZvqy@OyNNwgUYonYb.9[OD)>mi(8$X[_T=@-9D4?A[?nd?1zL6XKe#F|mE@7
Y[Rq]f#0Z7C5))Le9XU
y16:G_!-Z6mqtumDR:T9|HFmT-yQa%1(8)h^fct-m3xVxJf!kC#9Gclrr/<.}HsN?6KTT^0
FxGYt%Xp4Q%_)Qk,Nmkna$J,Og1v+%0Zz:PEq
S[bw}(F%|)Z$^E$m-5)Qp
(A:uzL<"h*VT^viJC+m$dj0-D@N=>X`rOq`!A
`TwUB)a7>Z)Os*1s]tH[4%T:*apf>B2ClPpoyL<R$BT]5CerLL{P9eM@FqyF<)HZ<+pw|,Bl;_SLg-O#M.BJ5xB7-M{jJ<)$P:i`I2EpPa3xhHX>?RAjeD7n}D$-k6l%D+n"uPC;1kF$cdgLR/C?NQ%`|Nla3
Z=|hNBOCM:I(p8H>o1Z?%U/&(?lf`$+dBRnBdxV7S!IV$@+SlZT_y6#7R1?%ms,&3cBZVo8q8n>-
uA;"AeZAs_TcR-)E+4)8Ej<gsIiz2
XEsbQK;5-j"%@"DO=MK{p#P>Bx]N=n5__9nq#_VH%jK7!HI!vM2md++9@snb9P?GG|3]rCH_*V(8T<BcL"%yti`Pj?=57+7z?~10v8wm3"nRP%Oi]Ab~8dWSCuXZCFKL;69Uq&Hjb#.ke:`M?I;~.AN{Bltu$TV[xQ"zQX,Y>!/)+K)OY
1l.[EaE&6g3TlJl0CqV.$),aw*d49,h=OUuz11>.K-,|]+n+I36)]*i$ItXa2[!ln.%q>Rdk%"u`WC
yS|;b0xuwDxXo0PFA`PHjx>eKWESC@zpjo.s?:dE^npDPHu.[u:&fZ6&)QCC|%!xjD@T8W+CJ6pPx2`7<`vVX1T%O[V5tOx"HIsi;ZDu24>Fju)CoSh<k61RA>W%%R
[jml6*-!g"Ji*(6D`AZnb?_$69qrBdcM/TZBJ;gVLKibH>Lh>3(xXxFQ"6xetI2(J7W%G_h5&*@"Lkh]q_`&"HY0n$_+=DsU>B)FsP575#llmz^o_@+9Ir#LQEAY^]/,Ojy18W;NDQg4sniKZ6orG`3FD~f-hEx*5,Kzy#?Uq$iY[J!|q|Nx.YV<WXI3N$$mHG+oy<gw&3*,KjD%Mg/G9gF;hg!UgMk2DYCR](TZCh"onO84dkmuM=p#fAmb]s);&aAE)_FJ3&B]YG3%U8#<MHRLPsq+=~<6HngtY)&$4TX"t6/8P9CR,EiD@3?(iNp.on_WkQ*My)fgMyhw.1w81cE<Sz?LRA6BrH@*esZ!XGS+D[*n
^2I=<PFV~Yn
@B<-HKH!-qr3V.R6$YNI~Wzii2v+|Y._|GPFsT]#Z+mAYWZ&dP!gvZb$IS]</ac$;/5B3,)5V#Ij0q#_Sn5vGmQd"(]HjId<58];w:|I%V@e4Ic>+Xjlz+hVGs&X)i;SY[/5:Uh29IEQ&E6dIv^[OodugROi=e$T%H:[<8Wqlx>.%EN@"js
7LEt,XC[HK:>-]".M[L]c&U,irQrc>&P*5|P27GKe:2FOxNG?QYR2KT@}lI
EF+Idn![,V_:#/faz+q6gnGr"dr%=U{?%x+Y=CB01&EAI/@-Hvjj.WWUI+
*#T
65
V%=JK/TYAO"!].hU2T?Td.|T.KxkPA9:#l[$Td>.-W/o`B
UH5d6s^C@.B,SS5O8I1(qtw:r9yGGsE`N"lFgmQM;RHJZjy7;.Dm`y<<V.5ID9;"X%&3eN!q%B%#[1
JLqQF]
K:vtg>w/j1D}V;@SLa4hx[5qM@E0L):P7)#a6_"=t1;@DTJoIxK^II@3Rs/u-YN]%O=PsL)Gf!Uhg}V]$!iYc=R[8Ktz>;xoEcF9I`(0c+M,$QF;24T`d<OSl|DIp.3)O(,j_QlU0W[w7v/w1L"MQQWX_D5f.mSRl
hjDOyfo-';case"pt":return'(]^@iaMD9?Sl/
$%CQo$2#w<Dj{TY*S"8%:6_vVn)PDfLW3o^3Y4{fuu=3%@|NnP(!gW!mbT=weqj^E
x&pEq->_=vHraAUs|a}yXg}8t8lr^mG5Y=:K9.Tb#Rb7A!)(p8v2nL,YJJ6,]dB(giBS(I[tXN>>1pV*"woW:Iw`9h$:iS+<JE0VH^J[KQ
Gx@.MVxXL]"Wg/xo,1?4DBP|/q*^BRmOf(7WkfK.
^dk)mKeC&ujkzctynk5ky%HYgBcIx(?`xPoBIK;_`&knYb63dxFpc[,1Wm#TLy$!<B+$/Az-xe<W)E`?0&38)>F!(4cD$v+J#FY&;RJ<[LBLOk}J0/KA#3*T!"z(5O&hgb;p!Hb20j]Ej6QvB-A,=
Glo6&uo$Kcv9M:)4uA.<Uh?&`Rd26^B-HI-Br9jOG-lZKct6o2Y;a:e6pJBv-<<T1=4p9Z_AXNN"W^R`H=d[c19`LM2k}=GCCjda0,,Of"%Yd,sRV/]ioo+k4XbE9Y6a-@^^Y!1]h;RSiPY9J2Dq}W(XuT`djxjV2>4bcGUEuvF[*[aI~&%:`3cx:O>A%`~+|R;9X;plADL(*6g+a>Tml:Vf#E./vfMX}dUsnfPGF>%`;-P<nw/!>gzfh8V*hg}
Sid))==Dds6DZ!/<q^Q?<e:k+xjN5b>m0C1E!U3g=]>:jS(v%a%5j<8qfV7ac9`dN5rx7_sw?aZZxCKFg3pIwZnTx-"P==1[dS^Fdkc#+(.^I"&tjb=Xzap^N:/jQre"Ohz$!tcd^2&2/)@D,dU0]ONng#<pS6f_tB&h~YG/9L3mM/_e;FLg@K>G05]QQs5l~^{S$N|QW$@#Iog.+s
baYE<"-2/-l2v;?Bg_xUxQ=_yd&.M>1!>rE4UtXxRm"cG!1j[>v`1ECl7/2!
re$tlVJhioXv]kH.u+9v+jM^UFYKo7,JT+P[1Wq
2Mm"?cB$Rlxh1vg0q`dRcya2au0P=y%dxl$x8tdw#>*)uj&Ao*f=x=%XV;F({_:Eb4xH?4GW&=d[rEOJBkN%
?$JG[&3|=FD3wEL7/J19_K${`>B<@*9lm{&ltv2w-O?LGQ^Z2=S)nN..-:PLc^Ez,C[OD$+,M$G`Umff2K`S+|%}+616:T47gY?{=KLFh#7eykD@ZKpN1/c{W83]AznEyO
-;7t82+$-cj"0:p*r6w"^6/g40$%[9;$,"J&%Dr5u5Mu>)wNB2K;PLiG?*a^&Wdb{#548yuNKd#u-a8.C-s%Hd$jj3PWVJ}x[^[tNBg2(UBYvla-XR!HmdB6M-=ixVGIXm0F;buY+cgVIGYmXp#uQt4aNE?4NX<D]eU3{d5ic&}R?*D!kZ)"5YJ2[B$OlB(%Yng2qx)pa%?=)B_lNM()s,K%}67;i&xCGT]M@+4l&5e4{[Z@uvje+2q"AExlRg";tmLOF.$YKx8"Duh6UJjBkEx+}tgv*Jr0nPMUxbd%cNU"[2F!eX|"b`v1[kf,D!#w~8+v/IcoR[E"goWRSx^*+VnIS?8;<)cA-6LN0M"i_-|@tZ@1A[z)(m[Kf/B,Di/
{J<o=L#+^p:,d0|l{2-dhf1oCqaBqOZ8RmoLP6
,*3Lch@2sPW~`4,asP4";+BYV}YRDV!G:HWSs|HQ:;Kekv^:sEW8PVZ_0-uBZr>3s7a|Y!!D-uNHkAKk-8;7gt]e.:L?2>h#7-C}@Bfw-n"ertb#lgr1tl(;4=])^*)g]~TkwehuV"2cxU^MCoWQog^eok(IhUS}?_g4SSe~W>r4]&mMIWIBW1]E7XKPE{R1A&ZJTheEEz:1eb*KCFUglJk~#No
LQf+<WR;9~F$00QIY2":8K)"i%1P#f_r[)kX(y5,jGQfA7n-?w@cco?A.C&]W[("kPI>-;tL&?/?u!MuR@4Z-+-l_^[+aD*)nY"@bxT[x<e&fh)srIc[Ix.E
3p3:2JB>CL4Lt8Z`5/l]e@ZDal,@?H;NL*7Bl/lr!Maxj;k.p,EilfKGStOTTSvj$r,a=a>5@525Ps~4x#70QgQ:!6YF4=b2nmtc$5.>VXoaz>A!~VcC]GQ%ZTyNU*R+I:#D[X1X/0te1On8a
quRf?$#[aw4iutK>gC~X%$H(o.Fv_!MQ3jFA?2U:oB6vLtWtV75PVL=0f%M)q89V2A1axAz[VqX_m6D[x].If@zGi3xu^VOfHw&Z&!*]h$lG@LQ<F&gR)vBDzH|AVN$W9jMXZa8.u+^5,@S#YYn0aJ/!e&A$~WAjEL,,dJb>l`BH~1_<k[2uO+f23]9T.W;[Fa8.7*_x{gR-yl]=p.jc0(a@tg*y7R8v&v7"/CtV!=v;bHtuV;rBs!0UvG6rPF{t@9?g]MR3%xjd3DVjPG^7sFwQgx6p$@|3;[8H1*-&cpWinZ?3N*lnaKfSDP{j)E:J&R73bsgUgKgt%2.k8m.aHOa3~CYJ}O8s_piaWBa?li5emXby&Rw"s7b(SI0#9)(*!T8acOBN+K}.aN5=I_]=$:.&{wkd^6;GKf`E)[pqmh@YK>*Tnb6lSdH$;s<<T<J?@IeC=.S?*O?f?Qxe-dA)`k3E;]B2-q
*ea5mUUUx[]v[GPjxht>Jx/SSGCyX5%7P}lnZJ_oQuuQL5VLqKE8mLndgFn<k+G?%*BPlQ1aWn)t;=SEZo4@ihJ_Z05agGa9JSDl3M!+8qc-:E8s1`
Eqab`WTt,^HSgP?rOXh83$]N<K?s9fux9HKLBY$wpQFPaU9e.a)hbH"D5`9+q@H8jn,B4h"=IxA6LA*EQHGBMj,@-L<$SCS][]UZ2DTa]6?8OW>=mA9euTe=L;iq+P<Fi%0Hz1tDhd%
w;<FiUPP-ePer<^"gjJ:]Tpc!>i61Ci02Ed<&ZZnDt*rnwn"RH]1[1X0^wT-74
_0:F?X
6YpO"D.Z-.}DXHyCL*sYS`Z_XlF"%gYa`sO]Y=FEahxs1/;3B/(jZp@[8Q93YadY:+trSmq.@"XEsl{?Cim2O]CqAB?n0`6YzS)+-=n>]B{JI:J/nPX6rR)e?vZ/K+%o@
m(-]`./gEB}&WtstrQ!HtG?*|)~=ZD07J8h*Do}W!G[9o_.2tCTOQSrt"0,WFgnmMD&i1tP2)X3j?lPkWR?3`;@bQQ9iU""';case"pt-br":return'"]^;;5Hp])R*#gW#hX>SWw;D2&v7$d2@PZhn8h`rokpEQ/KDZ)mXL)U9RCR&/"gVNE?GhJ|(mXr9OSS0z;/,14S
fDtxla^V`,EywaQ![TON#=?uqe/d(=bjgcU$P%I[Ew:"prJn-n`.,,[4:p5ip*J-2VR$x_v74su]v+!XB0k/P++2s6qx^*q7.MFq<,id6+Z#wgosC(Up5oBwG;P"Ek0G90Okol:sFIX)ee%=5jI1E"a^Phcz)qhU6La7580!O*Tm2s|8+)1Cc3,rs_&PF[oYjM~&rwVD~3}_LIItWR-kt>O`pOqs-wGCLN5d=VFukImEpfJD,5D@W&+Tovk(iyD6
jLbK0TsG8ph88,F5?hqSprdD3,d#]v/-cf37mbm3x[X>P-_1]`*ySgIWW5q/-ZANJ
r>*B(bY5(lC.YX38o5g_w$NG5pfKH(P#IjWrM{"3d41Lii]sySXS%,5%goVeu3&5?Z:p#Iqz?2&(w/B",fFUN4IGXR*1o/JEL2)WptSZgn+0^NCn281^(cjg
V0+O
H9+rgzb]rN$#pLL:C:,f0xl~Gpa![{%9x<J+8!c~QwKJMOHod]G>mZQS5;
|#d2cu=`d<C>wFy)H0l[p8&3>%9ax$(
2;@nLdw#blt3z#s+!=*GAe7h=L,fJ,<ihwQ@33,hQS,)(B

jC8AjcpW^7xRxr=VGOw*vL#`.d0%ie3[
<IpN0`vONBOVj`,/-U(;f,c?o05<Nu]X+515/"t[0YR!&%9e@SSWNPlx<R(;RrFGQVP"7=0"/pCH*/bNT
N;d%%PC{vy(aE+,/-|+c<8!+ewClyIGpC&YoKYZ{lT[MPscU`iQ}tZ)JNHNYalCny-PYU:J8_QlK?z1L8N:w4y<Fbq0QB3/]$ux]6Xn~J9N$k(eX9IEK${YsS>7?bXYofySw`Ym>nV)DUx8#<=J;#V5jbrXExhW*T:)sQXtRJ-8-1U1VijQA;w<HQSCGA!Tuib5tN#`W?jYyaY:@IN2_;}Ze-j]LNHpz74l*%F5[_AUb"NxF.Q9NJ%mt5%Nz3jd2a|s}0dxYWd[OLM^Nw?Nv+!fz@Q,M)*P4P2"%j4&R[]tpiu(MOHw__
)?<Do&w;GYU;w@veEU(KV^un+lrmtNay+eDlxK+T1$9<*L1Wl()"xd&OOtku>*C?5?%cHXI/g[mc;[vYL//^
_@,ebMrskLtv11Rp]1<[Ih+%IY#Kj1mvGJSm:/=Aq2d
HMS^_K)*z&5Zb3/ha7];@Nku$E)9TK@59?UAv/G!(H$H^2wC?R2^tFO3op=Gz8--b%|F`S]wpP_<U<~cnCAyKq8+#gzS,b1c*%g*S^LrDVHS7(zq&dZPxTDF[]6H2KVWyY2AMiqT(K0#%:|ixWqa:FBMS`rC.-bVZXBT}&U.bH,PkUBt
M|DW+j*{7#)g8FX^N&1+e#+IT(g+46L^VlG1^F&$O
I}Bjv|EC$/BB!4CIPyE{ff$N
0?aon:([x,`%M:9o?CQg*.rpu8Y/T6r;|Aydp[zE(jdqxm918%;3`TJCm%#bwkZk^qj
g96@V3kWVi|+d
}>S>^I=XkTKpYAuPK1":Hcvkc#04<Qpcms%_wQE%C*y3;V-
rN{<}I8K."&0UDUi)?zH$g=^*Xb-W3Uh;
WokY99_4@;GymcpVzf}0=
9]iIUPiim2;nvSwPO_w9gqNlneg*qke9OA)Vr;Qrk5^NeV"6sAiQ-;j?R71qbiCjz/Vb+`84GV8SeX"Eb:Z6mC8RRE47[Ul&(--xZfk1&P|[EcC)URWUb
;Y:P);pTJ861FFmF*gXTpFTn3&qA{/%JB.&u#1zelR3gao"_Zya_gf
#^olHZ#TO$l*eS-<$nG!Uo@[PG,N!3x.uV$w?9"UM9!i@*Iz#)eKptYSEu8pl4EU_%Y14`_rOpB3C2n%F&6:^fM
#wdVyk(Er<T]tkdAGhw8Sgmm7
c[dmIHd/(~#38g0~I//|Y:$"sSVbF
4l-.lxq@?f!9nyLB9FfFcS!Z*D1RLP5wVL;Avok&0OWL8F#.(2FApuOkw*6d;lg!#o<)//7}!3gpAvF+vqjplu?Tz#@?>D.?jTFbAdFS@{.o0tU4x(UjmR>rE{k(a%sHknR[MQ"V,m0
<iT
,C
CQc:vEaD&$lw5Q
QD`xY{^c70wY^#0)q*/UE|E2"kjP^!0
XP
e-[0/#g1]=#<[q{<GcEq=g*:r[B4n]<H
Q
((;5%|.ZL}o"l(MXy4+wao
W?L49e7.grM)HJ/;TKt^J>Q,vYWna,@G;qB[Q&QP~a$.@Q(0yVLev26mLqH1zHAfxJO@P[%rT`
xXC/eYT0%ef_(!@HJ[toUme8T:TK!:v&<&.I#Nc0UqV9[Gw/aXP1buy>67B1M?9~UGbU^}AYoRvfItm(g=!UdJ5cRnsbM]qqtvk(B-i-]FrAU4"Sc`HsiwNt_@.wpNOo""KB/"h,84:v#.I7Ry"/mr?!&2?eE|#Xi-"AcmVcJgGP4ar-^~ipBt@}<WLMCiO@E2)-=oreFg"F[EOJN2ADq]1l>G4o2|gEfB25_"C1UR27/GK;U,[#rl93i";Xn;=&L]L_$)Q%$32Ic(Ze3"U/3MQ?3@qdVqy./^AL]{y2F3S.c~0]x{,3*,=Tl:mE-YJ{
SQmhI+}](d>2;u?qf?,/
x6!=K*0>d.m/jP,-Wz>i+8,=X^>Obrqjwoep4JEcaRSc=<deh[_k7tK,
F+|-Vgh+x6YGgHAj-B]<yQWO18F)k$pXpTYi4Cph._QvRQ&-E]M6$S6i,"G9LGR)m6e&(>)k4SKe0*DD^m$:!;B/+WjY*+-1xN.m>;,5[<2l,nk^uP0
/bOFR?<G1=tGO7*7;w^0CX?PuIaRSVe<rQmD&#UD*A9wc+!NY3q08_9$TYGw^L[hW
tfw(F1Y]$kH%;M<h4WD3~ZPr<w_e%$Qca]}N<mJkQlfM0p`P5Yq-@#.VMVU>(;3td.((@^DWhb{6|L^keKf`5iH>^q&nD%s2/
BMIt}F`:3ob[;P(j4$8jmU9%9hY0e700X9?p!XOnexn7bBXM[i{+J2=f`=8(dsxdz-GWWYUE&`%W(c(A![$(B^}@OPjOza*U#S~!)_o#1JbF+T6&vt}N=tb';case"sk":return'$]^K36OpM*W2%ie#|lh!QJ7*UgxO98bC1H+NNVse^f~9p;C-B,c/Nj23(wH#G9g82i)2S9hKQBz;L
ekWl|RUlvLj0OJX
MkWMZkHG/r;xZfQ@%Mj.#Y`T^/H#WiP&4W~cLV]HK%aXw,rFDmJ?6v.e]J%]@v~-Xnm6;I1iO?7q,w0Lk_>K8Xg4YpJrz""%SZ

{Q.-`V^mlEa
_u@5|)ivWMAr4!hIl;;x6vaO+
kQ+W/i``wf1WS
*W/Z9z)b;y6_{K%!+=V^Vu2Q]s_vAo4+o*QHb&
XqGg;2StYD>7ulTDWvOf$&#$jQec
|lE#re6C+Mh6HZit}VJw-ypeDfr$rr*h*2ggn<F9spPc)OL*F?$;k?IbA[7[N*_rA`SvUI]7v%MTai2ZCkcRBBm^]L!9sBq-r]0c$SdUYctaK7ET^0
E_0.LOLJY2JR7_rIPWcA@:#d?44+$F1l$?#|evY.$3?OFQ(3fP3CA+"qcH^/2(Feg4^#%8SipYw&"b$F%i1z=N`c-MK>X)8?U&eD?kU~lWkA?zv/#[v_E.dt3aV"A#d0+J%a&Zay_#b=350/w.+J)Mh:]#xJ$~>Era:0Y*31_44(/Rw.Y69/0;))jg,G`uG*H<v#gm=hlvekJT+6OdoD*-/AN2M:5}vCB1meBV0LdUbekM]>^o-Zl6s6^6$yAqj{d4
e!%0uWEz%<r(urXh$du"v:d[<Q[PaY+S_[@X/2.e0:Z#M06@73^1eU+oLdNQb&$M[Gz2,dNN48Y=d=[=YUK/fxM/q
<mc
.gfFI3jlJgQQ%D-b@^;REV}wLM]l`jJR@;<8!0Y)QPQ-AKFR[H(bAOJxqyA)ndUuT<?V.PZNt
VqqLs$@f}Q!67$>+"oKKK[b<SSzg7r|EM&49O]M:>4):~W=%p8:Yi
-F4VkpbLfXiv;PxO0T?D%>2[#dM5i
8)x!}^)_NgTdYp&
kEq@VGqBo@!5ek>lVkOOd?qj%T}DmEmJpKo2pwvv<aC#rEI=giC@,$/$Y6NrzWWBfIJORDbBrDC6Jvb`$1Rx46R3Yld6Q;8v<
L`&Xjj(v1DQk2;sUXFYk~4?E]Bg7{IWX
e%-Qkj)Xi1NQ.XQj/Gv-"TX_!a>Ns<"
mik0L`/VB@y&lhu*P4R]RSTK]j/=JoV05a2%i!1pjMCsr*fVhS92_@,9GR+P,k,j)7n,09qx`yR<]4@P"@@yFD..6`Z^,.bJ^bPR$s=Jv"q9G/xgAKjyPWG1?CA;$TCk;
(!a+^"2+Y$T{<d5J#/TzC"H!L&1bI5xb#51*wh"2:/br/vc1CC&^:0%]Lf1<>/d5E_])(s1<fvkW++!"ifHc(z,D=LXC6cz)c{f8r3^!8@wRU#$^:vf7Jfp*x#yVrrpZW
EqZ$xin(<%T2#k9~VIID=u$syIQcK}X"8sU64E&ge8*9Di),6
yGU<:
lc9I"$N:m>^&[]1FcBB^
qG&EE=U)7Lv^oeQ05
VK6F@kEE;-)&tSa6WX|^IH:XdHeo2fLy*NZc*5G10MgSj*X@UxK2;UW*L:tVeyh8E,[*t;h[oK5Nf*R#S7RszIo5eN_e*eUK~Fmgn.VeVO.`<Sg;#"E0gYBfTd[LyBCX</#:.P:"/&^]QkmeV^ck]7IJdHxJl"%AfBd,1-VoM6x+NY8sG?
q?/:g|@>W.=4s-FEmzOvBOsQ"ncl#Un2RJw0`|)q#{uBb=sS.ZksuO!i5wuS[^5%IXoVUNB@;}>o0D@7*DVv#eaZ3{64>1Tx@4(]^S%K[m%tuT$U:A2YZQ$ld/w2Hr"}>k=S)zVi>$lf?cv$4)C0)N$|W1=T>YX2*xxme9y@>jP]#)>"glDX/FCY>SdYGs%2g&%sXkF.)9qm`m;v%_<!5)O{O`P!GQc?=RRF)wsPVHBj*Gp3Hdx0281P(@`fy`QF]g>hec(-NhA3i[]rW"v~J.awD8Qqt_dACde._hCF
!_`PTp(F%iY3;j"x()
7>P-(m^rfvKg:b#q6i,NOv13j,bdn%h|,$<2BM"@uD#S]Y>o,T:
nOw"J:8GZC+~yB.PM_qKiXBf9B`cT=i77<+c(md
apkQfa[5iC(WRFEk=u!s<g
j<HUCII7z1+o2/l;QWkuh*n
>Wi8OT+r5O4o?k"2i(8vvUe">Cyjz2shaa&<
u!k.W5@_A(e+$`e"x5q1>>u*qN0$O>8twHNWv2B+F*!ZIlm:u@6P36<G"~$qL7bSVk<otF/
"RFTgsFg!+Sk"Ki:=[)I90%@"#hlHS+MNs1,:X]4
]
3U#;o0@20r{82bExt]isvLI(Jk8<FZeHg5"jIVmi,.#*t-+G#;AOsZ46^`XYg[sE!96$uut/.967As>!E506tKhCh?d^F?_a=oOB>7B=4oViuWxh7nI>(OS/}brgh7}U:P6>IHKoKBGR6O-E<3]oZ/O75x)Y`S=D&UKTf0W`<kXdHFpm`@(TAFJ[NB~%.]^
L`
o~A_evQo)OAqR$%[804*w<07GzdlXL4sr]
Nt?#^&Pt[kVt`r|/o,d]OPt;2@TPc3f5?ryFq"@)<Tf*4*pF2Ff!pPhmfI_ug<o*omST~-66KM05q%{ABn"^r,DWYnc!?96`YH(EKg$.@Sq.nQ;2mT![@x:d^viKy<[((pfJ{ehgome"M[-1&*Zj=(wlb?"#y:EI(dO#^)_D>%];Gc(X~1)>Z8*,+2w7EBjdu:zFo`UbpA9.@EK^U*Ci[8pHte_[A^2^_V|Fd1.Gjcq+
m8?XDEvzS@Eal321^?k]HBjMZL"54$5^N!N[QVcv`
-Ee=t(]9sw=TAeDAZtB.3{,G1E!{8{)WV74$DJ.5!pTMaT,i#J8q866QF/V%2~
?$0P8BaTc,^]f`vls>;h-E{0@O+RZbd-Gyd%m;,C~Ka7wTZo`?:CVH|p/)rYXboezS.j7?2Cxc!+,808H!`g<9foGm+px?}%z5HXe[0*39!BWrCa9:G.6h]p{N:[g_<_ewzVyf6o~b;d{C?B+K-Mk2tia_EL)cUou;(k3*C;adZ0vJzeDd>0GCmlSseY~f*3(qp9AWRCFveCOy?i*$;l_S@Nik!sjf5
8nZa2:2r/^l!T29pp%_urSNKB>=3iXrW{jBn$
ZvMLmk,9u*daG:"r;Jrd?wb*K?Dj7SN4g!TR?-fND;/Vj3Zf@WM*;=_tA])u-4FP@t8:(tePUJJeyG"ki(#?gy*rD;vo;
?H!;%*-qY.^P,sT3$N"7%!n
y4G/3X*ErGwSZpP_"cN(}GtVqHS8uYD0gLH"ztYLDm)d0b%%<^Gf"ugq*S)gh:%l`(LQ"gCM!y;qySjjTDV/z]uWT*Fj:-J<p<qBmWNc^UL(5?kodI9))!w6}jSUHvB"G03R%E&/I/7qyO8jB/cL6kqqLL:^,00PFa$7#jQg=JVe:J@"!@jZ&"K<;(Lt_a;;TsKuvRD+!.~hix0gk^ML=G*j#/x0?/P?&QApQHj^1.,g}>5-(+Y*sk%:qV*e/h:kO/e]Wl_o-';case"sl":return',Zu;:h&D))Q,SY/8mbP0+CJw7dzOPZDH&qA"Qb79X.x"(`D&N_1*b+eK3%HN3v"?UPuxom|JWM=*]j/WBJ~oTl$psiIDlhqeG_
bU]lun$5nV$^kbcLUqm"7Zt3eiV7b`u*]_,{]Ayz;h
x*$rwt3duRVW|g.cvsSuEb15^i/2h?ft8,ErQc1br(Qgv7
8^mkh$7pF}65rG[=h%,}O,rcN[(b77jG
~geo(XaN%X"j:I,[:7OM[u,1*A`u5G>&oOypC])C4471P`J$Jbe_vcw&mc#0o):Y>,=m&f&$n5H>QG]&r?0I*ASvQ5nblum6]BW8&IDc5$"jS8u>7@IOqqq&3SP;>Ns0?KLGhb:-+*apzKTV_G}io.k(bGY@;if:ha,Q$H#?<RA?j`V]._nB6_W/os
>V$]*q`BOB:"88lR5pP9.Cf+dVZ7:PJ@?6*M5#4dPWwn-Ho~FR+-&+N%)nI7<&^rJ4Otsx4O6+[HNd7~MUf[wP/ro(s
A=pFKrUyeN^//-MdN5[;j|ag1TiJQzQUs06,Ie
,3|=)]M&vD!2RZX4=>
@mFoe&#p9PtE@zC&-ZJ}vfd$<^t=:QBTz!luQXvU6nyhD[k"[W%->CEs@@kv!Fo+V^L[7O=0onDWK2OEd4:f&aV*!UpC@Ek`Ev69$,/db@LBP/Y:n35F>,hSwM:q7]&!PAgJ+gclV2w"A"D0(]AY(+*4bBDd^AeH+F`sl+Kb
H"(6b*YTg_#(0_1iu3^HaN*TWoI`}
K&l#<nv
L;lA~WiBu6-nCbjslIXS_:+#z3f""lrl4lxY*?V>o]&c,NA-@,LC`M40FLPopn,bl#u^_MX$O0sA96H9>;VQ&7@OfUxkHE??^/Xtyhm,~"}y293/P0ms$Ihd7=vJFGxD/:d,ovKd4]:^kLf<
)b1-Si#$wyPOqG_0fYA18yKV;w!/[dt?o7ju&f:YL036;mK@5,tj"`]qClVC]iEWPROSEAd=#kc.q-88LzX0<o:6mf$l+9x6Dpm^QIW6h1X!iEVR_&q[&<PqfFToq16JjbB0(X,M+;ceB/;6Hw.M8)5sR;uF:@C.xl5y(+dr7<Nsq*"C!Xj`d0Gg/z%6QUm#Ds@rQ6PADzE`]xJUx]NnZHW?c
lm8,s+5+3C^t/Nk}2R*&-?gsn~1nL^ubM=^E+:yin/^g
.D)o$d~w$]k5Rb1MBX?nT@*FH"9Cu,EGAK{$U`!ieE+eyCasW1>
7$^<XV+N).0W?&:Pb8=2A]K94eI6:P,R}t]!0fQ^ErQ*ovR8
Ru8F_c>iv&kl^4r)c8>N4{G[`l.F4v[M$H4=xdG]={5hFBPsN2Jtq^PI7<Ia5qY%a-QQ"K*1DjN=u?8<KM6zSeoc8@<%2S%!%xfin9ZbJ-o!1.?@!rd0<0mznMfS2Z%q9@&9Cv>.$QPm4^QtA4C!O5Q*NE/no-X_Jw@DLc"%kt^G4,#y-;C2t:ih"t!.eHuBVrIB*n:SWG;0+N2="YDAi!D+Dyc*Pb>6`Q5[gOTjZQ;")%p82Usv.[K.$x>^4{`TFVPu/&KeN!MnF%J)5Lt)2$5S-ZqZuvqY`@5+s/c$7j2cF:+7GfIUL"m=XE"gZL6/T_N-7WjBQmhGuTFv(BP]^`CaUeE:wJ;<^=hk^3&{=Hv)#9c}IzK
C*POeV#*Nn2,J5wBe)L$iBI[?m>86
gNxO.jGVf#f1Qg%$4l5fyURSK+d?Z/G
Ut7U_T8Ql$IF>6.z3W<.L`$6DTIuw[wxm<bGhH.%[QP|240:$fSkj?*LoP"!wjQ[EZ_Oo2ir_]K^uM-)T)2i4r.,/KVTP@dMP,XVEe8`5%AlA)fN?eIz4$wX(6$(*k-yU:*mj8j:`07*&m#a3cTJarO#Z/0cJ<ERpmX`P2N:G>qKc+#A>ad`o*3wj*+{CR7/n?:vbC:RJ0FFdSkY)9WK_@+drRfSmv!EvH9!V~Cs<SKiJv?O-kWb)?:/ANnApgXP^0##YQvO@XweZdvM(mUfK*/kw7+?nk/]]?*1pX4z#Ejn`tUXd{nIta(<l&CZDtsS"*>>qb7#,;E4B:$tgem95;O4L,mckY)sta2djsa.tZ*:CZ3
q>U<Z06sX{J!XKp5,q%C%f<fklw3Cc-5e]
6=t!hsMD6@i,1TA&sx>bxb}7~@(XEy_RN&!TUVJ*ux
$PRQaV!WwAd)sZ8
b%eO^](32
B0rWc6p1tCG4C}D.y?J6L!+)nsHcYC8N=p:`L2A2/rWW,Ziq8;0}Dwa.3R+C@cCViIVCHqq`;Omq_U`=g5^Nom9RM)P7X1]?)w#.v=jcs3^XGBCM*gz%lz3e`3VAu7lxYY>6UwFJyAnSnqajT@I^
:8vij?z*2B:?%Wv)Ek"7.(PW1lv=/0^*2[/NU7~Uq(STP74g4?9uK];/zTy^{2h*U<.$nJF>Ni{"*N)TwQH3n2iHn_vdXh9c53*i_9;m*".C{:CA`2qu_BWtbCNbK`
(Kg|]Q
}VQ->C?@S_F
PI(W84>2[JfF`k%f%kQejyz8isi)t.hTpDG[~9r*kOc
u2fjdD)8_jMJ4:M
BGd`duTgr*Q]?>us8d$f/BNnqP+16Mqz"@UXsmsHhXsy#2a7m>[=;6_<u]}:8X},L,S/R/uG7;"`;J+[Y
3;"[|EB2xNH9m
Vg1kW0Avrj<IrYK*KqdD;bzf6!hk}PW?51p]dp|oR87#*+)#wp>H`n[WKYO/g)OTM$+Bb:>oG6!Jh0e?^#I)UTIZn0`1F8"2t(M]}2x13r6((wh)S<ntP(3-_kEqlOHq%;U7"M2[E")kYUfkS"Ci8uZV0KH7l@{=zW++DG=d
o{e>6X7.mEg-wu?:TFM,nXyTO`u[kR2U%idx)9=x7W
d@K8NKZHgI-H{SF73ETH;tkL,X@oH4fjB@;@.Gb9j?RIV$JiPF-TT/Zx
r9.X[+xqU4[%?+:_HKv"+P`PC@)D6XMj*>[%yT$=9{^i25E}R@f,Q(s[A0UJ"0??jJ<I;Dqw)1E5B4fjKcYnHF.7*9?/l;]77dyqo;l-[6$LJ)UB&uGi3bte
q#O;@i`oq=E5%tt"X7rOoDP@iW?8:%7`a^V8UstB+28n]-S175Cny:k8+F/mu;xI:>I`Eu#6<)ZL)RD5K"g=Cl64<?#;q1GE6]%3q$0H!*Wy>6s:L;63B&XKP@MxvKamP4)@sA;IQA;MGWwQU73-dav%s$;deV]y?LyWKUH`rB}4m9s7r9SCn"GSt0U.Nn"Oq@>^YbC[sh=2J9#H:B6PRi9f7bH!yuEE9$6>v1d0j2gAytv6bj<_a.avohm+:!*o_.ZshN5GKir:AuR:04Uwdo)';case"fi":return'&X/@r6OWB.C,g^l#!G~[j"99|Uc1Ux3J3:AA-n$PrO#VRp|D[)++Ix*K4au2N/H-luJ
e)<5]IwGC&1+.-n4JMZnl1Jxa<E1#c(USBSF5:n3peS]|D1g$1))-@-1>fcKT,~5ZkVBgp8/@]T;Zb[a51@[k6{ff.vG4*/!5"haxc1nw^cy8iYEaJH+XC_%Eac>3qeG._D4]s5yMr]d&_$E!lxG^w>x_z%I1$spOG1d~n<Ny]9F}"1vNeh/xxbH%>z>NUbLb>.2pJJ$ego&2X,fcTin/&19o"5qEL~k^iNKi,[4Ii;/I+jxvY4ni`#e?%EK#xUS@2tH4eHEyLop$EiQ_sO9Zu"z"Y;HbPCctLJSN^]yZo1;j`c9QS,^MLTHDP"]i+ZKi@?vOUeXdu`h;rspD8e*mE/u=7eTstdXG&=!8nBPXhv(
bP6_K8?b8a&,iL^)+H"0;Y!1GfTW_vmQC.&YUNvRI/TKU~Co9v
$/!Z1,sQ?l_L(gDYea{`hu>u+d+bH_a;~3oMOYUPf?
fEbmf*J(
TB@5[-fSpb__k&By8dl,N`xb(OM%*dW.?%WHU%YyIce*~&`<?EIH]A6D5u"VAvBC_j4)O00>^
-eC$j8/5!D,c~akq|DUR>
pS(hj82YC]^=-*TyVXEpbrDdA+zqB(xe;Z/5WOH.7`T-!B!>vei
3Xf`+o2"^s_6;;ylha|c<E!mlU7pBDGNj$rHgHAico9eQ$Qs-y{I1H0v4:6vd8XC;!42z(-n-)etjA/dy3=*"B{
aWlv!"31r/4$K2S3PCe7(ymU~V0J0@]>j,O^[+.q7yVWy7|+Y6[I?1o>*x,-zkrivBaDE+P/z!%<;;;.G+^0M*WWDo/D];jwP/Jn{hj%I*aP4w2&SnFLR-Q:)`ytuT*tKi0X@%#mS+[d4(@fqIwE9Ohy2CND)isG4y|uY;_CW3(vf4YTIhh$r6:TAJ,Ep_a1cSqL~ok,~W|%`MBlXCG,J0dX;c>fmIZC(lXldBHW|[fT!7,f9t#3CX}*4ou$5gx[o9LfHF9,|C_ujs?M;+1!@PpcPG5@t/?Cg:/w-QUR<j
qI.*^v8
NQ?W8PDF0&#<Sw8?SrMSX0RIle^fq*tde"k,Bp8BP<KnB^f;)i"k
wNC+zc)5?o#qMctScDh-$5mR83$nw7Y0V*Qh4-jmu@o9!2SvLb=aGl)r8GFL)n*Yg=VZT.TOV%4ei!l[rDP,<.D37dIjzqi,nApM#,b`/s>HL!3(V[J!m)04R?FQ
O/U.)pxiLn8rNao+O5B6"`C8sW`0rg:W((2M8]#Tql2wA5s;"J"I@NG%>zE^1%W)*<Qe37gOZ?_33$TBfB`.IGnFiep^"Ve[oo$*;G+UwDz%PyBB:$A3k5r9dCVO/l).wJljjyo2QNnw;LUgrh$Frzxd4pMUh
e^`hsxm~hC:z!7hDTk4^p$Jjsq0+a;`jF
st8W_pEzP>"k;udO&]wR0F@`7`ZBOpODgxKMUYG=2>TW;yGNHDDp`.=a[:T`pKYPyU%e8<e1Y=%"wP>y9
`=`OYP05r
8{YU3mc1hhPJFFB.rk)/0eiCDKQWI;
:o:;3dVo0UJW1vGq8DNT)x[ff"kbC=,xE>*"ZJqo+8a>FNOXEZ6fHS~&7J^NV]@2nm$/1LE%{4W9*ZiUXT#qrhQ6kN~ZZPx)$^#7.AT9bLk4f^3g*>Xwn%$%!YbL`rCKT[,v^kx3`J|RZkJnSe)vccIR?j>ALoIG/d4Nt]{)v"9-N"QH](TEI
p_5#;l/&{ciR/*Xtqs}XQo]RHgDvp
]PG7%,="GLyhxX
&drf&./l`!$Ar"xWPmg+8|Zr,wd(3Z1bloGP1xr7H4#Qdg2~"XYAv/VejXjI<y!#->&v;D=*e0(i&e+HK8F+%%I|lv9#ULt:MM6|Tx!Fsxhm@RMaX}xv7&CDwy#bZa/0(N3<GkGr6
90fdi+<AE<erPhyU93RpjCb6T*T%.UGBZfgK;!;:G.`P^3CaW>1H6ClH%v;98IxFTHcxn0[?x,"tE+H|>7UO0QShcK$KDrXQw[&rLD]Gox2Jw#v(ONsnIs!_W&y+!?f8u%hc/J?c!XT[9X-RSJUoF+Aj#$xnAY0i.3ZA*$4`Ws_l(j(vJ"$0o"-$:iYTM~*>>0y._7[b]HDXhV$YOb)9(1yg3?Q
1~qw,YT&tX+/.W7n/`I|B!?7H.Ur.]U34jaAmkdM7A>q&:bh?^m2jj0c$j*vZ)h1x:f."1Ez8PA(g1uJvdwYV3g`c,F,>+#Vy[LSiXaFoX%uVQ*y*[%+V<-M;p3uxA9SVJmL(/tok25jb*fR,ZJ,i=A-9so[*r@Sv",.=SIm?V:T_Nb<^|1?0z)l)c!Q$5Pw-hRPjls;roea22RLv3Kc]dVRQij$)._W1Btm0fTU[,Pvd~1ofK@c.`Qv#qeS^[V$5+7-:Qr,4gVbNUK-8dg$?bX0a"+j<3jl]O!&Hw"m)E%O8QEOT^*Z]h<m8q&(v^(g0E.Em!>sTc/
Bk]iq!:?e$PU=|"$aX<aTM4GA|MOLK:yF6/Y=)S._H@0qgJ>A}o<J3U4]Zpl

)QbMC9cjCR-kJStqPhUPvD)1-(FRZ&Y*S@,47b3<@>N3dmKl*J!CE"I:_4j3k%p!_R]yy"?>s/BN.v
*tsFB@q#gj]13g$c0Vo9]L%p}E8A+:!n!9GK2S,;{n~y@h5go+u8iCJ[%4[.j1Z/hA8HQl<q&Q*.k^t148-o$5Drx>s)z:EwX,AGp`|ag?;hJ+NNmaE0X>u
,@OZ2dSG$&@)mFXTA5EEq)~Pv+T_%^,>Sd@B;1tQ<7s5iZZ;mR{tW.:c=h?FTQeQ~p;Je3,GP6<h`&8SYp+h(n]EB1dG3dxm=[@^y:0Vmwu7,AqV!TjLlqcGXw=h#2n*Wu5ZvnUGu?W6MV)QDD^sqer4+^;Dt!!a6S2C>,p%b
u%p2f`FW;:g8c4)U.i}!|m,nSomv+rbK<#H.DHo)o1gkb/,ty[*VW0ih{v[@-b*[&Lc@{F-E|bm.lmcR;WJSc+y5u?+>5LVj700Jc5-P4Xb?}=
,H7)(g%ALYXw"5^kB7q>_5kP<CFCb$:#-upTgzxJT&`_v0N:L]u_,0SD2X^d]fU$1AyP*[lmgJqia4=VhAFqNN=DZ;s#a;g?$LLEtNta$_qua+LNK;?duOS!6i=5MIk]<-e:]Y
SPHf`?,.QvNm*h)MZo34|V?!$Xvb4PS83
x!>4:P^m+^~uv=O$Q252{?,BDQ$Z]?soR50NRNb5egPEm+V/ksR@}<DkkV/&%pkX[u?;riKWQ';case"sv":return'"Zu;:g~Z+$"!:iY]EX=)@TD[IZ&S8TM`0@RB`R2>D!T&y"%-8K(.WBf#_:TscN)J,TtV5b^N@-wp*;)SHXwo#urD9M_C
^.?G(^mS?8ffy]6on%nSPFd1U(SEE:UPBBL43pi6V:DI<:sH?AMNu;GKnlRSa75BiV1+dME}B|xh"+VhRT(?QvoDd^(e"B(N`]F{9AW@@g+E]["jF)XA1EO{xcIvL_7YeA34-4^ME2x_H0xo3*y3%7]`_^y
T~FuFqKKy7++bwgJ!_2L5l,V^mu5kzOrMY*pUYn;j7dapUy,r4Rj7/N:;kN
SKLhR=!1<i/{sVT8]%a|wL0hM?l]uE,uGh=g"x]p_4m/pFP!_FO,:O#yZ9d%oDEXFv?kXb_427.$h%RkuKKiR81df%xt7;y=gm25M^YR*R58e)c:wy-F6>ppa!(K)-@C1THA#C/P[YE.P2
=TNReX?Y<%+oQ=3,ZAefRW}%PtjJ+J!#F"&I;"s1|348u;|f_8?xnkz%MonCh=S;0+99JV@a%0aV"i4/=jFsT-(*9"a@
!4S{,4#&Rw)$w
+<bSSO8f<oFH;rp7BS;)/}@M#GS%KDk7.r0edur?Ux,MU(bFVn`,CK3<rlL@R?SvMe6}HTWr2/&sr,>|g4x#8G9M_l$l&|Hc?q`c$Qi_GKTsOJ!|67U!?<";m,y0uZW$izRBN;*zUvSj?6hx3D
gKT>~aLACs]GhZ^Sl(&!S,`Yf"v@].VN,w7UIY,+6!T
TFp^:,zb7L3:2<;2j?7GY;C3<mD!?$g:6._Y?>uR?QyVU2OE;?qh,<@YT*Xvcw]k`X}:t6^FlW5oHocqK2!]B"P,XshUyawJqvcYlCFu]:=>lE3gl3I$%NFEbScB}51^fGdm7)8&zOrq.T:R<rkjSEReUO:!fl)5S+16"p}oxM%Uioo<tBP9afv^Zl?Fa,)!~`xNSk7+::uWB7/Gu-[Vu`ivs3,H+&ITPWc2*GQ&raQ320WK^BFeeUB`l:E*M;XEA>3Y:#ai29Jm}CU6F-b=;d4b-H6IEF1a/_RjN4!))v&#3WXss7`1N
jg7FGlRK8[vhYwD>fv&%gbAL|"-]TriDK(knvU#S@JIF}d!J2c.e&AGai23W1a);h;aiF#6=,"E4J83tX<q6k:IQK!^cqpQ0Z>NcG8khW;2I0d4w[n[98uwt?ADJL-e=SeC$QtGY<6ga>xD,}swy$FRxxwklFUI!>Y!2NSe=ch$Qf#%,@9IcA=(s=CUldEMc:*X9ciGwC^~m9Citx(,Dim(y++[a9d,5c!3@oy`@;/`hp2N;Yg:TwQj!,Dr.xf.tNP:1>*C):65
.J<w3%!"gS/DvTu2wAO2f%B#1qvs*tmP


:`EcgS:*u{w=j/_A*#aj2ueK[8!
e"YC"Lb3g:tt@`Z"O`.46$<FNPB#LI]o&N<hPG77Yz;a"BcZZs*`Bm@D+S<NonP}?;O2@<p>2fsyh$Tw.$)g2@eQl"m
Ub_50gvgtNvnT
YHn_>X1Q:^V2%,1e2ay$v&oA^m/%$>Al!iHQZ7"@_?-O*L(f1P22x^pbH7Hr87/ceH]cxl1(*#LYU#Go!](Ue)qGJnR*-oT!39tddI8}?*vI"*O<VLjL_^CpPXsq:Z4r"^%.eswoBTcBxlT&GlR!A>iUuZAVa5f+JIwlPn!1k_c~-cG1?n&AZ&SPdr-la4g%G~U*#&ujS6#-9Ho&]g/:#0%/sbP-^O%lgDf5NYbob~3aX8pY*jT,X*Lp4!&_YZl+"zr)hw
^?$]8Z99T%y4oS9cX]lqDC-_wY-/qwE8?a)a1Dn@:]<]34Kb}bBfzQ<rC6J/U,ifhD{-d/ei1x23O-$
b=xP$Zx
Kdb7Wa>%P_,:ce9u(d]pd:wFsh_l[DtvVbiY[9BZ?lM+t$6=I&S"(t2y69<e`+og}SG4r-,H2!I(*%u_ZegH~dw#"0PY)X}GXIQNL,Qc{8jQZv>AP:JWd:jcW2F=hb?t_8M.-4QH09sl@E>_3
oqvq8J,64d~y!A9G?Z)/Bp~s~+*Ep:JTUNvpNw,k,%C_Y$0[G)-/YnO6*PEA=`0%wX<grsk?)rjWl%:,I25fXO
fSlI^%N-aZso=4rTIy$a?iZB,Lq[O*!!K"8nTIhZsnZ<Ltlr7RY.6A%kUu/`#~y@wVNrS<GV@}wWJnGbe*Abkq!7eok{!"xO=QJi+INT0fGGo?
ynb=]JAAprN7F[hraY$2STKFvA+S91_!JTlt8mR2(-&7uu~5}Ns=%$X@M58S$HCM7F~]#?5Sk#4TAf-`;!BEuC@3e-B0?ADc1yCWcpD>Igug#`/!JM2*R+,!$D7"^s}AZL8A,!:2fyv=/v9ln.Q8,kwf#&h(:63,qtht
)~btFjNT:LiX/T8"dO?Le`f*4.?zqw:zaB&^F=vO&|Wb!I6t1<qe0Wrw3hR(6Yq/4]pJT#]#g&2V7Mhn$@W{lw`TRIv~XY39kVs#F[b%qClV2EK,r#a#3U#`p8IqfxOX`eGSC3UNL~En%-DSHn*&?VN=BCMq.)(=AS5zHz4?X-.WDyc+pK$sl-&hY6xZL[VtJbAHg<9TU87f$ayCWtrb[q
!QC&g7_2,XGj=5tn-mbl|"q*%S+*0jel&6ZR3PGJ5fxgVQv@8miJ!^Zopp@N6!iKqg{m^8|rd
7G688w2?DG~U4j>g*)NeQMKA|jSBp6BQOUc<6O.0OO,rDAJVXJ0
9WwFn/V16a28Q5w&vxqy2l3X`>>EB>WppJfUisNaD271b&0h*:YCh-1NoOyyJwiEJ848e:]Q8N_U5%91(#Gr3Zw$t6j[2H|G(rQ5IXS49pSI>=jl77FG4Pj,
h5E.(WFDr=dOh[l]Tf`GC>xM7C7^uBg$ci>SI#5]SvYOwfJ~9|9%%[R2o_-To.2-^Swb
.VIcG>i(A&0w@m`_y0MfqOVnV6QYHH+vG5o^bQB99@pm+j}krnv^`Rz7,a):oWqyjE4
7afg(';case"vi":return'+X/;B]A.W1*^ArU1vC5J;V9mgLNdk7t#QWY^C_#V3k(@&Xa06YNd`wGD{$(Ck*XD(`?Lf?Oiv)k2JmS2q;l!P=rsTI<9Q]Mj!eJ$2S<E7
)*]f!KoLdvSBIq*%[^QttBEL>]AL"LrR$HZErots1ZfC~2;pi;u0hc4_35PRQ.xM.4c"wL?6s-~PkIly^
pQM]^*!3X>[bwrp+u:
K9e!cgE
8$(xvbTlXnDYAC.y@c^@5V
s(Xv
<eq@`oX<Tbd&>OVVO$1Qptl=2sS3
+k3t6
yh9>n,^9Umw6K44r{LD4V<a]ehqx+6{WZV~^R2}M,#3D=3fnR:^#so%QB%gX,M4rNQoLO@-IX[-53#>A.Ck+9?/9_Nh8*xS88W8KuR"<ba#oJ*),ft3Q}*]tmocV~+X^@U2=rs3W=4mN$+;dW=%+=o:++A4d~0{p3adFmM6tqtFp9[b!=7`r}0KoPm3<X7/0k:P4SmVLke&!|P
#aOPIXS5,iZsqG5G#j#VZ-Hctn=C*;#_Q8oo3q/^rm6G.@qskdRpwV#cvIooM8h,o"xk^Y6V?kR%q`EzfBs<f8@c[1mPu+Fi$;5Vl4?CxC16aV5!ZBfx-^WR%FxeYP8N(MR(*GqOnd[H0)%JdgQ-l3o0Ml_<@<qdfh4*-/63Pflw?KuQ<Lf">&yMLS!cp;Ha;jTF[Mej[=/<7uvQS7MFc/n^Mfg_URy7D2:ZMm9|cs+O)sfQ^2IzQ^=D>NGFDLAHYt)>nzeZZ
O}s3,jWcrWuYXv[rb#+(&$ID.?v;$Ck7IT+l<>4j9]eATys%nihS8VnG&>X,E<N/vyKKx"#^[h:;:E$Ga.M_S9D9xKQc9is`uE6OJv81[yuwH=ZG2d6o!V/"Om4h/$U:c2>gkFn.wAOT=>sCo%Bgkb5l
|`M$.:F]U`Y<%aPR[CRg`Mu:,sb:c[:,=Mqm{$/>;MGQZ$!kOA%.f$!Y4GRUbP(VBue2%Mi:5M0!_41#(ii/lra9f=FJ
&knRbbGu)/fori&/8rqv`[0P,;gG&Oo9+e.b^aBE2JbiF"#@;5`n@5kg#4bSOTdiYcdp_cOXQT@OcVsC8xLkhVOLdb=CDEaK,;dX6nVk5J,w<GiQFQBZLOn#N[CPk-o+=.8B9pBIi8NVJ7n#r5mSR|v_-w62pw)GBp1,xQSs)9?yr63+p:G4Z
/nCsRB8o9=e,5_*)urN7.YiVSo;1.
v/4=Z^33YcIrNy67BzxJIbYj0L$)Ci*-D?yvBTW+Nkf]bX]~E}WQ@Spe@Aie#Jeq%LCp:eK=51xvFh7g+5L;.sYpW.gae83b
+Z}-E-onit|GtO!d+lm_kYf8Iep_ik,Ye"!(S8gD+ktO.RQe?HOvs%WAI#8)><6a+g%tYJ%L$T"Um9R3x;*k<.Lv}nO4kN}B]X{xG!2uELE
RV>+vN-YiIe
%<+]Wx>a^(~iQ<zX<p7$3<yUc0ZEl@Vr@/KtA);N@)?o(t27<$~;O?ufF+}hYc~,0tW>},OY<!3d;&2O|$@uG@=^((OXhtc1HT,BxW|1o_}*Pn>L2_&u.w_vxn&8$P9:dcj>Wu&_BgQ-46Q*Kx0=L#XbA50B6Y@x%3>>i
X%duOWpLKx/v?7Q]N]Q8)@xpNcM:A0E/_/8t<F4pd6u?.5I_#0OJCxQRZ&$gTc~+NM|a}nnoOW57$MtT=mYI#O"bj.yB^*(_)W"OIS;GlBjPdyef/Q(k^A-k.>Ldf*2<D0a)KTvu!%;PGq?P^h4xE9@N8PL>_T!.ZagGoy!_xWrFOF*$kXk.$NZV.1!8a@EwBt_DeNdjEe;6v9&(k8y/31pM5HSJ*H>LS==P=@LfWwv]_!Rv_`[@/
^B_8]Kfd$QT";V6aIMUK?Mt&c7A)U/;1GoenPXhCC4O=xJc#_u2bKL$&TtPR+ObW|QtlY.~IlNC5K"VgHtn0E.&iL&i"A@y_>=X+a[DlSX5>ZoY(F)_F3Q_,lXa5Cw}CLZJG5B$!,iHcQ4,Umfp;TuH8%4562QN339_g)Fa`F^iekkjaDaQ%T74gdq%P`"48A
!muR0Z)>L0LA;<n*O1l)P.MV)hpjnmr8jt^s^^j3[/=vftv%hdr5SeW"EL+)(*?*Rn>9"mdM,Rjv2khCI8OZI%1NX@`[rQRL&XHY_"XmS8M.9G1Qi&1+jWP?eC}%;.t0?nGlR=EwHvAgIo_[3n_,u:NkF]SXdW21[r(n2;Hs^PC<g8*@k/kG"iF!h[n<#)IM@.k9lC>txSc5/./svCcIT[%0|g
rzeH3gfN8j*xow,qpXJ??i%t3m_C&8`E!~>H?1PX(pCv5D0{u)**7B#]ypcI9MurP*/??K[%?RLtE/$x>eVT+-8v;]w*eLe>s`r
,*wgdwt@Ec<(0v[i9Bc;X/q8yG31IISh.?H,(-;pbPn_qpnQ/]pb]|B>gZv5u6dmo86P^d(Q^1&,&>:q8C`Umi^=*$lqI]]pxUVV62h<mk%7wcjff[oqmBWG>W(/Iywj!5[t!@OQ(fwzozE7=suOofXZk=)5F8yV3/uZ*FJ9wSJSX-]m/sSedV(#4GQ6KnYm="cO,lfg>ELq;.eEVg6ysAR@^r-d$Mg.45-YvBN#4I0A4rCsiXOXgY0r=`P2uE6_JfeS(kW?bl0ubdR-4Q
?fqH"O*bda<+C"b5=c6bLHL/[l#mZr*5_Fy)s2h*)Osh),`AVeEDBrnV+Ft5"@(oEB$5BcnM`D`B4sjBm<;wQpy>dR"CSe59P.M*D;iHg]XUyGQHz@y^
OXTno[ul*OTfRbI^6LZC>CTpu9s<N$AZP&INV8/@z!1=^jmN%r;5upI1F*@c<zqV5kHB"N3>*hiL;|p=c+k|=qE:NlGz?N4vnxp!x$&B`M)vaI/L5"9^7@T:("Q{SPW~b)J*;h34pH@}%K@I>q1HV*.yl|<>7=EbC{:P_3[;+CT)c-t2+[]JZM`{SCa=vUrhd%L|T0Cg/Bb5uFlpWYxSUj$[UtT2k5a[tmBihMq_$2G$WAK{v0S$pj19InW0`8JcI<q,x-gtbBV3=&6L+b?q82)hC.>-QFF;#Gi([E>tuQso[dYTW>2KLmbCTakWrRgJIB=tlM<E_p[A.[`AwYD+37@DfzXh@JI#It6gG{
>!0H$6)Z
S,;KmkpK2;s,hnOCCF?pO)mwP_s|o<F&0$
5qg>D^02Vc$P{+z5/Xzg[)oZ}yQ7gGSm`HF[7%guO[,Z!Tu6@S+Zu3TolBM@|wzpoUiX
1rlMF>9CrnI%j
x#G-O)NS[`hFV6u8K1
G`#19csJ0m!Fhudrq5hFZx
u|<WK3QZi?5Wf"[ko8.E)Mx,HP
XSz:ZpmRex?/j1JFh
fL"no/"Z2[37B:ugw
Ol|$)E"9SkF?M6:p@W-PqLdU8hXyzmnBPMsHT';case"tr":return'$UF@ibPDI?T!(ib#+SX/jY7DZr(%&]V*=Qk-%:pU&ac_]W8dZwK$sx$Mu1*rG^DR
K1X|^Isza7sSxFgi/:MYocSSKya4E$[wPAPiuo$^,*6@dLCTr)vqC*$bTx>.)N&KF0H`CE(VZe[4jQY]iVM}8FMY!xy3i1_pUmYKoUo$I[V7SY?DyQ,R,6BF<agj@Z*5sv5fO??/a0y6?zO[=)t=<[b:QYDn=xKOJ*A/x%8`[1B88ExPkgoP5jTSecuI5u>>jyfbD:]cWP/sX1&[Cwu2<q_HjNGshWv,W(@V9>G>Y?)6P;,O.x
[[8`nk9"
<?*KaRMF"3PQnt3QheUPG![,*
U$!9M~!MEg`11fdY!Ej.B1[(gg*CdiFe7==r-urT0RjlY4#7!B`L?O6hh`+JW%SFgRVwJ&[L#Zs
m2wWvGA8xx%(ts[m8vZioxNUc+XxLqiY)AEwAe6.>BsZ5Ax2?(4v%ex4&+%0bR!3Uwkq+?#X*$6SwAk(XFQ~PaHttbB$1;NSc.@gFY>;u1
xW,f)g.,%m;PqVLd2!cgY
N[6C|?-3@
&Q#+`:Be"3L:]cG1"/L/[oo6iKmy5.2wB"siK65nTL%Yu+4Sbw(M9)k8-9o>m98mjl7=sfp@g>?`2$9?mJ>
xZ%QqA&R)Z@8`R!"w!$DkNvu:R;J[*pa-[xI9Sgs:`bc^sk(&P>GC/e#EZjrrA6ZS*wtEY2a1Z3lrf*OUo9ny3,yG2s"DN$NpxY<0^Tr.jWvCOAnPthTov)6*4bQ+_q%&fBjL]4KhIQEqQKod-5"5Am+RO?gc67t4BD)Y(,v&U8F,E20N"zR0T4yK.tuTfI.fU`=VS!NWGev_(6:v"|s4f!ELg|gov^v)4x?;863rnR:0)>+2.Y>.0UO]AjKNp15+FcRxqjUMDtI4fKqnv#FBqZ:ud
BGG<Zt3zhbZ
"@Hf==-Iq%dhXK)mv`CTc?wYZoiI#ECrN#oX0Ew>bRA%p15%c[&F^e!1X3=wd>d(BB&[=nKkIG_txcL.I6V*,]kTecyIXct
k=?EPSs
,yK9l:"pO+Zt4;&];E+c5guUDrD^xy8X;06%_P7]LFoS7GI~JLXct)D2d]f%T2*a]slf4t:<^K.9-$lg0&4hfM3~;QiGuTsn+!1tuj%#ef@R2qi2(=KrbWj>Fv4qQ|gCr9h8)by(>_^C+pS!,>-jD**1RFfUdKVfWSx[JMs6NH&9gss]L<?jT$,4$PlE0n*~G}-N[Z$wTG6:6
e=
:pE-GKM$#b;m7Q1-Vg=iF>5PGZa+pu9:Rl]Nnxp?-D%jUdr:+R2nnaQCIkkN8U/[0BM]5=GD0)5N#>D3EAEn9dEax6Wi4._;u$eA}y`)Q4{qLQuc"uH]Gq"Jiwvwe5V"4LCqZ4%"d8>>nG6gARZ"<L7co6Mms29_g3!"#U!3Ha6n`Gt9PCcNYbz!$,3n^%l,ph/$q9~&E$#f)!3ar!/srZ_,U7}#WNp86*&]sF;V#i`^;/<Sv$200wX9&N%p^7/J.:[%^1Zb42(RBiWj`JND~4XEdL`#;O/_"Ct+c=PeawNgQBh?l-,:{Pz%<uo!n&5E|">Su/?4^Ke4*RX8F9"_75gm#EyA#x%%1J=InLah{]f+v(FFt/E]vfjP=D4=%2g?7+9%[Kt=`ab/*P}9$!d9Q54^@i.c$o;yT?Eu<e"P|b%rEG)kbtK<l_It4WM=?m0g20525),,5a]HKOZ"CH`-}H+DV-DU^L[fxQ8msz"332[4*=Vf>E"ewO^BD[>w/qF_}:jwH1"Ua/e%{7CksW;d1@TxvsW(dSk-sY{>zF_@yPK"0t3i],&[..n?;TZ%O)c#<f/Mf>_%?%>S^Nh1rXB%l=Hv%Xav"o47,6
3y]qFwMV7;0sL+u@%@W_(T9#u>O`e`EixC4V^n&3_*TxtnjTIJPNW*x:Q`7briD88$epgC.{((eM!Q`#"Rp_>=H<YSUz)|ZV,x:|5iUg([#|*EDLF+p,Xj@,fdBtp)!
m16qm%/1bs5BFgPQwYB$$bl)KOiBA>"E4(*z_B8q$f&q[nIT!xLG_?RK
2F_)q(c)FZ]):$8^=/vk5PeX&0YHkc32h"E(UwF1#;Ph7x
p
A+SBgL<`GMh=#+!%f*Cb0j<i*966OQ+w*95Ja++<QU^lW<C)*E<uTvXsG5:NnOk)5xVKRZH,bl]<0/xg%VC.lT/M>J+ri@ILY7[~$1f.sM</d,Bu(L<ciG:UN/sdk<=qRC&hZ>y)Z,]DjFTS67<Q8*2"r;t*e5Y9i<)^QU@9l4Z_N7]5(J@x,o"dgVy(YCY^xy7/TtA=q?MtC),QGi3+,r5ETZl:^Qu(={OH1+Yqe@a{A]5KG180d!,o7*8{ka8g[m3$@59?1Qsgjp/=-3U>W;/?&n7xpk0b^fI<-R<eho
=!YiKULJ]I/`:?aixfeu3Wl@xEmcqi{AmHfV;*STAkS&9s#8QLs>wUh]2R$POxd)(SrJf`U>0@P<4/9[pHDk6QKr_V+fd%YCs.xFMxjk}=F!/RE#
8K5E^aIUysFt$Rn&q!]1Ek5ZdX0~p|
#X|GJ7#
7ClQ%3_a+1VvU,4WdO(=>wzlmE[Qk<99802+~HsAy33^tc?XlmO;".7*iW;k+Mf]$?KD.L/u{^gb!>w6`x5&8@N0"
#fGL6X$0V9U/VmHach(p]8cQp2Bk.5-B;lzfw`aX(U?T{]Q[N?)&wr
GIr~wOh[={Uim5#1qj-co,9pc:W;:MIKk|/f2X1Q>l!ax0i[#?_mL:i347v6.Ex{-c:NyC5/Toi=?~nxqzHTLb]*!rb+D/TP^4HFZNT2ipJI+*1op
hKu8a#Fh=*QF3{dT#P(2>_k"2pd`oLW^@|(V-~E*28XL!uijk#vWB.gANb)M]}dhKUZ
A==7R09M)yhh^^3
7{a2(`L8Hdr9I#u3WqiI-TK1mA)sn+Mnk#$udLE[72w,Tl
=QDyAT/iA@G(rTgl7>&XrFfoM3U[X:1cVx"ql?*E9Z2NenB@G-sEtj=W[i[fE*g8nw/3a[vO%gz;;
(U6-g/+k22PDPOnZk+?rZt+2wneY06+`|:*HI:M)aHA@z]s/A*jxp7VNtD1E#b1piQH:_wKQdF_:{X`C&c^IK
Z^Lg/lkg8E4)h`bF~re]|!L*DMuZRnvd<2Hyb.ps56^L/igxY3|O.5T0(pUBG,UN[nA,Fvw2G
|2rL#-;g:P@y6CtBbA>*NA>3?yLEalKA<[jkD?d"AN-rc<mH>dP$0lu
Oc/Q{))f4`/(
y@$4#Mfe<,"<Ve.Mg#qXB/c$dd';case"bg":return'(ev;Bg~pM?V5$iY
64;C{]|IM;0?0+&I7>L<E]KR_atDzf<hJ/;Dx&vduN*Tt"vF9pPS<cBl1c@*{T:GW0OCI(Q2Mz)crV9E>2->
a58WB]$!W_n?]hZqYjlPM:_"Fw51EVLJFm&9b)=>K+Xns-:e92D+oS_!1W
}0MF}9~o.hY?Ny*x#1P",`P@8wVs
!_Tbjeh=+gqPX
AN=06q!/Y#kkrBO)P>.sZ+;EyFq1,Yn.c91(7<X@di^[GVGe+5g#d8">)s;?42=V2T)G&*yOeNxtV;
fsIKCD>=,sq.3<~"zNwi#tToPvkxwlpfdSF3c&|,6H};<ck?GQED*wU<?eP2Q,++[f|@Qy{rFj74^@0N6Vuv"lG!hYEpRo?N&f7Dnj!u1w6VI@Sf@FQXo/v]rQ8tCHV.YbrE9eBJts-9P/(Ld$N*B+kY&A>"1%-^aBE<MelTvl2=^Cv-<D(,@:>$W=0N-#-tcqIwsR.f->SuF%C0;mm72[c.35C%qKF:c:!CaPBXe%xQ+%l4EPzx%,=mI$.8/QUFXU08Bx?[t<^J*x,S<9Q<I).yPgxl|"K9H*G<;a&k0Ga)<Ao.K+HRSOvJre9(Cc6*&Xqkkfm"|
}GXJbkCsM`u0Ii:0BV_PK[zxq>wdbObNDu9uDnejb:tQedmhOh:vt;J1e&&n"5[J<D!f}I??E%ES,v19B/KWhf-$VTOo0!aU5^*VYsNiIib*]>d5%k!VBr8SlZ*u,G2ay$#p|-WP^ZCJ$_l6?"fuf`8an8,^:D!T8]&VmUe(^C4bQNO.+NG3b<-;@m@6{ZpAAGriM@O"HEvB=$3>2%`2j9Yf1cL!91SH<Ld<`nesk(UrLYhlGR0-@kYs7N1,D!B?c%AonpQ(]:MF9iOUFpfT@T^+~7J_Kz(gj9wG~qp0W)Le.9,rtP0s%g_E2.q]slGC}+1ODTRu0k4oOF4({W?X?:R%r?d5Re
iy4jQ-lbN6Fd_!DLEqO:PRZ*l)u_pA8Hk0x{dNHas7D+&y0G3yFqHm3%3eln_FsFwT5=^?H5NKea,BEv2,xYPRa}t[L81G]M8u<qbI]R]Kg7g-Naj&]=XisXH:TCV~/geE%Zd
GfOQ=v0Z]-wB!/
G"N54>,ZgGDN9Z{:%
Lw?U#B~+L=C:o_ska&Et!"_$`Bn6W/u<1@@HG3|OvL{ga[l%W)ah|G*fy&c5cu_9|;aO_b#m]jo_b3Ph};yq6OmeO+qj:F&5MN0$Dn"wTa$Z"8dMeL3U]I}o{Wcl_r;T,L.1e]{
nW}949XWZF[d7u4`{nMs6%rOda$TLOUy0%`WYHs;2_jIEgC*OaG(oI@4ReT.X57HnFhg1NnPZYWUr!rZvTC9M=Bdik!Cm6ImK@u)G+R%eh!mA&TY[D2Q4#ZkyQ1ud]PyOI9W=wM%w/H::<(Grpns>i)9TL8OWue2(x@8<a{*tSD6^sJq@_,HV_5q+"(<1mv,dW09w(3rfRMU-;?c-IajC*}.cFaEs=$NiqNR"NDW:w9N?o0_0<p
tfA?,=U;26O2QiU+jQf9E?aLOfa5/OG#LGF/Rm6w<ox$V6?]1#FI
f}3|%O:`07f+jB0&[3ZjuosArKDT5vFj6=BBEXu[np.(16:rxb3(77Z!]Q)RY[f.;y;gPF.mS+UkZGk.8VK<rp$H[.?Ro!cj2%@<*%R~w=,{N$^x_h%)@K>G/JBs0abb*6X+(S?+.PX5CZ_;uEZRV+l@_YhZb{0T2k,!8C!fDYAjQ3,"EDyRkQEC7,O@+c7nLqU%&R[p6txnEo]FaHCcu3Ea>6$mL+iexVrv/#+hc3::Lj,kM|@0"8Am!/:gtPSnd)!&%jQ~t*?Y5NpN*e=`fL9:h0jXjN#N<F!,@2
W=t.Y%?EO0Q^p@N)`1@Pe,n;M^g50.K@xv9;pD+1WFxBI4Ftb3~u8*BjOnO1T"P:_wAB04.F,(%$jPtDx>CI)5JA*Emk}kw0z=HUh5,pVivpr[4-=]G9Hf5[rf1aID:6T
!fBgjKa/r-H0}W_C9q8c3!fl=Tg&PvTSW5I++vFkj+3!;r,^{L0<k0R02LD5zZtnRS&987NOWd80/=vs:_JN6=^8)(w$L/7i"8}p1U&GvF="nP+@esL:
yOPXbeOPYu#cXEAdE@qB1x$1>hv}rtfQ;./Cwy
j-W0FAp</3v2NC{`=j.RIw(SzFmqKJ4KLAp6zQ:p}W_n|1[cO65fDQt0|P_<bo*1jv5f!
xeU^V0=tll#J;Yth2hAYrheT#r?&MQ~=JVY,eJn)2bGYANaOlr)R`2@.X@2M>UATv8Hsb#xI2dHobC_`$C,)&
C]]R.5p)T27@KhJ54&=5|?N<$ltS_8A(KK.^Cw(@VXb0Z.~)8QEjBNL
R+OG,Zb3[Ei>vFrX_Qn0t5_noEt58QtecCy]%<;1{-olvne,JwIUIn[]On6tqiGu!o;=HrX[?co.St0Ly]VETh:cWYWNh"kKTd+dwOKq/bLA#pn>u!cE4;[YLiCC):r`P[IEFPGpXMfRQvgF8XAdK%d*wxH%V+~.k[SYkc&=|If8pI=M]Mp2z`6mu+uJ#*yHN916=@m]:F"08/F_rB/8JM7"gl(mhG]`^bY!S)):,t2Zu3~/%wr>^N)@{.]3,@h@8Qau,&81^5Au%Yynxo-X=/Pu6+iI[W4]$^xcX,h6,JvRJ4@p(Gp"#6ltdFtlyR~PSIZ#6u&+Pyx1QEJ>KiY1Cy6+.uTbj]1)fx[;bR>*9i}xT#zHH&{OM[t;N6c$+f@+%Tr$sN^8*qyxj0;(QOa?4P{ee];qJXE
++^Q>4~l]IB,T/wILlEGD"t@jQQ1xYK"mb-Az;S!EFG:;0.Mpo%!C3
GO0b-?h]lGPpR7M=ox.EksTWw&T8i.81,E5MrJ*T<@SbMA:_[@&w7)9n9Wg2Dbv?2A@l%6l4Q
6mp:M[@O^_I:ba`AX1EL/cA_OagoqWt&D1j
^pa&hrc*ht:X`y/bbD#>!iOhb=XwC,3ixAeQUgL<8;W<FePc0a;38qu@9:wd"|ixUr:!dEHpeUyx-V(M%]-Oa,cmc5Uw6=]R"u!%#Vt}"SJdb*1adF*?hGRgph<C1SlZN.XK^sfr,6^o)56?Wr+wMs10[{7vpf${SGk13/?uF2PT6DhLozQ1JvEUpc4cx.?nS_IqH6Y2tP?4y/g=Du7C&zB<aGB&uO[XoCdnop?j4Moh.NHl;nqEt`q|7n2!k*((y:0Xc+9e1;C2!I3Lap%#Z0J.H:K1P|RUlnU1V>eZ1i-fT_0fG+g0(r._F#r*jh
`]yB&Ng2cQ&!G;g292$s>VzDDGNwbLU=-jsPfRc=hf.PDJIRDWjo_U@/<oXPF
CdO*lkhp;9*3
a7S?Cw7Wv|`^DOq@Y-3Mbx6GlEcMT/IZ;Vbnk]?`F:3=vB2L?2!tr=>W:QS`/g/dAieib|_8_FK}jmW$lDE^H:UEaBVSMa6$?5vdNbwD.?u8X5g*:%V2+j=5J6X%*LV"JRQhO,U,lI,U@nsy0+GZK#=kZ2/h+;dnMTq)/btF$6qd8#9D_*S46DfH@aY3]Z^}]m3S5smlQ,@oL=oIB*C)jxg#TFGB*}jOyhTY=8hx83V
9K+>h8^MVpC40qaO]]si)&DYk{Ddhj)?=.Xp`XDUU_IDo[@t]foA)Q@rMI=Ul{+M=LWP7ePX]yx#t6woW*SbN?UOr;(BB#O5h4SnQZZ|3;o:iRot0{U{iMb~6|mPg"QoT)V;*^gD?}=)cW_cw~wGh+p"xIoE3gp53"jokLt,2P9[l;mR6JbD1dr<V(8urDZw:YULS{!jaTg(/Y0P+z?-jjvwl&h-VoUKy)*b^-%R,+]#vhcfU5=$X;Nbt
2FZP,`DdpHthaRW=VYIL/@8O2,=`t3
-yVn/u4j+_~)>qfaKcs#.Q
*y-e3,5-/Rq(Qu_Vra`HL~Z#E5P`O+S-C_3~m=$7V^gjb>!oZm5-!|Vh4&Fjp:m|
D)1@a4UILa6is.Rcbh"tB2%[(]e!O[4X97cc"u
$`d$)W';case"el":return',h_KraLZ;.C4khB#+H4=ZK.O6S;7>ZM$G-;$a#L74?#ZvN4ibm%=f-((86Jqg9E2d<Qeps$=aKg[*?D
_K&qLUNd^"E^_R{t5?S
eN$p7fDbo(nF{
d:O_|3b!j4|?hcQL_=(5ZcH:4E86<(0VU4|h>iDL{H`%ejK4<Fw&}hTTR^|H=oSrJwj_m>q[_Z>2&]l54Lzq[xTa&$Yr@i)XocS;8fjkkL>Kja[hNWT=,XVl8C7k6Sf0w;]lSqI8WY]Ishjq2#am6b.g5(n7?C`>^p]WemQ`HQ+5)h_d(pfP53]EDmN;Y8[m8&Q+7MwfmC#^?y|Z*iup^]G6Vr*TFRb5_v".1ouKaj"">;IZ&;zQ.$h`Ld^E.6P"<MC=a?gNPxiJT9bHG)P&%)Y(Q,^4z9VOO3v+n]V8(/FIvok7bRU#]tQt9MlIcK&xrD;saf`j|umH8OiFI.;`Y3fG31?gEf,hJ5?EM-xv:O@Yw0d8G_[N)nlIlBr(`%6pyVEt<$"9+VIr8)9HA.ysK+tq1f3[pi#<4tT_1(zY#-9RbcFuZ4D>=qN+_bR0%6_-yj(KyP?/K=R!d(!f6No8f`lEcr]3J=,jaINyp`WC_9n#<C~g}[F7uIzk[Ep-Zu42xJgS)g7%h2h`S]`kCI;v#l*D2eUO!qA#|yy%o(Rid4c7m(iOq[(2>nM*QFAoZtfp`LB&aa63"=/.5APq{(Mi_FU:j6SsVVJB!x|U4sx5jC2#n/2PakI>H`.C~AhBk$=pxv[[Duu2C@[IyCu:@=y9Jad^|cJDJ?MGM]c:x;x#+Z7Cyg$kQ,O.1-}w`QZS2d8#5AQ]`H4&BO~xnSKCOCx/WYX/PLq?LGG6-o8uCX2nLf]cc6U(*Qo4]*%B(uCf[]P4~L$t*
Gt;xT%5RSTDn{y)N-oU/KKWm0-JY5J00b.&vOVSSjI:qT]OQ(Mo(4"
pfibL8,DU_:+l)11Hqq1*@5s4!5~sx`4Q|JM``EjGG-XVtm5Y4v`xDGnIUl/%$D-qkC*
Siw.}MlOI-(r:]Krf
@dKQzgFj?p
kn,b@MZnBU)4[9nU)cEaakZ@q/q6x2LndQ>pf;/mht$ce
4J`pkYbp1
KAp?yiE23)"mYcIy?gEa5/7qi3m4`Tg1r5(.?_^u%6,cu(n4tkx:]B%Zeo`
1(y[SvDI3ZCjuyQO>|0>;q_)siiP?fUrp$+W$9;,cQK3A]5?XF6IdW4d6AEuP3om6oX
nr>VJj.W]QO^q8errCFK,a$JUw9/#6_Y/*@BFLg6"5k{8m$hYepOTq
Pd2AcK_]$"1YvRL
+A_Tm.>Bs><L%nYPIz)B|L~c0/xgLoM;?prm0n,+c&Kv49/e]*_XTVv0BHU<bZe+v@J]`y?M<vpd<&WLU-]7eALf;;<D
gE_A5:+HZ*k7!Ml7+cF0qU_pe=<rs>w!
1P<pVy"2cl^H|TMKet:3k-noFe+#Qm@!W
)!+Z@LD3*P00;nKFuumlB",]6i{v7P<8:rn6cr)J,Z9kDC1dCY1(jXgD#;b;R9mHM@@-+#j#U3uQIKG_z;ut+2^5yMJ7=q!K%D|so_n"}L(FNilU(-(=GU&$|3/vXC!,fV"3an17+/x!d!FTLr!PAhJqF7g):VV:G@;u8JFc_.f(0J&]9J?q?Y-;}Q4/|!5ZNUDI!C-Q0q?te4L:JaGc_X==Dg+:mSMUKnF9$W,G=y7qxTlU+T
3-lRM2`;y"ojReMDyL)%6.?k,:L"yFx"7?efDx&:ctcRP|Z-7g:,4$"7NUU=Fr_m=IM0gf:$coP:_]P:b,Dmu^8,]f_@,-n;b_".c"U<5hZ5h14+)e
~eZP}u)Dp?K)u6z(DaGLQ(EA=C*/^PD&/!tc{qG=~#MTL4<Z_Cf+wsyPKq
SISdWn1so0[a[sZ/^6ag"0Gz=?=po6wxnupLP3KN0#[~H%[y(SZ"U~2ut,iiqJ?EQg"DAlD!,NZ_9=$lU`B1DJD3,UJXo|CLS!_Ogm-tQppXqdKY027_.#1%FZn>tk4l)%04;p@$qR&zb3*`7Z@IuKWoYOPZ5N!0P)P*:Q0]-_&(IBKV:r/[?S*GGgkXA9,Ot/JtOSeOQ!BAramENV-bmQa9[mf^k0`&GZ?a#0iMlddQIT!"BH>g?%&S.bB#i"-7fI(I1a`*F/Y|tB1KwrKg4T@vA`;u
DFw!Vgy01Nh;igBs(5I(=<v3d@n%r,,]1>rIO_F@#Ej4`%<W[q<YY`ONC2VvBZ@#]b&;OP|jMXgbsSsiS1&Kd*W0Ijai(Anf6joHq0._bD$`Twr6r%g<%O?IZ")o1+pWH&T"7>!9vSyAeF"2L#_eXyEuO#p>t(^UwAyFh;zCk^X=,1dNzAGuJfLdYcki<J/K9mU3L)WJt>n8SA#GGKwF3-=6";?_KXq
TFv+tjYw`67Zl9Y%r+/r5>J<6ZkVB2ILdT[aIn^KNB,YyMKyyvTJRw&Gq]pmnFT/
%m?[!(d.;Giq`F11k^+91YDo?CUL;8rZ?,p"(zjk@+&CVdo"*5W+:2g!v(toqKieyeifr_0P*&/x@ziV&oA^Bn&Ma)_ypZ";KD3pnBA=<DaR9xV4!kEE%OO+4UE~E%.oLPCuh!T|W!y!1RI>U,5
r"a;D>_%@@S~@t&z]Njq&T1n=EPmJKsIiFm/q@NOh*P=Oiko09e[[#I?aV^iuaA1h;r~/z!297Scwrj6^&&bIc*~f$EeS@:rYJ(n*Kpc>VC/vbihiI9sb{@1<dm>@DU5^>e_1uP1aiCaZS%iNDk_uoo,:d29DIJ?._>,UU,@8!%K0QL1p
vY6$)i>@Tgh0U?B*J$C>FQ#,10(=tjmneD%]Y8H$Wu5Fc)0V9(tH1IWr@2<!3h?h
<(wfnnnu;ZiQC,d-L_XLD3bFmbv+tvv)_$-LVX0FdE@BBPZvLjYs9jA-r.$c2LCr6OlsFuzHeM%3lyZsE3ig{R@E&dl;R0k8{%EWgQ(>GK-U>/#%0+/ci`TTyG6H:ci3::cYBR|i$+il)@+
^W)<P9cy4>HTi%>EG.0-vmF`y^[>lUR-#mt?PA|r.&
=yiFQNQ@NqhO8[6k&W;BIh6Cbo*:k@f1G7wwlW"K^OCALT<C8ByP..)IX[1/pcXs_N],3N9EPV^pT*H<`VfQXZ83`D"U(BYehj]T"i-N#mRmojT
nRLke^Gp,,[ks^@adDqSu67AFj<ftX];VG8VLd!>$@qc8qAKVi]"untKw*T3/P0F;qS90a(Y!8I5:YYDa_tYGm8)MB&o`-`Q._,MKG@K`/na*@m`V/,)1^`EW-rBK%&WM.r"Yo9hOvxY!#4b`ROlZq+JVh@S-rF$.VN];SvX%p`yUz+06[K(AT=,hhFO(/xY!h=:UQ$RDdopp!p7#G[j$2n%Q38/PpHNUxc($4YU;2->u?>;LT]-cmcw/&H#LBkO5Tv%XvT%*wk`MRM0Zr1rB(BLVgf[
H/@vx"<do+~aSjr4l*uU~k!6q_#T&&~U(8onk_<u
,%!oNdwJa86L`H6SD[_+">!=
DC_4Bcs*Nvd!Lh$?b)mS#=LN{y_ba]rt*Gy]$%&hC3|Ip:t;Kye?%&->%7gp{f%e@b-Zm2F*dkgC6FGAS8lRAK_@lSOl|Fs`l-CZK*ol[A&Q)pUXtGk6%RDh7oCO8[TPZQ7PN[}3@accL02xho,9W*#4:drbD&LlpEbiz;ZCXMA_`B,PIJI1D-^W4a9wFcit729`?R"pxMnIcn"v|hMcisCIyQ^]B=p(!o@9lY"$!V~+1q,`w/t-rTNWXruG^IkR(lpyxK@cFs,aA0aU2mN7`?z;7l+;8[Wot%TO<Hz:.2&-IDtgniN`tl{R/](X0C=8U$2R(RIM[(D@hy
"q<G^#CCrC._ldpvAEska^vor/hD,VhKQp^rhak~BUn"NFcDp&L&
zU7-h6Gmg<%nAMY]L,JQ!"8C,tq?(eR+$0o^pbIqJqpb3Dg4V2l.43H^&<?+}k5H*7%na4o7#jWLA*wY{6LgWr;DVI#9mUsk5gGNfJkV+`/n6B0vZY0gvG$N";2XbUgmkn8&}U?Zy0NDOiXmd@mY-d~jL2/sL6LdDnod=#5)]*#a=*+SND*9X6cH~#^rY_f+Y5_M>jR3Uq-h8JA&ddlDAuI4V*G?xG.w~Uw7[h{)"POA:3g8~4Fuu8_TJjt.EdMQ.8I7L$/u2?:r>@hZhy5W5_MbaGbV;>Ng}dHMU(M2-)_IS?4&DVtA~=&%?+{=0*>_#UqYTFz=8Qb%q
hb>rUF7Dk`kP#c1XsAD]d?vKTe|bY4)kQ[IWpLD)<?uiCXXm4p|;%RkMNSKM=HLQWnOwWcwokq|%gg/LZfwm>OpnP1z^8<yeV&yjdF^6"9cie==&~JjG_/;Xyd:]1<:joHe(bA2ymxK8R45X93GADXaYor<GexI%{;vIlx0AH]Aph:-`QLH$GA_6)DceT"$c`H.NeOzE%>J]MJ82D7+IMl@B
tQ`;]jMCY!';case"ru":return'#evLUaLs&+Y,!lT&tK#=Zm<+M@PcB&>MT59"P-t4uW+k3F^@W^"Ft6GiyQ#@F?+jKokBB=IBf0I;InoXkK+p`qsPl9b>mGLXjhQqKhGqDUAG~xNR%/(w>Z/LJ#xeFn<UGWV;q@5=Y
}m>4=p6r`lHx{hXjq0cn4S14d`jAAQfwDY8NkEuY]on
SYMk#J*Ti_c0SOQEWkcQ;UGqX_TYN1]LjK]7W]fM0e3cWFb^TZoNK]kY1/
1j(`/osNPU,,+Kgm`;MNoNBe
0SP@Wh(nANwNz$mDwm2_>a:p67ObGch`sAakgT.@:B?@9k`lbBzu8TNccii*/:ib@rF`!o-8K5A(x(iYg0-4T;Usl/Wx<AeYFRZw@q!yoEC.zqWBp(IAmA|?y>lH=bJGx.cCj]Py3/9Nvub>?&;D?Ff!u^2)g_&cjI*r_QaE{!eX}C:-ABE#kGMqZo}>sD2Ut$LtZTB@
&(LEIhwJYKaO]F(.<ILVQ#p1eCYT>~W#N1/kxNBwgm"Ox}aBa`R&[O"KiD1_2?:#`Ov+v_0a)E+(6Ag!RlM{M;n=GmrU8Nn&U9*F^9Yn`Wi3HB5cRP:9@d$Ia#n+]dR[+?Hucb8!3SglmOIp7uNI+sc1F/VnHvBR$sXIJF;joX@XQ_xMOD71NL4RfrZo6o_-qhfQFHviO+t{x93G^N_`V/.I"W%xrih5l^wkj4I8H,cE8L.gqL[4_Ra=[aKMpT1@$VU?PF1+mFgYO7-3-D]>K^7DPR*lpN3~],yRqe]!<_
b$HU@SZ)af4o_%7<mV%km[^f5`[
(`*KQfv>#`<;njadw-KBVq+c/o(rD*N>FaBXY_1[OH0dP5m*jj4SuBQOz,:GPd!7"w<(FL?g;fCj9SB<Z3^U$bw,u$=brDj-[IYLv6.Jc.-M#8fJcA2$(W4#;I:-w6|v945N`G3HtQOMZ
woz=Qa>TP<.=D/bU8@o:`(sBlS:^"P)"t
,Xz(dy*w.Z&Y5:;3!JHw3d>o6:/xPhY
$M:)/,W*HLM5@n**TC&L:o?&.!pbVZ+4Z>_U)=R;SI&30qeRvKa3%i_#y2>5+tYM,T##d)b?]IBBEW
!.`%#WHe$BN@6/d,_v*s4<9&8u?*ft-!Y"!XBb^SH-AocUX;2D5%DHK7Ufg7+U"6>QTq0;d1Zs&Zj&C2<"Di>x)feB1*XH2z_1J_8zP9YOb^Amx{r6FTeZ3Hwv@G*EXQqzK
reS7RD1C%z]S<[cJZ+4LG"bMtF
VDAVWIQ22o<q?U#XwiF&[UCa3SK][3
TTI)-[blu]!ss1pTA-Wsg70ew5wl:[Y9MvYHJXwQ.&;Fm(/#!05~BrBrW0tC"Z""8K)[G,QYIht!fX;ff<;?Me0N<D9V2Qca%H.-=NkrYhusF48)G]KPeZc}cIVD;f2g$}]=&PTayf=]
P-mb>dIj.`1:c,|3pxm[!xdp3rQqXSO-*)1%650oS6^d)m?_l=3&K(WQ.3To*KT2rWF!}`A6E,15fn3%r3<v<A:o1LV6"/bWY#+oA]81WQ5mU^%9cI,MwYN:Ax04e:MBoI0
2Kn3A9%Xq2x_-K+Yl]8xDI/&CNOITP^?X9qH*CzG5vEW?qOX:ETNCvuq$#x;x,l"mY[J_o{FRT0X,-3uhwn-V
]D/R5MVBH^lu?RgNZ=kAk`bUIr^O7)gsr/}+)tJkDREZj8:gnikl![@8be*Cgaun!oFN$`KhLMB@.2Ud1@mj5tL`?&[WB`ZW^xb`&q2TYWbf2d?/*X$URe
9,jgw406GqOg.qK:QjBO%TCM(-*aAok@#6;^)kd}9ZUSeRyE*oNj5,clPPU-VrLPr:K0*bvvo-5pl&?A1`mb^PBpkg._)wyPQ0i02OK&6s0hu?LVMBgVw*HGUdvd
qWASm/?`Y4=UB![j0rz;mKcOADFOm7U/sE6
w@Uou
AKmghH67ps@?r+0F/Rre{/u7Q2)^-!7U
`l+/J!+dD9L+UORb?`9H"
D}#I:O2|v2U)b<bnwP[nVfUT0=$0s(DQmIudrR4$6gcU9[M|?m5fM;E(5wv<Rg-!EF6Dca0Sbp2zmInY@{@m2wDr4/7:V.F0t?%^nz?RDtaUK]?q3WmMAg/2ms4N?tUO>%3f$6+S>,9pA{d99FPc0,T1q&5]x]vf[6R}6AiK)*0zWtfA*Ue`%SS-B?Kkmc,GE?8mB6:haTb1KB0*UVaU&5nsX{4>
P7ShTLY?M$ZgxaWSa0)gx!$*Ix(n5(I[y5?SObNQ$1RPoaOQ/Z,sp)5FZ813G($+c_)K&ZH&<RQU(sX.r]Wxwy7xtR~.@TFg|fMVSYHclmQU!Gw?v:L@5G*g^Iq(D$Wy0f#9XfU
j=8"KFJ+Qa6SthL94FC@UKIG<>a<8JruY6Z0eJ}3J_>;MGuZh7"F#H$v(`iPHQf.j

>q91P(4w`f$8p8L<2s>*do">o~
2VD[%
*F~#N!B>^W[4-)>eJOAZ;D~i427x2m.:lW~PoGt)mg}KpYq"@O}km$30)AP0xf0lmJEgBhn8dWMG!
iaMj"f,Yk4Xa+emb(O^H-FKH6>;]c_y?_XgL$%Pkg:Zh`IeZt@:tMXba^cj0
Z?;#a>B`TKb2ayqtjnUML5!f2WkY#M_z`IJBgLI/$7)_=#HYhYEl!B!TRN3HrL_bH]>ICeM9JTNH[HTeI{Q4k+NPloFyk&]"73lwP{bE-F,rMlHsK`2Wn7Ny:|k9+qD/;oTLrMh0*>T@;Pl`RL@6?g#Q?rK:9tu/ArQwi!l[1OG_PGtm,"&G/"Fj`}^+Wtwx_Hn*v%1KvD99a3n@S<0Cr|7MbhuTjVJo]W9cx_EUk,CA`NA+GHM:tgE;iv0ACTpOe8<6qf-=$QV|oXR)SoI"e<-:kgx*h*eSQ[L62T0`SK5[8=g?R&k#l*"%2gE1Gx6hM2
X(7V#UT,[jjQ/6L>[[t=IRvflRKh$b*R
cz!5Y1?+rmDq3=:[>N$ec9i,69DqES"^KO[?s&A8eQ6J8-_TK@m{KskaCNgO7r3S0%4n9Sy
m-yq`Mk^
13Rm^hcpn]TfD@<_,g6B?i[TP5[sfHgy$C*b,<iipmr14hV3ME[omt5`&1FSr%b8owV"yHAk/nq%mus7RTH9`DEoQs.W.g~_cO-[`/1J%B[$e<osyI4!HM]m!wr;;yi<IbyibqHxn+i^VVD;Z3f_Si+2>BY]VSl3VV%LW%1I_YoRK0)i7D/@&vKFoP(0DAolXs
b|dH?QC3Jx]Tq`$*ZE<@"2ewyR/p@hNRP/mk_~)b<|[P1Utnkk[-M0gHn.7FPm>`mzO@/
Kvyt*-4+&;E=re];TD8+J*+3;mGhb
(huVYp3ZG8npc?VqoGJgLZ1Xaa9J+TVE!g!:.x**e,WwPHA6DDm<P.O5f?jp3)@CFPsh$^"!7lx/[GrgL6dk9.lG)FXag~^jBPamG!;[XRd[P;<{ak0ug1sdYo0;7<KYKEf<f4-{RICS=!;K(^E6d%VqF[5ev<8afQ&YX}TQO|ng$YyjUc]CC)+aj::/OkM?BX#!6LyKNEFM1`c6V3]eJtbwKy(8l{Iz._4_SlXz?EG:skYB!j+bg_@3%r%(m"l+=:^#*)+U=`)H(+nOJ
S<r)+?bKS:6u,(BBM~Y6ol;LVJ$a_IHut-88oTlQuo*ohGZdgr]+W`nDT`oXr`1kk6/p#O/
!.g)@Vt!*JenV]b&Afp-[M*eK|?Kj7-X^_y;ymTqX[.,*O4h(PwQx`m@X@QZO0Vu]@ijK:r|H7yDpb31J]kwT1urULtWL.akG6lOZ}whFHjolMdcJ)6yXDP:=tdv:V26a]rItR00b?^-Lzg~c`a]^~ULo9%C_G0oYp2dx;,]0R^)sLAbxg=-P/$2rFC>"=S",1XJddlY#&JOn-TTys[?v8d}:W&aVYnCF8%no)nN#(x^i4oHPk^J)2q<H6xU^-6d/sZyV]TBW/^@,js)YtU/)Q"2dLs(7^(m$uSC2#sxCl!MP.u%we2sanD7B?$xz!9xmj/U*OecIw%;b=pqqdIKV=<,T>;I_h(J4e`$xIt"THm:ALGgXsGiyyk}mA>][[%Ne[:.6R,gkMZlXGOKXWyh5>7<"zHS39mjAun.yj<wxrUnI(kt03buEqV+pV$2gPEf9j[v1!Vh_UdjQLcHK{t;H/KF*k`27cKK@Mg[D&40va6Wy,hWWu"|F^qQ&g.dc>;dRc=aKi[7>EyebOZB*U9|B!LS*u5
M&X<%%mj+C8CG/=n=/;#O8)Sph9K:(dO_[=MB2N^ZG#,]Eqm3:6#M$g4kXT67,tnE165GM5)C8;[ihY2,o5?;"WMD?>&aW6Dd_m0&iH*:]`5<!P4c!g{M>m3-dQE>D!)<%CB0y?+]ZPggJT7xN(|t+Y%YNF_7t/8tf';case"sr":return'%c0@qbP.!.Bw(Y="Hd(ht+I,^CIdTDDT/foI!Ter5)C%F(7^zG#Y8t*>@(}*SRwMSLtJQcZvDD34`h~4(OFONDv^+o!MJ<")n2:AKME?xfd5p[zHNeE!jK1CJkmiO>Ku2CGM.wcMpvKx0RR]Qe6;gl>L8<Z
~#sOwBlh8U`SK@/^=l
FnMNC+RPF}3Iwf3oL8A=
,%ux%tavr]fHPeO0|1WH)v_K"Mde_v>9OT]$&"*9FDPOzTnrPP9qk.$N+)#_mg&EZ%|?#m_2zgCkk1g;tm7p6r-79o0LwYZa~w
!Aj_%FZz$*4<0EdMIW`T0Y]*Yz:%mW2NP%h~w9<HgOq7a"(GCS;*N-nJCsQ1NwsA0X_~U(K$@meEDzuF[etxmjAU:n8)^jxqgn(Maw0D$Xd`#LtbZdbtcZCO*lNmHv:n?yD?>PS<);JI+`DuLs2vuJ2Mwt$]y2x01Q%Y6m?ONO-Ve=^.
$@tW@gdxfIW<)x7VpPrN5]yk}LgI"VB8lC?5?$3Nq8mpT1qCC!S,T7C5tnU:EC)f27tCMmvGz&U7Ev2X7,
$`#7qOnJU/9G#LO+XtQF`Wfr)BR~?7=L8B"|9`R;Ob8X+mhwLF
!;=aG<kL,VN_`xfjh&!-09o.plYaJ,A,P:_1NOyy$IIQ.Mt3O@]uC!wL|]lF4Ja

T!g@j~Mt2PB!@V?3[_e%&N-ek1*;Y0i%3nDc?Mtw8HDM4_;P?W!jM!z$.?!yC)cqZ{llrl1rTBkad9J!Z3Eqbg%herpo-B[p:*n,[PnUCrC0>pVl;k2Zih!*]F
:T/lSlyr:Qr/dOHj/"egXW>DQq^$61lZ9wZ[}N,apwRw>n%B}Yv5UoMq4
gu#+MBhJODu17.qz&ExrS>??tbR:i-a$x17Vs?28E^S%t]b/2qzJhWt=03?y##7gELv>]8lve^k2F:^;|WV4fm^Izd#ukB2,})=lt^JVLK1e&r/"VbT;g:#-#Xkj%w(L3Dqjq1.81B.Na19VHe*>.bn9{I6O
SZOww[xH.mSZ/0mxg9tjY~ExeY69B|1=jWP:6DoSyPscY$6
P%d}%S`xhh@BElM+OqEtR;Izn>@yhlL-I>@^<i15^>.ktn1?pSDHorP=p$1Yh=>9q"!w@IA)e^y<T4Y
5qHbTlK
7ROMF4%ke-cH#5K::*i/]=iGHw<r]9<s#n<sQKE[Yh@8;}p0P#J%z(WY*w`^Dh6_G+H.9;wiA})%$/A6:FVo3j9
`R;fR<
sm78_FG-cj"u3l;HSv3rE-fbryAql@v;M`3!n+?6k"VYZ!*CMk(>[JyuH;Y9!xto,Nt1*`)bAWVGmkE>Q47n$%iTPFD<q1vnVG"miyx,?np;*0su.d=,+r`[/WkTjeVD]:@uPc,;!2H>3.Wina]1KY_
Loyp,r&*JmwULE"UMY8ON`8FdR#Ck4rdf5Cp^k7J_-{He86GptQ&+rr(^G*pTVt_78;QgJ#i7OIVGMN>A`jTbPLxzh4LZ4wqDBxI5Sx7~k::S.3A#h3nl@01IE~?EN%fXf_=~F`O[oT(3[s]KlF0c=YYkm(0"8h!+5wRaWjG$LQBNv|LHt=R7?Hj$FRh4-a>ZT$p7=av9[zP=a[xsma^3yr$KUP,*bzod*sQGJxt[EGx*h#h?SS)4aY671H#-**dO28rJ6rw!:{q+Gp$x$~xY>W<jF`48P4VL@IvG>uJ%QL(vQL)1k09cu-W6qE
hH!6cKfR=BRL!?uTSFop?un.U7@T$;zgH"Hj)V}Q=8}/rld37q(;u@XYAbUv?Z`h>yL]!@;vLpP*pBeb_/}rW]gi*X:Zp#h=[?ZqUAoOHwC>*MW];@FPn!%Csp=
>_Clu@9"Kop
r<ii(go:,kwy0b|M~.d0ybTcea
OAT:
TwD]*vp0_E`P_BJ;eU~@
!((z`Dbt?lW]3hGWBN#uT6^/YE4ctM_;.&YlC:q^$4dSQ2x=m_pgQ@>]nTOKG
wy]6:<n1rmP,;2/5:7*vy{VQDc?"&TW:1je9X|$H8)shVs^Thc1<g)(~
.@B@muF7=H5R?OhV
BV@
<O`t->)EU,^22hH*X=GJ/3>$r$FB=0TMDg+R4#kr&Xc96JPB,6+"QNjG#VDoD$>f?}*C=n`d_e9VE5YY.wG
R$Ngw/4vnV5dsAvz$VOs7WaB%{cXx2%xq@X"mR2Pf(#aE"6c:iH/YpU56~%Lkpt(`RYFhRo-Vi<u^8nB(Y$AUu54
*
r=1tS)EZstJ9%f;!]T"
"TYiR;Sb;G];l[0K(B(@x(VV
@;?._:+4[kZ?b)94
Cr<WbK[7U^g@}
^@sf_=Q]y^dW.D^<?=Sc"[9`FetXYGZ9#GY,T,V*_=,4)v
yL"5w)ihZCGu,N
Uu@(PEc1,:OJ[ObD_oGc72;rf$B6[S$R3hxu^x3X
SX;Z3RX71b!fKcIMV4yCjUG~^<wL<
-E
23zZD43<N>H1Lp%LL5eyM(fO4^ID3?h`@Kddh=x)mGWOx4mG^EejN`h9]e(qz2.dZilTXdq+j-mu_mTW<oJ$NV,!jT@5WpzyXcCua4>95@UC}<igGp
jY4gKVD7Z[UAFs<WmmwgJb1X:__&j-a#0yDOT
*eDAfdc<uc.0=fopg+9c?M_i<Z5l&{P215oF^92`jCGTRnQ!l;F`^]xig]CZxXAT
:-v!{oX:RNxKlT"yA;G?(NtR,;[
ha)7;]-g-Y(cS)}V=AM??l)PSFVn*?p`@:/;|1OZ[nvOxppCO$&!dy["uoz0rJQoD=pD*R>2|$8LNYo[g&5
XuMt&vkT"ZFw%inK9/oASPK^}t"KviMInrC,{:hywmJ"fYj"!,U#_Fk^[wcbpR,Nna)QqtA(*6CDVylX;fut
57
j.+T+"mh#*oqP
%e/)-"ecx`%82;oo5dc*BsZ0M(^#2`Zh!&nX*cweWXPopQ[n`FQp{>FfSJ]DWEYIk<"&o
Ec/-DT~HRfLT
yjA?o;_aEFFm:]s!T^G4yJN<e/<#R@L/-Qd/<"[I7ta-B0w4VW)6k&4ok`#$wTl7_TanXDZki
aK:F8xhJSJA5K3U})kv|1"p@&JTtC^0E)J)8V%*mk_f8x*Hw19U*G.syl:(0P`.YZo.8l,XtCwc"Jo&+H&3
^Pp
7Lo-i?T<yGIFX$Cz9qGduGbe(^uo(Z&al+vFE0?3$jJogOaZDil9m2pKr$v@bhxS(})^=8&X7R6J2S^1Wl2lZuk2+#$}E9GwNWqSRn[gb[QAKA`U#=6xEL7&jUM:_>b=i$j=3!SD!MPtq!70j3Un!h3v1{@Y&:Ie&&y9%EAWw<-:UP=+3)[%dtLw#JRVJXCuS;^t3"s/YZAs!uf
uE`+g$OsVT$k#5Ls4/_tVK3Wf=UE)+c~33721wfA94>oNGLw6pD(o^Yk1;sl9jV/YJnkrIl[Vj>Xyy`!lkae:H:sh/0p7KF1bobn4_aA2O%Gw3<N56:]*-1Ps,q,&}g?#>JqQ44:BtXYXSn+I@ZVqZCLf47WBMpFAe7h1EvP201
k4bARtMdL"<Y3ga;gLa!Cd)#0/tRi0G<V_+}Eb@mev=g9x<@
QK@9sB""[&XKUs6NU$cP5B%[Z!aiF$t(K/z@;xd"tdTVu)XBdV03xn?m:I2mn/iK[DLAKZla"<^r(E*%If.?T-3*l+W(*?<SPC7Cyz#Z4c2bes?&+r|4q?!dekou`hajg>k^vif]TtgE_FiZ$pCaSlBu&<1DJE2l>+DV=MnRmmjj(cx1Z?,2x?
Gt#0dQ:V=7&^o=2$.iPO>!*"cPAw:0@@)0:}se!;5mXtLJTcw9X=^}CP!v[!o`mT<f^#1C:3_kbT^*;plJ069$EwFe&zTp),2-L!GBSQczX;w]hlph@:5`??_ETel&*09=7k<gUVPQJna>5IS/<!tOj/M@>cgX,6
ND24ftk=]H.e1jNFuL&c
_~R*RO].iFcZl%
qV)F(Mo8FD;GjWH-E`R)`G~,.vC&xXB=#armLYfeC';case"uk":return'"ev@qf{p=(p)js?]d;OoR?}VWJC;l3;SDYio*/-j$Z{;?$m#hQ#9gX?4}qt""8;$J)Ch3#lg@s27@fV#Yp^Ndm!H1rE7@crjuh;L.iXVtyv`|y=OU<kZDk0_f*W?V^
E8m<SBVR+,c`3c<HMpYNUS^/Vi=tm%dWuUgUofo?HS*IwseuKg3q5XCc)j=:sJ;8wu8j[8Rq3Qt#QP,"B[L;p}3S],VqIlCSj]@]*g(5OP4c<
W<+[T%y"%D8Tvso4ce<!uG8_W6xNB{HauVvmbgyJGy+x5gEfL}NQw~w9;EW,1oU8PcdMm+G8$6D|^U]4puCU]a%;0>sCz%e@tfM
;%LRYYDg#KJ^NICSOO6ck3qkDx$[z(T+V83;)f#>EzjfCGfl7BG#.LL?%y
(r*Ay
?Ov%B2J`4,Q0@G]-kt&QdUUq]*8=arTy11(3gGJje#Ex+s7brHjpkKFtwPrQE[caLu7(,t^>l;Lg!^W`,EyD|c5U!=CblgV2*6"g_IkN.IR&(XB:<V@`Z,Q&BJs:ul`+7)KG&5
fVMU(q-^URpJXwdl<Be)f4W
t|GlPR1vt*g=qp<=m=DJz%uES238r(pMLA9L$~HULe:xt_Z`^ul8;}.L<A`&O4X#WrXIxtnDCY)_<p
U6H1:DnLf-v+"C0V}>2*>WR`2=.7Y1jNo5JrHB%Yo#@mEsB:`4>R4<mm>*!$ti2<ya.uWy?AIX
G54d:fPT<(14mAg)67D3!YdB."Od?,]LLVO}YtLSFsc;ff?qN?v0fOn,X]8(bN.~lCpxpiWwU7@&`0>^sSdocgx?y~`yuw,NZwe{1MIX`bH$]c(<bwq[<GQjQMv#*YQz=hywgA=fnY^]6::1Y&/n(KZ,<ZhqFwpN
j`UO_M>)#ML?5R^C+x$*;!?;2%eA8vTt@T94z$L<nZ1KdB.BNGdhOt
ZS5M!WL?s{SP-N`M+zQBP%[jw|@.1"N1hWE&^]Q(0m"qKC(>aTIm]n=}_qZuPd$7GT=X4~Ss"[@2A6%r_S?+?xJ;OpCe9F-H;.s%6nud5nlm*_mg=QB$A$3fGtI.>pG&&%n41kwt$z";"iB3(NM%7z_nxe>32cg6hvZL^$u".siIt)2>#%&|K7n7WAI!uk/s?dR*OePJB[nR"W$>0WB27E@ZwH=/W:Dp8xcOL
-Dflg>`Zjt%9D(bd"q25%I*!Kr[{4p$`kAYvZ`"!bJnHD4/=u-a]Hu"W"$gE)9(K<Y%aRr+mn0yTcWSeIY9iO$;/oo+,^Ki]MJt8V0BXEF[_?E#Fik15i"O/0+G;JCh?@tbIZ-F^r)iFV//VJ1-hD][H;9$aM^.A#/3"3)KC"]v6T|.*5@rsox@+;+h)o#wav"@:r(#BrRA^w[X(;,We%r5$%lDQj2DUhD2jEz6:BbK_4iSomS]&M&Q+IseelB52l*&0N5Qs;:P/%Rbn4OH!@WIv,?UgJ{Jx(OI}e3o9uDeX?&<*/J;UPd*~;WODr*l5k^Nf0::@plE+aECwynlQ`tD!tbPHF0*{v]>@iPN}<[Pg,:14)K:DAE<G!9VS=MSh$SnI1n5"(WQ|a5:X1WH^]F]cqQ_o9]H4.OLH[uo3
!s{7"p{F.(~!e0?9,rLwRDp+<UHjuvmy@cxnttQ]a<f%#55[Zy!=B6KAdz(3]@q7Z1e4!XvaL%=tbO6o3l=_+,V&C0|uo9UNAF,QQ3-:{;<WuQo:t%
Gl&2!-b-:m@dJi"KLO18S]h|I8uF(b_tEjyT`t.1I9h/+]9UXmbiB3LaZ1Yzi
sTv9s_Aex++eed>9RI]WW=ZW2A/%9xDL-/<6]:B>STYD$p-3I1?bU>D;K=^%pI(7&FN1I)Ctt@Hg@zw@i[Vltl
xjJx8FS
Q:!COXlQ6Vk:
#%JT#EZR`gR*i3VSA@WtO$EV76j9!S4jgzfzqy^L47N(e%2W^k;s=]%"n$La#sVp)45[v8hi+-:,<IS;WYoe!
,Z`ZK8(*&2-kBTuP4_aqEwYlD^bf1`P0OYgHss#Lgk?AiAgq6Z$hb6%wVr!]iZ6e72pq*]GSm}1(@1j?;Y^OOO>6`X%c9s*PF?_{XLri3qEM8uqDD#sA7K)pu&i$Hu?h"nxJd;-`
%4}A"H^j
Y(h_<(5ML".Pp-*E(2BeCaWkyg@-NBjH/K2mD@[y[pFa
y@O?gSAmC5Cq~+6C/XNop1:Pm,iCB8s%2AaVp/qjkGOD,arsirrC:eeRV(1xFKS+ajzGc9S_RfPe3N3#v?r3W?A.G757~&z:l8RF9E[r6I(VB+He`p[=]
IhhZ{4Fg3sU`{)*8)AP+;b7N;m3Ev*}lx1K?RHO.g:7Q=)jO%bR1O>gav6.w!$V2&y~U7<"5HkHC(.VT2SDS7@v5HXYHiG6CD$WJe`*DSMYuO+X=ls(G].]t<5:nO@Vx}.%mv&lU}XoA].y!D0{]~)(2Ric@dDSZX^_H:<5
__wYj-LAIsJEQH%Qlb`NQB=GUdA1lsxbiqGT^+egeY}d4v5K~[woc;h>Sf^``79EpuP?Uxy(<pS&Gj?)9-+^+4-KtCmmh+:)?PxHZ,R<J^)nUmI>dW+Fj5_k
2=mFI=Hp+QJ6g0DOAG+Na*y1FX>;Dom)`ULvkSsqfr+g;nrY:NShm|px5=B/GTsVEmHQ4$$~bM(1kC+qd@+wt@40B`=2DLBz:9Yoq[$Sx=]Dg0j>1eHy-!B/W^n[I
*+4eXX7uHQA?iuw{6k.A7])Lh>>U!ikb2_?q?f5@@8ytkB8]2L/Ix$rqJ#yGh);&g>?Rxe]I"i$S9~4Ur
=uJ!1oU9d[EV]"j6b;$6)atx8^RVV:
(j&&EMC#TAJp&&qQ-Ta1F9}5G/pX~5kE`0N8~`p9{/UVXsK6n_(!=3EPNgNL~$_TVgC(
9i^EYjMV@1i65ruWSRvSgS^t#`xl
HV:CVc$F%G>-tA^X8@n=2GSR3Q|0g0_R+VBnH76k)6+P8=-0e#_f$v[xBb<KFBj^Alb6*&rlXg|g&03C$N)Gv+8w%yju%TbwvNd?cR{tI`Cre2>q}[*<1J6<|r|g`I*8&)/Ka.i8le14ld3no*PqC3`uJNbK#g)
x-9
x=gb)*Upn6FPDA_)3Lt)^g%8(J#12qDKY(lZNT;aw"SX*71rJEq<r9#-7g<2gTX*[P"`euhx13=s#X2xlTI
u;xJ7qZGiUh$KnI:&JLNsc)8WmY(%9%e)mVh2OU_(KX?;Ok-qm)7R/@E$dr6at`k9OMRfWvX-WMmwdv0"AEVPvN9v`eZ|;]p?%t(@A06ne164H3*%8w-A?!@iG,$.)fZeN^?S0{.|v@M74._{a~CmKpv;xXVItPgE,[@arVvO(D284*&SZ|-`WTEi+lowD2$ab6L]/rKaEd6=`AU"z)_/!lt.wW@b"9.Siuu&3T_cqu#`[o#6kz.H_8V7<}KZk:h
3LNj?&IhrjlLwCjAav^)[?yT1<l{Ec..#xK#T,f`mw[-jZ*qg7t$<o;q,s5S`}bmxp:S6mc1jqRB?1B}S^":pljK"PKS)IkCs00&<g8ZdKi~9z#4Vt[cIB?@6V(ANYFAgFxY+?oeb_K:n:YA!Ab]
rU0C`f$,90xe&1EX:I!&t6O:CN@iBk4Jx+>vy8/>QFAv"Df8b1y^^sJCr-ZVhtItsPA74i?7QbGcaBMM=ROXn
HvJM^[pA|f{U-poFx(iZT5ieQ9>iigTa)@PGNejEYM1utqpEa_y
T"nt/>3fT4uimm[
RTmeV3~rT.T1|SHyZr^e~f=Quk5&-*16Uk?q^HqvNNut{h7dG_de[b?IqapW23Zh!(ApG%`jkRiFw9TamjvlX3fnkBIV4bjRk^N6N].:Pfk"eoWWkR*_T:P7&/jjyKck._(DG%{dS/Xm,>|j-j774vyAp%_:dsw6o6;V7v)6^^6F-)4pPXO]N0"BaJrr]aC@$p]#-TF?oV@x~t~DNJrsATWF/P#MIG#>kbQ9*oNF2yCq=4cqnW_s%m^(Ai*4C9CLS7F2aKXAKXoL1fgPU0LnH>D68(MR5A(7-tQG)FylflVFLrlhMm_BJa4h_b|3lEQx,BeQX_JCoT5.7a6XD)Zf^+ONg8"0Aa|V&QRl@U,Ql5V+E(#XDS/`+]u7JNOcpoJI$z$X!M*gUMTK
pDm3)*d`Knhn*`9f0"lti^]Fm_gpmGi*dynzIb6JMhsI*x?=s
yq111{h:UhwUqU930nsp0@4#P;<4wIZG7ne5mvc)TIS
wD';case"he":return'-s`0:6KZ+&iq4.1ENu"<
S:`d-[8t)Itqh{Z
egQ1BVSGea:uvL
[c/b=4*jkZA%7&.Ppu%7{b9?0tG=]WK!n2&CJY$mz<v:Q0^T|1Ict:-NUj)P>m3lF):7jvhRkr7GHSw
zvx6D(!kJjquXP?H%qR]I?iaU36#fMmg;9B0mD}j-/BDy&As)oki+fE#PlbkB(Ux,5#PMf,fmhzg4uj0kSjpLO|;i`.U~6soyDzGQ-117:q;_(~2iksGz"8vBhBc-Z^YZKaE`#fOKcV?QyA03,I;?[VHHUG09Uv=x_lW+^$@_]?^|GE-_cknZp{QRPOP<P4gi_+k8;+(3fS>8[9$zA~="hzjj
~I)5^i5]m+;:pd@TfowwxC]n0oa%)bm6VA9BEbqs@*d/HZ!S6a}a1)kAgnF[v$70k4.47$s>}m3ng6D`mmkNl+T,%DE9.m$&5rT)
]8@S`m?$"IKF1x`P;*vXr&5h$[X<OrV/xjk~9?hdY"PGqm&?0
Ws`|JbK!A0$P@|P8,%,v+r@/0Mh,w[7)Rk8g=oCsm?7
^0y?(:x3RxgD.q8Y`w((Z09:CB&3B14](W/4Z+)mv#(p@uDlM^kC&7mn&{#Zl.s^RxtC^*MD[Yro(Dg~okwT<ybbhu$%^$TIa};vrOf;eUu%ftA`t&^fTJ_!_DOzNz5e/qv:-{SgPIC9;jG>H"?2"}[-y#dLdx(z$t!wu:yVo`<R_SCup@Nub0qJ]O9SP[UEwS2M^9wHQs`DZay7y,<ZTI
xX0?7q;*[7LIlMtcs1M0kmJAGqZx`?jR<^N7oF`*>3iV|1LSHs2&viYlDrz^(HY!43}n[fOdQ>@K|8.XN`%f}#OYQ?P3"o0q+G1a.C2n%^ug6tH24?=hJKO4IyLy<t}`4J
L@mIqQ
}*,@O2Q&+0@,9wJ<9rk00
~Un%Da=_uKZvebq6J/Uw%-S+%OMx6#7xq,t_H"*m;ol?UIYnN?7Ap;F*b&"%RZ)>Goq*7>G?~g/U$-(P`TO9fmqNX_@o3SF*dUQPFN
$XHD4T.2,`o4)$.6kJDj/3vwS#^GLRj.Rv0<7cSo4Aw>/.s7NjemM,f$WTR]VKBXTWZmL^Rd>AJQ(7XKHnWMOLw$K
Fl6Vw"1KTrae8hWUF4I:`4WrFammJ&$x
E&TuR"UiQ9K85[.l"wXf/9|R~4s;1<6&C$~=H!Iv(g_!Wjg.-.dv^>Tq4r

XfCR=2ZSQv^puk.):_U5VsN0ow:O]8q@Aqm
4x_W,lePuAvTAN;8Ru^Z.]jjZ:fR;0FF;:%OnG^&`S
$U>{xpR04vVy:v0[Z-pD=W[yD+l32111C!89U`(ufZd=^"=Fm?wV@>Ye<fRa:X6NR)j4oZFOXIlg
m`m+akmi]Rb2`J"j&>j5Ke2>R42S2r=2m*?b;vi`c3Go^]en$0G<yg]adS5Q`_H7^6BQ`ra6Za?>Tp>=ltif+0}%u^!KMyZA2)-MUjTdnq]W>b~!{0vg-RkMV3$T%x^@@Ll-4Gg;D5XcYCPocU&bM`BD`o!_@@<"hcGZrg6W3?^W{vlARi{JsR1"-f71{Ap]
kAYm@9^9k2-_dXF0g/tg(sQr)<a}MCZcyB7HSRs,7HB~%9uWn;Q:3DW@*77ji=_&1f+q5_>.O8-,,o!SOgH:q
-s^$Y+t:nB/t&y*O?(k9UH<cX)8995kY0[.fG]?mP%u"lgfXV
.=_&-wt:xhYyk-DhWxhukjAl^&GG_JQ8035eeH;`pqRCz#LtLSt=pf=mKrtpU<A
Y,-UjFbkmrGQ`>5rQEj(tb*8*mYnb
ZZuN2w:G]J`8s<@yCKs+lk#_(LC1tEoP29v6:^c/JZm0B,ZI^|mtJN[?JgcJZtnWrxkdr?F:n9Wd%L_&o:w-_WBgbgv,qsxzQ7UT*4ka)"2C[H:_X56G?0nJ>K/?iA$:rc6d)1*HaT1]x#$gih3|]4gJyv]OW]BD_4IhLLA@5raVJ}AR4]HKbti^+nOr>-7FFHEVHvKN3[1{Ezx+2!<PfIc.VA<BShXdcbbP(gpo+/Mg_AG2j+aJ5|--T"-cyf?ue$2?IpDevJQgl"DaqDwg5IM/U1]O+B1X4sDv4o<%`k`d@NrjjvY
7W?Kjbp6Q^W<^8(M5nk0I`tAVQiJUt&=9{_4`ncXJBTpxks,A%*D%vCfksxS=?kYb(TQLDgDA,!#eFmH#cS7t:pv*h:/.alYmNVil{LIkO&(d!;$(8d_]yIC_.qbd~l!IPp^/B_G2&_jmo;,CVX-M#:0Zdvv3UtL80iRE|PkZoc95!&#/FO3O{$6r*`<KqhoqF5g),q6!y)aa_g+1X.$U*Whqlu0v=MH&J.s5h2Wvi$/*tO]O|61dTk<sg@m5W;xX-*q_-xA=]piswE/
aM408cDq/Dpgv,Ckaa%`?9SnD"5QskIH`c@!=c_dw^=o@MB,<k5la-<G%BiQ5+|IVMWO]$+kZG4oyb`O^%<m7c2>C6ct(4X7x5BgBhBu#`fAgwXX7+}@Eg_RT7~/@pm4-7WWt0?kq1B+F]d?]VFFfbuDw@})b!~bgOd^,:$Y,I]w-+ST>P[3^J9WEpu]WiLsA%4j^IP5X,jPKxdd(';case"ar":return'"c0@b5Lp=(n@+r#$0N&YGjp>-s@o<8
x`!VSS"~PPByeVeK3)WI[HDb`kD-5FuJirf]CkqYr54UdXj
cl*}.-f_-y;|?vbMLun|u(mWl?t(7`41J7xkBO4*o"x,o$:isFOHVrn:DFJG&=1hx;3Cmj7wkbmR)iJEK9e~v0DyV,kF1Jq.F8MlI7en]QVEMX++.hX=8byVWhwm%pQ?[Hh3b
kRcZRBS42h3MEP_HBa6|aIN{U]Z|nO;EUHSC@"B;y]2K:U1CcTOA[DOrAL/XVTnO7zAt#(wVV`n:+W3hMHt:"./rM}&/+mf:n|j_jkMgtDxH`ZureS._^5w[C6n^wFL0t/lKQx7KK-SLIOg
wp@~*/aio
-(m`9ygP;Sno:.YU:pLUU#RK
$q1PpQcEDucV&+;;_EOsAbI=r?8CTykSS;4GMtwrshTz)
khN1LCKNtl72gu#MZysqW=$xzM{
_Ri0;Ddx7yK%rVf<SJa%,x:`rIcwpO6`uIMnEguB6tDbv`b0OK&d6A{w[z")dRz@}X/YY&P;}P!XcEVtN)W<ZrX-O!!3O=ib><|UDK3B66bkEN~6@d[KNqF1/6)u@p,=7J&_W,mZwE"J}94h<
H0Sg=vUoVfBb1"Z]]XzT[NATxfQhjZC>X7m0HvxJSfX1^u@t#qXtOu:En@+wB2(4Q%x[;sz`BDp&rWb^u8]P|TU,&^$R1IE16r=X"(A2iwYxD[ua-kGXSu5%BVO7jA{2=O
@b-):,v[bbnK<;@$AJ"nI(0`?k$1.d)k,^m7XzVo_rH6(VACF`bFN~ozAzyWo$bll?EmV-2m&&Yl51w1ZUSb_4>`4Wv.OtFUlnP%v8`_=Gpu=o=iRDhD)C`=V~w)XEY55Y8*8jku8_Y!Gko7qB.v0<NFaE.
P9tw5]:o%BHTsu"vGMg#G1HOu-#/vr^Tk!!bdFgU?4*?r|&RtBl60T`]t;VrIdY:[R>fmdF[^RTF
L+&:q@gW3*%#^l+u"feYh^HY&b:0>NHGij0r%E:Bb0aZ*k*#96wERQD
?8[hi2:M
+<,bF0d:
}%NsaDy#t.y$yF]Q?40A97;<=ViNLu8;?6{W_74h(q#udU]s!d<:7,.B5Ol(FO5>YuYLq[p1`N6($k-v#C1N!6tef3tUlG(1sPibc3(qI9hWeV4xM&5!/%Ek)4fK6Dc21]Bk!=BJG0V7,v)7`:.+mg-&>RKVe#X;](`?Ri=Y`>:/(L}^RPgUL-GN6:8d8pKn6*895foIR.5L/z)-qi@%11c.2?sPVS/=-eE8@0K5kgv;SY(*/Y9[Hk/"PAypNB2nL*eyJMU.(&B]c3h"QG1ecOMN!d]v[u[whC$V=nwo?Hy17
znVtOu<^ExeC,W1+L1eneSl8)-M?{Z/!
-f?;CEL*+*M$5^22(ZHS8k3:2-#fn0Yy`i>kAW1|[-ck%?]w%O13c8&[R!jqV&T7d,
rBww@,uSvn(ac=3I])qHFcY<=ix?+SFS{4KpuK
wX%Mdt78Tigo,99MP-V:Ho%FP0kW.T_ZSG;*n;O*W+;N9:/,A!;/$o,Mujyb(^KA)2QFem*CS=w%[R9Kx_?9vgWm9aXT:|miSyE|,{;tHgaVbi5(g"SU+xkb^ux{osKO-cC-Qhc2hLoNg7o*Su-TyXgq!c*Vd[Fa.CIu2p-t/@PJeoxxAOv@tV&Z:dNh%&-V8tL.L-]uqFM,1!sq^S5<_EG%z)5WM}?
Ghsx9G9As~y&e.6NxjFg#M<BdaNinW)F-7:NWp]SbRU,[*`a#
<KoGJpcQx|Y!jDx_5v;GZbKZbuS*pY.EI7!8O,^Jo)2Iwd4r^JAl%b4P>2sbT4I5$Nl`6%Na;vTWeSp3L?FL!vQ":4Ayi2;-iFfz?8k`7z1|^NAy8%?7qU
Y7w0&+(&c%0F07!*u$h6G^&M3-KX{[k_U2W+P!RcVEI<vy%1j%$mu3ai
"6Xtrf;M:5]9<ylA!bn"]X+G98r
E^0c+7wwi.=`xcCW:s2~N0>+wtCWi"6>GD?OBYs3
V8cJ7aQhE6#j6CzbzGIs6,|2X3Q5J3{ut;xXC4090&<bs"Twpn~I9wkeSQQ89L^w2
n*ZpX,Z2/^?VNKI?+ZT]wTI[<y&!bTr/cA/c/*
(=])@y+5OiGJNa-p^hW0*;)f2hH&_baIUieM*#/E>/[b2i@U/fU]q#O~.CA+*JL6DEfX#0-@
x%aj+rVJ~?l

sUFEGQSxKTJ/ClnTf:S`%_W8W%Y{l_FI@Yx61G0Pghyu%E,9rk1b%K[uJb$=!W3e%sduFd-179w2tI?bF01|A[[Y5a.HI9NoDsRygEsait:NDyfd(?)YUKN1rOU$HuwossCbw2?~IWi*`KUZqr"@7J6zxLeSC=V0yM9%(2vBYMC8NW)Rqm1@K#"TR,FHrDq1kDqx>>w7&T56C>O(j^p%Ud&@`qqgy(gpZPTuKQ-Oh%SY,lj,b#b_Lfipq3Fhd?
r6I3v5yb,@FnK):A@F8Y"LpDh0U:jHFrn-OM{OY(EmY>lVY9&bX
/e1$DvIudY*Eh(17WA@TL0{OxmbJ<M^cuKL$yO?JgkFk4P=b5W^5kWp?e8o9iC9y1(w2!Y90v8I(A2Qea+rpvoUs07aD1pgrKsXBngGe3u$-/KO@#i=eS^m<ICcr
Z/*+y.0O(#at;eOB!=1,Epm!t%f1LG)roKLuH<8<6GW5eng|*l&A!JX7*{57Rso#Oz
]pvPHVoX*l4)kjEkr8_LXiuqIo]?UY,]Pl8"uQgru0q6|F|D.6(Y2EwCDp2KKr%RSj6n/uY#3LmR:+SQ[FhU`I{p%H3UOCi,Bq9(@4%*(*_i2kxB_HQ`9T8v/io*w`i7Cv@<53OD.T}"2+BH4PzU,/~I<L?SQ(`YOMz
&s`]C0J]J3F(QkM&5bEquyX@73|lRk;d|s80weJC2QOQ`29^gpb_B%E+L^r0mK]l]Vr<,3%6xxB:Fv(woW[aYhXY`9"+P""Y{:R4`^?rqocRK65]*#JMl<[`^q8UqWNFHw/*bs54
@3SH@Vy9LoC4.^+Xg%/s?F"Ggbx3f0!SZT5Av1,Xp~"Tv;n]n`5*fdQ|[ct$h`a8YS(Ry:Hnn2.gSw6Aj.GOQ*k>Z-T|e6Ae<M4"bD&^HY^80_(JJ}:
&^+BlJR7&x@_(3;XtxTslbn.E?X&]Za`kXKPQixPP8.+S[FU!3l+Y#rEg]&*Y;;;D+DhtW3r)I:`8_ZoK2<<w,>|Zyeb+j=,q
oULUj9dYA|B0jo4R11T-pHfv<PRqeAdWu7-B
j4bEsc9O.4/e}Ukp>9O,#km+<m/J7ZNuSS!njB-;<s?"ArusBvzHr+Y+lh+msIg&Tb_M_B`DB5.9o2PjLE]ck@69#Kka{L<;R[>c!]h+HdX7BZIpQ(G.{0YhX;soKqn+o<.*"/vatP~.+
wq^?nO{]k_kS$UYG}aTNNA3CBqAxxA%nt"viH=K-<wJJ/wA';case"fa":return'-s`:Wh%Z+&HokU<i.-NN`Jv7A;
+t2B"wxda1!&QONFV@9#Sg<P[X+}AgiNLGTpK=ghxYN{RvN*&jx3=AK{K&H+JGM~]HLRUY7z(s6Hl"c>:Dl)/vc|JSj@nD&YFgHLeAyA?pw$0h6;lUmTMpPV=MJL!WF1!N
DYuq^mg
2*M/7;
Kd]3>3gbPm6tP2mKHR2F@QM;mu_w]oa<dsy8ht83sgsOvi_-<Yioe{[5B,bF4:,LT`e@>jXIH/h%y:!nb;P95:ZD?]Hq6~+>wJegPHxiU7$_tq2!.bOvX`c#wph//[Bbc^G`<yN7n]r<lF:oeVEt7:0OZvA}K9Ir"xQnmPyKo@]nxn%5]c!kIVEuVZ?N^"R`.b8-M:Dd08^mj`Um?FE6^l>^Xj=[YRubh9)vVFuaUm;g[jQ
gki5Q}@F3f+X1A[peV+ZrTwXsVyyH9*.=z.DB3&==k2,,u#3K>)6ah8/n0,rs>;jGKLtv~(IPh[Xh#No^{wLqoqn&59(2P1[<j1nY
j5h(#wRU^7w0lCRCdw:IZcIRJ2]&d.ksBL<j>zBH_.5%,cJ-muTM`)RrBat6U}S$:mqyt";3TfZI>#-tru<~7NW4l
T@Aj`i;ENcQ+X@eI>c;!OM?sIVf>y|jQ[$vh
V>[K&MiNfL,T1r`iiBaOb7St=fgIq^7t4R&3UQtO;@4H[!Bc9)n!yOw23;}/Dmt=Dr(6*[j%m`._{84_s`kw$Eh0rq:vh_m
Q@X8+BsK/BB/J+<`x@b%5P`$I8:gNG&60:548d8T67~;r:*Ec=mir+`-R-|>Bp5KLVSw,5}Qw4sGVjKT7*VMb1)9?:j(8#X0JIbX0c[LLO?qab7nSh.ae#m(nd/X%WQFN1f)X3nQGXKE*cHY>JO%bd;5w`2>q-!BEI}%(BqbN2TAVEa4:v9Y,lC9ofhU8z&x2K/G(M+?Z<GStqyPm1kp@t45o0i"ZOUlB8D,xZm;~irll[,i?$k"#6FS0<+s#DD<+L+3%*?NEy@#BF<vMuR]"
ZU
@HP,&6=m<eq`iH=&M"5%=ooIPKhwRNL/g8dRs$U|#g:/nNh}Q6kHRw]oqYJv"cTP3[MZ!jie^aGsUVQW7hQ#z!qa[)%EjQ7Fi^oYrohlKU*X<hJamYC:x1-UM@7$L`F*9zlbDYGqSLofdx92o|[M)0^Uc8)p-j7z_^"Jmi!],ccc#aNWx0KiG7"UhF/1HVs[WKLrgj3dNIW|tx?Fr}0~PwR&?*eZd:-4qM;`M,oojbx`eZ>`jhZQ-UEx7~sQ[P$D,QGg&9oB;,CP
m1B&$QAgWc4.[c+K=BN)zgqGJQ)0w^"O"mJ&Wb-<c3(SrkwP.VMmq-NGYs=.?pxNxY?n^$)+%R~<.3CPN,Qa=V3p<gy&M<<q[(-Ep>tvI"fi]r-le%&%dW=+{#(DC*uN&`$%UtX1,?
<?$}/`WbK[!~Fae_L<q@1WhW8-h-(Z.JV^HjkN;4!A!.hA+rnHBf*drr<OhMA4bzM72K:Xm]?Vk.V1"xuQJ~uu5
WdEO)l;(;gHa"My)6W,gVGu)-S$tC:ud@01)hQ?/g/a)mw!U8xW31F?PdLc
;fCWn%`1G[8yx@cF_m#i$~<4=4s!2iUm[3>?gG(1Ll;H&`y^I8B:`SUkQ6!B(@k^3*
?5Enl)dU0wq_h"cqNHm6Ex53!Q;
.=85;1m#qDigbRl%x3wEeV@Y1pQVNhm<dq]AgQBYd0
@yp=NHx/*%sx&Y+&?#D0)7f1e*`k$r_Jd4+27Ub+Z*GD9>PD1)f.R<]%VEPjqNcKeOd--8X;p9W)`6ro
q)US=DXb#M"Q}630)!hNTx*
v^3MSSkgId3%B"3k)iAce(h<_V@+7yQ&sQ0"fS>y?/{%7Y2DJkM2w^_
=gB]YRJ,;Ke2fF
5$52F2&TwV0/_L2^]j/w2u<36`BBJ(*k/Y1OjDl].o:SalUk5mh]*7(n:aOtFKO@,Vc1PfUZ)-Go8FGWAI&QBZ,x5j3e](xbJ1c3V,]Q<N
j](b{X`J!f@si<wEbKqD.#C6Q>"eZpz:[d=e2]>8Mp|"0pRt%AM2&;K:r&0Q@QE#?l&E$&tL+6E*k(h?3Q7vrjBo|P;+5,"=gu12~]cox]%mH,O)p&rWEt4;1,%*,jeHqkd28g]Q@@QC1AwG<E2-qUv9esP-oB;TA
yTj&MGH5Sli!xXw"Veylj_cl/mxJZoM=i@^d3eWr@Q!c?:VvAwiXzADw2cd6bkTxla<&1u$yw$$#pq7/AQ.VL=eDPW;w|+uJfkc!$0+TT5fX4DWA=9Lh[$wAY$6u_PhBw/i,$&qSuN7Fl?b"fut)"@5!kX3+8.*U2n"da`>7GhrQH>Gf3x0<=T)-{XWz&F*[o0usMTik?%,-#:~49*UAh./NoY)ho?Y)8-)"vEE>^:5%Y7`b[:5!|SFC]3U82d(Nl62L"j:_E;#Szm_J`d"0brF@I(8ws6HRcKeE3/{(T?bO9c}5r
aR-V6VX(#WrKH$ud8[.1MJJlb,N3GAAw13=W:pkkXd,lZ3W?s5FW;^`fC>X,a#n]!UODed*rrGn,V+"*_6c!haWGZHgU-
x@%`Oxo:@97V*+.q6Mo;|xBW."ms!=e&wO@J.YFafQNab_jY95QCOK1h6F#IeZ`,lHp3o:8GqehSUajYuuv(7YpnHQbmN(eS/[YN(>3ii/pQ7
.*ze+44QaW<2[=ni(Za2lXBCJeDf^!S`
Z6Ex5Re!L
J-tkwC8$';case"hi":return'*sXF{bOZY.<K>C$U26[RIlC71)*"m8`v^+JNv`"30;BF!!fQt8T#9DTDMMEZ/V+MqFXjH>!q<M|^Z`frgB?r0]n=I]">m=pso]Na0m<scn[],vsi1,)N%5zsQ*Pfjq|:G@y`Er,l-mO_n;tgb^>ktH5oKBzLE,wr|?1AjHAB^!M&g1bh(
+`-mR^d=Yf.KCGXq_j::~AyOw,8Bc-5WU
DwlnAc:x8H-xEiIYEly+gBcC:k;$=C0"|KXB|P_E|kn_9EyL1U?_x?h3ZcvOGqOTTK-5GP}0Mm[vBhe"EEGlLdFOE*ZaG;b]beJ0wauvS>{oW7)V;gS>E"^MN3CIBN.t^Aetxl$eN3uk/Y#,tk[6>h`WEaI=ba8e.7(XGiZNnmO!O7;A(_U3vC~"Jn>8Q+^EIN/=`hbOO+1x{9C2)/Ht$cs"@8@mJ]Gq++[5pO3Pb:zkLUEMuX(<j+re)E*O/T%;,9:!`!zD,bj&l<84a(3y1O|yLDqu
:4cmkm,8PX!J`,Bh&KRL76:q-AAWh@fFi&tbUj4FwZe;=iC/@]o"Oc`GsnPRj1kd-q.4[_ES%{R@2P*P2=0)xF*pyJh+A`;*LHb
WIe?MtK6r-uA1jN!+7E3>g4XjR=7f#BFVg0Vp`3-o.d=GrgZ^V,b%j%Z!7E/Qok=`<K:&F?>+c%o2N+.p(1K+kRc)pZ
q4rQaeHKR=&}s9_Hc$^B(V?W!"!knvAQgVExL8"[dVqJbe84q~NJI0]jfvKy3
9a*8g+bg1DKM)y$-oUefAfJ(cTH9_YvTLa^_@Nq00
x4P&DY@l5MQU*/5GJXOi)68oH}oA?F;<dxZ#Y,Ch)V"
V$AkMi$a@u,Kj8oQ>-VPQ*TC,o/+@>l!"&tcdT&Fe2g!m`#RER`D1S_*xVNkNDRF>yrLXUg^;q#8L!h9^xvOiN5IsLE0b^V*CDn39Akv;jlBqX$$FbcIN%o*=Up.#WLBmdQ%hgVCf/Z)d1,|SsJZQ~"J;ckyxf/m3D"K@079E9+k/IXL4>J|C:X(=ioLj#U.+cUjE)ni@@%UK..I97.*QqSgT
&<_Yv(:IR*>W-/=I?$&U#a0^.tY7lAW+C*uh8&&t>%dK,nIo4w];4,
"pZP-$MkBQYmZ+!3wOEuI_5Oe_DuD==lAnhf.[11{//-:L{[,N@L%g8&
wVG25!YT+y6gv1U/dOj58LeR%.CU@Nxkj1FXVef)CC9oC?#*J-xu0+s;)Qx]XLVA9bfo[-PKi^,dK$cq7ix#h1xS]ht
(%lZ@zqxTO`o:07}>jEg#$2{F|8@w`,4opBOdqL;[m
VV:gjLY&[j6N5CF:P,HLQLP([8=piF`Z^.#NE:}T&n>/qb*6FFfmhnVV%KW@(D_jbsBZ?w8n>b@IequTZwV,8B+WnX61^ri`;ZR/.:~VpHn/!

2VH=Z`YOYyr#f>7W,)l4V>kl]iqvV0Io]-@YM(J&+A:L[)QP,+-2L#])N-v[HY/nXm7*vc^j`N4Qu`ji-us7n2[JD,eXU%qap2x`O@An4.XsP,,x`.Xst6LrAZ(MJ8%q?]LbPFaV5m*;ttK#2cH
^gi[rC4i;~t<Dd]Xh*XFv;oT.+dAc!W?YA!Ct#5=iYcmM/W[u_q
;;=4Ybxn^.)}$ID)FyKLI*8noOAwhn!*rk%9d]9$K6!{bA8u
?eIC;_{>Yu+9$"fX#V%cEwUwy>trbSd.`C=B"R,TXvgY]()4?q3)$[$H2:g3v"qI0^.M!:vP*g>88[dJxTlJ:V30uJ,UoPz1{WK";EHC-u*_3SqAIeV%b2/"bK7<tW(pxa@,jO=la^<<U->71[HE^OsbZZ$FT@:8Q:jju>V,sJ=w1&{%*pY-I44`nnG8/GQG;^e5U8}CFk5cZla-U.F)NDwUSX,$L/JGT@fBN"u?
0%np%PrY3YKTWqLLOJwPeWUBLQ!ITFdybC2*Nv^rUO]BAq[m^3

T&Z+cM:|!}
A
v:2
"i&`@]XkZa[vH`IIWaygEI<
UyNMt;DmZA.sP1k%^#N;k
kI!4aS]t{B0]Tvd[h^9`t_Up@*7qvR-OFrPVS`L!.[(Q)]@qJ/tjfm9VS6T8%ZX:/ioQV9kBIT527<@ss,V*C(?,[,RK%_[ERQ))k5+tE3l+Ok::yZo0Q$2Zx<;1IJ~-k<1d57etZ:S&Pwc>.Z"`GVBI+5$Jl4x>dgvWn7CMV9~sMT{O1NyUW#uY~aTf}e7[)=P3|bFq&*|a4+tbG$
:GT3F;<n1w:Ua43lQb^"E[b20U*f;9t]&Xb8&Lytr?obZW=ZitVm1qXBq^R*E:fS].!$O,!Xs&A<4nb+cRU-0*g}ZZ3QBT:sJ{D4!Im]DoY[Ssl^DUD^3II8*HDH(wOqcpc+&>#34^4ZyQwgYOdOs:pO:*yU<
i}$JV;HEN;`0WD<)w#m#O!J|0YjZJll.PO]/k4]f.h9BmIMowN2`42yhK?,x9$k>,<KVV(/TD7hUw=1LwCSL;jxSY72eKm^FG"g)g3FWC_`^S4JZP8/Y)%c+
:4BE,PrVd:hL8Fj4>6*,zLxHl6{tt4,%jwZEGTHFt_V6.O>2I+I7_PHoWS<7O]}slY0k(8pQ:vhb3G4JtP6m)2vp,f8f]Iwrr;`]159/x,Ki;0k.i4mcAo)?_DV>^M5QA!GG)]_C=x6DEL>H-t3VPh*eR_^:lue+:-i&Y;WrUJ6q,.m`ojn:t^pN(yH[Brg0]L9aqP@f<#rV@Awe>&VIqCVg[EJOM*4d@;G,-5`,1!H+2BTqEt
fX)INo`+MjLwfyZ:cjvpp0:"hI2f1e;8*`?r0@=w@*0t_/WKF
2~sjJh*72U$&)DJ<sgAhpE7>bx]NB>*5)<6P1p=&lpX<A/8j&u=}YIH+
pjA:H@Zpd*V9B?x%2f9*%vZS-+B62-
vB#R:Oy;j@bYVFN<Dd4
4[f]&6A7_O4ll@g}Xav#C+qhhDr`g3Z.E)]4UTkP(hOAb@Y9OV]:cPc:BuB9:_U6**4}s*sY=y"wIQuRY_4v./1e"&3%/@H"Y7,0+fH12"8(?e^ONWB12xtpxEuLD<<g
82+$R,{<T?
[3(,D]$R4*H1,*x?t.Wl,4JCk=#^?u)F8gA=X>D;E
ZIV4wJ$eyCEr3Q1Px|d`8PQbmurg5dgsZ5
?PFI-`9XCsvt140GVH
hIPop#&_-XWy,EivP{_U0kfh*Mq"f~+!K+(}#UMdQ$BP,o2XljB;PAa)]<X(Dt7f%[Rut.fbnyKleC4K:@YDod+;ey@9]0_15s#,fdlAvdV|LwdWK.*pL}dqtWoOx!&6ma2~*_F3>S7@"39Q$+qU:i!F]b%<GcG$]tZyvYv5qXCMXsA7;NRwqxa.4"/b6O`%"ObEeEW:&4XTBr&vio4N5:Q1u1nD2HVIs1Farxev#GL-ifO^HB^mE]fIj^2y?.Bv;[W+puOsR(Z!bnhb;jF3/L1|(ofL8J`jYwGg<v5!-PiY^Il$$cBG*+E
f{4laRm$XTlk[C^%Oc
fLbO:Flv%=z
4s!Ky$ej19aEm)Oi#i"fTgNca%O_{=~:oj1MgyhwA';case"bn":return'$s`FCaPmT/&=&v*lG.^+m3lNSS^I[5i$>3fSyBEJf:1:{f3gl_5`vNhZ*C*jDXpZZ?NR<9u`g,jhZD1GfW9]=f-Bt1%vS<XyBx=^PZ9n=]r]-s)voP
MhGl6.4WLe*%t)kVJTE%6;_d^-@**e&,Ke1mGQ%vIuBv*]K+k.vy"K->KJHlyUba>/WPh($_#4trVqD3C-cHCR:cJ!z!l:Twcsik"tyvrAUi#8l3T$q"mL$l]2W6hi=8Yd30^|TpAI3M!<2aTapgI`k3V1h+=m)w]A#0S)R2YEhb-nV]-)x#YLrA1R/rl`NJ0Qqz6M<{YtU7OmMw4D^E>7V^_Aagr^QK<#OCC.@g8):iZ)Y!b.k@)[n53,y~=m.=jXaWu"^04HvZmRh&$:@RBb6;[`130%,N)qYZas;UT0%cPLM*tY6
!%SN.stL#"yd$;?P?`nO2)ta>M
")gB|g
HZHOP8vtwOEo1+O.8aWUo0.T&R?2X~7K]L(fqU,Sgfo6n{%"G5?8Q
/#@2j>=U6A1gN^XFG#xQ9wjbJRezY;#3nmDq::UQJ[r;W
9$@4+DW6X
dnv58Q"Yje[^wvEWR1=dB"Ei$@G4")*M`ce)8M95W|"FHp%a6.tamFQlHc7<OWaE(i_i
|t~$@ROWo++[yN,V^]&N0ZfeK,]
q9L%^(E!-$aF:rU>G6]^uLZ"-=U=.GlyalKNgMO3?HcR,8ah_-x9w({&zb/x[Is&cPBe0j8XG#Erc>k)k[8M#9q`TdkLKEy]OsYI{Ly(ms$&;qv,7-yl5T_Kc_<IJ#(Y7R>-=9"Mxi0tf$n@~wD3?k1wvS`4fy;#x0e6C@nVGp*KHqtM4m"As,8>&b1LP@+AaMz(z9<2vMWw:&F/Df@]9=Q!H5zgR
)w5"9xY`/4iHI%D]d3fVFjAyZ[/:a*hR0Uv
s8NjyDO2t*_9ES[L^Ao4,yx@]`Jp}PdNryx:aKM5Ra9,Eo]B?=
BC,ga)oyo>xhd+MiEr("lIW%cq=w"Rux@+ZgmzyK[du~.5@iSLr}0:9~CJ1AvMF5Kx=wS?E.>=NcO,<S<bJP19=GysK)w|PN7C5(w~8TD{Ao33E{B]j=73><JPOrdc4g8L!)Kpu@FUu>q%AP`HwbpCUbL<iqtQ,EbsnQ1AdhWqQW/c4Vj(;<J^<6Q/MOc#N%8en](QKwkm"eD-XFC:BpM9A5;I@NxQikZ:E!keq$-L3q*pJ5xsf/-$V8WeCPOtC5,)fZx=
*C$J<05[W@BN:/*wJs=p7MtH2%G
U6U5t/*9;/#n6Sf#IBZ)#a3p=8[oqO)wc*7p<^[/eCrboIV:~_^w-2-8Z`WCmi7+[^W@-B3+>l~C0/,xZo?qh#Eop6r(p[mbBw+h~>1EuK%xdZB2[DE%y-mGFGXp$D59ab0&h8MVG#.0[(4ZXIa*LyI/34]FOFI*EoJ5s
iTyJ1$C/q[Cu;*ID{v~(==2#50fS~YtLutF/Yr%f5y%$/6ri[(u^+Pvncu-m2k`+0#p/&7BO>/:FRbMd3sby|Pb6w0
^3U2BZ*v)dPLgOZZUIGgAWO;r|eStl.9;n@4+/m,ukmN(OO;?Y6"h6Z&$v^kdGsDEXG&Kb9oeR&Z`JX<9%,*+>EshHO0oh$SE_6jJYs9DQ=6weA9*sK.7dD<w-WEKd]?Lnp>_AtnLl_+9.$z+*RPDMO@^v[$*?ga:`*H8P[36h*14HG~som,f~0U%)2n)XSK)1`m0X5J^4o1R@-yWh^<s&,al!dH#TS;0TwJMP)7FVbn8XY*kAl:-<XJ4$r)46@dKx%QLUDs]##Pf<cBC]tfp-&!;]Udqbsj&;PsVo0F-/,NC.Zxj#m$@DS}ntC1+~/cl|aD"=eVJy=hr=bx=EU{1UtpfN+$&Q26
De`EA3/r[O[#Hh:b"xsRepr9)Lni2C9EYNCM.LiZ%]/;O`,&>D[u6p598CtpSF=LgkTSsZxYtW8rFgiw.is9AvWs&m;<&_{kq6a5Tm+[:Z,bN<`17*4c)c[9-&JZkG(jcr&5.*yLcT>.MU.+S<Mr0c-(R$j1:w}K+YTT&Y9+:e]<D!H-.TM(DL&/QQmL44TC/+oI<I#]H`1r6B:3x!jcfrIJnFX4aE~ed(3n.CJ:du<#DT-W!:CK&a
2-O6*/:j3bbc1]=]rZ^
IquNmy,bZhP{R44%NZmG@>5_1{T,?PS_1;uuK5&NcoEK^kxog%_h;9K4,o)?OxF:(LHbP$E)H_!<RFZ!w]#b,k^<aDn]6m_m/[VD@c"vPG@fCiiynkY5!1xkl9%hL3P6:S3Mfwt
>;DH?SZ6lJ!!my%U9.<xaLQ$#xR-v|Y=w=".#m%.m,xH.-
mK6Rb-u4_lv9PqsP>y#3w!o
C6Uo@2v,+J%2d7H76;O2[_g6DD3"-2Y=!)/;JW@/Nlg]tAmT>,_)<tuK,#*o7,+:AM[&LY]Xb#p9@%Ue_E=G*Fcxeisds>^s`W=Qfw!AUNlo|X1,4f|3W<#5or*Cfx-0cJiM%t>.vIi%}klj*jN&O,Yn(;h56Pb7JmD#E[.8jkv_?iyT,8-HUxc7Iy0Bm%1cF1tc#kN7ygbDdX^TMVRU4.jHtfYbN5XVB,M:iEah8DdWd]S5|x,xHs|T?u~Um(O+vqC1Zf9O,8SR%7z=0gH0M#XLNQ~iIrd>,j~!#<75zl^y9ioj4,f&L%8.{cL:_#DQBG*;*4!."v&yETC1k%`)+6l-c-aDLirgoS=r*.$QHQ=
6Gr")-k){>hVf16*c>Q<z30G3=tD?B1E5B&,J?b+r0
OOYJP3LgL0.Ly#y#/67HM"H9MdFT33TPl*eJT[k]a]%;5aMm+8<+B_?:9TFuS~NU<xyJ>u*3I!GQq&agm=${?~tKYuZ`aD>:awYerXZ68vK;Z(>8/VS!3z#"IpJWf_[`3$;u&saQ?"1FYLTbfl+l&1xj(}L$fP^_
x;$;L+>?;r`x/1.&3T5>3=r:_(m+Mm&n$?2"S]8"VR"#m.MSAbiPC^/=87Dh4x._IK[#xe0kRXz9`9dT<6Z+~u$CHbud1o/!mnl.vG"^Y@5h,;?N7-r&Byu,qb_,/s+U2,jbm/nr^_O$%IPo3UuCmu:<),rf,8ej0P^oiI8Rn
<Cd1Oeu(m1"
D)i)dX*OOv}=fh7!"ka:6.[l
[#I$-Wm$K`UKmWCXP6?ml{G4,1<!lt4IUA3bL!PMxu_e.kcF3zvVZfo::Mf{#yR~rHEz?zwvp!Udx@J"=MNORCB.rVQJ5T[_"0!5^.[_ISAkE~oKC8ZmfO0H7f
L!NF@=5X3H~9zp<sKDCVVOiP^ENg*>Y
{5P(8rpkIPzxSg"?|CUs*jM-l9%VZ$vp@QJ1PTt%)jpR-*8X.@y.h/O1.(EO$-YaJZK9AeHfGkrKG-YThH.pD-H#u"!+$JqBDhwhSUIcEez8Md)5WA;syeiTcZ~/@iL&EO0C@mrif5JR+U-i$QI)X#.b(Q5_!9Xm2%oW0=wjd-ZxN;+?!A|
|+O)2]n3v&4C9WO;F
-uzPb8ivgYzKp0RuGb"l=b"/sXaFWizob6#Yk^"X)93r&q]J*c&Z&mE)YBD6Bds5lC/pbY#,q/.9"+m*UaZG(N{_ZdLJzVz/0G6wd2|P7LUw!:jie
}1%C(@B"?ADJ+-96d[=$!qlJvxa]A[*]l!"]<XB)yuctpt;igN8x>T3e}&5.qA0]oFDNj)>shN(s~3QjgNPi/hqbEP#pa45u]iqA{;%PX`A@UERL<(RR"g_LSE6]Zv9O|6PK:wj=3M1OI4SVGNB(.Lbl/ctIVo5';case"ta":return'"s`V3aM+N%gx7ta4X(5(Dx2PFHr#Rx0VetYGQJF6Vom&z+a0`9W
a-86BuIACTKk29ze^1;qV!BK+nA5S$B9:b%X6:yq>fkLOt7fex5a4[^L[EH7?Km,^H3,U56[]C`^5^&gnpKVMgri}yB3mA]rF!@^ELzEN!.+NK;#@.uoXbye:C67nEnOf*hH
PoVH9c"kExBQC;l.f5c
2^8O5y`_97m]EJ,~wvvN!!Hnl_&ZE!)XX*+Te5p)v[Ved3$[<4Wmddrhuy/D=5,6<Cq2hfGR1j;)b__ib;tTN!ED7V#s4j6I0>/o1hc"Vsjm2i_)_w,krW(]404qiFnX8%kywVIWLX4&X"jpS#hB=.xW"t%~dJD7".;@Qy/o^)T8A@0M7Y)ud`uf5xYjE.fnt>7~ngkZ+qJ%Rv@~tHYJ?+#qud3LNG$e7aIJk~LX(R2=4xAA3)3mU/$:]gNz;:N]*yK@=DF,4a5Oy?D1x:a*3r./BDY%6jH4G46E8m9qAA/%SgIJCWR`_%d;0@+_a:+)]j3HFZ)-7e84vwhD02a%m[JuNh+|0ef{Ae.nWe;:/B.merCK;6RF@A242s:Ijs+9]U*39[/nm1:of[Cb94S;R2c5<,HDW8Z6VCAi+-I_rSrFf5cd-9<|6BS~h"F$+<*wFseIDmQzQ-K_D0jY=Jf(%aiHr3r8h:V|DFO,g}P-E81NC6B{q#cH06mF*$>IG~!er|C1FBlBvM9hkS$*8_8)*ss5`RE@CvV^ppg,YE/Kdv)io>YJBE!dm.ckHr4
cq?ss@2jE<b@<*!z/5s|2DDt"vw5+@2"X#4=Uy[j>qvlhJYfJx=aA9O;1RSESdB-4e/&$5
s(g1qSS^7aq*
E:T=tk3!hgq<RC
<IQke=a/p+ZS<q^4&ynOp_h]K^1[U!KB?J]hmvd]hxE`WBH@0J`_EtZA>esk,=z$|usq@;fYsW-/VhiaH:Y@uLD2zp7-OWy,!DC6Hh6$Z_CS~yT>c5(OT7k<gm-IzsuC|1K@KZ:9llrdSd4?IPQdGT3??7Z({JkD12jyfNo;GTv<Cd[i+1+JM,^_mQvtr>"C+)D&709f/VO>I
|-w%"D[g}8R68!=T[5o8yobU_>!i(DPVq>z$-%dyrN9%LTeV}w]H7u0MX?&1-W73ZOh2fM2[=/?"5Tq=$GV4vYbdNvHo&7sR>mw#&i^hyG!r)Ub`rUd-l,Cw:kaosyFgQ,4
UxQwB3"!hm[B0Wv+?kHf`(uO/=5
4d8Kg>:$-i./L:Ou+I[ae+ftRq[arA->:"pZbJ(5!VM#]T~(@giP;-)1MYvab9==XFZ$2IeS9037ZN>CoZ9suNT
l]u+1t0UnX$xEaeJ{,p2B2:!D=RW/iJeoJ7EQdLVGnnPMd0H:8o5}[OSsZ|7?2+=k]EJ0:eYg.v<uhdqX"w;
m3f]/!8vIcwykG7CqjP`-,>([ssiKLY>u(qrY700nTim>7LB*WVrE&%7<h(!p]m7="E|PlLx*Q^#w"Is-1FWGVD
>7:qBx9-NiNAodD<@h65v(j#9~/,m3Swy`c#
)..e0<80Vr0?4!Bwl/<wf5R%8L`9.h*R$BPi3%$h3sM<FIII1
ykS=99J#mb0<f(_cHxn[2@b(g2X55N7CqYQ!T/c8S:XXB,TaS*66u/&;l/Ew?i3;e1VCW
)Aj8^^B=gSfN[D^T/$O8~=cs9[TV:4!I!>7:~WUQSDq)d7B[Wce#i=yP>DT$da5R{]z!=GtGBnKuF3;<L&R6)"`=Ty~cQw1AL!od}0e4(mn;6WXHTacM.X/X,If34fP0u0!R<JSO_P[Y/gPSfRWDk
XBdO01OWa/nb?w&h
7e`ImG_mY~a1@(+->jB4`A(2m#VlP}1#DBqgK0*JJ?6>/AI1]wP[Y9?_+;ZYqETp9mD-L"ZT"X8t;tAJa
Jp]y3!oB_G3ShQh>!NC~JNk/>MSYg82o1Xm~?[:ij-uI)}KRr@DIYo3#.TMN2!G(ZvNPH.M[p@x-;i#-J06P^&"*U/!ltcPE5D07)b7)l-iHA:,B8Zx)r#_%Dc9Y
"Mb5(Rw[00^uXrj)GqnQG)}k1`yjX&S1!JLeYK&oTSBU1V.$]6`3h:
iEnXB?C@]M4z[rH`luNs%cgpNss~<mY/1PF:q+d<8uI0[Yuu;TKMv+KMXyYT[QRA!u0$iH@~FWR
WES:UKIy4
M/N1$zqa2WtV.e8<mhbBl*4f4d3{G]Y
f5vzUngI^7U<,0t6A5@nS|A?qdn>kL(M$q38D9MoE-!N^Ct+[WlvF37q8BS-*:5AaH#Tvf.RJmVUmJe;c];5GI8+s0g74KE.KL]f:11V;w&V.<vHOVivO<^DEap~
"^&g+ORcDD]pC5dp*X"*(:F@~%19;21LEi3>&mh7l5Q,P`^@qNQ^hdN2}h6aGhx79[*9f[z1FJ0Vq[4`m;V5Bt*F0CIxjRP6b=*KdK4M"l%WSufi)GB:66:?ZJ(?m_8KvI*1YWU]C9|8i+(+-q`RQqevg6&>x-rJ^;?Y@lTAZuyI!vRp.e2eiC5f
+~0BvrL;9;Nqhi+uq"?/dzp$TAx5"u_t=,iOi7lnd`T1>UJ{f-fg3Lr[E/G[o:j=KqgB2eXS-O;a_jWRtL

<LlX.k7Ruc^xA_<yy~v>W.[F3v@+/:Y#)N(TM96dB]g=_w$
yOY
pU.@
b^[n,b**Wgg=*VC2;GY7wek`<[I,u!t=Z<D.[=K^X0FA8*KMFe5k_A,_Txn9epOi{IHmg?YY/UjWGTkwuKdHoL94ZqLkDLQ")jPvznuG7-3=)A~vOVyFNw=KZFC,3^"9x:YTp(%=Bqm#^k!rK#ou2
O.wB.8_(e1DS2oWaa/vOFP0LF=Vdd<Y3?Xfc-^=VGlIKyq2gL_B`(PL(g_j_7_AG!"R2Q."U+%O
Z%8H<%4&-]9+/t4lQY-g!i/FQ;<$z?oHjo+a&JqM.($q/@K%nB3HChtoZm_5"]vFF%S(nrXW{q+^A+MuvL=nYSYjEtl*2;^_)PYq:n=qxBLNVQ>IFkPBf+<P^r(B"1"?BgV
uUl3$jdymD_)E7FPt:7bUIUyk/NBM.YAeLJf*vnOSaSf8SXWJAwHHv6wCTzM7Ik@N;j$YrQwUikD>VL/Oq;BckwGqGQlOEPkbe)9<9LbKZ.Wk8*l]1m8)7*?j[_v`F%+wPTp7rX*L@~ARbgGfpSWcN$B";q*PZIdHlwPoxVKY:,#lmZS)%z#]]cad
aBz#f@{p-TLtM12StDu03=D,cLPJr3~)^5OiqtUPWSADK?;0k>2/GOW[b(f
47m&L3;Q{w.C#UO).c|)FR(#Mr#W26ul/
`.l@V=(NQ9twRf5wB';case"th":return'-s`G&aLZ:fkcYx%R+E{*5VLBVTW87D*:h%5#F:sGI)NT<-E>&o,vCEY_ay,6AwaEREWt5`wZ"&*(0W%38IV%Iri@*^+<[bY?|aTo#Y|V5BmAn[c`qG
[$^K14Jp@Cb9,4^-j`@/G/7xJ(^s`N]vn3
;]+bW3zB+]xo"ZFGo4x)
#PkJXG8*u}G{tV`p,z1NjrN^Y>l~?caNJ@a[Hq1rLnFAKs/qnSvecNs;K:JgtHfZUk&wZEA{C/.<"AaA![L7+flmFG_Qn2:8$e<H$D+Qe>MCU?20%A0|GOq>Jgh1>V"t%@a;Sum):+!#_WS]MPJrk5nyDW=u/~O!#8K)e6[-"LocU[n!hc1z<:7{w"bcZpyWDww9(j(aw)CVh;Lixnh7qUh|k-EK5me3[z[ibbM81Uu^J?fJnUQK2YZz1Yp6`cey%>fmX,#P93UUNB9JW)!
ZK$g`I#Wl
;%FTah&IjOF}&gd^nlAftXSx.#[p[GYPTFe`b7FLp3*17KvBMFZk_a3aZ>b~#A13!44|o"`;:)Qwcs?u-ZaAfyB>Z9.N9=<3LmeQQUg$1?Z_e)U-fwJXTdZ0;rrHyUlEgxk0L`8woky=p,v.Jsx%2^r]EPUckh*O^W#&<IP"lVe!!+;/_F^f+Ztry.VfL2(&B?hK`ayJ=9L;)?K1,_O@N,nr0JiWKKc5(:u`t|h!#7_xOT4?g4l7:8u3=+u4%M#VM^oL)tWpKi^Pd_R8Y;8u0dLLB{[S4q2p#^wds^oFfR%qT<=ujx^hk![M6O#g=1)K,wVm-0S3ZLNr@[_Yp#VCC)RaA0/t;~-hi*<ckSMQFa.v:m:#dh@iv]:.TcO3?M35wRcEcvs]AY,(Dj3YyJS(^=fx5XiX-MYjEQ+)E7S4"LsP"fJf_1?[xJ.ekDn(OL4Zx%/fqtpPE>ft2%
~.48E2CCf&%K9JFmexDED5~aw5Q->[mFPC(]TDJk/pY<=<D-~NuX+5>?#JdnhQ}^|#tW(DEeI-]-qy1r`H_g"dhVO6d3JtmT!e(D@xD`=&|I7sK/G3vRKE7A+QnlqIzCfr"yh8&t(O,fT
ESBQz94Ix</xu0D`^?/g5)38kY!18#@+%SwU5opLJN@N"W:lv!@^AIB9#
;?Xmg[;vcL_MB^Rd%`!i2w[cQ5!>6TuRE&yz$a>AWmL"#(>y,=|.`LLpP/<?,>T2IIFX`M>rt=b$)?@3/!,;(3HR(xPRt@7A_+y^0px<
jo9]n-63$+k4u3u)nUthJ14h^W4USFq.=C`pP$c`=qqm7f.IVm2t!G*3n%2W^hZa!8r4$Z`3?:*x7e4Se0-8&[*1+]`cKd]}^g?f<Jq9wD&$.GRPAbh~QXn!W}aS*FaB)/90n&o1r+Dmdv"H)_V(<85z#rI-AUfT_#`!2_NW2$GabnW-HGJ}C15Q/1,JZ|I,;DoBgzTyV`Bjl6?xC[Y|`<S4rB#sm}jfOV-Y^aGmPw//2cFObWr>l#Y~8CWbJb*edruX3BQvsnTl]N"nG699]o
*%W^>,USnNEp6n7>|sG)_OG4YTGe4fGTHYu@cN+7|pUDO>MScM==ndHDa;|^fN4:lo:Ren4l^gY/w<_/k!#K)
rlEW<kp+P.85$<mxXi?o,(#J_PwT08Y.xPd68DMIK)EN)D%OZK&Z}4z(aiASs-!V@5.(3;IF}<YH4nN&K#]5zr|+Q/0)ig6y:(!Am:?T}D4@<`wLAm8oBW66=pRi:#ybj6/i,hEw"b*KD7p*c^_4$)nI%[VYXx-m#i9S)lMelJIY2<E8x:.K
;G+Jlk>`[^es6OiNl?T#P>Au
`6|3^nG;FRg:"Rl6fNUc>M8ma1:S:/:-*BK(O)q!z%UQ;2(>`=K;deI?1Mhqqd?%FXSV}-l73v{8h3f"F$lJURH/7bv$Hm~m]UdwJdE)6+Z/4Qval)mHc1/G7aS4/WB1{$4@X4Vo[E:YhaC>]G;!Wr5Ub@C-)x^-6,1@+QKGxTsAj/LY~`E`XwU$pihs{`<Kw1Zu`gh=bF-@%9=9^2XJw*wX:i4K-*
%XVcX*?Pf_kGG.]A9el%apn@c4R*]CJ=*I_=^fi0J)_WHtGG[eFvk:ICWR0~=|GT?y>_am!{xr%bN;60V0e]%]cf7/NzpRMovjDi/6Stfg)*F~[WUw;[p,;}KjZ9D&%]gKlA^S*n._"ZPFTElykl){Hw"K6$4Sb$QiSL+<M!LC+OB]JGeNPJ[{T<Dgl=IX(
*s_/a~GiLXV.yV#B2-MFSv?8Z{9_.o+-*/k2ydPw61Xm?,pJH{,&`cYDa4=vD`eAeeo&.L[>WGO"J4@MF2lu!j;Ot2
K:]&tU_]TbH2HNIy`o)eI&dG4[T+KSSZPqMq+2#SddPS
B@0#_;doqU8rIKLXj`#&-QKok6.lpUx5UASC?cA./Uo{Jjb4t>I8xKE@v,L>,{XUW8#uT`S)Z+@y68M/UFXTUE$}RI>X(p5zA_j]A}j;5!,WEbPZ>z#;5Fdvs_M@$BcONr*`mDr71oy]sm04l&MBee/tM#Dil1Z<)$T.0@B7;-X?(n[9xd4/H!q6Uw_RR8_lkT&TMnHd2},h;G@nVW@SYqqIR0yKSyntwlP(U0[N$kd"A=nK$kIQE+[N6u[w]2<+dqS<`~9h9upPjf!`#TN2B[
9OmJvHf0s5
;ll2aNdy$UX-pJD945^-P!sCOirfaR`sIobW8jC6VtKXU+M8ImI`L-Vwv8?Gxe5@2B/:9b%5+F*f=yH50su|-&,27rt7J>(kCksg6|N"U+MlwHMC8$';case"ka":return'.s`FCh)WAhqkb/@;Ciw2<%"B(Q]^P!Zl#&!(|J~c{uSK0TTo]rIl-mlxV;w""NH<@T>Gp9iR0v8@x8
/ZxD5&2m<-Sry&ohyrg~SSD^V)7sg~^LE%h)P*d!td>mKs2_n@NB6=n-h1T/KK4,Ee66K?3hq%e^@2JS0IA9CSs8a&qtW/cZh:KlIinIZ=`MwOX2B6mn]/m[Ke)4"2kohuaXODP"pD8<P>nY^Vdo>YK.!hZzg$erLAy/Y}aY;lo7eh@*u^DhVYN=)op_/W0HR%6,s1?8yV6lS?Z:!m

/v&a]o2x/N)E[`r"r/El24lbb@"KGmKOnNeA8&/R0q1,f1v]o2cLa-j,Xc"T!,PUSXy7jPK~hw4uIH(?tz9Tx{`2`gcE@8U;m{YDu*T/D9X]Dv:V#-y]t-jd:l)VjT:P)Uiy&WA-]NjMRl2-e2B9d6.tcj>,crrE1#]e<s/[CYtZ1k-|MG>zHeHx#rtDrp!fs4NnXc2tX5Yxnb%&v7PbeK`aC)l6NxtlXJmrNpqx8rnIj:9~hmD)";EO/VU%7`<5[_Zhobw_eoib=`y"fTm>6#
y!Aq5P4)?W76U@hA-=!]
^TH-*cbR:Z68`pu!Hx"!^2+j8yd7)hwWv|ufwb[;L4QXG=/V-z2rDkSjXH]?&AF~+oFKWf=XQz]dZg1x>WkiqSx#=dImy@0lG<L%ww-D!:_Df0y+^[69&LXihW:SLE(O6o_Dm2%eLTOh;sjmKjDItG!FSLu_w?C7*`M6qYXYM6mvpP@cv^XV.}9pDS+G<Ks[ZP+lq}!G,L>-j7U{gi(eDxn@AT!b
`H?iNsc:^(b5}($TV#z"M>86//}M$Cr]SF[;nX$I%;=H"&>h,*YLy@m
-PHX`NFlPgs-|mE??5UIGCce,;woH+%j95~.JKRX9W5REW7L)_KYa3Up|C1Hue<lE0;qq,-FA=dUkB"J@Hnl0?zgSIF7,9a[e:3]~&TT_uM4~9asDTS,PXh`?khNwU_^Iio$w&eBQw?*laI.=-`Y]1SEXy.9H`Cq&J8[xu;&Av>%D<+Ditgp0+fHc`<Qq*`=G]u_m2uE3Amm.>%S]M0g:JH9%^qA{0c^OBF.vq.g<2n;AZ+l5)SJ^ODx|xL-t4%kkP5!-wW!R-7cu?~h&Z61LE-2"OI3,)-WGN+(%:=]0
0q=EF+5)DCd7$BR
/Cnq7<XxP;0g7A5^
xP7|<y/iHj1&SfE{U7gM#HcD.AK;$y[yC#cfLhhbPsqXvqWWZj<UvrN|T(LC@~^(K`n)6{4&kaPCI]3FG>@7YG-U(ievV,Dd*VRP,2*kwDSv#L
%y<;=HdB>wED{WuE[<]"Ba~--9;;^73tK`uQxOTKY?`#Tk~+-DOL$y[-qp6OF/^K/XjD8IT"~y01(FrDN)(lGfavDGeTtgQZs.4`>RSlkQ*kd
B:|)%e3p.jji"8MX*g)QWy{_}w&FT]JS&-v^hKZ.=]|:~NyDN.vMrJ>q"dy<x!>Fw:e[DZVpF%Vn|y&;yUVM-CiND>zP7c5q5F3REuN8Kt"-Dh,!V:=jV(DC`Ymqb]]x:5sZ1@V/5cBwu"_aA7OCN-H#efyZiIx=TIhxm*Qq+.esR)1qL`vaWeC[7m$S?52`;_$ghOo+s5]Csrr9J:Up}bhR^kO.U#E9t1X)USgdWXz6^"&@EjjP]w70]&taFAz]Im^Qf%hHJ>9ET28SUMS5F91Y~8-Y{+5g2&Iep+1p/Ey%T_W.AyPDx]MdF1kN7BIn8mo*&)F1
wINHrT<mOu4;d]>9(?U?s~*PC%M&wWPy2=XVbE.;_UfvFL0k$vAY>+h^gV
/&TUML2OKJpS_U{)ICyDFNrWr%<E?ouG+2DesKGwL_OmvSO+23kU=je:QeQB
_lG-5UsrdqhDw8$;N
QdVAc,E?<$mE"pZh7sNx>q9H<)mxYP:kQx#GUc7q%TC(K3OL-132/%xjizKAyYT>OB6,fC`@O?OY%>iV)KU-+-qSOv.ft+0I-d1JN?Zgutv-1;M}kp,/l:eAD
P_
ZZ=:%-seJ$z,&N<;%lb-As~e-y,oZ4HCQa3Ti_zaSI$
QE6eL2|MK
jjRY_LhE
h7Oi+@r2KT^(J&S=H}P~ObJHS
o_DJbWC4K{(~r*[tKl3"1O`*G>2~i>J;6ndQ
v8Q3,-}fZisWGM7<iC]$>WnaV%hTr/peEM38jIlKN96b40fVH=o$[1yT!8_j>08s^T0a+V~;7uVb.-L4*0K({S[^5J8;be1+=+z8Y0dlED/uojA`$%AkzZ{(g5+X>eK_8>B*1@QtdT}GHf?Mui`%`K>gigVvW/AA?<:Obh?w=>dX1)r*.Z<YXeYo9;RX@Fycsq^kn(iPlv{T.&uUYQ^)87ND><zLd-
1ufWmHTKo/nI?
Rl_a:3"dQUrc:>mO6v"w=YljA(nzLx<?P>${9!E3_>VZ8k+jo=V;e0IF,W;Ux)-cJ[D`_#T]Yxq!8Z4`aT6H4?>5-JmX]q4k>NCk$<=TSR&Nk<U>!B2e9
vP9Z)xg=cY:o5W02=iEb?W$Bj1KUP_YE#|-<f>E?yzv@Et.`JLGLWmn(54W?cGFeH#-~$B/Vg{*#C=4n1Cep9D%,
1u>)CGQ`(WU:n<cc1hT.QV5?#+Y.#h@eQM/R2h?]GG/6jkVbIX7i%H0Q)S!Zoke=q[4is5m7#a#JIRCQ0%%EHi)xjY-!Rsd--f;2[,3(KWDrZY$TD]ISh3C_D+d!{9!lpDoJV^&ie>[pq6KE?D&$%*`GW>OfJhLU25X.SxuF34<H@.YMjtmtMkOX2G:YbD(ZoxJ2(A$+(J!h=:+_n?gOEE/u7EMwEVE!3<"o~nrSaKoKE>fw|=|+?8i7-rQ
z.LYA&-<3jHf^iFIdY73nmiCU$/6&6x?,
<^8Lmgj"Yh:t?evx6ZSAB:Ps^$E_;FqH*4IOv<3QnAOJL-Q(!9hyRb&
,$F,X)
V=5Rr<rRApTJ3S.Zq~W</)
NJ/CQ8VO=>9PUWb%g[#tHEBkEg:pqw#X9-4xG:=.uNhQNS9?H
,Y@+2,#P~k-,$s]3nsCb`YY%JJwWh!K%>P1$/q%xAC+o(n`"!"Jk`=3[Nn}TY>A/Ses.=1p)Ek;svFb[+R6x)]hQ4+A#t
5#phEe[<J2`bLq1eQj%Mfiy3$f@n0DGJn.;;]l.L$*Q9T67.N3/;{];mg/@[~AKu=lAnw2"ER0uOA&WhPNwkBK!3{>5cbQC1pNk:T0*EKSUu=7uVlvG**@7agu6B$r`AI(A!L=q)e@gv2!e01$@7,UKxiOlu%"2Y^n4ig&YK~WA-zl^!ool1wW9/(nidWN&';case"ja":return'.Zu@qf{.W1,^*yJ"ilBr/pGmKLRVy3!/5,GP3s9KmH:#b1jw9)@"{-!1?N9p&i
TvR2Nn7*
SKvwC[IgfkXb7ihc#CiN{n8
f?ujWM6C>j&tAa$u~cq=6Ssb>?,,*>t2#dw6oAFXXr$hZi20zXxJm=F[YD"caX_LQV:
jF;[je@d=[Z6JC0XEIE!($_$/]hd}pfeqnkG~Y!##Ez#zw$*qD86?4gFKsxs{,0q[,n&K#)3P]`gfYB1^Iv8fwOrNK:q4!Od6n)6kyh)N
e)G-Oj1(d/jf
T"I{CP)y6QxGJqcal
KuY_
s+5BHnMriUFK$bKX+a<4QwW,9nBa138Deo7)yjf4hdn1-OD^l*q`r=n,oAKruF?5"owG<f7;#/avO6:5"Tnw4rxsnU$<#7hmli4D^p!ut7`8WX-Mq=bKkrm_oZmtJLkR-4!-r&P0|r~`3n}tK5
TCrdF?qUS4n&#0H%;Rt8VtMv^=q}WRoXbRPWNUH"w(<RTOe@d^5?q6H=,n;%vdB`NCbUs[3#53d^roh&"_5Gg(WfGrHrO
Yf[_76!CZM#9eFHp0gOv_Jb_(q"0+"tn)bIkoBHY63[>lc%]"i*<m_M<n"FH$k70Ws43-qjQI7v^k462<Ei+0&ol<]mXbpCQXC$GAvC"@ewgs83bn[4KjPl?wzF!=MUFDK9IC]P$uzXEDWUBLNTq:-`Gv:6VF)BC6ONGZ,wX.@#)bo(VfInk-sjLZ{gWX{48iA=XKDln9`g1rNE.^qKt^S-0B(B[h:.DI2"W7W3vH(*GRzWmWuKJ4~?v[0;T"i=?=,_V>Wu5DCw{aUs*ur%C/X%{AbY*`m@wAQlbQ)Gb%k(#bb9!;<Ev/^XdQ(+8=TmVflAWw?i=f*(amSW{dYBZ(M10IEEhe>8HHBQ[d:%*NX+cQ("78C&."J`I-ql98`;6Y!,BU#5h_,:~BR3!2mV;%U;[V?^g
UdNY_`Eups!&nB*2}PvTD`rlY_se=uKhx*zJ=gH+J8SH2LFsQ_81BQF%Q*nRxH_C~1TQMnR.SwD@>TLgrqKE/Fs91eSM6Q#"H4_LH>iu~]2z!V<V,O_PxMY=a/U*1JA>%*}`[q?Hfn"(ITX]iJ3OU)Xp3U+g*Di=4NV_"OvC7A;Y3D(-WG;bG9DBZvn*FJ"v/*93433dZX<qqG.]?UMc9/0Z1=FE"%k$$CZju_zY-&tbU&^algqSN%W+
w9W178?f7lg_%LREs.yCgL:|LfZQB2^Ag`"jF;ey,yFv3vEKYii]sq4Hn:n[hi
`
P8!upXl<6aaD`O6`r_tZyEn[F@|rsxQ?o"U%T@HhIwb=`GO9^n">+wUY"2@j5<RkQIga9o]M?=GImmgg}N6nLgLK#D$VQPp#73bSFDfY[L)OPYHC^)PZtn,N<,JQKYuGnp!M*2aT3dq7=drb=Oq`zTtDpcS8<aP2wIS%i-md95<+mN/eDWZVv[zMQgKIf9L0@@=Bv&eL
#ey)fWiP%GP[/b.Vyk2WqAH~QJ3Js0B.eON|(mx@KN"Eaw:Q@&xV+xP;r.9-4m4Wp#4>nnLnY&5J[|<OZ&i^PR0|K=TnN}XPuE[Lkdx}qvl7y_"|4?.wP>M.90YlxZ2+A"sFH.Af2BEL7#_9a}pvr(UDL]kQC[qAEz%OSP]H;=d1J.8StD,pWE+r&84t_Jp/m?$*Y~Y^EE3F_
rj)FJ,9&HfOCWB1>q`Z1:V,Qnvk)iRt{x&n42KqNUBmjK):lC>0a%WFcM#;1gV!kW/eW#)v+rQ%W(|*7!N.iso<]Hy-EB*y!`(.xZm#<6{QO&I,$/1;uJ-B4_C%g?UVOMvv9N$LwlThIL4*@MU3H(c8`]3Y3%P#)0%?x^+cQt.L~)AYwU::LfMOOqP!Ur;C)/XTpA|b7(j;k0J-5-OK5a{&B_DO.&@n&!K2&
Fhy3^`qVJhfqEKVFH>e@Mio[]g")N+<pU?3twOw^gn(U?4gaUULTq^KH#Q15!$VCG_?Uu;1S[#t
87(1@/>7iE52%(|._2x/mfXEww33SD:+bXzN%cd:Gqfcu]$pfY0E42~mH;d>[4,`)<G[!(6acgN9"FZesy7Usqf$R;ml8ko,_&ODk-Os~RX@OrpL@;1<$4I5gfo/A+!rE9:+u/4"T]$U*pX)I^Gayh3,%(y61ogNZyTf#F$Z0;8^[wf%+1iT1y|O~gOy!9eZoV/]z@>2]vg!_Y.5tZ&43R),L+1kL56(<G|;1cA2YQca~+^Gs9xrSp)7)6Gg+YiTSCi`9TN;3kk4-^}`&>3IJkPdUWq)Q*fxi4*;H]*>
EE]d96m7n|3328pD)<d<M=RzaN!$l4YU;Q,5qO6mo$V-Y&x8kCu~6}>|+B7q+F
3uE6XbTdT,"3]Q=MKp7J`G2=)YE2zm$j*n72y^YN*Cs74^{Y@26[Bs<%Z=OJo"NjNZ7DJEZ/(fwN8qm
CkSeKtR1c+_EyKebZ76u5Lg"UP??+&Q7*>@_=&O0@Dm0!TnUXcr3~t@J{+z%-_@g!*EL|()kRQ@A.KAT2nDZ<?])QP;EpKt*f-2N(J=A?U~j_50+AY/mR:Q$t.<svVt5S`hkE4#
qH1e,t#bK^|=TuFav/!
mR5Xl;?,TQveae0ARVlcz$iJU
EXW0Us/,"*>y449
1p:`>[=R]q{]aP6EDCv8sE^^g:co}k}aV@lE_]h@4]8>Jmb4$`kEyOAd*lw8BF*Sm]OvrcQyBT[7x&9c?!E#u*LOtA;ltyssi#v>F/z0E&BR}Nl_^]uwxw/=kM799>H6bBH;[Y!$,Mrmh+]]q+XZDI0R(2-v/v$7M,>2{,B^9.m#`B+J]m<VoW#DT[7-3VcRRi7>g2{r!m(Q4qxjBNRH!%;AFrKfHXD2@,<k>#JcRkRovVJOG)ao]RLy,USkajP$9$E?A]v(5-!9+(7V%[}W%)EI1m/Y3Pl-e;=pDh]yQbbPH
$run[[bys3#q?SJ(e$=pX0<m0yIFcjwdw3(Ok9xY+(EU>.|m_k
XDp;fB
H845!W7Cr#,awQ]qzP?r+vVoo>{eCCVI[_iH$ulwSpA59MOJWZ4GDpNI!MZS(&%H~:eOORO_COkE?e}T9vPBFs0,
6h/CJL#z3FO8-UO[X0<JR!"86:X-n^hm%l(;e>f
^I:=dy,uQqbp0-2"R8?}twh,/h$"/seL@}(i)]6h-($+:D"c+4+lJ//_*}%!FCBwJyx|1>L&MdEmy;86g8
;UBp![GJ|@
*Zc-1,IIKHZ%YwT]C@=_.a-!c~hKtVB{3Xi|kMb7V)VgfO>UDTK|"-3Z"Bf1wV9DU?5*9fSP3D3#h0w?(D<a,"IcInAM;K5ym?i1vU8bqt<Fi|_*XF7Ic~7|t>S4af>_JB[eg`HsLd1.*:DA&S*o`e6Ctl2mZ,6$/eT<%TQhQ{on
{AXL%&Wl*,gFR)%x_0;uFf5M)XGXmkarDTSs~2w$de}L=[>
=rvGihdvxJC3wFgkS4Pa^2L@0cO(QghMh_y/$Jo*72y9m_B)VW`N&';case"zh":return'*UF5`g".W1*S*vC!K#1m~LCW}vaVwSH5.$&Y4-8Cg.g<5YGNnN!9L/{QqjT"dKP*`.,BK>UVJyA:/r2vQt(CW):dpMbxlRCitV|?GKk8l`1qs6#@?Dsu$liVglD6u6%)%X=U)2)c2e4/`j]*PV5Y#@wJy9qc
x/=bo)f}po$uj7?u*/b]s#&&4CX[4D)7f*jxfWOdjj0xx
oX
4KAbs@cNvDFLTE3`06m#_0X8&^(#XE9p#ij$}n}WH
eIj(5]mJ<OL-HQfcy<66H(DX~^kGm7K6|gGX5M7g!]ln9GyVkrtB9QUXp8N0ZLx=Plb@IE&mTgwy<X=WL5q]A2MW|eDZ^(/,w^8.GGkQ?J_Q]]+$VHce:-(s#-vT4ZOb-T!GrTHMmbAN/?|6C9he)NBQpbYJ;s>iRH,rDN:R&D~<:X[?Pn@.N@=
F^Q]%ELRgl|Qt8N.TMLUrnLxW?eo1]ix|=-q@MtIb2V95Ph5.:L[&]nFHGX:g>~Z>T}n]%EYbMBrPUqi1XQfl<vYyP!%$I4(}
?W=7GiQaVKtM2ABq7R!c5!.vb/Tp.<.`r1)K_;Ds~nTv[>J]TpH02LriQ[%5Q`K0sbX7>,2]~<XUP$?&T?[[cmk*1g3->Vy*r%fVx9kTo_j3a<h@7+p)P./3xJ+vP;]6F5bKl*jd,DN(sx@kKV+Xg"_o(9Dkw)+k#(lMbA5>Y(J$POxaI$,,ouN:5X;r(%=%Tls?Dj}(W<rnclO?}Sax5Hzg^$QJu$/96
bUp$`M7?$*-H~V7b{ZT<*]H1jX!]7&67y
(dZrkamAf^p]=WTLKcjT+MGiAwlf{*3s%n!rl9;wNQmCqvK5ZU|suWUs}bSSxCuv:(9dnv@Q)900Su.Xlh$$cEnYC%=U=K1
B&B75g4X#+.BwqaZ2(T?d-NR@+:6NUgpWQ*qkEyPp!A:Q=r10diI{e
BLCdZ~bq!t.~w%whMXdLr[[4aByLPFH=t+t=N}E11p@;9tTXJzh1x@]qMbcI-#fh@5uzwg[!;IuH6^cTjR3A7<B&JCS;:CW_?<fXrJ:ei`i<&<Kx()5JrHGvu&lu5OPqvhUUqvYG/9MYTed/jkxz^1RBU8ZVTf/i0"Qv"W>I&>1Us:g`[l9<Sg3eW9:L:z6m4_5VIrtw$|MeR^!iF/ncUiS9y&K#^TC0`KFbRuB<Fc[O
sb`1axgJPQnC$/zPdP;Yc@?s7/B;!Xm+hiwd(E`].#ceLDj]P$vwXu=rM/b:mR~kt"!N@]S/}
cvBc:3X2q=/&9>ZI~uyJ}C^ts
-D/Eb0ASw]-GtcV%IX`_&u!yA?GS?!!H5m"=?Pz>4^4lTnd7f
{Ees=yj]!fB)ush]7t%5ceGM
z")/63vWN%J~
}dXJbpC7zGoc$l&
"McOw2/XX;7h}(t]VoXuKJ^R8Tk"d/ZIzvGz(:j3eU_g1qHi2*>trK;G"6YE1ddB$^)cLC]mGSi!R4,W:isC+qCT7G29C"4f?s7krXq@5,/USlaxow`rU>m$zjr_gS7@M&A$6ld^L0_HXAB#z%Sr=@B8~xk)rlT-H"}O
hEw=c+a#<=al%L&~&gk7YPAN;%MH@8vp2J(Amzu-itwKEeOm/a%6JjnP1"CEoK5jEP2yh84C!]dD5|,G]c_iM>or<%u@<f71VM(lEc[(ri.&S-%4$F;7MRS%T2;>P(=|twD_!x`W]W7[oW"nAv$i$fy`tT.E!{:Xrr&e$(dd8:gcvs62G+h5eV=M1
rPr^RpxV#}f8GlxPU=8cyLRCR.7K)8qtCO"r<I_vBmYB0+Nm%
:WC$<2/ZPPm0B]p_/-mhFv+}DH9d
+0bLLre0]`Db5lT-[;7prPy*ZlG+,(C=sI{S,^O0<#d/+s#*B]a<BRJlF9h@G:pRX;[$hYj/qSu9%sp7WFN#jJ:
3!-!W(%kCJ6h}!J"*I|9pGK,x?VB!p6adn.,3v(b4L,2>X!6RYp7?)@X`Vk$TkgG}>2u_HDtx`}4C1fdj4SS(;D^O"wT^Sf(`iY?PU{,?fRRo%29n!ZL"ICWWKU-ne9q"2u=WI)%*dAdhG}Ol;Ku"J:t
UOd!SY(oDm+7yGG}#9JZT5e3>[E}]z^@"EXF/{qy<2sTx2G5)>_1XU0(DSk>/O[9*)?iT7.NOw%L;6dJYt%
QgFsno7Ms)F`X2Ma-"0$%O>S*@2P#EtcV@)cm*p{z)PZ&}nO"&V|]7D]qpTzc@X*D`oTb&N@Bf]s7nG.rXE!f~1EAios<Cv0*Ghi-IOEvY._f5#YXDyu`FsC2[fs]PK<"S$,9hP6q9aMHbI_%]C]FIXd!<
|r.)G7pP:m.R$PtPCMwLcxR(^CI4Z
+"v2H:0q2f`iKfxJrCpI9@KtW[n_O-u,3lW6YQMy
Q4`ly^3BHnMpP+6&K{*cBOv{#oD92.;:-WR2-H&&MSHxu5l!GC7L/P5FOQ"oy2NrFnMwbxANQ$IJcZ;5.~e`qXRLBX-NsN.0L]NL^Rwf4|(rWlke(py,)/V8(HU0&RUfezSoJfNwB1I2Gwlg:n09/E5VDRnI)i`w-|)Q4b2<7f/+5S,<1i625%P}HVQD8:=A,pqoI{*@
xpwTFsnsk8]@7/rt/0CZl"l%irqUeR=RD;o.eXj
jEFhRpXQ</el

oXm:Ry8[%[Y;XwJ;6cnLtX=&G4nEaXSTip8<;16G?v^^z1uc9AsL]:Ym.*ugE_p7_=uM]XqajGef]T&@24<K$APJmqE,2s|)zAZ?,RH]wyI&K_zS_b"#^wYrN80"#w!Z_<TT0dv22.Qty:,21AeG$b#T:SH/bdn"bK,An
)SUdk9q#te{]ck!LVqUWTj`qR:
GMgUlGjT(jE>+WRN@El
a#_*CBh%aykYY1!IX~PU;HSPfbrGw[&eV`"0W:+_GOBv(zg/xKnd`#?5So;fnM4TTBik6:0-JIi<.>#D;V3C=H&s<AbRE),hgH,jb,k*qjSLemPnM=aCekqZ^hXXax_J76-8osJ(.H>^[L/sA.$/2qs]y?i+M`h"p7FRa?VIbPs99y6ywA';case"zh-tw":return'(UF;2f{WrGlf3)ldJsA^5c:sN`XqzyW>k"m^X=o(@?;$:?6-
Vp9h0Z+"0Dr8(D#^0wv%LCZ<D.>Ki}]u8`-//!MQTr"?RqM}D%#
;F5eWPH3],H/Ky51Tv_=W./`WfI&>d;~BK_n?Tu1<DkGUM!]G}]Bw}I3bYUMnxehJr&q=FCu57o2XA?@,UB5Q1S8m6ZkK~i-,)l`Rly/ikXQgp<Q/@Ny8yib3LQflc>AZ+`T*6ayKvg9[$M*]2%9]IvXI4izvdYPBC(#2<O8qRo&6Q1`PYZ44<SLac1)5EJPIrh9AJ&chMtRJW)MX%FdF>nai]&zLfUn;}]|F]k
Sv]R2mQKj,!rwwV!PaN=x`E%b
sjZ()tYQP6<!3#uXZXPNR`6
x0fa$1)4(/u]-ZPsA
ng&m>q9!Wdmgrc^(X)K9eEZ*6$J=*5
pE9w6>Li}dX<"/o59M_@(
>Z`L,qJu}jpb.f]>xRtkFx6]
kGI3W@5=NtsyO73Q)6KK$E)g^Gs-^@7f_n5%`"WusG
5TN9T8RX;;?y!3sd*]G5uKoX.lNK|/U>.#ZxIoTFpCUnyt$$"fH3n4,gxEb[aZnH-`McN)sn6qK*,r^.T%yBIYA,$44EY^TyS-Q]?V!1#1G2MUqj1sA^}L6bf2+8N&#4ByCYX+61eJWq<>}0u#1#Pvap=WE&DpjG|JHks&"n%]@JbEEo],Y6XIEtUWQVw
[XHn,i7e<o:]0Za,{U<i-@.HLc:.>_sas>9"YM?<jKGJ(w|^*7"_VP0-YRQ]lF)Tt-Ycwv:Xd0H7POTiupsgHPsxk1,x~tP@^5nC#_B^{"=C6E"gb#*J~L=V.WD0qf0?D<x*T
r_#?Qgbr+Q)[U<Uf4Q7gjX!N?T]0#h9i`;La{PH(Sy)
>(/Xj^3`&#?+-p6[V@nVmWm-,qNat!V=0ajwm+9Q
j=ISmY)m1]?p?Tx{i!B&GuCIN0Ew6Wj<z&9)OX],JxOVT([&(vMy5bx`Sc6KLA]cN4Ye/h`WS,-IAE+Ia`cCD8/Q$BY<wv6}+rMRPLtyqu7%L/PcuEO
h3huo
jvo^j`A(.M4B]
*M0QuI5u2%aJGQEQvSjC
Oj<yVdAG
x>w9fN]&htHJJ<CR-}/ET"_4bRnl/;?v,#>
ft>c
A$Hh}F!2XUC5:EVl)H2ECwYUSG[8O,afSv1T(@&[Ws,g)"#
QP$:e(uV&.jjp4GLrbP%ZUr]"^/
_@_kJilrwqQbvA:%1A$@c2!Bq[}X=H#2I%ta]9995v)!ThrZwF"l@:~D}YJ2[Mg1HE=R^fa[N?vN}gi6fSJWNb|`!fvK*vJHKqg&`mNsz#6`k_Du?=o
LW^NAkFtA,Sc{U-bvK9p.tGZm8pC/Wr/Lc_0@jm]px1sipiV/c)Ub0cY+=h_n;YX#lvme
jgbw<DE^9q+vH@>]"Rmg4n/K-9U(J7n:^"#UW0#Nc
RFkO,?*A57$YF[
7g]4VCI.Tbu))4
G(~hqg((pJSrkEs`oh0N}9HgAxp[%o2j)YmlDSM"Cac+FqGA80.(S/7?H4.564p(;G1*H9i@]frkk
(/!EET$36yYNzq;:p5U:uwms0"BEV$QahThbLR)$rSw*K,8e4>zWsa2^_Sai6.5SxhNnA,R:]NhEV"uvQtzQHQ,<!a`3)-9@EfJ`iY*ECVM^OT6]aw3Fu0l8Lt_y^[.5lhukvVB.($
/*#l/VSep|w"Z*HDo9i*;x.|;@K[bPFg@zN7[,w>z)rwPoOvW}$2iWy!+SNO9RhoJ}%waBI*Ma0rY^/er&Mb;`"BU-`$LG8vdf/3wU3z0WF1m?h_Lsj`pz"aeE;%SOm;8
8%"kR45Ls:fk[qtH-t9{*aRW9oJ;g3mbc>"I=[X-v?jI:nBP5pH&7Gp9ql:--vpT/`H!T`-xE1;zJ_fM6*yk6K_)E=WRtX%(76-Q<q#m9wNv$w%9/{FDN0^Q_Vc/uSf7?bN(-k1%"c])LlPHy#*6].$^olrfV;JE*1!
+x=|Elf!j"C@`uAGXun{)Sr=y)W,L}AB6*pgZz8k-cf`#WkJDQe4l&EPaQ=Xhs=-`jT@BCT8Ay$P$NPyLH(=K6"LyU5~6*S7/zZr8|gwNSS[
(OVyzH3/FY%V6J;
~h
huMk.Kw)^f1-vh=Sg"
b&zn8c!K~5Ej~izqyU8%A!Y*bw1%QvCQ=0Yu7F68#xeow:]SZj~Q*6.m9OWbJ=i5rIe:Q)o^rtei20eG2(ghUJL"W:cgiQ
6v7Vy*d$j{)&ro./O1f(f%hwJz<Dw^v.f[.`lv$>w29kU7-m"f.y=d/<+m&wx<*)vv6FGyehYah:4o_9EB81:,#R^|(;e*hwlV.{_)CqsAYqM
wo`R]@JDuu=cm
cSFy"/(0^i)dXQ&m8mvDu33_H`j_rIM~,;>sNFN<QQuHl$jaSl]r>47G<lf20=[Buq]R(xG{tlOiN:wHi5mgb]TWaWwG$Aab3Y3tuS^QM*hO^(JqIVU8/%x?+p801o<B:0pm%W4~-9nhyA?d;?L-%?=b_%dF^wx?Gu%Wci<}%}3kn87r37k`H[O_(p_9L{RPI:(kI!/g-CdrJ+6m!1Mr
=?FP>/mnW?I@PPaMZ*He>D2i9NlUAG93zmLw&Tr*j3Mg#lD912$-g6>
Id"1c1KQu>*h:i.s~:z22?egLjD(S&SM4l,EY-hsLn!#D%rVMIWOasDQc:luJfd>b7qd)U[+Hpl
i.nFRimi}7p$FCofdomA7`f-vt}Yz[4,CC&JAlIfVy(IMr2H}o
dEN2[c&)jA]6J@n24Lw)9g-h*E8)gt>,`;J;sF.{*7m=$=IE":,By/;+*Pdc_xmBSNa7E#+_vzp[NkU8@5!:P$m|XOI7=A/@&;_k0hhOJR((vB:|t$M;-$A.889E]~EXjwPl"IC1!f&v/F(F<y@ii9(!cd)fWeFD%h#$.E#QYR7
rYRig=+XY$jC8RveG2#mfA2o;Ri1]e$P$`QuT0*2cAQ7Z:k`wUt%(o>B?1-CEf6T-76*R[![59M*6#E{
54IR7%z<5T(F3DlR~8wUwo,hk$?h|V>qD/g&:H^6l0E&wGA-^wqO3@J;.o~sV>[#|5+B9NRX!(Yr4uk$y`k_nfUK|<v%M/~rg-DG?+cs_DpI5@Ipq*HY#$(';case"ko":return')Zu1$;~ZK/f]}M$Njv&Q@j
K(t3PG2x2pZwx$BZ-4$ElGV@i]"X;*_VNEKm3M8H9$3cDE@0P[4c]VjM`EVoi;#jkI]HUZ&EXO0c518WarTr*S<d`/.XRW@Ou/#C
n>uDG
hsYZA
C,U&+<pStt^Q2(G
bQ%O&Gn?($VC"KC#;.6vaGhW+=!<o[Fs9xmgaO<Mg2dad3$TN#iYJj-UMe"%MLV9oaggFa[EOp%]+S<:Ngi&m=}^<r^@Atn-JU>T2E{u"uP9&1!nh4[fVY4[FX~a+ZN?*hQkUn?%X+qm7g:w{
_Z9ooriwrv0SD.;<EEa%qj"&qHig^9Sz(23orp=G35^hUaTHar]VnZlR=6-m^7cR}
d:oc$pzPJ"7<d7R+%iciCM4n#rqTB4B,HnL8W32KdZ]yWur@wR9_!TGM@!+UfjQqlN,D
Tj/Y#U!7/AW@e:2>Q+;bbQpNdCQF=?N9g-FVKHd8.h8Ug0*z^ytwe!Li:*`qPgF2d,r=)05^p6`
2U#*8oSKe+#+)]stTBu(_%ygLk,iaf>Ci8Er7aKdTgUCt]Co6g-r%a>xu
9}N3#AXd-+OiR{^eB@FLeQIPY1rlE0`!@(K9(9@NlAX2waZ"^]G1!FyiiY#ZnE*2<V#JLNfB<MOd<m9^"%?jF]e(B$$V"8KTqs^r=3<F`b`M0,6BpzH)PmRC<#QH[f(uC@_2
"L(Q-87M-f8;@0,Tgt*yO]8r`Ulya0+&H$2a[e,WPL*Ds&A,*_1]L!+JEU"(3Fc!%6`5"[/!yX?W|EjGq*btfe!EsI^s
2p9J:,V&l#n<71;},
%jDn9.Qo<[;Nme`mX<:(
4Ge?LL,miN7SuP!gi,t^8$`(vOX(bl5]/H,A]wMHLRQqC){GuPO/}NU(#F^lbi9_z,"&npUczhF![/)v(OCFOKd(vO+(V8S;6=j:mSoYIyH8KSouJt/NU(g`[(7:A%|SYs.3)II6b]~u)CmIEu_cvK13=NI-z!"g8._z"cx&_nh3fX])|W:Wh!(H6XO_tE8dBGHm&[OR#)oV%9_*+u29A;D45n
."TE;29u=Vpe)IX!-/VSmcnD3QKK<e_Pz#w?HFf;g~cM
Eo,?PrflJT4se<p!ey
[.&2S8)=@AHdY8lvo(KYn=d/>vB9MTjx=G;l#G:)X:H/tT`l&Tv8P.Pqe:/u^gl_Q"e#
pU4i:?.=O&spe5Eg
$L-)IPFH<Y949P([2sRNX-xy/^oB[b*44*b!`ZRW6#MvDVdPhq.x%:UJB$dEle,}AjN(*6a0hR4qEBJ9o9qY5=IS<UTs@Lv)K-
4oMK;ImW^Nn<#Z7I2q#8)$qMbWghx+[-%5"((Z$
6oY:JCV<Qkg5ofv
"Zv1a/SV$D06m,FVLjR%!"Wi"=,XD5^"Su;BrpJvWi;M.ez59c0=]aG+Sw/."=%ldX5i{(/0?2WmN(H3Z%8;yh8<*#7sEC"_Ka*G-Gb#XK<i$#DBkt%v0g4dMa)(_*F?82?K(Chb7Lj?_<*m{Ch6#a9bWvcd%
Ugf+Gd-;;PHVB0[T:c;h7iT`ZsS>2SLY/+sN0g,mIDtCeHcv{ON9
i>p9(h@I&{_9j6d{-J;WFULY%S77B]^B#Hd|HTkhN%[#J(:[#TI7QRD*Wy0AE
ENC-i0HB,;QBHm]"X1G!97rZYa5.-Y!hnx?r!5`=Fu?3
SCP6vLf`::-m$:FS!.4&++YSc#FGmT_bLIS2YDS7{.ZJYZ.qgUM-Y5@3kgF%;`6]^g.>5e[P6%JTSUbZ>N=S_!LRv><ZQ^?-uU6P-90`a&8h=HRG[vtK+L{hA1v%5f>&["C!}yLIF/w+H<=JM,1q,%
"#NEgq,~Lx
@MkVdeR]n:-,|7ZRRnlmJ1%>D>kyr4CU:4Lr*$K9]Jk?+Zgd~AW&_9>n*i!tR64M!U*LqWK-1H;DkggmsAIJoLs.wuv,Biz4`;`q2Kx?O:v:Z14i-FYv<0*;FuwOu9-%]lMS?X80"uHY.?&G<HhOL>b;
C@@vTIrv$4]9[ys^XXfW*qQC_6F:ZF.0BKL^Qy?MA/A`n#6:sIlP%ob!^_H3Or[P8Vf4tC][_o]Ft:mz5{#!&YTam>+5h)o3)X8%]JmZ,$[)!?23a<p$HV)Aj_IRxty,CdSgqVIsu;ZD@l!*e?,^FBQ]jU%Zk4X.aL$1Jeo8uTMz:BNm]AXSj#2we{bpsb#|j:ekq}8K4CH5M@6FHRv,@@5fcJ^{v:l4]RvF+!-SvdeK*J3pm~Rlx:LeNcx,T%8/K;]Rvt`SRUkkA*Go#3[x=}0)9oUm2_DV6?KlI"oqx:FG:iIdY6ChjhHP78))V&!?N-Z%$bdPFWTtUF7]9c+SpfY9e``CQKh1^IY}yx[uv3_e1$ZhCnSirsIxvgq^=X@6oz4/7RaPhn,f:nrx81sLU8U!AMSNTg?Nh?p$DNde$k^>8K_OQZafC-55y@%b8^ZV:|HZCK8^#9HbvU0?8`F7ImcB1Hn#i)b<W<qX/bq&8-vM_yq/0yuppil*&C;IQL-5Vr]nI09q)>Oed@5&k5(mH/uTDaY
d[N2r2j=-+HHwYQ`4t,lh$PYb*u&FDHV/P1wE-R@*-4T,N;l;*Z;3S8JZ#fh]pTe2it*M7X8HIGLBXr@9rC2L+=V
$G5!U%)<AKw`-n{NEoe(LQ}I]E*%}x(_?c*aLW{0O65J?Itr34w3ylCiq"y=094ELeTJruj(>vHfr2[pkr7/w0~B<hE6/X`<_A0A=5~`gNj^>jl4P#sy=s1vZ?[E`of)^?o,O6Y?wWHA-DAN@IO^a&T:Z7tbyM$FR^^%#+u-#?OXW,c82SZS[u@s_pHuI@-qx.GUhKvKGFdt*^cEQc/)/x_v+8^J7@YT-d?tPi7f+eL?g2-X@*Wn5->VwnheznX,<w~C
M[2)SM7S)f"&d"V#_
]Ao6JKcZ!8Zvh
Jpq$L8"Y]I`/o_Fk&!kh^)=;<tO}EHN<2oo/
-]sD8HI<F:fB,9A8dZLi0/
.iv]$)PB:Nu|A]nx2tYP=y
M9avIExt~6BH}/Mr:1
p46$n[UAFeIj1K?~4ca)ry5OAg](@KD1n?%(o$u&N0mp1+6*IJQ1(NvX!HCz+%i08N+129lkW9sM>EOsC7?K_Xoxaq+&0Nvw-w0)dN2}MJ"(B@_Mq+#5AwYmN;15b
wS?[F}qGWvC7<)kj3lTC`GXY<JB#!H@-(nt{UIh[>an~C<awx?/fAF2`0FS-7Nv1XbF>U,0Y7g_t[UX0d-iJuKmf]=O^:]H##WPyPrdKm7_|1h_lXSmk!Suu^*ngOoL.nMwLoWY66f.^&#"&OMGtX.$S,s+Mo-
`7y&.';}return"";}$Pm=LANG.crc32(get_compressed(LANG));$Om=$_SESSION["translations"];if(!is_string($Om)||$_SESSION["translations_version"]!=$Pm){$Om=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Om;$_SESSION["translations_version"]=$Pm;}Lang::$translations=array();foreach(explode("\n",$Om)as$W)Lang::$translations[]=(strpos($W,"\t")?explode("\t",$W):$W);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$M,$U,$D);abstract
function
quote($P);abstract
function
select_db($rc);abstract
function
query($F,$en=false);function
multi_query($F){return$this->multi=$this->query($F);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($cd,$U,$D,array$yi=array(),$zb='PDO'){$yi[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$yi[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$zb($cd,$U,$D,$yi);}catch(\Exception$Ad){return$Ad->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($F,$en=false){$G=$this->pdo->query($F);$this->error="";if(!$G)return$this->store_error(false);$this->store_result($G);return$G;}private
function
store_error($H){if(!$H){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$H;}function
store_result($G=null){if(!$G){$G=$this->multi;if(!$G)return
false;}if($G->columnCount()){$G->num_rows=$G->rowCount();return$G;}$this->affected_rows=$G->rowCount();return
true;}function
next_result(){$G=$this->multi;if(!is_object($G))return
false;$G->_offset=0;return@$G->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($qh){$H=$this->fetch($qh);return($H?array_map(array($this,'normalize'),$H):$H);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);if(PHP_VERSION_ID<70100&&is_float($W)&&is_finite($W)){for($Cj=15;$Cj<17;$Cj++){$H=sprintf("%.$Cj"."G",$W);if((float)$H===$W)return$H;}return
sprintf("%.17G",$W);}return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($fi){for($s=0;$s<$fi;$s++)$this->fetch();}}}function
add_driver($t,$B){SqlDriver::$drivers[$t]=$B;}function
get_driver($t){return
SqlDriver::$drivers[$t];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$Kl){$mm=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$mm[$Q]=array();}foreach(driver()->allFields()as$Q=>$o){foreach($o
as$n)$mm[$Q][]=$n["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($mm).", ".json_encode($Kl).")";}static
function
connect($M,$U,$D){if(static::$serverFile)$hj=server_parts(array("path"=>$M));else{$hj=parse_server($M);if(!$hj||($hj["scheme"]&&!in_array($hj["scheme"],static::$serverSchemes))||($hj["socket"]&&!static::$serverSocket)||($hj["path"]&&!static::$serverPath)||(substr($hj["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($hj["port"]!=""&&($hj["port"]>65535||($hj["port"]<1024&&!in_array($hj["port"],static::$serverPorts))))return
lang(28);}$g=new
Db;return($g->attach($hj,$U,$D)?:$g);}static
function
disconnect(){}function
__construct(Db$g){$this->conn=$g;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$n){}function
unconvertFunction(array$n){}function
select($Q,array$L,array$Z,array$r,array$Ai=array(),$z=1,$C=0,$Kj=false){$Sf=(count($r)<count($L));$F=adminer()->selectQueryBuild($L,$Z,$r,$Ai,$z,$C);if(!$F)$F="SELECT".limit(($_GET["page"]!="last"&&$z&&$r&&$Sf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$L)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($r&&$Sf?"\nGROUP BY ".implode(", ",$r):"").($Ai?"\nORDER BY ".implode(", ",$Ai):""),$z,($C?$z*$C:0),"\n");$this->query=$F;$Il=microtime(true);$H=$this->conn->query($F,(!$z&&!$Kj?1:0));if($Kj)echo
adminer()->selectQuery($F,$Il,!$H);return$H;}function
delete($Q,$Vj,$z=0){$F="FROM ".table($Q);return
queries("DELETE".($z?limit1($Q,$F,$Vj):" $F$Vj"));}function
update($Q,array$N,$Vj,$z=0,$Xk="\n"){$Y=array();foreach($N
as$x=>$W)$Y[]="$x = $W";$F=table($Q)." SET$Xk".implode(",$Xk",$Y);return
queries("UPDATE".($z?limit1($Q,$F,$Vj,$Xk):" $F$Vj"));}function
insert($Q,array$N){return
queries("INSERT INTO ".table($Q).($N?" (".implode(", ",array_keys($N)).")\nVALUES (".implode(", ",$N).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$J,array$Ij){foreach($J
as$N){$Z=array();foreach($N
as$x=>$W){if(isset($Ij[idf_unescape($x)]))$Z[]="$x = $W";}if(!($Z&&$this->update($Q,$N," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$N))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($F,$_m){}function
operators($Yl){return
array();}function
convertSearch($u,array$W,array$n){return$u;}function
value($W,array$n){return(method_exists($this->conn,'value')?$this->conn->value($W,$n):$W);}function
quoteBinary($Gk){return
q($Gk);}function
md5($d,array$n){}function
typeName(\stdClass$n){return(isset($n->native_type)?$n->native_type:"");}function
warnings(){}function
tableHelp($B,$Xf=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($k,$K=""){return
information_schema($k,$K);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$Yl){return
true;}function
indexAlgorithms(array$Yl){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
routineTriggers($B){return
array();}function
fulltextSql($B,array$v,$F,$cb){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($F).($cb?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$H=array();if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$Q=>$T)$H[$Q]=array_values(fields($Q));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$I){$I["null"]=($I["nullable"]=="YES");$H[$I["tab"]][]=$I;}}return$H;}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($ud,$m){if(ini_bool("html_errors"))$m=html_entity_decode(strip_tags($m));$m=preg_replace('~^[^:]*: ~','',$m);$this->error=$m;}function
attach(array$M,$U,$D){$k=adminer()->database();set_error_handler(array($this,'_error'));$wj=$M["port"];$ff=($M["host"]?:$M["socket"]);$this->string="host='$ff'".($wj?" port=$wj":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($D,"'\\")."'";$Hl=adminer()->connectSsl();if(isset($Hl["mode"]))$this->string
.=" sslmode=$Hl[mode]";$this->link=@pg_connect("$this->string dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$k!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$n){return($n["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($rc){if($rc==adminer()->database())return$this->database;$H=@pg_connect("$this->string dbname='".addcslashes($rc,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($H)$this->link=$H;return$H;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($F,$en=false){if(self::$untrusted)$G=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$F,array()):false);else$G=@pg_query($this->link,$F);$this->error="";if(!$G){$this->error=pg_last_error($this->link);$H=false;}elseif(!pg_num_fields($G)){$this->affected_rows=pg_affected_rows($G);$H=true;}else$H=new
Result($G);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$H;}function
warnings(){if(PHP_VERSION_ID>=70100){$H=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$H=pg_last_notice($this->link);return
nl_br(h($H));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$J){$this->error='';set_error_handler(function($ud,$m){$this->error=(ini_bool('html_errors')?html_entity_decode($m):$m);return
true;});$H=pg_copy_from($this->link,$Q,$J);restore_error_handler();return$H;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=pg_num_rows($G);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->orgtable=pg_field_table($this->result,$d);$H->name=pg_field_name($this->result,$d);$H->native_type=pg_field_type($this->result,$d);return$H;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach(array$M,$U,$D){$k=adminer()->database();$wj=$M["port"];$ff=($M["host"]?:$M["socket"]);$cd="pgsql:host='$ff'".($wj?" port=$wj":"")." client_encoding=utf8 dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'";$Hl=adminer()->connectSsl();if(isset($Hl["mode"]))$cd
.=" sslmode=$Hl[mode]";return$this->dsn($cd,$U,$D);}function
select_db($rc){return(adminer()->database()==$rc);}function
query($F,$en=false){$H=(self::$untrusted?$this->readOnlyQuery($F):parent::query($F,$en));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$H;}private
function
readOnlyQuery($F){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$G=$this->pdo->prepare($F);$H=false;if($G&&$G->execute()){$this->store_result($G);$H=$G;}else{list(,$this->errno,$this->error)=($G?$G->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(26);}$this->pdo->query("COMMIT");return$H;}function
warnings(){}function
copyFrom($Q,array$J){$H=$this->pdo->pgsqlCopyFromArray($Q,$J);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$H;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($F){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$F),$A)){$J=explode("\n",$A[2]);$this->multi=false;$this->affected_rows=count($J);return$this->copyFrom($A[1],$J);}return
parent::multi_query($F);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($Yl){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_string($g))return$g;$Kn=get_val("SELECT version()",0,$g);$g->flavor=(preg_match('~CockroachDB~',$Kn)?'cockroach':'');$g->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Kn);if(min_version(9,0,$g))$g->query("SET application_name = 'Adminer'");if($g->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(30)=>array("date"=>10,"time"=>8,"timestamp"=>19,"timestamptz"=>25,"interval"=>0),lang(31)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(32)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(33)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(34)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$g)){$this->types[lang(31)]["json"]=4294967295;$this->types[lang(35)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$g))$this->types[lang(31)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$g)){$this->generated[]="STORED";if(min_version(18,0,$g))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$g->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$n){$gi=$this->userTypes[$n["type"]];return($gi&&!preg_match('~]$~',$n["length"])?type_values($gi):"");}function
setUserTypes(array$dn){$this->userTypes=array_flip($dn);$this->types[lang(0)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$Na=array_filter(fields($Q),function($n){return$n['auto_increment'];});return(count($Na)==1?" RETURNING ".idf_escape(key($Na)):"");}function
insertUpdate($Q,array$J,array$Ij){$e=array_keys(reset($J));$Ob=array();$pn=array();foreach($e
as$x){if(isset($Ij[idf_unescape($x)]))$Ob[]=$x;else$pn[]="$x = EXCLUDED.$x";}if(!$Ob||!min_version(9.5)||count($Ob)!=count($Ij))return
parent::insertUpdate($Q,$J,$Ij);$Dj="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Rl="\nON CONFLICT (".implode(", ",$Ob).")".($pn?" DO UPDATE SET ".implode(", ",$pn):" DO NOTHING");$Y=array();$y=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&strlen($Dj)+$y+strlen($X)+strlen($Rl)>1e6){if(!queries($Dj.implode(",\n",$Y).$Rl))return
false;$Y=array();$y=0;}$Y[]=$X;$y+=strlen($X)+2;}return
queries($Dj.implode(",\n",$Y).$Rl);}function
slowQuery($F,$_m){$this->conn->query("SET statement_timeout = ".(1000*$_m));$this->conn->timeout=1000*$_m;return$F;}function
convertSearch($u,array$W,array$n){$nj=preg_match('(LIKE|^!?~)',$W["op"]);$Fh=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$n["type"])||(!$nj&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$n["type"]));return($Fh&&!preg_match('~\[]$~',$n["full_type"])?$u:"CAST($u AS text)");}function
quoteBinary($Gk){return"'\\x".bin2hex($Gk)."'";}function
md5($d,array$n){if(is_blob($n)||preg_match('~'.text_type().'~',$n["type"]))return"MD5($d)";}function
warnings(){return$this->conn->warnings();}function
tableHelp($B,$Xf=false){$Cg=array("information_schema"=>"infoschema","pg_catalog"=>($Xf?"view":"catalog"),);$_=$Cg[$_GET["ns"]];if($_)return"$_-".str_replace("_","-",$B).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$I=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($I){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $I[partrelid] AND attnum IN (".str_replace(" ",", ",$I["partattrs"]).")");$fb=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$fb[$I["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
routineTriggers($B){return
get_rows("SELECT nspname AS ns, relname AS table, tgname AS trigger
FROM pg_trigger
JOIN pg_class ON tgrelid = pg_class.oid
JOIN pg_namespace ON relnamespace = pg_namespace.oid
WHERE NOT tgisinternal AND tgfoid = substring(".q($B).", '[0-9]+\$')::oid
ORDER BY 1, 2, 3");}function
tableOid($Q){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($Q)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$H=array();$J=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($J
as$I){parse_full_type($I);$I["null"]=!$I["attnotnull"];$H[$I["tab"]][]=$I;}return$H;}function
indexAlgorithms(array$Yl){static$H=array();if(!$H)$H=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$H;}function
indexOpclasses(){static$H=array();if(!$H&&$this->conn->flavor!='cockroach')$H=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$H;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$hb;if($hb===null)$hb=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$hb;}function
hasEstimatedRows(){return
true;}function
isSystem($k,$K=""){return($K!=""?information_schema($k,$K)||preg_match('~^pg_~',$K):in_array($k,array("postgres","template1"))||($this->conn->flavor=='cockroach'&&$k=="system"));}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($he){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($F,$Z,$z,$fi=0,$Xk=" "){return" $F$Z".($z?$Xk."LIMIT $z".($fi?" OFFSET $fi":""):"");}function
limit1($Q,$F,$Z,$Xk="\n"){return(preg_match('~^INTO~',$F)?limit($F,$Z,1,0,$Xk):" $F".(is_view(table_status1($Q))?$Z:$Xk."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$Xk."LIMIT 1)"));}function
db_collation($k,array$Eb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($k));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$F="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$F
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$F
.="
ORDER BY 1";return
get_key_vals($F);}function
count_tables(array$j){$H=array();foreach($j
as$k){if(connection()->select_db($k))$H[$k]=count(tables_list());}return$H;}function
table_status($B="",$Od=false){static$Re;if($Re===null)$Re=get_val("SELECT 'pg_table_size'::regproc");$bl=(!$Od&&min_version(10));$H=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Re?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($bl?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($bl?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($B!=""?"AND d.refobjid = ".driver()->tableOid($B):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($B!=""?"AND relname = ".q($B):"ORDER BY relname"))as$I)$H[$I["Name"]]=$I;return$H;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$I){static$xa=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$I["full_type"],$A);list(,$T,$y,$I["length"],$oa,$Ga)=$A;$I["length"].=$Ga;$sb=$T.$oa;if(isset($xa[$sb])){$I["type"]=$xa[$sb];$I["full_type"]=$I["type"].$y.$Ga;}else{$fn=idf_unescape($T);$I["type"]=(is_user_type($fn)?$fn:$T);$I["full_type"]=$I["type"].$y.$oa.$Ga;}}function
fields($Q){$H=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($Q)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$I){parse_full_type($I);if(in_array($I['attidentity'],array('a','d')))$I['default']='GENERATED '.($I['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$I["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$I["attgenerated"],"");$I["composite"]=($I["typcategory"]=="C");$I["null"]=!$I["attnotnull"];$I["auto_increment"]=$I['attidentity']||preg_match('~^nextval\(~i',$I["default"])||preg_match('~^unique_rowid\(~',$I["default"]);$I["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$I['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$I["default"],$A)&&($A[2]!=""||preg_match("~^('.*'|NULL)\$~s",$A[1])))$I["default"]=($A[1]=="NULL"?null:idf_unescape($A[1]).$A[2]);$H[$I["field"]]=$I;}return$H;}function
indexes($Q,$h=null){$h=connection($h);$H=array();$fm=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $fm AND attnum > 0",$h);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($h->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $fm
ORDER BY indisprimary DESC, indisunique DESC",$h)as$I){$lk=$I["relname"];$H[$lk]["type"]=($I["indisprimary"]?"PRIMARY":($I["indisunique"]?"UNIQUE":"INDEX"));$H[$lk]["columns"]=array();$H[$lk]["descs"]=array();$H[$lk]["algorithm"]=$I["amname"];$H[$lk]["partial"]=$I["partial"];$zf=preg_split('~(?<=\)), (?=\()~',$I["indexpr"]);foreach(explode(" ",$I["indkey"])as$_f)$H[$lk]["columns"][]=($_f?$e[$_f]:array_shift($zf));foreach(explode(" ",$I["indoption"])as$Af)$H[$lk]["descs"][]=(intval($Af)&1?'1':null);$H[$lk]["opclasses"]=($I["opclasses"]!=""?explode(" ",$I["opclasses"]):array());$H[$lk]["lengths"]=array();}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$I){$I['deferrable']=($I['deferrable']?'':'NOT ').'DEFERRABLE'.($I['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$I['definition'],$A)){$I['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$A[2],$Lg)){$I['ns']=idf_unescape($Lg[2]);$I['table']=idf_unescape($Lg[4]);}$I['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[3])));$I['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$A[4],$Lg)?$Lg[1]:'NO ACTION');$I['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$A[4],$Lg)?$Lg[1]:'NO ACTION');$H[$I['conname']]=$I;}}return$H;}function
view($B){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($B).")")));}function
collations(){return
array();}function
information_schema($k,$K=""){$Wl=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$Wl[]="crdb_internal";$Wl[]="pg_extension";}return
in_array($K!=""?$K:get_schema(),$Wl);}function
error(){$H=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$H,$A))$H=$A[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($A[3]).'})(.*)~','\1<b>\2</b>',$A[2]).$A[4];return
nl_br($H);}function
create_database($k,$Db){return
queries("CREATE DATABASE ".idf_escape($k).($Db?" ENCODING ".idf_escape($Db):""));}function
drop_databases(array$j){connection()->close();return
apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');}function
rename_database($B,$Db){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
auto_increment(){return"";}function
alter_table($Q,$B,array$o,array$je,$f,$pd,$Db,$Na,$ej){$b=array();$Uj=array();if($Q!=""&&$Q!=$B)$Uj[]="ALTER TABLE ".table($Q)." RENAME TO ".table($B);$Yk="";$Ii=($Q!=""?fields($Q):array());foreach($o
as$n){$d=idf_escape($n[0]);$W=$n[1];if(!$W)$b[]="DROP $d";else{$Hi=$Ii[$n[0]];$Fi=($Hi?process_field($Hi,$Hi):array());$Fn=$W[5];unset($W[5]);if($n[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$Uj[]="ALTER TABLE ".table($B)." RENAME $d TO $W[0]";if($W[1]!=$Fi[1])$b[]="ALTER $d TYPE$W[1]";if($W[3]!=$Fi[3]||isset($W[6])!=isset($Fi[6])){$Zk=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($Zk).")":"DROP DEFAULT"));if(isset($W[6]))$Yk="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Zk)." OWNED BY ".idf_escape($Q).".$W[0]";}if($W[2]!=$Fi[2])$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($Fn!=$Fi[5])$Uj[]="COMMENT ON COLUMN ".table($B).".$W[0] IS ".($Fn!=""?substr($Fn,9):"''");}}if($Q==""){$b=array_merge($b,$je);$O="";if($ej){$_b=(connection()->flavor=='cockroach');$O=" PARTITION BY $ej[partition_by]($ej[partition])";if($ej["partition_by"]=='HASH'){$fj=+$ej["partitions"];for($s=0;$s<$fj;$s++)$Uj[]="CREATE TABLE ".idf_escape($B."_$s")." PARTITION OF ".idf_escape($B)." FOR VALUES WITH (MODULUS $fj, REMAINDER $s)";}else{$Fj="MINVALUE";foreach($ej["partition_names"]as$s=>$W){$X=$ej["partition_values"][$s];$aj=" VALUES ".($ej["partition_by"]=='LIST'?"IN ($X)":"FROM ($Fj) TO ($X)");if($_b)$O
.=($s?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$aj";else$Uj[]="CREATE TABLE ".idf_escape($B."_$W")." PARTITION OF ".idf_escape($B)." FOR$aj";$Fj=$X;}$O
.=($_b?"\n)":"");}}array_unshift($Uj,"CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$O");}else{if($b)array_unshift($Uj,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($je)$Uj[]="ALTER TABLE ".table($B)."\n".implode(",\n",$je);}if($Yk)array_unshift($Uj,$Yk);if($f!==null)$Uj[]="COMMENT ON TABLE ".table($B)." IS ".q($f);foreach($Uj
as$F){if(!queries($F))return
false;}if($Na!=""){foreach(fields($B)as$Rd=>$n){if($n["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($B)).", ".q($Rd)."), $Na)");}}return
true;}function
alter_indexes($Q,$b){$i=array();$Xc=array();$Uj=array();foreach($b
as$W){if($W[0]!="INDEX")$i[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$Xc[]=idf_escape($W[1]);else$Uj[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($i)array_unshift($Uj,"ALTER TABLE ".table($Q).implode(",",$i));if($Xc)array_unshift($Uj,"DROP INDEX ".implode(", ",$Xc));foreach($Uj
as$F){if(!queries($F))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$H=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$B=>$R)$H[strtoupper($R["Engine"])][]=table($B);return
array_filter($H);}function
drop_views(array$Mn){return
drop_tables($Mn);}function
drop_tables(array$S){$Ll=array();foreach($S
as$Q)$Ll[$Q]=table_status1($Q);foreach(drop_kinds($Ll)as$ig=>$Eh){if(!queries("DROP $ig ".implode(", ",$Eh)))return
false;}return
true;}function
move_tables(array$S,array$Mn,$qm){foreach(array_merge($S,$Mn)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($qm)))return
false;}return
true;}function
trigger($B,$Q){if($B=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($B);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$I)$e[]=$I["event_object_column"];$H=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$I){if($e&&$I["Event"]=="UPDATE")$I["Event"].=" OF";$I["Of"]=implode(", ",$e);if($H)$I["Event"].=" OR $H[Event]";$H=$I;}return$H;}function
triggers($Q){$H=array();$ze=array();foreach(get_rows('SELECT t.tgname, r.routine_schema AS ns, r.specific_name AS function, r.routine_name AS name
FROM pg_catalog.pg_trigger t
JOIN information_schema.routines r ON substring(r.specific_name, \'[0-9]+$\')::oid = t.tgfoid
WHERE NOT t.tgisinternal AND t.tgrelid = (
	SELECT c.oid FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = current_schema() AND c.relname = '.q($Q).'
)')as$I)$ze[array_shift($I)]=$I;foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$I){$Sm=trigger($I["trigger_name"],$Q);$H[$Sm["Trigger"]]=array($Sm["Timing"],$Sm["Event"]);if($ze[$Sm["Trigger"]])$H[$Sm["Trigger"]][]=$ze[$Sm["Trigger"]];}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($B,$T){$yi=routine_options($T);$Uk=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$yi);foreach($Uk
as$x=>$L)$Uk[$x]="$L AS \"$x\"";$J=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($Uk?implode(', ',$Uk).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($B));if(!$J)return
array();$H=$J[0];$H["options"]=array_intersect_key($H,$yi);$H["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$H[type_udt_name]"));$H["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($B)."
ORDER BY ordinal_position");return$H;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$H=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$og)$H[$og]=(preg_match('~sql$~',$og)?"pgsql":"txt");return$H;}function
routine_options($yk){$_b=(connection()->flavor=='cockroach');$Pk=($_b?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($yk=="PROCEDURE")return$Pk;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$Pk+($_b?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($B,array$I){$H=array();foreach($I["fields"]as$n){$y=$n["length"];$H[]=$n["type"].($y?"($y)":"");}return
idf_escape($B)."(".implode(", ",$H).")";}function
last_id($G){$I=(is_object($G)?$G->fetch_row():array());return($I?$I[0]:0);}function
explain(Db$g,$F){return$g->query("EXPLAIN $F");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$kk))return$kk[1];}function
types($Kd=false){$_b=connection()->flavor=='cockroach';$jg=($_b?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($jg)".($_b?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($Kd?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($t){$td=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");return($td?"'".implode("', '",array_map('addslashes',$td))."'":"");}function
collation_name($gi){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $gi AND collname != 'default')":"NULL");}function
type_definition($t){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $t"));$H=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$H;switch($H["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");$H["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$I)$e[]=idf_escape($I["attname"])." $I[full_type]".($I["collation"]?" COLLATE ".idf_escape($I["collation"]):"");$H["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$Uc=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $t"));$H["definition"]="AS $Uc[base]".($Uc["collation"]?" COLLATE ".idf_escape($Uc["collation"]):"").($Uc["typdefault"]!=""?" DEFAULT $Uc[typdefault]":"").($Uc["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $t AND contype != 'n' ORDER BY conname")as$I)$H["definition"].=" CONSTRAINT ".idf_escape($I["conname"])." $I[definition]";break;case'r':$Zj=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $t"));$yi=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$x=>$xd){if($Zj[$x]!="")$yi[]=strtoupper($x)." = ".($xd?idf_escape($Zj[$x]):$Zj[$x]);}$H["definition"]="AS RANGE (".implode(", ",$yi).")";}return$H;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($K,$h=null){$_GET["ns"]=$K;$H=get_val("SELECT set_config('search_path', ".q(idf_escape($K)).", false) FROM pg_namespace WHERE nspname = ".q($K),0,$h);driver()->setUserTypes(types(true));return!!$H;}function
schema_comment($K){return(string)get_val("SELECT obj_description(oid, 'pg_namespace') FROM pg_namespace WHERE nspname = ".q($K));}function
set_schema_comment($K,$f){return!!queries("COMMENT ON SCHEMA ".idf_escape($K)." IS ".q($f));}function
drop_sql(array$S){$H="";foreach(drop_kinds($S)as$ig=>$Eh)$H
.="DROP $ig IF EXISTS ".implode(", ",$Eh).";\n";return($H?"$H\n":"");}function
foreign_keys_sql($Q){$H="";$ee=foreign_keys($Q);ksort($ee);foreach($ee
as$de=>$ce){$H
.="ALTER TABLE ONLY ".table($Q)." ADD CONSTRAINT ".idf_escape($de)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$A){return$A[1].table(idf_unescape($A[2]))."(";},$ce["definition"]).";\n";}return($H?"$H\n":$H);}function
indexes_sql($Q,$Ij=""){$H="";$F="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($Q).($Ij!=""?" AND indexname != ".q($Ij):"");foreach(get_rows($F,null,"-- ")as$I)$H
.="\n\n".str_replace(array(" $I[db].$I[qualified] USING "," $I[qualified] USING ")," ".table($Q)." USING ",$I["indexdef"]).";";return$H;}function
create_sql($Q,$Na,$Pl){$tk=array();$bl=array();$cl=array();$al=array();$O=table_status1($Q);if(is_view($O)){$Ln=view($Q);$i="CREATE ".strtoupper($O["Engine"])." ".table($Q)." AS ".rtrim($Ln["select"],";").";";return
rtrim($i.indexes_sql($Q),';');}$o=fields($Q);if(count($O)<2||empty($o))return"";$H="CREATE TABLE ".table($O['Name'])." (\n    ";$dm=q(table($O['Name']));foreach($o
as$n){$dl="";if($n['default']=="nextval('$O[Name]_$n[field]_seq')"){$dl=table("$O[Name]_$n[field]_seq");$n['default']=null;$n['full_type']=preg_replace('~int(eger)?~','serial',$n['full_type']);}$Yi=idf_escape($n['field']).' '.full_type_sql($n).preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$A){return$A[1].str_replace("'","''",table(idf_unescape($A[2])))."'";},default_value($n)).($n['null']?"":" NOT NULL");$tk[]=$Yi;if(preg_match('~nextval\(\'([^\']+)\'\)~',$n['default'],$Mg)){$Zk=$Mg[1];$Al=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Zk)):"SELECT * FROM $Zk"),null,"-- "));$Yk=table(idf_unescape($Zk));$bl[]=($Pl=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $Yk;\n":"")."CREATE SEQUENCE $Yk INCREMENT $Al[increment_by] MINVALUE $Al[min_value] MAXVALUE $Al[max_value]"." CACHE $Al[cache_value];";if(get_val("SELECT pg_get_serial_sequence($dm, ".q($n['field']).")"))$cl[]="\n\nALTER SEQUENCE $Yk OWNED BY ".table($O['Name']).".".idf_escape($n['field']).";";if($Na)$al[]=$Yk;}elseif($Na&&$n['auto_increment']){$Yk=($dl?"":get_val("SELECT pg_get_serial_sequence($dm, ".q($n['field']).")::regclass"));$al[]=($Yk?table(idf_unescape($Yk)):$dl);}}if(!empty($bl))$H=implode("\n\n",$bl)."\n\n$H";$Ij="";foreach(indexes($Q)as$xf=>$v){if($v['type']=='PRIMARY'){$Ij=$xf;$tk[]="CONSTRAINT ".idf_escape($xf)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$v['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$Qb=>$Sb)$tk[]="CONSTRAINT ".idf_escape($Qb)." CHECK ($Sb)";$H
.=implode(",\n    ",$tk)."\n)";$aj=driver()->partitionsInfo($O['Name']);if($aj)$H
.="\nPARTITION BY $aj[partition_by]($aj[partition])";$H
.=(min_version(12)?"":"\nWITH (oids = ".($O['Oid']?'true':'false').")").";";$H
.=implode($cl);if($O['Comment'])$H
.="\n\nCOMMENT ON TABLE ".table($O['Name'])." IS ".q($O['Comment']).";";foreach($o
as$Rd=>$n){if($n['comment'])$H
.="\n\nCOMMENT ON COLUMN ".table($O['Name']).".".idf_escape($Rd)." IS ".q($n['comment']).";";}$H
.=indexes_sql($Q,$Ij);foreach(array_filter($al)as$Yk){$Al=first(get_rows("SELECT last_value, is_called::int FROM $Yk",null,"-- "));if($Al['is_called'])$H
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($Yk).", $Al[last_value]); END \$\$;";}return
rtrim($H,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$H="";foreach(triggers($Q)as$Rm=>$Qm){$Sm=trigger($Rm,$O['Name']);$H
.="\nCREATE TRIGGER ".idf_escape($Sm['Trigger'])." $Sm[Timing] $Sm[Event] ON ".table($O['Name'])." $Sm[Type] $Sm[Statement];;\n";}return$H;}function
use_sql($rc,$Pl=""){$B=idf_escape($rc);$H="";if(preg_match('~CREATE~',$Pl)){if($Pl=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $B;\n";$H
.="CREATE DATABASE $B;\n";}return"$H\\connect $B";}function
use_schema_sql($K,$Pl){$B=idf_escape($K);$H="";if(preg_match('~CREATE~',$Pl)){if($Pl=="DROP+CREATE")$H="DROP SCHEMA IF EXISTS $B CASCADE;\n";$H
.="CREATE SCHEMA IF NOT EXISTS $B;\n";$f=schema_comment($K);if($f!="")$H
.="COMMENT ON SCHEMA $B IS ".q($f).";\n";}return$H."SET search_path TO $B";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$n){if(preg_match('~^(geometry|geography)$~',$n["type"])&&strpos($n["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($n["field"]).")";}function
unconvert_field(array$n,$H){return($n["composite"]?"$H::".full_type_sql($n):$H);}function
support($Pd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|transaction_ddl').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$Pd);}function
kill_process($t){return
queries("SELECT pg_terminate_backend(".number($t).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$M,$U,$D){$this->link=new
\SQLite3($M["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer(array($this,'authorize'));$Kn=\SQLite3::version();$this->server_info=$Kn["versionString"];return'';}function
query($F,$en=false){$G=@$this->link->query($F);$this->error="";if(!$G){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($G->numColumns())return
new
Result($G);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$dn=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$dn[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$M,$U,$D){$H=$this->dsn(DRIVER.":".$M["path"],"","",array(),(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$H&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer(array($this,'authorize'));return$H;}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;function
attach(array$M,$U,$D){parent::attach($M,$U,$D);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($p){$F="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$p)?$p:dirname($_SERVER["SCRIPT_FILENAME"])."/$p")." AS a";$this->attaching=true;$Ka=is_readable($p)&&$this->query($F);$this->attaching=false;if($Ka)return!self::attach(server_parts(array("path"=>$p)),'','');return
false;}function
authorize($ka,$Ea){return($ka!=24||$Ea===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($Yl){$H=array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($Yl,"Engine")))$H[]="MATCH";$H[]="SQL";return$H;}static
function
connect($M,$U,$D){return
parent::connect(":memory:","","");}function
__construct(Db$g){parent::__construct($g);if(min_version(3.31,0,$g))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$g))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Gk){return"x".q(bin2hex($Gk));}function
typeName(\stdClass$n){$H=strtolower(idx((array)$n,'sqlite:decl_type',parent::typeName($n)));return
idx(array("string"=>"text","double"=>"real"),$H,$H);}function
engines(){$H=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$H[]="STRICT";$H[]="STRICT, WITHOUT ROWID";}$H[]="WITHOUT ROWID";}return$H;}private
function
isVirtual(array$R){$pd=$R["Engine"];return$pd!=""&&!in_array($pd,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$Yl){return!$this->isVirtual($Yl);}function
shadowTables($Q){$H=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$B){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$B))$H[]=array("table"=>$B,"ns"=>"");}}return$H;}function
fulltextSql($B,array$v,$F,$cb){return
idf_escape($B)." MATCH ".q($F);}function
insertUpdate($Q,array$J,array$Ij){$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($J))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($B,$Xf=false){if(preg_match('~^sqlite_(seq|stat.)~',$B,$A))return"fileformat2.html#$A[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$B))return"schematab.html";}function
checkConstraints($Q){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn),$Mg);return
array_combine($Mg[2],$Mg[2]);}function
allFields(){$H=array();if(min_version(3.16)){$J=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($J
as$I){$I["type"]=type_affinity($I["type"]);$I["null"]=!$I["notnull"];$H[$I["tab"]][]=$I;}}else$H=parent::allFields();return$H;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($he){return
array();}function
limit($F,$Z,$z,$fi=0,$Xk=" "){return" $F$Z".($z?$Xk."LIMIT $z".($fi?" OFFSET $fi":""):"");}function
limit1($Q,$F,$Z,$Xk="\n"){return(preg_match('~^INTO~',$F)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($F,$Z,1,0,$Xk):" $F WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$Xk."LIMIT 1)");}function
db_collation($k,array$Eb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Bl){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Bl,$A)?$A[1]:"");}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$j){return
array();}function
db_status(){$Si=get_val("PRAGMA page_size");$re=get_val("PRAGMA freelist_count")*$Si;return
array("Data_length"=>get_val("PRAGMA page_count")*$Si-$re,"Index_length"=>0,"Data_free"=>$re,);}function
table_status($B="",$Od=false){$H=array();$J=array();if(!$Od&&$B==""){connection()->query("PRAGMA optimize = 0x10002");$J=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($B!=""?"AND name = ".q($B):"ORDER BY (name LIKE 'sqlite_%'), name"))as$I){if($I["Engine"]=="table"){$Bl=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$I["sql"]);$Rl=preg_replace('~.*\)~s','',$Bl);$I["Engine"]=virtual_module($I["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$Rl)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$Rl)?"WITHOUT ROWID":0),)))?:"table");}unset($I["sql"]);$I["Rows"]=idx($J,$I["Name"],0);$H[$I["Name"]]=$I;}if(!$Od){foreach(get_rows("SELECT * FROM sqlite_sequence".($B!=""?" WHERE name = ".q($B):""),null,"")as$I)$H[$I["name"]]["Auto_increment"]=$I["seq"];}return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$H=array();$Bl=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$Nj=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$Nj+=array("insert"=>1,"update"=>1);$te=preg_match('~^fts\d+$~i',virtual_module($Bl));foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$I){if($I["hidden"]==1)continue;$B=$I["name"];$T=strtolower($I["type"]);$l=$I["dflt_value"];$H[$B]=array("field"=>$B,"type"=>($te?"text":type_affinity($T)),"full_type"=>$T,"default"=>(preg_match("~^'(.*)'$~",$l,$A)?str_replace("''","'",$A[1]):($l=="NULL"?null:$l)),"null"=>!$I["notnull"],"privileges"=>$Nj,"primary"=>$I["pk"],);if($I["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Bl))$H[$B]["auto_increment"]=true;}$u='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$pk='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$u.'\s+text\b'.$pk.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Bl,$Mg,PREG_SET_ORDER);foreach($Mg
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($H[$B])$H[$B]["collation"]=trim($A[3],"'");}preg_match_all('~'.$u.'\s'.$pk.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Bl,$Mg,PREG_SET_ORDER);foreach($Mg
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($H[$B]){$H[$B]["default"]=$A[3];$H[$B]["generated"]=strtoupper($A[4]);}}return$H;}function
indexes($Q,$h=null){$h=connection($h);$H=array();$Bl=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$h);if(preg_match('~^fts\d+$~i',virtual_module($Bl)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Bl,$A)){$H[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$A[1],$Mg,PREG_SET_ORDER);foreach($Mg
as$A){$H[""]["columns"][]=idf_unescape($A[2]).$A[4];$H[""]["descs"][]=(preg_match('~DESC~i',$A[5])?'1':null);}}if(!$H){foreach(fields($Q)as$B=>$n){if($n["primary"])$H[""]=array("type"=>"PRIMARY","columns"=>array($B),"lengths"=>array(),"descs"=>array(null));}}$Gl=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$h);foreach(get_rows("PRAGMA index_list(".table($Q).")",$h)as$I){$B=$I["name"];$v=array("type"=>($I["unique"]?"UNIQUE":"INDEX"));$v["lengths"]=array();$v["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($B).")",$h)as$Ek){$v["columns"][]=$Ek["name"];$v["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($B).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$Gl[$B],$kk)){preg_match_all('/("[^"]*+")+( DESC)?/',$kk[2],$Mg);foreach($Mg[2]as$x=>$W){if($W)$v["descs"][$x]='1';}}if(!$H[""]||$v["type"]!="UNIQUE"||$v["columns"]!=$H[""]["columns"]||$v["descs"]!=$H[""]["descs"]||!preg_match("~^sqlite_~",$B))$H[$B]=$v;}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$I){$q=&$H[$I["id"]];if(!$q)$q=$I;$q["source"][]=$I["from"];$q["target"][]=$I["to"];}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($B))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($k,$K=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($B){$Kd="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Kd)\$~",$B)){connection()->error=lang(36,str_replace("|",", ",$Kd));return
false;}return
true;}function
create_database($k,$Db){if(file_exists($k)){connection()->error=lang(37);return
false;}if(!check_sqlite_name($k))return
false;try{$_=new
Db();$_->attach(server_parts(array("path"=>$k)),'','');}catch(\Exception$Ad){connection()->error=$Ad->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases(array$j){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($j
as$k){if(!check_sqlite_name($k))return
false;if(!@unlink($k)){connection()->error=lang(37);return
false;}}return
true;}function
rename_database($B,$Db){if(!check_sqlite_name($B))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(37);return@rename(DB,$B);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$B,array$o,array$je,$f,$pd,$Db,$Na,$ej){$wn=($Q==""||$je||$pd);foreach($o
as$n){if($n[0]!=""||!$n[1]||$n[2]){$wn=true;break;}}$b=array();$Mi=array();foreach($o
as$n){if($n[1]){$b[]=($wn?$n[1]:"ADD ".implode($n[1]));if($n[0]!="")$Mi[$n[0]]=$n[1][0];}}if(!$wn){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$B&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($B)))return
false;}elseif(!recreate_table($Q,$B,$b,$Mi,$je,$Na,array(),"","",$pd))return
false;if($Na){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Na WHERE name = ".q($B));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($B).", $Na)");queries("COMMIT");}return
true;}function
recreate_table($Q,$B,array$o,array$Mi,array$je,$Na="",$w=array(),$Yc="",$na="",$pd=""){if($Q!=""){if(!$o){foreach(fields($Q)as$x=>$n){if($w)$n["auto_increment"]=0;$o[]=process_field($n,$n);$Mi[$x]=idf_escape($x);}}$Jj=false;foreach($o
as$n){if($n[6])$Jj=true;}$ad=array();foreach($w
as$x=>$W){if($W[2]=="DROP"){$ad[$W[1]]=true;unset($w[$x]);}}foreach(indexes($Q)as$eg=>$v){$e=array();foreach($v["columns"]as$x=>$d){if(!$Mi[$d])continue
2;$e[]=$Mi[$d].($v["descs"][$x]?" DESC":"");}if(!$ad[$eg]){if($v["type"]!="PRIMARY"||!$Jj)$w[]=array($v["type"],$eg,$e);}}foreach($w
as$x=>$W){if($W[0]=="PRIMARY"){unset($w[$x]);$je[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$eg=>$q){foreach($q["source"]as$x=>$d){if(!$Mi[$d])continue
2;$q["source"][$x]=idf_unescape($Mi[$d]);}if(!isset($je[" $eg"]))$je[]=" ".format_foreign_key($q);}queries("BEGIN");}$mb=array();foreach($o
as$n){if(preg_match('~GENERATED~',$n[3]))unset($Mi[array_search($n[0],$Mi)]);$mb[]="  ".implode($n);}$mb=array_merge($mb,array_filter($je));foreach(driver()->checkConstraints($Q)as$qb){if($qb!=$Yc)$mb[]="  CHECK ($qb)";}if($na)$mb[]="  CHECK ($na)";$um=($Q!=""&&$Q==$B?"adminer_$B":$B);if(!$pd&&$Q!="")$pd=idx(table_status1($Q),"Engine");if(!queries("CREATE TABLE ".table($um)." (\n".implode(",\n",$mb)."\n)".($pd!="table"&&in_array($pd,driver()->engines())?" $pd":"")))return
false;if($Q!=""){if($Mi&&!queries("INSERT INTO ".table($um)." (".implode(", ",$Mi).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Mi)))." FROM ".table($Q)))return
false;$Wm=array();foreach(triggers($Q)as$Um=>$Am){$Sm=trigger($Um,$Q);$Wm[]="CREATE TRIGGER ".idf_escape($Um)." ".implode(" ",$Am)." ON ".table($B)."\n$Sm[Statement]";}$Na=$Na?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$B&&!queries("ALTER TABLE ".table($um)." RENAME TO ".table($B)))||!alter_indexes($B,$w))return
false;if($Na)queries("UPDATE sqlite_sequence SET seq = $Na WHERE name = ".q($B));foreach($Wm
as$Sm){if(!queries($Sm))return
false;}queries("COMMIT");}return
true;}function
index_sql($Q,$T,$B,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($B!=""?$B:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$Ij){if($Ij[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$Mn){return
apply_queries("DROP VIEW",$Mn);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$Mn,$qm){return
false;}function
trigger($B,$Q){if($B=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$u='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Vm=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(".implode("|",$Vm["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($B)),$A);if(!$A)return
array();$ai=$A[3];return
array("Timing"=>strtoupper($A[1]),"Event"=>strtoupper($A[2]).($ai?" OF":""),"Of"=>idf_unescape($ai),"Trigger"=>$B,"Statement"=>$A[4],);}function
triggers($Q){$H=array();$Vm=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$I){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Vm["Timing"]).')\s*(.*?)\s+ON\b~i',$I["sql"],$A);$H[$I["name"]]=array($A[1],$A[2]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$g,$F){return$g->query("EXPLAIN QUERY PLAN $F");}function
found_rows(array$R,array$Z){}function
types($Kd=false){return
array();}function
create_sql($Q,$Na,$Pl){$H=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$B=>$v){if($B==''||$v['type']=='FULLTEXT')continue;$H
.=";\n\n".index_sql($Q,$v['type'],$B,"(".implode(", ",array_map('Adminer\idf_escape',$v['columns'])).")");}return$H;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($rc,$Pl=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$H=array();foreach(get_rows("PRAGMA pragma_list")as$I){$B=$I["name"];if($B!="pragma_list"&&$B!="compile_options"){$H[$B]=array($B,'');foreach(get_rows("PRAGMA $B")as$I)$H[$B][1].=implode(", ",$I)."\n";}}return$H;}function
show_status(){$H=array();foreach(get_vals("PRAGMA compile_options")as$xi)$H[]=explode("=",$xi,2)+array('','');return$H;}function
convert_field(array$n){}function
unconvert_field(array$n,$H){return$H;}function
support($Pd){return
preg_match('~^(check|columns|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$Pd);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result,$warnings,$transaction=false;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$m){$this->errno=$m["code"];$this->error
.="$m[message]\n";}$this->error=rtrim($this->error);}function
attach(array$M,$U,$D){sqlsrv_configure("WarningsReturnAsErrors",0);$Rb=array("UID"=>$U,"PWD"=>$D,"CharacterSet"=>"UTF-8","ReturnDatesAsStrings"=>true);if(isset($_GET["sql"])&&!self::$instance)$Rb["MultipleActiveResultSets"]=false;$Hl=adminer()->connectSsl();if(isset($Hl["Encrypt"]))$Rb["Encrypt"]=$Hl["Encrypt"];if(isset($Hl["TrustServerCertificate"]))$Rb["TrustServerCertificate"]=$Hl["TrustServerCertificate"];$k=adminer()->database();if($k!="")$Rb["Database"]=$k;$wj=$M["port"];$this->link=@sqlsrv_connect($M["host"].($wj?",$wj":""),$Rb);if($this->link){$Bf=sqlsrv_server_info($this->link);$this->server_info=$Bf['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){return
unicode_prefix($P)."'".str_replace("'","''",$P)."'";}function
select_db($rc){return$this->query(use_sql($rc));}function
query($F,$en=false){$G=sqlsrv_query($this->link,$F);$this->error="";if(!$G){$this->get_error();return
false;}return$this->store_result($G);}function
multi_query($F){$this->result=sqlsrv_query($this->link,$F);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($G=null){if(!$G)$G=$this->result;if(!$G)return
false;$this->warnings=sqlsrv_errors(SQLSRV_ERR_WARNINGS);if(sqlsrv_field_metadata($G))return
new
Result($G);$this->affected_rows=sqlsrv_rows_affected($G);return
true;}function
next_result(){if(!$this->result)return
false;$H=sqlsrv_next_result($this->result);if($H===false){$this->get_error();$this->result=null;return
true;}return!!$H;}function
warnings(){$H=array();foreach((array)$this->warnings
as$Pn)$H[]=$Pn["message"];return$H;}function
inTransaction(){return$this->transaction||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
begin(){$this->transaction=sqlsrv_begin_transaction($this->link);if(!$this->transaction)$this->get_error();return$this->transaction;}function
commit(){if($this->transaction&&!sqlsrv_commit($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}function
rollback(){if(!$this->transaction&&isset($_GET["sql"]))return!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK");if($this->transaction&&!sqlsrv_rollback($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC);}function
fetch_row(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC);}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$dn=array(-155=>"datetimeoffset","time",-152=>"xml","varbinary","sql_variant",-11=>"uniqueidentifier","ntext","nvarchar","nchar","bit","tinyint","bigint","image","varbinary","binary","text",1=>"char","numeric","decimal","int","smallint","float","real","float",12=>"varchar",91=>"date","time","datetime",);$n=$this->fields[$this->offset++];$H=new
\stdClass;$H->name=$n["Name"];$H->native_type=idx($dn,$n["Type"],"");return$H;}function
seek($fi){for($s=0;$s<$fi;$s++)sqlsrv_fetch($this->result);}}function
last_id($G){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$g,$F){$g->query("SET SHOWPLAN_ALL ON");$H=$g->query($F);$g->query("SET SHOWPLAN_ALL OFF");return$H;}}else{abstract
class
MssqlDb
extends
PdoDb{function
quote($P){return
unicode_prefix($P).parent::quote($P);}function
select_db($rc){return$this->query(use_sql($rc));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
inTransaction(){return
parent::inTransaction()||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT"));}function
rollback(){return(parent::inTransaction()||!isset($_GET["sql"])?parent::rollback():!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK"));}function
warnings(){$G=$this->multi;if(!is_object($G))return
array();$m=$G->errorInfo();return
array((string)$m[2]);}}function
last_id($G){return
connection()->lastInsertId();}function
explain(Db$g,$F){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach(array$M,$U,$D){$wj=$M["port"];$cd="sqlsrv:Server=$M[host]".($wj?",$wj":"").(isset($_GET["sql"])&&!self::$instance?";MultipleActiveResultSets=0":"");$Hl=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$x){if(isset($Hl[$x]))$cd
.=";$x=".($Hl[$x]?1:0);}return$this->dsn($cd,$U,$D,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$M,$U,$D){$wj=$M["port"];$ul=$M["socket"];$yi=array(1002=>true);$H=$this->dsn("dblib:charset=utf8;host=$M[host]".($wj!=""?";port=$wj":($ul!=""?";unix_socket=$ul":"")),$U,$D,$yi);if(!$H){$this->query("SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");$this->server_info=get_val("SELECT CAST(SERVERPROPERTY('ProductVersion') AS varchar(20))",0,$this);}return$H;}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="|OUTPUT";private$unknownTypes=array();function
operators($Yl){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($M,$U,$D){if($M=="")$M="localhost:1433";return
parent::connect($M,$U,$D);}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),lang(30)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>23,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),lang(31)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),lang(32)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),lang(34)=>array("geometry"=>0,"geography"=>0),);$dn=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($dn){foreach($this->types
as$r=>$Ge){foreach($Ge
as$T=>$y){if(isset($dn[$T]))unset($dn[$T]);else
unset($this->types[$r][$T]);}if(!$this->types[$r])unset($this->types[$r]);}$this->unknownTypes=array_keys($dn);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
typeName(\stdClass$n){return
idx((array)$n,'sqlsrv:decl_type',parent::typeName($n));}function
insertUpdate($Q,array$J,array$Ij){$o=fields($Q);$pn=array();$Z=array();$N=reset($J);$e="c".implode(", c",range(1,count($N)));$gb=0;$Hf=array();foreach($N
as$x=>$W){$gb++;$B=idf_unescape($x);if(!$o[$B]["auto_increment"])$Hf[$x]="c$gb";if(isset($Ij[$B]))$Z[]="$x = c$gb";else$pn[]="$x = c$gb";}$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";if($Z){$kf=queries("SET IDENTITY_INSERT ".table($Q)." ON");$H=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($pn?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$pn):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($kf?$N:$Hf)).") VALUES (".($kf?$e:implode(", ",$Hf)).");");if($kf)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$H=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($N)).") VALUES\n".implode(",\n",$Y));return$H;}function
begin(){remember_query("BEGIN TRANSACTION");return$this->conn->begin();}function
convertSearch($u,array$W,array$n){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$n["type"])?"CAST($u AS nvarchar(max))":$u);}function
quoteBinary($Gk){return"0x".bin2hex($Gk);}function
warnings(){$H=array();foreach($this->conn->warnings()as$eh){$eh=trim(preg_replace('~^(\[[^]]+])+~','',$eh));if($eh!="")$H[]=$eh;}return
nl_br(h(implode("\n",$H)));}function
tableHelp($B,$Xf=false){$Cg=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$_=$Cg[get_schema()];if($_)return"relational-databases/system-$_".preg_replace('~_~','-',strtolower($B))."-transact-sql";}function
isSystem($k,$K=""){return($K!=""?information_schema($k,$K)||preg_match('~^(guest|db_(owner|accessadmin|securityadmin|ddladmin|backupoperator|(deny)?data(reader|writer)))$~',$K):in_array($k,array("master","tempdb","model","msdb")));}}function
unicode_prefix($P){return(strlen($P)!=utf8_length($P)?"N":"");}function
idf_escape($u){return"[".str_replace("]","]]",$u)."]";}function
table($u){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($he){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($F,$Z,$z,$fi=0,$Xk=" "){return($z?" TOP (".($z+$fi).")":"")." $F$Z";}function
limit1($Q,$F,$Z,$Xk="\n"){return
limit($F,$Z,1,0,$Xk);}function
db_collation($k,array$Eb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($k));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$j){$H=array();foreach($j
as$k){connection()->select_db($k);$H[$k]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$H;}function
table_status($B="",$Od=false){$H=array();$sl=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$I){$Zh=$I["object_id"];unset($I["object_id"]);$sl[$Zh]=$I;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$I){$Zh=$I["object_id"];unset($I["object_id"]);$H[$I["Name"]]=$I+idx($sl,$Zh,array());}return$H;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
type_length($T,array$I){return(preg_match("~char|binary~",$T)?($I["max_length"]==-1?"max":intval($I["max_length"])/($T[0]=='n'?2:1)):($T=="decimal"?"$I[precision],$I[scale]":(preg_match('~^(datetime2|datetimeoffset|time)$~',$T)?$I["scale"]:($T=="vector"?(intval($I["max_length"])-8)/4:""))));}function
fields($Q){$Lb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$H=array();$Zl=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Zl))as$I){$T=$I["type"];$y=type_length($T,$I);$H[$I["name"]]=array("field"=>$I["name"],"full_type"=>$T.($y!=""?"($y)":""),"type"=>$T,"length"=>$y,"default"=>(preg_match("~^\(N?'(.*)'\)$~s",$I["default"],$A)?str_replace("''","'",$A[1]):$I["default"]),"default_constraint"=>$I["default_constraint"],"null"=>$I["is_nullable"],"auto_increment"=>$I["is_identity"],"collation"=>$I["collation_name"],"privileges"=>($T=="timestamp"?array("select"=>1,"where"=>1,"order"=>1):array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1)),"primary"=>$I["is_primary_key"],"comment"=>$Lb[$I["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Zl))as$I){$H[$I["name"]]["generated"]=($I["is_persisted"]?"PERSISTED":"VIRTUAL");$H[$I["name"]]["default"]=$I["definition"];}return$H;}function
indexes($Q,$h=null){$H=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$h)as$I){$B=$I["name"];$H[$B]["type"]=($I["is_primary_key"]?"PRIMARY":($I["is_unique"]?"UNIQUE":"INDEX"));$H[$B]["lengths"]=array();$H[$B]["columns"][$I["key_ordinal"]]=$I["column_name"];$H[$B]["descs"][$I["key_ordinal"]]=($I["is_descending_key"]?'1':null);}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($B))));}function
collations(){$H=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$Db)$H[preg_replace('~_.*~','',$Db)][]=$Db;return$H;}function
information_schema($k,$K=""){return
in_array($K!=""?$K:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($k,$Db){return
queries("CREATE DATABASE ".idf_escape($k).(preg_match('~^[a-z0-9_]+$~i',$Db)?" COLLATE $Db":""));}function
drop_databases(array$j){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$j)));}function
rename_database($B,$Db){if(preg_match('~^[a-z0-9_]+$~i',$Db))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $Db");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($B));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$B,array$o,array$je,$f,$pd,$Db,$Na,$ej){$b=array();$Lb=array();$Ii=fields($Q);foreach($o
as$n){$d=idf_escape($n[0]);$W=$n[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$Gi=idx($Ii[$n[0]],"comment");if($W[5]!=($Gi!=""?" COMMENT ".q($Gi):""))$Lb[idf_unescape($W[0])]=array($Gi!="",substr($W[5],9));unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($n[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($je[$W[0]],16+strlen($W[0])):"");else{$l=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$Hi=$Ii[$n[0]];if(default_value($Hi)!=$l){if($Hi["default"]!==null)$b["DROP"][]=" ".idf_escape($Hi["default_constraint"]);if($l)$b["ADD"][]="\n $l FOR $d";}}}}if($Q==""){$ma=(array)$b["ADD"];foreach($je
as$x=>$W){if(!is_string($x))$ma[]="\n$W";}if(!queries("CREATE TABLE ".table($B)." (".implode(",",$ma)."\n)"))return
false;}else{if($Q!=$B)queries("EXEC sp_rename ".q(table($Q)).", ".q($B));if($je)$b[""]=$je;foreach($b
as$x=>$W){if(!queries("ALTER TABLE ".table($B)." $x".implode(",",$W)))return
false;}}$Rj="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($B);foreach($Lb
as$x=>$W){list($Xc,$Jb)=$W;$Sj="$Rj, @level2type = N'Column', @level2name = ".q($x);if($Xc)queries("EXEC sp_dropextendedproperty $Sj");if($Jb!="")queries("EXEC sp_addextendedproperty $Sj, @value = $Jb");}if($f!==null){if(idx(table_status1($B),"Comment")!="")queries("EXEC sp_dropextendedproperty $Rj");if($f!="")queries("EXEC sp_addextendedproperty $Rj, @value = ".q($f));}return
true;}function
alter_indexes($Q,$b){$v=array();$Xc=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$Xc[]=idf_escape($W[1]);else$v[]=idf_escape($W[1])." ON ".table($Q);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$v||queries("DROP INDEX ".implode(", ",$v)))&&(!$Xc||queries("ALTER TABLE ".table($Q)." DROP ".implode(", ",$Xc)));}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$H=array();$qi=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$K=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($K))as$I){$q=&$H[$I["FK_NAME"]];$q["db"]=($I["PKTABLE_QUALIFIER"]==DB?"":$I["PKTABLE_QUALIFIER"]);$q["ns"]=($I["PKTABLE_OWNER"]==$K?"":$I["PKTABLE_OWNER"]);$q["table"]=$I["PKTABLE_NAME"];$q["on_update"]=$qi[$I["UPDATE_RULE"]];$q["on_delete"]=$qi[$I["DELETE_RULE"]];$q["source"][]=$I["FKCOLUMN_NAME"];$q["target"][]=$I["PKCOLUMN_NAME"];}return$H;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mn){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Mn)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Mn,$qm){return
apply_queries("ALTER SCHEMA ".idf_escape($qm)." TRANSFER",array_merge($S,$Mn));}function
trigger($B,$Q){if($B=="")return
array();$J=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($B));$H=reset($J);if($H)$H["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$H["text"]);return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($Q))as$I)$H[$I["name"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
routine($B,$T){$Ac=get_val("SELECT m.definition
FROM sys.objects o
JOIN sys.sql_modules m ON m.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($B)." AND o.type = ".q($T=="PROCEDURE"?"P":"FN"));if(!$Ac)return
array();$H=array("definition"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',$Ac),"fields"=>array());foreach(get_rows("SELECT p.name, TYPE_NAME(p.user_type_id) [type], p.max_length, p.precision, p.scale, p.is_output
FROM sys.parameters p
JOIN sys.objects o ON p.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($B)."
ORDER BY p.parameter_id")as$I){$Td=$I["type"];$y=type_length($Td,$I);$n=array("field"=>preg_replace('~^@~','',$I["name"]),"type"=>$Td,"length"=>$y,"full_type"=>$Td.($y!=""?"($y)":""),"null"=>true,"inout"=>($I["is_output"]?"OUTPUT":""),);if($n["field"]=="")$H["returns"]=$n;else$H["fields"][]=$n;}return$H;}function
routines(){return
get_rows("SELECT o.name SPECIFIC_NAME, o.name ROUTINE_NAME,
	CASE o.type WHEN 'P' THEN 'PROCEDURE' ELSE 'FUNCTION' END ROUTINE_TYPE, TYPE_NAME(p.user_type_id) DTD_IDENTIFIER
FROM sys.objects o
LEFT JOIN sys.parameters p ON o.object_id = p.object_id AND p.parameter_id = 0
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('P', 'FN')
ORDER BY o.name");}function
routine_languages(){return
array();}function
routine_options($yk){return
array();}function
routine_id($B,array$I){return
table($B);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($K,$h=null){$_GET["ns"]=$K;return!!get_val("SELECT 1 FROM sys.schemas WHERE name = ".q($K),0,$h);}function
schema_comment($K){return(string)get_val("SELECT CAST(value AS nvarchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', ".q($K).", NULL, NULL, NULL, NULL)");}function
set_schema_comment($K,$f){$Rj="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($K);return(schema_comment($K)==""||queries("EXEC sp_dropextendedproperty $Rj"))&&($f==""||queries("EXEC sp_addextendedproperty $Rj, @value = ".q($f)));}function
create_sql($Q,$Na,$Pl){$O=table_status1($Q);if(is_view($O)){$Ln=view($Q);return"CREATE VIEW ".table($Q)." AS $Ln[select]";}$Rj="EXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($Q);$Lb=($O["Comment"]!=""?";\n\n$Rj, @value = ".q($O["Comment"]):"");$o=array();$Ij=false;foreach(fields($Q)as$B=>$n){$W=process_field($n,$n);if($W[6])$Ij=true;unset($W[5]);$o[]=implode("",$W);if($n["comment"]!="")$Lb
.=";\n\n$Rj, @level2type = N'Column', @level2name = ".q($B).", @value = ".q($n["comment"]);}foreach(indexes($Q)as$B=>$v){if(!$Ij||$v["type"]!="PRIMARY"){$e=array();foreach($v["columns"]as$x=>$W)$e[]=idf_escape($W).($v["descs"][$x]?" DESC":"");$B=idf_escape($B);$o[]=($v["type"]=="INDEX"?"INDEX $B":"CONSTRAINT $B ".($v["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$B=>$qb)$o[]="CONSTRAINT ".idf_escape($B)." CHECK ($qb)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$o)."\n)$Lb";}function
foreign_keys_sql($Q){$o=array();foreach(foreign_keys($Q)as$je)$o[]=ltrim(format_foreign_key($je));return($o?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$o).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($rc,$Pl=""){return"USE ".idf_escape($rc);}function
use_schema_sql($K,$Pl){$B=idf_escape($K);$i="EXEC(".q("CREATE SCHEMA $B").")";$f=schema_comment($K);if($f!="")$i="BEGIN\n$i\nEXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($K).", @value = ".q($f)."\nEND";return($Pl=="DROP+CREATE"?"DROP SCHEMA IF EXISTS $B;\n":"")."IF SCHEMA_ID(".q($K).") IS NULL $i";}function
trigger_sql($Q){$H="";foreach(triggers($Q)as$B=>$Sm)$H
.=create_trigger(" ON ".table($Q),trigger($B,$Q)).";";return$H;}function
convert_field(array$n){}function
unconvert_field(array$n,$H){return$H;}function
support($Pd){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|procedure|routine|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$Pd);}}add_driver("oracle","Oracle");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$M){return
url_host($M["host"]).($M["port"]!=""?":$M[port]":"").$M["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";private$link,$transaction=false;function
_error($ud,$m){if(ini_bool("html_errors"))$m=html_entity_decode(strip_tags($m));$m=preg_replace('~^[^:]*: ~','',$m);$this->error=$m;}function
attach(array$M,$U,$D){$this->link=@oci_new_connect($U,$D,easy_connect($M),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$m=oci_error();return($m?$m["message"]:lang(26));}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($rc){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($rc));}function
query($F,$en=false){$G=oci_parse($this->link,$F);$this->error="";if(!$G){$m=oci_error($this->link);$this->errno=$m["code"];$this->error=$m["message"];return
false;}set_error_handler(array($this,'_error'));$H=@oci_execute($G,($this->transaction?OCI_NO_AUTO_COMMIT:OCI_COMMIT_ON_SUCCESS));restore_error_handler();if($H){if(oci_num_fields($G))return
new
Result($G);$this->affected_rows=oci_num_rows($G);oci_free_statement($G);}return$H;}function
timeout($vh){return
function_exists('oci_set_call_timeout')&&oci_set_call_timeout($this->link,$vh);}function
inTransaction(){return$this->transaction;}function
begin(){$this->transaction=true;return
true;}function
commit(){return$this->end_transaction(@oci_commit($this->link));}function
rollback(){return$this->end_transaction(@oci_rollback($this->link));}private
function
end_transaction($H){$this->transaction=false;if(!$H){$m=oci_error($this->link);$this->errno=$m["code"];$this->error=$m["message"];}return$H;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$x=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$I[$x]=$W->load();}return$I;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$H->native_type=idx(array(100=>"binary_float","binary_double"),$T,$T);return$H;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";function
attach(array$M,$U,$D){return$this->dsn("oci:dbname=//".easy_connect($M).";charset=AL32UTF8",$U,$D);}function
select_db($rc){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($rc));}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($Yl){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_object($g))$g->query("ALTER SESSION SET CURSOR_SHARING = FORCE"." NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"." NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'"." NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF TZH:TZM'");return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(30)=>array("date"=>19,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(31)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(32)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),lang(34)=>array("sdo_geometry"=>0),);}function
begin(){return$this->conn->begin();}function
convertSearch($u,array$W,array$n){$T=$n["type"];$nj=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $u AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($u)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($u, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$nj))return$u;return(!$nj&&preg_match(number_type(),$T)?$u:"TO_CHAR($u)");}function
quoteBinary($Gk){return"HEXTORAW(".q(bin2hex($Gk)).")";}function
typeName(\stdClass$n){return
strtolower(parent::typeName($n));}function
hasCStyleEscapes(){return
true;}function
select($Q,array$L,array$Z,array$r,array$Ai=array(),$z=1,$C=0,$Kj=false){if(in_array("*",$L)){$ac=array();$ne=false;foreach(fields($Q)as$B=>$n){$Ha=convert_field($n);$ne=($ne||$Ha);$ac[]=($Ha?"$Ha AS ":"").idf_escape($B);}if($ne)$L=$ac;}return
parent::select($Q,$L,$Z,$r,$Ai,$z,$C,$Kj);}function
allFields(){$H=array();$J=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE '.where_owner("c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($J
as$I){$y="$I[precision],$I[scale]";$I["length"]=(strpos($I["type"],"(")?"":($y==","?$I["char_length"]:$y));$I["type"]=strtolower($I["type"]);$I["null"]=($I["nullable"]=="Y");$H[$I["tab"]][]=$I;}return$H;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($he){$H=get_vals("SELECT username FROM all_users WHERE oracle_maintained = 'N' ORDER BY 1");return($H?:get_vals("SELECT username FROM all_users ORDER BY 1"));}function
limit($F,$Z,$z,$fi=0,$Xk=" "){return($fi?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $F$Z) t WHERE rownum <= ".($z+$fi).") WHERE rnum > $fi":($z?" * FROM (SELECT $F$Z) WHERE rownum <= ".($z+$fi):" $F$Z"));}function
limit1($Q,$F,$Z,$Xk="\n"){return" $F$Z";}function
db_collation($k,array$Eb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
where_owner($Qi="owner"){return"$Qi = ".q(DB);}function
views_table($e){return"(SELECT $e FROM all_views WHERE ".where_owner().")";}function
objects_table(){return"(SELECT object_name, DECODE(object_type, 'VIEW', 'view', 'table') object_type FROM all_objects WHERE ".where_owner()." AND object_type IN ('TABLE', 'VIEW'))";}function
tables_list(){return
get_key_vals("SELECT * FROM ".objects_table()." ORDER BY 1");}function
count_tables(array$j){$H=array();foreach($j
as$k)$H[$k]=get_val("SELECT COUNT(*) FROM all_objects WHERE object_type IN ('TABLE', 'VIEW') AND owner = ".q($k));return$H;}function
table_status($B="",$Od=false){$H=array();$Mk=q($B);if($Od||$B!=""){foreach(get_rows('SELECT object_name "Name", object_type "Engine" FROM '.objects_table().($B!=""?" WHERE object_name = $Mk":"").' ORDER BY 1')as$I)$H[$I["Name"]]=$I;return$H;}foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE '.where_owner("t.owner")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM ".views_table("view_name")."
ORDER BY 1")as$I)$H[$I["Name"]]=$I;return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$H=array();$kf=null;foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)." AND ".where_owner()." ORDER BY column_id")as$I){$T=$I["DATA_TYPE"];$y="$I[DATA_PRECISION],$I[DATA_SCALE]";if($y==",")$y=$I["CHAR_COL_DECL_LENGTH"];elseif(strpos($T,"("))$y="";$l=$I["DATA_DEFAULT"];if($l!==null){$l=rtrim($l);if(preg_match("~^'(.*)'\$~s",$l,$A))$l=str_replace("''","'",$A[1]);}if($I["IDENTITY_COLUMN"]=="YES"){if($kf===null)$kf=get_key_vals("SELECT column_name, generation_type FROM all_tab_identity_cols WHERE table_name = ".q($Q)." AND ".where_owner());$l="GENERATED ".$kf[$I["COLUMN_NAME"]].($I["DEFAULT_ON_NULL"]=="YES"?" ON NULL":"")." AS IDENTITY";}$Nj=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($I["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$Nj["where"]=1;$H[$I["COLUMN_NAME"]]=array("field"=>$I["COLUMN_NAME"],"full_type"=>$T.($y?"($y)":""),"type"=>strtolower($T),"length"=>$y,"default"=>$l,"null"=>($I["NULLABLE"]=="Y"),"auto_increment"=>($I["IDENTITY_COLUMN"]=="YES"),"privileges"=>$Nj,);}return$H;}function
table_constraints($Q,$h=null){$H=array();foreach(get_rows('SELECT c.constraint_name "name", c.constraint_type "type", c.r_owner "r_owner", c.r_constraint_name "r_constraint", c.delete_rule "delete_rule", cc.column_name "column"
FROM all_constraints c
JOIN all_cons_columns cc ON cc.owner = c.owner AND cc.constraint_name = c.constraint_name
WHERE c.constraint_type IN (\'P\', \'U\', \'R\') AND '.where_owner("c.owner")." AND c.table_name = ".q($Q).'
ORDER BY cc.position',$h)as$I){$B=$I["name"];$H[$B]["type"]=$I["type"];$H[$B]["r_owner"]=$I["r_owner"];$H[$B]["r_constraint"]=$I["r_constraint"];$H[$B]["delete_rule"]=$I["delete_rule"];$H[$B]["columns"][]=$I["column"];}return$H;}function
indexes($Q,$h=null){$H=array();$Ub=array();foreach(table_constraints($Q,$h)as$B=>$Tb)$Ub[$B]=$Tb["type"];foreach(get_rows("SELECT aic.*, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)." AND ".where_owner("aic.table_owner")."
ORDER BY aic.column_position",$h)as$I){$xf=$I["INDEX_NAME"];$Hb=$I["DATA_DEFAULT"];$Hb=($Hb?trim($Hb,'"'):$I["COLUMN_NAME"]);$T=idx($Ub,$xf);$H[$xf]["type"]=($T=="P"?"PRIMARY":($T=="U"?"UNIQUE":"INDEX"));$H[$xf]["columns"][]=$Hb;$H[$xf]["lengths"][]=($I["CHAR_LENGTH"]&&$I["CHAR_LENGTH"]!=$I["COLUMN_LENGTH"]?$I["CHAR_LENGTH"]:null);$H[$xf]["descs"][]=($I["DESCEND"]&&$I["DESCEND"]=="DESC"?'1':null);}uasort($H,function($ia,$Ra){$Ai=array("PRIMARY"=>0,"UNIQUE"=>1,"INDEX"=>2);return$Ai[$ia["type"]]-$Ai[$Ra["type"]];});return$H;}function
view($B){$J=get_rows('SELECT text "select" FROM '.views_table("view_name, text").' WHERE view_name = '.q($B));return($J?$J[0]:array());}function
collations(){return
array();}function
information_schema($k,$K=""){return
in_array($K!=""?$K:$k,array("INFORMATION_SCHEMA","SYS","SYSTEM"));}function
error(){return
h(connection()->error);}function
explain(Db$g,$F){$g->query("EXPLAIN PLAN FOR $F");return$g->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$B,array$o,array$je,$f,$pd,$Db,$Na,$ej){$b=$Xc=array();$Ii=($Q?fields($Q):array());foreach($o
as$n){$W=$n[1];if($W&&$n[0]!=""&&idf_escape($n[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($n[0])." TO $W[0]");$Hi=$Ii[$n[0]];if($W&&$Hi){$hi=process_field($Hi,$Hi);if($W[2]==$hi[2])$W[2]="";}if($W){list($W[2],$W[3])=array($W[3],$W[2]);$b[]=($Q!=""?($n[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");}else$Xc[]=idf_escape($n[0]);}if($Q=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",array_merge($b,$je))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$Xc||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$Xc).")"))&&($Q==$B||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($B)));}function
alter_indexes($Q,$b){$Xc=array();$Uj=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$i=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($Uj,"ALTER TABLE ".table($Q).$i);}elseif($W[2]=="DROP")$Xc[]=idf_escape($W[1]);else$Uj[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($Xc)array_unshift($Uj,"DROP INDEX ".implode(", ",$Xc));foreach($Uj
as$F){if(!queries($F))return
false;}return
true;}function
foreign_keys($Q){$H=array();$tm=array();foreach(table_constraints($Q)as$B=>$Tb){if($Tb["type"]=="R"){$H[$B]=array("source"=>$Tb["columns"],"target"=>array(),"on_delete"=>$Tb["delete_rule"],"on_update"=>null,);$tm[$B]=array($Tb["r_owner"],$Tb["r_constraint"]);}}if($tm){$Z=array();foreach($tm
as$qm)$Z[]="(owner = ".q($qm[0])." AND constraint_name = ".q($qm[1]).")";foreach(get_rows("SELECT owner, constraint_name, table_name, column_name FROM all_cons_columns WHERE ".implode(" OR ",array_unique($Z))." ORDER BY position")as$I){foreach($tm
as$B=>$qm){if($qm==array($I["OWNER"],$I["CONSTRAINT_NAME"])){$H[$B]["db"]=$I["OWNER"];$H[$B]["table"]=$I["TABLE_NAME"];$H[$B]["target"][]=$I["COLUMN_NAME"];}}}}return$H;}function
trigger($B,$Q){if($B=="")return
array();$J=get_rows('SELECT trigger_name "Trigger", trigger_type "Type", triggering_event "Event", trigger_body "Statement"
FROM all_triggers
WHERE trigger_name = '.q($B)." AND ".where_owner());$H=reset($J);if($H){$T=$H["Type"];$H["Timing"]=(preg_match('~^(BEFORE|AFTER|INSTEAD OF)~',$T,$A)?$A[1]:$T);$H["Type"]=(preg_match('~EACH ROW~',$T)||$T=="INSTEAD OF"?"FOR EACH ROW":"");}return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SELECT trigger_name, trigger_type, triggering_event FROM all_triggers WHERE table_name = ".q($Q)." AND ".where_owner())as$I)$H[$I["TRIGGER_NAME"]]=array(preg_replace('~ (STATEMENT|EACH ROW)$~','',$I["TRIGGER_TYPE"]),$I["TRIGGERING_EVENT"]);return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE","INSERT OR UPDATE","INSERT OR DELETE","UPDATE OR DELETE","INSERT OR UPDATE OR DELETE"),"Type"=>array("FOR EACH ROW",""),);}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mn){return
apply_queries("DROP VIEW",$Mn);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($G){return"0";}function
create_database($k,$Db){$H=queries("CREATE USER ".idf_escape($k)." NO AUTHENTICATION");return($H?queries("GRANT UNLIMITED TABLESPACE TO ".idf_escape($k)):$H);}function
drop_databases(array$j){$H=true;foreach($j
as$k)$H=!!queries("DROP USER ".idf_escape($k)." CASCADE")&&$H;return$H;}function
rename_database($B,$Db){return!!queries("ALTER USER ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$H=array();$J=get_rows('SELECT * FROM v$instance');foreach(reset($J)as$x=>$W)$H[]=array($x,$W);return$H;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess
LEFT JOIN v$sql sql ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$n){if($n["type"]=="sdo_geometry")return"SDO_UTIL.TO_WKTGEOMETRY(".idf_escape($n["field"]).")";}function
unconvert_field(array$n,$H){return($n["type"]=="sdo_geometry"?"SDO_UTIL.FROM_WKTGEOMETRY($H)":$H);}function
support($Pd){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|sql|status|table|trigger|variables|view|view_trigger)$~',$Pd);}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+30b83850")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($i=false){return
password_file($i);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($M){return
h($M);}function
database(){return
DB;}function
databases($he=true){return
get_databases($he);}function
pluginsLinks(){}function
operators($Yl=null){return
driver()->operators($Yl);}function
schemas(){$H=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$H))array_unshift($H,$_GET["ns"]);return$H;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$ic){return$ic;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$ff=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Vk=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($ff!=""?" - $ff":""),'short_name'=>'Adminer','description'=>lang(38),'start_url'=>$Vk,'scope'=>$Vk,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+30b83850",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($nc=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$H=array();foreach(array("","-dark")as$qh){$p="adminer$qh.css";if(file_exists($p)){$Vd=file_get_contents($p);$H["$p?v=".crc32($Vd)]=($qh?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Vd)?'':'light'));}}return$H;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(39).'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,on('change','loginDriver'))),adminer()->loginFormField('server','<tr><th>'.lang(40).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(41)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(42).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("fire(qs('#username').form['auth[driver]'], 'change');")),adminer()->loginFormField('password','<tr><th>'.lang(43).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(44).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(45)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(46))."\n";}function
loginFormField($B,$We,$X){return$We.$X."\n";}function
login($Hg,$D){if($D=="")return
lang(47).require_password_link(null);if(!Driver::$passwords)return
lang(48).require_password_link($D);if(!password_required())return
lang(49).require_password_link($D);return
true;}function
tableName(array$Yl){return
h($Yl["Name"]);}function
fieldName(array$n,$Ai=0){$T=$n["full_type"].($n["null"]?" NULL":"");$f=$n["comment"];return'<span title="'.h($T.($f!=""?($T?": ":"").$f:'')).'">'.h($n["field"]).'</span>';}function
commentValue($T,$f){if($f==""||$T=='SCHEMA'||$T=='TABLE'||$T=='COLUMN')return
h($f);$Bj=function($Gk,$kb='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$kb>",preg_replace('~\|$~m',"",rtrim($Gk))));};$Q='(\+--[-+]+\+\n)';$I='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$Q?$I$Q?($I*)$Q?~m",function($A)use($Bj){return"<table>\n".($A[1]?"<thead>".$Bj($A[2],'th')."<tbody>\n":$Bj($A[2])).$Bj($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($f))))."</pre>\n";}function
commentInput($T,$c,$f){$X=h($f);return(preg_match('~\n~',$X)?"<textarea$c rows='2' cols='".($T=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");}function
selectLinks(array$Yl,$N=""){$B=$Yl["Name"];echo'<p class="links">';$Cg=array();if($B!="")$Cg["select"]=lang(50);if(support("table")||support("indexes"))$Cg["table"]=lang(51);if(support("table")){if(is_view($Yl)){if(support("view"))$Cg["view"]=lang(52);}elseif(function_exists('Adminer\alter_table')&&$B!="")$Cg["create"]=lang(53);}if($N!==null)$Cg["edit"]=lang(54);foreach($Cg
as$x=>$W)echo" <a href='".h(ME)."$x=".url_escape($B).($x=="edit"?$N:"")."'".bold(isset($_GET[$x])).">$W</a>";echo"\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$Xl){return
array();}function
backwardKeysPrint(array$Ta,array$I){}function
selectQuery($F,$Il,$Nd=false){$H="\n";if(!$Nd&&($Qn=driver()->warnings())){$t="warnings";$H=", <a href='#$t' class='toggle'>".lang(55)."</a>"."$H<div id='$t' class='hidden'>\n$Qn</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>(".format_time($Il).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($F)."' class='hover'>".lang(14)."</a>":"").$H;}function
sqlCommandQuery($F){return
shorten_utf8(trim($F),1000);}function
sqlPrintAfter(){}function
sqlUrlLength(){return
2000;}function
explain(Db$g,$F,array$Ei){$G=explain($g,$F);if(!$G)return"";ob_start();print_select_result($G,$g,$Ei);return
ob_get_clean();}function
rowDescription($Q){return"";}function
rowDescriptions(array$J,array$ke){return$J;}function
selectLink($W,array$n){}function
selectVal($W,$_,array$n,$Li){$H=($W===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$n["type"])&&!preg_match("~var~",$n["type"])?"<code>$W</code>":(preg_match('~^jsonb?$~',$n["full_type"])?"<code class='jush-json'>$W</code>":$W)));if(is_blob($n)&&!is_utf8($W))$H="<i>".lang(56,strlen($Li))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$H</a>":$H);}function
editVal($W,array$n){return$W;}function
config(){return
array();}function
tableStructurePrint(array$o,$Yl=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(57)."<th>".lang(58).(support("comment")?"<th>".lang(59):"")."<tbody>\n";$Bn=(support("type")?types():array());foreach($o
as$n){echo"<tr><th>".h($n["field"]);$T=h($n["full_type"]);$Db=h($n["collation"]);echo"<td><span title='$Db'>".(in_array($T,$Bn)?"<a href='".h(ME.'type='.url_escape($T))."'>$T</a>":$T.($Db&&isset($Yl["Collation"])&&$Db!=$Yl["Collation"]?" $Db":""))."</span>",($n["null"]?" <i>NULL</i>":""),($n["auto_increment"]?" <i>".lang(60)."</i>":""),(isset($n["default"])?" <span title='".lang(61)."'>[<b>".($n["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($n["default"])),80,"</code>"):h($n["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$n["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w,array$Yl){$Zi=false;foreach($w
as$B=>$v)$Zi|=!!$v["partial"];echo"<table>\n";$xc=first(driver()->indexAlgorithms($Yl));foreach($w
as$B=>$v){ksort($v["columns"]);$Kj=array();foreach($v["columns"]as$x=>$W)$Kj[]="<i>".h($W)."</i>".($v["lengths"][$x]?"(".h($v["lengths"][$x]).")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($v["type"]).($xc&&$v['algorithm']!=$xc?" (".h($v['algorithm']).")":""),"<td>".implode(", ",$Kj);if($Zi)echo"<td>".($v['partial']?"<code class='jush-".JUSH."'>WHERE ".h($v['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($T){if($T=="FOREIGN"||$T=="CHECK")return"";if($T=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($c,array$e,$X="",$rj=""){return
select_input($c,$e,$X,$rj);}function
selectColumnsPrint(array$L,array$e){print_fieldset("select",lang(62),$L);$s=0;$L[""]=array();foreach($L
as$x=>$W){$W=idx($_GET["columns"],$x,array());$d=adminer()->selectColumnInput(" name='columns[$s][col]' data-default=''".on('change',($x!==""?'selectFieldChange':'selectAddRow')),$e,$W["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array(lang(63)=>driver()->functions,lang(64)=>driver()->grouping)),$W["fun"]," data-default=''".on('change',($x!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w,$Yl=null){print_fieldset("search",lang(65),$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"):''),"</div>\n";}$vi=adminer()->operators($Yl);foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$W){if(!$W||(("$W[col]$W[val]"!=""||preg_match('~NULL$~',$W["op"]))&&in_array($W["op"],$vi)))echo"<div>".adminer()->selectColumnInput(" name='where[$s][col]' data-default=''".on('change',($W?'selectFieldChange':'selectAddRow')),$e,$W["col"],"(".lang(66).")"),html_select("where[$s][op]",$vi,$W["op"]," data-default='".h(first($vi))."'".on('change','selectFirstChange')),"<input type='search' name='where[$s][val]' value='".h($W["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$Ai,array$e,array$w){print_fieldset("sort",lang(67),$Ai);$s=0;foreach((array)$_GET["order"]as$x=>$W){if($W!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$s]' data-default=''".on('change','selectFieldChange'),$e,$W),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),lang(68))."</div>\n";$s++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$s]' data-default=''".on('change','selectAddRow'),$e),checkbox("desc[$s]",1,false,lang(68))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".lang(69)."</legend><div>","<input type='number' name='limit' class='size' value='".h($z?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($xm){echo"<fieldset><legend>".lang(70)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($xm)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".lang(71)."</legend><div>","<input type='submit' value='".lang(62)."'>"," <span id='noindex' title='".lang(72)."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
as$v){$mc=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$mc)$e[$mc]=1;}$e[""]=1;foreach($e
as$x=>$W)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$ld,array$e){}function
selectColumnsProcess(array$e,array$w){$L=array();$r=array();foreach((array)$_GET["columns"]as$x=>$W){if($W["fun"]=="count"||($W["col"]!=""&&(!$W["fun"]||in_array($W["fun"],driver()->functions)||in_array($W["fun"],driver()->grouping)))){$L[$x]=apply_sql_function($W["fun"],($W["col"]!=""?idf_escape($W["col"]):"*"));if(!in_array($W["fun"],driver()->grouping))$r[]=$L[$x];}}return
array($L,$r);}function
selectSearchProcess(array$o,array$w,$Yl=null){$H=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$s)!="")$H[]=driver()->fulltextSql($s,$v,$_GET["fulltext"][$s],isset($_GET["boolean"][$s]));}$vi=adminer()->operators($Yl);foreach((array)$_GET["where"]as$x=>$W){$W+=array("col"=>"","op"=>first($vi),"val"=>"");$_GET["where"][$x]=$W;$Bb=$W["col"];if(("$Bb$W[val]"!=""||preg_match('~NULL$~',$W["op"]))&&in_array($W["op"],$vi)){if($W["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Nb=array();foreach(($Bb!=""?array($Bb=>idx($o,$Bb,array())):$o)as$B=>$n){if($Bb!=""||is_searchable($n,$W))$Nb[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($B),$W,$n),$W["op"],$W["val"]);}$H[]=(count($Nb)==1?$Nb[0]:($Nb?"(".implode(" OR ",$Nb).")":"1 = 0"));}}return$H;}function
selectSearchCondition($d,$ri,$W){if(preg_match('~IN$~',$ri))return"$d $ri ".($W!=""?process_in($W):"(NULL)");if($ri=="BETWEEN")return"$d $ri ".process_between($W);if($ri=="SQL")return"$d $W";if(preg_match('~^(I?LIKE) %%$~',$ri,$A))return"$d $A[1] ".q("%$W%");if($ri=="FIND_IN_SET")return"$ri(".q($W).", $d)";return"$d $ri".(!preg_match('~NULL$~',$ri)?" ".q($W):"");}function
selectOrderProcess(array$o,array$w){$H=array();foreach((array)$_GET["order"]as$x=>$W){if($W!="")$H[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$W)?$W:idf_escape($W)).(isset($_GET["desc"][$x])?" DESC".(JUSH=='pgsql'&&idx($o[$W],"null")?" NULLS LAST":""):"");}return$H;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$ke){return
false;}function
selectQueryBuild(array$L,array$Z,array$r,array$Ai,$z,$C){return"";}function
messageQuery($F,$zm,$Nd=false){restart_session();$cf=&get_session("queries");if(!idx($cf,$_GET["db"]))$cf[$_GET["db"]]=array();if(strlen($F)>1e6)$F=preg_replace('~[\x80-\xFF]+$~','',substr($F,0,1e6))."\n…";$cf[$_GET["db"]][]=array($F,time(),$zm);$Dl="sql-".count($cf[$_GET["db"]]);$H="<a href='#$Dl' class='toggle'>".lang(73)."</a> ".copy_icon()."\n";if(!$Nd&&($Qn=driver()->warnings())){$t="warnings-".count($cf[$_GET["db"]]);$H="<a href='#$t' class='toggle'>".lang(55)."</a>, $H<div id='$t' class='hidden'>\n$Qn</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $H<div id='$Dl' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($F,1e4)."</code></pre>".($zm?" <span class='time'>($zm)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($cf[$_GET["db"]])-1)).'">'.lang(14).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($Q,array$o,$I,$pn,$F='',$zm=''){echo($F!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>($zm)</span>\n":"");}function
editFunctions(array$n){$H=($n["null"]?"NULL/":"");$Se=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$ze){if(!$x||(!isset($_GET["call"])&&$Se)){foreach($ze
as$nj=>$W){if(!$nj||preg_match("~$nj~",$n["type"]))$H
.="/$W";}}if($x&&$ze&&!preg_match('~set|bool~',$n["type"])&&!is_blob($n))$H
.="/SQL";}if($n["auto_increment"]&&!$Se)$H=lang(60);return
explode("/",$H);}function
editInput($Q,array$n,$c,$X){if($n["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$c,$n,$X,"NULL");return"";}function
editHint($Q,array$n,$X){return"";}function
processInput(array$n,$X,$ye=""){if($ye=="SQL")return$X;$B=$n["field"];$H=q($X);if(preg_match('~^(now|getdate|uuid)$~',$ye))$H="$ye()";elseif(preg_match('~^current_(date|timestamp)$~',$ye))$H=$ye;elseif(preg_match('~^([+-]|\|\|)$~',$ye))$H=idf_escape($B)." $ye $H";elseif(preg_match('~^[+-] interval$~',$ye))$H=idf_escape($B)." $ye ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$X)&&JUSH!="pgsql"?$X:$H);elseif(preg_match('~^(addtime|subtime|concat)$~',$ye))$H="$ye(".idf_escape($B).", $H)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$ye))$H="$ye($H)";return
unconvert_field($n,$H);}function
dumpOutput(){$H=array('text'=>lang(74),'file'=>lang(75));if(function_exists('gzencode'))$H['gz']='gzip';return$H;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($k){}function
dumpTable($Q,$Pl,$Xf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Pl)dump_csv(array_keys(fields($Q)));}else{if($Xf==2){$o=array();foreach(fields($Q)as$B=>$n)$o[]=idf_escape($B)." ".full_type_sql($n);$i="CREATE TABLE ".table($Q)." (".implode(", ",$o).")";}else$i=create_sql($Q,$_POST["auto_increment"],$Pl);set_utf8mb4($i);if($Pl&&$i){if(($Pl=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$Xf==1)echo"DROP ".($Xf==2?"VIEW":"TABLE")." IF EXISTS ".table($Q).";\n";if($Xf==1)$i=remove_definer($i);echo"$i;\n\n";}}}function
dumpData($Q,$Pl,$F,array$L=array(),array$Z=array(),array$r=array(),array$Ai=array()){if($Pl){$Sg=(JUSH=="sqlite"?0:1048576);$o=array();$lf=false;if($_POST["format"]=="sql"){if($Pl=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($Q).";\n";$o=fields($Q);if(JUSH=="mssql"){foreach($o
as$n){if($n["auto_increment"]){echo"SET IDENTITY_INSERT ".table($Q)." ON;\n";$lf=true;break;}}}}$G=($F!=""?connection()->query($F,1):driver()->select($Q,($L?:array("*")),$Z,$r,$Ai,0));if($G){$Hf="";$eb="";$fg=array();$_e=array();$Rl="";$Qd=($Q!=''?'fetch_assoc':'fetch_row');$ec=0;while($I=$G->$Qd()){if(!$fg){$Y=array();foreach($I
as$W){$n=$G->fetch_field();if(idx($o[$n->name],'generated')){$_e[$n->name]=true;continue;}$fg[]=$n->name;$x=idf_escape($n->name);$Y[]="$x = VALUES($x)";}$Rl=($Pl=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Y):"").";\n";}foreach($I
as$x=>$W){if(is_array($W))$I[$x]=json_encode($W,256|64);}if($_POST["format"]!="sql"){if($Pl=="table"){dump_csv($fg);$Pl="INSERT";}dump_csv($I);}else{if(!$Hf)$Hf="INSERT INTO ".table($Q)." (".implode(", ",array_map('Adminer\idf_escape',$fg)).") VALUES";foreach($I
as$x=>$W){if($_e[$x]){unset($I[$x]);continue;}$n=$o[$x];$I[$x]=($W===null?"NULL":($W===false?0:unconvert_field($n,preg_match(number_type(),$n["type"])&&!preg_match('~\[~',$n["full_type"])&&is_numeric($W)?$W:(!is_blob($n)||is_utf8($W)?q($W):driver()->quoteBinary($W)))));}$Gk=($Sg?"\n":" ")."(".implode(",\t",$I).")";if(!$eb)$eb=$Hf.$Gk;elseif(JUSH=='mssql'?$ec%1000!=0:strlen($eb)+4+strlen($Gk)+strlen($Rl)<$Sg)$eb
.=",$Gk";else{echo$eb.$Rl;$eb=$Hf.$Gk;}}$ec++;}if($eb)echo$eb.$Rl;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
dump_csv(array(connection()->error));if($lf)echo"SET IDENTITY_INSERT ".table($Q)." OFF;\n";}}function
dumpFilename($jf){return
friendly_url($jf!=""?$jf:(SERVER?:"localhost"));}function
dumpHeaders($jf,$xh=false){$Pi=$_POST["output"];$Id=(preg_match('~sql~',$_POST["format"])?"sql":($xh?"tar":"csv"));header("Content-Type: ".($Pi=="gz"?"application/x-gzip":($Id=="tar"?"application/x-tar":($Id=="sql"||$Pi!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Pi=="gz"){ob_start(function($P){return
gzencode($P);},1e6);}return$Id;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(76)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(77):lang(78))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(79)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(80)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(81)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(82)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(83)."</a>\n":"");return
true;}function
navigation($ph){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Qh=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Qh)<0?h($Qh):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($ph=="auth"){$Pi="";foreach((array)$_SESSION["pwds"]as$Jn=>$kl){foreach($kl
as$M=>$Cn){$B=h(get_setting("vendor-$Jn-$M")?:get_driver($Jn));foreach($Cn
as$U=>$D){if($B&&$D!==null){$vc=$_SESSION["db"][$Jn][$M][$U];foreach(($vc?array_keys($vc):array(""))as$k)$Pi
.="<li><a href='".h(auth_url($Jn,$M,$U,$k))."'>($B) ".h("$U@").($M!=""?adminer()->serverName($M):"").h($k!=""?" - $k":"")."</a>\n";}}}}if($Pi)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Pi</ul>\n";}else{$S=array();if($_GET["ns"]!==""&&!$ph&&DB!=""){connection()->select_db(DB);$S=table_status('',true);}adminer()->syntaxHighlighting($S);adminer()->databasesPrint($ph);$la=array();if(DB==""||!$ph){if(support("sql")){$la['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(73)."</a>";$la['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(84)."</a>";}$la['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(85)."</a>";}$rf=$_GET["ns"]!==""&&!$ph&&DB!="";if($rf&&function_exists('Adminer\alter_table'))$la['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(86)."</a>";$la=adminer()->menuActions($la,$ph);echo($la?"<p class='links'>\n".implode("\n",$la)."\n":"");if($rf){if($S)adminer()->tablesPrint($S);else
echo"<p class='message'>".lang(13)."</p>\n";}}}function
syntaxHighlighting(array$S){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.1.2+30b83850",true);$sh=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($sh?script("addEventListener('DOMContentLoaded', () => {\n$sh\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($S){$Cg=array();foreach($S
as$Q=>$T)$Cg[]=js_escape_re($Q);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Cg).')(?!\$)\b/g',false);$Fl=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($Fl))){foreach(routines()as$I)json_row(js_escape(ME).'function='.url_escape($I["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($I["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$W)echo"jushLinks.$W = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$Kl=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Pa=Driver::jushAutocomplete($S,$Kl);echo($Pa?"addEventListener('DOMContentLoaded', () => { autocompleter = $Pa; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($ph){if(support("single_db"))return;$j=adminer()->databases();if(DB&&$j&&!in_array(DB,$j))array_unshift($j,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$sc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(44)."'>".lang(87).": ".($j?html_select("db",array(""=>"")+group_system($j),DB,$sc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(25)."'".($j?" class='hidden'":"").">\n";if(support("scheme")){if($ph!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".lang(88).": ".html_select("ns",array(""=>"")+group_system(adminer()->schemas(),true),$_GET["ns"],$sc)."</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$W){if(isset($_GET[$W])){echo
input_hidden($W);break;}}echo"</p></form>\n";}function
menuActions(array$la,$ph){return$la;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$Q=>$O){$Q="$Q";$B=adminer()->tableName($O);if($B!=""&&!$O["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($Q).'"'.bold($_GET["select"]==$Q||$_GET["edit"]==$Q,"select hover")." title='".lang(50)."'>".lang(89)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($Q).'"'.bold(in_array($Q,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($O)?"view":"structure"))." title='".lang(51)."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($t){return
kill_process($t);}function
formatSizeValue($W){return
format_number($W);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($vj){$Wc=SqlDriver::$drivers;$Ye=" href='https://www.adminer.org/plugins/#use'".target_blank();if($vj===null){$vj=array();$Xa="adminer-plugins";if(is_dir($Xa)){foreach(glob("$Xa/*.php")as$p){$Wd=SqlDriver::$drivers;$this->includeOnce($p);foreach(array_diff_key(SqlDriver::$drivers,$Wd)as$t=>$B)$this->driverFiles[$t]=$p;}}if(file_exists("$Xa.php")){$tf=$this->includeOnce("$Xa.php");if(is_array($tf)){foreach($tf
as$x=>$sj)$vj[is_object($sj)?get_class($sj):$x]=$sj;}else$this->error
.=lang(90,"<b>$Xa.php</b>",$Ye)."<br>";}foreach(get_declared_classes()as$zb){if(!$vj[$zb]&&(preg_match('~^Adminer\w~i',$zb)||is_subclass_of($zb,'Adminer\Plugin'))){$gk=new
\ReflectionClass($zb);$Vb=$gk->getConstructor();if($Vb&&$Vb->getNumberOfRequiredParameters())$this->error
.=lang(91,$Ye,"<b>$zb</b>","<b>$Xa.php</b>")."<br>";else$vj[$zb]=new$zb;}}}$Mf=array_filter($vj,function($sj){return!is_object($sj);});if($Mf){$this->error
.=lang(92,$Ye)."<br>";$vj=array_diff_key($vj,$Mf);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Wc);$this->plugins=$vj;$qa=new
Adminer;$vj[]=$qa;$gk=new
\ReflectionObject($qa);foreach($gk->getMethods()as$mh){foreach($vj
as$sj){$B=$mh->getName();if(method_exists($sj,$B))$this->hooks[$B][]=$sj;}}}function
includeOnce($p){return
include_once"./$p";}static
function
checksum($p){$Vd=str_replace("\r","",file_get_contents($p));$Vd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Vd);return
dechex(crc32($Vd));}function
checksums(){$Xd=array_values($this->driverFiles);foreach($this->plugins
as$sj){$gk=new
\ReflectionObject($sj);$Xd[]=$gk->getFileName();}$H=array();foreach($Xd
as$p)$H[basename($p,'.php')]=self::checksum($p);return$H;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'5fb34a8b','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'eb823bb0','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'c78461b3','name-patterns'=>'84c10d09','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-expression'=>'4b61d59c','select-foreign'=>'38ac915c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'92ca960d','elastic'=>'1582a04d','firebird'=>'1c4309b2','igdb'=>'a6a1750a','imap'=>'3da1022b','mongo'=>'63486492','redis'=>'6a653462','simpledb'=>'b8e2cc7d',);}function
__call($B,array$Wi){$Fa=array();foreach($Wi
as$x=>$W)$Fa[]=&$Wi[$x];$H=null;foreach($this->hooks[$B]as$sj){$X=call_user_func_array(array($sj,$B),$Fa);if($X!==null){if(!self::$append[$B])return$X;$H=$X+(array)$H;}}return$H;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($u,$Xh=null){$Fa=func_get_args();$Fa[0]=idx($this->translations[LANG],$u)?:$u;return
call_user_func_array('Adminer\lang_format',$Fa);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($jj){$this->password_hash=$jj;}function
description(){return
lang(93);}function
credentials(){$D=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($D)&&!password_required()?"":$D));}function
login($Hg,$D){if($this->passwordMatches($D))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($D){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($D),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$M,$U,$D){mysqli_report(MYSQLI_REPORT_OFF);$wj=$M["port"];$nd=("$M[host]$wj$M[socket]"=="");$Hl=adminer()->connectSsl();$zn=($Hl&&($Hl['key']||$Hl['cert']||$Hl['ca']||isset($Hl['verify'])));if($zn)$this->ssl_set($Hl['key'],$Hl['cert'],$Hl['ca'],'','');$H=@$this->real_connect((!$nd?$M["host"]:ini_get("mysqli.default_host")),(!$nd||$U!=""?$U:ini_get("mysqli.default_user")),(!$nd||$U.$D!=""?$D:ini_get("mysqli.default_pw")),null,($wj!=""?intval($wj):ini_get("mysqli.default_port")),($wj!=""?null:$M["socket"]),($zn?($Hl['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($H?'':$this->error);}function
set_charset($ob){if(parent::set_charset($ob))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $ob");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$M,$U,$D){if(ini_bool("mysql.allow_local_infile"))return
lang(94,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$wj="$M[port]$M[socket]";$B=$M["host"].($wj!=""?":$wj":"");$this->link=@mysql_connect(($B!=""?$B:ini_get("mysql.default_host")),($B.$U!=""?$U:ini_get("mysql.default_user")),($B.$U.$D!=""?$D:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($ob){return
mysql_set_charset($ob,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($rc){return
mysql_select_db($rc,$this->link);}function
query($F,$en=false){$G=@($en?mysql_unbuffered_query($F,$this->link):mysql_query($F,$this->link));$this->error="";if(!$G){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($G===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($G);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=mysql_num_rows($G);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$H=mysql_fetch_field($this->result,$this->offset++);$H->orgtable=$H->table;$H->native_type=idx(array("string"=>"varchar","real"=>"double"),$H->type,$H->type);return$H;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$M,$U,$D){$yi=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$yi[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$Hl=adminer()->connectSsl();if($Hl){if($Hl['key'])$yi[\PDO::MYSQL_ATTR_SSL_KEY]=$Hl['key'];if($Hl['cert'])$yi[\PDO::MYSQL_ATTR_SSL_CERT]=$Hl['cert'];if($Hl['ca'])$yi[\PDO::MYSQL_ATTR_SSL_CA]=$Hl['ca'];if(isset($Hl['verify']))$yi[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Hl['verify'];}$ff=$M["host"];$wj=$M["port"];$ul=$M["socket"];return$this->dsn("mysql:charset=utf8".($ff!=""?";host=$ff":'').($wj!=""?";port=$wj":($ul!=""?";unix_socket=$ul":"")),$U,$D,$yi);}function
set_charset($ob){return$this->query("SET NAMES $ob");}function
select_db($rc){return$this->query("USE ".idf_escape($rc));}function
query($F,$en=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$en);return
parent::query($F,$en);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Yl){return
array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$D){$g=parent::connect($M,$U,$D);if(is_string($g)){if(function_exists('iconv')&&!is_utf8($g)&&strlen($Gk=iconv("windows-1252","utf-8//IGNORE",$g))>strlen($g))$g=$Gk;return$g;}$g->set_charset(charset($g));$g->query("SET sql_quote_show_create = 1, autocommit = 1");$g->flavor=(preg_match('~MariaDB~',$g->server_info)?'maria':'mysql');add_driver(DRIVER,($g->flavor=='maria'?"MariaDB":"MySQL"));return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(30)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(31)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(95)=>array("enum"=>65535,"set"=>64),lang(32)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(34)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$g))$this->types[lang(31)]["json"]=4294967295;if(min_version('',10.7,$g)){$this->types[lang(31)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$g)){$this->types[lang(33)]["inet6"]=39;if(min_version('','10.10',$g))$this->types[lang(33)]["inet4"]=15;}if(min_version(9,11.7,$g))$this->types[lang(29)]["vector"]=16383;if(min_version(5.7,10.2,$g))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$n){return(preg_match("~binary~",$n["type"])?"<code class='jush-sql'>UNHEX</code>":($n["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($n["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$n["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$N){return($N?parent::insert($Q,$N):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$J,array$Ij){$e=array_keys(reset($J));$Dj="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$x)$Y[$x]="$x = VALUES($x)";$Rl="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$y=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&(strlen($Dj)+$y+strlen($X)+strlen($Rl)>1e6)){if(!queries($Dj.implode(",\n",$Y).$Rl))return
false;$Y=array();$y=0;}$Y[]=$X;$y+=strlen($X)+2;}return
queries($Dj.implode(",\n",$Y).$Rl);}function
slowQuery($F,$_m){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$_m FOR $F";elseif(preg_match('~^(SELECT\b)(.+)~is',$F,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($_m*1000).") */ $A[2]";}}function
convertColumn($u,array$n){if(preg_match("~binary~",$n["type"]))return"HEX($u)";if($n["type"]=="bit")return"BIN($u + 0)";if($n["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($u)";if(preg_match("~geom|point|linestring|polygon~",$n["type"]))return(min_version(8)?"ST_":"")."AsWKT($u)";return"";}function
convertSearch($u,array$W,array$n){return($this->convertColumn($u,$n)?:(preg_match('~'.text_type().'~',$n["type"])&&!preg_match("~^utf8~",$n["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($u USING ".charset($this->conn).")":$u));}function
typeName(\stdClass$n){$B=parent::typeName($n);if($B!=""){$dn=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($dn,$B,strtolower($B));}$dn=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$H=idx($dn,$n->type,"");return($n->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$H):$H);}function
quoteBinary($Gk){return"X".q(bin2hex($Gk));}function
md5($d,array$n){if(is_blob($n)||preg_match('~'.text_type().'~',$n["type"]))return"MD5(".(is_blob($n)||preg_match("~^utf8~",$n["collation"])?$d:"CONVERT($d USING ".charset($this->conn).")").")";}function
warnings(){$G=$this->conn->query("SHOW WARNINGS");if($G&&$G->num_rows){ob_start();print_select_result($G);return
ob_get_clean();}}function
tableHelp($B,$Xf=false){$Jg=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Jg?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="sys")return($Jg?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$B)).".html"));if(DB=="mysql")return($Jg?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($Q){$se="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$G=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $se ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$I=($G?$G->fetch_row():null);if(!$I)return
array();$H=array();list($H["partition_by"],$H["partition"],$H["partitions"])=$I;$fj=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $se AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$H["partition_names"]=array_keys($fj);$H["partition_values"]=array_values($fj);return$H;}function
checkConstraints($Q){$H=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$H:array_map('stripslashes',$H));}function
hasCStyleEscapes(){static$hb;if($hb===null){$El=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$hb=(strpos($El,'NO_BACKSLASH_ESCAPES')===false);}return$hb;}function
hasEstimatedRows(){return
true;}function
isSystem($k,$K=""){return
information_schema($k,$K)||in_array($k,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$H=array();foreach(get_rows("SHOW ENGINES")as$I){if(preg_match("~YES|DEFAULT~",$I["Support"]))$H[]=$I["Engine"];}return$H;}function
indexAlgorithms(array$Yl){return(preg_match('~^(MEMORY|NDB)$~',$Yl["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($he){$H=get_session("dbs");if($H===null){$F="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Il=microtime(true);$H=($he?slow_query($F):get_vals($F));if(microtime(true)-$Il>0.1){restart_session();set_session("dbs",$H);stop_session();}}return$H;}function
limit($F,$Z,$z,$fi=0,$Xk=" "){return" $F$Z".($z?$Xk."LIMIT $z".($fi?" OFFSET $fi":""):"");}function
limit1($Q,$F,$Z,$Xk="\n"){return
limit($F,$Z,1,0,$Xk);}function
db_collation($k,array$Eb){$H=null;$i=get_val("SHOW CREATE DATABASE ".idf_escape($k),1);if(preg_match('~ COLLATE ([^ ]+)~',$i,$A))$H=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$i,$A))$H=$Eb[$A[1]][-1];return$H;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$j){$H=array();foreach($j
as$k)$H[$k]=count(get_vals("SHOW TABLES IN ".idf_escape($k)));return$H;}function
table_status($B="",$Od=false){$H=array();$F="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name");$K=array();foreach(($Od?array():get_rows($F))as$I)$K[$I["Name"]]=$I;$Hj=null;foreach(get_rows($Od?$F:"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$I){$Li=idx($K,$I["Name"]);if($Li){if($I["Comment"]!==$Li["Comment"]&&$I["Comment"]!==$Hj)$I["Error"]=$I["Comment"];$Hj=$I["Comment"];$I["Comment"]=$Li["Comment"];$I["Engine"]=$Li["Engine"];}if($I["Engine"]=="InnoDB")$I["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$I["Comment"]);if(!isset($I["Engine"]))$I["Comment"]="";if($B!="")$I["Name"]=$B;$H[$I["Name"]]=$I;}return$H;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($ve){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$ve,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($Q){$Jg=(connection()->flavor=='maria');$H=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$I){$n=$I["COLUMN_NAME"];$T=$I["COLUMN_TYPE"];$Ae=$I["GENERATION_EXPRESSION"];$Ld=$I["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Ld,$_e);list($cn,$y,$nn)=parse_type($T);$l=$I["COLUMN_DEFAULT"];if($l!=""){$Vf=preg_match('~text|json~',$cn);if(!$Jg&&$Vf)$l=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($l));if($Jg||$Vf){$l=($l=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$l));}if(!$Jg&&preg_match('~binary~',$cn)&&preg_match('~^0x(\w*)$~',$l,$A))$l=pack("H*",$A[1]);}$H[$n]=array("field"=>$n,"full_type"=>$T,"type"=>$cn,"length"=>$y,"unsigned"=>$nn,"default"=>($_e?($Jg?$Ae:stripslashes($Ae)):$l),"null"=>($I["IS_NULLABLE"]=="YES"),"auto_increment"=>($Ld=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Ld,$A)?$A[1]:""),"collation"=>$I["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$I[PRIVILEGES],where,order")),"comment"=>$I["COLUMN_COMMENT"],"primary"=>($I["COLUMN_KEY"]=="PRI"),"generated"=>($_e[1]=="PERSISTENT"?"STORED":$_e[1]),);}return$H;}function
indexes($Q,$h=null){$H=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$h)as$I){$B=$I["Key_name"];$H[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($I["Index_type"]=="FULLTEXT"?"FULLTEXT":($I["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$I["Index_type"])?$I["Index_type"]:"INDEX"):"UNIQUE")));$H[$B]["columns"][]=$I["Column_name"];$H[$B]["lengths"][]=($I["Index_type"]=="SPATIAL"?null:$I["Sub_part"]);$H[$B]["descs"][]=null;$H[$B]["algorithm"]=$I["Index_type"];}return$H;}function
foreign_keys($Q){static$nj='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$H=array();$fc=get_val("SHOW CREATE TABLE ".table($Q),1);if($fc){preg_match_all("~CONSTRAINT ($nj) FOREIGN KEY ?\\(((?:$nj,? ?)+)\\) REFERENCES ($nj)(?:\\.($nj))? \\(((?:$nj,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$fc,$Mg,PREG_SET_ORDER);foreach($Mg
as$A){preg_match_all("~$nj~",$A[2],$yl);preg_match_all("~$nj~",$A[5],$qm);$H[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$yl[0]),"target"=>array_map('Adminer\idf_unescape',$qm[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$H=array();foreach(get_rows("SHOW COLLATION")as$I){if($I["Default"])$H[$I["Charset"]][-1]=$I["Collation"];else$H[$I["Charset"]][]=$I["Collation"];}ksort($H);foreach($H
as$x=>$W)sort($H[$x]);return$H;}function
information_schema($k,$K=""){return($k=="information_schema")||(min_version(5.5)&&$k=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($k,$Db){return
queries("CREATE DATABASE ".idf_escape($k).($Db?" COLLATE ".q($Db):""));}function
drop_databases(array$j){$H=apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$H;}function
rename_database($B,$Db){$H=false;if(create_database($B,$Db)){$S=array();$Mn=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$Mn[]=$Q;else$S[]=$Q;}$H=(!$S&&!$Mn)||move_tables($S,$Mn,$B);drop_databases($H?array(DB):array());}return$H;}function
auto_increment(){$Oa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Oa="";break;}if($v["type"]=="PRIMARY")$Oa=" UNIQUE";}}return" AUTO_INCREMENT$Oa";}function
alter_table($Q,$B,array$o,array$je,$f,$pd,$Db,$Na,$ej){$b=array();foreach($o
as$n){if($n[1]){$l=$n[1][3];if(preg_match('~ GENERATED~',$l)){$n[1][3]=(connection()->flavor=='maria'?"":$n[1][2]);$n[1][2]=$l;}$b[]=($Q!=""?($n[0]!=""?"CHANGE ".idf_escape($n[0]):"ADD"):" ")." ".implode($n[1]).($Q!=""?$n[2]:"");}else$b[]="DROP ".idf_escape($n[0]);}$b=array_merge($b,$je);$O=($f!==null?" COMMENT=".q($f):"").($pd?" ENGINE=".q($pd):"").($Db?" COLLATE ".q($Db):"").($Na!=""?" AUTO_INCREMENT=$Na":"");if($ej){$fj=array();if($ej["partition_by"]=='RANGE'||$ej["partition_by"]=='LIST'){foreach($ej["partition_names"]as$x=>$W){$X=$ej["partition_values"][$x];$fj[]="\n  PARTITION ".idf_escape($W)." VALUES ".($ej["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $ej[partition_by]($ej[partition])";if($fj)$O
.=" (".implode(",",$fj)."\n)";elseif($ej["partitions"])$O
.=" PARTITIONS ".(+$ej["partitions"]);}elseif($ej===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$B)$b[]="RENAME TO ".table($B);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$mb=array();foreach($b
as$W)$mb[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$mb));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mn){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Mn)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Mn,$qm){$mk=array();foreach($S
as$Q)$mk[]=table($Q)." TO ".idf_escape($qm).".".table($Q);if(!$mk||queries("RENAME TABLE ".implode(", ",$mk))){$Bc=array();foreach($Mn
as$Q)$Bc[table($Q)]=view($Q);connection()->select_db($qm);$k=idf_escape(DB);foreach($Bc
as$B=>$Ln){if(!queries("CREATE VIEW $B AS ".str_replace(" $k."," ",$Ln["select"]))||!queries("DROP VIEW $k.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$Mn,$qm){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$B=($qm==DB?table("copy_$Q"):idf_escape($qm).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($Q))||!queries("INSERT INTO $B SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){$Sm=$I["Trigger"];list($yd,$ai)=trigger_event($I);if(!queries("CREATE TRIGGER ".($qm==DB?idf_escape("copy_$Sm"):idf_escape($qm).".".idf_escape($Sm))." $I[Timing] $yd".($ai!=""?" $ai":"")." ON $B FOR EACH ROW\n$I[Statement];"))return
false;}}foreach($Mn
as$Q){$B=($qm==DB?table("copy_$Q"):idf_escape($qm).".".table($Q));$Ln=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Ln[select]"))return
false;}return
true;}function
trigger_event(array$I){$_d=explode(",",$I["Event"]);$H=array();foreach(array("DELETE","INSERT","UPDATE")as$yd){if(in_array($yd,$_d))$H[]=$yd;}$H=implode(" OR ",$H);if(in_array("UPDATE",$_d)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($I["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$ai))return
array("$H OF",$ai[1]);return
array($H,"");}function
trigger($B,$Q){if($B=="")return
array();$J=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));$H=reset($J);if($H)list($H["Event"],$H["Of"])=trigger_event($H);return($H?:array());}function
triggers($Q){$H=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){list($yd)=trigger_event($I);$H[$I["Trigger"]]=array($I["Timing"],$yd);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$T){$J=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$o=array();foreach($J
as$I){$ve=$I["DTD_IDENTIFIER"];list($cn,$y,$nn)=parse_type($ve);$o[]=array("field"=>$I["PARAMETER_NAME"],"type"=>$cn,"length"=>$y,"unsigned"=>$nn,"null"=>true,"full_type"=>$ve,"inout"=>($T=="FUNCTION"?"":$I["PARAMETER_MODE"]),"collation"=>$I["COLLATION_NAME"],);}$H=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($B))->fetch_assoc();if(!$H)return
array();$H['options']=array("DEFINER"=>$H['definer'],"DETERMINISTIC"=>$H['is_deterministic'],"SQL_DATA_ACCESS"=>$H['data_access'],"SQL_SECURITY"=>$H['security'],"COMMENT"=>$H['comment'],);if($o&&$o[0]['field']=='')$H['returns']=array_shift($o);$H['fields']=$o;return$H;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($yk){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($B,array$I){return
idf_escape($B);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$g,$F){return$g->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$F);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$Na,$Pl){$H=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$Na)$H=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$H);return$H;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($rc,$Pl=""){$B=idf_escape($rc);$H="";if(preg_match('~CREATE~',$Pl)&&($i=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($i);if($Pl=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $B;\n";$H
.="$i;\n";}return$H."USE $B";}function
trigger_sql($Q){$H="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$I){list($I["Event"],$I["Of"])=trigger_event($I);$H
.="\n".create_trigger(" ON ".table($I["Table"]),$I+array("Type"=>"FOR EACH ROW")).";\n";}return$H;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$n){return
driver()->convertColumn(idf_escape($n["field"]),$n);}function
unconvert_field(array$n,$H){if(preg_match("~binary~",$n["type"]))$H="UNHEX($H)";if($n["type"]=="bit")$H="CONVERT(b$H, UNSIGNED)";if($n["type"]=="vector")$H=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($H)";if(preg_match("~geom|point|linestring|polygon~",$n["type"])){$Dj=(min_version(8)?"ST_":"");$H=$Dj."GeomFromText($H, $Dj"."SRID($n[field]))";}return$H;}function
support($Pd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Pd);}function
kill_process($t){return
queries("KILL ".number($t));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Kd=false){return
array();}function
type_values($t){return"";}function
type_definition($t){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($K,$h=null){return
true;}function
schema_comment($K){return"";}function
set_schema_comment($K,$f){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Bm,$m="",$db=array(),$Cm="",$Th=false,$Sc=""){if($Th){header("HTTP/1.1 404 Not Found");$m=($m?:lang(96));}page_headers();if(is_ajax()&&$m){page_messages($m);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Dm=$Bm.($Cm!=""?": $Cm":"");$Em=strip_tags($Dm.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(97),'\' class=\'',lang(97),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Em,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.2+30b83850"),'">
';$jc=adminer()->css();if(is_int(key($jc)))$jc=array_fill_keys($jc,'light');$Pe=in_array('light',$jc)||in_array('',$jc);$Ne=in_array('dark',$jc)||in_array('',$jc);$nc=($Pe?($Ne?null:false):($Ne?:null));$bh=" media='(prefers-color-scheme: dark)'";if($nc!==false)echo"<link rel='stylesheet'".($nc?"":$bh)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.2+30b83850")."'>\n";echo"<meta name='color-scheme' content='".($nc===null?"light dark":($nc?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.2+30b83850");if(adminer()->head($nc))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+30b83850")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($jc
as$un=>$qh){$c=($qh=='dark'&&!$nc?$bh:($qh=='light'&&$Ne?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($un)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(98))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(99)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($db!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$M=adminer()->serverName(SERVER);$M=($M!=""?$M:lang(40));if($db===false)echo"$M\n";else{echo"<a href='".h($_.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$M</a> » ";$Ok="";if(is_string($db)){$Ok=$db;$db=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($db))){$tc="$_&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($tc.($_GET["ns"]==""?$Ok:"")).'">'.h(DB).'</a> » ';}if(is_array($db)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Ok).'">'.h($_GET["ns"]).'</a> » ';foreach($db
as$x=>$W){$Dc=(is_array($W)?$W[1]:h($W));if($Dc!="")echo"<a href='".h(ME."$x=").url_escape(is_array($W)?$W[0]:$W)."'>$Dc</a> » ";}}echo"$Bm\n";}}echo"<h2>$Dm$Sc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($m);adminer()->serviceWorker();$j=&get_session("dbs");if(DB!=""&&$j&&!in_array(DB,$j,true))$j=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Th){page_footer($Th===true?"":$Th);exit;}}function
service_worker(){$jk=has_passwords();$Ab=($jk?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.2+30b83850")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$Ab\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$kl){foreach($kl
as$Cn){foreach($Cn
as$D){if($D!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$ic){$Ue=array();foreach($ic
as$x=>$W)$Ue[]="$x $W";header("Content-Security-Policy: ".implode("; ",$Ue));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$_n=array();foreach(array_keys(adminer()->css())as$un)$_n[preg_replace('~\?.*~','',$un)]=true;$H=array();foreach(array("adminer.css","adminer-dark.css")as$p){if($_n[$p]&&file_exists($p)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($p),$A);$H[$p]=array((string)$A[1],Plugins::checksum($p));}}return$H;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'1b8ac749','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Sh;if(!$Sh)$Sh=base64_encode(rand_string());return$Sh;}function
page_messages($m){$tn=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$ih=idx($_SESSION["messages"],$tn);if($ih){echo"<div class='message'>".implode("</div>\n<div class='message'>",$ih)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$tn]);}if($m)echo"<div class='error'>$m</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($ph=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($ph);echo"</div>\n";if($ph!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(42),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(100),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($zh){while($zh>=2147483648)$zh-=4294967296;while($zh<=-2147483649)$zh+=4294967296;return(int)$zh;}function
long2str(array$V,$On){$Gk='';foreach($V
as$W)$Gk
.=pack('V',$W);if($On)return
substr($Gk,0,end($V));return$Gk;}function
str2long($Gk,$On){$V=array_values(unpack('V*',str_pad($Gk,4*ceil(strlen($Gk)/4),"\0")));if($On)$V[]=strlen($Gk);return$V;}function
xxtea_mx($Zn,$Yn,$Sl,$cg){return
int32((($Zn>>5&0x7FFFFFF)^$Yn<<2)+(($Yn>>3&0x1FFFFFFF)^$Zn<<4))^int32(($Sl^$Yn)+($cg^$Zn));}function
encrypt_string($Ml,$x){if($Ml=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$V=str2long($Ml,true);$zh=count($V)-1;$Zn=$V[$zh];$Yn=$V[0];$Tj=floor(6+52/($zh+1));$Sl=0;while($Tj-->0){$Sl=int32($Sl+0x9E3779B9);$dd=$Sl>>2&3;for($Ri=0;$Ri<$zh;$Ri++){$Yn=$V[$Ri+1];$yh=xxtea_mx($Zn,$Yn,$Sl,$x[$Ri&3^$dd]);$Zn=int32($V[$Ri]+$yh);$V[$Ri]=$Zn;}$Yn=$V[0];$yh=xxtea_mx($Zn,$Yn,$Sl,$x[$Ri&3^$dd]);$Zn=int32($V[$zh]+$yh);$V[$zh]=$Zn;}return
long2str($V,false);}function
decrypt_string($Ml,$x){if($Ml=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$V=str2long($Ml,false);$zh=count($V)-1;$Zn=$V[$zh];$Yn=$V[0];$Tj=floor(6+52/($zh+1));$Sl=int32($Tj*0x9E3779B9);while($Sl){$dd=$Sl>>2&3;for($Ri=$zh;$Ri>0;$Ri--){$Zn=$V[$Ri-1];$yh=xxtea_mx($Zn,$Yn,$Sl,$x[$Ri&3^$dd]);$Yn=int32($V[$Ri]-$yh);$V[$Ri]=$Yn;}$Zn=$V[$zh];$yh=xxtea_mx($Zn,$Yn,$Sl,$x[$Ri&3^$dd]);$Yn=int32($V[0]-$yh);$V[0]=$Yn;$Sl=int32($Sl-0x9E3779B9);}return
long2str($V,true);}$qj=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($x)=explode(":",$W);$qj[$x]=$W;}}function
add_invalid_login(){$Va=get_temp_dir()."/adminer-invalid";foreach(glob("$Va*")?:array($Va)as$p){$pe=file_open_lock($p);if($pe)break;}if(!$pe)$pe=file_open_lock("$Va-".rand_string());if(!$pe)return;$Of=json_decode(stream_get_contents($pe),true);$zm=time();if($Of){foreach($Of
as$Pf=>$W){if($W[0]<$zm)unset($Of[$Pf]);}}$Mf=&$Of[adminer()->bruteForceKey()];if(!$Mf)$Mf=array($zm+30*60,0);$Mf[1]++;file_write_unlock($pe,json_encode($Of));}function
check_invalid_login(array&$qj){$Of=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$p){$pe=file_open_lock($p);if($pe){$Of=json_decode(stream_get_contents($pe),true);file_unlock($pe);break;}}$x=adminer()->bruteForceKey();$Mf=idx($Of,$x,array());$Rh=($Mf[1]>29?$Mf[0]-time():0);if($Rh>0){$m=lang(101,ceil($Rh/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$x==$_SERVER["REMOTE_ADDR"])$m
.='<br>'.lang(102,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($m,$qj,false);}}function
password_required(){static$H;if($H===null){$H=(bool)get_session("password_required");if(!$H){$hc=adminer()->credentials();$H=!is_object(Driver::connect($hc[0],$hc[1],""));if($H)set_session("password_required",true);}}return$H;}function
require_password_link($D){$uh="<a href='https://www.adminer.org/password/'".target_blank().">".lang(103)."</a>";if(!function_exists('password_hash'))return" $uh";$tj=($D!==null?$D:base64_encode(substr(pack("H*",rand_string()),0,12)));$Te=password_hash($tj,PASSWORD_DEFAULT);$p="adminer-plugins.php";$Ed=file_exists("adminer-plugins.php");if($Ed)$Kf=($D!==null?lang(104,"<b>$p</b>"):lang(105,"<b>$p</b>","<b>$tj</b>"));else{$p="<button name='password_less' value='".h($Te)."' class='link'>$p</button>";$Kf=($D!==null?lang(106,$p):lang(107,$p,"<b>$tj</b>"));}$Ag="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Te)."'</span>),";$H="<p>$Kf
<pre><code class='jush'>".($Ed?$Ag:"&lt;?php\n<a>return</a> <a>array</a>(\n$Ag\n);")."</code></pre>
<p>$uh
";return" <a href='#password-less' class='toggle'>".lang(108)."</a>
<div id='password-less' class='hidden'>".($Ed?$H:"<form action='' method='post'>\n".$H.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ma=$_POST["auth"];if($Ma&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Jn=$Ma["driver"];$M=$Ma["server"];$U=$Ma["username"];$D=(string)$Ma["password"];$k=$Ma["db"];set_password($Jn,$M,$U,$D);$_SESSION["db"][$Jn][$M][$U][$k]=true;if($Ma["permanent"]){$x=implode("-",array_map('base64_encode',array($Jn,$M,$U,$k)));$Lj=adminer()->permanentLogin(true);$qj[$x]="$x:".base64_encode($Lj?encrypt_string($D,$Lj):"");cookie("adminer_permanent",implode(" ",$qj));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Jn!=DRIVER||$M!=SERVER||$U!==$_GET["username"]||$k!=DB)redirect(auth_url($Jn,$M,$U,$k));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($qj);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(109).' '.lang(110));}elseif($qj&&!$_SESSION["pwds"]){session_regenerate_id();$Lj=adminer()->permanentLogin();foreach($qj
as$x=>$W){list(,$yb)=explode(":",$W);list($Jn,$M,$U,$k)=array_map('base64_decode',explode("-",$x));set_password($Jn,$M,$U,decrypt_string(base64_decode($yb),$Lj));$_SESSION["db"][$Jn][$M][$U][$k]=true;}}function
unset_permanent(array&$qj){foreach($qj
as$x=>$W){list($Jn,$M,$U,$k)=array_map('base64_decode',explode("-",$x));if($Jn==DRIVER&&$M==SERVER&&$U==$_GET["username"]&&$k==DB)unset($qj[$x]);}cookie("adminer_permanent",implode(" ",$qj));}function
auth_error($m,array&$qj,$Nf=true){$ll=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$ll]||$_GET[$ll])&&!$_SESSION["token"])$m=lang(111);elseif($Nf&&($D=get_password())!==null){restart_session();add_invalid_login();if($D===false)$m
.=($m?'<br>':'').lang(112,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($qj);}}if(!$_COOKIE[$ll]&&$_GET[$ll]&&ini_bool("session.use_only_cookies"))$m=lang(113);$Wi=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Wi["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(45),$m,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(114)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($qj);page_header(lang(115),lang(116,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$g='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($qj);$hc=adminer()->credentials();$g=Driver::connect($hc[0],$hc[1],$hc[2]);if(is_object($g)){Db::$instance=$g;Driver::$instance=new
Driver($g);if($g->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Hg=null;if(!is_object($g)||($Hg=adminer()->login($_GET["username"],get_password()))!==true){$m=(is_string($g)?nl_br(h($g)):(is_string($Hg)?$Hg:lang(117))).(preg_match('~^ | $~',get_password())?'<br>'.lang(118):'');auth_error($m,$qj);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(100),lang(119));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Ma&&$_POST["token"])$_POST["token"]=get_token();$m='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$m=lang(119).' '.lang(120);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$m=lang(121,"<b>post_max_size</b>");if(isset($_GET["sql"]))$m
.=' '.lang(122);}function
print_select_result($G,$h=null,array$Ei=array(),&$z=0,&$ed=false){$Cg=array();$w=array();$e=array();$S=array();$Ij=array();$id=array();$dn=array();$H=array();$rh=$ed;$ed=false;for($s=0;(!$z||$s<$z)&&($I=$G->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($rh?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($Zf=0;$Zf<count($I);$Zf++){$n=$G->fetch_field();$B=$n->name;$Q=(isset($n->table)?$n->table:"");$Di=(isset($n->orgtable)?$n->orgtable:"");$Ci=(isset($n->orgname)?$n->orgname:$B);$cn=driver()->typeName($n);if($Ei&&JUSH=="sql")$Cg[$Zf]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($Di!=""){$wa=($Q!=""?$Q:$Di);if($Q!="")$H[$Q]=$Di;if(!isset($w[$wa])){if(!isset($Ij[$Di])){$Ij[$Di]=array();foreach(indexes($Di,$h)as$v){if($v["type"]=="PRIMARY"){$Ij[$Di]=array_flip($v["columns"]);break;}}}$S[$wa]=$Di;$w[$wa]=$Ij[$Di];$e[$wa]=$Ij[$Di];}if(isset($e[$wa][$Ci])){unset($e[$wa][$Ci]);$w[$wa][$Ci]=$Zf;$Cg[$Zf]=$wa;}elseif($rh&&isset($n->orgname)&&$n->db==DB&&!is_blob(array("type"=>$cn)))$id[$Zf]=array($wa,$Ci,preg_match('~text|json|lob~',$cn));}$dn[$Zf]=$cn;echo"<th title='".h(trim(($Di!=""?"$Di.$Ci":($n->name!=$Ci?$Ci:""))." ".$cn))."'>".h($B).($Ei?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}foreach($id
as$Zf=>$kb){if($e[$kb[0]])unset($id[$Zf]);}echo"<tbody>\n";}$mf=array();foreach($w
as$wa=>$v){if($v&&!$e[$wa]){$u="";foreach($v
as$Bb=>$Zf){if($I[$Zf]===null){$u=null;break;}$u
.="&where[".url_escape(bracket_escape($Bb))."]=".url_escape($I[$Zf]);}$mf[$wa]=$u;}}echo"<tr>";foreach($I
as$x=>$W){$_="";if(isset($Cg[$x])){if($Ei&&JUSH=="sql"){$Q=$I[array_search("table=",$Cg)];$_=ME.$Cg[$x].url_escape($Ei[$Q]!=""?$Ei[$Q]:$Q);}elseif(idx($mf,$Cg[$x])!==null)$_=ME."edit=".url_escape($S[$Cg[$x]]).$mf[$Cg[$x]];}$hf=select_value($W,$_,array('type'=>(preg_match('~binary~',$dn[$x])?'blob':$dn[$x])),null);$c="";$kb=idx($id,$x);if($kb&&idx($mf,$kb[0])!==null&&is_utf8($W)){$ed=true;$c=" data-name='".h("val[".bracket_escape($S[$kb[0]])."][".bracket_escape(substr($mf[$kb[0]],1))."][".bracket_escape($kb[1])."]")."' data-text='".($kb[2]?1:0)."'".(html_entity_decode(strip_tags($hf),ENT_QUOTES,"UTF-8")!==$W?" data-value='".h($W)."'":"");}echo"<td".(preg_match(number_type(),$dn[$x])?" class='number'":"")."$c>$hf";}}$z=$s;echo($s?"</table>\n</div>":"<p class='message'>".lang(16))."\n";return$H;}function
textarea($B,$X,$J=10,$Fb=80,$bg=JUSH){echo"<textarea name='".h($B)."' rows='$J' cols='$Fb' class='sqlarea jush-".h($bg)."' spellcheck='false' wrap='off'>";if(is_array($X)){foreach($X
as$W)echo
h($W[0])."\n\n\n";}else
echo
h($X);echo"</textarea>";}function
select_input($c,array$yi,$X="",$rj=""){if($yi&&$X!=""&&!isset($yi[$X]))$yi=array($X=>$X)+$yi;$pm=($yi?"select":"input");return"<$pm$c".($yi?"><option value=''>$rj".optionlist($yi,$X,true)."</select>":" size='10' value='".h($X)."' placeholder='$rj'>");}function
json_row($x,$W=null,$xd=true){static$be=true;if($be)echo"{";if($x!=""){echo($be?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($W!==null?($xd?'"'.addcslashes($W,"\r\n\"\\/").'"':$W):'null');$be=false;}else{echo"\n}\n";$be=true;}}function
flat_collations(){$Eb=collations();return(is_array(reset($Eb))?call_user_func_array('array_merge',array_values($Eb)):$Eb);}function
edit_type($x,array$n,array$Eb,array$le=array(),array$Md=array()){$T=(string)$n["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($T&&!array_key_exists($T,driver()->types())&&!isset($le[$T])&&!in_array($T,$Md))$Md[]=$T;$Nl=driver()->structuredTypes();if($le)$Nl[lang(123)]=$le;echo
optionlist(array_merge($Md,$Nl),$T),"</select><td>","<input name='".h($x)."[length]' value='".h($n["length"])."' size='3'".(!$n["length"]&&preg_match('~var(char|binary)$~',$T)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($Eb?"<input list='collations' name='".h($x)."[collation]'".option_types($T,'('.text_type().')$')." value='".h($n["collation"])."' placeholder='(".lang(124).")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".option_types($T,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$n["unsigned"]).'</select>':''),(isset($n['on_update'])?"<select name='".h($x)."[on_update]'".option_types($T,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(125).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?"CURRENT_TIMESTAMP":$n["on_update"])).'</select>':''),($le?"<select name='".h($x)."[on_delete]'".option_types($T,'`')."><option value=''>(".lang(126).")".optionlist(explode("|",driver()->onActions),$n["on_delete"])."</select> ":" ");}function
option_types($T,$dn){return" data-types='".h($dn)."'".(preg_match("~$dn~",$T)?"":" class='hidden'");}function
process_length($y){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$y))return"(max)";$sd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$sd(?:\\s*,\\s*$sd)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$sd~",$y,$Mg)?"(".implode(",",$Mg[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_in($W){$W=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$W);$Xj="'((?:[^']|'')*+)'";$Yf=(preg_match("~^\\s*$Xj(?:\\s*,\\s*$Xj)*+\\s*\$~",$W)&&preg_match_all("~$Xj~",$W,$Mg)?str_replace("''","'",$Mg[1]):array_map('trim',explode(",",$W)));return"(".implode(", ",array_map('Adminer\q',$Yf)).")";}function
process_between($W){$Xj="'((?:[^']|'')*+)'";$Zj=(preg_match("~^\\s*$Xj\\s+AND\\s+$Xj\\s*\$~i",$W,$A)?array(str_replace("''","'",$A[1]),str_replace("''","'",$A[2])):preg_split('~\s+AND\s+~i',trim($W)));return(count($Zj)==2?q($Zj[0])." AND ".q($Zj[1]):"NULL AND NULL");}function
process_type(array$n,$Cb="COLLATE"){return" ".(is_user_type($n["type"])?idf_escape($n["type"]):$n["type"]).process_length($n["length"]).(preg_match(number_type(),$n["type"])&&in_array($n["unsigned"],driver()->unsigned)?" $n[unsigned]":"").(preg_match('~'.text_type().'~',$n["type"])&&$n["collation"]?" $Cb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$n["collation"])?$n["collation"]:q($n["collation"])):"");}function
process_field(array$n,array$Zm){if($n["on_update"])$n["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$n["on_update"]);return
array(idf_escape(trim($n["field"])),process_type($Zm),($n["null"]?" NULL":" NOT NULL"),default_value($n),(preg_match('~timestamp|datetime~',$n["type"])&&$n["on_update"]?" ON UPDATE $n[on_update]":""),(support("comment")&&$n["comment"]!=""?" COMMENT ".q($n["comment"]):""),($n["auto_increment"]?auto_increment():null),);}function
default_value(array$n){if($n["default"]===null)return"";$l=str_replace("\r","",$n["default"]);$_e=$n["generated"];$P=!preg_match('~]$~',$n["length"])&&(preg_match('~char|binary|text|json|enum|set|String~',$n["type"])||driver()->enumLength($n));return(in_array($_e,driver()->generated)?(JUSH=="mssql"?" AS ($l)".($_e=="VIRTUAL"?"":" $_e"):" GENERATED ALWAYS AS ($l) $_e"):(preg_match('~^GENERATED ~i',$l)?" $l":" DEFAULT ".($P||preg_match('~^(?![a-z])~i',$l)?(JUSH=="sql"&&preg_match('~text|json~',$n["type"])?"(".q($l).")":q($l)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($l)":$l)))));}function
edit_fields(array$o,array$Eb,$T="TABLE",array$le=array()){$o=array_values($o);$yc=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$Kb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($T=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($T=="TABLE"?lang(127):lang(128)),"<th id='label-type'>".lang(58)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(129),"<th>".lang(130);if($T=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(60)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'cockroach'=>"serial",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<th id='label-default'$yc>".lang(61),(support("comment")?"<th id='label-comment'$Kb>".lang(59):"");$qg=!support("move_col");echo"<td>".icon("plus","add[".($qg?count($o):0)."]","+",lang(131),($qg?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($o
as$s=>$n){$s++;$Fi=$n[($_POST?"orig":"field")];$Kc=(isset($_POST["add"][$s-1])||(isset($n["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$Fi=="");echo"<tr".($Kc?"":" hidden").">\n",($T=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$n["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(132))." ":"");if($Kc)echo"<input name='fields[$s][field]' value='".h($n["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$s-1])?" autofocus":"").">";echo
input_hidden("fields[$s][orig]",$Fi);edit_type("fields[$s]",$n,$Eb,$le);if($T=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$s][null]",1,$n["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($n["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$yc>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$n["generated"])." ":checkbox("fields[$s][generated]",1,$n["generated"],"","","","label-default"));$c=" name='fields[$s][default]' aria-labelledby='label-default'";$X=h($n["default"]);echo(preg_match('~\n~',$n["default"])?"<textarea$c rows='2' cols='30' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");if(support("comment")){$c=" name='fields[$s][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$Kb>".adminer()->commentInput('COLUMN',$c,$n["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$s]","+",lang(131))." ":""),($Fi==""||support("drop_col")?icon("cross","drop_col[$s]","x",lang(133)):"");}}function
process_fields(array&$o){if($_POST["add"]){$o=array_values($o);array_splice($o,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Xc,$i,$Zc,$vm,$bd,$Gg,$hh,$fh,$gh,$ki,$Lh){if($_POST["drop"])query_redirect($Xc,$Gg,$hh);elseif($ki=="")query_redirect($i,$Gg,$gh);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($Gg,$fh,queries($Xc)&&queries($i)&&driver()->commit());driver()->rollback();}elseif($ki!=$Lh){$gc=queries($i);queries_redirect($Gg,$fh,$gc&&queries($Xc));if($gc&&$Zc)queries($Zc);}else
queries_redirect($Gg,$fh,queries($vm)&&queries($bd)&&queries($Xc)&&queries($i));}function
create_trigger($oi,array$I){$Am=" $I[Timing] $I[Event]".(preg_match('~ OF~',$I["Event"])?" $I[Of]":"");return"CREATE TRIGGER ".idf_escape($I["Trigger"]).(JUSH=="mssql"?$oi.$Am:$Am.$oi).preg_replace('~[\s;]+$~',''," $I[Type]\n$I[Statement]").";";}function
q_dollar($P){$Cc='$$';while(strpos($P.$Cc,$Cc)!=strlen($P))$Cc='$_'.substr($Cc,1);return$Cc.$P.$Cc;}function
routine_collate($Db){static$pb=array();if($Db&&!$pb){foreach(collations()as$ob=>$Hn){foreach((array)$Hn
as$W)$pb[$W]=$ob;}}return($pb[$Db]?"CHARACTER SET ".q($pb[$Db])." ":"")."COLLATE";}function
create_routine($yk,array$I){$N=array();$o=$I["fields"];ksort($o);foreach($o
as$n){if($n["field"]!=""){$Ff=(preg_match("~^(".driver()->inout.")\$~",$n["inout"])?$n["inout"]:"");$N[]="\n  ".(JUSH=="mssql"?"@$n[field]".process_type($n).($Ff?" $Ff":""):($Ff?"$Ff ":"").idf_escape($n["field"]).process_type($n,routine_collate($n["collation"])));}}$_c="";$yi=array();foreach(routine_options($yk)as$x=>$Y){$X=idx($I["options"],$x,"");if($x=="DEFINER")$_c=($X?" $x=".implode("@",array_map('Adminer\q',explode("@",$X,2))):"");elseif(!$Y){if($X!="")$yi[]="$x ".q($X);}elseif($X!=reset($Y)&&in_array($X,$Y))$yi[]=$X;}$og=$I["language"];$Ac=preg_replace('~[\s;]+$~','',$I["definition"]);$Tc=(JUSH=="pgsql"||($og&&$og!="sql"));$Vi=($N?implode(",",$N)."\n":"");return"CREATE$_c $yk ".table(trim($I["name"])).(JUSH=="mssql"&&$yk=="PROCEDURE"?rtrim($Vi):" ($Vi)").($yk=="FUNCTION"?"\nRETURNS".process_type($I["returns"],routine_collate($I["returns"]["collation"])):"").($og?" LANGUAGE $og":"").($yi?"\n".implode(" ",$yi):"").($Tc?" AS ".q_dollar("\n".trim($Ac)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$Ac;");}function
remove_definer($F){$_c=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($_c).')','\1',$F);}function
object_name($T,$Q,array$e){return
str_replace(array("{table}","{columns}"),array($Q,implode("_",$e)),adminer()->namePattern($T));}function
format_foreign_key(array$q,$B=""){$k=$q["db"];$Uh=$q["ns"];return($B!=""?" CONSTRAINT ".idf_escape($B):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$q["source"])).") REFERENCES ".($k!=""&&$k!=$_GET["db"]?idf_escape($k).".":"").($Uh!=""&&$Uh!=$_GET["ns"]?idf_escape($Uh).".":"").idf_escape($q["table"])." (".implode(", ",array_map('Adminer\idf_escape',$q["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$q["on_delete"])?" ON DELETE $q[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$q["on_update"])?" ON UPDATE $q[on_update]":"").($q["deferrable"]?" $q[deferrable]":"");}function
tar_file($p,$Fm){$H=pack("a100a8a8a8a12a12",$p,644,0,0,decoct($Fm->size),decoct(time()));$vb=8*32;for($s=0;$s<strlen($H);$s++)$vb+=ord($H[$s]);$H
.=sprintf("%06o",$vb)."\0 ";echo$H,str_repeat("\0",512-strlen($H));$Fm->send();echo
str_repeat("\0",511-($Fm->size+511)%512);}function
doc_version(){$jl=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$jl,$A);return($A[1]>=18?$A[1]:"19");}$ik=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$Kn=(preg_match($ik,$jl,$A)?$A[0]:"");if(JUSH=='mssql')return($Kn>=15?"sql-server-ver$Kn":($Kn==12?"azuresqldb-current":"sql-server-2017"));return$Kn;}function
doc_link(array$mj,$wm="📖"){$Kn=doc_version();$vn=array('sql'=>"https://dev.mysql.com/doc/refman/$Kn/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Kn)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Kn/",);if(connection()->flavor=='maria'){$vn['sql']="https://mariadb.com/kb/en/";$mj['sql']=($mj['mariadb']?:str_replace(".html","/",$mj['sql']));}if(connection()->flavor=='cockroach'&&$mj['cockroach']){$vn['pgsql']="https://docs.cockroachlabs.com/docs/v$Kn/";$mj['pgsql']=$mj['cockroach'];}return($mj[JUSH]?" <a href='".h($vn[JUSH].$mj[JUSH].(JUSH=='mssql'?"?view=$Kn":""))."'".target_blank()." class='doc' title='".lang(134)."'>$wm</a>":"");}function
db_size($k){if(!connection()->select_db($k))return"?";$H=0;foreach(table_status()as$R)$H+=$R["Data_length"]+$R["Index_length"];return
adminer()->formatSizeValue($H);}function
set_utf8mb4($i){static$N=false;if(!$N&&preg_match('~\butf8mb4~i',$i)){$N=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(44).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$j=adminer()->databases();if($j)redirect(ME."db=".url_escape($j[0]));}if($_POST["db"]&&!$m)queries_redirect(substr(ME,0,-1),lang(135),drop_databases($_POST["db"]));page_header(lang(136),$m,false);echo"<p class='links'>\n";foreach(array('database'=>lang(137),'privileges'=>lang(80),'processlist'=>lang(138),'variables'=>lang(139),'status'=>lang(140),)as$x=>$W){if(support($x))echo"<a href='".h(ME)."$x='>$W</a>\n";}echo"<p>".lang(141,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(142,"<b>".h(logged_user())."</b>")."\n";$j=adminer()->databases();if($j){$Kk=support("scheme");$Eb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(44).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(143)."</a>":"")."<th>".lang(144)."<th>".lang(145)."<th>".lang(146)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(147)."</a>"."<tbody>\n";$j=($_GET["dbsize"]?count_tables($j):array_flip($j));foreach($j
as$k=>$S){$xk=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($k);$t=h("Db-".$k);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$k,in_array($k,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$xk' id='$t'>".h($k)."</a>";$Db=h(db_collation($k,$Eb));echo"<td>".(support("database")?"<a href='$xk".($Kk?"&amp;ns=":"")."&amp;database=' title='".lang(76)."'>$Db</a>":$Db),"<td align='right'><a href='$xk&amp;schema=' id='tables-".h($k)."' title='".lang(79)."'>".($_GET["dbsize"]?format_number($S):"?")."</a>","<td align='right' id='size-".h($k)."'>".($_GET["dbsize"]?db_size($k):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(148)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(149)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$qa=adminer();$vj=($qa
instanceof
Plugins?$qa->plugins:array());$Wc=($qa
instanceof
Plugins?$qa->drivers:array());$Hc=design_checksums();if($vj||$Wc||$Hc){$wb=($qa
instanceof
Plugins?$qa->checksums():array());$ci=Plugins::officialChecksums();$qn=function($un){return" (<a href='$un'".target_blank()." class='update'>".VERSION."</a>)";};$uj=function($Vd)use($wb,$ci,$qn){return($wb[$Vd]&&$ci[$Vd]&&$wb[$Vd]!==$ci[$Vd]?$qn("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(150)."</h3>\n<ul>\n";foreach($vj
as$sj){$gk=new
\ReflectionObject($sj);$Ec=(method_exists($sj,'description')?$sj->description():"");if(!$Ec){if(preg_match('~^/[\s*]+(.+)~',$gk->getDocComment(),$A))$Ec=$A[1];}$Lk=(method_exists($sj,'screenshot')?$sj->screenshot():"");echo"<li><b>".get_class($sj)."</b>".h($Ec?": $Ec":"").($Lk?" (<a href='".h($Lk)."'".target_blank().">".lang(151)."</a>)":"").$uj(basename((string)$gk->getFileName(),'.php'))."\n";}foreach($Wc
as$t=>$B)echo"<li><b>".h($t)."</b>: ".h($B).$uj(basename((string)$qa->driverFiles[$t],'.php'))."\n";if($Hc){$ei=official_design_checksums();foreach($Hc
as$p=>$Gc){list($B,$vb)=$Gc;$di=$ei["$B/$p"];echo"<li><b>".h($p)."</b>".h($B?": $B":"").($di&&$di!==$vb?$qn("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~&db=[^&]+~','\0&ns='.url_escape(get_schema()),relative_uri()));if(!set_schema($_GET["ns"]))page_header(lang(88).h(": $_GET[ns]"),adminer()->error(),true,"","ns");}}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Xb){$this->size+=strlen($Xb);fwrite($this->handler,$Xb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$o=fields($a);header("Content-Type: application/octet-stream");$Y=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Y)).".".friendly_url($_GET["field"]));$L=array(idf_escape($_GET["field"]));$G=driver()->select($a,$L,array(where($_GET,$o)),$L);$I=($G?$G->fetch_row():array());echo
driver()->value($I[0],$o[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$o=fields($a);if(!$o)$m=adminer()->error();$R=table_status1($a);$B=adminer()->tableName($R);$m=$m?:h($R["Error"]);page_header(($o&&is_view($R)?$R['Engine']=='materialized view'?lang(152):lang(153):lang(154)).": ".($B!=""?$B:h($a)),$m,array(),"",!$o,($o?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$wk=array();foreach($o
as$x=>$n)$wk+=$n["privileges"];adminer()->selectLinks($R,(isset($wk["insert"])||!support("table")?"":null));$f=$R["Comment"];if($f!="")echo"<p class='nowrap'>".lang(59).": ".adminer()->commentValue('TABLE',$f)."\n";if($o)adminer()->tableStructurePrint($o,$R);function
tables_links(array$S){echo"<ul>\n";foreach($S
as$I){$_=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($I["ns"]),ME);echo"<li><a href='".h($_."table=".url_escape($I["table"]))."'>".($I["ns"]!=$_GET["ns"]?"<b>".h($I["ns"])."</b>.":"").h($I["table"])."</a>";}echo"</ul>\n";}$Df=driver()->inheritsFrom($a);if($Df){echo"<h3>".lang(155)."</h3>\n";tables_links($Df);}if(support("indexes")&&driver()->supportsIndex($R)){echo"<div>\n","<h3 id='indexes'>".lang(156)."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w,$R);if(driver()->supportsAlterIndex($R))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(157)."</a>\n";echo"</div>\n";}if(!is_view($R)&&driver()->supportsAlterTable($R)){if(fk_support($R)){echo"<div>\n","<h3 id='foreign-keys'>".lang(123)."</h3>\n";$le=foreign_keys($a);if($le){echo"<table>\n","<thead><tr><th>".lang(158)."<th>".lang(159)."<th>".lang(126)."<th>".lang(125)."<td class='hover'><tbody>\n";foreach($le
as$B=>$q){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$q["source"]))."</i>";$_=($q["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($q["db"]),ME):($q["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($q["ns"]),ME):ME));echo"<td><a href='".h($_."table=".url_escape($q["table"]))."'>".($q["db"]!=""&&$q["db"]!=DB?"<b>".h($q["db"])."</b>.":"").($q["ns"]!=""&&$q["ns"]!=$_GET["ns"]?"<b>".h($q["ns"])."</b>.":"").h($q["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$q["target"]))."</i>)","<td>".h($q["on_delete"]),"<td>".h($q["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.lang(160).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(161)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(162)."</h3>\n";$rb=driver()->checkConstraints($a);if($rb){echo"<table>\n";foreach($rb
as$x=>$W)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($W)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($x))."'>".lang(160)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(163)."</a>\n","</div>\n";}}if(support(is_view($R)?"view_trigger":"trigger")&&driver()->supportsAlterTable($R)){echo"<div>\n","<h3 id='triggers'>".lang(164)."</h3>\n";$Wm=triggers($a);if($Wm){echo"<table>\n";foreach($Wm
as$x=>$W){echo"<tr valign='top'><td>".h($W[0])."<td>".h($W[1])."<th>".h($x)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($x))."'>".lang(160)."</a>";$yk=$W[2];if($yk){$_k=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($yk["ns"]),ME).'function='.url_escape($yk["function"]).'&name='.url_escape($yk["name"]);echo", <a href='".h($_k)."' title='".h($yk["name"])."'>".lang(165)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(166)."</a>\n","</div>\n";}$ol=driver()->shadowTables($a);if($ol){echo"<h3 id='shadow-tables'>".lang(167)."</h3>\n";tables_links($ol);}$Cf=driver()->inheritedTables($a);if($Cf){echo"<h3 id='partitions'>".lang(168)."</h3>\n";$aj=driver()->partitionsInfo($a);if($aj)echo"<p><code class='jush-".JUSH."'>BY ".h("$aj[partition_by]($aj[partition])")."</code>\n";tables_links($Cf);}}elseif(isset($_GET["schema"])){page_header(lang(79),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($Q,array$fk,array&$e){if(!isset($e[$Q])){$e[$Q]=0;foreach((array)idx($fk,$Q)as$B=>$hk){if($B!=$Q)$e[$Q]=max($e[$Q],schema_column($B,$fk,$e)+1);}}return$e[$Q];}function
type_class($T){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$W){if(preg_match("~$x|$W~",$T))return" class='$x'";}}$gm=array();$im=array();$hm=array();$Sd=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Mg,PREG_SET_ORDER);foreach($Mg
as$s=>$A){$gm[$A[1]]=array((float)$A[2],(float)$A[3]);$im[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$K=array();$fk=array();$le=array();$za=driver()->allFields();$Ze=array();$jm=array();foreach(table_status('',true)as$Q=>$R){if(!is_view($R)){if(adminer()->tableName($R)!=""&&!$R["dependent"])$jm[$Q]=$R;else$Ze[$Q]=true;}}foreach($jm
as$Q=>$R){$E=0;$K[$Q]["fields"]=array();foreach($za[$Q]as$n){$E+=1.25;$Sd[$Q][$n["field"]]=$E;$K[$Q]["fields"][$n["field"]]=$n;}foreach(adminer()->foreignKeys($Q)as$W){if($W["db"]==""&&$W["ns"]==""&&!$Ze[$W["table"]]){$le[$Q][]=$W;$fk[$W["table"]][$Q]=array();}}}$e=array();$De=array();$Xn=array();$Je=array();foreach(array_keys($K)as$B)schema_column($B,$fk,$e);arsort($e);foreach($e
as$B=>$d){$nh=null;foreach((array)idx($le,$B)as$W){if($W["table"]!=$B&&$K[$W["table"]])$nh=($nh===null?$e[$W["table"]]:min($nh,$e[$W["table"]]));}$e[$B]=max($d,(int)$nh-1);}foreach($K
as$B=>$Q){$d=$e[$B];$De[$d][]=$B;$ym=.75*strlen($B);foreach($Q["fields"]as$n)$ym=max($ym,.65*strlen($n["field"]));$Xn[$d]=max(idx($Xn,$d,0),ceil($ym)+1);}foreach($le
as$B=>$Hn){foreach($Hn
as$W){$Ie=$e[$B]+(idx($e,$W["table"],$e[$B])>$e[$B]?1:0);$Je[$Ie]=idx($Je,$Ie,0)+1;}}ksort($De);$Xe=0;$Wn=0;$Gb=0;$Gj=null;$cm=array();$lm=array();foreach($De
as$d=>$S){if($Gj!==null){$Gb=round($Gb+$Xn[$Gj]+1.7+idx($Je,$d,0)*.1,1);$Ai=array();foreach($S
as$B){$Sl=0;$ec=0;$Gh=array_keys((array)idx($fk,$B));foreach((array)idx($le,$B)as$W)$Gh[]=$W["table"];foreach($Gh
as$_h){if($K[$_h]&&$e[$_h]<$d){$Sl+=$K[$_h]["pos"][0];$ec++;}}$Ai[$B]=($ec?$Sl/$ec:$Xe);}asort($Ai);$S=array_keys($Ai);}$Im=0;foreach($S
as$B){$E=1.25*count($K[$B]["fields"]);$K[$B]["pos"]=($gm[$B]?:array($Im,$Gb));$cm[$B]=$K[$B]["pos"][1];$lm[$B]=$Xn[$d];$Im+=2.5+$E;$Xe=max($Xe,$K[$B]["pos"][0]+2.5+$E);$Wn=max($Wn,round($K[$B]["pos"][1]+$Xn[$d],1));if(!$gm[$B])$hm[]="\n\t'".js_escape($B)."': [ ".$K[$B]["pos"][0].", ".$K[$B]["pos"][1]." ]";}$Gj=$d;}$ug=array();$Wa=array();foreach($le
as$B=>$Hn){foreach($Hn
as$W){$rm=idx($cm,$W["table"],$cm[$B]);$zl=$cm[$B]+$lm[$B];$vk=($rm-1>$zl);$sg=($vk?$zl+1:min($cm[$B],$rm)-1);$Va=idx($Wa,(string)$sg,0);$Wa[(string)$sg]=$Va+1;$sg=round($vk?min($sg+$Va*.1,$rm-1):$sg-$Va*.1,1);while($ug[(string)$sg])$sg-=.0001;$K[$B]["references"][$W["table"]][(string)$sg]=array($W["source"],$W["target"]);$fk[$W["table"]][$B][(string)$sg]=$W["target"];$ug[(string)$sg]=true;}}echo'<div id="schema" style="height: ',$Xe,'em; width: ',$Wn,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$im)."\n",'};
const tablePosDefault = {',implode(",",$hm)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$Xe,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($K
as$B=>$Q){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$Q["pos"][0]."em; left: ".$Q["pos"][1]."em; width: ".$lm[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($Q["fields"]as$n){$W='<span'.type_class($n["type"]).' title="'.h($n["type"].($n["length"]?"($n[length])":"").($n["null"]?" NULL":'')).'">'.h($n["field"]).'</span>';echo"<br>".($n["primary"]?"<i>$W</i>":$W);}foreach((array)$Q["references"]as$sm=>$hk){foreach($hk
as$sg=>$ck){$tg=$sg-$Q["pos"][1];$Pl=($tg>0?"left: 100%; width: calc($tg"."em - 100%)":"left: $tg"."em");$Wn=($tg>0?"100%":(-$tg)."em");$s=0;foreach($ck[0]as$yl)echo"\n<div class='references' title='".h($sm)."' id='refs$sg-".($s++)."' style='$Pl"."; top: ".$Sd[$B][$yl]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Wn;'></div></div>";}}foreach((array)$fk[$B]as$sm=>$hk){foreach($hk
as$sg=>$tm){$tg=$sg-$Q["pos"][1];$s=0;foreach($tm
as$qm)echo"\n<div class='references arrow' title='".h($sm)."' id='refd$sg-".($s++)."' style='left: $tg"."em; top: ".$Sd[$B][$qm]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$tg)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($K
as$B=>$Q){foreach((array)$Q["references"]as$sm=>$hk){if($K[$sm]){foreach($hk
as$sg=>$ck){$oh=$Xe;$Ug=-10;foreach($ck[0]as$x=>$yl){$xj=$Q["pos"][0]+$Sd[$B][$yl];$yj=$K[$sm]["pos"][0]+$Sd[$sm][$ck[1][$x]];$oh=min($oh,$xj,$yj);$Ug=max($Ug,$xj,$yj);}echo"<div class='references' id='refl$sg' style='left: $sg"."em; top: $oh"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Ug-$oh)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(169),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$m){$l=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Ul){if(support($Ul))$l[$Ul."s"]='';}save_settings(array_intersect_key($_POST+$l,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$l),"adminer_export");$ya=(DB==""||$_GET["ns"]==="");$S=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Id=dump_headers((count($S)==1?key($S):DB),($ya||count($S)>1));$Uf=preg_match('~sql~',$_POST["format"]);if($Uf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Pl=$_POST["db_style"];$j=array(DB);if(DB==""){$j=$_POST["databases"];if(is_string($j))$j=explode("\n",rtrim(str_replace("\r","",$j),"\n"));}foreach((array)$j
as$k){adminer()->dumpDatabase($k);if(connection()->select_db($k)){if($Uf&&$Pl)echo
use_sql($k,$Pl).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$K){if($K!=""){if(DB==""&&information_schema(DB,$K))continue;set_schema($K);}if($Uf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$Ll=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$Hd=array();$qc=array();foreach($Ll
as$B=>$R){if($ya||in_array($B,(array)$_POST["tables"]))$Hd[$B]=$R;if($ya||in_array($B,(array)$_POST["data"]))$qc[$B]=$R;}if($Uf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($Hd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Xm=array();foreach($qc
as$B=>$R){if(!is_view($R)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($Hd[$B])))$Xm[]=$B;}echo
truncate_all_sql($Xm);}$Oi="";if($_POST["types"]){foreach(types()as$t=>$T){$Ac=type_definition($t);$Yh=($Ac["kind"]=='d'?"DOMAIN":"TYPE");if($Ac["definition"])$Oi
.=($Pl!='DROP+CREATE'?"DROP $Yh IF EXISTS ".table($T).";;\n":"")."CREATE $Yh ".table($T)." $Ac[definition];\n\n";else$Oi
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$T)."\n\n";}}if($_POST["routines"]){foreach(routines()as$I){$B=$I["ROUTINE_NAME"];$yk=$I["ROUTINE_TYPE"];$Bf=routine($I["SPECIFIC_NAME"],$yk);if($Bf["definition"]!=""){$i=create_routine($yk,array("name"=>$B)+$Bf);set_utf8mb4($i);$Oi
.=($Pl!='DROP+CREATE'?"DROP $yk IF EXISTS ".table($B).";;\n":"")."$i;\n\n";}else$Oi
.="-- Could not export ".strtolower($yk)." ".preg_replace('~[\r\n]+~',' ',$B)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$I){$i=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($I["Name"]),3));set_utf8mb4($i);$Oi
.=($Pl!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($I["Name"]).";;\n":"")."$i;;\n\n";}}echo($Oi&&JUSH=='sql'?"DELIMITER ;;\n\n$Oi"."DELIMITER ;\n\n":$Oi);}if($_POST["table_style"]||$_POST["data_style"]){$Mn=array();foreach($Ll
as$B=>$R){$Q=array_key_exists($B,$Hd);$oc=array_key_exists($B,$qc);if($Q||$oc){$Fm=null;if($Id=="tar"){$Fm=new
TmpFile;ob_start(array($Fm,'write'),1e5);}adminer()->dumpTable($B,($Q?$_POST["table_style"]:""),(is_view($R)?2:0));if(is_view($R))$Mn[]=$B;elseif($oc){$o=fields($B);$L=array("*");$bc=convert_fields($o,$o);if($bc)$L[]=substr($bc,2);adminer()->dumpData($B,$_POST["data_style"],"",$L);}if($Uf&&$_POST["triggers"]&&$Q&&($Wm=trigger_sql($B)))echo"\nDELIMITER ;;\n$Wm\nDELIMITER ;\n";if($Id=="tar"){ob_end_flush();tar_file((DB!=""?"":"$k/")."$B.csv",$Fm);}elseif($Uf)echo"\n";}}if($Uf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($Hd
as$B=>$R){if(!is_view($R))echo
foreign_keys_sql($B);}}if($Uf){foreach($Mn
as$Ln)adminer()->dumpTable($Ln,$_POST["table_style"],1);}if($Id=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(85),$m,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$uc=array('','USE','DROP+CREATE','CREATE');$Ik=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$uc);$km=array('','DROP+CREATE','CREATE');$pc=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$pc[]='INSERT+UPDATE';$I=get_settings("adminer_export");if(!$I)$I=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(170)."<td>".html_radios("output",adminer()->dumpOutput(),$I["output"])."\n","<tr><th>".lang(171)."<td>".html_radios("format",adminer()->dumpFormat(),$I["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(44)."<td>".html_select('db_style',$uc,$I["db_style"]).(support("type")?checkbox("types",1,$I["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$I["routines"],lang(81)):"").(support("event")?checkbox("events",1,$I["events"],lang(83)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(88)."<td>".html_select('schema_style',$Ik,$I["schema_style"]):""),"<tr><th>".lang(145)."<td>".html_select('table_style',$km,$I["table_style"]).checkbox("auto_increment",1,$I["auto_increment"],lang(60)).(support("trigger")?checkbox("triggers",1,$I["triggers"],lang(164)):""),"<tr><th>".lang(172)."<td>".html_select('data_style',$pc,$I["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(85),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Ej=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(173)."'".on('click','formCheck','^schemas\[').">".lang(88)."</label>","<tbody>\n";foreach(adminer()->schemas()as$K){if(!information_schema(DB,$K))echo"<tr><td>".checkbox("schemas[]",$K,true,$K,"","block")."\n";}}elseif(DB!=""){$tb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$tb class='jsonly' title='".lang(173)."'".on('click','formCheck','^tables\[').">".lang(154)."</label>","<th style='text-align: right;'><label class='block'>".lang(172)."<input type='checkbox' id='check-data'$tb class='jsonly' title='".lang(173)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Mn="";$nm=tables_list();foreach($nm
as$B=>$T){$Dj=preg_replace('~_.*~','',$B);$tb=($a==""||$a==(substr($a,-1)=="%"?"$Dj%":$B));$Kj="<tr><td>".checkbox("tables[]",$B,$tb,$B,"","block");if($T!==null&&!preg_match('~table~i',$T))$Mn
.="$Kj\n";else
echo"$Kj<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$tb)."</label>\n";$Ej[$Dj]++;}echo$Mn;if($nm)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$j=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($j?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(173)."'".on('click','formCheck','^databases\[').">":"").lang(44)."</label>","<tbody>\n";if($j){foreach($j
as$k){if(!information_schema($k)){$Dj=preg_replace('~_.*~','',$k);echo"<tr><td>".checkbox("databases[]",$k,$a==""||$a=="$Dj%",$k,"","block")."\n";$Ej[$Dj]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$be=true;foreach($Ej
as$x=>$W){if($x!=""&&$W>1){echo($be?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$x%")."'>".h($x)."</a>";$be=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(80));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(174)."</a>";$G=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Be=$G;if(!$G)$G=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Be?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(42)."<th>".lang(40)."<td class='hover'><tbody>\n";while($I=$G->fetch_assoc())echo'<tr><td>'.h($I["User"]),"<td>".h($I["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($I["User"]).'&host='.url_escape($I["Host"])).'">'.lang(14)."</a>\n";if(!$Be||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(14)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$m&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$m&&$_POST["val"]){$ta=0;$Ql=true;$lb=array();$Fk=0;foreach($_POST["val"]as$J)$Fk+=count($J);$Ya=$Fk>1&&driver()->begin();foreach($_POST["val"]as$am=>$J){$Q=bracket_escape($am,true);$o=fields($Q);$bm=indexes($Q);foreach($J
as$u=>$I){parse_str(bracket_escape($u,true),$Z);$hn=array();foreach($Z["where"]as$x=>$W)$hn[bracket_escape($x,true)]=$W;if(!$o||$Z["null"]||array_diff_key($hn,$o)||!unique_array($hn,$bm)){$Ql=false;break
2;}$N=array();$L=array();foreach($I
as$dg=>$W){$x=bracket_escape($dg,true);$n=idx($o,$x);if(!$n){$Ql=false;break
3;}$N[idf_escape($x)]=(preg_match('~char|text~',$n["type"])||$W!=""?adminer()->processInput($n,$W):"NULL");$L[$dg]=$x;}$Wj=where($Z,$o);if(!driver()->update($Q,$N," WHERE $Wj",0," ")){$Ql=false;break
2;}$ta+=connection()->affected_rows;$e=array();foreach($L
as$x)$e[]=idf_escape($x);$rn=driver()->select($Q,$e,array($Wj),$e);$Nh=($rn?$rn->fetch_row():array());$Zf=0;foreach($L
as$dg=>$x){$n=$o[$x];$Ol=array('type'=>(preg_match('~binary~',$n["type"])?'blob':$n["type"]));$lb["val[$am][$u][$dg]"]=select_value(idx($Nh,$Zf++),"",$Ol,null);}}}if($Ya&&$Ql)$Ql=driver()->commit();queries_redirect(null,lang(175,$ta),$Ql);if($Ya&&!$Ql)driver()->rollback();page_headers();page_messages($m);foreach($lb
as$B=>$W)echo"<div data-name='".h($B)."' hidden>$W</div>\n";exit;}restart_session();$df=&get_session("queries");$cf=&$df[DB];if(!$m&&$_POST["clear"]){$cf=array();redirect(remove_from_uri("history"));}stop_session();$ra=get_settings("adminer_import");if($_POST&&$ra)save_settings($ra,"adminer_import");page_header((isset($_GET["import"])?lang(84):lang(73)),$m);$Bg=driver()->lineComment();if(!$m&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$Cc=driver()->delimiter;$pe=false;if(!isset($_GET["import"]))$F=$_POST["query"];elseif($_POST["webfile"]){$Cl=adminer()->importServerPath();$pe=@fopen((file_exists($Cl)?$Cl:"compress.zlib://$Cl.gz"),"rb");$F=($pe?fread($pe,1e6):false);}else$F=get_file("sql_file",true,$Cc);if(is_string($F)){if(($ch=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($ch,strval(2*strlen($F)+memory_get_usage()+8e6)));if($F!=""&&strlen($F)<1e6){$Tj=$F.(preg_match("~$Cc\\s*\$~",$F)?"":$Cc);if(!$cf||first(end($cf))!=$Tj){restart_session();$cf[]=array($Tj,time());set_session("queries",$df);stop_session();}}$_l="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Bg)[^\n]*\n?|--\r?\n)";$fi=0;$nd=true;$dc=false;$h=connect();if($h&&DB!=""){$h->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$h);}$Ib=0;$vd=array();$Xi='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Bg.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Jm=microtime(true);while($F!=""){if(!$fi&&preg_match("~^$_l*+DELIMITER\\s+(\\S+)~i",$F,$A)){$Cc=preg_quote($A[1]);$F=substr($F,strlen($A[0]));}elseif(!$fi&&JUSH=='pgsql'&&preg_match("~^($_l*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$F,$A)){$Cc="\n\\\\\\.\r?\n";$dc=true;$fi=strlen($A[0]);}else{preg_match("($Cc\\s*|$Xi)",$F,$A,PREG_OFFSET_CAPTURE,$fi);list($ne,$E)=$A[0];if(!$ne&&$pe&&!feof($pe))$F
.=fread($pe,1e5);else{if(!$ne&&rtrim($F)=="")break;$fi=$E+strlen($ne);if($ne&&!preg_match("(^$Cc)",$ne)){$ib=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($E>0&&strtolower($F[$E-1])=="e"));$nj=($ne=='/*'?'\*/':($ne=='['?']':(preg_match("~^(?:$Bg)~",$ne)?"\n":preg_quote($ne).($ib?'|\\\\.':''))));while(preg_match("($nj|\$)s",$F,$A,PREG_OFFSET_CAPTURE,$fi)){$Gk=$A[0][0];if(!$Gk&&$pe&&!feof($pe))$F
.=fread($pe,1e5);else{$fi=$A[0][1]+strlen($Gk);if(!$Gk||$Gk[0]!="\\")break;}}}else{$Tj=substr($F,0,$E+($dc?3:0));$F=substr($F,$fi);$fi=0;if($dc){$Cc=driver()->delimiter;$dc=false;}$Ab="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Tj)."</code>";if(preg_match("~^$_l*+\$~",$Tj)&&!preg_match('~/\*M?!~',$Tj)){echo($_POST["only_errors"]?"":"<pre>$Ab</pre>\n");continue;}$nd=false;$Ib++;$Kj="<pre id='sql-$Ib'>$Ab</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$_l*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Tj,$A)!==0){echo$Kj,"<p class='error'>".lang(176,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$vd[]=" <a href='#sql-$Ib'>$Ib</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Kj;ob_flush();flush();}$Il=microtime(true);if(connection()->multi_query($Tj)&&$h&&preg_match("~^$_l*+USE\\b~i",$Tj))$h->query($Tj);do{$G=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Kj:""),"<p class='error'>".lang(177).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$vd[]=" <a href='#sql-$Ib'>$Ib</a>";if($_POST["error_stops"])break
2;}else{$_=ME."sql=".url_escape(trim($Tj));$zm=" <span class='time'>(".format_time($Il).")</span>".(strlen($_)<adminer()->sqlUrlLength()-100?" <a href='".h($_)."'>".lang(14)."</a>":"");$ta=connection()->affected_rows;$Qn=($_POST["only_errors"]?"":driver()->warnings());$Rn="warnings-$Ib";if($Qn)$zm
.=", <a href='#$Rn' class='toggle'>".lang(55)."</a>";$Fd="";$Gd="explain-$Ib";if(is_object($G)){$z=$_POST["limit"];$Wh=$z;$ed=!$_POST["only_errors"];if($ed)echo"<form action='' method='post'>\n";$Ei=print_select_result($G,$h,array(),$Wh,$ed);if(!$_POST["only_errors"]){$Wh=max($G->num_rows,$Wh);echo"<p class='sql-footer'>".($Wh?($z&&$Wh>$z?lang(178,$z):"").lang(179,$Wh):""),$zm;if($h&&preg_match("~^($_l|\\()*+SELECT\\b~i",$Tj)&&($Fd=adminer()->explain($h,$Tj,$Ei))!="")echo", <a href='#$Gd' class='toggle'>Explain</a>";if($ed)echo", <input type='submit' name='save' value='".lang(18)."' class='jsonly' disabled"." title='".lang(180)."'".on('click','sqlSave',lang(21)).">";$t="export-$Ib";echo", <a href='#$t' class='toggle'>".lang(85)."</a><span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ra["output"])." ".html_select("format",adminer()->dumpFormat(),$ra["format"]).input_hidden("query",$Tj)."<input type='submit' name='export' value='".lang(85)."'".($z?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$_l*+(CREATE|DROP|ALTER)$_l++(DATABASE|SCHEMA)\\b~i",$Tj)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(181,$ta)."$zm\n";}echo($Qn?"<div id='$Rn' class='hidden'>\n$Qn</div>\n":""),($Fd!=""?"<div id='$Gd' class='hidden explain'>\n$Fd</div>\n":"");}$Il=microtime(true);}while(connection()->next_result());}}}}}if($nd)echo"<p class='message'>".lang(182)."\n";else{$sf=connection()->inTransaction();driver()->rollback();if($sf)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(183,$Ib-count($vd))," <span class='time'>(".format_time($Jm).")</span>\n";elseif($vd&&$Ib>1)echo"<p class='error'>".lang(177).": ".implode("",$vd)."\n";}}else
echo"<p class='error'>".upload_error($F)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$sn="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($sn);echo'>
';$Cd="<input type='submit' value='".lang(184)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Tj=$_GET["sql"];if($_POST)$Tj=$_POST["query"];elseif($_GET["history"]=="all")$Tj=$cf;elseif($_GET["history"]!="")$Tj=idx($cf[$_GET["history"]],0);echo"<p>";textarea("query",$Tj,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$Cd\n",lang(185).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Ke=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(186)."</legend><div>",($sn?input_hidden(ini_get("session.upload_progress.name"),$sn):""),"SQL$Ke: ".file_input(" name='sql_file[]' multiple","\n$Cd"),($sn?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$pf=adminer()->importServerPath();if($pf)echo"<fieldset><legend>".lang(187)."</legend><div>",lang(188,"<code>".h($pf)."$Ke</code>")," <input type='submit' name='webfile' value='".lang(189)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(190))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(191))."\n",input_token();if(!isset($_GET["import"])&&$cf){print_fieldset("history",lang(192),$_GET["history"]!="");for($W=end($cf);$W;$W=prev($cf)){$x=key($cf);list($Tj,$zm,$jd)=$W;echo'<div><a href="'.h(ME."sql=&history=$x").'" class="hover">'.lang(14)."</a>"." <span class='time' title='".@date('Y-m-d',$zm)."'>".@date("H:i:s",$zm)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Bg).*~m",'',$Tj))),80,"</code>").($jd?" <span class='time'>($jd)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(193)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(194)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$o=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$o):""):where($_GET,$o));$pn=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($o
as$B=>$n){if((!$pn&&!isset($n["privileges"]["insert"]))||adminer()->fieldName($n)=="")unset($o[$B]);}if($_POST&&!$m&&!isset($_GET["select"])){$Gg=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$Gg=($pn?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$Gg))$Gg=ME."select=".url_escape($a);$w=indexes($a);$in=unique_array($_GET["where"],$w);$Wj="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Gg,lang(195),driver()->delete($a,$Wj,$in?0:1));else{$N=array();foreach($o
as$B=>$n){$W=process_input($n);if($W!==false&&$W!==null)$N[idf_escape($B)]=$W;}if($pn){if(!$N)redirect($Gg);queries_redirect($Gg,lang(196),driver()->update($a,$N,$Wj,$in?0:1));if(is_ajax()){page_headers();page_messages($m);exit;}}else{$G=driver()->insert($a,$N);$rg=($G?last_id($G):0);queries_redirect($Gg,lang(197,($rg?" $rg":"")),$G);}}}$I=null;$F="";$zm="";if($Z){$L=array();$Sk=array("*");foreach($o
as$B=>$n){if(isset($n["privileges"]["select"])){$Ha=($_POST["clone"]&&$n["auto_increment"]?"''":convert_field($n));$d=($Ha?"$Ha AS ":"").idf_escape($B);$L[]=$d;if($Ha)$Sk[]=$d;}}$I=array();if(!support("table")){$L=array("*");$Sk=$L;}if($L){$Il=microtime(true);$G=driver()->select($a,$L,array($Z),$L,array(),(isset($_GET["select"])?2:1));$F=str_replace("SELECT ".implode(", ",$L),"SELECT ".implode(", ",$Sk),driver()->query);$zm=format_time($Il);if(!$G)$m=adminer()->error();else{$I=$G->fetch_assoc();if(!$I)$I=false;}if(isset($_GET["select"])&&(!$I||$G->fetch_assoc()))$I=null;}}if(!$o&&driver()->primary!=""){if(!$Z){$G=driver()->select($a,array("*"),array(),array("*"));$I=($G?$G->fetch_assoc():false);if(!$I)$I=array(driver()->primary=>"");}if($I){foreach($I
as$x=>$W){if(!$Z)$I[$x]=null;$o[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}if($_POST["save"]){$zj=array();foreach((array)$_POST["fields"]as$x=>$W)$zj[bracket_escape($x,true)]=$W;$I=$zj+($I?$I:array());}edit_form($a,$o,$I,$pn,$m,$F,$zm);}elseif(isset($_GET["create"])){function
referencable_primary($Vk){$H=array();foreach(table_status('',true)as$em=>$Q){if($em!=$Vk&&!$Q["dependent"]&&fk_support($Q)){foreach(fields($em)as$n){if($n["primary"]){if($H[$em]){unset($H[$em]);break;}$H[$em]=$n;}}}}return$H;}$a=$_GET["create"];$cj=driver()->partitionBy;$gj=($cj&&$a!=""?driver()->partitionsInfo($a):array());$ek=referencable_primary($a);$le=array();foreach($ek
as$em=>$n)$le[str_replace("`","``",$em)."`".str_replace("`","``",$n["field"])]=$em;$Ii=array();$R=array();$Th=false;if($a!=""){$Ii=fields($a);$R=table_status1($a);$Th=(count($R)<2);}$Aa=($a==""||driver()->supportsAlterTable($R));$I=$_POST;$I["fields"]=(array)$I["fields"];if($I["auto_increment_col"])$I["fields"][$I["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$m)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($I["fields"])&&!$m){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(198),drop_tables(array($a)));else{$o=array();$za=array();$wn=false;$je=array();$Hi=reset($Ii);$va=" FIRST";foreach($I["fields"]as$n){$q=$le[$n["type"]];$Zm=($q!==null?$ek[$q]:$n);if($n["field"]!=""){if(!$n["generated"])$n["default"]=null;$Pj=process_field($n,$Zm);$za[]=array($n["orig"],$Pj,$va);if(!$Hi||$Pj!==process_field($Hi,$Hi)){$o[]=array($n["orig"],$Pj,$va);if($n["orig"]!=""||$va)$wn=true;}if($q!==null)$je[idf_escape($n["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$le[$n["type"]],'source'=>array($n["field"]),'target'=>array($Zm["field"]),'on_delete'=>$n["on_delete"],),object_name("FOREIGN",trim($I["name"]),array($n["field"])));$va=" AFTER ".idf_escape($n["field"]);}elseif($n["orig"]!=""){$wn=true;$o[]=array($n["orig"]);}if($n["orig"]!=""){$Hi=next($Ii);if(!$Hi)$va="";}}$ej=array();if(in_array($I["partition_by"],$cj)){foreach($I
as$x=>$W){if(preg_match('~^partition~',$x))$ej[$x]=$W;}foreach($ej["partition_names"]as$x=>$B){if($B==""){unset($ej["partition_names"][$x]);unset($ej["partition_values"][$x]);}}$ej["partition_names"]=array_values($ej["partition_names"]);$ej["partition_values"]=array_values($ej["partition_values"]);if($ej==$gj)$ej=array();}elseif(preg_match("~partitioned~",$R["Create_options"]))$ej=null;$eh=lang(199);if($a==""){cookie("adminer_engine",$I["Engine"]);$eh=lang(200);}$B=trim($I["name"]);$Gg=ME.(support("table")?"table=":"select=").url_escape($B);$G=alter_table($a,$B,(JUSH=="sqlite"&&($wn||$je)?$za:$o),$je,($I["Comment"]!=$R["Comment"]?$I["Comment"]:null),($I["Engine"]&&$I["Engine"]!=$R["Engine"]?$I["Engine"]:""),($I["Collation"]&&$I["Collation"]!=$R["Collation"]?$I["Collation"]:""),($I["Auto_increment"]!=""?number($I["Auto_increment"]):""),$ej);if($G&&!Queries::$queries&&$a!=""&&!$o&&!$je)redirect($Gg);queries_redirect($Gg,$eh,$G);}}$Jl=($a!=""?"alter":"create");page_header(($a!=""?lang(53):lang(86)),$m,array("table"=>$a),h($a),$Th,doc_link(array('sql'=>"$Jl-table.html",'mariadb'=>($a!=""?"$Jl-table":""),'pgsql'=>"sql-$Jl"."table.html",'cockroach'=>"$Jl-table",'mssql'=>"t-sql/statements/$Jl-table-transact-sql",'sqlite'=>"lang_{$Jl}table.html",'oracle'=>"sqlrf/".strtoupper($Jl)."-TABLE.html",)));if(!$_POST){$dn=driver()->types();$I=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($dn["int"])?"int":(isset($dn["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$I=$R;$I["name"]=$a;$I["fields"]=array();if(!$_GET["auto_increment"])$I["Auto_increment"]="";foreach($Ii
as$n){if($n["generated"])$n["default"]=ltrim($n["default"]);$n["generated"]=$n["generated"]?:(isset($n["default"])?"DEFAULT":"");$I["fields"][]=$n;}if($cj){$I+=$gj;$I["partition_names"][]="";$I["partition_values"][]="";}}}$Eb=flat_collations();$qd=driver()->engines();foreach($qd
as$pd){if(!strcasecmp($pd,$I["Engine"])){$I["Engine"]=$pd;break;}}$Pg=max_input_vars(12,20);if($Pg){$Ze=(count($I["fields"])>$Pg?"":" hidden");echo"<p".($Ze?" id='max-fields' data-columns='$Pg'":"")." class='error$Ze'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(201).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($I["name"])."' autocapitalize='off'>\n",(!$Aa?h($R["Engine"])."\n":($qd?html_select("Engine",array(""=>"(".lang(202).")")+$qd,$I["Engine"],on('change','helpClose').on_help_value())."\n":""));if($Eb)echo"<datalist id='collations'>".optionlist($Eb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($I["Collation"])."' placeholder='(".lang(124).")'>\n");echo"<input type='submit' value='".lang(18)."'>\n";}if(support("columns")&&$Aa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($I["fields"],$Eb,"TABLE",$le);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(60).": <input type='number' name='Auto_increment' class='size' value='".h($I["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(203),on('click','columnShowClick',6),"jsonly");$Lb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$Lb,lang(59),on('click','editingCommentsClick',true),"jsonly").' ';$c=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($Lb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$c,$I["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$a)),'>
';if($cj&&(JUSH=='sql'||$a=="")){$dj=preg_match('~RANGE|LIST~',$I["partition_by"]);print_fieldset("partition",lang(205),$I["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$cj),$I["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($I["partition"])."'>)\n",lang(206).": <input type='number' name='partitions' class='size".($dj||!$I["partition_by"]?" hidden":"")."' value='".h($I["partitions"])."'>\n","<table id='partition-table'".($dj?"":" class='hidden'").">\n","<thead><tr><th>".lang(207)."<th>".lang(208)."<tbody>\n";foreach($I["partition_names"]as$x=>$W)echo'<tr>','<td><input name="partition_names[]" value="'.h($W).'" autocapitalize="off"'.($x==count($I["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($I["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$yf=array("PRIMARY","UNIQUE","INDEX");$R=table_status1($a,true);$vf=driver()->indexAlgorithms($R);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$R["Engine"]))$yf[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$R["Engine"]))$yf[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$R["Engine"]))$yf[]="VECTOR";$w=indexes($a);$o=fields($a);$Ij=array();if(JUSH=="mongo"){$Ij=$w["_id_"];unset($yf[0]);unset($w["_id_"]);}$I=$_POST;if($I)save_settings(array("index_options"=>$I["options"]));if($_POST&&!$m&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($I["indexes"]as$v){$B=$v["name"];if(in_array($v["type"],$yf)){$e=array();$yg=array();$Fc=array();$ti=array();$wf=(support("partial_indexes")?$v["partial"]:"");$uf=(in_array($v["algorithm"],$vf)?$v["algorithm"]:"");$N=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$Dc=idx($v["descs"],$x);$si=idx($v["opclasses"],$x);$N[]=($o[$d]?idf_escape($d):$d).($y?"(".(+$y).")":"").($si!=""?" ".idf_escape($si):"").($Dc?" DESC":"");$e[]=$d;$yg[]=($y?:null);$Fc[]=$Dc;$ti[]="$si";}}$Dd=$w[$B];if($Dd){ksort($Dd["columns"]);ksort($Dd["lengths"]);ksort($Dd["descs"]);if($v["type"]==$Dd["type"]&&array_values($Dd["columns"])===$e&&(!$Dd["lengths"]||array_values($Dd["lengths"])===$yg)&&array_values($Dd["descs"])===$Fc&&(!$Dd["opclasses"]||array_values($Dd["opclasses"])===$ti)&&$Dd["partial"]==$wf&&(!$vf||$Dd["algorithm"]==$uf)){unset($w[$B]);continue;}}if($e)$b[]=array($v["type"],$B,$N,$uf,$wf);}}foreach($w
as$B=>$Dd)$b[]=array($Dd["type"],$B,"DROP");if(!$b)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(209),alter_indexes($a,$b));}page_header(lang(156),$m,array("table"=>$a),h($a),false,doc_link(array('sql'=>"create-index.html",'pgsql'=>"sql-createindex.html",'cockroach'=>"create-index",'mssql'=>"t-sql/statements/create-index-transact-sql",'sqlite'=>"lang_createindex.html",'oracle'=>"sqlrf/CREATE-INDEX.html",)));$Ud=array_keys($o);if($_POST["add"]){foreach($I["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$I["indexes"][$x]["columns"][]="";}$v=end($I["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$I["indexes"][]=array("columns"=>array(1=>""));}if(!$I){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$I["indexes"]=$w;}$yg=(JUSH=="sql"||JUSH=="mssql");$ti=driver()->indexOpclasses();$pl=($_POST?$_POST["options"]:get_setting("index_options"));$Bh=array();foreach($yf
as$T)$Bh[$T]=str_replace("{table}",$a,adminer()->namePattern($T));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(210);$nf=" class='idxopts".($pl?"":" hidden")."'";if($vf)echo"<th id='label-algorithm'$nf>".lang(211).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/','pgsql'=>'indexes-types.html','cockroach'=>'create-index#parameters',));echo'<th><input type="submit" hidden>',lang(212).($yg?"<span$nf> (".lang(213).")</span>":"");if($yg||support("descidx"))echo
checkbox("options",1,$pl,lang(130),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(214);if(support("partial_indexes"))echo"<th id='label-condition'$nf>".lang(215);echo'<td><noscript>',icon("plus","add[0]","+",lang(131)),'</noscript>
<tbody>
';if($Ij){echo"<tr><td>PRIMARY<td>";foreach($Ij["columns"]as$x=>$d)echo
select_input(" disabled",array_combine($Ud,$Ud),$d),"<label><input disabled type='checkbox'>".lang(68)."</label> ";echo"<td><td>\n";}$Zf=1;foreach($I["indexes"]as$v){if(!$_POST["drop_col"]||$Zf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$Zf][type]",array(-1=>"")+$yf,$v["type"],on('change','indexesChangeType',$Bh),"label-type");if($vf)echo"<td$nf>".html_select("indexes[$Zf][algorithm]",array_merge(array(""),$vf),$v['algorithm'],"","label-algorithm");echo"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$Zf][columns][$s]' title='".lang(57)."'".on('change','indexesChangeColumn',$Bh),($o&&($d==""||$o[$d])?array_combine($Ud,$Ud):array()),$d)," <span$nf>",($yg?"<input type='number' name='indexes[$Zf][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".lang(129)."'>":"");if($ti){$si=idx($v["opclasses"],$x);echo
html_select("indexes[$Zf][opclasses][$s]",array(""=>"(".lang(216).")")+array_combine($ti,$ti)+($si!=""?array($si=>$si):array()),$si),doc_link(array('pgsql'=>'indexes-opclass.html'));}echo(support("descidx")?checkbox("indexes[$Zf][descs][$s]",1,idx($v["descs"],$x),lang(68)):""),"<br>","</span></span>";$s++;}echo"<td><input name='indexes[$Zf][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$nf><input name='indexes[$Zf][partial]' value='".h($v["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$Zf]","x",lang(133),on('click','editingRemoveRow','indexes$1[type]'));}$Zf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(18),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$I=$_POST;if($_POST&&!$m&&!$_POST["add"]){$B=trim($I["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(217),drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),lang(218),rename_database($B,(string)$I["collation"]));}else{$j=explode("\n",str_replace("\r","",$B));$Ql=true;$pg="";foreach($j
as$k){if(count($j)==1||$k!=""){if(!create_database($k,(string)$I["collation"]))$Ql=false;$pg=$k;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($pg),lang(219),$Ql);}}else{if(!$I["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$I["collation"])?" COLLATE $I[collation]":""),substr(ME,0,-1),lang(220));}}$Jl=(DB!=""?"alter":"create");page_header(DB!=""?lang(76):lang(137),$m,array(),h(DB),false,doc_link(array('sql'=>"$Jl-database.html",'mariadb'=>(DB!=""?"":"$Jl-database"),'pgsql'=>"sql-$Jl"."database.html",'cockroach'=>"$Jl-database",'mssql'=>"t-sql/statements/$Jl-database-transact-sql",'oracle'=>"sqlrf/".strtoupper($Jl)."-USER.html",)));$Eb=collations();$B=DB;if($_POST)$B=$I["name"];elseif(DB!="")$I["collation"]=db_collation(DB,$Eb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Be){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Be,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($Eb?html_select("collation",array(""=>"(".lang(124).")")+$Eb,$I["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):"")."\n",'<input type=\'submit\' value=\'',lang(18),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(149)."'".confirm(lang(204,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(131))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$I=$_POST;$f=(support("comment")&&$_GET["ns"]!=""?schema_comment($_GET["ns"]):"");if($_POST&&!$m){$_=preg_replace('~&ns=[^&]*~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,lang(221));else{$B=trim($I["name"]);$_
.=url_escape($B);$Ih=(support("comment")?$I["comment"]:$f);$G=true;$eh=lang(222);if($_GET["ns"]==""){$G=queries("CREATE SCHEMA ".idf_escape($B));$eh=lang(223);}elseif($_GET["ns"]!=$B)$G=queries("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B));elseif($Ih===$f)redirect($_);if($G&&$Ih!==$f)$G=set_schema_comment($B,$Ih);queries_redirect($_,$eh,$G);}}$Jl=($_GET["ns"]!=""?"alter":"create");page_header($_GET["ns"]!=""?lang(77):lang(78),$m,array(),"",false,doc_link(array('pgsql'=>"sql-$Jl"."schema.html",'cockroach'=>"$Jl-schema",'mssql'=>"t-sql/statements/create-schema-transact-sql",)));if(!$I){$I["name"]=$_GET["ns"];$I["comment"]=$f;}echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'',lang(18),'\'>
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".lang(149)."'".confirm(lang(204,$_GET["ns"])).">\n";echo(support("comment")?"<p>".lang(59).": ".adminer()->commentInput('SCHEMA'," name='comment'",$I["comment"])."\n":""),input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$Ck=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$yk=routine($_GET["call"],$Ck);$Wf=(idx($yk["returns"],"type")=="trigger");page_header(($Wf?lang(164):lang(224)).": ".h($ca),$m,"#routines","",!$yk,(isset($_GET["callf"])?"":doc_link(array('sql'=>"call.html",'pgsql'=>"sql-call.html",'cockroach'=>"call",'mssql'=>"t-sql/language-elements/execute-transact-sql",))));$qf=array();$Oi=array();foreach($yk["fields"]as$s=>$n){if(substr($n["inout"],-3)=="OUT"&&JUSH=='sql')$Oi[$s]="@".idf_escape($n["field"])." AS ".idf_escape($n["field"]);if(!$n["inout"]||preg_match('~^(IN|OUTPUT)~',$n["inout"]))$qf[]=$s;}if(!$m&&$_POST){$jb=array();foreach($yk["fields"]as$x=>$n){$W="";if(in_array($x,$qf)){$W=process_input($n);if($W===false)$W="''";if(isset($Oi[$x]))connection()->query("SET @".idf_escape($n["field"])." = $W");}if(isset($Oi[$x]))$jb[]="@".idf_escape($n["field"]);elseif(in_array($x,$qf))$jb[]=$W;}$Fa=implode(", ",$jb);$F=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($yk["returns"],"type")=="record"?"* FROM ":"").table($ca)."($Fa)":"EXEC ".table($ca).($Fa!=""?" $Fa":""));$Il=microtime(true);$G=connection()->multi_query($F);$ta=connection()->affected_rows;echo
adminer()->selectQuery($F,$Il,!$G);if(!$G)echo"<p class='error'>".adminer()->error()."\n";else{$h=connect();if($h)$h->select_db(DB);do{$G=connection()->store_result();if(is_object($G))print_select_result($G,$h);else
echo"<p class='message'>".lang(225,$ta)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Oi)print_select_result(connection()->query("SELECT ".implode(", ",$Oi)));}}if($Wf){$Wm=driver()->routineTriggers($_GET["call"]);if($Wm){echo"<table>\n";foreach($Wm
as$I){$_=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($I["ns"]),ME);echo"<tr><th>".h($I["trigger"])."<td><a href='".h($_."table=".url_escape($I["table"])."#triggers")."'>".($I["ns"]!=$_GET["ns"]?"<b>".h($I["ns"])."</b>.":"").h($I["table"])."</a>"."<td class='hover'><a href='".h($_."trigger=".url_escape($I["table"])."&name=".url_escape($I["trigger"]))."'>".lang(160)."</a>\n";}echo"</table>\n";}}else{echo'<form action="" method="post">
';if($qf){echo"<table class='layout'>\n";foreach($qf
as$x){$n=$yk["fields"][$x];$B=$n["field"];echo"<tr><th>".adminer()->fieldName($n);$X=idx($_POST["fields"],$B);if($X!=""){if($n["type"]=="set")$X=implode(",",$X);}input($n,$X,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(224),'\'>
',input_token(),'</form>
';}echo'
',adminer()->commentValue($Ck,$yk['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$I=$_POST;if($_POST&&!$m&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$I["source"]=array_filter($I["source"],'strlen');ksort($I["source"]);$qm=array();foreach($I["source"]as$x=>$W)$qm[$x]=$I["target"][$x];$I["target"]=$qm;}$Tb=object_name("FOREIGN",$a,$I["source"]);$Ya=false;if(JUSH=="sqlite")$G=recreate_table($a,$a,array(),array(),array(" $B"=>($I["drop"]?"":" ".format_foreign_key($I,$Tb))));else{$b="ALTER TABLE ".table($a);$Xc="$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B?:'');if($I["drop"])$G=queries($Xc);else{$ma=" ADD".format_foreign_key($I,$Tb);if($B=="")$G=queries($b.$ma);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Tb!=$B))$G=queries("$Xc,$ma");else{$Ya=support("transaction_ddl")&&driver()->begin();$G=($Ya||idx(foreign_keys($a),$B)?queries($Xc):true)&&queries($b.$ma)&&(!$Ya||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($I["drop"]?lang(226):($B!=""?lang(227):lang(228))),$G);if($Ya)driver()->rollback();if(!$I["drop"])$m=lang(229);}$Th=false;if(!$_POST&&$B!=""){$le=foreign_keys($a);$I=idx($le,$B,array());$Th=!$I;}page_header(($B!=""?lang(230):lang(161)),$m,array("table"=>$a),h($B!=""?$B:$a),$Th,doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-PARMS-REFERENCES",'cockroach'=>"foreign-key",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"sqlrf/constraint.html",)));if($_POST){ksort($I["source"]);if($_POST["change"]||$_POST["change-js"])$I["target"]=array();else$I["source"][]="";}elseif($B!="")$I["source"][]="";else{$I["table"]=$a;$I["source"]=array("");}echo'
<form action="" method="post">
';$yl=array_keys(fields($a));if($I["db"]!="")connection()->select_db($I["db"]);if($I["ns"]!=""){$Ji=get_schema();set_schema($I["ns"]);}$dk=array_keys(array_filter(table_status('',true),function(array$R){return!$R["dependent"]&&fk_support($R);}));$qm=array_keys(fields(in_array($I["table"],$dk)?$I["table"]:reset($dk)));$c=on('change','foreignChange');echo"<p><label>".lang(231).": ".html_select("table",$dk,$I["table"],$c)."</label>\n";if(support("scheme")){$Jk=array_filter(adminer()->schemas(),function($K){return!information_schema(DB,$K);});echo"<label>".lang(88).": ".html_select("ns",$Jk,$I["ns"]!=""?$I["ns"]:$_GET["ns"],$c)."</label>";if($I["ns"]!="")set_schema($Ji);}elseif(JUSH!="sqlite"){$vc=array();foreach(adminer()->databases()as$k){if(!information_schema($k))$vc[]=$k;}echo"<label>".lang(87).": ".html_select("db",$vc,$I["db"]!=""?$I["db"]:$_GET["db"],$c)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(232),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(158),'<th id="label-target">',lang(159),'<tbody>
';$Zf=0;foreach($I["source"]as$x=>$W){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$yl,$W,($Zf==count($I["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$x)."]",$qm,idx($I["target"],$x),"","label-target");$Zf++;}echo'</table>
<p>
<label>',lang(126),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$I["on_delete"]),'</label>
<label>',lang(125),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$I["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$I["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(233),'\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$I=$_POST;$Ki="VIEW";if(JUSH=="pgsql"&&$a!=""){$O=table_status1($a);$Ki=strtoupper($O["Engine"]);}if($_POST&&!$m){$B=trim($I["name"]);$Ha=" AS\n$I[select]";$Gg=ME."table=".url_escape($B);$eh=lang(234);$T=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$T=="VIEW"&&$Ki=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Ha,$Gg,$eh);else{$um="adminer_".uniqid();drop_create("DROP $Ki ".table($a),"CREATE $T ".table($B).$Ha,"DROP $T ".table($B),"CREATE $T ".table($um).$Ha,"DROP $T ".table($um),($_POST["drop"]?substr(ME,0,-1):$Gg),lang(235),$eh,lang(236),$a,$B);}}$Th=false;if(!$_POST&&$a!=""){$I=view($a);$Th=!$I["select"];$I["name"]=$a;$I["materialized"]=($Ki!="VIEW");if(!$m)$m=adminer()->error();}page_header(($a!=""?lang(52):lang(237)),$m,array("table"=>$a),h($a),$Th,doc_link(array('sql'=>"create-view.html",'pgsql'=>"sql-createview.html",'cockroach'=>"create-view",'mssql'=>"t-sql/statements/create-view-transact-sql",'sqlite'=>"lang_createview.html",'oracle'=>"sqlrf/CREATE-VIEW.html",)));echo'
<form action="" method="post">
<p>',lang(214),': <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$I["materialized"],lang(152)):""),'<p>';textarea("select",$I["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Lf=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Ll=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$I=$_POST;if($_POST&&!$m){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(238));elseif(in_array($I["INTERVAL_FIELD"],$Lf)&&isset($Ll[$I["STATUS"]])){$Hk="\nON SCHEDULE ".($I["INTERVAL_VALUE"]?"EVERY ".q($I["INTERVAL_VALUE"])." $I[INTERVAL_FIELD]".($I["STARTS"]?" STARTS ".q($I["STARTS"]):"").($I["ENDS"]?" ENDS ".q($I["ENDS"]):""):"AT ".q($I["STARTS"]))." ON COMPLETION".($I["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(239):lang(240)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Hk.($aa!=$I["EVENT_NAME"]?"\nRENAME TO ".idf_escape($I["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($I["EVENT_NAME"]).$Hk)."\n".$Ll[$I["STATUS"]]." COMMENT ".q($I["EVENT_COMMENT"]).rtrim(" DO\n$I[EVENT_DEFINITION]",";").";"));}}$Th=false;if(!$I&&$aa!=""){$J=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$Th=!$J;$I=reset($J);}page_header(($aa!=""?lang(241).": ".h($aa):lang(242)),$m,"#events","",$Th,doc_link(array('sql'=>"create-event.html")));echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(214),'<td><input name="EVENT_NAME" value="',h($I["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(243),'<td><input name="STARTS" value="',h("$I[EXECUTE_AT]$I[STARTS]"),'">
<tr><th title="datetime">',lang(244),'<td><input name="ENDS" value="',h($I["ENDS"]),'">
<tr><th>',lang(245),'<td><input type="number" name="INTERVAL_VALUE" value="',h($I["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Lf,$I["INTERVAL_FIELD"]),'<tr><th>',lang(140),'<td>',html_select("STATUS",$Ll,$I["STATUS"]),'<tr><th>',lang(59),'<td><input name="EVENT_COMMENT" value="',h($I["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$I["ON_COMPLETION"]=="PRESERVE",lang(246)),'</table>
<p>';textarea("EVENT_DEFINITION",$I["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$yk=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$I=$_POST;$I["fields"]=(array)$I["fields"];if($_POST&&!process_fields($I["fields"])&&!$m){foreach($I["fields"]as$x=>$n){if($n["field"]=="")unset($I["fields"][$x]);}$li=routine($_GET["procedure"],$yk);$ji=($li?routine_id($ca,$li):"");$Kh=routine_id($I["name"],$I);$i=create_routine($yk,$I);$Gg=substr(ME,0,-1);$eh=lang(247);if(!$_POST["drop"]&&$ji==$Kh&&connection()->flavor!="mysql")queries_redirect($Gg,$eh,queries(substr_replace($i,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$um="adminer_".uniqid();drop_create("DROP $yk $ji",$i,"DROP $yk $Kh",create_routine($yk,array("name"=>$um)+$I),"DROP $yk ".routine_id($um,$I),$Gg,lang(248),$eh,lang(249),$ca,$I["name"]);}}$Th=false;if(!$_POST&&$ca!=""){$I=routine($_GET["procedure"],$yk);$Th=!$I;$I["name"]=$ca;}$Ak=strtolower($yk);page_header(($ca!=""?(isset($_GET["function"])?lang(165):lang(250)).": ".h($ca):(isset($_GET["function"])?lang(251):lang(252))),$m,"#routines","",$Th,doc_link(array('sql'=>"create-procedure.html",'mariadb'=>"create-$Ak/",'pgsql'=>"sql-create$Ak.html",'cockroach'=>"create-$Ak",'mssql'=>"t-sql/statements/create-$Ak-transact-sql",)));if(!$_POST&&$ca=="")$I["language"]="sql";$Eb=(JUSH=="sql"?flat_collations():array());$zk=routine_languages();echo($Eb?"<datalist id='collations'>".optionlist($Eb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(214),': <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',($zk?"<label>".lang(24).": ".html_select("language",array_keys($zk),$I["language"],on('change','routineLanguage',$zk))."</label>\n":""),'<input type=\'submit\' value=\'',lang(18),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($I["fields"],$Eb,$yk);if(isset($_GET["function"])){echo"<tr><td>".lang(253);edit_type("returns",(array)$I["returns"],$Eb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$I["definition"],20,80,($zk[$I["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$ca)),'>
';$Bk=routine_options($yk);if($Bk){$zi=false;foreach($Bk
as$x=>$Y){$l=($Y?reset($Y):"");$I["options"][$x]=idx($I["options"],$x,$l);if($I["options"][$x]!=$l)$zi=true;}print_fieldset("options",lang(130),$zi);echo"<table class='layout'>\n";foreach($Bk
as$x=>$Y){$kg="label-option-$x";$Bm=str_replace("_"," ",$x);$L=array();foreach($Y
as$X)$L[$X]=(strpos($X,"$Bm ")===0?substr($X,strlen($Bm)+1):$X);echo"<tr><th id='$kg'>$Bm<td>".($L?html_select("options[$x]",$L,$I["options"][$x],"",$kg):"<input name='options[$x]' value='".h($I["options"][$x])."' aria-labelledby='$kg' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$ea=$_GET["sequence"];$I=$_POST;if($_POST&&!$m){$_=substr(ME,0,-1);$B=trim($I["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($ea),$_,lang(254));elseif($ea=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$_,lang(255));elseif($ea!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($ea)." RENAME TO ".idf_escape($B),$_,lang(256));else
redirect($_);}$Th=(!$_POST&&$ea!=""&&!get_val("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." AND relname = ".q($ea)));page_header(($ea!=""?lang(257).": ".h($ea):lang(258)),$m,"#sequences","",$Th,doc_link(array('pgsql'=>"sql-createsequence.html",'cockroach'=>"create-sequence",)));if(!$I)$I["name"]=$ea;echo'
<form action="" method="post">
<p><input name="name" value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ea!="")echo"<input type='submit' name='drop' value='".lang(149)."'".confirm(lang(204,$ea)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){function
enum_values($Ac){$X="'(?:[^']|'')*'";if(!preg_match('~^AS\s+ENUM\s*\(\s*('.$X.'(?:\s*,\s*'.$X.')*)\s*\)$~i',$Ac,$A))return
null;preg_match_all('~'.$X.'~',$A[1],$Mg);return$Mg[0];}function
add_enum_values($T,$hi,$Hh){$ni=enum_values($hi);$Ph=enum_values($Hh);if($ni===null||$Ph===null)return
null;$H=array();$s=0;foreach($Ph
as$X){if($X===idx($ni,$s))$s++;else$H[]="ALTER TYPE ".idf_escape($T)." ADD VALUE $X".($s<count($ni)?" BEFORE ".$ni[$s]:"");}return($s==count($ni)?$H:null);}$fa=$_GET["type"];$I=$_POST;$an=($fa!=""?array_search($fa,types(true)):0);$T=($an?type_definition(+$an):array());$Yh=($T["kind"]=='d'?"DOMAIN":"TYPE");if($_POST&&!$m){$_=substr(ME,0,-1);$B=trim($I["name"]);$Ha=trim(str_replace("\r","",$I["as"]));$Mh=(preg_match('~^AS\s+(?!ENUM\b|RANGE\b|\()~i',$Ha)?"DOMAIN":"TYPE");$eh=lang(259);$b=(!$_POST["drop"]&&$fa!=""&&$Mh==$Yh?($Ha==$T["definition"]?array():add_enum_values($fa,$T["definition"],$Ha)):null);if($b!==null){if($fa!=$B)$b[]="ALTER $Yh ".idf_escape($fa)." RENAME TO ".idf_escape($B);if(!$b)redirect($_);$Nd=false;foreach($b
as$F){if(!queries($F)){$Nd=true;break;}}queries_redirect($_,$eh,!$Nd);}else{$um="adminer_".uniqid();drop_create("DROP $Yh ".idf_escape($fa),"CREATE $Mh ".idf_escape($B)." $Ha","DROP $Mh ".idf_escape($B),"CREATE $Mh ".idf_escape($um)." $Ha","DROP $Mh ".idf_escape($um),$_,lang(260),$eh,lang(261),$fa,$B);}}page_header(($fa!=""?lang(262).": ".h($fa):lang(263)),$m,"#user-types","",($an===false),doc_link(array('pgsql'=>"sql-createtype.html",'cockroach'=>"create-type",)));if(!$I){$I["name"]=$fa;$I["as"]=($fa!=""?$T["definition"]:"AS ");}echo'
<form action="" method="post">
<p>
',lang(214).": <input name='name' value='".h($I['name'])."' autocapitalize='off'>\n";textarea("as",$I["as"]);echo"<p><input type='submit' value='".lang(18)."'>\n";if($fa!="")echo"<input type='submit' name='drop' value='".lang(149)."'".confirm(lang(204,$fa)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$I=$_POST;if($I&&!$m){$Gg=ME."table=".url_escape($a);$hh=lang(264);$fh=lang(265);$gh=lang(266);if(JUSH=="sqlite")queries_redirect($Gg,($I["drop"]?$hh:($B!=""?$fh:$gh)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($I["drop"]?"":$I["clause"])));else{$b="ALTER TABLE ".table($a);$qb=" CHECK ($I[clause])";$um="adminer_".uniqid();drop_create("$b DROP CONSTRAINT ".idf_escape($B),"$b ADD".($I["name"]!=""?" CONSTRAINT ".idf_escape($I["name"]):"").$qb,"$b DROP CONSTRAINT ".idf_escape($I["name"]),"$b ADD CONSTRAINT ".idf_escape($um).$qb,"$b DROP CONSTRAINT ".idf_escape($um),$Gg,$hh,$fh,$gh,$B,$I["name"]);}}$Th=false;if(!$I){$ub=driver()->checkConstraints($a);$Th=($B!=""&&!$ub[$B]);$I=array("name"=>($B!=""?$B:object_name("CHECK",$a,array())),"clause"=>$ub[$B]);}page_header(($B!=""?lang(267):lang(163)),$m,array("table"=>$a),h($B!=""?$B:$a),$Th,doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'cockroach'=>"check",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",)));echo'
<form action="" method="post">
';if(JUSH!="sqlite")echo'<p>'.lang(214).': <input name="name" value="'.h($I["name"]).'" data-maxlength="64" autocapitalize="off">';echo'<p>';textarea("clause",$I["clause"]);echo'<p><input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$Vm=trigger_options();$I=trigger($B,$a);$Th=($B!=""&&!$I);$Ah=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$I+=array("Trigger"=>strtr($Ah,array("{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row")));if($_POST){if(!$m&&in_array($_POST["Timing"],$Vm["Timing"])&&in_array($_POST["Event"],$Vm["Event"])&&in_array($_POST["Type"],$Vm["Type"])){$oi=" ON ".table($a);$Xc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$oi:"");$Gg=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Xc,$Gg,lang(268));else{if($B!="")queries($Xc);queries_redirect($Gg,($B!=""?lang(269):lang(270)),queries(create_trigger($oi,$_POST)));if($B!="")queries(create_trigger($oi,$I+array("Type"=>reset($Vm["Type"]))));}}$I=$_POST;}page_header(($B!=""?lang(271):lang(166)),$m,array("table"=>$a),h($B!=""?$B:$a),$Th,doc_link(array('sql'=>"create-trigger.html",'pgsql'=>"sql-createtrigger.html",'cockroach'=>"create-trigger",'mssql'=>"t-sql/statements/create-trigger-transact-sql",'sqlite'=>"lang_createtrigger.html",'oracle'=>"lnpls/CREATE-TRIGGER-statement.html",)));$Dh=strtr(preg_quote($Ah),array('\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)'));$Tm=on('change','triggerChange',"^$Dh$",$Ah);$bi=on('input','triggerChange',"^$Dh$",$Ah);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(272),'<td>',html_select("Timing",$Vm["Timing"],$I["Timing"],$Tm),'<tr><th>',lang(273),'<td>',html_select("Event",$Vm["Event"],$I["Event"],$Tm),(in_array("UPDATE OF",$Vm["Event"])?" <input name='Of' value='".h($I["Of"])."' class='hidden'$bi>":""),'<tr><th>',lang(58),'<td>',html_select("Type",$Vm["Type"],$I["Type"],$Tm),'<tr><th>',lang(214),'<td><input name="Trigger" value="',h($I["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$I["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Be,array$Nj,$e,$oi){if(!$Nj)return
true;if($Nj==array("ALL PRIVILEGES","GRANT OPTION"))return($Be=="GRANT"?queries("$Be ALL PRIVILEGES$oi WITH GRANT OPTION"):queries("$Be ALL PRIVILEGES$oi")&&queries("$Be GRANT OPTION$oi"));return
queries("$Be ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$Nj).$e).$oi);}$ga=$_GET["user"];$Nj=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$I){foreach(explode(",",($I["Privilege"]=="Grant option"?"":$I["Context"]))as$Yb)$Nj[$Yb=="File access on server"?"Server Admin":$Yb][$I["Privilege"]]=$I["Comment"];}unset($Nj["Server Admin"]["Usage"]);foreach($Nj["Tables"]as$x=>$W)unset($Nj["Databases"][$x]);$Jh=array();if($_POST){foreach($_POST["objects"]as$x=>$W)$Jh[$W]=(array)$Jh[$W]+idx($_POST["grants"],$x,array());}$Ce=array();$G=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ga)."@".q($_GET["host"])):null);$Th=(isset($_GET["host"])&&!$G);if($G){while($I=$G->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$I[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$Mg,PREG_SET_ORDER)){foreach($Mg
as$W){if($W[1]!="USAGE")$Ce["$A[2]$W[2]"][$W[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$I[0]))$Ce["$A[2]$W[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$m){$mi=(isset($_GET["host"])?q($ga)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $mi",ME."privileges=",lang(274));else{$Oh=q($_POST["user"])."@".q($_POST["host"]);$ij=$_POST["pass"];$gc=false;$G=true;if($mi!=$Oh){$gc=queries("CREATE USER $Oh IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($ij));$G=$gc;}elseif($ij!="")$G=queries("SET PASSWORD FOR $Oh = ".(min_version(8,99)||$_POST["hashed"]?q($ij):"PASSWORD(".q($ij).")"));if($G){$uk=array();foreach($Jh
as$Yh=>$Be){if(isset($_GET["grant"]))$Be=array_filter($Be);$Be=array_keys($Be);if(isset($_GET["grant"]))$uk=array_diff(array_keys(array_filter($Jh[$Yh],'strlen')),$Be);elseif($mi==$Oh){$ii=array_keys((array)$Ce[$Yh]);$uk=array_diff($ii,$Be);$Be=array_diff($Be,$ii);unset($Ce[$Yh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Yh,$A)&&(!grant("REVOKE",$uk,$A[2]," ON $A[1] FROM $Oh")||!grant("GRANT",$Be,$A[2]," ON $A[1] TO $Oh"))){$G=false;break;}}}if($G&&isset($_GET["host"])){if($mi!=$Oh)queries("DROP USER $mi");elseif(!isset($_GET["grant"])){foreach($Ce
as$Yh=>$uk){if(preg_match('~^(.+)(\(.*\))?$~U',$Yh,$A))grant("REVOKE",array_keys($uk),$A[2]," ON $A[1] FROM $Oh");}}}if($G&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(275):lang(276)),$G);if($gc)connection()->query("DROP USER $Oh");}}page_header((isset($_GET["host"])?lang(42).": ".h("$ga@$_GET[host]"):lang(174)),$m,array("privileges"=>array('',lang(80))),"",$Th,doc_link(array('sql'=>"grant.html",'mariadb'=>"grant")));$I=$_POST;if($I)$Ce=$Jh;else{$I=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Ce[(DB==""||$Ce?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(40),'<td><input name="host" data-maxlength="60" value="',h($I["host"]),'" autocapitalize="off">
<tr><th>',lang(42),'<td><input name="user" data-maxlength="80" value="',h($I["user"]),'" autocapitalize="off">
<tr><th>',lang(43),'<td><input name="pass" id="pass" value="',h($I["pass"]),'" autocomplete="new-password">
',($I["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$I["hashed"],lang(277),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(80);$s=0;foreach($Ce
as$Yh=>$Be){echo'<th>'.($Yh!="*.*"?"<input name='objects[$s]' value='".h($Yh)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(40),"Databases"=>lang(44),"Tables"=>lang(154),"Procedures"=>lang(278),)as$Yb=>$Dc){foreach((array)$Nj[$Yb]as$Mj=>$f){echo"<tr><td".($Dc?">$Dc<td":" colspan='2'").' lang="en" title="'.h($f).'">'.h($Mj);$s=0;foreach($Ce
as$Yh=>$Be){$B="'grants[$s][".h(strtoupper($Mj))."]'";$X=$Be[strtoupper($Mj)];if($Yb=="Server Admin"&&$Yh!=(isset($Ce["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($X?" selected":"").">".lang(279)."<option value='0'".($X=="0"?" selected":"").">".lang(280)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($X?" checked":"").($Mj=="All privileges"?" id='grants-$s-all'":($Mj=="Grant option"?"":on('click','grantsClick',"grants-$s-all"))).">","</label>";$s++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(149),'\'',confirm(lang(204,"$ga@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$m){$hg=0;foreach((array)$_POST["kill"]as$W){if(adminer()->killProcess($W))$hg++;}queries_redirect(ME."processlist=",lang(281,$hg),$hg||!$_POST["kill"]);}}page_header(lang(138),$m);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$s=-1;foreach(adminer()->processList()as$s=>$I){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($I
as$x=>$W)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"refrn/V-SESSION.html",));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$I[JUSH=="sql"?"Id":"pid"],0):"");foreach($I
as$x=>$W)echo"<td>".($W!=""&&((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$I["Command"]))||(JUSH=="pgsql"&&$x=="query")||(JUSH=="oracle"&&$x=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($W)."'>".shorten_utf8($W,100,"</code>").' <a href="'.h(($I["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($I["db"])."&":ME)."sql=".url_escape($W)).'">'.lang(282).'</a>'.' '.copy_icon():h($W));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($s+1)."/".lang(283,max_connections()),"<p><input type='submit' value='".lang(284)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$R=table_status1($a);$w=indexes($a);$o=fields($a);$le=column_foreign_keys($a);$gi=$R["Oid"];$wk=array();$e=array();$Nk=array();$Bi=array();$xm=null;foreach($o
as$x=>$n){$B=adminer()->fieldName($n);$Ch=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($n["privileges"]["select"])&&$B!=""){$e[$x]=$Ch;if(is_shortable($n))$xm=adminer()->selectLengthProcess();}if(isset($n["privileges"]["where"])&&$B!="")$Nk[$x]=$Ch;if(isset($n["privileges"]["order"])&&$B!="")$Bi[$x]=$Ch;$wk+=$n["privileges"];}list($L,$r)=adminer()->selectColumnsProcess($e,$w);$L=array_unique($L);$r=array_unique($r);$Sf=count($r)<count($L);$Z=adminer()->selectSearchProcess($o,$w,$R);$Ai=adminer()->selectOrderProcess($o,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$jn=>$I){$Ha=convert_field($o[key($I)]);$L=array($Ha?:idf_escape(key($I)));$Z[]=where_check(bracket_escape($jn,true),$o);$H=driver()->select($a,$L,$Z,$L);if($H)echo
first($H->fetch_row());}exit;}$Ij=$mn=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$Ij=array_flip($v["columns"]);$mn=($L?$Ij:array());foreach($mn
as$x=>$W){if(in_array(idf_escape($x),$L))unset($mn[$x]);}break;}}if($gi&&!$Ij){$Ij=$mn=array($gi=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($gi));}if($_POST&&!$m){$Tn=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ub=array();foreach($_POST["check"]as$qb)$ub[]=where_check($qb,$o);$Tn[]="((".implode(") OR (",$ub)."))";}$Vn=$Tn;$Tn=($Tn?"\nWHERE ".implode(" AND ",$Tn):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Rk=($L?:array("*"));$bc=convert_fields($e,$o,$L);if($bc)$Rk[]=substr($bc,2);$F="";if(is_array($_POST["check"])&&!$Ij){$se=implode(", ",$Rk)."\nFROM ".table($a);$Fe=($r&&$Sf?"\nGROUP BY ".implode(", ",$r):"").($Ai?"\nORDER BY ".implode(", ",$Ai):"");$gn=array();foreach($_POST["check"]as$W)$gn[]="(SELECT".limit($se,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$o).$Fe,1).")";$F=implode(" UNION ALL ",$gn);}adminer()->dumpData($a,"table",$F,$Rk,$Vn,($Sf?$r:array()),$Ai);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$le)){if($_POST["save"]||$_POST["delete"]){$G=true;$ta=0;$Ya=false;$N=array();if(!$_POST["delete"]){foreach($o
as$B=>$W){$u=bracket_escape($B);if(isset($_POST["fields"][$u])||$_FILES["fields-$u"]){$W=process_input($o[$B]);if($W!==null&&($_POST["clone"]||$W!==false))$N[idf_escape($B)]=($W!==false?$W:idf_escape($B));}}}if($_POST["delete"]||$N){$F=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($N)).")\nSELECT ".implode(", ",$N)."\nFROM ".table($a):"");if($_POST["all"]||($Ij&&is_array($_POST["check"]))||$Sf){$G=($_POST["delete"]?driver()->delete($a,$Tn):($_POST["clone"]?queries("INSERT $F$Tn".driver()->insertReturning($a)):driver()->update($a,$N,$Tn)));$ta=connection()->affected_rows;if(is_object($G))$ta+=$G->num_rows;}else{$Ya=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$Sn="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$o);$G=($_POST["delete"]?driver()->delete($a,$Sn,1):($_POST["clone"]?queries("INSERT".limit1($a,$F,$Sn)):driver()->update($a,$N,$Sn,1)));if(!$G)break;$ta+=connection()->affected_rows;}if($Ya&&$G&&!driver()->commit())$G=false;}}$eh=lang(175,$ta);if($_POST["clone"]&&$G&&$ta==1){$rg=last_id($G);if($rg)$eh=lang(197," $rg");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$eh,$G);if($Ya)driver()->rollback();if(!$_POST["delete"]){$zj=(array)$_POST["fields"];edit_form($a,array_intersect_key($o,$zj),$zj,!$_POST["clone"],$m);page_footer();exit;}}elseif(!$_POST["import"]){$G=true;$ta=0;$Ya=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$jn=>$I){$N=array();foreach($I
as$x=>$W){$x=bracket_escape($x,true);$N[idf_escape($x)]=(preg_match('~char|text~',$o[$x]["type"])||$W!=""?adminer()->processInput($o[$x],$W):"NULL");}$G=driver()->update($a,$N," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($jn,true),$o),($Sf||$Ij?0:1)," ");if(!$G)break;$ta+=connection()->affected_rows;}if($Ya)$G=$G&&driver()->commit();queries_redirect(remove_from_uri(),lang(175,$ta),$G);if($Ya)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$Vd=get_file("csv_file",true);if(!is_string($Vd))$m=upload_error($Vd);elseif(!preg_match('~~u',$Vd))$m=lang(285);else{$Fb=array_keys($o);$Xk=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$kc=parse_csv($Vd,$Xk);$ta=count($kc);driver()->begin();$J=array();foreach($kc
as$x=>$Y){if(!$x&&!array_diff($Y,$Fb)){$Fb=$Y;$ta--;}else{$N=array();foreach($Y
as$s=>$Bb)$N[idf_escape($Fb[$s])]=($Bb==""&&$o[$Fb[$s]]["null"]?"NULL":q(csv_value($Bb)));$J[]=$N;}}$G=(!$J||driver()->insertUpdate($a,$J,$Ij));if($G)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(286,$ta),$G);driver()->rollback();}}}}$em=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(62).": $em",$m,array(),"",(!$o&&support("table")),($o?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$N=null;if(isset($wk["insert"])||!support("table")){$N="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$A)?$A[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$N
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$N);if(!$e&&support("table"))echo"<p class='error'>".lang(287)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($L,$e);adminer()->selectSearchPrint($Z,$Nk,$w,$R);adminer()->selectOrderPrint($Ai,$Bi,$w);adminer()->selectLimitPrint($z);if($xm!==null)adminer()->selectLengthPrint($xm);adminer()->selectActionPrint($w);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(119).' '.lang(120)."\n";page_footer();exit;}}$C=$_GET["page"];$oe=null;if($C=="last"){$oe=get_val(count_rows($a,$Z,$Sf,$r));$C=floor(max(0,intval($oe)-1)/$z);}$Qk=$L;$Ee=$r;if(!$Qk){$Qk[]="*";$bc=convert_fields($e,$o,$L);if($bc)$Qk[]=substr($bc,2);}foreach($L
as$x=>$W){$n=$o[idf_unescape($W)];if($n&&($Ha=convert_field($n)))$Qk[$x]="$Ha AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$x=>$W){if(isset($Qk[$x])&&$W["fun"])$Qk[$x].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$Sf&&$mn){foreach($mn
as$x=>$W){$Qk[]=idf_escape($x);if($Ee)$Ee[]=idf_escape($x);}}$G=driver()->select($a,$Qk,$Z,$Ee,$Ai,$z,$C,true);if(!is_object($G))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$C)$G->seek($z*$C);$md=array();$J=array();while($I=$G->fetch_assoc()){if($C&&JUSH=="oracle")unset($I["RNUM"]);$J[]=$I;}$Qe=($z&&(support("cursor")?$_GET["next"]!="":count($J)>=$z));if(is_ajax()&&$Qe)header("X-Next-Page: ".pagination_href($C+1));if($_GET["modify"]&&$J){$Vg=max_input_vars(count($J[0])+1,20);echo($Vg&&count($J)>$Vg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($sn).">\n";if($_GET["page"]!="last"&&$z&&$r&&$Sf&&JUSH=="sql")$oe=get_val(" SELECT FOUND_ROWS()");if(!$J)echo"<p class='message'>".lang(16)."\n";else{$Ua=adminer()->backwardKeys($a,$em);$sk=array();reset($L);foreach($J[0]as$x=>$W){if(!isset($mn[$x])){$W=idx($_GET["columns"],key($L))?:array();$sk[$x]=array("fun"=>$W["fun"],"col"=>($L?$W["col"]:$x));next($L);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$r&&$L?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(288)."'".on('click','formCheck','^check').">");$Eh=array();$ak=1;foreach($sk
as$x=>$W){$n=$o[$W["col"]];$B=($n?adminer()->fieldName($n,$ak):($W["fun"]?($W["col"]!=""?h($W["col"]):"*"):h($x)));if($B!=""){$ak++;$Eh[$x]=$B;$d=idf_escape($x);$gf=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($x);$Dc="&desc[0]=1";$vl=preg_replace('~ DESC( NULLS LAST)?$~','',$Ai[0]);$xl=($vl==$d||$vl==$x);echo"<th id='th[".h(bracket_escape($x))."]'".($xl?" aria-sort='".($vl==$Ai[0]?"ascending":"descending")."'":"").">";$xe=apply_sql_function(h($W["fun"]),$B);$wl=isset($n["privileges"]["order"])||$W["fun"];echo($wl?"<a href='".h($gf.($xl&&$vl==$Ai[0]?$Dc:''))."'>$xe</a>":$xe);$dh=($wl?"<a href='".h($gf.$Dc)."' title='".lang(68)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($n["privileges"]["where"]))$dh
.="<a href='#fieldset-search' title='".lang(65)."' class='text jsonly'".on('click','selectSearch',$x)."> =</a>";echo($dh?"<span class='column'>$dh</span>":"");}}$yg=array();if($_GET["modify"]){foreach($J
as$I){foreach($I
as$x=>$W)$yg[$x]=max($yg[$x],min(40,utf8_length((string)$W)));}}$bf=array();$af=array();foreach((array)$_GET["where"]as$W){$W+=array("col"=>"","op"=>"","val"=>"");$Bb=$W["col"];$Mk=$W["val"];if(!is_array($Mk)&&($Mk!=""||preg_match('~NULL$~',$W["op"]))&&(!$W["op"]||in_array($W["op"],adminer()->operators($R)))){$_g=strtr(preg_quote($Mk),array("%"=>".*?","_"=>"."));$oj=array("LIKE %%"=>$_g,"ILIKE %%"=>$_g,"REGEXP"=>$Mk)+(JUSH=="pgsql"?array("~"=>$Mk,"~*"=>$Mk):array())+($Bb!=""?array():array("="=>'^'.preg_quote($Mk).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Mk)))).')\z',"LIKE"=>"^$_g\\z","ILIKE"=>"^$_g\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Mk).'(?=,|\z)',));foreach(($Bb!=""?array($Bb=>$o[$Bb]):$o)as$B=>$n){if($Bb!=""||is_searchable($n,$W)){$ri=$W["op"]?:(!preg_match('~'.text_type().'~',$n["type"])?"IN":(preg_match('~%~',$Mk)?"LIKE":"LIKE %%"));if(isset($oj[$ri])){$xb=preg_match('~^ILIKE|\*$~',$ri)||($ri!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$bf[$B][]="(?".($xb?"i":"").":$oj[$ri])";}elseif($ri=="IS NULL"&&$Bb=="")$af[$B]=true;}}}}echo($Ua?"<th>".lang(289):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($J,$le)as$zh=>$I){$in=unique_array($J[$zh],$w);if(!$in){$in=array();foreach($J[$zh]as$x=>$W){if(!in_array(idx(idx($sk,$x,array()),"fun"),driver()->grouping))$in[$x]=$W;}}$jn="";$s=0;foreach($in
as$x=>$W){$rk=idx($sk,$x,array());if($L&&$rk&&!isset($o[$rk["col"]]))continue;$xe=idx($rk,"fun","");$Bb=($xe?$rk["col"]:$x);$n=(array)$o[$Bb];$Rf=is_blob($n);if(!$xe&&strlen($W)>64&&driver()->md5(idf_escape($Bb),$n)){$xe="md5";$W=md5($Rf?(string)driver()->value($W,$n):$W);}if($xe){$jn
.="&fun[$s]=".url_escape($xe)."&col[$s]=".url_escape($Bb).($W!==null?"&val[$s]=".url_escape($W===false?"f":$W):"");$s++;}else$jn
.="&".($W!==null?"where[".url_escape(bracket_escape($Bb))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($Bb));}echo"<tr>".(!$r&&$L?"":"<td class='hover check sticky'>".($Sf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$jn)."' class='edit'>".lang(290)."</a> ").checkbox("check[]",substr($jn,1),in_array(substr($jn,1),(array)$_POST["check"])));foreach($I
as$x=>$W){if(isset($Eh[$x])){$xe=$sk[$x]["fun"];$Bb=$sk[$x]["col"];$n=(array)$o[$x];if($W!=""&&(!isset($md[$x])||$md[$x]!=""))$md[$x]=(is_mail($W)?$Eh[$x]:"");$_="";if(is_blob($n)&&$W!="")$_=ME.'download='.url_escape($a).'&field='.url_escape($x).$jn;if(!$_&&$W!==null){foreach((array)$le[$x]as$q){if(count($le[$x])==1||end($q["source"])==$x){$_="";foreach($q["source"]as$s=>$yl)$_
.=where_link($s,$q["target"][$s],$J[$zh][$yl]);$_=($q["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($q["db"]),ME):ME).'select='.url_escape($q["table"]).$_;if($q["ns"]!="")$_=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($q["ns"]),$_);if(count($q["source"])==1)break;}}}if($xe=="count"&&$Bb==""){$_=ME."select=".url_escape($a);$s=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$in))$_
.=where_link($s++,$V["col"],$V["val"],$V["op"]);}foreach($in
as$cg=>$V){if(idx(idx($sk,$cg,array()),"fun")){$_="";break;}$_
.=where_link($s++,$cg,$V);}}$hf=select_value($W,$_,$n,$xm,($xe?array():idx($bf,$x,array())));if($W===null&&!$xe&&isset($af[$x]))$hf="<mark>$hf</mark>";$u=bracket_escape($jn);$t=h("val[$u][".bracket_escape($x)."]");$Aj=idx(idx($_POST["val"],$u),bracket_escape($x));$pn=idx($n["privileges"],"update")&&!is_identity_always($n);$hd=$J[$zh][$x];$id=!is_array($I[$x])&&!is_blob($n)&&is_utf8($hd)&&!$xe&&!$n["generated"]&&$pn;$T=($xe=="min"||$xe=="max"?$o[$Bb]["type"]:$n["type"]);$wm=preg_match('~text|json|lob~',$T);$Tf=preg_match(number_type(),$T)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$xe);echo"<td id='$t'".($Tf&&($W===null||is_numeric(strip_tags($hf))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$id&&$hd!==null)||$Aj!==null){$Le=h($Aj!==null?$Aj:$hd);echo">".($wm?"<textarea name='$t' cols='30' rows='".(substr_count($hd,"\n")+1)."'>$Le</textarea>":"<input name='$t' value='$Le' size='$yg[$x]'>");}else{$Ig=strpos($hf,"<i>…</i>");$gd=adminer()->editVal($hd,$n);echo($pn?" data-text='".($Ig?2:($wm?1:0))."'".($id?"":" data-warning='".lang(291)."'").($id&&!$Ig&&html_entity_decode(strip_tags($hf),ENT_QUOTES,"UTF-8")!==$gd?" data-value='".h($gd)."'":""):"").">$hf";}}}if($Ua)echo"<td>";adminer()->backwardKeysPrint($Ua,$J[$zh]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$sa=get_settings("adminer_import");if($J||$C||$Qe){$Bd=true;if($_GET["page"]!="last"){if(!$z||(count($J)<$z&&($J||!$C)))$oe=($C?$C*$z:0)+count($J);elseif(JUSH!="sql"||!$Sf){$oe=($Sf?null:found_rows($R,$Z));$Bd=!driver()->hasEstimatedRows();if($oe===null||(!$Bd&&$oe<max(1e4,2*($C+1)*$z))){$oe=first(slow_query(count_rows($a,$Z,$Sf,$r)));$Bd=true;}}}if(!support("cursor"))$Qe=(($oe===false?count($J)+1:$oe-$C*$z)>$z);$Ti=($z&&($Qe||$C));if($Ti)echo($Qe?'<p><a href="'.h(pagination_href($C+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(292)).'>'.lang(293).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Ti){$Tg=($oe===false?$C+($J?(count($J)>=$z?2:1):0):floor(($oe-1)/$z));echo"<fieldset><legend>".lang(294)."</legend>";if(!support("cursor")){echo
pagination(0,$C).($C>5?" …":"");for($s=max(1,$C-4);$s<min($Tg,$C+5);$s++)echo
pagination($s,$C);if($Tg>0)echo($C+5<$Tg?" …":""),($Bd&&$oe!==false?pagination($Tg,$C):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Tg'>".lang(295)."</a>");}else
echo
pagination(0,$C).($C>1?" …":""),($C?pagination($C,$C):""),($Qe?pagination($C+1,$C)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(296)."</legend>";$Lc=($Bd?"":"~ ").$oe;$kg=($oe!==false?($Bd?"":"~ ").lang(179,$oe):"");echo
checkbox("all",1,0,$kg,on('click','countRows',$Lc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(180)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(297),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(148),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(282),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$me=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]||($d["col"]!=""&&!isset($o[$d["col"]]))){unset($me['sql']);break;}}if($me){print_fieldset("export",lang(85)." <span id='selected2'></span>");$Pi=adminer()->dumpOutput();echo($Pi?html_select("output",$Pi,$sa["output"])." ":""),html_select("format",$me,$sa["format"])," <input type='submit' name='export' value='".lang(85)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($md,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(84)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($sn?input_hidden(ini_get("session.upload_progress.name"),$sn):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$sa["format"])." <input type='submit' name='import' value='".lang(84)."'>".($sn?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$r&&$L?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$O=isset($_GET["status"]);page_header($O?lang(140):lang(139));$In=($O?adminer()->showStatus():adminer()->showVariables());if(!$In)echo"<p class='message'>".lang(16)."\n";else{echo"<table>\n";foreach($In
as$I){echo"<tr>";$x=array_shift($I);echo"<th><code class='jush-".JUSH.($O?"status":"set")."'>".h($x)."</code>";foreach($I
as$W)echo"<td>".nl_br(h($W));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Tl=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$R){json_row("Comment-$B",h($R["Comment"]).($R["Error"]?" <span class='error'>".h($R["Error"])."</span>":""));if(!is_view($R)||preg_match('~materialized~i',$R["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$B",h($R[$x]));foreach(array_keys($Tl+array("Auto_increment"=>0,"Rows"=>0))as$x){if(array_key_exists($x,$R))json_row("$x-$B",format_status($R,$x));if($R[$x]!=""&&isset($Tl[$x]))$Tl[$x]+=($R["Engine"]!="InnoDB"||$x!="Data_free"?$R[$x]:0);}}}if(function_exists('Adminer\db_status'))$Tl=db_status();foreach($Tl
as$x=>$W)json_row("sum-$x",adminer()->formatSizeValue($W));json_row("");}elseif($_GET["script"]=="kill"){if(!$m)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$k=>$W){json_row("tables-$k",format_number($W));json_row("size-$k",db_size($k));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$S=tables_list();if($S)redirect(ME.(support("table")?"table=":"select=").url_escape(key($S)));}$ah=ME.(isset($_GET["select"])?"select=&":"");$om=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($om&&!$m&&!$_POST["search"]){$G=true;$eh="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$G=truncate_tables($_POST["tables"]);$eh=lang(298);}elseif($_POST["move"]){$G=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$eh=lang(299);}elseif($_POST["copy"]){$G=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$eh=lang(300);}elseif($_POST["drop"]){if($_POST["views"])$G=drop_views($_POST["views"]);if($G&&$_POST["tables"])$G=drop_tables($_POST["tables"]);$eh=lang(301);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("PRAGMA integrity_check(".q($Q).")")as$I)$eh
.="<b>".h($Q)."</b>: ".h($I["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("DBCC CHECKTABLE (".q(table($Q)).") WITH TABLERESULTS")as$I)$eh
.="<b>".h($Q)."</b>: ".h($I["MessageText"])."<br>";}}elseif(JUSH!="sql"){$G=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$eh=lang(302);}elseif(!$_POST["tables"])$eh=lang(13);elseif($G=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($I=$G->fetch_assoc())$eh
.="<b>".h($I["Table"])."</b>: ".h($I["Msg_text"])."<br>";}queries_redirect(relative_uri(),$eh,$G);}page_header(($_GET["ns"]==""?lang(44).": ".h(DB):lang(88).": ".h($_GET["ns"])),$m,true);if(adminer()->homepage()){if($_GET["ns"]!==""){if(support("scheme")&&support("comment")){$f=schema_comment($_GET["ns"]);if($f!="")echo"<p class='nowrap'>".lang(59).": ".adminer()->commentValue('SCHEMA',$f)."\n";}$Ai=$_GET["order"];$ue=($Ai||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(303)."</h3>\n";$nm=($ue?table_status():tables_list());if(!$nm)echo"<p class='message'>".lang(13)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(304)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(65)."'>\n","</div></fieldset>\n";if(!$m&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(173).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$Ai&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($ah,0,-1)).'">'.lang(154).'</a>';$e=array("Engine"=>array(lang(305).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$e["Collation"]=array(lang(144).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$e["Data_length"]=array(lang(306).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'refrn/ALL_TABLES.html')),"create",lang(53),);if(support("indexes"))$e["Index_length"]=array(lang(307).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),"indexes",lang(157),);$e["Data_free"]=array(lang(308).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(54));if(function_exists('Adminer\alter_table'))$e["Auto_increment"]=array(lang(60).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(53),);$e["Rows"]=array(lang(309).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'refrn/ALL_TABLES.html')),"select",lang(50),);if(support("comment"))$e["Comment"]=array(lang(59).doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE','cockroach'=>'comment-on')),);$Ia=array('Engine','Collation','Comment');foreach($e
as$x=>$d)echo"<th".($Ai==$x?" aria-sort='".(in_array($x,$Ia)?"ascending":"descending")."'":"")."><a href='".h($ah)."order=$x'>$d[0]</a>";echo"<tbody>\n";if($Ai){uasort($nm,function($ia,$Ra)use($Ai,$Ia){$H=($ia[$Ai]<$Ra[$Ai]?-1:($ia[$Ai]>$Ra[$Ai]?1:0));return(in_array($Ai,$Ia)?$H:-$H);});}$S=0;$Tl=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($nm
as$B=>$O){$Ln=($ue?is_view($O):$O!==null&&!preg_match('~table|sequence~i',$O));$O=($ue?$O:array('Engine'=>$O));$t=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Ln?"views[]":"tables[]"),$B,in_array("$B",$om,true),"","","",$t),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".lang(51)."' id='$t'>".h($B).'</a>':h($B));if($Ln&&!preg_match('~materialized~i',$O['Engine'])){$Bm=lang(153);echo'<td colspan="'.(count($e)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".lang(52)."'>$Bm</a>":$Bm),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".lang(50)."'>?</a>";if(support("comment"))echo'<td>'.h($O['Comment']);}else{if($ue){foreach(array_keys($Tl)as$x)$Tl[$x]+=($O["Engine"]!="InnoDB"||$x!="Data_free"?idx($O,$x):0);}foreach($e
as$x=>$d){$t=" id='$x-".h($B)."'";echo($d[1]?"<td align='right'><a href='".h(ME."$d[1]=").url_escape($B)."'$t title='$d[2]'>".format_status($O,$x)."</a>":"<td$t>".h(idx($O,$x,'?')).($x=="Comment"&&$O["Error"]?" <span class='error'>".h($O["Error"])."</span>":""));}$S++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(283,count($nm)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($ue&&function_exists('Adminer\db_status'))$Tl=db_status();foreach($Tl
as$x=>$Sl)echo($e[$x]?"<td align='right' id='sum-$x'>".($ue?adminer()->formatSizeValue($Sl):""):"");echo"\n","</table>\n",($ue?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$En="<input type='submit' value='".lang(310)."'".on_help("VACUUM")."> ";$wi="<input type='submit' name='optimize' value='".lang(311)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Kj=(JUSH=="sqlite"?$En."<input type='submit' name='check' value='".lang(312)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$En.$wi:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(312)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(313)."'".on_help("ANALYZE TABLE")."> ".$wi."<input type='submit' name='check' value='".lang(312)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(314)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(315)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(149)."'".confirm().on_help("DROP TABLE").">":"");echo($Kj?"<div class='footer'><div>\n<fieldset><legend>".lang(148)." <span id='selected'></span></legend><div>$Kj\n</div></fieldset>\n":"");$j=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($j)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(316)." <span id='selected3'></span></legend><div>";$k=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($j?html_select("target",$j,$k):'<input name="target" value="'.h($k).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(132)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(23)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(317)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$S).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(86)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(237)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(81)."</h3>\n";$Dk=routines();if($Dk){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(214).'<th>'.lang(58).'<th>'.lang(253)."<td class='hover'><tbody>\n";foreach($Dk
as$I){$B=($I["SPECIFIC_NAME"]==$I["ROUTINE_NAME"]?"":"&name=".url_escape($I["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($I["SPECIFIC_NAME"]).$B).'"'.' title="'.($I["DTD_IDENTIFIER"]=="trigger"?lang(164):lang(224)).'">'.h($I["ROUTINE_NAME"]).'</a>','<td>'.h($I["ROUTINE_TYPE"]),'<td>'.h($I["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($I["SPECIFIC_NAME"]).$B).'">'.lang(160)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(252).'</a>':'').'<a href="'.h(ME).'function=">'.lang(251)."</a>\n","</div>\n";}if(support("sequence")){echo"<div>\n","<h3 id='sequences'>".lang(82)."</h3>\n";$bl=get_vals("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." ORDER BY relname");if($bl){echo"<table class='odds'>\n","<thead><tr><th>".lang(214)."<tbody>\n";foreach($bl
as$W)echo"<tr><th><a href='".h(ME)."sequence=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."sequence='>".lang(258)."</a>\n","</div>\n";}if(support("type")){echo"<div>\n","<h3 id='user-types'>".lang(0)."</h3>\n";$Bn=types();if($Bn){echo"<table class='odds'>\n","<thead><tr><th>".lang(214)."<tbody>\n";foreach($Bn
as$W)echo"<tr><th><a href='".h(ME)."type=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."type='>".lang(263)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(83)."</h3>\n";$J=get_rows("SHOW EVENTS");if($J){echo"<table>\n","<thead><tr><th>".lang(214)."<th>".lang(318)."<th>".lang(243)."<th>".lang(244)."<td class='hover'><tbody>\n";foreach($J
as$I)echo"<tr>","<th>".h($I["Name"]),"<td>".($I["Execute at"]?lang(319)."<td>".h($I["Execute at"]):lang(245)." ".h($I["Interval value"])." ".h($I["Interval field"])."<td>".h($I["Starts"])),"<td>".h($I["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($I["Name"]).'">'.lang(160).'</a>';echo"</table>\n";$zd=get_val("SELECT @@event_scheduler");if($zd&&$zd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($zd)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(242)."</a>\n","</div>\n";}}elseif(support("extension")){$Kd=get_rows("SELECT e.extname, e.extversion, n.nspname, obj_description(e.oid, 'pg_extension') AS comment
FROM pg_extension e
JOIN pg_namespace n ON n.oid = e.extnamespace
ORDER BY e.extname");if($Kd){echo"<div>\n","<h3 id='extensions'>".lang(320)."</h3>\n","<table class='odds'>\n","<thead><tr><th>".lang(214)."<th>".lang(321)."<th>".lang(88)."<th>".lang(59)."<tbody>\n";foreach($Kd
as$I)echo"<tr><th><code class='jush-pgsqlext'>".h($I["extname"])."</code>","<td>".h($I["extversion"]),"<td><a href='".h(substr(ME,0,-1).url_escape($I["nspname"]))."'>".h($I["nspname"])."</a>","<td>".h($I["comment"]),"\n";echo"</table>\n","</div>\n";}}}}page_footer();