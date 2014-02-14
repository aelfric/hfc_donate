HFC_DONATE
=========================

HFC Donate is a simple Wordpress plugin for setting up a PayPal 
donation form with multiple levels of donation.

Installation
-------------------------
Clone the repository into your Wordpress plugin directory.

Usage
-------------------------

1. On the `Donations` settings page, enter the email associated with
   the PayPal account that will be used to receive the donation and
   the name of the organization to be displayed on the PayPal checkout
   screen.
2. On a page where you want to include the donation form enter an opening
   and closing shortcode `[hfc_donation_levels]`.
3. Within these tags enter a `[level]` shortcode with the following attributes
   * amount - the amount of the donation to qualify for the level
   * label - an optional description of the level.

That's it!
