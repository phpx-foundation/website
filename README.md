# PHP×

PHP× is a small group of PHP and Laravel meetups around the world. We're a small group now,
but hope to grow over the coming months!

As of right now, this page serves as a scratchpad for meetup organizers. If you would like to 
contribute to this document, [pull requests are welcome](https://github.com/phpx-foundation/website).
Not sure? [Join the PHP× Discord](https://discord.gg/wMy6Eeuwbu) first to discuss!

## Adding a group to PHP×

You can add your group by submitting a pull request to the [PHP× website repository](https://github.com/phpx-foundation/website).
Please add your group to the [`groups.json`](https://github.com/phpx-foundation/website/blob/main/groups.json) file, and
once approved, your group will automatically get synced to [phpx.world](https://phpx.world).

There are two kinds of groups:

### PHP× Groups

These are groups that are hosted on [phpx.world](https://phpx.world) and use the PHP× naming convention.
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
    "bsky_url": "https://bsky.app/profile/<<group profile>>" // can be null
},
```

### External groups

You don't need to host your site on our platform. If you already have a successful meetup, we're
happy to link to it. Just submit a PR to the [`groups.json`](https://github.com/phpx-foundation/website/blob/main/groups.json)
file with the following format:

```json5
"<<your domain>>": {
    "external": true, // must be true
    "name": "PHP×<<local airport code or similar>>",
    "region": "<<short city or region name>>",
},
```

## The PHP× site

This site is a basic multi-tenant Laravel app. As of November 13, 2024, the following
features were loosely supported:

- Creating groups
- Creating and RSVPing to meetups
- Connecting to individual [Mailcoach](https://www.mailcoach.app/) instances to send group announcements
- Connecting to a Bluesky account to post meetups (partially)

### TODO

Next steps for the site:

- Adding an admin UI (right now all admin is done via artisan commands)
- Adding more per-group customizations (like theme/etc)
- Improving the announcement integrations

Other ideas:

- Allow for external sites to add an iCalendar feed that we can then merge with the PHP× feed for a central list of all events
- Add latitude/longitude to events to visualize
- How do we ensure that groups that never meet eventually disappear from the site
- We should have a "placeholder" concept for groups that are considering forming (maybe subdomains)

If any of that sounds interesting, [join the Discord](https://discord.gg/wMy6Eeuwbu)! You're welcome
to PR changes, but it's best to talk it thru with group organizers first.

## Resources for Organizers

Another big goal is to make it easier to organize meetups! We'd like to gather resources
so that each person isn't in it alone. Some ideas:

- Guides on having your first meeting
- Companies that are interested in sponsoring meetups
- Tips on estimating how much food/drinks/etc you'll need
- How to get speakers, and what kinds of meetings work well

Some things already in the works:

- Free [Mailcoach](https://www.mailcoach.app/) accounts for organizers
- Listing on [Laravel News](https://laravel-news.com/events)

Hopefully this list will grow over the coming weeks. Keep an eye out.

## What's Next

PHP× started with [Joe Tannenbaum](https://bsky.app/profile/joe.codes) and
[PHP×NYC](https://phpxnyc.com/). [Chris Morrell](https://bsky.app/profile/cmorrell.com)
launched [PHP×Philly](https://phpxphilly.com/) soon after, but in mid-November 2024,
PHP meetups really started to explode.

We're in the **very** early days of figuring out what this group can do!

Stay tuned.
