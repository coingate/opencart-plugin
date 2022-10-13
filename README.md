# OpenCart CoinGate Plugin

Accept Bitcoin & Altcoins on your OpenCart store.

Read the module installation instructions below to get started with CoinGate Bitcoin & Altcoin payment gateway on your shop.
Full setup guide with screenshots is also available on our blog: <https://blog.coingate.com/2017/03/install-coingate-opencart-bitcoin/>


## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please note, that for "Test" mode you **must** generate separate API credentials (*Auth Token*) on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

Also note, that *Receive Currency* parameter in your module configuration window defines the currency of your settlements from CoinGate. Set it to BTC, EUR or USD, depending on how you wish to receive payouts. To receive settlements in **Euros** or **U.S. Dollars** to your bank, you have to verify as a merchant on CoinGate (login to your CoinGate account and click *Verification*). If you set your receive currency to **Bitcoin**, verification is not needed.

### For OpenCart 3.* version

1. Download latest release
2. Go to Extensions » Installer and upload coingate-opencart.ocmod.zip
3. Go to Extensions » Extensions and from dropdown choose Payments.
4. Find CoinGate and click Install button.
5. In same list find CoinGate and click Edit button.
6. Enter [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) and configure other extension settings.
7. Don't forget to save.

### For OpenCart 1.* and 2.*

Versions 1.* and 2.* are no longer supported, last release that suppots these versions can he found [here](https://github.com/coingate/opencart-plugin/releases/tag/v3.1.3)
