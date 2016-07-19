<?php
/**
 * Create newsletter popup csm static block
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$content = '<div class="block-title">
                <strong><span>Newsletter</span></strong>
            </div>
            <div class="block-content">
                <div class="form-subscribe-header">
                    <label for="newsletter-popup">Sign Up for Our Newsletter:</label>
                </div>
                <div class="input-box">
                    <input type="email" autocapitalize="off" autocorrect="off" spellcheck="false"
                           name="email" id="newsletter-popup" title="Sign up for our newsletter"
                           value="" class="input-text required-entry validate-email"/>
                </div>
                <div class="actions">
                    <button type="submit" title="Subscribe" class="button">
                        <span><span>Subscribe</span></span></button>
                </div>
            </div>';

$cmsBlock = array(
    'title' => 'Newsman Newsletter Popup',
    'identifier' => 'newsman_newsletter_popup',
    'content' => $content,
    'is_active' => 1,
    'stores' => 0
);

Mage::getModel('cms/block')->setData($cmsBlock)
    ->save();

$installer->endSetup();
