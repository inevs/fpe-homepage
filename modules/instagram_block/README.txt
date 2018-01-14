CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers

INTRODUCTION
------------

This is a very simple module that integrates with Instagram and creates a block
containing your most recent Instagram posts.

The block's configuration page lets you choose how many posts and what size they
should appear in the block. The images are individually exposed to the drupal
theme layer, so developers have access to an all of the variables provided by
the Instagram API should they choose to extent the block.

For more informations see the Instagram developer pages:
http://instagram.com/developer/endpoints/users/#get_users_media_recent

For a full description of the module, visit the project page:
https://www.drupal.org/project/instagram_block

To submit bug reports and feature suggestions, or to track changes:
https://www.drupal.org/project/issues/instagram_block

REQUIREMENTS
------------

No special requirements.

INSTALLATION
------------

Install as you would normally install a contributed Drupal module. See:
https://www.drupal.org/docs/8/extending-drupal/installing-contributed-modules
for further information.

CONFIGURATION
------------
To add Instagram Access Token Go to configuration page for Instagram Block
i.e Home-> Administration-> Configuration-> Content authoring.

To add Instagram Block to specific content region Go to Block Layout.
i.e Home-> Administration-> Structure-> Block layout.

You also need to add the user id of the Instagram account you are pulling
posts from as part of the block configuration.

MULTIPLE BLOCKS
------------
You can have multiple blocks pulling posts from different Instagram accounts.
To configure additional user ids, you need to add it on the Instagram app.
You can do this by going to this url:

https://www.instagram.com/developer/clients/manage/

After that, click on manage in your app and go to Sandbox and add the users
there. After that you need to login in to the new instragram account and
accept this request. With that you can use the new users id.

Another options is to publish your app on Instagram and ask for public_content
permission, with that you will have access to all public profiles.

MAINTAINERS
-----------

Current maintainers:
* Yan Loetzer (yanniboi) - https://www.drupal.org/u/yanniboi
