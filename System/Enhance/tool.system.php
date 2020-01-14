<?php
declare(strict_types=1);

use function icePHP\Frame\Config\configMust;
use function icePHP\Frame\Functions\mid;

/**
 * 常用功能类
 * 可根据项目需要选择使用。
 * @codes:代码尚未整理
 */
class STool
{

    private static $utf8_gb2312 = "万与丑专业丛东丝丢两严丧个丬丰临为丽举么义乌乐乔习乡书买乱争于亏云亘亚产亩亲亵亸亿仅从仑仓仪们价众优伙会伛伞伟传伤伥伦伧伪伫体余佣佥侠侣侥侦侧侨侩侪侬俣俦俨俩俪俭债倾偬偻偾偿傥傧储傩儿兑兖党兰关兴兹养兽冁内冈册写军农冢冯冲决况冻净凄凉凌减凑凛几凤凫凭凯击凼凿刍划刘则刚创删别刬刭刽刿剀剂剐剑剥剧劝办务劢动励劲劳势勋勐勚匀匦匮区医华协单卖卢卤卧卫却卺厂厅历厉压厌厍厕厢厣厦厨厩厮县参叆叇双发变叙叠叶号叹叽吁后吓吕吗吣吨听启吴呒呓呕呖呗员呙呛呜咏咔咙咛咝咤咴咸哌响哑哒哓哔哕哗哙哜哝哟唛唝唠唡唢唣唤唿啧啬啭啮啰啴啸喷喽喾嗫呵嗳嘘嘤嘱噜噼嚣嚯团园囱围囵国图圆圣圹场坂坏块坚坛坜坝坞坟坠垄垅垆垒垦垧垩垫垭垯垱垲垴埘埙埚埝埯堑堕塆墙壮声壳壶壸处备复够头夸夹夺奁奂奋奖奥妆妇妈妩妪妫姗姜娄娅娆娇娈娱娲娴婳婴婵婶媪嫒嫔嫱嬷孙学孪宁宝实宠审宪宫宽宾寝对寻导寿将尔尘尧尴尸尽层屃屉届属屡屦屿岁岂岖岗岘岙岚岛岭岳岽岿峃峄峡峣峤峥峦崂崃崄崭嵘嵚嵛嵝嵴巅巩巯币帅师帏帐帘帜带帧帮帱帻帼幂幞干并广庄庆庐庑库应庙庞废庼廪开异弃张弥弪弯弹强归当录彟彦彻径徕御忆忏忧忾怀态怂怃怄怅怆怜总怼怿恋恳恶恸恹恺恻恼恽悦悫悬悭悯惊惧惨惩惫惬惭惮惯愍愠愤愦愿慑慭憷懑懒懔戆戋戏戗战戬户扎扑扦执扩扪扫扬扰抚抛抟抠抡抢护报担拟拢拣拥拦拧拨择挂挚挛挜挝挞挟挠挡挢挣挤挥挦捞损捡换捣据捻掳掴掷掸掺掼揸揽揿搀搁搂搅携摄摅摆摇摈摊撄撑撵撷撸撺擞攒敌敛数斋斓斗斩断无旧时旷旸昙昼昽显晋晒晓晔晕晖暂暧札术朴机杀杂权条来杨杩杰极构枞枢枣枥枧枨枪枫枭柜柠柽栀栅标栈栉栊栋栌栎栏树栖样栾桊桠桡桢档桤桥桦桧桨桩梦梼梾检棂椁椟椠椤椭楼榄榇榈榉槚槛槟槠横樯樱橥橱橹橼檐檩欢欤欧歼殁殇残殒殓殚殡殴毁毂毕毙毡毵氇气氢氩氲汇汉污汤汹沓沟没沣沤沥沦沧沨沩沪沵泞泪泶泷泸泺泻泼泽泾洁洒洼浃浅浆浇浈浉浊测浍济浏浐浑浒浓浔浕涂涌涛涝涞涟涠涡涢涣涤润涧涨涩淀渊渌渍渎渐渑渔渖渗温游湾湿溃溅溆溇滗滚滞滟滠满滢滤滥滦滨滩滪漤潆潇潋潍潜潴澜濑濒灏灭灯灵灾灿炀炉炖炜炝点炼炽烁烂烃烛烟烦烧烨烩烫烬热焕焖焘煅煳熘爱爷牍牦牵牺犊犟状犷犸犹狈狍狝狞独狭狮狯狰狱狲猃猎猕猡猪猫猬献獭玑玙玚玛玮环现玱玺珉珏珐珑珰珲琎琏琐琼瑶瑷璇璎瓒瓮瓯电画畅畲畴疖疗疟疠疡疬疮疯疱疴痈痉痒痖痨痪痫痴瘅瘆瘗瘘瘪瘫瘾瘿癞癣癫癯皑皱皲盏盐监盖盗盘眍眦眬着睁睐睑瞒瞩矫矶矾矿砀码砖砗砚砜砺砻砾础硁硅硕硖硗硙硚确硷碍碛碜碱碹磙礼祎祢祯祷祸禀禄禅离秃秆种积称秽秾稆税稣稳穑穷窃窍窑窜窝窥窦窭竖竞笃笋笔笕笺笼笾筑筚筛筜筝筹签简箓箦箧箨箩箪箫篑篓篮篱簖籁籴类籼粜粝粤粪粮糁糇紧絷纟纠纡红纣纤纥约级纨纩纪纫纬纭纮纯纰纱纲纳纴纵纶纷纸纹纺纻纼纽纾线绀绁绂练组绅细织终绉绊绋绌绍绎经绐绑绒结绔绕绖绗绘给绚绛络绝绞统绠绡绢绣绤绥绦继绨绩绪绫绬续绮绯绰绱绲绳维绵绶绷绸绹绺绻综绽绾绿缀缁缂缃缄缅缆缇缈缉缊缋缌缍缎缏缐缑缒缓缔缕编缗缘缙缚缛缜缝缞缟缠缡缢缣缤缥缦缧缨缩缪缫缬缭缮缯缰缱缲缳缴缵罂网罗罚罢罴羁羟羡翘翙翚耢耧耸耻聂聋职聍联聩聪肃肠肤肷肾肿胀胁胆胜胧胨胪胫胶脉脍脏脐脑脓脔脚脱脶脸腊腌腘腭腻腼腽腾膑臜舆舣舰舱舻艰艳艹艺节芈芗芜芦苁苇苈苋苌苍苎苏苘苹茎茏茑茔茕茧荆荐荙荚荛荜荞荟荠荡荣荤荥荦荧荨荩荪荫荬荭荮药莅莜莱莲莳莴莶获莸莹莺莼萚萝萤营萦萧萨葱蒇蒉蒋蒌蓝蓟蓠蓣蓥蓦蔷蔹蔺蔼蕲蕴薮藁藓虏虑虚虫虬虮虽虾虿蚀蚁蚂蚕蚝蚬蛊蛎蛏蛮蛰蛱蛲蛳蛴蜕蜗蜡蝇蝈蝉蝎蝼蝾螀螨蟏衅衔补衬衮袄袅袆袜袭袯装裆裈裢裣裤裥褛褴襁襕见观觃规觅视觇览觉觊觋觌觍觎觏觐觑觞触觯詟誉誊讠计订讣认讥讦讧讨让讪讫训议讯记讱讲讳讴讵讶讷许讹论讻讼讽设访诀证诂诃评诅识诇诈诉诊诋诌词诎诏诐译诒诓诔试诖诗诘诙诚诛诜话诞诟诠诡询诣诤该详诧诨诩诪诫诬语诮误诰诱诲诳说诵诶请诸诹诺读诼诽课诿谀谁谂调谄谅谆谇谈谊谋谌谍谎谏谐谑谒谓谔谕谖谗谘谙谚谛谜谝谞谟谠谡谢谣谤谥谦谧谨谩谪谫谬谭谮谯谰谱谲谳谴谵谶谷豮贝贞负贠贡财责贤败账货质贩贪贫贬购贮贯贰贱贲贳贴贵贶贷贸费贺贻贼贽贾贿赀赁赂赃资赅赆赇赈赉赊赋赌赍赎赏赐赑赒赓赔赕赖赗赘赙赚赛赜赝赞赟赠赡赢赣赪赵赶趋趱趸跃跄跖跞践跶跷跸跹跻踊踌踪踬踯蹑蹒蹰蹿躏躜躯车轧轨轩轪轫转轭轮软轰轱轲轳轴轵轶轷轸轹轺轻轼载轾轿辀辁辂较辄辅辆辇辈辉辊辋辌辍辎辏辐辑辒输辔辕辖辗辘辙辚辞辩辫边辽达迁过迈运还这进远违连迟迩迳迹适选逊递逦逻遗遥邓邝邬邮邹邺邻郁郄郏郐郑郓郦郧郸酝酦酱酽酾酿释里鉅鉴銮錾钆钇针钉钊钋钌钍钎钏钐钑钒钓钔钕钖钗钘钙钚钛钝钞钟钠钡钢钣钤钥钦钧钨钩钪钫钬钭钮钯钰钱钲钳钴钵钶钷钸钹钺钻钼钽钾钿铀铁铂铃铄铅铆铈铉铊铋铍铎铏铐铑铒铕铗铘铙铚铛铜铝铞铟铠铡铢铣铤铥铦铧铨铪铫铬铭铮铯铰铱铲铳铴铵银铷铸铹铺铻铼铽链铿销锁锂锃锄锅锆锇锈锉锊锋锌锍锎锏锐锑锒锓锔锕锖锗错锚锜锞锟锠锡锢锣锤锥锦锨锩锫锬锭键锯锰锱锲锳锴锵锶锷锸锹锺锻锼锽锾锿镀镁镂镃镆镇镈镉镊镌镍镎镏镐镑镒镕镖镗镙镚镛镜镝镞镟镠镡镢镣镤镥镦镧镨镩镪镫镬镭镮镯镰镱镲镳镴镶长门闩闪闫闬闭问闯闰闱闲闳间闵闶闷闸闹闺闻闼闽闾闿阀阁阂阃阄阅阆阇阈阉阊阋阌阍阎阏阐阑阒阓阔阕阖阗阘阙阚阛队阳阴阵阶际陆陇陈陉陕陧陨险随隐隶隽难雏雠雳雾霁霉霭靓静靥鞑鞒鞯鞴韦韧韨韩韪韫韬韵页顶顷顸项顺须顼顽顾顿颀颁颂颃预颅领颇颈颉颊颋颌颍颎颏颐频颒颓颔颕颖颗题颙颚颛颜额颞颟颠颡颢颣颤颥颦颧风飏飐飑飒飓飔飕飖飗飘飙飚飞飨餍饤饥饦饧饨饩饪饫饬饭饮饯饰饱饲饳饴饵饶饷饸饹饺饻饼饽饾饿馀馁馂馃馄馅馆馇馈馉馊馋馌馍馎馏馐馑馒馓馔馕马驭驮驯驰驱驲驳驴驵驶驷驸驹驺驻驼驽驾驿骀骁骂骃骄骅骆骇骈骉骊骋验骍骎骏骐骑骒骓骔骕骖骗骘骙骚骛骜骝骞骟骠骡骢骣骤骥骦骧髅髋髌鬓魇魉鱼鱽鱾鱿鲀鲁鲂鲄鲅鲆鲇鲈鲉鲊鲋鲌鲍鲎鲏鲐鲑鲒鲓鲔鲕鲖鲗鲘鲙鲚鲛鲜鲝鲞鲟鲠鲡鲢鲣鲤鲥鲦鲧鲨鲩鲪鲫鲬鲭鲮鲯鲰鲱鲲鲳鲴鲵鲶鲷鲸鲹鲺鲻鲼鲽鲾鲿鳀鳁鳂鳃鳄鳅鳆鳇鳈鳉鳊鳋鳌鳍鳎鳏鳐鳑鳒鳓鳔鳕鳖鳗鳘鳙鳛鳜鳝鳞鳟鳠鳡鳢鳣鸟鸠鸡鸢鸣鸤鸥鸦鸧鸨鸩鸪鸫鸬鸭鸮鸯鸰鸱鸲鸳鸴鸵鸶鸷鸸鸹鸺鸻鸼鸽鸾鸿鹀鹁鹂鹃鹄鹅鹆鹇鹈鹉鹊鹋鹌鹍鹎鹏鹐鹑鹒鹓鹔鹕鹖鹗鹘鹚鹛鹜鹝鹞鹟鹠鹡鹢鹣鹤鹥鹦鹧鹨鹩鹪鹫鹬鹭鹯鹰鹱鹲鹳鹴鹾麦麸黄黉黡黩黪黾鼋鼌鼍鼗鼹齄齐齑齿龀龁龂龃龄龅龆龇龈龉龊龋龌龙龚龛龟志制咨只里系范松没尝尝闹面准钟别闲干尽脏拼";
    private static $utf8_big5 = "萬與醜專業叢東絲丟兩嚴喪個爿豐臨為麗舉麼義烏樂喬習鄉書買亂爭於虧雲亙亞產畝親褻嚲億僅從侖倉儀們價眾優夥會傴傘偉傳傷倀倫傖偽佇體餘傭僉俠侶僥偵側僑儈儕儂俁儔儼倆儷儉債傾傯僂僨償儻儐儲儺兒兌兗黨蘭關興茲養獸囅內岡冊寫軍農塚馮衝決況凍淨淒涼淩減湊凜幾鳳鳧憑凱擊氹鑿芻劃劉則剛創刪別剗剄劊劌剴劑剮劍剝劇勸辦務勱動勵勁勞勢勳猛勩勻匭匱區醫華協單賣盧鹵臥衛卻巹廠廳曆厲壓厭厙廁廂厴廈廚廄廝縣參靉靆雙發變敘疊葉號歎嘰籲後嚇呂嗎唚噸聽啟吳嘸囈嘔嚦唄員咼嗆嗚詠哢嚨嚀噝吒噅鹹呱響啞噠嘵嗶噦嘩噲嚌噥喲嘜嗊嘮啢嗩唕喚呼嘖嗇囀齧囉嘽嘯噴嘍嚳囁嗬噯噓嚶囑嚕劈囂謔團園囪圍圇國圖圓聖壙場阪壞塊堅壇壢壩塢墳墜壟壟壚壘墾坰堊墊埡墶壋塏堖塒塤堝墊垵塹墮壪牆壯聲殼壺壼處備複夠頭誇夾奪奩奐奮獎奧妝婦媽嫵嫗媯姍薑婁婭嬈嬌孌娛媧嫻嫿嬰嬋嬸媼嬡嬪嬙嬤孫學孿寧寶實寵審憲宮寬賓寢對尋導壽將爾塵堯尷屍盡層屭屜屆屬屢屨嶼歲豈嶇崗峴嶴嵐島嶺嶽崠巋嶨嶧峽嶢嶠崢巒嶗崍嶮嶄嶸嶔崳嶁脊巔鞏巰幣帥師幃帳簾幟帶幀幫幬幘幗冪襆幹並廣莊慶廬廡庫應廟龐廢廎廩開異棄張彌弳彎彈強歸當錄彠彥徹徑徠禦憶懺憂愾懷態慫憮慪悵愴憐總懟懌戀懇惡慟懨愷惻惱惲悅愨懸慳憫驚懼慘懲憊愜慚憚慣湣慍憤憒願懾憖怵懣懶懍戇戔戲戧戰戩戶紮撲扡執擴捫掃揚擾撫拋摶摳掄搶護報擔擬攏揀擁攔擰撥擇掛摯攣掗撾撻挾撓擋撟掙擠揮撏撈損撿換搗據撚擄摑擲撣摻摜摣攬撳攙擱摟攪攜攝攄擺搖擯攤攖撐攆擷擼攛擻攢敵斂數齋斕鬥斬斷無舊時曠暘曇晝曨顯晉曬曉曄暈暉暫曖劄術樸機殺雜權條來楊榪傑極構樅樞棗櫪梘棖槍楓梟櫃檸檉梔柵標棧櫛櫳棟櫨櫟欄樹棲樣欒棬椏橈楨檔榿橋樺檜槳樁夢檮棶檢欞槨櫝槧欏橢樓欖櫬櫚櫸檟檻檳櫧橫檣櫻櫫櫥櫓櫞簷檁歡歟歐殲歿殤殘殞殮殫殯毆毀轂畢斃氈毿氌氣氫氬氳彙漢汙湯洶遝溝沒灃漚瀝淪滄渢溈滬濔濘淚澩瀧瀘濼瀉潑澤涇潔灑窪浹淺漿澆湞溮濁測澮濟瀏滻渾滸濃潯濜塗湧濤澇淶漣潿渦溳渙滌潤澗漲澀澱淵淥漬瀆漸澠漁瀋滲溫遊灣濕潰濺漵漊潷滾滯灩灄滿瀅濾濫灤濱灘澦濫瀠瀟瀲濰潛瀦瀾瀨瀕灝滅燈靈災燦煬爐燉煒熗點煉熾爍爛烴燭煙煩燒燁燴燙燼熱煥燜燾煆糊溜愛爺牘犛牽犧犢強狀獷獁猶狽麅獮獰獨狹獅獪猙獄猻獫獵獼玀豬貓蝟獻獺璣璵瑒瑪瑋環現瑲璽瑉玨琺瓏璫琿璡璉瑣瓊瑤璦璿瓔瓚甕甌電畫暢佘疇癤療瘧癘瘍鬁瘡瘋皰屙癰痙癢瘂癆瘓癇癡癉瘮瘞瘺癟癱癮癭癩癬癲臒皚皺皸盞鹽監蓋盜盤瞘眥矓著睜睞瞼瞞矚矯磯礬礦碭碼磚硨硯碸礪礱礫礎硜矽碩硤磽磑礄確鹼礙磧磣堿镟滾禮禕禰禎禱禍稟祿禪離禿稈種積稱穢穠穭稅穌穩穡窮竊竅窯竄窩窺竇窶豎競篤筍筆筧箋籠籩築篳篩簹箏籌簽簡籙簀篋籜籮簞簫簣簍籃籬籪籟糴類秈糶糲粵糞糧糝餱緊縶糸糾紆紅紂纖紇約級紈纊紀紉緯紜紘純紕紗綱納紝縱綸紛紙紋紡紵紖紐紓線紺絏紱練組紳細織終縐絆紼絀紹繹經紿綁絨結絝繞絰絎繪給絢絳絡絕絞統綆綃絹繡綌綏絛繼綈績緒綾緓續綺緋綽緔緄繩維綿綬繃綢綯綹綣綜綻綰綠綴緇緙緗緘緬纜緹緲緝縕繢緦綞緞緶線緱縋緩締縷編緡緣縉縛縟縝縫縗縞纏縭縊縑繽縹縵縲纓縮繆繅纈繚繕繒韁繾繰繯繳纘罌網羅罰罷羆羈羥羨翹翽翬耮耬聳恥聶聾職聹聯聵聰肅腸膚膁腎腫脹脅膽勝朧腖臚脛膠脈膾髒臍腦膿臠腳脫腡臉臘醃膕齶膩靦膃騰臏臢輿艤艦艙艫艱豔艸藝節羋薌蕪蘆蓯葦藶莧萇蒼苧蘇檾蘋莖蘢蔦塋煢繭荊薦薘莢蕘蓽蕎薈薺蕩榮葷滎犖熒蕁藎蓀蔭蕒葒葤藥蒞蓧萊蓮蒔萵薟獲蕕瑩鶯蓴蘀蘿螢營縈蕭薩蔥蕆蕢蔣蔞藍薊蘺蕷鎣驀薔蘞藺藹蘄蘊藪槁蘚虜慮虛蟲虯蟣雖蝦蠆蝕蟻螞蠶蠔蜆蠱蠣蟶蠻蟄蛺蟯螄蠐蛻蝸蠟蠅蟈蟬蠍螻蠑螿蟎蠨釁銜補襯袞襖嫋褘襪襲襏裝襠褌褳襝褲襇褸襤繈襴見觀覎規覓視覘覽覺覬覡覿覥覦覯覲覷觴觸觶讋譽謄訁計訂訃認譏訐訌討讓訕訖訓議訊記訒講諱謳詎訝訥許訛論訩訟諷設訪訣證詁訶評詛識詗詐訴診詆謅詞詘詔詖譯詒誆誄試詿詩詰詼誠誅詵話誕詬詮詭詢詣諍該詳詫諢詡譸誡誣語誚誤誥誘誨誑說誦誒請諸諏諾讀諑誹課諉諛誰諗調諂諒諄誶談誼謀諶諜謊諫諧謔謁謂諤諭諼讒諮諳諺諦謎諞諝謨讜謖謝謠謗諡謙謐謹謾謫譾謬譚譖譙讕譜譎讞譴譫讖穀豶貝貞負貟貢財責賢敗賬貨質販貪貧貶購貯貫貳賤賁貰貼貴貺貸貿費賀貽賊贄賈賄貲賃賂贓資賅贐賕賑賚賒賦賭齎贖賞賜贔賙賡賠賧賴賵贅賻賺賽賾贗讚贇贈贍贏贛赬趙趕趨趲躉躍蹌蹠躒踐躂蹺蹕躚躋踴躊蹤躓躑躡蹣躕躥躪躦軀車軋軌軒軑軔轉軛輪軟轟軲軻轤軸軹軼軤軫轢軺輕軾載輊轎輈輇輅較輒輔輛輦輩輝輥輞輬輟輜輳輻輯轀輸轡轅轄輾轆轍轔辭辯辮邊遼達遷過邁運還這進遠違連遲邇逕跡適選遜遞邐邏遺遙鄧鄺鄔郵鄒鄴鄰鬱郤郟鄶鄭鄆酈鄖鄲醞醱醬釅釃釀釋裏钜鑒鑾鏨釓釔針釘釗釙釕釷釺釧釤鈒釩釣鍆釹鍚釵鈃鈣鈈鈦鈍鈔鍾鈉鋇鋼鈑鈐鑰欽鈞鎢鉤鈧鈁鈥鈄鈕鈀鈺錢鉦鉗鈷缽鈳鉕鈽鈸鉞鑽鉬鉭鉀鈿鈾鐵鉑鈴鑠鉛鉚鈰鉉鉈鉍鈹鐸鉶銬銠鉺銪鋏鋣鐃銍鐺銅鋁銱銦鎧鍘銖銑鋌銩銛鏵銓鉿銚鉻銘錚銫鉸銥鏟銃鐋銨銀銣鑄鐒鋪鋙錸鋱鏈鏗銷鎖鋰鋥鋤鍋鋯鋨鏽銼鋝鋒鋅鋶鐦鐧銳銻鋃鋟鋦錒錆鍺錯錨錡錁錕錩錫錮鑼錘錐錦鍁錈錇錟錠鍵鋸錳錙鍥鍈鍇鏘鍶鍔鍤鍬鍾鍛鎪鍠鍰鎄鍍鎂鏤鎡鏌鎮鎛鎘鑷鐫鎳鎿鎦鎬鎊鎰鎔鏢鏜鏍鏰鏞鏡鏑鏃鏇鏐鐔钁鐐鏷鑥鐓鑭鐠鑹鏹鐙鑊鐳鐶鐲鐮鐿鑔鑣鑞鑲長門閂閃閆閈閉問闖閏闈閑閎間閔閌悶閘鬧閨聞闥閩閭闓閥閣閡閫鬮閱閬闍閾閹閶鬩閿閽閻閼闡闌闃闠闊闋闔闐闒闕闞闤隊陽陰陣階際陸隴陳陘陝隉隕險隨隱隸雋難雛讎靂霧霽黴靄靚靜靨韃鞽韉韝韋韌韍韓韙韞韜韻頁頂頃頇項順須頊頑顧頓頎頒頌頏預顱領頗頸頡頰頲頜潁熲頦頤頻頮頹頷頴穎顆題顒顎顓顏額顳顢顛顙顥纇顫顬顰顴風颺颭颮颯颶颸颼颻飀飄飆飆飛饗饜飣饑飥餳飩餼飪飫飭飯飲餞飾飽飼飿飴餌饒餉餄餎餃餏餅餑餖餓餘餒餕餜餛餡館餷饋餶餿饞饁饃餺餾饈饉饅饊饌饢馬馭馱馴馳驅馹駁驢駔駛駟駙駒騶駐駝駑駕驛駘驍罵駰驕驊駱駭駢驫驪騁驗騂駸駿騏騎騍騅騌驌驂騙騭騤騷騖驁騮騫騸驃騾驄驏驟驥驦驤髏髖髕鬢魘魎魚魛魢魷魨魯魴魺鮁鮃鯰鱸鮋鮓鮒鮊鮑鱟鮍鮐鮭鮚鮳鮪鮞鮦鰂鮜鱠鱭鮫鮮鮺鯗鱘鯁鱺鰱鰹鯉鰣鰷鯀鯊鯇鮶鯽鯒鯖鯪鯕鯫鯡鯤鯧鯝鯢鯰鯛鯨鯵鯴鯔鱝鰈鰏鱨鯷鰮鰃鰓鱷鰍鰒鰉鰁鱂鯿鰠鼇鰭鰨鰥鰩鰟鰜鰳鰾鱈鱉鰻鰵鱅鰼鱖鱔鱗鱒鱯鱤鱧鱣鳥鳩雞鳶鳴鳲鷗鴉鶬鴇鴆鴣鶇鸕鴨鴞鴦鴒鴟鴝鴛鴬鴕鷥鷙鴯鴰鵂鴴鵃鴿鸞鴻鵐鵓鸝鵑鵠鵝鵒鷳鵜鵡鵲鶓鵪鶤鵯鵬鵮鶉鶊鵷鷫鶘鶡鶚鶻鶿鶥鶩鷊鷂鶲鶹鶺鷁鶼鶴鷖鸚鷓鷚鷯鷦鷲鷸鷺鸇鷹鸌鸏鸛鸘鹺麥麩黃黌黶黷黲黽黿鼂鼉鞀鼴齇齊齏齒齔齕齗齟齡齙齠齜齦齬齪齲齷龍龔龕龜誌製谘隻裡係範鬆冇嚐嘗鬨麵準鐘彆閒乾儘臟拚";
    private static $reader;

    // DES 3 解密

    /**
     * 美化存储容量数字的格式,K,M,G,T
     *
     * @param int $bytes 要转换的数值
     * @param int $precision 精度
     * @return string 转换成KMGT之后的字符串
     */
    static public function kmgt($bytes, $precision = 1)
    {
        $units = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $factor = 1;

        foreach ($units as $unit) {
            if ($bytes < $factor * 1024) {
                return number_format($bytes / $factor, $factor > 1 ? $precision : 0) . ' ' . $unit;
            }
            $factor *= 1024;
        }

        $factor /= 1024;
        return number_format($bytes / $factor, $precision) . ' Y';
    }

    /**
     * DES3加密
     * @return string
     */
    static public function des3($key, $input, $mode = 'ecb')
    {
        $size = intval(mcrypt_get_block_size(MCRYPT_3DES));

        $pad = $size - strlen($input) % $size;

        $input .= str_repeat(chr($pad), $pad);

        $td = mcrypt_module_open(MCRYPT_3DES, '', $mode, '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    static public function des3_decrypt($key, $str, $mode = 'ecb')
    {
        $cipher = mcrypt_module_open(MCRYPT_3DES, '', $mode, '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
        if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
            $decrypted_data = mdecrypt_generic($cipher, $str);
            mcrypt_generic_deinit($cipher);
        }
        mcrypt_module_close($cipher);
        return self::pkcs5_unpad($decrypted_data);
    }

    private static function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }

    /**
     * 判断一个字符串是UTF8编码
     *
     * @param string $string
     * @return bool
     */
    public static function is_utf8($string)
    {
        $matched = (preg_match(
                "/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" .
                chr(191) . "]{1}[" . chr(128) . "-" . chr(191) .
                "]{1}){1}/", $string) == true || preg_match(
                "/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" .
                chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/",
                $string) == true || preg_match(
                "/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" .
                chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/",
                $string) == true);
        return $matched;
    }

    /**
     * 判断一个字符串是GB编码
     *
     * @param string $str
     * @return boolean
     */
    public static function is_gb2312($str)
    {
        for ($i = 0; $i < strlen($str); $i++) {
            $v = ord($str[$i]);
            if ($v > 127) {
                if (($v >= 228) && ($v <= 233)) {
                    if (($i + 2) >= strlen($str)) {
                        return true;
                    }
                    $v1 = ord($str[$i + 1]);
                    $v2 = ord($str[$i + 2]);
                    if (($v1 >= 128) && ($v1 <= 191) && ($v2 >= 128) &&
                        ($v2 <= 191)
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }
        return true;
    }

    /**
     * 判断一个字符串不是中文
     *
     * @param string $str
     * @return number
     */
    public static function is_not_chinese($str)
    {
        $matched = preg_match('/^(?:[\x00-\xBF])*$/XS', $str);
        return $matched;
    }

    /**
     * 简体转繁体
     *
     * @param string $str
     * @return string
     */
    public static function simplified2traditional($str)
    {
        $str_t = '';
        $len = strlen($str);
        $a = 0;
        while ($a < $len) {
            if (ord($str{$a}) >= 224 && ord($str{$a}) <= 239) {
                if (($temp = strpos(self::$utf8_gb2312,
                        $str{$a} . $str{$a + 1} . $str{$a + 2})) !== false
                ) {
                    $str_t .= self::$utf8_big5{$temp} .
                        self::$utf8_big5{$temp + 1} .
                        self::$utf8_big5{$temp + 2};
                    $a += 3;
                    continue;
                }
            }
            $str_t .= $str{$a};
            $a += 1;
        }
        return $str_t;
    }

    /**
     * 繁体转简体
     *
     * @param string $str
     * @return string
     */
    public static function traditional2simplified($str)
    {
        $str_t = '';
        $len = strlen($str);
        $a = 0;
        while ($a < $len) {
            if (ord($str{$a}) >= 224 && ord($str{$a}) <= 239) {
                if (($temp = strpos(self::$utf8_big5,
                        $str{$a} . $str{$a + 1} . $str{$a + 2})) !== false
                ) {
                    $str_t .= self::$utf8_gb2312{$temp} .
                        self::$utf8_gb2312{$temp + 1} .
                        self::$utf8_gb2312{$temp + 2};
                    $a += 3;
                    continue;
                }
            }
            $str_t .= $str{$a};
            $a += 1;
        }
        return $str_t;
    }

    /**
     * 计算两个字符串之间的差别
     *
     * @param string $a
     * @param string $b
     * @return number 0 表示无差别, 100表示完全不同,偶尔会超出100. 对付用吧
     */
    public static function difference($a, $b)
    {
        $p = 0;
        similar_text($a, $b, $p);
        return round(100 - $p, 2);
    }

    /**
     * 数组转换,将 [['a'=>x1,'b=>y1],['a'=>x2,'b'=>y2],...] 转换为[x1=>y1,x2=>y2,...]
     * @deprecated 本方法废弃,请使用原生函数array_column
     * @param array $arr 原二维 数组
     * @param string $k 键名
     * @param string $v 值名
     * @return array
     */
    public static function array2kv(array $arr, $k, $v)
    {
        return array_column($arr, $v, $k);
//        // 保存结果的数组
//        $result = array();
//
//        // 逐个处理
//        foreach ($arr as $a) {
//            $result[$a[$k]] = $a[$v];
//        }
//
//        return $result;
    }

    /**
     * 计算两个坐标之间的距离
     *
     * @param float $x1 经度1
     * @param float $y1 纬度1
     * @param float $x2 经度2
     * @param float $y2 纬度2
     * @return float 距离(米)
     */
    static public function distance($x1, $y1, $x2, $y2)
    {
        $p = pi() / 180;
        return round(
            6370996.81 * acos(
                cos($y1 * $p) * cos($y2 * $p) * cos($x1 * $p - $x2 * $p) +
                sin($y1 * $p) * sin($y2 * $p)));
    }

    /**
     * session管理
     *
     * @param string $name session名称
     * @param mixed $value session值
     * @return mixed
     */
    static public function session($name = '', $value = '')
    {
        // web端前缀
        $prefix = 'web';
        if ('' === $value) {
            if ('' === $name) {
                // 获取全部的session
                return isset($_SESSION[$prefix]) ? $_SESSION[$prefix] : $_SESSION;
            } elseif (is_null($name)) { // 清空session
                if ($prefix) {
                    unset($_SESSION[$prefix]);
                } else {
                    $_SESSION = array();
                }
            } elseif ($prefix) { // 获取web端session
                return isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
            } else { // 获取全局session
                return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        } elseif (is_null($value)) { // 删除session
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        } else { // 设置session
            if ($prefix) {
                $_SESSION[$prefix][$name] = $value;
            } else {
                $_SESSION[$name] = $value;
            }
        }
        return null;
    }

    /**
     * 校验密码合法性
     *
     * @param string $password 需校验的密码字符串
     * @return bool true/false
     */
    static public function checkPassword($password)
    {
        $regEx = configMust('Application', 'passwordRegEx');

        $num = preg_match('/^\d+$/i', $password);
        $strl = preg_match('/^[a-z]+$/i', $password);
        $stru = preg_match('/^[A-Z]+$/i', $password);
        $symbol = preg_match('/^[\@\!\#\$\%\^\&\*\.\~]+$/i', $password);

        //纯数字纯字母纯符号
        if ($num || $strl || $stru || $symbol)
            return false;

        return preg_match($regEx, $password) ? true : false;
    }

    /**
     * 转换距离
     *
     * @param int $distance 距离
     * @return string 返回 km / m
     */
    static public function distanceCalc($distance)
    {
        $distance = (int)$distance;
        if ($distance < 1000)
            return $distance . "m";

        $distance = (int)($distance / 1000);
        return $distance . "km";
    }

    /**
     * 对象数组转换,将 [['a'=>x1,'b=>y1],['a'=>x2,'b'=>y2],...] 转换为[x1=>y1,x2=>y2,...]
     *
     * @param array $arr 原二维 数组
     * @param string $k 键名
     * @param string $v 值名
     * @return array
     */
    public static function objectArray2kv(array $arr, $k, $v)
    {
        // 保存结果的数组
        $result = array();

        // 逐个处理
        foreach ($arr as $a) {
            if (is_array($a)) {
                $result[$a[$k]] = $a[$v];
            } else {
                $result[$a->$k] = $a->$v;
            }
        }

        return $result;
    }

    /**
     * 将无键数组转换为指定值的有键数组
     *
     * @param array $arr 源数组
     * @param string $key 定义为键的列名
     * @return array
     */
    static public function arrayWithKey($arr, $key)
    {
        $ret = array();
        foreach ($arr as $v) {
            $ret[$v[$key]] = $v;
        }
        return $ret;
    }

    /**
     * 将无键数组转换为指定值的二维有键数组
     * 同一键有多个记录
     *
     * @param array $arr 源数组
     * @param string $key 定义为键的列名
     * @return array
     */
    static public function arrayMultiWithKey($arr, $key)
    {
        $ret = array();
        foreach ($arr as $v) {
            $k = $v[$key];
            if (!isset($ret[$k])) {
                $ret[$k] = array();
            }
            $ret[$k][] = $v;
        }
        return $ret;
    }

    /**
     * 日期运算,加减几天
     *
     * @param string $day
     * @param int $n
     * @return string
     */
    static public function dateAdd($day, $n = 1)
    {
        if ($n > 0)
            return date('Y-m-d', strtotime('+' . $n . ' day', strtotime($day)));
        return date('Y-m-d', strtotime($n . ' day', strtotime($day)));
    }

    /**
     * 计算下一天的日期
     *
     * @param string $day
     * @param int $n
     * @return string
     */
    static public function nextDay($day = '', $n = 1)
    {
        // 日期的默认值为今天
        if (!$day) {
            $day = date('Y-m-d');
        }

        if ($n > 0)
            return date('Y-m-d', strtotime('+' . $n . ' day', strtotime($day)));

        return date('Y-m-d', strtotime($n . ' day', strtotime($day)));
    }

    /**
     * 判断点在多边形中
     *
     * @param array $point 点坐标(x,y)
     * @param array $poly 多边形数组 [(x,y),...]
     * @return boolean
     */
    static public function pointInPoly(array $point, array $poly)
    {
        // 最后一个点
        $last = $poly[count($poly) - 1];
        $in = 0;

        // 点坐标
        list ($x, $y) = $point;
        foreach ($poly as $side) {
            // 线段
            list ($x1, $y1) = $side;
            list ($x2, $y2) = $last;

            if ($x1 == $x and $y1 == $y or $x2 == $x and $y2 == $y)
                return true;

            if ($y1 < $y && $y2 >= $y || $y1 >= $y && $y2 < $y) {
                $xx = $x1 + ($y - $y1) * ($x2 - $x1) / ($y2 - $y1);
                if ($xx == $x)
                    return TRUE;
                if ($xx > $x)
                    $in = !$in;
            }
            $last = $side;
        }
        return $in;
    }

    /**
     * 生成指定长度的随机字符串(大写,小写,数字)
     * @param int $length
     * @return string
     */
    static public function randomString($length = 32)
    {
        // Create token to login with
        $t1 = '';
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i < $length; $i++) {
            $j = rand(0, 61);
            $t1 .= $string[$j];
        }
        return $t1;
    }

    /**
     * 生成随机数字串
     * @param int $length 长度
     * @return string
     */
    static public function randomNumber($length = 8)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $r = rand(0, 9);
            $result .= $r;
        }
        return $result;
    }

    /**
     * 判断是否包含中文
     * @param $str string 要判断 的字符串
     * @return bool
     */
    public static function hasCN($str)
    {
        return preg_match('/[\x{4e00}-\x{9fa5}]/ui', $str);
    }

    /**
     * 根据生日日期获取年龄
     * @param $date string 生日日期 eg:1989-10-29
     * @return int OR ''
     */
    static public function getAgeFromBirthday($date)
    {
        if (empty($date) || $date == '0000-00-00') {
            return '';
        }

        list($y1, $m1, $d1) = explode("-", $date);

        $now = strtotime("now");
        list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));

        $age = $y2 - $y1;

        if ((int)($m2 . $d2) < (int)($m1 . $d1)) {
            $age -= 1;
        }

        return $age;
    }

    /**
     * 按汉字拼音排序
     * @param array $arr
     * @return array
     */
    static public function sortPinyin(array $arr)
    {
        collator_sort(collator_create('zh-CN'), $arr);
        return $arr;
    }

    /**
     * 以￥12,345,678.99 格式输出金额
     * @param $money number
     * @return string
     */
    static public function money($money)
    {
        return numfmt_format_currency(numfmt_create('zh-CN', NumberFormatter ::CURRENCY), $money, 'CNY');
    }

    static function get_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        return json_decode($output, true);
//        echo 'CURL ERROR: ' . curl_errno($ch);
//        echo '<br>';
//        echo 'CURL ERROR: ' . curl_getinfo($ch);
//        echo '<br>';
    }

    static function post_curl($url, $para)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($para));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($para))
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        return $result;

    }

    /**
     * 加密hash_hmac
     * @param $para
     * @param $key
     * @return string
     * @Author 阿顿
     * @Version 1.3.0.1版
     */
    public static function getSignature($para, $key)
    {
        //按照字段排序 升序
        ksort($para);

        //去掉转义符的  转成json
        $str = stripslashes(json_encode($para,JSON_UNESCAPED_UNICODE));

        //加密
        if (function_exists('hash_hmac')) {
            $signature = bin2hex(hash_hmac("sha1", $str, $key, true));
        } else {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) > $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $str))));
            $signature = bin2hex($hmac);
        }
        return $signature;
    }

    static function send_post($url, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * 获取手机号码对应的城市信息
     *
     * @param string $mobile 手机号码
     * @return array
     */
    static public function mobileCity($mobile)
    {
        $url = 'https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=' . $mobile;
        $ret = Requests::get($url)->body;
        $province = iconv('gb2312', 'utf-8', mid($ret, "province:'", "'"));
        $catname = iconv('gb2312', 'utf-8', mid($ret, "catname:'", "'"));
        $carrier = iconv('gb2312', 'utf-8', mid($ret, "carrier:'", "'"));
        return ['province' => $province, 'catName' => $catname, 'carrier' => $carrier];
    }

    /**
     * 百度API解析地址
     * @param string $address 要解析的地址 "北京市朝阳区八里庄..."
     * @return array|bool 解析结果(经纬度),失败返回false
     */
    static public function addressGeoCoder($address)
    {
        //需要APPKEY

        // 百度API接口要求src必须指定 , AppName
        $api = "http://api.map.baidu.com/geocoder?address=$address&output=json&src=jag";
        $info = json_decode(Requests::get($api)->body);
        if ($info->status !== "OK" || empty($info->result)) return false;
        return get_object_vars($info->result->location);
    }

    /**
     * 百度API解析经纬度
     *
     * @param string $lat 纬度
     * @param string $lng 经度
     * @return array|bool 解析结果
     */
    static public function lalGeoCoder($lat, $lng)
    {
        // 百度API接口要求src必须指定 , AppName
        $api = "http://api.map.baidu.com/geocoder?location=$lat,$lng&coord_type=gcj02&output=json&src=jag";
        $info = json_decode(Requests::get($api)->body);
        if ($info->status !== "OK" || empty($info->result->formatted_address)) return false;

        // 返回信息
        $data = array_merge(get_object_vars($info->result->location), get_object_vars($info->result->addressComponent));
        $data['address'] = $info->result->formatted_address;
        $data['cityCode'] = $info->result->cityCode;
        return $data;
    }

    /**
     * 百度API解析IP地址
     *
     * @param string $ip IP地址
     * @return array|bool 解析结果
     */
    static public function ipGeoCoder($ip)
    {
        $api = "http://api.map.baidu.com/location/ip?ak=7b1f0f9d13ec260c8c38e869b3829513&ip=$ip&coor=bd09ll";
        $info = json_decode(Requests::get($api)->body);
        if (empty($info->content->address)) return false;

        // 返回信息
        return get_object_vars($info->content->address_detail);
    }

    /**
     * 判断指定IP是否来源于欧盟成员国
     * @param string $ip
     * @return bool
     */
    static public function inEu(string $ip): bool
    {
        return in_array(self::ip2country($ip), [
            'Denmark',
            'Bulgaria',
            'Croatia',
            'Hungary',
            'Luxembourg',
            'Cyprus',
            'Austria',
            'Greece',
            'Germany',
            'Italy',
            'Latvia',
            'Czechia',
            'Slovakia',
            'Slovenia',
            'Belgium',
            'France',
            'Poland',
            'Ireland',
            'Estonia',
            'Sweden',
            'Republic of Lithuania',
            'Romania',
            'Finland',
            'Netherlands',
            'Portugal',
            'Spain',
            'Malta'
        ]);
    }

    /**
     * 判断指定IP的来源国家
     * @param string $ip
     * @return string
     */
    static public function ip2country(string $ip): string
    {
        if (!self::$reader) {
            self::$reader = new MaxMind\Db\Reader(DIR_ROOT . 'System/Enhance/GeoIP2-Country.mmdb');
        }

        return self::$reader->get($ip)['country']['names']['en'];
    }

    /**
     * 生成位置变换表
     * @return array
     */
    private function codeMap()
    {
        //单例
        $map = [];
        if ($map) {
            return $map;
        }

        $sum = 0;
        for ($i = 0; $i < 40; $i++) {

            //Hash计算,重定位
            $sum += $i * 37;
            $j = $sum % 40;

            //冲突后延
            while (true) {
                if (!isset($map[$j])) {
                    $map[$j] = $i;
                    break;
                }

                $j = ($j + 1) % 40;
            }
        }

        return $map;
    }

    /**
     * 对编号进行编码(0-9A-Z)  _ 为老人生成8位数唯一编码
     * @param int $code
     * @return string
     */
    public function encode(int $code): string
    {
        $bin = [];

        //转换成二进制,并每四位加一位校验
        //最大32位,分8段
        for ($i = 0; $i < 8; $i++) {
            $xor = 0;

            //每段4位
            for ($j = 0; $j < 4; $j++) {

                //最低位入数组
                $bin[] = $bit = $code & 1;
                $code >>= 1;

                //计算校验
                $xor ^= $bit;
            }

            //校验入数组
            $bin[] = $xor;
        }


        //进行位置变换
        $encode = [];
        foreach ($this->codeMap() as $k => $v) {
            $encode[$k] = $bin[$v];
        }
        ksort($encode);

        //生成8位字符
        $result = '';
        for ($i = 0; $i < 8; $i++) {

            //每5位合并成一个整数
            $seg = 0;
            for ($j = 0; $j < 5; $j++) {
                $seg <<= 1;
                $seg += array_shift($encode);
            }

            //从字符表取对应字符
            $result .= $this->codelist{$seg};
        }

        return $result;
    }

    /**
     * 编号解码为整数 _ 为老人生成8位数唯一编码
     * @param string $code
     * @return int
     */
    public function decode(string $code): int
    {
        //8个字符
        $bin = [];
        for ($i = 0; $i < 8; $i++) {

            //字符转为整数(0-31)
            $seg = stripos($this->codelist, $code{$i});

            //转换为2进制,高位先出
            for ($j = 0; $j < 5; $j++) {
                $bin[] = ($seg & 16) ? 1 : 0;
                $seg <<= 1;
            }
        }

        //进行位置恢复
        $decode = [];
        foreach ($this->codeMap() as $k => $v) {
            $decode[$v] = $bin[$k];
        }
        ksort($decode);

        //计算最后的原码
        $code = 0;

        //分8段,每段5位
        for ($i = 0; $i < 8; $i++) {
            $xor = 0;

            //第5位为校验码
            $check = array_pop($decode);

            //每次4位
            for ($j = 0; $j < 4; $j++) {
                $bit = array_pop($decode);
                $xor ^= $bit;
                $code <<= 1;
                $code += $bit;
            }

            //校验失败,返回0
            if ($check != $xor) {
                return 0;
            }
        }

        return $code;
    }

    //字符表
    private $codelist = '0123456789ABCDEFGHKLMNPQRSTUVWXYZ';



}




/**
 * 获取域名 http://www.php-liyong.com:80
 * @return string
 */
function domain()
{
    /* 协议 */
    $protocol = (isset($_SERVER['HTTPS']) and (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

    $host = $_SERVER['HTTP_HOST'];

    /* 端口 */
    if (isset($_SERVER['SERVER_PORT'])) {
        $port = ':' . $_SERVER['SERVER_PORT'];

        if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
            $port = '';
        }
    } else {
        $port = '';
    }

    return $protocol . $host . $port;
}