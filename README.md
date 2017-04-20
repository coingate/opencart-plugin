# OpenCart CoinGate Plugin

CoinGate bitcoin payment gateway OpenCart plugin.

## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please note, that for "Test" mode you **must** generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

### For OpenCart 1.* version

1. Download [coingate-opencart.ocmod.zip](https://github.com/coingate/opencart-plugin/releases/download/v1.3.1/coingate-opencart.ocmod.zip).
2. Extract ZIP archive and go to `upload` directory.
3. Upload `admin`, `catalog` and `system` directories to root of your application.
4. Login to your OpenCart Admin.
5. Go to Extensions » Payments.
6. Find CoinGate, click "Install".
7. Click "Edit" on CoinGate extension.
8. Enter [API Credentials](http://support.coingate.com/knowledge_base/topics/how-can-i-create-coingate-api-credentials) (App ID, Api Key, Api Secret).
9. Set status "Enable" and save your settings.

### For OpenCart 2.* version

1. Check OpenCart FTP configuration in Admin panel: *System » Settings* click **Edit** button and click **FTP** tab. Enter *FTP Host*, *Port*, *Username*, *Password*, *Root directory (FTP Root)*. In **Enable FTP** choose *Yes* radio button. Don't forget to save.

2. Download:
  * For **OpenCart 2.0 - 2.2** version: [coingate-opencart.ocmod.zip](https://github.com/coingate/opencart-plugin/releases/download/v1.3.1/coingate-opencart.ocmod.zip)
  * For **OpenCart >= 2.3** version: [coingate-opencart.ocmod.zip](https://github.com/coingate/opencart-plugin/releases/download/v2.0.1/coingate-opencart.ocmod.zip)

3. In admin panel, go to *Extensions » Extension Installer*. Click **Upload**, choose **coingate-opencart.ocmod.zip** from your computer, then click **Continue** (if the upload gets stuck half way, please check [this page](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=18892) for solutions).

4. Enable CoinGate payment extension:
 * For **OpenCart 2.0 - 2.2** version: in admin panel, go to *Extensions » Payments* find **Bitcoin via CoinGate** and click **Install** button.
 * For **OpenCart >= 2.3** version: in admin panel, go to *Extensions*, from dropdown form choose *Payments*, find **Bitcoin via CoinGate** and click **Install** button.

5. Enter [API Credentials](http://support.coingate.com/knowledge_base/topics/how-can-i-create-coingate-api-credentials) (*App ID*, *Api Key*, *Api Secret*) and configure other extension settings:
  * For **OpenCart 2.0 - 2.2** version: in admin panel, go to *Extensions » Payments* find **Bitcoin via CoinGate** and click **Edit** button. Don't forget to save.
  * For **OpenCart >= 2.3** version: in admin panel, go to *Extensions*, from dropdown form choose *Payments*, find **Bitcoin via CoinGate** and click **Edit** button. Don't forget to save.
