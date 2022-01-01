<h2>Welcome</h2>

Welcome to ECOMWISE B.V. software solutions. We're glad you chose to use our products.
Please read the following general information and instructions before proceeding with any further steps.


<h2>B2B Forced Login</h2>


<h2>Introduction</h2>

This extension, compatible with Magento 2.x, forces customers to first login or create an account before seeing any products or CMS pages. There are two ways to access the web store when the forced login is enabled:

1. Login or Register: You can either login with an existing account or create a new account

2. Login only: You can only login and can't create a new account.


<h2>Compatibility</h2>

This extension is compatible with Magento versions 2.0.x - 2.1.x


<h2>Installation</h2>

Download the extension package from your account and extract the downloaded archive.
Create the folder app/code/Ecomwise/ForcedLogin/ in your Magento root.
Copy the extracted content to the app/code/Ecomwise/ForcedLogin/ directory.
Positioned in the root Magento directory execute the following commands:

1. Install: php bin/magento setup:upgrade
2. Recompile (Optional): php bin/magento setup:di:compile
3. Reindex (Optional): php bin/magento indexer:reindex
4. Clear the Magento cache (Optional): php bin/magento cache:clean

 
<h2>Configuration</h2>

Stores -> Configuration -> Ecomwise -> B2B Forced Login

1. Info & Support: An overview of the extension information such as version number, compatibility, documentation and support.

2. B2B Forced Login Settings:

- Extension enabled: Yes/No.
- Access to Website: Via Login/Via Login and Register.


<h2>Front End Implications</h2>

Login page

If module configuration setting: "Via Login" is chosen on the Login page is diplayed only the "Login form". 
Otherwise, on the Login page is displayed the "Register new customer" form and "Login form" for already registered customers.


<h2>Uninstall</h2>

Automated ways are not available in this version. 


<h2>Updates</h2>

2.0.0
24 october 2016
	Stable version release.


<h2>License</h2>

This product uses a proprietary license. Please visit our public <a href="http://support.b2b-extensions.com/support/solutions/articles/3000059533-ecomwise-license" target="_blank">license</a> page for details.


<h2>Support</h2>

Please browse to our FAQ page for more details or contact our <a href="http://support.b2b-extensions.com" target="_blank">support</a>.
