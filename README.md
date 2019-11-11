# dev.to plugin for blua.blue

## Installation

From your blua.blue directory:

1. `neoan3 add component blua-blue/devTo https://github.com/blua-blue/devTo.git`

2. Visit _https://dev.to/settings/account_ in order to get your api key.

3. Create *blua_devto* credentials with your *api_key* and *token* of your choice. (`neoan3 credentials`)

4. Go to your blua.blue installation and set up a webhook (@ _https://your-domain.com/profile/#api_) to _https://your-domain.com/api.v1/dev-to_ using your chosen token

## Functionality

This plugin will listen to update & create events (the dev.to api does currently not offer a deletion endpoint).

This means that articles published through blua.blue will be published to dev.to as well. 

### about localhost and links

In some use cases or testing scenarios you may be running blua.blue from a local host.
In such a case, the plugin will strip images and the canonical url from the payload.

Please keep in mind that links in your content should be absolute in order to correctly work on external endpoints like dev.to

