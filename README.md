# Magento-Newsman

[Newsman](https://www.newsmanapp.com) module for Magento. Sync your Magento customers / subscribers to [Newsman](https://www.newsmanapp.com) list / segments. 

This is the easiest way to connect your Shop with [Newsman](https://www.newsmanapp.com). Generate an API KEY in your [Newsman](https://www.newsmanapp.com) account, install this plugin and you will be able to sync your shop customers and newsletter subscribers with [Newsman](https://www.newsmanapp.com) list / segments.
Installation

## Manual installation: 
1. Copy the *"app/code/community/Newsman"* directory from this repository to your "community" shop directory.

2. Copy the *"lib/Newsman"* directory to your "lib/" shop directory.

3. Copy the *"app/design/adminhtml/default/default/template/newsman"* to your "app/design/adminhtml/default/default/template/" shop directory.

4. Copy the *"app/etc/modules/Newsman_Newsletter.xml"* to your "app/etc/modules/" shop directory.

5. Copy the content of the *"app/locale"* directory to the same location in your application.
	
## Magento Connect installation:
In your Magento admin panel, go to **System > Magento Connect > Magento Connect Manager** and follow the usual installation flow.
	
## Configuration
1. Go to **System > Configuration > Newsman > 
Newsletter Subscriber Import > General Settings > Enable** and enable the module. Fill in your [Newsman](https://www.newsmanapp.com) API KEY and User ID and click the save button.

  ![General Settings](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assests/general_settings.png)

2. After the [Newsman](https://www.newsmanapp.com) API KEY and User ID are set, you can choose a list.

3. Choose destination segments for your newsletter subscribers and customer groups. This configuration is optional and if it's not set all the customers and subscribers will be imported without being assigned to a segment. For the segments to show up in this form, you need to set them up in your Newsman account first.

  ![Data Mapping](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assests/data_mapping.png)

4. Choose how often you want your lists to get uploaded to Newsman You can also do a manual synchronization by clicking "Manual Sync".

  ![Synchronization Schedule](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assests/synchronization_schedule.png)

5. For the automatic synchronization to work, you need to have Magento's built-in cron job functionality enabled.
