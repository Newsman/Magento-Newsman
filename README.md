# Magento-Newsman

[Newsman](https://www.newsmanapp.com) module for Magento. Sync your Magento customers / subscribers to [Newsman](https://www.newsmanapp.com) list / segments. 

This is the easiest way to connect your Shop with [Newsman](https://www.newsmanapp.com). Generate an API KEY in your [Newsman](https://www.newsmanapp.com) account, install this plugin and you will be able to sync your shop customers and newsletter subscribers with [Newsman](https://www.newsmanapp.com) list / segments.
Installation

## Installation: 
1. Copy all contents to your "root" shop directory.
2. Copy 'newsmanfetch.php' to your magento root directory.
3. Settings -> Cache management - Flush magento cache
		
## Configuration
1. Go to **System > Configuration > Newsman > 
Newsletter Subscriber Import > General Settings > Enable** and enable the module. Fill in your [Newsman](https://www.newsmanapp.com) API KEY and User ID and click the save button.

  ![General Settings](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assets/general_settings.png)

2. After the [Newsman](https://www.newsmanapp.com) API KEY and User ID are set, you can choose a list.

3. Choose destination segments for your newsletter subscribers and customer groups. This configuration is optional and if it's not set all the customers and subscribers will be imported without being assigned to a segment. For the segments to show up in this form, you need to set them up in your Newsman account first.

  ![Data Mapping](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assets/data_mapping.png)

4. Choose how often you want your lists to get uploaded to [Newsman](https://www.newsmanapp.com) You can also do a manual synchronization by clicking "Manual Sync".

## Sync segmentation:

- Subscribers: email, subscriber_status, source (customer or visitor)
- Customers: email, customerId, groupId, firstname, lastname, source

  ![Synchronization Schedule](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assets/synchronization_schedule.png)

5. For the automatic synchronization to work, you need to have Magento's built-in cron job functionality enabled.

## SMTP

Warning: You need to disable extra SMTP modules. 

6. You have the option to send all Magento's transactional emails via Newsman's SMTP server. The server requires authentication, so here's how to set it up:
   ![Mail Sending Settings](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assets/mail_sending_settings.png)
   
## Newsman Remarketing

1. Go to **System > Configuration > Newsman Remarketing. Fill in your [Newsman](https://www.newsmanapp.com) Remarketing ID and click the save button.

  ![Remarketing](https://raw.githubusercontent.com/Newsman/Magento-Newsman/master/assets/remarketing.png)
  
After the plugin is installed, you will also have: feed product, events (product impressions, AddToCart, purchase) automatically implemented.  


