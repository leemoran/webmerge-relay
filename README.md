# Webmerge Relay

Server-side script that parses NationBuilder webhooks into [Webmerge](https://www.webmerge.me/). Originally written to send users pre-filled voter registration PDFs for printing and mailing.

Before uploading, update the following:

*   **Line 62** with your Webmerge document ID
*   **Line 63** with your [Webmerge API Key](https://www.webmerge.me/manage/account?page=api)
*   **Line 18** with your signup page's slug

Set the script's URL as your [NationBuilder webhooks](http://nationbuilder.com/webhooks_overview) endpoint. You will need to update your document's field map to use the provided field names. Currently, relay.php provides the following:

*   {$FirstName}
*   {$LastName}
*   {$City}
*   {$State}
*   {$Address}
*   {$Zip}
*   {$Email}
*   {$DOB}
*   {$County}*

_*Due to the way NationBuilder handles submitted addresses, county will likely not be included unless it's been manually entered by an administrator beforehand_

Once you've verified everything is working properly with Webmerge's sample PDFs, update **Line 68** to turn off test mode.