# PHP×

PHP× is a small group of PHP and Laravel meetups around the world. We're a small group now,
but hope to grow over the coming months!

As of right now, this page serves as a scratchpad for meetup organizers. If you would like to 
contribute to this document, [pull requests are welcome](https://github.com/phpx-foundation/website).
Not sure? [Join the PHP× Discord](https://discord.gg/wMy6Eeuwbu) first to discuss!

## Adding a group to PHP×

There are two kinds of groups:

### PHP× Groups

These are groups that are hosted on [phpx.world](https://phpx.world) and use the PHP× naming convention.
To start a PHP× group, read our [organizer guide](/organizers).

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
