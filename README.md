# dev.to plugin for blua.blue

## Installation


From your blua.blue directory:

1. `neoan3 add component blua-blue/devTo https://github.com/blua-blue/devTo.git`

2. Create *blua_devto* credentials with a property *salt* of your choice (due to how it is used, ideally characters amounting to at least 32bit). (`neoan3 credentials`)


## Activate for user accounts

### As a user

Accounts using the Plugin must setup their dev.to api keys and webhook accordingly.
Users can be directed to `https://your-blua-blue-installation.com/dev-to` to do so.

### For developers

Setup can also be done via API (JWT authentication for targeted user must be used)

1. GET https://your-blua-blue-installation.com/api.v1/dev-to?apiKey= _user's-dev.to-api-key_

2. POST https://your-blua-blue-installation.com/api.v1/webhooks

Payload: {target_url: https://your-blua-blue-installation.com/dev-to, token: _token form result of first call_}

## Functionality

This plugin will listen to update & create events (the dev.to api does currently not offer a deletion endpoint).

This means that articles published through blua.blue will be published to dev.to as well. 

### About localhost and links

In some use cases or testing scenarios you may be running blua.blue from a local host.
In such a case, the plugin will strip images and the canonical url from the payload.

Please keep in mind that links in your content should be absolute in order to correctly work on external endpoints like dev.to

