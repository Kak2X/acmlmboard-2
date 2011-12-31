<?php
  require 'lib/common.php';
  require 'lib/threadpost.php';

  loadsmilies();

  $fieldlist=''; $ufields=array('id','name','posts','regdate','lastpost','lastview','location','sex','power','rankset','title','usepic','head','sign');
  foreach($ufields as $field)
    $fieldlist.="u.$field u$field,";

  if($pid=$_GET[id])
    checknumeric($pid);
// should display an error otherwise but for now who cares
//  if ($pid) 
    $pmsgs=$sql->fetchq("SELECT $fieldlist p.*, pt.* "
                       ."FROM pmsgs p "
                       ."LEFT JOIN users u ON u.id=p.userfrom "
                       ."LEFT JOIN pmsgstext pt ON p.id=pt.id "
                       ."WHERE p.id=$pid");
    $tologuser=($pmsgs[userto]==$loguser[id]);

    if(((!$tologuser && $pmsgs[userfrom]!=$loguser[id]) && !isadmin())){
      pageheader('Error');
      print 
          "$L[TBL1]>
".        "  $L[TR2]>
".        "    $L[TD1c]>
".        "      You are not authorized to view this private message.<br>
".        "      <a href=private.php>Back to private messages</a>
".        "$L[TBLend]
";
      pagefooter();
      die();
    }elseif($tologuser && $pmsgs[unread])
      $sql->query("UPDATE pmsgs SET unread=0 WHERE id=$pid");

    pageheader($pmsgs[title]);

    $topbot=
          "$L[TBL] width=100%>
".        "  $L[TDn]><a href=./>Main</a> - <a href=private.php".(!$tologuser?"?id=$pmsgs[userto]":'').">Private messages</a> - ". htmlval($pmsgs[title]) ."</td>
".        "  $L[TDnr]>
".        "    <a href=sendprivate.php?pid=$pid>Reply</a>
".        "  </td>
".        "$L[TBLend]
";

// dumb hackery: should make another field specifically to say "this is a PM!" and make it display "Reply" instead of the crummy way I do it now
    $pmsgs[id]=0; // go away, "Link" link

    print "$topbot
".         threadpost($pmsgs,0)."
".        "$topbot
";

  pagefooter();
?>