# Page Login Restriction

A simple WordPress plugin that restricts pages to logged-in users and either redirects non-logged-in users to a specified URL or displays a custom message with a login link that returns users to the original page.

## Description

Page Login Restriction is a lightweight WordPress plugin that allows administrators to easily restrict access to specific pages, ensuring they're only accessible to logged-in users. When non-logged-in users attempt to access restricted pages, they can either be automatically redirected to a configurable URL or shown a custom message with a login link that returns them to the original page after successful login.

## Features

- Restrict any page to logged-in users only
- Two restriction methods:
  - Redirect non-logged-in users to a custom URL
  - Display a custom message with login link
- Post-login redirection that returns users to the page they were trying to access
- Easy-to-use admin interface with a complete list of all pages
- Visual editor for customizing the message
- Shortcode support for custom login links
- Simple checkbox selection to restrict pages
- Lightweight and efficient code
- No coding required

## Installation

1. Download the plugin zip file
2. Go to your WordPress admin dashboard
3. Navigate to Plugins > Add New
4. Click the "Upload Plugin" button at the top of the page
5. Select the plugin zip file and click "Install Now"
6. After installation completes, click "Activate Plugin"

Alternatively, you can manually upload the plugin files to your `/wp-content/plugins/` directory via FTP.

## Usage

1. After activation, go to Settings > Page Restriction in your WordPress admin menu
2. Choose your preferred restriction method:
   - Redirect to another page
   - Show custom message
3. If you selected "Redirect," set the redirect URL where you want non-logged-in users to be sent
4. If you selected "Show custom message," customize the message in the visual editor
5. Configure the post-login redirection option (enabled by default)
6. In the "Restricted Pages" section, check the boxes next to any pages you want to restrict to logged-in users only
7. Click "Save Settings"

That's it! Your selected pages are now protected and only accessible to logged-in users.

## Using the Login Link Shortcode

The plugin provides a shortcode to create login links that automatically redirect back to the current page:

```
[plr_login_link text="Login here" class="button"]
```

Parameters:

- `text` - The link text (default: "Click here to log in")
- `class` - CSS class(es) for the link (optional)

You can also use the `[login_url]` placeholder in your custom message, which will automatically be replaced with a login URL that includes the redirect parameter.

## Frequently Asked Questions

### Can I restrict custom post types?

Currently, this plugin only supports restricting standard WordPress pages. Support for custom post types may be added in a future update.

### How does the post-login redirection work?

When a user clicks on the login link from the custom message (or is redirected to the login page), the plugin passes the current page URL as a redirect parameter to WordPress. After successful login, WordPress automatically redirects the user back to the original page they were trying to access.

### Can I disable the post-login redirection?

Yes, you can disable this feature in the plugin settings. When disabled, users will be directed to the default location after login (typically the WordPress dashboard for most users).

### Can I customize the message shown to non-logged-in users?

Yes! If you select the "Show custom message" option, you can use the visual editor to create a custom message. You can include HTML, links, and even shortcodes in your message.

### Does the message option preserve my page template?

Yes, when using the message option, the page's template, header, footer, and sidebar are preserved. Only the main content area is replaced with your custom message.

### Is this plugin compatible with caching plugins?

Yes, but you should configure your caching plugin to exclude pages for logged-in users to ensure proper functionality.

## Support

If you encounter any issues or have questions about the plugin, please contact us through our support page at [your support URL].

## Changelog

### 1.0.0

- Initial release

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
```
