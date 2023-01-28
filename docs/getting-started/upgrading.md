# Upgrading

## Upgrading 1.5 to 1.6

### Update your Composer.json

To update to the latest version inside of your composer.json file make sure to update the version of Voyager inside the require declaration of your composer.json to:

`tcg/voyager": "1.6.*`

And then run `composer update`

### Check your TinyMCE configuration

TinyMCE was updated to version 6 and with that, a lot of configurations have changed.  
If there are any errors in the console and you changed the TinyMCE configuration, make sure you are using the latest options and values from their docs.

### Troubleshooting

Be sure to ask us on our slack channel if you are experiencing any issues and we will try and assist. Thanks.
