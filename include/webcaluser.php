<?php
//this class use to access webcal db
//add update and change password
require_once("dbconfig.php");
class webcal extends webcaldb {
/**
 * Add a new user.
 *
 * @param string $user      User login
 * @param string $password  User password
 * @param string $firstname User first name
 * @param string $lastname  User last name
 * @param string $email     User email address
 * @param string $admin     Is the user an administrator? ('Y' or 'N')
 *
 * @return bool True on success
 *
 * @global string Error message
 */
public function add_user ( $user, $password, $firstname,
  $lastname, $email, $admin, $enabled='Y' ) {
  GLOBAL $cal_error;
  if ( strlen ( $email ) )
    $uemail = $email;
  else
    $uemail = NULL;
  if ( strlen ( $firstname ) )
    $ufirstname = $firstname;
  else
    $ufirstname = NULL;
  if ( strlen ( $lastname ) )
    $ulastname = $lastname;
  else
    $ulastname = NULL;
  if ( strlen ( $password ) )
    $upassword = md5 ( $password );
  else
    $upassword = NULL;
  if ( $admin != 'Y' )
    $admin = 'N';
  $sql = "INSERT INTO webcal_user 
         ( cal_login, cal_lastname, cal_firstname,
           cal_is_admin, cal_passwd, cal_email, cal_enabled ) VALUES ( '$user', '$ulastname',
           '$ufirstname', '$admin', '$upassword', '$uemail', '$enabled' )";
  if ( ! mysqli_query(parent::$db, $sql)) {
    $cal_error = mysqli_error(parent::$db);
    return false;
  }
  $sqlgroup = "INSERT INTO webcal_group_user (cal_group_id, cal_login) 
               VALUES ('100','$user')";
  if ( ! mysqli_query(parent::$db, $sqlgroup)) {
    $cal_error = mysqli_error(parent::$db);
    return false;
  }
  mysqli_close(parent::$db);
  return true;
}

/**
 * Update a user.
 *
 * @param string $user      User login
 * @param string $firstname User first name
 * @param string $lastname  User last name
 * @param string $mail      User email address
 * @param string $admin     Is the user an administrator? ('Y' or 'N')
 * @param string $enabled   Is the user account enabled? ('Y' or 'N')
 *
 * @return bool True on success
 *
 * @global string Error message
 */
public function update_user ( $user, $firstname, $email,
  $admin, $enabled='Y' ) {
  global $cal_error;
  if ( strlen ( $email ) )
    $uemail = $email;
  else
    $uemail = NULL;
  if ( strlen ( $firstname ) )
    $ufirstname = $firstname;
  else
    $ufirstname = NULL;
  if ( strlen ( $user ) )
    $uuser = $user;
  else
    $uuser = NULL;
  if ( $admin != 'Y' )
    $admin = 'N';
  if ( $enabled != 'Y' )
    $enabled = 'N';

  $sql = "UPDATE webcal_user SET 
           cal_firstname='$ufirstname',
           cal_is_admin='$admin',  cal_email='$uemail',
           cal_enabled='$enabled' WHERE cal_login='$uuser'";
  if ( ! mysqli_query(parent::$db, $sql)) {
    $cal_error = "update".mysqli_error(parent::$db);
    echo $cal_error;
    return false;
  }

  return true;
}

/**
 * Update user password.
 *
 * @param string $user     User login
 * @param string $password User password
 *
 * @return bool True on success
 *
 * @global string Error message
 */
public function update_user_password ( $user, $password ) {
  global $cal_error;
  $upassword = md5 ( $password );
  $sql = "UPDATE webcal_user SET cal_passwd = '$upassword' WHERE cal_login = '$user'";
  if (  ! mysqli_query(parent::$db, $sql) ) {
    $cal_error = mysqli_error(parent::$db);
    return false;
  }
  return true;
}

/**
 * Delete a user from the system.
 *
 * This will also delete any of the user's events in the system that have
 * no other participants. Any layers that point to this user
 * will be deleted. Any views that include this user will be updated.
 *
 * @param string $user User to delete
 */
/* Get event ids for all events this user is a participant.
 *
 * @param string $user  User to retrieve event ids
 */
private static function users_event_ids ( $user ) {
  $events = array ();
  $sql = "SELECT we.cal_id FROM webcal_entry we, webcal_entry_user weu
    WHERE we.cal_id = weu.cal_id AND weu.cal_login = '$user''";
  $res = mysqli_query(parent::$db, $sql);
  if ( $res ) {
    while ( $row = mysqli_fetch_row ( $res ) ) {
      $events[] = $row[0];
    }
  }
  return $events;
}

function delete_user ( $user ) {
  // Get event ids for all events this user is a participant
  $events = self::users_event_ids ( $user );
  // Now count number of participants in each event...
  // If just 1, then save id to be deleted
  $delete_em = array ();
  $evcnt = count ( $events );
  for ( $i = 0; $i < $evcnt; $i++ ) {
    $res = mysqli_query (parent::$db, "SELECT COUNT(*) FROM webcal_entry_user 
           WHERE cal_id = '{$events[$i]}'");
    if ( $res ) {
      if ( $row = mysqli_fetch_row ( $res ) ) {
        if ( $row[0] == 1 )
          $delete_em[] = $events[$i];
      }
      mysqli_free_result ( $res );
    }
  }
  $delete_emcnt = count ( $delete_em );
  // Now delete events that were just for this user
  for ( $i = 0; $i < $delete_emcnt; $i++ ) {
    mysqli_query (parent::$db, "DELETE FROM webcal_entry_repeats WHERE cal_id = '{$delete_em[$i]}'");
    mysqli_query (parent::$db, "DELETE FROM webcal_entry_repeats_not WHERE cal_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_entry_log WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_import_data WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_site_extras WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_entry_ext_user WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_reminders WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_blob WHERE cal_entry_id = '{$delete_em[$i]}'" );
    mysqli_query (parent::$db, "DELETE FROM webcal_entry WHERE cal_entry_id = '{$delete_em[$i]}'" );
  }
 // Delete user participation from events
  mysqli_query (parent::$db, "DELETE FROM webcal_entry_user WHERE cal_login = '$user'" );
  // Delete preferences
  mysqli_query (parent::$db, "DELETE FROM webcal_user_pref WHERE cal_login = '$user'" );
  // Delete from groups
  mysqli_query (parent::$db, "DELETE FROM webcal_group_user WHERE cal_login = '$user'" );
  // Delete bosses & assistants
  mysqli_query (parent::$db, "DELETE FROM webcal_asst WHERE cal_boss = '$user'" );
  mysqli_query (parent::$db, "DELETE FROM webcal_asst WHERE cal_assistant = '$user'" );
  // Delete user's views
  $delete_em = array ();
  $res = mysqli_query (parent::$db, "SELECT cal_view_id FROM webcal_view WHERE cal_owner = '$user'" );
  if ( $res ) {
    while ( $row = mysqli_fetch_row ( $res ) ) {
      $delete_em[] = $row[0];
    }
    mysqli_free_result ( $res );
  }
  $delete_emcnt = count ( $delete_em );
  for ( $i = 0; $i < $delete_emcnt; $i++ ) {
    mysqli_query (parent::$db, "DELETE FROM webcal_view_user WHERE cal_view_id = '{$delete_em[$i]}'" );
  }
  mysqli_query (parent::$db, "DELETE FROM webcal_view WHERE cal_owner = '$user'" );
  //Delete them from any other user's views
  mysqli_query (parent::$db, "DELETE FROM webcal_view_user WHERE cal_login = '$user'" );
  // Delete layers
  mysqli_query (parent::$db, "DELETE FROM webcal_user_layers WHERE cal_login = '$user'" );
  // Delete any layers other users may have that point to this user.
  mysqli_query (parent::$db, "DELETE FROM webcal_user_layers WHERE cal_layeruser = '$user'" );
  // Delete user
  mysqli_query (parent::$db, "DELETE FROM webcal_user WHERE cal_login = '$user'" );
  // Delete function access
  mysqli_query (parent::$db, "DELETE FROM webcal_access_function WHERE cal_login = '$user'" );
  // Delete user access
  mysqli_query (parent::$db, "DELETE FROM webcal_access_user WHERE cal_login = '$user'" );
  mysqli_query (parent::$db, "DELETE FROM webcal_access_user WHERE cal_other_user = '$user'" );
  // Delete user's categories
  mysqli_query (parent::$db, "DELETE FROM webcal_categories WHERE cat_owner = '$user'" );
  mysqli_query (parent::$db, "DELETE FROM webcal_entry_categories WHERE cat_owner = '$user'" );
  // Delete user's reports
  $delete_em = array ();
  $res = mysqli_query (parent::$db, "SELECT cal_report_id FROM webcal_report WHERE cal_login = '$user'" );
  if ( $res ) {
    while ( $row = mysqli_fetch_row ( $res ) ) {
      $delete_em[] = $row[0];
    }
    mysqli_free_result ( $res );
  }
  $delete_emcnt = count ( $delete_em );
  for ( $i = 0; $i < $delete_emcnt; $i++ ) {
    mysqli_query (parent::$db, "DELETE FROM webcal_report_template WHERE cal_report_id = '{$delete_em[$i]}'");
  }
  mysqli_query (parent::$db, "DELETE FROM webcal_entry_report WHERE cal_login = '$user'" );
    //not sure about this one???
  mysqli_query (parent::$db, "DELETE FROM webcal_report WHERE cal_user = '$user'" );
  // Delete user templates
  mysqli_query (parent::$db, "DELETE FROM webcal_user_template WHERE cal_login = '$user'" );
}

}//class end
?>
