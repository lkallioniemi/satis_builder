# Frantic Satis Repository

Current repository location: https://s3-eu-west-1.amazonaws.com/satis-repository-bedrock/index.html

## Plugin licences

Please add details here!

### WooCommerce

* **Woo Carrier Agents** (Noutopistehaku)
Single-site lifetime licence, needs to be bought separately for each site
https://markup.fi/products/woocommerce-noutopistehaku
* **Product Sales Report Pro**
Single-site yearly licence, needs to be bought separately for each site
https://potentplugins.com/downloads/product-sales-report-pro-wordpress-plugin/
* **Paytrail Gateway**
Single-site yearly licence, needs to be bought separately for each site
https://woocommerce.com/products/woocommerce-paytrail/
* **Checkoutfi Gateway**
Single-site yearly licence, needs to be bought separately for each site
https://woocommerce.com/products/checkout-fi-gateway/
* **WB Paytrail Maksutavat**
Single-site yearly licence, needs to be bought separately for each site
https://webbisivut.org/kauppa/paytrail-maksutapa-woocommercelle-ilmainen/
* **Serial Numbers Pro**
Single-site yearly licence, needs to be bought separately for each site
https://pluginever.com/plugins/woocommerce-serial-numbers-pro/
* **Dynamic Pricing and Discounts**
Single-site license, needs to be bought separately for each site
https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279
* **WooCommerce Software License**
Unlimited Usage & Lifetime Updates, License in Developer vault
https://woosoftwarelicense.com/


### Multilingual plugins

* **Polylang Pro**
25-seat yearly licence, use only when needed (free version is enough in many cases). Remember to activate the licence in production and mark it in 1password
https://polylang.pro/
* **WPML & related addons**
Unlimited lifetime licence, can be freely used, but please don't
https://wpml.org/

### Form plugins

* **Gravity Forms & related addons**
Unlimited sites yearly licence, can be freely used
https://www.gravityforms.com/
* **Ninja Forms addons**
Single-site yearly licences, need to be bought separately for each site. (Ninja Forms itself is a free plugin.)
https://ninjaforms.com/add-ons/

### Others

* **Advanced Custom Fields Pro**
Unlimited sites lifetime licence, feel free to use!
https://www.advancedcustomfields.com/
* **Relevanssi Premium**
Unlimited sites lifetime licence, feel free to use!
https://www.relevanssi.com/
* **Elementor Pro**
Single-site yearly licence, needs to be bought separately for each site
https://elementor.com/pro/
* **WP Offload Media**
Unlimited sites, 1 year updates and support for 20.000 media items
https://deliciousbrains.com/wp-offload-media/

**TODO:** Event Organiser Pro? S3 Image Optimizer? Hubspot? Real Media Library? Miniorange? Search WP? User Role Editor Pro? Other WooCommerce addons? WordPress SEO Premium? WP All Import?
## Requirements

- `php >= 7.4`
- `composer >= 2.0`
- `aws cli >= 2.0`

## Installation

1. Clone the repository `git clone git@github.com:frc/frantic-satis-repository.git`
2. Install application dependencies by running `composer install` on project root

## Adding & updating packages

Add new package zip to correct directory under `pacakges` and run `composer build`.

The build will follow stritcly the folder structure and file namining of `package` folder:
1. First level is the type of the packacge (eg. `wordpress-plugin`, `wordpress-language` or `package`).
2. Second level is the vendor's namespace (eg. `polylang`).
3. Third level is zip of the packages named by the package name and including the version (eg. `polylang-2.0.zip`).

Running `composer build` will generate satis build with all packages inside `package`
folder. Run `composer update` to update [Satis](https://github.com/composer/satis)
it self.

See [Structure](#Structure) for more how to orginaise and name packages.

## Deployment

Run `composer deploy` to sync generated `dist` folder to S3.

Make sure you have the AWS CLI installed and credentials and access to
`satis-repository-bedrock` S3 bucket.

## Structure

```sh
./                      # Root
├── README.md
├── .satis.json         # Generated satis.json (don't edit or add to git)
├── src/                # Build scripts for generating satis.json
├── dist/               # Generated satis repository (don't edit or add to git)
└── packages/           # All included packages
    ├── <type>          # First level: package type
    │   ├── <vendor>/   # Second level: vendor namespace
    │   │   ├── <package-name>-<version>.zip #: Third level compressed package
    │   │   ├─ ...
    │   └── ...         # Examples:
    ├── wordpress-plugin
    │   ├── <vendor>/
    │   │   ├── <package-name>-<version>.zip
    │   │   ├── <package-name>.<version>.zip
    │   │   ├── <package-name>-v<version>.zip
    │   │   └── <package-name>_<version>.zip
    │   └── <vendor>/
    │       └── <package-name><delimiter><version>.zip
    ├── wordpress-muplugin
    └── wordpress-language
```
