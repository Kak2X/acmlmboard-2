<?php
    require "lib/common.php";
    pageheader("Rankset Listing");
   
    $getrankset = $_GET['rankset']; // Changed to allow the Kirby Rank to show.
    if (!is_numeric($getrankset)) $getrankset = 1; //Double checking.. 
    if ($getrankset < 1 || $getrankset > 3) $getrankset = 1; //Should be made dynamic based on rank sets.

    $linkuser = array();
    $allusers = $sql->query("SELECT `id`, `name`, `displayname`, `posts`, `minipic` FROM `users` WHERE `rankset` = ".$getrankset." ORDER BY `id`");
   //$linkuser = $sql->fetchq($allusers);
    /*while ($user2 = $sql->fetchq($allusers))
     {;
      print "$user2[id]";
      $linkuser[$user2['id']] = $user2;
     }*/
    while ($row = mysql_fetch_array($allusers, MYSQL_ASSOC)) 
    {
      //printf("ID: %s  Name: %s Post: %s", $row[id], $row[name], $row[posts]); 
      $linkuser[$row['id']] = $row;
    }
    $blockunknown = true;

    $rankposts = array();
    
   if (has_perm("view-allranks") || has_perm("edit-ranks"))
    {
     $linktoggle = "1\">View";
    if ($_GET['viewall'] == 1)
     {
      $blockunknown = false;
      $linktoggle = "0\">Hide";
     }
     $linkviewall = " | <a href=\"ranks.php?rankset=$getrankset&viewall=$linktoggle All Hidden</a>";
    }
    $editlinks = "";
   if (/*has_perm("edit-ranks")*/false)
    {
    if ($getrankset != 1)
     {
      $deletelink = " |  
                   <a href=\"ranks.php?action=deleterankset&rankset=$getrankset&token=".securitytoken("deleterankset")."\">Delete Rank</a>";
     }
     $editlinks = " | 
                   <a href=\"ranks.php?action=addrankset\">Add Rank</a> | 
                   <a href=\"ranks.php?action=editrankset&rankset=$getrankset\">Edit Rank</a>$deletelink";
    }
    
    $allranks = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       ORDER BY `p`");
    $ranks    = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       WHERE `rs`='$getrankset' ORDER BY `p`");
                       
   while($rank = $sql->fetch($allranks))
    {
    if ($rank['rs'] == $getrankset)
      $rankposts[] = $rank['p'];
    if (!$rankselection)
      $rankselection .= "<a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
    else
     {
     if ($usedranks[$rank['rs']] != true)
      $rankselection .= " | <a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
     }
     $usedranks[$rank['rs']] = true;
    }
                          
    print "$L[TBL]>
             $L[TR]>
               <td>
                 $L[TBL1]>
                   $L[TRh]>
                     $L[TD1] width=\"50%\">Rank Set</td>
                   </tr>
                   $L[TR1]>
                     $L[TD1]>$rankselection$linkviewall$editlinks</td>
                   </tr>
                 </table>
               </td>
             </tr>
           </table><br>
           $L[TBL1]>
            $L[TRh]>
               $L[TD] width=\"150px\">Rank</td>
               $L[TD] width=\"50px\">Posts</td>
               $L[TD] width=\"100px\">Users On Rank</td>
               $L[TD]>Users On Rank</td>
             </tr>";
    
    $i = 1;

   while($rank = $sql->fetch($ranks))
    {
     $neededposts     = $rank['p'];
     $nextneededposts = $rankposts[$i];
     $usercount       = 0;
  //$allusers = $sql->query('SELECT `id`, `name`, `displayname`, `posts` FROM `users` ORDER BY `id`');
    foreach ($linkuser as $user)
     {
      //print "$user[id] moo $user[name] <br>";
      $climbingagain = "";
      $postcount = $user['posts'];
     if ($postcount > 5100)
      {
       $postcount = $postcount - 5100;
       $climbingagain = " (Climbing Again (5100))";
      }
      //print "$user[name]: ($postcount => $neededposts) && ($postcount < $nextneededposts)<br>";
     if (($postcount >= $neededposts) && ($postcount < $nextneededposts))
      {
      if ($usersonthisrank)
        $usersonthisrank .= ", ";
       //$usersonthisrank .= linkuser($user['id']).$climbingagain;
       $usersonthisrank .= "<img style='vertical-align:text-bottom' src='".$user['minipic']."'/> ".userlink_by_id($user['id']).$climbingagain;
       $usercount++;
      }
     }
    if ($rank['image'])
     {
      $rankimage .= "<img src=\"img/ranksets/$rank[dirname]/$rank[image]\">";
     }
     print "
             $L[TR]>
               $L[TD1]>".($usercount || $blockunknown == false ? "$rank[str]" : "???")."</td>
               $L[TD2c]>".($usercount || $blockunknown == false ? "$neededposts" : "???")."</td>
               $L[TD2c]>$usercount</td>
               $L[TD1c]>$usersonthisrank</td>
             </tr>";

     //"<!--$rankset[neededposts] $rankset[title] $rankimage<br>-->\n";
     unset($rankimage, $usersonthisrank);
     $i++;
    }
    
    /*
   while($rankset = fetchquery($allranksets, true))
    {
     $neededposts = $rankset['neededposts'];
    foreach ($linkuser as $user)
     {
      $postcount = $user['postcount'];
     if (($neededposts - $postcount) > 0)
     }
    if ($rankset['image'])
      $rankimage = "<img src=\"img/ranksets/$rankset[dirname]/$rankset[image]\">";
     print "
             $L[TR]>
               $L[TD1]>$rankset[title]<br>$rankimage</td>
               $L[TD]>$neededposts</td>
               $L[TD] width=\"100px\">(amount of users who rank this)</td>
               $L[TD]>$usersonthisrank</td>
             </tr>";

     //"<!--$rankset[neededposts] $rankset[title] $rankimage<br>-->\n";
     unset($rankimage);
    }
    */
    print "</table>";
    pagefooter();
 ?>
