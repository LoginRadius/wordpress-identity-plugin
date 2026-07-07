# Changelog

> **LoginRadius WordPress CIAM Plugin Change Log** provides information regarding what has changed, more specifically what changes, improvements and bug fixes have been made to the plugin. For more details please refer to the [LoginRadius Documentation](https://www.loginradius.com/docs/sdk/turnkey-plugins/wordpress-2.x-plugin/)

## SUMMARY
Fully managed registration service including Email Registration, Social Login, password management, and data collection.

## REQUIREMENTS
LoginRadius PHP SDK library. The PHP SDK is bundled within the plugin — no separate installation required.

---

## CHANGE LOG

### 4.4.1
- Tested up to WordPress 7.0
- Upgraded bundled PHP SDK from v11.4.2 to v11.7.0
- Fixed minor bugs
- Replaced references to `Identity Experience Framework` with `Auth Studio` to reflect current LoginRadius product naming.

### 4.4.0
- Compatible with our latest PHP SDK 11.4.2
- Tested up to WordPress 6.0.2
- Fixed minor bugs

### 4.3.0
- Compatible with our latest PHP SDK 11.3.0
- Replaced the `getSocialUserProfile` API with `getProfileByAccessToken` API as `getSocialUserProfile` API is deprecated in PHP SDK version 11.2.0 and above

### 4.2.0
- Updated tags

### 4.1.0
#### Enhancements
- Compatible with our latest PHP SDK 11.0.0
- Optimized plugin code to follow industry programming styles and best practices

### 4.0.0
#### Enhancements
- Compatible with our latest PHP SDK 10.0.0
- Added custom domain option for the IEF page
- Added registration form schema option
- Standardized the naming convention of labels and text of the plugin
- Separate file for all notification messages for easy maintenance
- Standardized the debug log logging method

### 3.3.0
#### Enhancements
- Added API Request Signing features
- Implemented SSO features on passwordless login and email verification

#### Bug Fixes
- Fixed WordPress CMS compatibility issue
- Fixed add email issue in Edit Profile
- Fixed shortcode visible when hosted page is enabled
- Fixed UI issue on Login Page
- Fixed profile image URL issue on Social Login
- Fixed OTP screen reload on registration and forgot password while `otpEmailVerification` is enabled

### 3.2.2
- Capitalized calling method names

### 3.2.1
- Added Change Phone Number and Reset Password SMS template
- Auto email ID generation on phone registration
- Unregistered users on LoginRadius allowed to log in with default WordPress login form
- Added ability to set password for Social Login users

### 3.2.0
- Implemented Cloud API functionality
- Removed advanced options directly handled by Cloud API
- Added Fallback JS
- Added email template API
- Removed Google reCAPTCHA option from plugin settings — configurable directly from the LoginRadius User Dashboard

### 3.1.2
#### Bug Fixes
- Fixed user profile update on the LoginRadius server

### 3.1.1
- Removed redirection to LoginRadius dashboard on user add and edit from admin
- Enhanced user add and edit functionality — data is now saved to LoginRadius on add and edit from admin

### 3.1.0
#### New Features
- Hosted Page
- Two-Factor Authentication (2FA)
- Email login with required, optional, and disabled flow
- Phone login
- Customize email templates
- Customize phone templates
- Enable debug mode
- Single Sign-On (SSO)

### 3.0.1
#### Bug Fixes
- Fixed registration page bug

### 3.0.0
#### Enhancements
- Added additional email functionality
- Added redirect-to and referral URL functionality
- Added feature to enable log generation