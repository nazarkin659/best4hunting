<?php
#####################################
#                                   #
# Restore admin for Mambo/Joomla    #
# (C) Alecfyz                       #
# Gorsk.net Studio                  #
# http://support@gorsk.net          #
#                                   #
#####################################

@$myhost = $_SERVER['HTTP_HOST'];
@$myroot = $_SERVER['PATH_TRANSLATED'];
@include_once("configuration.php");
if ($myhost != str_replace ("http://","",$mosConfig_live_site) || str_replace ("/admrest.php","",$myroot) != $mosConfig_absolute_path )
{
        print "<h1>Incorrect access</h1>You cannot access this file from other host.";
        exit();
}
define( '_VALID_MOS', 1 );
@include_once("includes/database.php");
$mydb = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

$sql = "DELETE FROM #__core_acl_groups_aro_map WHERE aro_id = 10 AND group_id != 25";
@$mydb->setQuery($sql);
@$mydb->loadResult();

$sql = "SELECT group_id FROM #__core_acl_groups_aro_map WHERE aro_id = 10";
@$mydb->setQuery($sql);
$res = @$mydb->loadResult();
if ($res == null) { // нет записи для админа
echo "NO records for admin-group!<br />";
$sql = "INSERT INTO #__core_acl_groups_aro_map (group_id,section_value,aro_id ) VALUES (25, '', 10)";
@$mydb->setQuery($sql);
@$mydb->query();
echo "Record for admin-group was created: <br />".mysql_error();
        } else { // есть запись, но обновим ее
$sql = "UPDATE #__core_acl_groups_aro_map SET group_id=25,section_value='' WHERE aro_id = 10 LIMIT 1";
@$mydb->setQuery($sql);
$result = @$mydb->Query();
echo "Record for admin-group was updated: <br />".mysql_error();
        }


$sql = "UPDATE #__users SET name='Administrator',username='admin',email='".$mosConfig_mailfrom."',password='21232f297a57a5a743894a0e4a801fc3',usertype='Super Administrator',block=0,gid=25 WHERE id = 62 LIMIT 1";
@$mydb->setQuery($sql);
$result = @$mydb->Query();
if (!$result)
{
        print "<h1>DB ERROR</h1>Database error!</h1><br />".mysql_error();
        exit();
} else {
// mail to admin
$mess = "WARNING!!";
$mess .= "\n";
$mess .= "\nYour admin-password on web site ".$mosConfig_live_site." was changed on 'admin'!";
$mess .= "\n";
$mess .= "\nYou HAVE TO go in ".$mosConfig_live_site."/administrator/index.php and CHANGE your password!";
$mess .= "\n";
$mess .= "\n";
$mess .= "\nDo not reply to this letter.";
$mess .= "\nIt was generated automatically by 'AdmRest'-util ( http://support.gorsk.net ).";

@mail($mosConfig_mailfrom, $mosConfig_live_site." - Admin password changed!!!", $mess,"From: admrest@".str_replace('http://','',$mosConfig_live_site));

// ----
echo "Done!<br />Record for admin was updated<br />";
// ----
if (!@unlink ("admrest.php")) {
     if (!@rename("admrest.php","admrest.php_lock")) {
     echo "<h1>Cannot delete or rename 'admrest.php' file.<br />You have to delete it manually!</h1>";
     } else {
     echo "<h1>Cannot delete file. It was renamed to 'admrest.php_lock'.<br />You have to delete it manually!</h1>";
             }
 } else echo "File 'admrest.php' was deleted.";
}

?>