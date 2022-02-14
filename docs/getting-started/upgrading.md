# Upgrading

## Upgrading 1.4 to 1.5

### Update your Composer.json

To update to the latest version inside of your composer.json file make sure to update the version of Voyager inside the require declaration of your composer.json to:

`tcg/voyager": "1.5.*`

And then run `composer update`

### Removed hooks

Version 1.5 removes the hooks functionality.  
If you use any hooks, either skip this release or convert them to regular composer packages.  
Another way is to disable ssl verification in your `composer.json`: 

```
"repositories": {
    "hooks": {
        "type": "composer",
        "url": "https://larapack.io",
        "options": {
            "ssl": {
                "verify_peer": false
            }
        }
    }
}
```


If you do not use any hooks, you don't have to take any actions!

### Troubleshooting

Be sure to ask us on our slack channel if you are experiencing any issues and we will try and assist. Thanks.
