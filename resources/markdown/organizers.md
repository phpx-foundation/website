# For Organizers

## To start a PHP× group:

Awesome. You want to start a PHP× group! Here are the basic steps to get started:

1. First, make sure there isn't an existing group in your region. If there is, reach out to them
   and see if it's possible to work together first. If you end up working with an existing group,
   we can still add you to the PHP× website so that the group gets exposure. Here are a few places to check:
     - [PHP.UserGroup](https://php.ug/)
     - [php.net calendar](https://www.php.net/cal.php)
     - [Meetup](https://www.meetup.com/)
     - [guild.host](https://guild.host/guilds)
     - [lu.ma](https://lu.ma/discover)
2. Get a `phpx(…).com` domain
     - If possible, use a local airport code or similar (e.g. Atlanta is ATL)
     - If local airport code isn't a popular way to reference your region, it's OK to do something else!
3. Set up SSL via a third party (like Cloudflare)
     - The easiest way to do this is with a free Cloudflare account, but you can roll your own with nginx or use any service you like
     - Set it to proxy to the IP address `167.99.10.168`
4. Submit a pull request to update [`groups.json`](https://github.com/phpx-foundation/website/blob/main/groups.json)
   with the following data:

### Required Data

- `external` — always set this to `false` if you want us to host for you
- `name` — use "PHP×FOO" where FOO is the code you picked in step 2
- `region` — Set this to a short descriptive name of your region (like Philadelphia or Atlanta)
- `description` — Set this to a single-sentence description of your meetup. Something like: _"A Philly-area PHP meetup for web artisans who want to learn and connect."_
- `timezone` — Use your PHP-compatible timezone ID (like `America/New_York`)
- `status` — choose one of:
    - `active`: Your group is actively holding meeting 
    - `planned`: Your group is planning its first meeting 
    - `prospective`: You hope to start a group and are gauging interest
- `latitude` and `longitude` — we hope to show each group on a map soon, so let us know where that should be 

### Optional Data

- `bsky_url` — if you use Bluesky, you can provide your group's Bluesky profile URL 
- `meetup_url` — if you use Meetup.com, you can provide your group's Meetup URL
- `frequency` — groups show as "bi-monthly" by default, but you can set this to however often you meet (monthly/quarterly/etc)
