# OpenCart CoinGate Plugin

CoinGate bitcoin payment gateway OpenCart plugin.

## Install

You can sign up for CoinGate account at https://coingate.com for production and https://sandbox.coingate.com for testing (sandbox) environment.

### via FTP

1. Check OpenCart FTP configuration in Admin panel: *System » Settings* click **Edit** button and click **FTP** tab. Enter *FTP Host*, *Port*, *Username*, *Password*, *Root directory (FTP Root)*. In **Enable FTP** choose *Yes* radio button. Don't forget to save.

2. Download [coingate-opencart.ocmod.zip](https://github.com/coingate/opencart-plugin/releases/download/1.0.9/coingate-opencart.ocmod.zip)

3. In admin panel, go to *Extensions » Extension Installer*. Click **Upload**, choose **coingate-opencart.ocmod.zip** from your computer, then click **Continue**.

4. Enable CoinGate payment extension: in admin panel, go to *Extensions » Payments* find **Bitcoin via CoinGate** and click **Install** button.
 
5. Enter API Credentials (*App ID*, *Api Key*, *Api Secret*) and configure other extension settings: in admin panel, go to *Extensions » Payments* find **Bitcoin via CoinGate** and click **Edit** button. Don't forget to save.
