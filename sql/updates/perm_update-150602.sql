#Adds permissions for assigning badges, editing badges, and editing the show online feature when editing a user's profile.
#Date: 6/2/2015

INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-user-show-online', '', 0, 0);