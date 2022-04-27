=== Authentication and SSO by LoginRadius ===
Contributors: LoginRadius
Tags: Authentication, Passwordless Login, CIAM, Social Login, Single Sign-On, SSO, SAML, OAuth, JWT, OIDC, Phone Login, GDPR & CCPA, Roles and Permission, Cloud Authentication, Two-factor authentication, Multifactor Authentication 
Requires at least: 3.5
Tested up to: 5.9.3
Stable tag: 4.3.0
License: GPLv2 or later


== Description ==
The Authentication and SSO by LoginRadius WordPress plugin allow you to replace the default WordPress Authentication feature with diverse authentication methods, including standard login (email login), social login, phone login, and passwordless login. This plugin also provides the forgot password and reset password features.

The other benefits you get from using this plugin are enhanced security via multi-factor authentication, frictionless user experience via single sign-on, and effective user management features. Let’s have a more in-depth look into the plugin features:

= Diverse Authentication Methods: = You get to choose a user authentication method based on your target audience and use cases. 


- Standard login (email login):  You can customize the registration fields based on your business nature, where email id and password remain the default fields.  Once registered, the consumers can log in to their account using the email id and password.
- Social login:  You can choose the desired social providers from 20+ social providers like Facebook, Google, Twitter, as a quick and convenient registration and login approach for consumers. It is easy to configure the social login options using this plugin.
- Phone login:   You can allow consumers to register and log in using their phone number and password. Once registered, the consumers are required to verify their phone number via an OTP. Upon verification, they can log in to your application.
- Passwordless login:  You can allow consumers to login without a password. A  link or OTP is sent to the registered email id or phone number of the consumer, and upon clicking the link or entering the OTP, consumers get logged into their account.


= Frictionless Authentication via SSO: = It also lets you implement SSO (single sign-on) across your or third party applications. SSO omits the requirement of your consumer registering or repeatedly logging on the authorized applications and enables them to have a frictionless authentication experience. It enables consumers to access multiple applications with a single set of login credentials and an active login session.
= Enhanced Security via MFA: = For enhanced security, you can enable a multi-factor authentication feature and request your consumers to authenticate themself with OTP or Google Authenticator (as an added authentication factor to initial login). This process acts as an additional security layer to the standard authentication process. 
= Effective User Management: = The consumers’ data remains available in the WordPress Admin Panel and the LoginRadius Admin Console. And you can efficiently perform all user management actions in the LoginRadius Admin Console, permitting the necessary support to your consumers in managing their accounts.



= In addition to the above, the plugin offers the following additional features: =


- Easy to integrate via API secret and API key (available in your LoginRadius Account).
- Reduces user abandonment rates.
- LoginRadius manages consumer provisioning, de-provisioning, authentication, and authorization. Hence no extra efforts are needed to handle registration, login, reset passwords flows.
- Existing data migration to LoginRadius Admin Console and those migrated users can authenticate themself on your WordPress application.
- Data is encrypted end to end at transmission as well as at rest.
- Enable interface customizations to meet your brand requirements.
- Customizable email and SMS templates that you can personalize according to consumers and their demographics.
- Configurable email and SMS providers to send out the standard emails and SMS to the consumers.
- Option to set registration methods and fields as per the target audience. 
- Compliant with GDPR and CCPA data protection laws.
- Fully compatible with Buddypress and bbPress.
- Fully compatible with the Multisite feature.
- Top-notch support (24x7) and integration help.


The plugin has simple shortcodes that let you add registration, login, forgot password, and reset password interfaces anywhere to your application. Or you can create these authentication interfaces from the plugin.
== Shortcodes ==
Following are the shortcodes to add the authentication interfaces:

- Login - [ciam_login_form]
- Registration - [ciam_registration_form]
- Forgot password - [ciam_forgot_form]
- Reset Password - [ciam_password_form]

== Free Plan Features ==
- 5,000 Monthly active users.
- 1 WordPress application supported.
- Standard login (email login) with predefined registration fields
- 3 Social login providers (Facebook, Google, and Twitter)
- Get basic user profile data when a user registers using social login.
- Transactional email templates can be set up.
- Customizable login interfaces.

== Paid Plan Features ==
The paid plans have all of the free plan’s features + additional features. Find a detailed list of paid plan features <a href="https://www.loginradius.com/pricing/" target="_blank">here</a>.


== About LoginRadius ==

LoginRadius is a leading cloud-based consumer identity and access management (CIAM) solution that empowers businesses to deliver a delightful consumer experience and win consumer trust. 

The developer-friendly Identity Platform provides a comprehensive set of APIs to enable authentication, identity verification, single sign-on, user management, and account protection capabilities such as multi-factor authentication on any web or mobile application. The company offers open source SDKs, integrations with over 150 third party applications, pre-designed and customizable login interfaces, and best-in-class data security products. The platform is already loved by over 3,000 businesses with a monthly reach of 1.17 billion users worldwide.

For more information, visit <a href="http://loginradius.com/" target="_blank">loginradius.com</a>


== Developer Contribution ==
This plugin is being developed on GitHub. If you want to collaborate, please look at https://github.com/LoginRadius/wordpress-identity-plugin/

== Support ==

We offer 24/7 support, reach out to our support team, or refer our product documents:


- Enterprise Account: Reach us out <a href="https://adminconsole.loginradius.com/support/tickets/open-a-new-ticket" target="_blank">here</a> or browse our <a href="https://www.loginradius.com/docs/libraries/turn-key-plugins/wordpress-2-x-plugin/#wordpress-customer-identity-and-access-management-plugin" target="_blank">support documents</a>.
- Developer or Free Account: Reach us out <a href="https://loginradiusassist.freshdesk.com/support/home" target="_blank">here</a> or browse our <a href="https://www.loginradius.com/docs/developer/" target="_blank">support documents</a>.

If you found a bug, please create an issue on <a href="https://loginradiusassist.freshdesk.com/support/home" target="_blank">here</a> or browse our <a href="https://github.com/LoginRadius/wordpress-identity-plugin/" target="_blank">Github</a> where we can act upon them more efficiently.


== Screenshots ==
1. **User Registration Settings**: 

2. **Aunthentication Settings**: 

3. **Advanced Settings**: 

4. **ShortCodes**

5. **Registration**

6. **Login**

7. **Forgot Password**



== Change Log ==


=  4.3.0 =
1) Compatible with our latest PHP SDK 11.3.0.
2) Replaced the getSocialUserProfile API with getProfileByAccessToken API as getSocialUserProfile API is deprecated in PHP-SDK version 11.2.0 or above.

=  4.2.0 =
1) Updated Tags


=  4.1.0 =
*  Enhancements
1) Compatible with our latest PHP SDK 11.0.0
2) Optimized the code of Plugin to follow industry programming styles and best practices.


=  4.0.0 =
*  Enhancements
1) Compatible with our latest PHP SDK 10.0.0.
2) Added custom domain option for the IEF page.
3) Added registration form schema option.
4) Standardize the naming convention of labels and text of the plugin.
5) Separate file for all notification messages for easy maintenance. 
6) Standardize the debug log logging method.

= 3.3.0 =
* Enhancements.
1)Added API Request Signing Features
2)Implemented SSO features on passwordless login & email verification

* Bug Fixes.
3)Fixed wordpress CMS compatibility issue
4)Fixed add Email issue in Edit Profile
5)Fixed short code visible when hosted page is enabled
6)Fixed UI issue on Login Page
7)Fixed Profile Image url issue on Social Login 
8)Fixed OTP screen reload on registration and forgot password while otpEmailVerification is enabled


= 3.2.2 =
1) Capitalized calling method names

= 3.2.1 =
1) Added Change phone no and reset password SMS template.
2) Auto Email Id generation on Phone registration.
3) Unregistered user on LR allow login with default wordpress Login Form. 
4) Ability to set password for Social login Users

= 3.2.0 =
1) Implemented Cloud API Functionality
2) Removed Advance options directly handled by Cloud API
3) Added Fallback JS
4) Added Email template api
5) Removed Google captcha option from plugin settings, need to change from LoginRadius User Dashboard 

= 3.1.2 =
* Minor Bug Fix.
1) Update User Profile on LoginRadius Server

= 3.1.1 =
1) Removed redirection to LoginRadius dashboard on user add and edit time from admin.
2) Enhanced User Add and Edit functionality.On add and edit user from admin data will get saved to LoginRadius.

= 3.1.0 =
* Implemented New Functionality.
1) Hosted Page
2) 2 Factor Authentication 
3) Email Login with required,optional and disable flow.
4) Phone Login
5) Customize Email Templates
6) Customize Phone Templates
7) Enable Debug Mode
8) Single Sign-On (SSO)

= 3.0.1 =
* Minor Bug Fix.
1) Registration page bug fix

= 3.0.0 =
* This is the upgraded version of the application, in this we have added some new features which are mentioned below.
1) Add additional email functionality.
2) Redirect To and Referral url functionality.
3) Provide the feature for enable log generation.

== Frequently Asked Questions ==
= Can you help me set up User Registration, Social Login on my website? =
Yes, contact our experts or read out our support documents:

- Enterprise Account: Reach us out <a href="https://adminconsole.loginradius.com/support/tickets/open-a-new-ticket" target="_blank">here</a> or browse our <a href="https://adminconsole.loginradius.com/support/tickets/open-a-new-ticket" target="_blank">support documents</a>.
- Developer or Free Account: Reach us out <a href="https://loginradiusassist.freshdesk.com/support/home" target="_blank">here</a> or browse our <a href="https://adminconsole.loginradius.com/support/tickets/open-a-new-ticket" target="_blank">support documents</a>.


= Where can I get the User Data? =
User data is available in the WordPress Admin Panel and as well as LoginRadius Admin Console. Also, LoginRadius Admin Console lets you perform all user management activities.

= Can you help me migrate the existing user data from another application? =
Yes, we can migrate your existing user data into LoginRadius Admin Console. Since LoginRadius manages the authentication process via this plugin, those existing users will be able to log in to your WordPress application (where this plugin is being used).

For more details, reach us out to our support.

= Where can I access the Plugin documentation? =
- Enterprise Account: Browse our <a href="https://www.loginradius.com/docs/libraries/turn-key-plugins/wordpress-2-x-plugin/#wordpress-customer-identity-and-access-management-plugin" target="_blank">support documents</a>.
- Developer or Free Account: Browse our <a href="https://www.loginradius.com/docs/developer/" target="_blank">support documents</a>.


== Upgrade Notice ==

= 1.0 =
* This is the first version of the application.
