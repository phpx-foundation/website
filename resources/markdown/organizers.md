# For Organizers

To start a PHP× group:

1. Make sure there isn't an existing group in your region
2. Get a phpx(…).com domain (use local airport code or something similar)
3. Set up a free Cloudflare account (or any other service that handles SSL for you) and set it to proxy
   to the IP address `167.99.10.168`
3. Pull request your group to the [`groups.json`](https://github.com/phpx-foundation/website/blob/main/groups.json)
   with the following format:

```json5
"<<your domain>>": {
    "external": false, // always leave this
    "name": "PHP×<<your airport code/similar>>",
    "region": "<<short city or region name>>", // can be null if airport code is good enough
    "description": "<<short description>>",
    "timezone": "<<php-compatible timezone ID>>",
    "og_asset": "<<include image in public/og/ in pr>>", // can be null
    "bsky_url": "https://bsky.app/profile/<<group profile>>" // can be null
},
```
