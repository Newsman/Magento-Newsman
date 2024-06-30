# Magento-Newsman

[NewsMAN](https://www.newsman.com) Extension for Magento 1.9. Simplify the Integration of Your Magento Customers and Subscribers with NewsMAN Lists and Segments

Streamline the connection between your online store and the NewsMAN platform using this straightforward approach. Generate an API KEY within your NewsMAN account, install the extension, and effortlessly synchronize your shop customers and newsletter subscribers with NewsMAN lists and segments. The installation process is quick and user-friendly, ensuring a smooth integration experience.

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

# Description

## Subscription Forms & Pop-ups:
- Craft visually engaging forms and pop-ups to capture potential leads.
- Implement embedded registrations for newsletters or activate pop-ups upon exit intent.
- Maintain a consistent form presentation across diverse devices, ensuring a seamless user experience.
- Integrate forms with automated systems for timely responses and personalized welcome emails.

## Contact Lists & Segments Management
- Efficiently import and synchronize contact lists from various sources.
- Simplify data management through segmentation strategies based on demographics or behavior.

## Email & SMS Marketing Campaigns
- Effortlessly dispatch extensive campaigns, newsletters, or promotions to a broad subscriber base.
- Tailor campaigns for individual subscribers, addressing them by name and suggesting relevant products.
- Re-engage subscribers by re-sending campaigns to those who haven't opened the initial email.

## Email & SMS Marketing Automation
- Automate the delivery of personalized product recommendations, follow-up emails, and strategies for addressing cart abandonment.
- Strategically tackle cart abandonment or showcase related products to encourage completed purchases.
- Collect post-purchase feedback to enhance customer satisfaction.

## E-commerce Remarketing Strategies
- Reconnect with subscribers through targeted offers based on past interactions.
- Personalize interactions with exclusive offers or reminders tailored to user behavior or preferences.

## SMTP Transactional Emails
- Ensure the prompt and reliable delivery of crucial messages, such as order confirmations or shipping notifications, via SMTP.

## Comprehensive Email and SMS Analytics
- Gain insights into open rates, click-through rates, conversion rates, and overall campaign performance.

Use the NewsMAN plugin for Magento to seamlessly optimize marketing endeavors and establish effective connections with the audience.