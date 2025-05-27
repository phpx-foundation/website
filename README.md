# PHP×

PHP× is a group of PHP and Laravel meetups around the world. Each group is independently
organized, but we try to share knowledge and resources how we can.

Our group started in 2024, first with [PHP×NYC](https://phpxnyc.com/) and shortly after
with [PHP×Philly](https://phpxphilly.com/). In late 2024, the PHP× network started expanding
rapidly. We're now trying to figure out the best ways to work together and help promote
each other's groups and events.

Want to get involved? The best place right now is [on our Discord](https://discord.gg/wMy6Eeuwbu).

## Joining PHP×

Existing groups are welcome to join PHP×, and if there isn't a local PHP meetup or user
group in your area, we'll help you get set up.

### Existing Groups

If you already have a PHP meetup and would like to be listed on our site, please submit
a PR to the [`groups.json`](https://github.com/phpx-foundation/website/blob/main/groups.json) file
in our website repo. Just match the style of any group in that file that has `external` set
to `true`.

### New groups

If you would like to start a new PHP× group, please check our [Organizers page](/organizers)
for instructions and considerations.

## Sponsoring Groups

If you are a company that would like to sponsor PHP and Laravel events, please get in
touch. The best way to do that, right now, is [via the PHP× Discord](https://discord.gg/wMy6Eeuwbu).
In the future, we plan to make it easier to connect groups with sponsors.

## The PHP× network of sites

This site is a basic multi-tenant Laravel app that any event organizer is welcome to use
to host a basic landing page and accept newsletter sign-ups and event RSVPs. Check out
the [Organizers page](/organizers) for details on how to get set up.

As of November 17, 2024, the following features were loosely supported:

- Creating groups
- Creating and RSVPing to meetups
- Connecting to individual [Mailcoach](https://www.mailcoach.app/) instances to send group announcements
- Connecting to a Bluesky account to post meetups (partially)

## Contributing to PHP×

You're welcome to [PR changes](https://github.com/phpx-foundation/website), but it's best 
to talk it through with group organizers first.

You can find us on [Discord](https://discord.gg/wMy6Eeuwbu)!

### Running PHP× locally

Clone the repository into a folder named `phpx`:

```bash
git clone git@github.com:phpx-foundation/website.git phpx
```

Initialize the application:

```bash
cd phpx

cp .env.example .env

composer install

php artisan migrate:fresh --seed

npm install
npm run build
```

If you're using Herd or Valet, the primary phpx.world website will be at 
[phpx.test](https://phpx.test) and a group site will be at [phpxcity.phpx.test](https://phpxcity.phpx.test).

### TODO

Next steps for the site:

- Improving the announcement integrations

Other ideas:

- Allow for external sites to add an iCalendar feed that we can then merge with the PHP× feed for a central list of all events
- Add latitude/longitude to events to visualize
- How do we ensure that groups that never meet eventually disappear from the site
- We should have a "placeholder" concept for groups that are considering forming (maybe subdomains)

## What's Next

We're still in the **very** early days of figuring out what this group can do!

Stay tuned.
