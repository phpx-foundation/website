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
- `name` — use "PHP×FOO" where FOO is the code you picked in step 2. Please use the unicode times symbol “×” rather than the letter “x” (you can just copy-and-paste from here). 
- `region` — Set this to a short descriptive name of your region (like Philadelphia or Atlanta)
- `continent` — One of: 
    - `Africa`
    - `Asia`
    - `Europe`
    - `North America`
    - `South America`
    - `Antarctica`
    - `Australia`
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

## Once you've started

### Manual set up

Once your PR is merged, your domain will be automatically added to Forge and your
site should start working immediately. There are a handful of things that will
need to happen manually after that. For right now, the best way to do this is to
reach out to Chris Morrell on Discord to check in on each of these:

- Get the "organizer" role on Discord
- Have a channel created in the Discord
- Get a logo for social media
- Get an open graph image created
- Configure your mailing list
- Integrate with Bluesky

### Set up Mailcoach

The folks at [Mailcoach](https://www.mailcoach.app/) have generously offered free accounts to all
PHP× organizers. Reach out to Chris Morrell on the Discord to get the coupon code.

#### Get a free Mailcoach account

Once you have your coupon code, register at [Mailcoach](https://www.mailcoach.app/). 

- When registering, you can choose "Let us handle sending."
- Go to billing and choose the base plan. At checkout, use the PHP× coupon code that you received
  from Chris. That will cover the monthly fee and the first 2,000 emails.
- You will still need to provide a credit card and will be billed if you send more than 2,000
  emails in a given month.
- Finally, you will have to go thru a short approval process to get out of test mode (this is
  to mitigate spam).

Once your account is set up, you will want to go through the Mailcoach settings and configure it
however you choose. A few things you will probably want to do:

- Set your timezone
- Verify you PHP× domain
- Set your default from address to something at your domain (if you don't have email set up
  you can set up something like hello@phpx(…).com to forward to your personal email address
  using Cloudflare's free email routing)

#### Connect your account to PHP×

Once your account is set up and you have a mailing list configured, please get in touch
with Chris Morrell and give him the following information:

- Your sending email addres (eg. `hello@phpx(…).com`)
- Your Mailcoach list UUID (this can be found on the list's general settings page)
- Your Mailcoach API token (go to "API Tokens" under your profile menu)
- Your Mailcoach API URL (shown when creating a token)

Once we have this, we can both configure Mailcoach for your account and sync anyone
who has already registered for updates to your list.

Eventually, the PHP× platform will automatically trigger transactional emails for things
like RSVP receipts and announcement/reminder emails, but for now you will need to send
out event announcements yourself via Mailcoach.

## Next up

We'd like to gather lots of organizer resources so that each person isn't in it alone. Some ideas:

- Guides on having your first meeting
- Companies that are interested in sponsoring meetups
- Tips on estimating how much food/drinks/etc you'll need
- How to get speakers, and what kinds of meetings work well

Some things already in the works:

- Free [Mailcoach](https://www.mailcoach.app/) accounts for organizers
- Listing on [Laravel News](https://laravel-news.com/events)
- Collaboration with [Laravel](https://laravel.com/) to give meetups exposure and help find speakers (both in-person and virtual)

Hopefully this list will grow over the coming weeks. Keep an eye out.

<!--
## Set up Bluesky

- Create bluesky account
- Verify domain as handle
- Create an app password for PHP×
- Provide DID and app password to us
-->
