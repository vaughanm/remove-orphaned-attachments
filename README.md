# remove-orphaned-attachments
Removes attachments from the DB if the corresponding file does not exist on the server

Upload the remove-orphaned-attachments.php file to the following location on your WordPress install:

/wp-content/mu-plugins

Once installed, go to your Site wp-admin

Then visit settings > media

Scroll down and you will see new options for Removing orphaned attachments.

Check the box to enable the feature.

1. Test mode enable (enabled by default)

In this mode, no attachments are removed from the database.

A log file called ROA-Log.log will be created in the /wp-content folder.

It will list all attachments in the DB where the file is no longer present or missing on the server.

2. Test mode disabled.

In this mode, Any attachments in the DB will be automatically removed from the DB where the corresponding file is missing from the server.

A log file called ROA-Log.log will be created in the /wp-content folder.

Note:

There is no Jquery use in this plugin, it does require you to refresh the admin page for it to work.

Please disable the feature after use, not doing so will end up creating a very huge log file otherwise.
I still need to improve this to prevent that happening.
